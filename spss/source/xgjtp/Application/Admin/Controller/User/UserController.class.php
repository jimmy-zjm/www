<?php
namespace Admin\Controller\User;
use \Admin\Controller\Index\AdminController;
/*
用户名控制器
 */
class UserController extends AdminController{
    private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Admin\Model\User\UserModel;
    }

	public function index(){
		$map = array();
        if(!empty($_GET['mobile'])){
            $map['mobile_phone'] = array('like', '%'.I('get.mobile').'%');
        }
		if(!empty($_GET['starttime'])&&!empty($_GET['endtime'])){
			 $map['reg_time']=array(array('gt',strtotime(I('get.starttime'))),array('lt',strtotime(I('get.endtime'))));			
		}
		
        $total = M('xgj_users')->where($map)->count();
        $page  = getPage($total,C('ADPOS_PAGE_SIZE'));
        $data  = M('xgj_users')->where($map)->order('user_id')->limit($page['limit'])->select();
        $this->assign('page', $page['page']);
        $this->assign('user_list', $data);
        $this->display();
	}
	public function add(){

		$this->display();
	}
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

	public function edit($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');
		
        $data = M('xgj_users')->find($id);
        $this->assign('user', $data);
		
        $this->display();
    }

    /*
    执行修改
     */
    public function update($user_id){
        $id = intval($user_id);
        if(!$id) $this->redirect('index');
        if($this->m->create(I('post.'),2)){	
            if($this->m->save() !== false){
                $this->success('修改成功',U('index'));
                die;
            }
        }
        $this->error($this->m->getError());
    }

}
?>