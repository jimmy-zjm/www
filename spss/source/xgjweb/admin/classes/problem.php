<?php 
require_once(WWW_DIR."/libs/page.php");
require_once(WWW_DIR."/conf/mysql_db.php");
require_once(WWW_DIR."/admin/model/problem_model.php");

class problem{
	function probl(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());

		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}

		$model = new problem_model();

		$result_all = $model->select_all();

		$result = $model->select($page);

		$count = count($result_all);

		$tpl = get_admin_smarty();

		//实例化分页类
		$t = new Page(10,$count,$page,5,'problem.php?probl&p=');
		//分页样式
		$page=$t->subPageCss2();//分页样式

		for ($i=0; $i <count($result) ; $i++) { 

			$result[$i]['time'] = date("Y-m-d H:i:s", $result[$i]['time']);

		}

		$tpl->assign('data',$result);

		$tpl->assign('page',$page);
				
		$tpl->display('admin_problem.tpl.html');
	}

	function state(){

		$state = $_GET['state'];
		$id = $_GET['id'];

		if ($state == 0) {
			$num = 1;
		}else if($state == 1){
			$num = 0;
		}
		
		$model = new problem_model();

		$return = $model->updata($num,$id);

		if ($return==1) {
			header("Location:problem.php?probl");exit;
		}
	}

	function note(){

		$note = $_GET['note'];
		$id = $_GET['problem_id'];

		$model = new problem_model();

		$return = $model->updata_content($note,$id);

		if ($return==1) {
			header("Location:problem.php?probl");exit;
		}else{
			echo '添加备注失败！请重新添加，谢谢！';
		}
	}

	function delete(){

		$id = $_GET['delete'];

		$model = new problem_model();

		$return = $model->delete($id);

		header("Location:problem.php?probl");exit;
	}

	function prob_ok(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());

		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}

		$model = new problem_model();

		$result_all = $model->select_all_ok();

		$result = $model->select_ok($page);

		$count = count($result_all);

		$tpl = get_admin_smarty();

		for ($i=0; $i <count($result) ; $i++) { 

			$result[$i]['time'] = date("Y-m-d H:i:s", $result[$i]['time']);
			
		}

		//实例化分页类
		$t = new Page(10,$count,$page,5,'problem.php?prob_ok&p=');
		//分页样式
		$page=$t->subPageCss2();//分页样式

		$tpl->assign('data',$result);

		$tpl->assign('page',$page);
				
		$tpl->display('admin_problem_ok.tpl.html');
	}

	function state_ok(){

		$state = $_GET['state_ok'];
		$id = $_GET['id'];

		if ($state == 0) {
			$num = 1;
		}else if($state == 1){
			$num = 0;
		}
		
		$model = new problem_model();

		$return = $model->updata($num,$id);

		if ($return==1) {
			header("Location:problem.php?prob_ok");exit;
		}
	}

	function note_ok(){

		$note = $_GET['note_ok'];
		$id = $_GET['problem_id'];

		$model = new problem_model();

		$return = $model->updata_content($note,$id);

		if ($return==1) {
			header("Location:problem.php?prob_ok");exit;
		}else{
			echo '添加备注失败！请重新添加，谢谢！';
		}
	}

	function delete_ok(){

		$id = $_GET['delete_ok'];

		$model = new problem_model();

		$return = $model->delete($id);

		header("Location:problem.php?prob_ok");exit;
	}

	function prob_no(){
		//判断后台是否登录并返回权限
		$permission=intval(admin_check_logon());

		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}

		$model = new problem_model();

		$result_all = $model->select_all_no();

		$result = $model->select_no($page);

		$count = count($result_all);

		$tpl = get_admin_smarty();

		for ($i=0; $i <count($result) ; $i++) { 

			$result[$i]['time'] = date("Y-m-d H:i:s", $result[$i]['time']);
			
		}

		//实例化分页类
		$t = new Page(10,$count,$page,5,'problem.php?prob_no&p=');
		//分页样式
		$page=$t->subPageCss2();//分页样式

		$tpl->assign('data',$result);

		$tpl->assign('page',$page);
				
		$tpl->display('admin_problem_no.tpl.html');
	}

	function state_no(){

		$state = $_GET['state_no'];
		$id = $_GET['id'];

		if ($state == 0) {
			$num = 1;
		}else if($state == 1){
			$num = 0;
		}
		
		$model = new problem_model();

		$return = $model->updata($num,$id);

		if ($return==1) {
			header("Location:problem.php?prob_no");exit;
		}
	}

	function note_no(){

		$note = $_GET['note_no'];
		$id = $_GET['problem_id'];

		$model = new problem_model();

		$return = $model->updata_content($note,$id);

		if ($return==1) {
			header("Location:problem.php?prob_no");exit;
		}else{
			echo '添加备注失败！请重新添加，谢谢！';
		}
	}

	function delete_no(){

		$id = $_GET['delete_no'];

		$model = new problem_model();

		$return = $model->delete($id);

		header("Location:problem.php?prob_no");exit;
	}
}