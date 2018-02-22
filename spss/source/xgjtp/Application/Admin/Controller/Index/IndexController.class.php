<?php
namespace Admin\Controller\Index;
/*
后台首页控制器
 */
class IndexController extends AdminController {
    public function index(){
        $this->display();
    }
    public function main(){
        $last_login = $_SESSION['admin_user']['last_login']==0?'没有登陆过':date('Y-m-d H:i:s',$_SESSION['admin_user']['last_login']);
        $admin_name=$_SESSION['admin_user']['user_name'];
        $admin_id=$_SESSION['admin_user']['user_id'];
        $this->assign('admin_id', $admin_id);
        $this->assign('admin_name', $admin_name);
        $this->assign('last_login', $last_login);
        $this->display();
        die;
    }
    public function top(){
    	$admin_name=$_SESSION['admin_user']['user_name'];
    	$this->assign('admin_name', $admin_name);
        $this->display();
        die;
    }
    public function left(){
        $id = $_SESSION['admin_user']['user_id'];
        $pri = M('xgj_admin_user')->where(array('user_id'=>$id))->getField('permission');
        if($pri==0){
            $inf = M('xgj_column')->where(array('pid'=>0,'status'=>'1'))->order('is_order desc,id')->select();
            foreach ($inf as $ke => $va) {
                $inf[$ke]['list']= M('xgj_column')->where("pid = {$va['id']} and status='1' ")->order('is_order desc,id')->select();
            }
        }else{
            $data = M('xgj_column')->where("id in ($pri) and status='1'")->order('is_order desc,id')->select();
            $info=array();
            foreach ($data as $k => $v) {
                $info[$v['pid']][]= $v;
            }
            $infos=array();
            foreach ($info as $k => $v) {
                $infos[$k]= M('xgj_column')->where("id = {$k} and status='1' ")->find();
                $infos[$k]['list']=$v;
            }
            $inf=array();
            foreach ($infos as $k => $v) {
                $inf[]=$v;
                
            }
        }
        //var_dump($inf);exit;
        $this->assign('id', $id);
        //$this->assign('pri',array_filter(explode(',', $pri)));
        $this->assign('pri_list', $inf);
        $this->display();
        die;
    }
    public function footer(){
        $this->display();
        die;
    }
}