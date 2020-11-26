<?php
namespace YeRongHao\JinritemaiSdk\Sku;

use YeRongHao\JinritemaiSdk\Common;

class skuStockNum extends Common
{
    const METHOD = 'sku/syncStock';

    public function __construct($appKey,$appSecret,$accessToken){
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->accessToken = $accessToken;
        $this->apiUrl = $this->host.self::METHOD;
    }

    /**
     * Notes: 查询库存
     * @param $skuId
     * @param $outWarehouseId 外部仓库id
     * @return mixed
     * @throws \Couchbase\Exception
     */
    public function getSkuStockNum($skuId,$outWarehouseId){
        $param = [
            'sku_id' => $skuId
        ];

        $outWarehouseId !== '' && $param['out_warehouse_id'] = $outWarehouseId;

        $this->makeParam($this->method(self::METHOD),$param);
        $requestQuery = $this->getRequestQuery($this->apiUrl,$this->param);
        return $requestQuery;
    }
}