<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");
/*
* @param id       分类id
* @param cat_id	  分类id
* @return $row 	  分类名称
* @return $result 分类下的产品
*/
class furnishlist{
/*-------------------------------------------------------------------------------------------------------------------------------*/	
	/*
	* @param $cid 报价分类id 
	* @param $type 类型id  经济 1 /舒适 2 /豪华 3 /空 0
	* @return $re 产品结果集
	*/
	function getInfoFromId($cid,$type=0){
		
		$db= new db();
		$re=$db->getAll("select l.*,g.promote_price,c.c_name 
		from xgj_quote_child_list l 
		left join xgj_fur_formula_cid c on l.quote_id=c.cid 
		left join xgj_furnish_goods g on l.goods_sn=g.goods_sn 
		where l.quote_id=$cid and l.level = $type ");
		return $re;
	}
	/*
	* @return $re 产品结果
	*/
	function hostmax(){//获取最大功率主机(新风)
		$db=new db();
		$re=$db->getAll("select l.*,g.promote_price
		from xgj_quote_child_list l 
		left join xgj_furnish_goods g on l.goods_sn=g.goods_sn 
		where maxarea = 230 ");
		return $re;
	}
	/*
	* @sort_id 父类id
	* @return $ids 子类id结果集
	*/
	function getChildrenIds ($sort_id){//得到当前分类下的所有子分类
       $db = new db();
       $ids = '';
       $sql = "SELECT l.* ,g.promote_price
	   FROM xgj_quote_child_list l left join xgj_furnish_goods g on l.goods_sn=g.goods_sn 
	   WHERE `chf_id` = $sort_id ";
       $result = $db->getAll($sql);
       if ($result)
       {
           foreach ($result as $key=>$val)
				{
               $ids .= '+'.'('.$val['formula'].')'.'*'.$val['promote_price'];
               $ids .= $this->getChildrenIds ($val['id']);
			   }
       }
       return $ids;
		}
	/*
	* @sort_id 父类id
	* @return $ids 子类id结果集
	*/
	function getChildId ($sort_id){//得到当前分类下的所有子分类
       $db = new db();
       $ids = '';
       $sql = "SELECT l.* ,g.promote_price
	   FROM xgj_quote_child_list l left join xgj_furnish_goods g on l.goods_sn=g.goods_sn 
	   WHERE `chf_id` = $sort_id  ";
       $result = $db->getAll($sql);
       if ($result)
       {
           foreach ($result as $key=>$val)
				{
               $ids .= '+'.'('.$val['formula'].')'.'*'.$val['promote_price'];
               $ids .= $this->getChildId ($val['id']);
			   }
       }
       return $ids;
		}
	/*
	* @param $exp 字符串参数
	* @param $arr_n 存放剩余运算内容
	* @param $arr_op 存放结果
	* @param $s 
	* @param $t
	* @param $temp 
	* @param $quote 参数
	* @param $endquote 参数
	* @return $n2 运算结果
	*/
	function calcexp( $exp ){//字符串进行四则运算
			$arr_n = array();
			$arr_op = array();
			while( ($s = array_pop( $exp )) != '' ){
			if( $s == '(' ){
			$temp = array(); $quote = 1; $endquote = 0;
			while( ($t = array_pop($exp)) != '' ){
			if( $t == '(' ){
			$quote++;
			}
			if( $t == ')' ){
			$endquote++;
			if( $quote == $endquote ){
			break;
			}
			}
			array_push($temp, $t);
			}
			$temp = array_reverse($temp);
			array_push($arr_n, $this->calcexp($temp) );
			
			}else if( $s == '*' || $s == '/' ){
			$n2 = array_pop($exp);
			if( $n2 == '(' ){
			$temp = array(); $quote = 1; $endquote = 0;
			while( ($t = array_pop($exp)) != '' ){
			if( $t == '(' ){
			$quote++;
			}
			if( $t == ')' ){
			$endquote++;
			if( $quote == $endquote )
			break;
			}
			array_push($temp, $t);
			}
			$temp = array_reverse($temp);
			$n2 = $this->calcexp($temp);
			}
			$op = $s;
			$n1 = array_pop($arr_n);
			$result = $this->operation($n1, $op, $n2);
			array_push($arr_n, $result);
			}elseif( $s == '+' || $s == '-' ){
			array_push($arr_op, $s);
			}else{
			array_push($arr_n, $s);
			}
			}
			$n2 = array_pop($arr_n);
			while( ($op = array_pop($arr_op)) != '' ){
			$n1 = array_pop($arr_n);
			$n2 = $this->operation($n1, $op, $n2);
			}
			return $n2;
			}
	/*
	* @param $n1
	* @param $n2
	* @param $op
	* return 运算结果
	*/		
	function operation( $n1, $op, $n2 ){
			switch ($op) {
			case '+':
			return (float)$n1 + (float)$n2;
			break;
			case '-':
			return (float)$n1 - (float)$n2;
			break;
			case '*':
			return (float)$n1 * (float)$n2;
			break;
			case '/':
			return (float)$n1 / (float)$n2;
			break;
			}
			} 
	/*
	* @param $str 需要增加前缀的字符串
	* @param $char 需要增加的前缀内容
	* @param $n 增加前缀的数量
	* @return $str 结果
	*/
	function str_prefix($str, $n=1, $char=" "){//添加字符串前缀
			  for ($x=0;$x<$n;$x++){$str = $char.$str;}
			  return $str;
			}
	/*
	* @param $str 需要增加后缀的字符串
	* @param $char 需要增加的后缀内容
	* @param $n 增加后缀的数量
	* @return $str 结果
	*/
	function str_suffix($str, $n=1, $char=" "){//添加字符串后缀
			  for ($x=0;$x<$n;$x++){$str = $str.$char;}
			  return $str;
			}

	// 获取健康舒适家居列表数据
	 public function  get_furnish_list($id,$cat_id){
	 	
	 	$db=new db();
	 	$sql = "select * from `xgj_furnish_quote` where cat_id=$id";
	 	$data['list']=$db->getAll($sql);
	 	foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['img']=getImages($v['img']);
		}
		$data['info']=$db->getAll("select * from xgj_article where cat_id={$cat_id}");
		foreach ($data['info'] as $key => $val) {
			$data['info'][$key]['image']=getImages($val['image']);
		}
	 	return $data;
	 }
	 //获取选定的列表
	 public function  get_quote_list($id){
	 	$db=new db();
	 	$sql = "select * from `xgj_furnish_quote` where quote_id in ($id)";
	 	$data=$db->getAll($sql);
	 	foreach ($data as $key => $val) {
			$data[$key]['img']=getImages($val['img']);
		}
	 	return $data;
	 }
	 //获取购物车信息
    public function getCart(){
    	if (!empty($_SESSION['userId'])) {
    		$db = new db();
	    	$sql = "SELECT * FROM xgj_furnish_cart where user_id={$_SESSION['userId']}";
			$detail=$db->getAll($sql);
			return $detail;
    	}else{
    		return ;
    	}
    }
}




















