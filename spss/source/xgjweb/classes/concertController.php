<?php
/**
 * Created by PhpStorm.
 * User: 唐文权
 * Date: 2016/01/06
 * Time: 14:29
 */

require_once(WWW_DIR."/model/searchModel.php");
require_once(WWW_DIR."/model/concertModel.php");

class concertController{

	/**
	 * concertGoods	关注商品
	 */
	function concertGoods(){

		//调用判断是否登录的方法
		user_check_logon();

		$tpl = get_smarty();

		$classId = $_GET["classId"];
		$goodsId = $_GET["goodsId"];

		$concert = new concertModel();	//实例化concert模型
		$search = new searchModel();	//实例化search模型

		//判断class_id
		if($classId == 1){	//如果是健康舒适家
			$searchResult = $search->searchInfoById("`xgj_furnish_goods`",$goodsId);
		}elseif($classId == 2){	//如果是欧洲团代购
			$searchResult = $search->searchInfoById("`xgj_eu_goods`",$goodsId);
		}elseif($classId == 3){	//如果是绿色食品
			$searchResult = $search->searchInfoById("`xgj_greenfood_goods`",$goodsId);
		}else{
			exit("非法操作");
		}

		//判断商品是否被收藏
		$concertStatus = $concert->concertList($_SESSION["userId"],$searchResult["class_id"],$searchResult["goods_id"]);

		//如果为空(没有收藏)
		if(empty($concertStatus)){
			$concertResult = $concert->concertGoods($_SESSION["userId"],$searchResult["goods_id"],$searchResult["class_id"],$searchResult["goods_img"],$searchResult["goods_name"],$searchResult["shop_price"]);
		}

	}

}
?>