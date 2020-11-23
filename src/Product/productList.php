<?php
namespace YeRongHao\JinritemaiSdk\Product;

use YeRongHao\JinritemaiSdk\Common;

class productList extends Common
{
    const METHOD = 'product/list';

    public function __construct($appKey,$appSecret,$accessToken){
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->accessToken = $accessToken;
        $this->apiUrl = $this->host.self::METHOD;
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
    public function getProductList($page,$size,$status,$checkStatus){
        $param = [
            'page' => $page,
            'size' => $size,
            'status' => $status,
            'check_status' => $checkStatus
        ];
        $this->makeParam($this->method(self::METHOD),$param);
        $requestQuery = $this->getRequestQuery($this->apiUrl,$this->param);
        return $requestQuery;
    }
}