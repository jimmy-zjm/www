<?php
namespace Admin\Controller\Furnish;
use \Admin\Controller\Index\AdminController;

/**
 * 后台健康舒适家居产品管理
 * @author Administrator
 */
class GoodsController extends AdminController{
	private $m;
	public function __construct(){
		parent::__construct();
		$this->m = new \Admin\Model\Furnish\GoodsModel;
	}
	
	/**
	 * 产品列表
	 */
	public function index(){
		$data  = $this->m->getAll();
		//获取商品的品牌
		$data_ = $this->m->getData();

        $this->assign('page', $data['page']);
        $this->assign('brand_list', $data_['brand_list']);
        $this->assign('goods_list', $data['goods_list']);
        $this->display();
	}

	/*
	 展示添加商品的页面
	*/
	public function add(){
		//获取商品的品牌
		$data = $this->m->getData();
		$this->assign('brand_list', $data['brand_list']);
		$this->display();
	}
	
	/*
	 执行添加商品
	*/
	public function insert(){

		if(empty($_POST['shop_price_a'])) $_POST['shop_price_a'] = '0';

		//获取品牌数据
    	$brand = new \Admin\Model\Furnish\BrandModel;
    	//根据b_id查询brand_name（品牌名称），
    	if(!empty($_POST['b_id'])){
    		$_POST['goods_brand']=$brand->where("brand_id={$_POST['b_id']}")->getField('name');
    	}
		//var_dump($_POST);exit;
    	if(!IS_POST) $this->redirect('index');
		if($this->m->create($_POST,1)){
			if($this->m->add() !== false){
				$this->success('添加成功',U('index'));
				die;
			}
		}
		$this->error($this->m->getError());
	}

	/*
	 展示编辑商品的页面
	*/
	public function edit($id){
		//接收商品的id
		$id = intval($id);
		if(empty($id)) $this->redirect('参数非法');
	
		//获取商品的品牌,
		$data = $this->m->getData();
	
		//获取商品的信息,用于回填数据
		$goods = $this->m->getGoods($id);
		$this->assign('goods', $goods['goods']);
		$this->assign('goods_image_list', $goods['image']);
		$this->assign('brand_list', $data['brand_list']);
		$this->display();
	}
	
	/*
	 执行修改商品
	*/
	public function update(){

		if(empty($_POST['shop_price_a'])) $_POST['shop_price_a'] = '0';
		//获取品牌数据
		$brand = new \Admin\Model\Furnish\BrandModel;
		//根据b_id查询brand_name（品牌名称），
		if(!empty($_POST['b_id'])){
			$_POST['goods_brand']=$brand->where("brand_id={$_POST['b_id']}")->getField('name');
		}
		if(!IS_POST) $this->redirect('index');
		if($d = $this->m->create($_POST,1)){
			if($this->m->save() !== false){
				$this->success('修改成功',U('index'));
				die;
			}
		}
		$this->error($this->m->getError());
	}
	

	/*
	 将商品放入回收站
	*/
	public function delete($id){
		$id = intval($id);
		if(M('xgj_furnish_goods')->where(array('goods_id'=>$id))->setField('is_delete',1)){
			$this->success('放入回收站成功',U('index'));
			die;
		}else{
			$this->error($this->m->getError());
		}
	}
	
	/*
	 商品回收站
	*/
	public function recycle(){
		//获取所有商品数据
		$data  = $this->m->getAll(1);
		//获取商品的分类, 品牌,类型数据
		$data_ = $this->m->getData();
	
		$this->assign('page', $data['page']);
		$this->assign('goods_list', $data['goods_list']);
		$this->assign('brand_list', $data_['brand_list']);
		$this->display();
	}
	
	/*
	 商品从回收站还原
	*/
	public function recover($id){
		$id = intval($id);
		if(M('xgj_furnish_goods')->where(array('goods_id'=>$id))->setField('is_delete',0)){
			$this->success('商品还原成功',U('recycle'));
			die;
		}
		$this->error($this->m->getError());
	}
	
	/*
	 真正删除商品数据
	*/
	public function trueDelete($id){
		$id = intval($id);
		
		if($this->m->deleteGoods($id)){
			$this->success('商品删除成功',U('recycle'));
			die;
		}
		$this->error($this->m->getError());
	}
	
	/*
	 切换是商品相册的图片的显示或者隐藏
	*/
	public function toggleImage($id){
		$id = intval($id);
		if(empty($id)) die;
		if(M()->execute('UPDATE xgj_furnish_image SET is_show = is_show^1 WHERE id='. $id)){
			echo 1;
		}else{
			echo -1;
		}
		die;
	}
	
	/*
	 删除商品相册中的图片
	*/
	public function deleteImage($id){
		$id = intval($id);
		if(empty($id)) die;
		$image = M('xgj_furnish_image')->find($id);
		if(M('xgj_furnish_image')->delete($id)){
			//删除图片文件
			deleteImage($image['url'],C('IMG_THUMB'));
			echo 1;
		}else{
			echo -1;
		}
		die;
	}

