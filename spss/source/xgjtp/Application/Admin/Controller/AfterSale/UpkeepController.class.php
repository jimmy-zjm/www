<?php

namespace Admin\Controller\AfterSale;
use \Admin\Controller\Index\AdminController;
/*
 后台管理员控制器
 */
class UpkeepController extends AdminController{

	//机电保养
	public function index(){
		$name   = I('get.name');
		$is_use = I('get.is_use');
		$where['name'] = array('like',"%$name%");
		if(!empty($is_use)) $where['is_use'] = $is_use;

		$total = M('xgj_s_upkeep')->where($where)->count();
		$page = getPage($total, 5);
		$data = M('xgj_s_upkeep')->where($where)->limit($page['limit'])->select();

		$this->assign('data',$data);
		$this->assign('page',$page['page']);
		$this->display();
	}

	//机电保养添加
	public function add(){
		$this->display();
	}

	//执行机电保养添加
	public function doAdd(){
		foreach ($_POST as $k => $v) {
			if ($v=='') $this->error('请填写完整');
		}

		if (empty($_FILES['u_img']['name'])) $this->error('请上传图片');

		$price = I('post.price');
		if(!preg_match("/^[0-9]{1,9}(.[0-9]{0,2})?$/", $price)){
		   	$this->error('请正确填写单次保养费用');
		}
		$map['name'] = I('post.name');

		$name = M('xgj_s_upkeep')->where($map)->count();
		if ($name>0) $this->error('此系统已存在');

		$data = M('xgj_s_upkeep')->create();

		$data['add_time'] = time();//添加时间   

    	//商品商品的封面图
    	if(isset($_FILES['u_img'])&&$_FILES['u_img']['error']==0){
    		$image = uploadOne('u_img','Upkeep',C('IMG_THUMB_FACE'));
    		if($image['code']!=1){
    			//商品封面图片上传失败
    			$this->error($image['error']);
    		}
    		$data['u_img'] = $image['images'];
    	}
		
		$re   = M('xgj_s_upkeep')->add($data);
		if ($re>0) $this->success('添加成功',U('index'));
		else $this->error('添加失败');
		
	}

	//机电保养修改
	public function edit(){
		$map['id'] = I('get.id');
		$data = M('xgj_s_upkeep')->where($map)->find();
		$this->assign('data',$data);
		$this->display();
	}

	//执行机电保养修改
	public function doEdit(){

		foreach ($_POST as $k => $v) {
			if ($v=='') $this->error('请填写完整');
		}

		$price = I('post.price');
		$id    =I('post.id');
		if(!preg_match("/^[0-9]{1,9}(.[0-9]{0,2})?$/", $price)){
		   	$this->error('请正确填写单次保养费用');
		}
		$map['name'] = I('post.name');

		$data = M('xgj_s_upkeep')->create();

		//商品商品的封面图
    	if(isset($_FILES['u_img'])&&$_FILES['u_img']['error']==0){
    		$image = uploadOne('u_img','Upkeep',C('IMG_THUMB_FACE'));
    		if($image['code']!=1){
    			//商品封面图片上传失败
    			$this->error($image['error']);
    		}
    		deleteImage($_POST['old_img'],C('IMG_THUMB_FACE'));
    		$data['u_img'] = $image['images'];
    	}

		$save['id']=$id;
		$re   = M('xgj_s_upkeep')->where($save)->save($data);

		if ($re>=0) $this->success('编辑成功',U('index'));
		else $this->error('编辑失败');
		
	}


	//删除
	public function delete(){
		$map['id'] = I('get.id');

		$v = M('xgj_s_upkeep')->where($map)->getField('is_use');
		if ($v==1) $save['is_use'] = '2';
		else $save['is_use'] = '1';
		
		$re = M('xgj_s_upkeep')->where($map)->save($save);

		if ($re>0) echo $save['is_use'];
	}


}
