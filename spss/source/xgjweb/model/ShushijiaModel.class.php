<?php
require_once (WWW_DIR . "/libs/db.php");
//require_once (WWW_DIR . "/libs/UploadFile.class.php");

class ShushijiaModel{
	
	//获取某一篇文章
	public function getArticleInfo($article_id='',$cat_id=''){
		$db = new db();
		$where='';
		if(!empty($article_id)){
			$where='where article_id='.$article_id;
		}
		if(!empty($cat_id)){
			$where='where cat_id='.$cat_id;
		}
		$sql = "select * from xgj_article $where";
		$result = $db->getRow($sql);
		return $result;
		//print_r($db);
	}
	//获取某一分类下的所有文章
	public function getAllArticle($cat_id,$page,$each_disNums){
		$db = new db();
		$start = ($page-1)*$each_disNums;
		$sql = 'select * from xgj_article where cat_id='.$cat_id.' order by article_id asc limit '.$start.','.$each_disNums;
		$result = $db->getAll($sql);
		return $result;
	}
	//获取广告信息
	public function getAdv($ad_pos_id){
		$db = new db();
		
		$sql = 'select * from xgj_ad where is_on=1 and ad_pos_id='.$ad_pos_id;
		$result = $db->getAll($sql);
		return $result;
	}
	
	//获取视频信息
	public function getVedio($video_pos_id){
		$db = new db();
		$sql = 'select * from xgj_video where is_on=1 and video_pos_id='.$video_pos_id.' order by id asc';
		$result = $db->getAll($sql);
		foreach ($result as $k=>$v){
			$result[$k]['image']=getImages($v['image']);
			$result[$k]['video']=getImages($v['video']);
		}
		return $result;
	}
	
	//获取文章分页的总条数
	public function show_total_count($tab,$cat_id){
		$db=new db();
		$sql = "select count(*) from $tab where cat_id=$cat_id";
		$result=$db->getOne($sql);
		return $result;
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
	
	//获取产品线信息
    public function getProduct(){
		$db = new db();
    	$sql = "SELECT * FROM xgj_furnish_cat where is_show=1 order by sort_order asc ";
		$detail=$db->getAll($sql);
		foreach($detail as $k=>$v){
			$detail[$k]['image']=getImages($v['image']);
		}
		return $detail;
    }
    //获取文章分类
    public function getArticleCate($cat_id){
    	$db = new db();
    	$sql = "SELECT * FROM xgj_article_cat where parent_id=$cat_id";
    	$detail=$db->getAll($sql);
    	return $detail;
    }
}