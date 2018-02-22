<?php
/**
 * Created by PhpStorm.
 * User: 唐文权
 * Date: 2016/01/06
 * Time: 14:29
 */

header("Content-type:text/html; charset=utf-8");
require_once(WWW_DIR."/conf/mysql_db.php");

class searchModel{

    /**
     * searchIndex   查询要搜索的商品
     * @param 搜索的商品名 $info
     * @sql Union 并集(将查询出来的数据并在一起)
     * @return array $searchResult   搜索到的信息
     */
    function searchIndex($info){

        //处理分页数据START

        //查询总记录
        $countDateSql="SELECT COUNT(*) 'countDate' FROM `xgj_greenfood_goods` g WHERE g.`goods_name` LIKE '%{$info}%'
                          Union
                        SELECT COUNT(*) 'countDate' FROM `xgj_furnish_goods` f WHERE f.`goods_name` LIKE '%{$info}%'
                          Union
                        SELECT COUNT(*) 'countDate' FROM `xgj_eu_goods` e WHERE e.`goods_name` LIKE '%{$info}%'";

        //获取当前页,如果page参数为空或者小于等于0或者不为数字
        $page = empty($_GET["page"]) || $_GET["page"] <=0 || !is_numeric($_GET["page"]) ? 1 : intval($_GET["page"]);

        //总记录
        $data = @mysql_fetch_array(mysql_query($countDateSql));
        $dataCount = $data["countDate"];

        //每页显示数
        $pageSize = 6;

        //总页数
        $pageCount = ceil($dataCount / $pageSize);

        //如果总页数等于0
        $pageCount = $pageCount==0 ? 1 : $pageCount;

        //如果page参数大于总页数
        if ($page > $pageCount){
            $page = $pageCount;
        }

        //每页起始数
        $pageNum = ($page - 1) * $pageSize;

        //处理分页数据


        $searchSql = mysql_query("
                                SELECT
                                  g.`goods_id`,g.`class_id`,g.`goods_name`,g.`goods_img`,g.`shop_price`
                                FROM `xgj_greenfood_goods` g
                                  WHERE g.`goods_name` LIKE '%{$info}%'

                                Union

                                SELECT
                                  f.`goods_id`,f.`class_id`,f.`goods_name`,f.`goods_img`,f.`shop_price`
                                FROM
                                  `xgj_furnish_goods` f
                                WHERE
                                  f.`goods_name` LIKE '%{$info}%'
                                Union

                                SELECT
                                  e.`goods_id`,e.`class_id`,e.`goods_name`,e.`goods_img`,e.`shop_price`
                                FROM
                                  `xgj_eu_goods` e
                                WHERE
                                  e.`goods_name` LIKE '%{$info}%'
                                LIMIT
                                  {$pageNum}, {$pageSize}
                                  ");

        while ($goodsList = mysql_fetch_array($searchSql,MYSQL_ASSOC)){
            $searchResult[] = $goodsList;
        }

        $result = @array('searchResult'=>$searchResult,
            'pageInfo'=>array(
                'page'=>$page,
                'pageCount'=>$pageCount,
                'pageSize'=>$pageSize
            )
        );

        return @$result;

    }


    public function category($class_id){
        $data = M('xgj_eu_category')->where("class_id=$class_id and pid=0")->select();
        //后台三级分类 前台读取后两级分类
        $pid='';
        foreach ($data as $k=>$v){
            $pid .= ','.$v['id'];
        }
        $pid =ltrim($pid,',');
        $data1 = M('xgj_eu_category')->where("class_id=$class_id and pid in ( {$pid} )")->select();
        foreach ($data1 as $key=>$v1){
            
            $data1[$key]['list']=M('xgj_eu_category')->where("class_id=$class_id and pid={$v1['id']}")->select();
        }
        return $data1;
    }

    /**
     * searchInfoById   通过ID查询商品信息
     * @param $table
     * @param $goodsId
     * @return array
     */
    function searchInfoById($table, $goodsId){
        $searchInfoSql = mysql_query("
                        SELECT
                          `goods_id`,`class_id`,`goods_name`,`goods_img`,`shop_price`
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

}