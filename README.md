# 抖店SDK
# composer包地址
```
composer require yeronghao/jinritemai-sdk
```

# 初始化
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
