<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");

class home{

	private $m;
	public function __construct(){
		$this->m = new db();
	}

	//获取健康舒适家广告图片
	public function getFurAd(){
		$data=$this->m->getAll("select * from xgj_ad_pos where id in(4,5,6,7,8,9,10,11) order by id asc");
		foreach ($data as $k=>$v){
			$data[$k]['list']=$this->m->getAll("select * from xgj_ad where ad_pos_id={$v['id']} order by sort_order asc");
		}
		foreach ($data as $key => $val) {
			foreach ($val['list'] as $k => &$v) {
				$data[$key]['list'][$k]['img']=getImages($v['image']);
			}
		}
		return $data;
	}

	
	//获取健康舒适家分类及产品
	public function getFurCate(){
		$data=$this->m->getAll("select * from xgj_furnish_cat where is_show=1");
		foreach ($data as $k=>$v){
			$data[$k]['list']=$this->m->getAll("select * from xgj_furnish_quote where cat_id={$v['cat_id']}");
		}
		foreach ($data as $key => $val) {
			foreach ($val['list'] as $k => &$v) {
				$data[$key]['list'][$k]['img']=getImages($v['img']);
			}
		}
		return $data;
	}

	//获取欧洲团代购或健康绿色食品所有父分类及产品
	public function getEuFoodGood($class_id){
		$data=$this->category($class_id);
		foreach ($data as $key => $val) {
			if(!empty($val['list'])){
				$ids='';
				foreach ($val['list'] as $k => $v) {
					$ids.=$v['id'].",";
					$data[$key]['goods']=$this->m->getAll("select * from xgj_eu_goods_new where is_rec=1 and cate_id in(".rtrim($ids,',').") limit 0,5");
				}
				
			}
		}
		foreach ($data as $key => $val) {
			if(!empty($val['goods'])){
				foreach ($val['goods'] as $k => &$v) {
					$data[$key]['goods'][$k]['face_image']=getImages($v['face_image']);
				}
			}
		}
		return $data;
	}
	//获取海外超市所有父分类及产品
	public function getOvGood($class_id){
		
		$data=$this->m->getAll("select * from xgj_ov_category where class_id=$class_id and pid=0 order by `order`");

		foreach ($data as $key => $val) {
			$data1=$this->m->getAll("select * from xgj_ov_category where class_id=$class_id and pid = {$val['id']} ");
			$cid='';
			foreach ($data1 as $k => $v) {
				$cid.=$v['id'].',';
			}
			$cid=rtrim($cid,',');
			
			$data[$key]['goods']=$this->m->getAll("select * from xgj_ov_goods where is_rec=1 and cate_id in ( {$cid} ) limit 0,5");
			
		}
		return $data;
	}
	public function getFedback($page,$num){
		$db=new db();
		$start=($page-1)*$num;
		$data=$this->m->getAll("select c.*,q.quote_name,h.*,u.user_name from xgj_furnish_comment c join xgj_users_house h on h.user_id=c.user_id join xgj_furnish_quote q on q.quote_id=c.quote_id join xgj_users u on c.user_id=u.user_id limit {$start},$num");
		return $data;
	}
	/**
	 *总条数
	 * @return string
	 */
	function getFedbackCount(){
		$db=new db();
		$sql = "select count(*) from xgj_furnish_comment c join xgj_users_house h on h.user_id=c.user_id join xgj_furnish_quote q on q.quote_id=c.quote_id join xgj_users u on c.user_id=u.user_id";
		$result=$db->getOne($sql);
		return $result;
	}

	//获取产品手册
	public function getManual($id){
		$data=$this->m->getAll("select quote_name,manual,alias from xgj_furnish_quote where cat_id=$id ");
		foreach ($data as $k => &$v) {
			$v['manual']=base64_encode($v['manual']);
		}
		return $data;
	}

