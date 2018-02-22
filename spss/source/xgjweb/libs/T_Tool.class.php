<?php
/**
 * Created by PhpStorm.
 * User: 唐文权
 * Date: 2015/12/22
 * Time: 16:41
 */
class T_Tool{

    /**
     * triMall      表示删除空格
     * @param $str  要去除空格的字符串
     * @return string $strTrim  已成功去除空格的字符串
     */
    static public function triMall($str)
    {
        $qian=array(" ","　","\t","\n","\r");
        $hou=array("","","","","");
        $strTrim = str_replace($qian,$hou,$str);
        return $strTrim;
    }


    /**
     * concernGoodsList 分页方法
     * 调用此方法，查询出来的分页总数量的别名一定要为"countDate",无需在加LIMIT排序
     * eg: SELECT COUNT(`id`) 'countDate' FROM `table`
     * @param string $pageCountSql     查询总记录数的sql语句
     * @param string $dataListSql     查询数据的sql语句
     * @param int $pageSizeParam    每页显示数据条数
     * @return array  $result   二维数组(查询到的数据和分页信息)
     */
    static public function pageDataList($pageCountSql, $dataListSql, $pageSizeParam=10){

        //获取当前页,如果page参数为空或者小于等于0或者不为数字
        $page = empty($_GET["page"]) || $_GET["page"] <=0 || !is_numeric($_GET["page"]) ? 1 : intval($_GET["page"]);

        //总记录
        $data = @mysql_fetch_array(mysql_query($pageCountSql));
        $dataCount = $data["countDate"];

        //每页显示数
        $pageSize = $pageSizeParam;

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
        $dataSelSql = mysql_query("{$dataListSql} LIMIT {$pageNum}, {$pageSize}");
          
      
        while ($dataList = mysql_fetch_array($dataSelSql,MYSQL_ASSOC)){
             $dataSelResult[] = $dataList;
        }
        
   
        $result = @array(
            'dataSelResult'=>$dataSelResult,
            'pageInfo'=>array(
            'page'=>$page,
            'pageCount'=>$pageCount,
            'pageSize'=>$pageSize
            )
        );

        return $result;

    }

}