<?php
namespace YeRongHao\JinritemaiSdk;

use Couchbase\Exception;

class Common{
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
}