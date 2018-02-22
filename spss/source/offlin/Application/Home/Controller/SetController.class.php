<?php
namespace Home\Controller;
use Think\Controller;
class SetController extends BaseController {

	public function _initialize() {  
		parent::_initialize();
		$this->assign('now','5');
	}

	//设置
	public function index(){
		$this->display();
	}


	//公司列表
	public function companyInfo(){
		$userId = $_SESSION['dealerId'];
		$data = M('pad_company')->where(['u_id'=>$userId])->select();
		$this->assign('data',$data);
		$this->display();
	}

	//公司详情
	public function companyDetail(){
		$id = I('get.id');
		if ($this->companyVerify($id) !== false) {
			$data    = M('pad_company')->where(['id'=>$id])->select();
			$role    = M('pad_role')->where(['c_id'=>$id])->select();
			$section = M('pad_section')->where(['c_id'=>$id])->select();
			$this->assign('data',$data);
			$this->assign('role',$role);
			$this->assign('section',$section);
		}
		$this->display();
	}

	//验证当前公司是否是当前账号下的
	public function companyVerify($id){
		$u_id = M('pad_company')->where(['id'=>$id])->getField('u_id');
		if ($u_id != $_SESSION['dealerId']) {
			return false;
		}
	}


	public function doAddCompanyDetail(){
		layout(false);
		$id      = I('post.id');
		$section = I('post.section');
		$role    = I('post.role');

		if ($this->companyVerify($id) === false) {
			$this->error('请勿非法操作');
		}

		if(!empty($_FILES['logo']['name'])){
            if($_FILES['logo']['error']==0){
                //将上传成功的商品相册图片地址保存起来
                $image = uploadOne('logo','logo');
                if($image['code']!=1){
                     //头像上传失败
                     $error = $image['error'];
                }else{
                	$logo['logo'] = $image['images'];
                	$oldLogo = M('pad_company')->where(['id'=>$id])->getField('logo');
                	deleteImage($oldLogo);
	                $re = M('pad_company')->where(['id'=>$id])->save($logo);
		        	if ($re===false) {
		        		$error ='图片上传失败';
		        	}
                }
            }else{
                $error ='图片上传失败:'.$image['error'];
            }
        }

        if (!empty($error)) {
        	$this->error($error);
        }

        if (empty($logo['logo'])) {
        	if (empty($section) && empty($role)) {
	        	$this->error('请新增后再提交');
			}
        }
		
		$addData['c_id'] = $id;

		$error = false;

		$model = M();
        $model->startTrans();

		if (!empty($section)) {
			$section = explode('|', ltrim($section,'|'));
			foreach ($section as $k => $v) {
				if ($v != '') {
					$addData['name'] = $v;
					$re = $model->table('pad_section')->add($addData);
					if ($re<1) {
						deleteImage($oldLogo);
						$model->rollback();
						$this->error('添加失败');
					}
				}
			}
		}
		
		if (!empty($role)) {
			$role = explode('|', ltrim($role,'|'));
			foreach ($role as $k => $v) {
				if ($v != '') {
					$addData['name'] = $v;
					$re = $model->table('pad_role')->add($addData);
					if ($re<1) {
						deleteImage($oldLogo);
						$model->rollback();
						$this->error('添加失败');
					}
				}
			}
		}
		
		if ($re !== false) {
			$model->commit();
			$this->success('添加成功');
		}else{
			$model->rollback();
			$this->error('添加失败');
		}
	}

	public function employeeManage(){
		$key = I('get.key');
		$userId = $_SESSION['dealerId'];

		if (!empty($key)) {
			$where['c.name|u.real_name'] = ['like','%'.$key.'%'];
		}
		$where['c.u_id'] = $userId;
		$data = M('pad_user u')
				->join('pad_company c on u.c_id=c.id')
				->join('pad_section s on u.s_id=s.id')
				->join('pad_role r on u.r_id=r.id')
				->join('pad_user p on u.pid = p.id')
				->field('u.id,u.name uname,u.real_name,u.sex,u.level,s.name sname,r.name rname,c.name,p.real_name pname')
				->where($where)
				->select();

		$company = M('pad_company')->field('id,name')->where(['u_id'=>$userId])->select();

		$this->assign('company',$company);
		$this->assign('data',$data);
		$this->display();
	}

	public function getSection(){
		$id = I('get.id');
		if ($id == '') exit;
		$re['section'] = M('pad_section')->field('id,name')->where(['c_id'=>$id])->select();
		$re['role']    = M('pad_role')->field('id,name')->where(['c_id'=>$id])->select();
		
		$map['id']         = $_SESSION['dealerId'];
		$where['c_id']     = $id;
		$where['_complex'] = $map;
		$where['_logic']   = 'or';

        $re['user']    = M('pad_user')->field('id,real_name')->where($where)->select();

		echo json_encode($re);
	}

