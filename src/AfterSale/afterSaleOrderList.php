<?php
namespace YeRongHao\JinritemaiSdk\AfterSale;

use YeRongHao\JinritemaiSdk\Common;

class afterSaleOrderList extends Common
{
    const METHOD = 'afterSale/orderList';

    public function __construct($appKey,$appSecret,$accessToken){
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->accessToken = $accessToken;
        $this->apiUrl = $this->host.self::METHOD;
    }

    /**
     * Notes: 获取已发货且有售后的订单列表
     * @param $startTime 开始时间 2018/06/03 00:00:00
     * @param $endTime 结束时间
     * @param $type 类型(1.全部售后单 2.待商家处理 3.待商家收货 4.待客服仲裁 5.退款成功 7.待买家退货)
     * @param int $page 页数（默认值为0，第一页从0开始）
     * @param int $size 每页订单数（默认为10，最大100）
     * @param string $orderBy 搜索时间条件：按订单创建时间create_time；按订单更新时间进行搜索update_time
     * @param int $isDesc 订单排序方式：最近的在前，1；最近的在后，0
     * @return mixed
     * @throws \Couchbase\Exception
     */
    public function getAfterSaleOrderList($startTime,$endTime,$type,$page,$size,$orderBy,$isDesc){
        $param = [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'type' => $type,
            'page' => $page,
            'size' => $size,
            'order_by' => $orderBy,
            'is_desc' => $isDesc
        ];
        $this->makeParam($this->method(self::METHOD),$param);
        $requestQuery = $this->getRequestQuery($this->apiUrl,$this->param);
        return $requestQuery;
    }
}