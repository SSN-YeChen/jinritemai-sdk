<?php
namespace YeRongHao\JinritemaiSdk\Sku;

use YeRongHao\JinritemaiSdk\Common;

class skuList extends Common
{
    const METHOD = 'sku/list';

    public function __construct($appKey,$appSecret,$accessToken){
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->accessToken = $accessToken;
        $this->apiUrl = $this->host.self::METHOD;
    }

    /**
     * Notes: 获取sku列表
     * @param $productId
     * @return mixed
     * @throws \Couchbase\Exception
     */
    public function getSkuList($productId){
        $param = [
            'product_id' => $productId
        ];
        $this->makeParam($this->method(self::METHOD),$param);
        $requestQuery = $this->getRequestQuery($this->apiUrl,$this->param);
        return $requestQuery;
    }
}