	//添加
	public function doAddUser(){
		if (count($_POST) != 11) {
			echo '请填写完整再提交';exit;
		}
		foreach ($_POST as $k => $v) {
			if ($v=='') {
				echo '请填写完整再提交';exit;
			}
		}

		if(!preg_match("/^\w{6,15}$/", $_POST['psd'])){
		   echo '密码为6-15位英文、数组或下划线';exit;
		}

		if ($_POST['psd'] != $_POST['spsd']) {
			echo '2次密码不一致';exit;
		}

		if(!preg_match("/^[1][34578][0-9]{9}$/", $_POST['tel'])){
		   echo '请填写正确的手机号码';exit;
		}

		$name = I('post.name');
		$re = M('pad_user')->where(['name'=>$name])->count();

		if ($re > 0) {
			echo '此账号已注册过';exit;
		}
	
		$user = M('pad_user')->where(['id'=>$_SESSION['dealerId']])->find();
		$_POST['system']             = $user['system'];
		$_POST['p_service_city_all'] = $user['p_service_city_all'];

		$data = M('pad_user')->create();
		$data['psd']      = md5(trim($data['psd']).C('MD5_PAD_PSD'));
		$data['add_time'] = time();
		$re = M('pad_user')->add($data);

		if ($re > 0) {
			echo '1';
		} else {
			echo '2';
		}
	}

	//编辑
	public function doEditUser(){
		foreach ($_POST as $k => $v) {
			if ($v['psd']!='' && $v['spsd']!='' && $v=='') {
				echo '请填写完整再提交';exit;
			}
		}

		if(!empty($_POST['psd']) && !preg_match("/^\w{6,15}$/", $_POST['psd'])){
		   echo '密码为6-15位英文、数组或下划线';exit;
		}
		
		if ($_POST['psd'] != $_POST['spsd']) {
			echo '2次密码不一致';exit;
		}

		$id   = I('post.u_id');
		if ($_POST['is_open']!='') {
			$_POST['is_open'] = $_POST['is_open']+1;
		}

		$data = M('pad_user')->create();
		foreach ($data as $k => $v) {
			if ($v=='') {
				unset($data[$k]);
			}
		}

		if (!empty($data['psd'])) {
			$data['psd'] = md5(trim($data['psd']).C('MD5_PAD_PSD'));
		}
		
		$re = M('pad_user')->where(['id'=>$id])->save($data);

		if ($re !== false) {
			echo '1';
		} else {
			echo '2';
		}
	}

	//批量编辑
	public function doAllEditUser(){
		foreach ($_POST as $k => $v) {
			if ($v['psd']!='' && $v['spsd']!='' && $v=='') {
				echo '请填写完整再提交';exit;
			}
		}

		$ids   = ltrim(I('post.ids'),',');
		$data = M('pad_user')->create();
		$re = M('pad_user')->where(['id'=>['in',$ids]])->save($data);

		if ($re !== false) {
			echo '1';
		} else {
			echo '2';
		}
	}


	public function editUserData(){
		$id = I('get.id');
		$re['data'] = M('pad_user')
				->field('id,c_id,real_name,tel,sex,level,s_id,r_id,level,pid,is_open')
				->where(['id'=>$id])
				->find();
		//公司
		$u_id = M('pad_company')->where(['id'=>$re['data']['c_id']])->getField('u_id');
		$re['company'] = M('pad_company')->field('id,name')->where(['u_id'=>$u_id])->select();

		//角色
		$re['role']    = M('pad_role')->field('id,name')->where(['c_id'=>$re['data']['c_id']])->select();
		//部门
		$re['section'] = M('pad_section')->field('id,name')->where(['c_id'=>$re['data']['c_id']])->select();
		//主管
		$map['id']         = $u_id;
		$where['c_id']     = $re['data']['c_id'];
		$where['_complex'] = $map;
		$where['_logic']   = 'or';
		$re['user']     = M('pad_user')->field('id,real_name')->where($where)->select();

		echo json_encode($re);
	}


	//权限
	public function superManageSet(){
		$id = I('get.id');

		$info = M('pad_user')->field('is_try,level')->where(['id'=>$_SESSION['dealerId']])->find();

		if ($info['level'] != 0) {
			layout(false);
			$this->error('抱歉您没有此权限');
		}

		$user = M('pad_user')->field('c_id,permission,level')->where(['id'=>$id,'status'=>'1'])->find();
		
		if ($this->companyVerify($user['c_id']) === false) {
			layout(false);
			$this->error('请勿非法操作');
		}
		
		$list = M('pad_permissions')->select();
		
		
		foreach ($list as $k => $v) {
			if ($info['is_try']==1) {
				if ($v['ctl'] != 'Order' && $v['ctl'] != 'Aftersale') {
					$data[$v['pid']][] = $v;
				}
			}else{
				$data[$v['pid']][] = $v;
			}
		}

		$permission = explode(',', $user['permission']);
		$this->assign('data',$data);
		$this->assign('permission',$permission);
		$this->display();
	}

	//添加权限
	public function doPermission(){
		$id   = I('get.id');

		$c_id = M('pad_user')->where(['id'=>$id])->getField('c_id');
		
		if ($this->companyVerify($c_id) === false) {
			echo '请勿非法操作';exit;
		}

		$data = I('get.permission');

		$add['permission'] = implode(',', $data);
		$re = M('pad_user')->where(['id'=>$id])->save($add);
		if ($re !== false) {
			echo '1';
		}else{
			echo '添加失败';
		}
	}

}
