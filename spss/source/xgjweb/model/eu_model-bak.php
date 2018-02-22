<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");

class eumodel{
	/**
	 * 分页
	 * @param unknown $page
	 * @return multitype:unknown
	 */
	function show_list($page){
		$db=new db();
		$start=($page-1)*12;
		$sql = "SELECT * FROM xgj_eu_comment limit ".$start.",12";
		$detail=$db->getAll($sql);
		return $detail;
	
	}
	
	/**
	 * 总条数
	 * @return string
	 */
	function show_count(){
		$db=new db();
		$sql = "SELECT count(*) FROM xgj_eu_comment";
		$result=$db->getOne($sql);
		return $result;
	}
	
	function Primary_navbar(){
		
		$sql = "SELECT cat_id,parent_id,cat_name FROM xgj_eu_cat where parent_id =0 ";
		
		$detail=mysql_query($sql)or die("Invalid query: " . mysql_error());
		
		$result=array();
		
		while($row=mysql_fetch_array($detail,MYSQL_ASSOC )){
			
			$result[]=$row;
			
		}
		
        foreach ($result as $cat){
        $sql = "SELECT cat_id,parent_id,cat_name FROM xgj_eu_cat where parent_id ={$cat['cat_id']} ";
		
		$ret=mysql_query($sql)or die("Invalid query: " . mysql_error());
		
		$cate=array();
		
		while($row=mysql_fetch_array($ret,MYSQL_ASSOC )){
			
			$cate[]=$row;
			
		}
		$cate['list']=$cat;
		$arr[]=$cate;
        }
        
        return $arr;
	}
	
	function getparentcat(){//得到parent_id=0的 id
		
		$sql = "SELECT cat_id,cat_name FROM xgj_eu_cat where parent_id =0";
		
		$detail=mysql_query($sql)or die("Invalid query: " . mysql_error());
		
		$result=array();
		
		while($row=mysql_fetch_array($detail,MYSQL_ASSOC )){
			
			$result[]=$row;
			
		}
		
		return $result;
		
	}
	
	function getfid($cat_id){
		
		$sql = "SELECT parent_id FROM xgj_eu_cat where cat_id=$cat_id";
		
		$detail=mysql_query($sql)or die("Invalid query: " . mysql_error());
		
		$result=array();
		
		$row=mysql_fetch_array($detail,MYSQL_ASSOC );
		
		return $row;
		
	}
	
	function getSoncate($id){//得到子分类ID
	
		$sql="select cat_name from xgj_eu_cat where parent_id=$id";
	
		$detail=mysql_query($sql)or die("Invalid query: " . mysql_error());
	
		$row=mysql_fetch_array($detail,MYSQL_ASSOC );
	
		return $row;
	
	}
	
	/*
	* 首页推荐商品
	*/
	function getrecommendgoods($cat_id){
		
		$sql = "select g.class_id,c.cat_name,g.parent_id,g.goods_name,g.promote_price,g.goods_id,g.goods_img
		from xgj_eu_goods g left join xgj_eu_cat c 
		on g.parent_id=c.cat_id 
		where g.parent_id=$cat_id and g.is_recommend=1 limit 6";
		
		$detail=mysql_query($sql)or die("Invalid query: " . mysql_error());
		
		$result=array();
		
		while($row=mysql_fetch_array($detail,MYSQL_ASSOC )){
			
			$result[]=$row;
			
		}
		
		return $result;
		
	}
	
	/**
	 * 根据goods_id查询详情
	 */
	function getGooddetailById($id){
		$db=new db();
		$rs=$db->getRow("select * from xgj_eu_goods where goods_id = $id");
		return $rs;
	}
	
	/**
	 * 根据goods_id评价列表
	 */
	function getCommentById($id){
		$db=new db();
		$rs=$db->getAll("select * from xgj_eu_comment where goods_id = $id");
		return $rs;
	}

/*
* 整点抢购
*/	
	
	function getBuyingGoods(){
		
		$db = new db();
		
		$result = $db -> getAll("select class_id,parent_id,goods_name,promote_price,goods_id,goods_img,begin_time,end_time 
			
								from xgj_eu_buy_onclock where is_recommend = 1 order by goods_id desc limit 5 ");
		
		return $result;
		
	}

}






















