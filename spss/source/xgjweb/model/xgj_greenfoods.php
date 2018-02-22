<?php
require_once(WWW_DIR."/conf/mysql_db.php");

class foods{
	
// 	function getcat(){
// 		$sql="select cat_id from xgj_greenfood_cat where parent_id = 0 ";
// 		$detail=mysql_query($sql)or die("Invalid query: " . mysql_error());
// 		$result=array();
// 		while($row=mysql_fetch_array($detail,MYSQL_ASSOC )){
// 				$result[]=$row;
// 			}
// 			return $result;
// 	}
	
// 	function getrecommend($cat_id){
	
// 		$sql = " select g.class_id,c.cat_name,g.parent_id,g.goods_name,g.promote_price,g.goods_id,g.goods_img
// 		from xgj_greenfood_goods g left join xgj_greenfood_cat c 
// 		on g.parent_id=c.cat_id 
// 		where g.parent_id=$cat_id and g.is_recommend=1 limit 5 ";
		
// 		$detail=mysql_query($sql)or die("Invalid query: " . mysql_error());
		
// 		$result=array();
		
// 		while($row=mysql_fetch_array($detail,MYSQL_ASSOC )){
			
// 			$result[]=$row;
			
// 		}
		
// 		return $result;
	
// 	}
	
// 	function show_list($page,$cat_id){
// 		$db=new db();
// 		$start=($page-1)*6;
// 		$sql = "select class_id,goods_id,goods_sn,goods_name,brand_name,shop_price,goods_img 
// 				from xgj_greenfood_goods g left join xgj_brand b on b.brand_id=g.brand_id
// 				where g.cat_id=$cat_id limit ".$start.",6";
// 		$detail=$db->getAll($sql);
// 		return $detail;
	
// 	}
	
// 	function show_count($cat_id){
// 		$db=new db();
// 		$sql = "SELECT count(*) FROM  xgj_greenfood_goods where cat_id = $cat_id ";
// 		$result=$db->getOne($sql);
// 		return $result;
	
// 	}
	
// 	function show_list_p($page,$cat_id){
// 		$db=new db();
// 		$start=($page-1)*6;
// 		$sql = "select class_id,goods_id,goods_sn,goods_name,brand_name,shop_price,goods_img 
// 				from xgj_greenfood_goods g left join xgj_brand b on b.brand_id=g.brand_id
// 				where g.parent_id=$cat_id limit ".$start.",6";
// 		$detail=$db->getAll($sql);
// 		return $detail;
	
// 	}
	
// 	function show_count_p($cat_id){
// 		$db=new db();
// 		$sql = "SELECT count(*) FROM  xgj_greenfood_goods where parent_id = $cat_id ";
// 		$result=$db->getOne($sql);
// 		return $result;
	
// 	}
// 	function show_list_g($page,$goods_id){
// 		$db=new db();
// 		$start=($page-1)*12;
// 		$sql = "SELECT * FROM xgj_greenfood_comment where goods_id=$goods_id limit ".$start.",12";
// 		$detail=$db->getAll($sql);
// 		return $detail;
	
// 	}
	
// 	/**
// 	 * 总条数
// 	 * @return string
// 	 */
// 	function show_count_g(){
// 		$db=new db();
// 		$sql = "SELECT count(*) FROM xgj_greenfood_comment";
// 		$result=$db->getOne($sql);
// 		return $result;
// 	}
	
// 	function getfoodslist($cat_id){
		
// 		$sql = "select class_id,goods_id,goods_sn,goods_name,goods_number,shop_price,goods_img
	
// 		from xgj_greenfood_goods where cat_id = $cat_id ";
		
// 		$detail = mysql_query($sql)or die("Invalid query: " . mysql_error());
		
// 		$result=array();
		
// 		while($row=mysql_fetch_array($detail,MYSQL_ASSOC )){
			
// 			$result[]=$row;
			
// 		}
		
// 		return $result;
		
// 	}
	
// 	function getcatname($cat_id){
		
// 		$sql = " select cat_name from xgj_greenfood_cat where cat_id= $cat_id ";
		
// 		$detail = mysql_query($sql)or die("Invalid query: " . mysql_error());
		
// 		$row=mysql_fetch_array($detail,MYSQL_ASSOC );
		
// 		return $row;
		
// 	}
	
// 		function getchildname($cat_id){
		
// 		$sql = " select cat_name,cat_id from xgj_greenfood_cat where parent_id= $cat_id order by cat_id desc";
		
// 		$detail = mysql_query($sql)or die("Invalid query: " . mysql_error());
		
// 		$result=array();
		
// 		while($row=mysql_fetch_array($detail,MYSQL_ASSOC )){
			
// 			$result[]=$row;
			
// 		}
		
// 		return $result;
		
// 	}
// /*
// *	根据品牌查询相关商品
// */
// 	function getcatelist($cat_id){
		
// 		$sql = "select goods_id,goods_sn,goods_name,brand_name,shop_price,goods_img 
// 				from xgj_greenfood_goods g left join xgj_brand b on b.brand_id=g.brand_id
// 				where g.cat_id=$cat_id ";
				
// 		$detail = mysql_query($sql)or die("Invalid query: " . mysql_error());
		
// 		$result = array();
		
// 		while($row=mysql_fetch_array($detail,MYSQL_ASSOC )){
			
// 			$result[]=$row;
			
// 		}
		
// 		return $result;
		
// 	}
	
// 	/*
// 	* 得到商品详情
// 	*/
// 	function getfoodgoodsinfo($goods_id){
		
// 		$sql = " select class_id,goods_id,goods_name,goods_sn,goods_brief,goods_diff,goods_desc,specification,goods_number,shop_price,promote_price,goods_img
		
// 				from xgj_greenfood_goods where goods_id = $goods_id ";
		
// 		$detail = mysql_query($sql)or die("Invalid query: " . mysql_error());
		
// 		$row=mysql_fetch_array($detail,MYSQL_ASSOC );
		
		
// 		return $row;
		
// 	}
	
// 	/*
// 	*得到缩略图详情
// 	*/
// 	function getthumb($goods_id){
		
// 		$sql = " select img_url from xgj_greenfood_goods_gallery where goods_id = $goods_id ";
		
// 		$detail = mysql_query($sql)or die("Invalid query: " . mysql_error());
		
// 		$result='';
		
// 		while($row=mysql_fetch_array($detail,MYSQL_ASSOC )){
			
// 			$result[]=$row;
			
// 		}
		
// 		return $result;
		
// 	}
}






















