<?php
namespace yeronghao\jinritemai;

use Couchbase\Exception;
use yeronghao\jinritemai\product\ProductList;

class JinritemaiService extends Common
{
    //接口默认地址
    protected $host = 'https://openapi-fxg.jinritemai.com/';

    protected $appKey;

    protected $appSecret;

    protected $accessToken;

    protected $toutiaoId;

    protected $apiVersion = '2';

    protected $apiPath;

    protected $param;

    const OAUTH_URL = 'oauth2/access_token';
    //产品列表
    const PRODUCT_LIST = 'product/list';

    //sku 列表
    const SKU_LIST  = 'sku/list';


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
     * JinritemaiService constructor.
     * @param $options
     * @throws Exception
     */
    public function __construct($options)
    {
        //初始化参数
        $this->appKey = isset($options['app_key']) ? $options['app_key'] : '';
        $this->appSecret = isset($options['app_secret']) ? $options['app_secret'] : '';
        $this->toutiaoId = isset($options['toutiao_id']) ? $options['toutiao_id'] : '';

        //处理 access_token
        $this->accessToken = $this->getAccessToken();
    }


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
            return $this->accessToken = $requestQuery['data']['access_token'];
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
     * Notes:设置缓存
     * @param string $cachename
     * @param mixed $value
     * @return boolean
     */
    protected function setCache($cachename,$value){
        //TODO :  按需调整
        //默认过期时间是7天，提前半个小时刷新了令牌
        $expired = 86400 * 7 - 1800;
        $redis = redis();
        return $redis->setex($cachename,$expired,$value);
    }


    /**
     * Notes:获取缓存
     * @param string $cachename
     * @return mixed
     */
    protected function getCache($cachename){
        //TODO :  按需调整
        $redis = redis();
        return $redis->get($cachename);
    }


    /**
     * Notes: get请求
     * @param $url
     * @param $param
     * @return mixed
     * @throws Exception
     */
    protected function getRequestQuery($url,$param){
        $requestQuery = $this->http_get($url,$param);

        //判断是否请求成功
        if(is_array($requestQuery)){
            //判断返回状态
            if($requestQuery['err_no'] === 0){
                return $requestQuery["data"];
            }
        }

        //TODO :  记录错误日志
        throw new Exception($requestQuery);
    }


    protected function method($method){
        return str_replace('/','.',$method);
    }


    /**
     * Notes: 获取产品列表
     * @param int $page 第几页（第一页为0，最大为99）
     * @param int $size 每页返回条数，最多支持100条
     * @param int $status 指定状态返回商品列表：0上架 1下架
     * @param int $checkStatus 指定审核状态返回商品列表：1未提审 2审核中 3审核通过 4审核驳回 5封禁
     * @return mixed
     * @throws \Couchbase\Exception
     */
    public function getProductList($page = 0,$size = 100,$status = 0,$check_status = 3){
        $apiUrl = $this->host.$this->method(self::PRODUCT_LIST);
        $param = $this->makeParam(self::PRODUCT_LIST,compact('page','size','status','check_status'));
        $requestQuery = $this->getRequestQuery($apiUrl,$param);
        return $requestQuery;
    }


    /**
     * Notes: 获取sku列表
     * @param $product_id
     * @return mixed
     * @throws Exception
     */
    public function getSkuList($product_id){
        $apiUrl = $this->host.$this->method(self::SKU_LIST);
        $param = $this->makeParam(self::SKU_LIST,compact('product_id'));
        $requestQuery = $this->getRequestQuery($apiUrl,$param);
        return $requestQuery;
    }
}