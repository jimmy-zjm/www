<?php
/**
* @package WWW
* @see feed_center, user_cace, photo_lib, notification_center, user_application, user_relations, user_register
*/

require_once(WWW_DIR."/model/eu_model.php");
require_once(WWW_DIR."/libs/page.php");
class eugroup
{
	/**
	 * 欧团首页
	 */
	function euindex()
	{
        $tpl = get_smarty();
        
        $euOb= new eumodel();
        
        $navArr=$euOb->Primary_navbar();//导航分类
		
		//$sonArr=$euOb->getSoncate();//二级分类
		
		$parentArr=$euOb->getparentcat();
		
		$cat_count=count($euOb->getparentcat());
		
		$info=array();
		
		for($i=1;$i<=$cat_count;$i++){
		$info[]= $euOb->getrecommendgoods($i);
		}
		
		$re=$euOb->getBuyingGoods();
		
/* 		foreach($navArr as $v){
			
			foreach ($v as $key=>$value){
				
				if ($value['parent_id']==0){
					
					$cid[]="||";
			
				}else{
					$cid[]=$value['cat_id'];
					}
			
			}
			
		}
		$cat_id=implode(" ",$cid);
		
		$cate_id=explode("||",$cat_id); */
		
		//var_dump($cate_id);exit;
		$tpl->assign("info",$info);

		$tpl->assign("buy",$re);

		$tpl->assign("parentArr",$parentArr);
		
		$tpl->assign("navArr",$navArr);
		
		$tpl->display('euindex.tpl.html');
	}
    
	/**
	 * 二级页面列表页
	 */
	function category(){
	
		$cat_id=$_GET['cat_id'];//得到地址栏传值
	
		$euOb=new eumodel();
	
		$cate=$euOb->getSoncate($cat_id);//通过分类id得到分类名称
		
		$tpl = get_smarty();
	
		$tpl->assign("cate",$cate);//模板传值
		
		$tpl->display('eugrouplist.tpl.html');
	
	}
	
	/**
	 * 详情页
	 */
	function eugooddetails(){

		$goods_id=$_GET['goods_id'];//得到地址栏传值
		
		//判断是否有分页
        if(!isset($_GET['p'])){
			$page = 1;
		}else{
            $page = $_GET['p'];
		}

		$euOb=new eumodel();
		
		$comment_list=$euOb->show_list($page);//显示列表内容
		
		$comment_count=$euOb->show_count();//分页的总条数
		
		$t = new Page(12, $comment_count, $page, 5, "eugroup.php?goods_id=$goods_id&p=");
		
		$page=$t->subPageCss2();//分页样式

		$goods=$euOb->getGooddetailById($goods_id);//通过分类id得到商品信息
		//var_dump($goods);exit();
		$imgs = explode("|", $goods['goods_img']);//拆分图片字段
			
		$attribute= explode("|", $goods['goods_attribute']);//拆分颜色字段
				
		$tpl = get_smarty();
	
		//模板传值
		$tpl->assign("goods",$goods);
		
		$tpl->assign("imgs",$imgs);
		
		$tpl->assign("attribute",$attribute);
		
		$tpl->assign("page",$page);
		
		$tpl->assign("comment",$comment_list);
	
		$tpl->display('eugooddetails.tpl.html');
	
	}
	
	/**
	 * 代购攻略
	 */
	function euraiders()
	{
		$tpl = get_smarty();
		
		$tpl->display('euraiders.tpl.html');
	}
	
	/**
	 * 明日预告
	 */
	function euforeshow()
	{
		$tpl = get_smarty();
	
		$tpl->display('euforeshow.tpl.html');
	}
}