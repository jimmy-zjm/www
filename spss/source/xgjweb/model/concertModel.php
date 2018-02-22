<?php
/**
 * Created by PhpStorm.
 * User: 唐文权
 * Date: 2016/01/06
 * Time: 14:29
 */

header("Content-type:text/html; charset=utf-8");
require_once(WWW_DIR."/conf/mysql_db.php");

class concertModel{


    /**
     * concertList  查询商品是否存在于关注表中
     * @param int $userId   用户ID
     * @return array $concertStatueSelResult    查询到的信息
     */
    function concertList($userId, $classId, $goodsId){
        $concertStatueSelSql = mysql_query("
                                              SELECT
                                                `c_id`,`class_id`,`goods_id`,`user_id`
                                              FROM
                                                `xgj_concern`
                                              WHERE
                                                `user_id` = '{$userId}' AND `class_id` = '{$classId}' AND `goods_id` = '{$goodsId}'");

        while ($concertResult = mysql_fetch_array($concertStatueSelSql,MYSQL_ASSOC)){
            $concertStatueSelResult[] = $concertResult;
        }

        return @$concertStatueSelResult;

    }


    /**
     * InfoSelById  根据商品Id查询商品信息(classID已经在上一级页面进行判断)
     * @param string $table 表名
     * @param $goodsId  商品主键ID
     * @return array $infoResult    返回查到的商品信息
     */
    function InfoSelById($table, $goodsId){
        $searchInfoSql = mysql_query("
                        SELECT
                          `goods_id`,`goods_name`,`goods_img`,`shop_price`
                        FROM
                          {$table}
                        WHERE
                          `goods_id` = '{$goodsId}'
                                ");

        while ($goodsInfo = mysql_fetch_array($searchInfoSql,MYSQL_ASSOC)){
            $infoResult = $goodsInfo;
        }

        return @$infoResult;

    }


    /**
     * concertGoods     执行关注商品(添加数据)操作
     * @param int $userId   用户ID
     * @param int $goodsId  商品ID
     * @param int $classId  商品分类ID
     * @param string $concertImg    商品图片
     * @param string $concertName   商品名称
     * @param float $concertPrice   商品价格
     * @return resource     返回执行操作的结果
     */
    function concertGoods($userId, $goodsId, $classId, $concertImg, $concertName, $concertPrice){

        $concertResult = mysql_query("INSERT INTO `xgj_concern` (`user_id`,`goods_id`,`class_id`,`c_images`,`c_goodsname`,`c_goodsprice`) VALUE ('{$userId}','{$goodsId}','{$classId}','{$concertImg}','{$concertName}','{$concertPrice}')");

        return $concertResult;
    }

}