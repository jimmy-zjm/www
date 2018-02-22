<?php
require_once(WWW_DIR."/model/xgj_greenfoods.php");
require_once(WWW_DIR."/libs/page.php");


class greenfood{
	
/*
*  健康食品首页
*/	
	function greenfoodindex(){
		
		$food = new foods;
		
		$cat_count=count($food->getcat());
		
		$info=array();
		
		for($i=1;$i<=$cat_count;$i++){
		$info[]= $food->getrecommend($i);
		}
		
		$tpl = get_smarty();
		
		if(!empty($_GET)){//判断是否有传值
		
		$ver = $_GET['cat_id'];
		
		$tpl->assign("ver",$ver);
		
		}
		
		$tpl->assign("info",$info);
		
		//$tpl->assign("cate",$cate);
		
		$tpl->display('greenfood.tpl.html');
		
	}
/*
*商品列表页
*/
	
	
	
	function foodslist(){
		
		if(isset($_GET)){
		
		$ver = $_GET['cat_id'];
		
		}
		
		if(!isset($_GET['p'])){//判断是否有分页传值
			$page = 1;
		}else{
            $page = $_GET['p'];
		}
		
		$food = new foods;
		
		$child=$food->getchildname($ver);
		
		if(empty($child)){
		
		$goods_list=$food->show_list($page,$ver);//显示列表内容
		
		$goods_count=$food->show_count($ver);//分页的总条数
		
		$t = new Page(6, $goods_count, $page, 5, "greenfood.php?cat_id=$ver&p=");
		
		$page=$t->subPageCss2();//分页样式
		
		$cat=$food->getcatname($ver);
		
		$name=$cat['cat_name'];//分类名称
		
		$tpl = get_smarty();
		
		$tpl->assign("ver",$ver);
		
		$tpl->assign("cat_name",$name);
		
		$tpl->assign("info",$goods_list);
		
		$tpl->assign("page",$page);

		$tpl->display('greenfoodlist.tpl.html');
		
		}else{
			
		$goods_list=$food->show_list_p($page,$ver);//显示列表内容
		
		$goods_count=$food->show_count_p($ver);//分页的总条数
		
		$t = new Page(6, $goods_count, $page, 5, "greenfood.php?cat_id=$ver&p=");
		
		$page=$t->subPageCss2();//分页样式
		
		$cat=$food->getcatname($ver);
		
		
		
		$name=$cat['cat_name'];
		
		$tpl = get_smarty();
		
		$tpl->assign("ver",$ver);
		
		$tpl->assign("cat_name",$name);
		
		$tpl->assign("info",$goods_list);
		
		$tpl->assign("page",$page);

		$tpl->display('greenfoodlist.tpl.html');
			
		}
		
	}
/*
*商品详情页面
*/
	
	
	function foodsinfo(){
		
		if(isset($_GET)){
		
		$goods_id=$_GET['goods_id'];
		
		}
		
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
            $page = $_GET['p'];
		}
		
		$foodsinfo = new foods;
		
		$comment_list=$foodsinfo->show_list_g($page,$goods_id);//显示列表内容
		
		$comment_count=$foodsinfo->show_count_g();//分页的总条数
		
		$t = new Page(12, $comment_count, $page, 5, "greenfood.php?goods_id=$goods_id&p=");
		
		$page=$t->subPageCss2();//分页样式
		
		$info=$foodsinfo->getfoodgoodsinfo($goods_id);
		
		$thumb=$foodsinfo->getthumb($goods_id);
		
		$diff=explode('||',$info['goods_diff']);
				
		$tpl = get_smarty();
		
		$tpl->assign('info',$info);
		
		$tpl->assign('diff',$diff);
		
		$tpl->assign('thumb',$thumb);
		
		$tpl->assign('page',$page);
		
		$tpl->assign('comment',$comment_list);
				
		$tpl->display('greenfoodinfo.tpl.html');
		
	}
	
	function know(){
		
		$tpl = get_smarty();
		
		$tpl->display('greenfoodknow.tpl.html');
		
	}
		
	function strategy(){
		
		$tpl = get_smarty();
		
		$tpl->display('greenfoodstrategy.tpl.html');
		
	}
	
}













