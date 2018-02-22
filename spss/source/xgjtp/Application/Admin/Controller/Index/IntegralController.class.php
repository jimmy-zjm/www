<?php
namespace Admin\Controller\Index;
use \Admin\Controller\Index\AdminController;

/*
后台积分控制器
 */
class IntegralController extends AdminController{

	//总积分
	public function index(){
		$name = I("get.name");

		if ($name != '') {
			//搜索分页
			$where['user_name']=array('like',"%$name%");

			$data1 = M('xgj_users')->where('integral>0')->where($where)->select();

			$total = count($data1);

	        $page  = getPage($total,C('MANAGER_PAGE_SIZE'));

	        $data  = M('xgj_users')->where('integral>0')->where($where)->limit($page['limit'])->select();

		}else{
			//查询总积分
			$data1 = M('xgj_users')->where('integral>0')->select();

			$total = count($data1);

	        $page  = getPage($total,C('MANAGER_PAGE_SIZE'));

	        $data  = M('xgj_users')->where('integral>0')->limit($page['limit'])->select();
		}

        $this->assign('page', $page['page']);

		$this->assign('data',$data);

		$this->display();
	}

	//具体积分
	public function select(){

		$id = $_GET['id'];

		$data1 = M('xgj_user_integral')->where("user_id=$id")->select();

		$total = count($data1);

        $page  = getPage($total,C('MANAGER_PAGE_SIZE'));

        $data  = M('xgj_user_integral')->where(array("user_id"=>$id))->order("integral_id DESC")->limit($page['limit'])->select();

        $this->assign('page', $page['page']);

		$this->assign('data',$data);

		$this->display();
	}

	//总积分删除
	public function delete(){

		$user_id = $_GET['id'];

		//删除和总积分有关的所有具体积分
		$del = M('xgj_user_integral')->where("user_id=".$user_id)->delete();

		//成功的话删除总积分(将积分修改为0)
		if ($del == 1) {

			$updata['integral'] = 0;

			$data = M('xgj_users')->where("user_id=$user_id")->save($updata);

			if ($data == 1) {

				$this->success('删除成功',U('index'));

			}else{

				$this->error('删除失败',U('index'));

			}

		}else{

			$this->error('删除失败',U('index'));

		}
		
	}

	//添加积分
	public function add(){

		$this->assign('user_id',$_GET['user_id']);
		$this->assign('user_name',$_GET['user_name']);
		$this->display();
	}

	//执行添加
	public function add_exe(){

		$user_id = $_POST['user_id'];
		$user_name = $_POST['user_name'];
		$integral = $_POST['integral'];
		$order = $_POST['order'];

		if (empty($user_id)) {
			$this->error('添加失败',U("index"));exit;
		}
		
		if (!is_numeric($integral)) {
			$this->error('请正确填写积分',U("select?id=$user_id"));exit;
		}

		if ($integral==0) {
			$this->error('添加的积分不能为0',U("select?id=$user_id"));exit;
		}

		$data['user_id'] = $user_id;
		$data['user_name'] = $user_name;
		$data['order'] = $order;
		$data['integral'] = $integral;
		$data['time'] = time();

		$insert = M('xgj_users')->field('integral')->where("user_id=$user_id")->select();

		$integrals = $insert['0']['integral']+$integral;

		if ($integrals < 0) {
			$this->error("添加失败，减掉的积分不能大于总积分",U("select?id=$user_id"));exit;
		}

		$add = M('xgj_user_integral')->add($data);

		if (!empty($add)) {

			$row['integral'] = $integrals;

			$updata = M('xgj_users')->where("user_id=$user_id")->save($row);

			if ($updata==1) {

				$this->success('添加成功',U("select?id=$user_id"));

			}else{

				$data = M('xgj_user_integral')->where("id=$add")->delete();
				$this->error('添加失败',U("select?id=$user_id"));
			}
			
		}else{
			$this->error('添加失败',U("select?id=$user_id"));
		}
	}

	//删除具体积分
	public function integral_delete(){

		$id = $_GET['id'];
		
		//查询要删除的积分记录
		$data = M('xgj_user_integral')->field('user_id,integral')->where("integral_id=$id")->select();

		$user_id = $data['0']['user_id'];	//要删除的积分ID
		$integral = $data['0']['integral'];	//要删除的积分数

		//如果要删除的积分数为0就直接删除删除具体积分表内数据
		if ($integral == 0) {
			$del = M('xgj_user_integral')->where("integral_id=$id")->delete();
			if ($del == 1) {
				$this->success('删除成功',U("select?id=$user_id"));exit;
			}else{
				$this->error('删除失败',U("select?id=$user_id"));exit;
			}
		}

		//查询总积分
		$data_all = M('xgj_users')->field('integral')->where("user_id=$user_id")->select();

		$integral_all = $data_all['0']['integral'];		//总积分数

		$integral_num['integral'] = $integral_all-$integral;	//总积分数-删除的积分数

		//更改总积分数
		$integral_all_updata =  M('xgj_users')->where("user_id=$user_id")->save($integral_num);

		if ($integral_all_updata==1) {
			//更改总积分数成功后删除具体积分
			$del = M('xgj_user_integral')->where("integral_id=$id")->delete();
			if ($del == 1) {
				$this->success('删除成功',U("select?id=$user_id"));exit;
			}else{
				$this->error('删除失败',U("select?id=$user_id"));exit;
			}
		}else{
			$this->error('删除失败',U("select?id=$user_id"));exit;
		}

		



	}
}