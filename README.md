# 抖店SDK
调用抖店API接口  

# composer包地址
```
composer require yeronghao/jinritemai-sdk
```
  
# 实例化
```
protected $jinritemai;
/**
 * 初始化 加载抖音SDK
 */
public function __construct()
{
    $options = [
        "app_key" => self::DY_APPKEY,
        "app_secret" => self::DY_APPSERET,
        //抖店ID 目前用于redis的key值
        "toutiao_id" => self::DY_SHOPID
    ];
    $this->jinritemai = new JinritemaiService($options);
}
```
  
# API调用
目前只开发了少量的API
具体请查看 JinritemaiService.php
范例:
获取上架的所有商品
```
$this->jinritemai->getProductList();
```
  
# 注意事项
***一定要去调整 Common.php中的获取 redis(缓存)的方法***
```
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
```
