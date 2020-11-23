<?php
namespace YeRongHao\JinritemaiSdk;

use Couchbase\Exception;

class JinritemaiService extends Common
{
    //产品列表
    const PRODUCT_LIST = 'product/list';
    //sku 列表
    const SKU_LIST  = 'sku/list';

    //订单列表
    const ORDER_LIST = 'order/list';
    //订单详情
    const ORDER_DETAIL = 'order/detail';

    //已发货且有售后的订单列表
    const AFTER_SALE_ORDER_LIST = 'afterSale/orderList';
    //根据子订单ID查询退款详情
    const AFTER_SALE_REFUND_PROCESS_DETAIL = 'afterSale/refundProcessDetail';


    /**
     * JinritemaiService constructor.
     * @param $options
     * @throws Exception
     */
    public function __construct($options)
    {
        //初始化参数
        $this->appKey = isset($options['app_key']) ? $options['app_key'] : '';
        $this->appSecret = isset($options['app_secret']) ? $options['app_secret'] : '';
        $this->toutiaoId = isset($options['toutiao_id']) ? $options['toutiao_id'] : '';

        //处理 access_token
        $this->getAccessToken();
    }


    /**
     * Notes: 获取产品列表
     * @param int $page 第几页（第一页为0，最大为99）
     * @param int $size 每页返回条数，最多支持100条
     * @param int $status 指定状态返回商品列表：0上架 1下架
     * @param int $checkStatus 指定审核状态返回商品列表：1未提审 2审核中 3审核通过 4审核驳回 5封禁
     * @return mixed
     * @throws \Couchbase\Exception
     */
    public function getProductList($page = 0,$size = 100,$status = 0,$checkStatus = 3){
        $apiUrl = $this->host.self::PRODUCT_LIST;
        $param = [
            'page' => $page,
            'size' => $size,
            'status' => $status,
            'check_status' => $checkStatus
        ];
        $this->makeParam($this->method(self::PRODUCT_LIST),$param);
        $requestQuery = $this->getRequestQuery($apiUrl,$this->param);
        return $requestQuery;
    }


    /**
     * Notes: 获取sku列表
     * @param $productId
     * @return mixed
     * @throws Exception
     */
    public function getSkuList($productId){
        $apiUrl = $this->host.self::SKU_LIST;
        $param = [
            'product_id' => $productId
        ];
        $this->makeParam($this->method(self::SKU_LIST),$param);
        $requestQuery = $this->getRequestQuery($apiUrl,$this->param);
        return $requestQuery;
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
     * @throws Exception
     */
    public function getOrderList($startTime,$endTime,$orderStatus,$page = 1,$size = 100,$orderBy = 'create_time',$isDesc = 0){
        $apiUrl = $this->host.self::ORDER_LIST;
        $param = [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'order_starts' => $orderStatus,
            'page' => $page,
            'size' => $size,
            'order_by' => $orderBy,
            'is_desc' => $isDesc
        ];
        $this->makeParam($this->method(self::ORDER_LIST),$param);
        $requestQuery = $this->getRequestQuery($apiUrl,$this->param);
        return $requestQuery;
    }


    /**
     * Notes: 获取订单详情
     * @param $orderId 订单ID
     * @return mixed
     * @throws Exception
     */
    public function getOrderDetail($orderId){
        $apiUrl = $this->host.self::ORDER_DETAIL;
        $param = [
            'order_id' => $orderId . 'A'
        ];
        $this->makeParam($this->method(self::ORDER_DETAIL),$param);
        $requestQuery = $this->getRequestQuery($apiUrl,$this->param);
        return $requestQuery;
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
     * @throws Exception
     */
    public function getAfterSaleOrderList($startTime,$endTime,$type,$page = 0,$size = 100,$orderBy = 'update_time',$isDesc = 0){
        $apiUrl = $this->host.self::AFTER_SALE_ORDER_LIST;
        $param = [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'type' => $type,
            'page' => $page,
            'size' => $size,
            'order_by' => $orderBy,
            'is_desc' => $isDesc
        ];
        $this->makeParam($this->method(self::AFTER_SALE_ORDER_LIST),$param);
        $requestQuery = $this->getRequestQuery($apiUrl,$this->param);
        return $requestQuery;
    }


    /**
     * Notes:根据子订单ID查询退款详情
     * @param $orderId 子订单ID，不带字母A
     * @return mixed
     * @throws Exception
     */
    public function getAfterSaleRefundProcessDetail($orderId){
        $apiUrl = $this->host.self::AFTER_SALE_REFUND_PROCESS_DETAIL;
        $param = [
            'order_id' => $orderId
        ];
        $this->makeParam($this->method(self::AFTER_SALE_REFUND_PROCESS_DETAIL),$param);
        $requestQuery = $this->getRequestQuery($apiUrl,$this->param);
        return $requestQuery;
    }
}