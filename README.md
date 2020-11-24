    /**
     * 初始化 加载抖音SDK
     */
    public function __construct()
    {
        $options = [
            "app_key" => self::DY_APPKEY,
            "app_secret" => self::DY_APPSERET,
            "toutiao_id" => self::DY_SHOPID
        ];
        $this->jinritemai = new JinritemaiService($options);
    }
