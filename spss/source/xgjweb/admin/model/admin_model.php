<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");
class admin_model{

	/**
     * doLogin  表示实行登录
     * @param string $userName  用户提交的名户名
     * @param string $passWord  用户提交的密码
     * @return  void
     */
    public function doLogin($userName, $passWord){
    	$db=new db();
        
        $sql = "SELECT `user_id`,`user_name`,`password`,`last_login` FROM `xgj_admin_user` WHERE `user_name` = '{$userName}' AND `password` = '{$passWord}' LIMIT 1";
		
        $result=$db->getRow($sql);
        
    	if ($result){
    		//使用cookie存储最后登录时间
    		setcookie("last_login",$result['last_login']);
   			$re=$db->update("xgj_admin_user", array('last_login'=>time()),"`user_name` = '{$userName}' AND `password` = '{$passWord}'");
   			if($re){
	   			$sql="select `user_id`,`user_name`,`password` from xgj_admin_user where `user_name` = '{$userName}' AND `password` = '{$passWord}'";
	   			$rs=$db->getRow($sql);
   			}
   			return $rs;
   		}else{
   			
   			return $result;
   			
   		}
    }
    /**
     * 获取一条管理员信息
     * @param unknown $user_id
     */
    public function getOneByid($user_id){
    	$db=new db();
    	$sql="SELECT * FROM `xgj_admin_user` WHERE `user_id` = '{$user_id}' LIMIT 1";
    	$result=$db->getRow($sql);
    	return $result;
    }
    
    /**
     * 管理员列表分页
     * @param unknown $page
     * @return array
     */
    public function show_list($page,$condition){
    	$db=new db();
    	$start=($page-1)*10;
    	$sql = "SELECT * FROM xgj_admin_user where permission !=0 and $condition order by user_id desc limit ".$start.",10";
    	$detail=$db->getAll($sql);
    	return $detail;
    }
    
    /**
     * 管理员列表总条数
     * @return string
     */
    public function show_count(){
    	$db=new db();
    	$sql = "SELECT count(*) FROM xgj_admin_user where permission !=0";
    	$result=$db->getOne($sql);
    	return $result;
    }
    
    /**
     * 删除一个管理员
     * @return int
     */
    public function del_admin_user_id($user_id){
    	$db=new db();
    	$sql = "delete from xgj_admin_user where user_id=$user_id";
    	$result=$db->query($sql);
    	return $result;
    }
}