<?php
namespace Home\Model;
use Think\Model;
class IndexModel extends Model {
	protected $autoCheckFields =false;

	//读取一分类文章
	public function article($id){
		
		$data=M('xgj_article')->where(array('cat_id'=>$id))->select();
		return $data;
	}
		//文章分类
	public function article_cat($parent_id){
		$data=M('xgj_article_cat')->where(array('parent_id'=>$parent_id))->order('sort_order')->select();
		return $data;
	}
		//读取一分类文章
	public function artdetail($id){
		
		$data=M('xgj_article')->where(array('article_id'=>$id))->find();
		return $data;
	}
	//获取所有合作品牌
	public function getCbrandAll($class_id){
		$where['class_id']=$class_id;
		$where['is_show']=1;
		$data=M('xgj_cbrand a')->where($where)->order('a.order')->select();
		return $data;
	}
    //根据id获取合作品牌图册
	public function getCbrandImage($id){
		$data=M('xgj_cbrand_image')->where(array('b_id'=>$id))->select();
		return $data;
	}
	//根据id获取合作品牌信息
	public function getCbrandOne($id){
		
		$data=M('xgj_cbrand')->where(array('brand_id'=>$id))->find();		
		//$data['list']=$db->getAll("select * from xgj_cbrand_info where b_id=$id");
		return $data;
	}
	//根据id获取合作品牌应用列表
	public function getCbrandList($id,$page,$num){
		$page = ($page-1)*$num;
		$data=M('xgj_cbrand_info')->where(array('b_id'=>$id))->limit($page,$num)->select();
		
		return $data;
	}
	//根据id获取合作品牌应用列表总条数
	function getCbrandCount($id){
		$result= M('xgj_cbrand_info')->where(array('b_id'=>$id))->count("id");
		return $result;
	}

	//读取一个分类下的所有文章
	public function getArticle($cat_id){
		$data['cat_name']=M('xgj_article_cat')->where(array('cat_id'=>$cat_id))->getField('cat_name');
		$data['info']= M('xgj_article')->where(array('cat_id'=>$cat_id))->select();
		return $data;
	}

	//读取一个分类下的所有文章
	public function getArticleOne($a_id){
		$data= M('xgj_article a')->field('a.*,c.cat_name')->join('xgj_article_cat c on c.cat_id=a.cat_id')->where(array('a.article_id'=>$a_id))->find();;
		return $data;
	}


	//获取广告
	public function getBanner($ad_pos_id){
		$time = time();
		$map['ad_pos_id']  = $ad_pos_id;
		$map['start_time'] = array('ELT',$time);
		$map['end_time']   = array('EGT',$time);
		$map['is_on']      = 1;
		$data= M('xgj_ad')->field('url,image')->where($map)->select();
		return $data;
	}
}