<?php
namespace YeRongHao\JinritemaiSdk\Order;

use YeRongHao\JinritemaiSdk\Common;

class orderDetail extends Common
{
    const METHOD = 'order/detail';

    public function __construct($appKey,$appSecret,$accessToken){
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->accessToken = $accessToken;
        $this->apiUrl = $this->host.self::METHOD;
    }

    /**
     * Notes: 获取订单详情
     * @param $orderId 订单ID
     * @return mixed
     * @throws \Couchbase\Exception
     */
    public function getOrderDetail($orderId){
        $param = [
            'order_id' => $orderId . 'A'
        ];
        $this->makeParam($this->method(self::METHOD),$param);
        $requestQuery = $this->getRequestQuery($this->apiUrl,$this->param);
        return $requestQuery;
    }
}