<?php
namespace YeRongHao\JinritemaiSdk\AfterSale;

use YeRongHao\JinritemaiSdk\Common;

class afterSaleRefundProcessDetail extends Common
{
    const METHOD = 'afterSale/refundProcessDetail';

    public function __construct($appKey,$appSecret,$accessToken){
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->accessToken = $accessToken;
        $this->apiUrl = $this->host.self::METHOD;
    }

    /**
     * Notes:根据子订单ID查询退款详情
     * @param $orderId 子订单ID，不带字母A
     * @return mixed
     * @throws \Couchbase\Exception
     */
    public function getAfterSaleRefundProcessDetail($orderId){
        $param = [
            'order_id' => $orderId
        ];
        $this->makeParam($this->method(self::METHOD),$param);
        $requestQuery = $this->getRequestQuery($this->apiUrl,$this->param);
        return $requestQuery;
    }
}