<?php
namespace Admin\Controller\Index;
use \Admin\Controller\Index\AdminController;
/*
后台管理员控制器
 */
class ManagerController extends AdminController{
    private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Admin\Model\Index\ManagerModel;
    }

    /*
    管理员列表
     */
    public function index(){
        $map = array();
        if(isset($_GET['user_name'])){
            $map['user_name'] = array('like', '%'.I('get.user_name').'%');
        }
        if(isset($_GET['linkman'])){
            $map['linkman'] = array('like', '%'.I('get.linkman').'%');
        }
        $total = M('xgj_admin_user')->where($map)->count();
        $page  = getPage($total,C('MANAGER_PAGE_SIZE'));
        $data  = M('xgj_admin_user')->where($map)->limit($page['limit'])->select();
        $this->assign('page', $page['page']);
        $this->assign('manager_list', $data);
        $this->display();
    }

    /*
    添加管理员
     */
    public function add(){
        $this->display();
    }

    /*
    执行添加管理员
     */
    public function insert(){
        if(!IS_POST) $this->redirect('index');
        if($data = $this->m->create(I('post.',1))){
            if($this->m->add()){
                $this->success('添加成功',U('index'));
                die;
            }
        }
        $this->error($this->m->getError());
    }

    /*
    显示修改管理员的页面
     */
    public function edit($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');
        if($_SESSION['admin_user']['user_id']!=1 && $id!=1) $this->error('没有权限修改超级管理员信息');
        $user = M('xgj_admin_user')->find($id);
        $this->assign('user', $user);
        $this->display();
    }

    /*
    执行修改管理员
     */
    public function update($user_id){
        $user_id = intval($user_id);
        if(!$user_id) $this->redirect('index');
        if($_SESSION['admin_user']['user_id']!=1 && $id!=1) $this->error('没有权限修改超级管理员信息');
        if($this->m->create(I('post.'),2)){
            if($this->m->save() !== false){
                $this->success('修改成功',U('index'));
                die;
            }
        }
        $this->error($this->m->getError());
    }

    /*
    执行删除管理员
     */
    public function delete($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');
        if($id==1) $this->error('不能删除超级管理员');
        if($this->m->delete($id)){
            $this->success('删除成功',U('index'));
            die;
        }
        $this->error($this->m->getError());
    }

    /*
    显示分配权限的页面
     */
    public function dispath($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');
        if($id==1) $this->error('超级管理员没有权限限制');
        $per  = M('xgj_admin_user')->where(array('user_id'=>$id))->getField('permission');
        if(!empty($per)){
            $this->assign('pri_list', array_filter(explode(',', $per)));
        }
        $data = M('xgj_column')->where(array('pid'=>0,'status'=>'1'))->order('is_order desc,id')->select();
        //$info = M('xgj_column')->where("id in ($per)")->select();
        $pl='';
        foreach ($data as $ke => $va) {
            $data[$ke]['list']= M('xgj_column')->where("pid = {$va['id']} and status='1' ")->order('is_order desc,id')->select();
            foreach ($data[$ke]['list'] as $k => $v) {
                if($va['id']=$v['pid']){
                    $data[$ke]['pl'].='pri-'.$v['id'].',';
                }
            }
        }
        //var_dump($per,$info);exit;
        $this->assign('data',$data);
        //$this->assign('info',$info);
        $this->assign('id',$id);
        $this->display();
    }

    /*
    执行分配权限
     */
    public function dodispath(){
        $id = I('post.id/d');
        $pri=I('post.pri');
        $str=''; 
        foreach ($pri as $k => $v) {
            $str.=ltrim($v,'pri-').',';
        }
        $pri_str=rtrim($str,',');
        //var_dump($pri_str);exit;
        if(M('xgj_admin_user')->where(array('user_id'=>$id))->setField('permission', $pri_str)){
            $this->success('修改成功','index');
            die;
        }
        $this->error('修改失败','index');
    }


    /*
    切换 是否启动账号
     */
    public function toggle($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');

        //不切换超级管理员的状态
        if($id==1) die('-1');

        if(M('xgj_admin_user')->where(array('user_id'=>$id))->setField('is_lock',array('exp','is_lock^1'))){
            echo 1;
        }else{
            echo -1;
        }
        die;
    }

    public function generalize(){
        $map = array();
        if(isset($_GET['user_name'])){
            $map['user_name'] = array('like', '%'.I('get.user_name').'%');
        }
        if(isset($_GET['linkman'])){
            $map['linkman'] = array('like', '%'.I('get.linkman').'%');
        }
        $total = M('xgj_admin_generalize_user')->where($map)->count();
        $page  = getPage($total,C('MANAGER_PAGE_SIZE'));
        $data  = M('xgj_admin_generalize_user')->where($map)->limit($page['limit'])->select();
        $this->assign('page', $page['page']);
        $this->assign('manager_list', $data);
        $this->display();
    }

    public function generalizeToggle($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');

        if(M('xgj_admin_generalize_user')->where(array('user_id'=>$id))->setField('is_lock',array('exp','is_lock^1'))){
            echo 1;
        }else{
            echo -1;
        }
        die;
    }

    public function generalizeAdd(){
        $this->display();
    }

    public function generalizeInsert(){
        $add['user_name'] = I('post.user_name');
        $add['password']  = md5(I('post.password').C('MD5_KEY'));
        $add['linkman']   = I('post.truename');
        $add['tel']       = I('post.telphone');
        $add['is_lock']   = I('post.is_use')==1?0:1;
        $add['add_time']  = time(); 

        if (empty($_POST['user_name'])) $this->error('请填写用户名');
        if (empty($_POST['password'])) $this->error('请填写密码');
        $is_name = M('xgj_admin_generalize_user')->where(array("user_name"=>$add['user_name']))->find();
        if ($is_name) $this->error('用户名已存在');

        if(!IS_POST) $this->redirect('generalize');
        if($data = M('xgj_admin_generalize_user')->create($add)){
            if(M('xgj_admin_generalize_user')->add($add)){
                $this->success('添加成功',U('generalize'));
                die;
            }
        }
        $this->error($this->m->getError());
    }

    public function generalizedelete($id){
        $id = intval($id);
        if(!$id) $this->redirect('generalize');
        if(M('xgj_admin_generalize_user')->delete($id)){
            $this->success('删除成功',U('generalize'));
            die;
        }
        $this->error($this->m->getError());
    }

    
    public function generalizeEdit($id){
        $id = intval($id);
        if(!$id) $this->redirect('generalize');
        $user = M('xgj_admin_generalize_user')->find($id);
        $this->assign('user', $user);
        $this->display();
    }

    public function generalizeUpdate(){

        $user_id = I('post.user_id');
        $id = intval($user_id);
        if(!$user_id) $this->redirect('generalize');

        if(isset($data['user_name'])) unset($data['user_name']);//排除用户名, 不修改用户名

        //传了密码, 就修改密码
        if(!empty($_POST['password'])){
            $data['password'] = md5(I('post.password').C('MD5_KEY'));
        }else{
            unset($data['password']);
        }

        $data['linkman']  = I('post.truename');
        $data['tel']      = I('post.telphone');
        $data['is_lock']  = I('post.is_use')==1?0:1;
        
        if(M('xgj_admin_generalize_user')->create($data)){
            if(M('xgj_admin_generalize_user')->where("user_id=$user_id")->save($data) !== false){
                $this->success('修改成功',U('generalize'));
                die;
            }
        }
        $this->error($this->m->getError());
    }

}