 	//获取所有分类
	public function category($class_id){
		$data=$this->m->getAll("select * from xgj_eu_category where class_id=$class_id and pid=0");
		//后台两级分类 前台读取两级分类
		/*foreach ($data as $k=>$v){
			$data[$k]['list']=$this->m->getAll("select * from xgj_eu_category where class_id=$class_id and pid={$v['id']}");
		}*/
		//后台三级分类 前台读取后两级分类
		$pid='';
		foreach ($data as $k=>$v){
			$pid .= ','.$v['id'];
		}
		$pid =ltrim($pid,',');
		$data1=$this->m->getAll("select * from xgj_eu_category where class_id=$class_id and pid in ( {$pid} )");
		foreach ($data1 as $key=>$v1){
			$data1[$key]['list']=$this->m->getAll("select * from xgj_eu_category where class_id=$class_id and pid={$v1['id']}");
		}
		return $data1;
	}

	//海外超市分类----导航分类
	public function Ov_Category(){
        $cate_list=$this->m->getAll("select * from xgj_ov_category where class_id=2 and pid=0 and is_show=1");
        /*查询分类下推荐的六条数据*/
        $str='';
        foreach($cate_list as $k=>$v){
            $cate_list[$k]['list']=$this->m->getAll("select * from xgj_ov_category where class_id=2 and pid={$v['id']} and is_show=1");
        }
        return $cate_list;
	}
	//文章分类
	public function article_cat($parent_id){
		$data=$this->m->getAll("select * from xgj_article_cat where parent_id=$parent_id order by sort_order");
		return $data;
	}
	//读取一个分类
	public function article_catInfo($id){
		$data=$this->m->getRow("select * from xgj_article_cat where cat_id=$id");
		return $data;
	}
	//读取一分类文章
	public function article($id ,$p=0){
		if($p==0){
			$limit='';
		}else{
			$page=($p-1)*10;
			$limit = "limit $page,10";
		}
		//var_dump($p,"select * from xgj_article where cat_id=$id $limit");exit;
		$data=$this->m->getAll("select * from xgj_article where cat_id=$id $limit");
		foreach ($data as $k => $v) {
			$data[$k]['image']=getImages($v['image']);
		}
		return $data;
	}

	//读取一个文章
	public function articleInfo($id){
		$data=$this->m->getRow("select * from xgj_article where article_id=$id");
		$data['image']=getImages($data['image']);
		return $data;
	}


