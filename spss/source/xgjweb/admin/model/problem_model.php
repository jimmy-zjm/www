<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");
/**
 * @author L
 * 用户问题分页
 */
class problem_model{
	function select_all(){
		$mysql = new db();

		$sql = "SELECT * FROM xgj_user_problem";

		$result = $mysql->getAll($sql);

		return $result;
	}

	function select($page){
		$mysql = new db();

		$start=($page-1)*10;

		$sql = "SELECT * FROM xgj_user_problem limit ".$start.",10";

		$result = $mysql->getAll($sql);

		return $result;
	}

	function updata($num,$id){
		$mysql = new db();

		$table = 'xgj_user_problem';

		$data = array('state'=>$num);

		$where = "problem_id=".$id;

		$return = $mysql->update($table,$data,$where);

		return $return;
	}

	function updata_content($note,$id){
		$mysql = new db();

		$table = 'xgj_user_problem';

		$data = array('note'=>$note);

		$where = "problem_id=".$id;

		$return = $mysql->update($table,$data,$where);

		return $return;
	}

	function delete($id){
		$mysql = new db();

		$sql = "DELETE FROM xgj_user_problem WHERE problem_id=$id";

		$return = $mysql->query($sql);

	}

	function select_all_ok(){
		$mysql = new db();

		$sql = "SELECT * FROM xgj_user_problem where state=1";

		$result = $mysql->getAll($sql);

		return $result;
	}

	function select_ok($page){
		$mysql = new db();

		$start=($page-1)*10;

		$sql = "SELECT * FROM xgj_user_problem where state=1 limit ".$start.",10";

		$result = $mysql->getAll($sql);

		return $result;
	}

	function select_all_no(){
		$mysql = new db();

		$sql = "SELECT * FROM xgj_user_problem where state=0";

		$result = $mysql->getAll($sql);

		return $result;
	}

	function select_no($page){
		$mysql = new db();

		$start=($page-1)*10;

		$sql = "SELECT * FROM xgj_user_problem where state=0 limit ".$start.",10";

		$result = $mysql->getAll($sql);

		return $result;
	}
}