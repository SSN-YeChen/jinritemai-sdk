<?php
namespace yeronghao\jinritemai\product;

use yeronghao\jinritemai\JinritemaiService;

class ProductList extends JinritemaiService
{
    protected $apiUrl = 'product/list';

    protected $method = 'product.list';

    public function __construct()
    {
        $this->apiPath = $this->host . $this->apiUrl;
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
    public function productList($page,$size,$status,$checkStatus){
        $param = $this->makeParam($this->method,compact('page','size','status','checkStatus'));
        $requestQuery = $this->getRequestQuery($this->apiUrl,$param);
        return $requestQuery;
    }
}