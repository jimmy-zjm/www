<?php
namespace Admin\Controller\RecruitCooperation;
use \Admin\Controller\Index\AdminController;
/*
招聘与合作 控制器
 */
class RecruitCooperationController extends AdminController{
	private $p=NULL;  //当前页

	//招聘
	public function recruit(){
		//分页  
		if (!empty($this->$p)) {
			$_GET['p']=$this->$p;
		}

        $data1 = M('xgj_job')->select();

        $total = count($data1);

        $page  = getPage($total,C('MANAGER_PAGE_SIZE'));

        $data  = M('xgj_job')->limit($page['limit'])->select();

        //模板传值
        $this->assign("page",$page['page']);
        $this->assign('data',$data);
		$this->display('RecruitCooperation/recruit');
	}

	//招聘开启与暂停
	public function isopen(){
		$p = I('get.p');
		$isopen = I('get.isopen');
		$id = I('get.id');
		if ($isopen==0) {
			$data['isopen'] = 1;
		}else{
			$data['isopen'] = 0;
		}
		$return = M('xgj_job')->where("id=$id")->save($data);
		$this->p = $p;
		$this->recruit();
	}

	//修改招聘
	public function edit(){
		$id = I('get.id');
		$data = M('xgj_job')->where("id=$id")->select();
		$this->assign('data',$data['0']);
		$this->display('RecruitCooperation/recruitedit');
	}

	//确认修改招聘
	public function doedit(){
		$p = I('post.p');
		$id = I('post.id');

		foreach ($_POST as $key => $value) {
			if ($value == '') {
				$this->error('请正确填写完整再提交',U('edit?id='.$id));exit;
			}
		}
		$num = intval($_POST['num']);
		if (empty($num)) {
			$this->error('请正确填写完整再提交',U('edit?id='.$id));exit;
		}
		$num = substr($_POST['num'],0,1);
		if ($num=='-') {
			$this->error('人数不能为负数',U('edit?id='.$id));exit;
		}

		$data = D('xgj_job')->create();
		$updata = D('xgj_job')->where("id=$id")->save($data);
		if ($updata==1) {
			$this->success('修改成功',U('recruit?p='.$p));
		}else{
			$this->error('修改失败',U('recruit?p='.$p));
		}
	}

	//确认删除招聘信息
	public function delete(){
		$p = I('get.p');
		$id=I('get.id');
		$del = M('xgj_job')->where("id=$id")->delete();
		if (!empty($del)) {
			$this->success('删除成功',U('recruit?p='.$p));
		}else{
			$this->error('删除失败',U('recruit?p='.$p));
		}
	}

	//新增招聘信息页面
	public function add(){
		$this->display('RecruitCooperation/recruitadd');
	}

	//确认新增招聘信息
	public function doadd(){
		$_SESSION['doadd'] = $_POST;
		foreach ($_POST as $key => $value) {
			if ($value == '') {
				$this->error('请正确填写完整再提交',U('add'));exit;
			}
		}
		$num = intval($_POST['num']);
		if (empty($num)) {
			$this->error('请正确填写完整再提交',U('add'));exit;
		}
		$num = substr($_POST['num'],0,1);
		if ($num=='-') {
			$this->error('人数不能为负数',U('add'));exit;
		}
		unset($_SESSION['doadd']);
		$_POST['time']=time();
		$data = D('xgj_job')->create();
		$add = M('xgj_job')->add($data);
		if (!empty($add)) {
			$this->success('添加成功',U('recruit'));
		}else{
			$this->error('添加失败',U('add'));
		}
	}

	//合作
	public function cooperation(){
		$keyword = I('post.keyword');
		if (!empty($keyword)) {
			$where['company'] = array('like',"%$keyword%");
		}else{
			$where['company'] = array('like',"%%");
		}
		//分页  
		if (!empty($this->$p)) {
			$_GET['p']=$this->$p;
		}

        $data1 = M('xgj_join')->where($where)->select();

        $total = count($data1);

        $page  = getPage($total,C('MANAGER_PAGE_SIZE'));

        $data  = M('xgj_join')->where($where)->limit($page['limit'])->select();

        //模板传值
        $this->assign("page",$page['page']);
        $this->assign('data',$data);
        $this->assign('keyword',$keyword);
		$this->display('RecruitCooperation/cooperation');
	}

	//合作编辑备注
	public function cedit(){
		$id = I('get.id');
		$data = M('xgj_join')->where("id=$id")->select();
		$this->assign('data',$data['0']);
		$this->display('RecruitCooperation/cedit');
	}

	//合作确认编辑备注
	public function docedit(){
		$id = I('post.id');
		$p = I('post.p');
		$data = D('xgj_join')->create();
		$updata = D('xgj_join')->where("id=$id")->save($data);
		if ($updata==1) {
			$this->success('修改成功',U('cooperation?p='.$p));
		}else{
			$this->error('修改失败',U('cooperation?p='.$p));
		}
	}

	//确认删除合作信息
	public function cdelete(){
		$p = I('get.p');
		$id=I('get.id');
		$del = M('xgj_join')->where("id=$id")->delete();
		if (!empty($del)) {
			$this->success('删除成功',U('cooperation?p='.$p));
		}else{
			$this->error('删除失败',U('cooperation?p='.$p));
		}
	}
}