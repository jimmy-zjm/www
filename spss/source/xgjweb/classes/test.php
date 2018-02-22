<?php
/**
* @package WWW
* @see feed_center, user_cace, photo_lib, notification_center, user_application, user_relations, user_register
*/
require_once(WWW_DIR."/model/xgj_furnish.php");
require_once(WWW_DIR."/conf/mysql_db.php");
class test
{
	//参数
	 public	function  parameter(){
	 	$tpl = get_smarty();
		$quote_id=!empty($_GET['cid'])?$_GET['cid']:'1';
		$content=$this->content($quote_id,'parameter');
		$tpl->assign("content",$content);	
		$pn= new home();
		$eu_tree=$pn->category(1);
		$tpl->assign("eu_tree", $eu_tree);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
	 	$tpl->display('fu_parameter.html');
	 }
	 //施工标准
	  public	function  standard(){
	 	$tpl = get_smarty();
		$quote_id=!empty($_GET['cid'])?$_GET['cid']:'1';
		$content=$this->content($quote_id,'standard');
		$tpl->assign("content",$content);	
		$pn= new home();
		$eu_tree=$pn->category(1);
		$tpl->assign("eu_tree", $eu_tree);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
	 	$tpl->display('fu_standard.html');
	 }
	 //产品优势
	  public	function  advantage(){
	 	$tpl = get_smarty();
		$quote_id=!empty($_GET['cid'])?$_GET['cid']:'1';
		$content=$this->content($quote_id,'advantage');
		$tpl->assign("content",$content);	
		$pn= new home();
		$eu_tree=$pn->category(1);
		$tpl->assign("eu_tree", $eu_tree);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
	 	$tpl->display('fu_advantage.html');
	 }
	 //概述
	  public	function  summary(){
	 	$tpl = get_smarty();
		$quote_id=!empty($_GET['cid'])?$_GET['cid']:'1';
		$content=$this->content($quote_id,'summary');
		$tpl->assign("content",$content);	
		$pn= new home();
		$eu_tree=$pn->category(1);
		$tpl->assign("eu_tree", $eu_tree);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
	 	$tpl->display('fu_summary.html');
	 }
	 //原理
	  public	function  principle(){
	 	$tpl = get_smarty();
		
		$quote_id=!empty($_GET['cid'])?$_GET['cid']:'1';
		$content=$this->content($quote_id,'principle,principle_video,principle_video_img');
		$tpl->assign("content",$content);	
		$pn= new home();
		$eu_tree=$pn->category(1);
		$tpl->assign("eu_tree", $eu_tree);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
	 	$tpl->display('fu_principle.html');
	 }
	 //售后
	 public	function  service(){
	 	$tpl = get_smarty();
		$quote_id=!empty($_GET['cid'])?$_GET['cid']:'1';
		$content=$this->content($quote_id,'customer');
		$tpl->assign("content",$content);	
		$pn= new home();
		$eu_tree=$pn->category(1);
		$tpl->assign("eu_tree", $eu_tree);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
		$tpl->display('fu_service.html');
	 }

	public	function  content($id,$field=''){
		$db=new db();
		if(!empty($field))
			$field.=',';
		$sql = "select a.quote_id,a.quote_name,$field b.cat_name ,b.cat_id from `xgj_furnish_quote` a join `xgj_furnish_cat` b on  a.cat_id = b.cat_id where quote_id=$id";
		$result=$db->getRow($sql);
		return $result;
	}

}