	/*
	 更新产品数据库信息
	*/
	public function updata(){
		// echo '111';exit;
		header("Content-Type:text/html; charset=utf-8");

		$secret = "4tjizi1t5otxe43awhgjq7ms2talxa0x";
		$uri="http://172.16.100.238:8088/OMS/eic/rest/getMaterials.action";
		//print_r($array_temp);die();
		$post_data['appKey']     =  '86497276';
		$post_data['partnerID']  =  'eic-sdk-java-20130701';
		$post_data['format']     =  'json';
		$post_data['signMethod'] =  'md5';
		$post_data['requestID']	 =  'LCd33vUUYuWC5z83Iwg8ay4FHnwn1OVV';
		$post_data['timestamp']	 =   date("Y-m-d H:i:s");
		$post_data['version']	 =  '2.0';
		$a=array("beginDate"=>'',"endDate"=>'',"pageNum"=>'1',"pageSize"=>'');
		$post_data['restStr']    =   json_encode($a);
		$result=$this->sign($post_data,$secret);
		$post_data['authCode']   =   $result['str'];
		//var_dump($result);
		
		$post=$result['post'].'authCode='.$post_data['authCode'];
		//var_dump($post);die();
		// curl 方法
		$ch = curl_init();
		curl_setopt ( $ch, CURLOPT_URL, $uri );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		//curl_setopt ( $ch, CURLOPT_HEADER, 1 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post );
		$return = curl_exec ( $ch );
		curl_close ( $ch );

		$orders = json_decode($return);
		// echo '<pre>';
		// print_r($return);exit;

		foreach ($orders->materials as $key => $value) {
			$data_sn[$key]['goods_sn'] = $value->matNumber;
			$data[$key]['goods_name'] = $value->matName;
			$data[$key]['goods_model'] = $value->model;
			$data[$key]['update_time'] = time();
			// $data[$key]['status'] = $value->status;
		}


		foreach ($data_sn as $key => $value) {
			$returnsave = M('xgj_furnish_goods')->where("goods_sn=".$value['goods_sn'])->save($data[$key]);

			if ($returnsave!='1') {
				$data[$key]['goods_sn'] = $value['goods_sn'];
				$returnadd = M('xgj_furnish_goods')->add($data[$key]);
			}

		}
		echo 'ok';
		// echo "<script>alert('更新完毕！')</script>";exit;
	}

	function sign($data,$secret){
		ksort($data);
		$result['str'] =$secret;
		$result['post']='';
		if($data){
			foreach($data as $key =>$v){
				$result['str'].=  $key.$v;
				$result['post'].=  $key.'='.$v.'&';
			}
		}
		//var_dump($post);die();
		$result['str'].=$secret;
		$result['str']=strtoupper(md5($result['str'],false));//md5加密 2进制转为16进制 转为大写字母
		
		return $result;
	}


	public function updata_sn(){
		$this->display();
	}

	public function doupdata_sn(){
		$type = I('get.type');
		$oldsn = ltrim(I('get.oldsn'),'o');
		$newsn = ltrim(I('get.newsn'),'n');
		if ($type==1) {
			$data = M('xgj_furnish_goods')->where("goods_sn=$oldsn")->select();
			echo json_encode($data);exit;
		}
		
		if ($type==2) {
			$data = M('xgj_furnish_goods')->where("goods_sn=$newsn")->select();

			if (!empty($data)) {
				echo 'error';exit;
			}else{
				$row['goods_sn'] = $newsn;
				
				$updata = M('xgj_furnish_goods')->where("goods_sn=$oldsn")->save($row);
				if ($updata == true) {

					$rows = M('xgj_quote_child_list')->where("goods_sn=$oldsn")->limit(1)->select();

					if (empty($rows)) {
						echo 'ok';exit;
					}else{
						$list = M('xgj_quote_child_list')->where("goods_sn=$oldsn")->save($row);
					
						if ($list == true) {
							echo 'ok';exit;
						}else{
							//修改系统清单表xgj_quote_child_list失败
							echo 'error2';exit;
						}
					}
				}else{
					//修改材料表xgj_furnish_goods失败
					echo 'error1';exit; 
				}
			}
		}
	}


	// function ceshi(){
	// 	$data = M('cheshi')->select();
		
	// 	foreach ($data as $key => $value) {
	// 		$list[] = M('xgj_furnish_goods')->field('goods_sn,goods_name,goods_model')->where('goods_sn='.$value['sn'])->select();
	// 	}
	
	// 	foreach ($list as $key => $value) {
	// 		$list1[$key]['goods_sn'] = $value['0']['goods_sn'];
	// 		$list2[$key]['name'] = $value['0']['goods_name'];
	// 		$list2[$key]['model'] = $value['0']['goods_model'];
	// 	}

	// 	foreach ($list1 as $key => $value) {
	// 		M('cheshi')->where('sn='.$list1[$key]['goods_sn'])->save($list2[$key]);
	// 	}
	// }
}