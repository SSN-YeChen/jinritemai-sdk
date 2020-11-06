<?php
namespace yeronghao\jinritemai\sku;


use yeronghao\jinritemai\JinritemaiService;

class SkuList extends JinritemaiService
{
    protected $apiUrl = 'sku/list';

    protected $method = 'sku.list';

    public function __construct($options)
    {
        parent::__construct($options);
        $this->apiPath = $this->host . $this->apiUrl;
    }


    public function skuList(){

    }
}