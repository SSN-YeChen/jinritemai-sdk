<?php
namespace YeRongHao\JinritemaiSdk;

use Couchbase\Exception;

class Common{
    //接口默认地址
    protected $host = 'https://openapi-fxg.jinritemai.com/';

    protected $appKey;

    protected $appSecret;

    protected $accessToken;

    protected $toutiaoId;

    protected $apiVersion = '2';

    protected $apiPath;

    protected $param;

    protected $apiUrl;

    const OAUTH_URL = 'oauth2/access_token';

    /**
     *  请求参数返回错误代码

    1	"请登录后再操作"
    2	"无权限"
    3	"缺少参数"
    4	"参数错误"
    5	"参数不合法"
    6	"业务参数json解析失败, 所有参数需为string类型"
    7	"服务器错误"
    8	"服务繁忙"
    9	"访问太频繁"
    10	"需要用 POST 请求"
    11	"签名校验失败"
    12	"版本太旧，请升级"
    302	"找不到user_id"
    30001	认证失败，app_key格式不正确，应为19位纯数字
    30001	认证失败，app_key不存在
    30001	认证失败，access_token不能为空
    30002	access_token已过期
    30003	店铺授权已失效，请重新引导商家完成店铺授权
    30004	应用已被系统禁用
    30005	access_token不存在，请使用最新的access_token访问
    30006	店铺授权已被关闭，请联系商家打开授权开关
    30007	app_key和access_token不匹配，请仔细检查
     */


    /**
     * Notes:获取access_token
     * @return bool|mixed
     * @throws Exception
     */
    public function getAccessToken(){
        $redisKey = 'jinritemai_access_token_'.$this->toutiaoId;
        $accessToken = $this->getCache($redisKey);

        if($accessToken){
            $this->accessToken = $accessToken;
            return true;
        }

        //获取新的access_token
        $param = [
            'app_id' => $this->appKey,
            'app_secret' => $this->appSecret,
            'grant_type' => 'authorization_self'
        ];
        $requestQuery = $this->http_get($this->host . self::OAUTH_URL,$param);

        if($requestQuery['err_no'] === 0){
            //存入redis
            $this->setCache($redisKey,$requestQuery['data']['access_token']);
            $this->accessToken = $requestQuery['data']['access_token'];
            return true;
        }
        else{
            $this->removeCache($redisKey);
        }

        //TODO :  记录获取 access_token 获取失败并通知相关人员
    }


    /**
     * Notes: 设置请求参数
     */
    protected function makeParam($method,$paramArr){
        $this->param = [
            'method' => $method,
            'app_key' => $this->appKey,
            'param_json' => $this->makeParamJson($paramArr),
            'timestamp' => date("Y-m-d H:i:s"),
            'v' => $this->apiVersion,
        ];

        $this->param['sign'] = $this->makeSign();
        $this->param['access_token'] = $this->accessToken;
        $this->param['sign_method'] = 'md5';
    }


    /**
     * Notes:创建 param_json
     * @param $paramArr
     * @return false|string
     */
    protected function makeParamJson($paramArr){
        ksort($paramArr,SORT_STRING);
        foreach ($paramArr as &$value){
            $value = (string)$value;
        }
        return json_encode($paramArr,JSON_HEX_TAG);
    }


    /**
     * Notes:创建签名
     * @return string
     */
    protected function makeSign(){
        $array = $this->param;
        ksort($array,SORT_STRING);
        $signStr = '';
        foreach ($array as $key => $value){
            $signStr .= $key . $value;
        }

        return md5($this->appSecret . $signStr . $this->appSecret);
    }


    /**
     * Notes: 驼峰命名风格转换成下划线命名风格
     * @param $string
     * @return string
     */
    public function parseUnderline($string)
    {
        //替换过程 NameStyle => N | S => _N | _S => _Name_Style => Name_Style => name_style
        $string = strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $string), "_"));
        return $string;
    }

    /**
     * Notes:设置缓存
     * @param string $cachename
     * @param mixed $value
     * @return boolean
     */
    public function setCache($cachename,$value){
        //TODO :  按需调整
        $expired = 1800;
        $redis = redis();
        return $redis->setex($cachename,$expired,$value);
    }


    /**
     * Notes:获取缓存
     * @param string $cachename
     * @return mixed
     */
    public function getCache($cachename){
        //TODO :  按需调整
        $redis = redis();
        return $redis->get($cachename);
    }

    /**
     * Notes:清除缓存
     * @param string $cachename
     * @return boolean
     */
    public function removeCache($cachename){
        //TODO :  按需调整
        $redis = redis();
        return $redis->del($cachename);
    }

    /**
     * Notes: get请求
     * @param $url
     * @param $param
     * @return mixed
     * @throws Exception
     */
    public function getRequestQuery($url,$param){
        $requestQuery = $this->http_get($url,$param);

        //判断是否请求成功
        if(is_array($requestQuery)){
            //判断返回状态
            if($requestQuery['err_no'] === 0){
                return $requestQuery["data"];
            }
        }

        //TODO :  记录错误日志
    }

    public function method($method){
        return str_replace('/','.',$method);
    }

    /**
     * Notes:GET 请求
     * @param $url
     * @param array $param
     * @return bool|string
     */
    public function http_get($url,$param = []){
        if($param){
            $path = [];
            foreach ($param as $key => $value){
                $path[] = "{$key}=$value";
            }
            $url .= '?' . implode("&",$path);
        }

        $url = str_replace(' ','%20',$url);

        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return json_decode($sContent,true);
        }else{
            return $sContent;
        }
    }

    /**
     * POST 请求
     * @param string $url
     * @param array $param
     * @param boolean $post_file 是否文件上传
     * @return string content
     */
    public function http_post($url,$param = [],$post_file=false){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if (is_string($param) || $post_file) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach($param as $key=>$val){
                $aPOST[] = $key."=".urlencode($val);
            }
            $strPOST =  join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($oCurl, CURLOPT_POST,true);
        /**if(PHP_VERSION_ID >= 50500){
        curl_setopt($oCurl, CURLOPT_SAFE_UPLOAD, FALSE);
        }*/
        curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);

        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }
}