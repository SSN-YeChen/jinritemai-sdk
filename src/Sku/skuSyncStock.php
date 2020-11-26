<?php
namespace YeRongHao\JinritemaiSdk\Sku;

use YeRongHao\JinritemaiSdk\Common;

class skuSyncStock extends Common
{
    const METHOD = 'sku/syncStock';

    public function __construct($appKey,$appSecret,$accessToken){
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->accessToken = $accessToken;
        $this->apiUrl = $this->host.self::METHOD;
    }

    /**
     * Notes: 同步sku库存
     * @param $skuId
     * @param $stockNum 库存 (可以为0)
     * @param $outWarehouseId 外部仓库ID
     * @param $supplierId 供应商ID
     * @param $incremental true表示增量库存，false表示全量库存，默认为false
     * @param $idempotentId 幂等ID(仅incremental=true时有用)
     * @return mixed
     * @throws \Couchbase\Exception
     */
    public function syncSkuStock($skuId,$stockNum,$outWarehouseId,$supplierId,$incremental,$idempotentId){
        $param = [
            'sku_id' => $skuId,
            'stock_num' => $stockNum
        ];

        $outWarehouseId !== '' && $param['out_warehouse_id'] = $outWarehouseId;
        $supplierId !== '' && $param['supplier_id'] = $supplierId;
        $incremental !== '' && $param['incremental'] = $incremental;
        $idempotentId !== '' && $param['idempotent_id'] = $idempotentId;

        $this->makeParam($this->method(self::METHOD),$param);
        $requestQuery = $this->getRequestQuery($this->apiUrl,$this->param);
        return $requestQuery;
    }
}