	//关于我们
	public function aboutus($id){
		$data['cat']=$this->article_cat($id);
		foreach ($data['cat'] as $key => $value) {
			$data['list'][$key]=$this->m->getRow("select * from xgj_article where cat_id={$value['cat_id']}");
		}
		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['image']=getImages($v['image']);
		}
		return $data;
	}

	//积分和券分类
	public function integral($id){
		$data['cat']=$this->article_cat($id);
		foreach ($data['cat'] as $key => $value) {
			$data['cat'][$key]['s_cat']=$this->article_cat($value['cat_id']);
		}
		return $data;
	}

	//积分和券详情
	public function integral_detail($id){
		$data=$this->m->getRow("select * from xgj_article where article_id=$id");
		return $data;
	}



	//查询所有cat_id下的文章
	public function pay_list_all($where){
		$data=$this->m->getAll("select * from xgj_article where cat_id $where");
		return $data;
	}

	//传入父id得到所有子id，拼接为in语句
	public function implodeInByCateId($cate_id){
        $this->cate_arr = array();
        $cate = $this->_getCateTree($this->getCateList(),$cate_id);
        $ids  = array();
        foreach ($cate as $v) {
            $ids[] = $v['cat_id'];
        }
        if(empty($cate)){
          return ' IN('.$cate_id.')';
        }else{
          return ' IN('.$cate_id.','.implode(',',$ids).')';
        }
    }

	//递归函数
	public function _getCateTree($cate_list, $pid=0, $deep=0, $deep_limit=-1){
	      if(!is_array($cate_list)) return array();
	        //必须是静态的,或者全局的,递归有作用域问题
	        foreach ($cate_list as $cate) {
	            //判断设置的深度
	            if($deep_limit!=-1 and $deep_limit < $deep) break;
	            if($cate['parent_id'] == $pid){
	                $cate['deep']     = $deep;
	                $this->cate_arr[] = $cate;
	                $this->_getCateTree($cate_list, $cate['cat_id'], $deep+1, $deep_limit);
	            }
	        }
	        return $this->cate_arr;
    }

    //获取所有的文章分类
    public function getCateList(){
    	$rs=$this->m->getAll("select * from xgj_article_cat");
      	return $rs;
    }

    //获取用户id中所有系统名称
    public function getQuoteName($id){
    	$arr=$this->m->getAll("select d.quote_name,d.order_id,q.alias,d.quote_id from xgj_furnish_order_info o join xgj_furnish_order_detail d on o.order_id=d.order_id join xgj_furnish_quote q on d.quote_id=q.quote_id where o.user_id=$id order by d.order_id desc");
    	// foreach ($arr as $k => $v) {
    		
    	// }
    	return $arr;
    }


    //获取订单id查找联系人地址
    public function getHouseAddr($id){
    	$arr=$this->m->getRow("select h.*,a.a_name,a.a_mobile_phone from xgj_furnish_order_info o join xgj_users_house h on o.house_id=h.house_id join xgj_address a on h.user_id = a.user_id where o.order_id=$id");
    	return $arr;
    }

	public function video($id){
		$time = time();
		$mysql = new db();
		$sql = "select * from xgj_video where start_time<$time and end_time>$time and is_on = 1 and is_home_page = 1 and video_pos_id='{$id}' limit 1";
		$data = $mysql->getAll($sql);

		return $data;
	}

	public function video_pos(){
		$mysql = new db();
		$sql = "select * from xgj_video_pos where pid=12 order by id ASC limit 5";
		$data = $mysql->getAll($sql);
		return $data;
	}

	/**
	 * 查经销商信息
	 */
	public function xgj_furnish_dealer($xcd){
		$db=new db();
		$sql = "SELECT d_service_city_all,d_province,d_city,d_area FROM xgj_furnish_dealer WHERE d_runstatus='1'";
		$result=$db->getAll($sql);
		$retule = false;
		foreach ($result as $k => $v) {
			$citys = $v['d_province'].'-'.$v['d_city'].'-'.$v['d_area'];
			$city = explode('|', $v['d_service_city_all']);
			if ($citys == $xcd) $retule = true;
			else if (in_array($xcd, $city)) $retule = true;
		}
		return $retule;
	}

	// public function video_data($video_id){
	// 	$time = time();
	// 	$mysql = new db();

	// 	$sql = "select * from xgj_video where start_time<$time and end_time>$time and is_on = 1 and id = $video_id";
	// 	$video_data = $mysql->getAll($sql);

	// 	$sql = "select id,title from xgj_video where start_time<$time and end_time>$time and is_on = 1 and video_pos_id =".$video_data['0']['video_pos_id'];
	// 	$pos_data = $mysql->getAll($sql);

	// 	$sql = "select * from xgj_video_pos where id<>1";
	// 	$pos = $mysql->getAll($sql);

	// 	for ($i=0; $i <count($pos) ; $i++) {
	// 		$sql = "select * from xgj_video where start_time<$time and end_time>$time and is_on = 1 and video_pos_id<>1 and video_pos_id = ".$pos[$i]['id']." limit 6";
	// 		$data['video_data_all'][$pos[$i]['name']] = $mysql->getAll($sql);
	// 	}
		
	// 	$data['video_data'] = $video_data;

	// 	$data['pos_data'] = $pos_data;
	
	// 	return $data;
	// }
	public function video_data($video_id,$id){
		$time = time();
		$mysql = new db();


		
		$sql = "select * from xgj_video_pos where pid=12 order by id ASC";
		$data['all'] = $mysql->getAll($sql);

		if(preg_match("/^[0-9]*$/", $video_id) && !empty($video_id)){
   			// empty($video_id)?$video_id=$data['all']['0']['id']:$video_id=$video_id;
   			$sql = "select * from xgj_video where video_pos_id='{$video_id}' order by id ASC";
			$data['list'] = $mysql->getAll($sql);
		}
		
		if (!empty($data['list']['0']['id']) || !empty($id)) {
			empty($id)?$id=$data['list']['0']['id']:$id=$id;
			$sql = "select * from xgj_video where id=$id";
			$data['row'] = $mysql->getRow($sql);
		}else{
			$data['row'] = '';
		}
	
		// if ($data['row']['video_pos_id'] != $video_id) {
		// 	header("Location:index.php?error");exit;
		// }

		if(preg_match("/^[0-9]*$/", $video_id) && !empty($video_id)){
			$sql = "select * from xgj_video_pos where pid=$video_id order by id ASC";
			$data['rows'] = $mysql->getAll($sql);
		
			foreach ($data['rows'] as $key => $value) {
				$sql = "select * from xgj_video where video_pos_id='{$value['id']}' order by id ASC";
				$data['info'][] = $mysql->getAll($sql);
			}
		}

		return $data;
	}
	
	public function video_data_info($id){
		$mysql = new db();
		if(preg_match("/^[0-9]*$/", $id) && !empty($id)){
			$sql = "select * from xgj_video where id=$id";
			$data['row'] = $mysql->getRow($sql);
			$pid= $data['row']['video_pos_id'];
			$sql = "select * from xgj_video where video_pos_id=$pid order by id ASC";
			$data['info'] = $mysql->getAll($sql);
		}else{
			$data='';
		}
		
		return $data;
	}

	public function image(){
		$mysql = new db();

		$time = time();

		$sql = "select * from xgj_ad where is_on = 1 and start_time<$time and end_time>$time and ad_pos_id>41 and ad_pos_id<51";
		$data = $mysql->getAll($sql);

		foreach ($data as $key => $value) {
			if ($value['ad_pos_id']=='42') {
				$img['0']['1'][] = $value;
			}
			if ($value['ad_pos_id']=='43') {
				$img['0']['2'][] = $value;
			}
			if ($value['ad_pos_id']=='44') {
				$img['0']['3'][] = $value;
			}
			if ($value['ad_pos_id']=='45') {
				$img['1']['1'][] = $value;
			}
			if ($value['ad_pos_id']=='46') {
				$img['1']['2'][] = $value;
			}
			if ($value['ad_pos_id']=='47') {
				$img['1']['3'][] = $value;
			}
			if ($value['ad_pos_id']=='48') {
				$img['2']['1'][] = $value;
			}
			if ($value['ad_pos_id']=='49') {
				$img['2']['2'][] = $value;
			}
			if ($value['ad_pos_id']=='50') {
				$img['2']['3'][] = $value;
			}

		}

		//以下是删除超过数量的广告 热卖最大为3个 推荐最大为2个 右侧广告最大为30个
		for ($i=0; $i < 3 ; $i++) { 
			if (!empty($img[$i]['1'])) {
				if (count($img[$i]['1']) >3){
					foreach ($img[$i]['1'] as $key => $value) {
						if ($key >= 3) {
							unset($img[$i]['1'][$key]);
						}
					}
				}
			}else{
				$img[$i]['1'] = '';
			}
			if (!empty($img[$i]['2'])) {
				if (count($img[$i]['2']) >2){
					foreach ($img[$i]['2'] as $key => $value) {
						if ($key >= 2) {
							unset($img[$i]['2'][$key]);
						}
					}
				}
			}else{
				$img[$i]['2'] = '';
			}
			if (!empty($img[$i]['3'])) {
				if (count($img[$i]['3']) >30){
					foreach ($img[$i]['3'] as $key => $value) {
						if ($key >= 30) {
							unset($img[$i]['3'][$key]);
						}
					}
				}
			}else{
				$img[$i]['3'] = '';
			}
		}
		
		return $img;
		
		
	}

	public function contactus(){
		$mysql = new db();
		// $sql = "select a.title title,a.content content,a.add_time add_time,a.image image from xgj_article_cat c,xgj_article a where c.cat_id=a.cat_id and c.parent_id=75";

		$sql = "select * from xgj_article where cat_id=79";
		$data['0'] = $mysql->getAll($sql);
		$sql = "select * from xgj_article where cat_id=80";
		$data['1'] = $mysql->getAll($sql);

		return $data;
	}

	public function cooperationlist(){
		$mysql = new db();
		$sql = "select * from xgj_article where cat_id=81";
		$data['0']= $mysql->getAll($sql);
		$sql = "select * from xgj_article where cat_id=82";
		$data['1'] = $mysql->getAll($sql);
		return $data;
	}

	public function select_cooperationlist($p){
		$p = intval($p);
		if (!empty($p)) {
			$mysql = new db();
			$page = ($p-1)*5;
			$sql = "select * from xgj_article where cat_id=81 limit $page,5";
			$data['0']= $mysql->getAll($sql);
			// $sql = "select * from xgj_article where cat_id=82";
			// $data['1'] = $mysql->getAll($sql);
			return $data;
		}
	}

	public function cooperationinfo($article_id){
		$mysql = new db();
		$sql = "select * from xgj_article where article_id=$article_id and cat_id=81";
		$data['0']= $mysql->getAll($sql);
		$sql = "select * from xgj_article where cat_id=82";
		$data['1'] = $mysql->getAll($sql);
		return $data;
	}

	public function join(){
		$mysql = new db();
		$sql = "select * from xgj_article where cat_id=77 limit 1";
		$data = $mysql->getAll($sql);
		return $data;
	}

	public function join_add($data){
		$mysql = new db();
		$return = $mysql->add('xgj_join',$data);
		return $return;
	}

	public function joblist(){
		$mysql = new db();
		$sql = "select * from xgj_job";
		$data = $mysql->getAll($sql);
		return $data;
	}

	public function select_joblist($p){
		$p = intval($p);
		if (!empty($p)) {
			$mysql = new db();
			$page = ($p-1)*5;
			$sql = "select * from xgj_job limit $page,5";
			$data = $mysql->getAll($sql);
			return $data;
		}
	}

	public function jobinfo($id){
		$id = intval($id);
		if (!empty($id)) {
			$mysql = new db();
			$sql = "select * from xgj_job where isopen=0 and id=$id limit 1";
			$data = $mysql->getAll($sql);
			return $data;
		}
	}

	public function unitprice($cid){
		$mysql = new db();
		$sql = "select unitprice from xgj_furnish_quote where quote_id=$cid limit 1";
		$data = $mysql->getAll($sql);
		return $data;
	}

	//获取所有合作品牌
	public function getCbrandAll($class_id){
		$db=new db();
		$sql="select * from xgj_cbrand c where c.class_id=$class_id and c.is_show=1 order by c.order asc";
		$data = $db->getAll($sql);
		foreach ($data as $k=>&$v){
			$v['logo']=getImages($v['logo']);
		}
		return $data;
	}

	//根据id获取合作品牌信息
	public function getCbrandOne($id){
		$db=new db();
		$sql="select * from xgj_cbrand where brand_id=$id";
		$data = $db->getRow($sql);
		$data['list']=$db->getAll("select * from xgj_cbrand_info where b_id=$id");

		return $data;
	}

	//根据id获取合作品牌应用列表
	public function getCbrandList($id,$page,$num){
		$db=new db();
        $page = ($page-1)*$num;
        $limit = " limit $page,$num";

		$data=$db->getAll("select * from xgj_cbrand_info where b_id=$id $limit ");
		foreach ($data as $k=>&$v){
			$v['image']=getImages($v['image']);
		}
		return $data;
	}
	//根据id获取合作品牌应用列表总条数
	function getCbrandCount($id){
		$db=new db();
		$sql = "SELECT count(*) FROM xgj_cbrand_info where b_id=$id";
		$result=$db->getOne($sql);
		return $result;
	}

	//根据id获取合作品牌图册
	public function getCbrandImage($id){
		$db=new db();
		$data=$db->getAll("select * from xgj_cbrand_image where b_id=$id");
		foreach ($data as $k=>&$v){
			$v['url']=getImages($v['url']);
		}
		return $data;
	}

	//获取首页brand图
	function getImg(){
		$db=new db();
		$sql = "SELECT * FROM xgj_ad WHERE ad_pos_id='3' order by sort_order , id asc";
		$result=$db->getAll($sql);
		foreach ($result as $k=>&$v){
			$v['image']=getImages($v['image']);
		}
		return $result;
	}

	public function getDemo($id){
    	$db = new db();
    	$sql = "SELECT * FROM xgj_furnish_quote where quote_id=$id";
    	$row=$db->getRow($sql);
    	return $row;
    }
}