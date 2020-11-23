<?php
namespace YeRongHao\JinritemaiSdk;

use Couchbase\Exception;
use YeRongHao\JinritemaiSdk\Product;
use YeRongHao\JinritemaiSdk\Sku;
use YeRongHao\JinritemaiSdk\Order;
use YeRongHao\JinritemaiSdk\AfterSale;

class JinritemaiService extends Common
{
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

    //获取产品列表
    public function getProductList($page = 0,$size = 100,$status = 0,$checkStatus = 3){
        return (new Product\productList($this->appKey,$this->appSecret,$this->accessToken))->getProductList($page,$size,$status,$checkStatus);
    }

    //获取sku列表
    public function getSkuList($productId){
        return (new Sku\skuList($this->appKey,$this->appSecret,$this->accessToken))->getSkuList($productId);
    }

    //获取订单列表
    public function getOrderList($startTime,$endTime,$orderStatus,$page = 1,$size = 100,$orderBy = 'create_time',$isDesc = 0){
        return (new Order\orderList($this->appKey,$this->appSecret,$this->accessToken))->getOrderList($startTime,$endTime,$orderStatus,$page,$size,$orderBy,$isDesc);
    }

    //获取订单详情
    public function getOrderDetail($orderId){
        return (new Order\orderDetail($this->appKey,$this->appSecret,$this->accessToken))->getOrderDetail($orderId);
    }

    //获取已发货且有售后的订单列表
    public function getAfterSaleOrderList($startTime,$endTime,$type,$page = 0,$size = 100,$orderBy = 'update_time',$isDesc = 0){
        return (new AfterSale\afterSaleOrderList($this->appKey,$this->appSecret,$this->accessToken))->getAfterSaleOrderList($startTime,$endTime,$type,$page,$size,$orderBy,$isDesc);
    }

    //根据子订单ID查询退款详情
    public function getAfterSaleRefundProcessDetail($orderId){
        return (new AfterSale\afterSaleRefundProcessDetail($this->appKey,$this->appSecret,$this->accessToken))->getAfterSaleRefundProcessDetail($orderId);
    }
}