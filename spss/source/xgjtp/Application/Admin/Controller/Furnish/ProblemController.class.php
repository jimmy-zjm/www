<?php
namespace Admin\Controller\Furnish;
use \Admin\Controller\Index\AdminController;

class ProblemController extends AdminController{

	//待解决问题
	public function problem_no(){

		$like["name"] = array("like", "%%");
		if (!empty($_POST['name'])) {
			$name = I('post.name');
			$like["name"] = array("like", "%".$name."%");
		}

		$data1 = M('xgj_user_problem')->where("state=0")->where($like)->select();

        $total = count($data1);

        $page  = getPage($total,C('MANAGER_PAGE_SIZE'));

        $data  = M('xgj_user_problem')->where("state=0")->where($like)->limit($page['limit'])->order("problem_id ASC")->select();

        //模板传值
        $this->assign("page",$page['page']);

        $this->assign('data',$data);

        //显示模板
        $this->display();
	}

	//已解决问题
	public function problem_ok(){

		$like["name"] = array("like", "%%");
		if (!empty($_POST['name'])) {
			$name = I('post.name');
			$like["name"] = array("like", "%".$name."%");
		}

		$data1 = M('xgj_user_problem')->where("state=1")->where($like)->select();

        $total = count($data1);

        $page  = getPage($total,C('MANAGER_PAGE_SIZE'));

        $data  = M('xgj_user_problem')->where("state=1")->where($like)->limit($page['limit'])->order("problem_id DESC")->select();


        //模板传值
        $this->assign("page",$page['page']);

        $this->assign('data',$data);

        //显示模板
        $this->display();
	}

	//全部问题
	public function problem(){

		$like["name"] = array("like", "%%");
		if (!empty($_POST['name'])) {
			$name = I('post.name');
			$like["name"] = array("like", "%".$name."%");
		}

		$data1 = M('xgj_user_problem')->where($like)->select();

        $total = count($data1);

        $page  = getPage($total,C('MANAGER_PAGE_SIZE'));

        $data  = M('xgj_user_problem')->limit($page['limit'])->where($like)->order("problem_id DESC")->select();

        //模板传值
        $this->assign("page",$page['page']);

        $this->assign('data',$data);

		$this->display();
	}

	//添加及修改备注
	public function add_note(){

		$note = I('post.note_no');
		$id = I('post.problem_id');
		$number = I('post.number');

		if ($note=='') {
			$this->error('内容不能为空',U('problem_no'));
		}

		$data['note'] = $note;

		$return = M('xgj_user_problem')->where("problem_id=".$id)->save($data);

		if ($number == 'no') {
			if ($return==1) {
				$this->success('添加成功',U('problem_no'));
			}else{
				$this->error('添加失败',U('problem_no'));
			}
		}else if ($number == 'ok') {
			if ($return==1) {
				$this->success('添加成功',U('problem_ok'));
			}else{
				$this->error('添加失败',U('problem_ok'));
			}
		}else if ($number == 'all') {
			if ($return==1) {
				$this->success('添加成功',U('problem'));
			}else{
				$this->error('添加失败',U('problem'));
			}
		}else{
			$this->error('添加失败',U('problem_no'));
		}
		

	}

	//修改状态
	public function state(){

		if (!empty($_GET['state_no']) || $_GET['state_no']=='0') {
			$state = I('get.state_no');
		}else if (!empty($_GET['state_ok'])) {
			$state = I('get.state_ok');
		}else if (!empty($_GET['state_all']) || $_GET['state_all']=='0') {
			$state = I('get.state_all');
		}

		$id = I('get.id');

		if ($state == 0) {
			$num = 1;
		}else if($state == 1){
			$num = 0;
		}

		$data['state'] = $num;
		
		$return = M('xgj_user_problem')->where("problem_id=".$id)->save($data);



		if (!empty($_GET['state_no']) || $_GET['state_no']=='0') {
			if ($return==1) {
				$this->success('修改成功',U('problem_no'));
			}else{
				$this->error('修改失败',U('problem_no'));
			}
		}else if(!empty($_GET['state_ok'])) {
			if ($return==1) {
				$this->success('修改成功',U('problem_ok'));
			}else{
				$this->error('修改失败',U('problem_ok'));
			}
		}else if(!empty($_GET['state_all']) || $_GET['state_all']=='0') {
			if ($return==1) {
				$this->success('修改成功',U('problem'));
			}else{
				$this->error('修改失败',U('problem'));
			}
		}else{
			$this->error('修改失败',U('problem_no'));
		}
	}

	//删除问题
	public function delete(){

		if (!empty($_GET['delete_no']) || $_GET['delete_no']=='0') {
			$id = I('get.delete_no');
		}else if (!empty($_GET['delete_ok']) || $_GET['delete_ok']=='0') {
			$id = I('get.delete_ok');
		}else if (!empty($_GET['delete_all']) || $_GET['delete_all']=='0') {
			$id = I('get.delete_all');
		}

		$del = M('xgj_user_problem')->where("problem_id = $id")->delete();

		if (!empty($_GET['delete_no']) || $_GET['delete_no']=='0') {
			if ($del==1) {
				$this->success('删除成功',U('problem_no'));
			}else{
				$this->error('删除失败',U('problem_no'));
			}
		}else if(!empty($_GET['delete_ok']) || $_GET['delete_all']=='0') {
			if ($del==1) {
				$this->success('删除成功',U('problem_ok'));
			}else{
				$this->error('删除失败',U('problem_ok'));
			}
		}else if(!empty($_GET['delete_all']) || $_GET['delete_all']=='0') {
			if ($del==1) {
				$this->success('删除成功',U('problem'));
			}else{
				$this->error('删除失败',U('problem'));
			}
		}else{
			$this->error('删除失败',U('problem_no'));
		}

	}

}