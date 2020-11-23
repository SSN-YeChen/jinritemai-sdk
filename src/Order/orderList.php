<?php
namespace YeRongHao\JinritemaiSdk\Order;

use YeRongHao\JinritemaiSdk\Common;

class orderList extends Common
{
    const METHOD = 'order/list';

    public function __construct($appKey,$appSecret,$accessToken){
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->accessToken = $accessToken;
        $this->apiUrl = $this->host.self::METHOD;
    }

    /**
     * Notes: 获取订单列表
     * @param $startTime 开始时间 2018/06/03 00:00:00
     * @param $endTime 结束时间
     * @param $orderStatus 子订单状态 1在线支付订单待支付；货到付款订单待确认 2备货中（只有此状态下，才可发货）3已发货 4已取消 5已完成
     * @param int $page 页数（默认为0，第一页从0开始）
     * @param int $size 每页订单数（默认为10，最大100）
     * @param string $orderBy 值为“create_time”：按订单创建时间；值为“update_time”：按订单更新时间
     * @param int $isDesc 订单排序方式：0(is_desc，最近的在前)， 1(asc，最近的在后)
     * @return mixed
     * @throws \Couchbase\Exception
     */
    public function getOrderList($startTime,$endTime,$orderStatus,$page,$size,$orderBy,$isDesc){
        $param = [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'order_starts' => $orderStatus,
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