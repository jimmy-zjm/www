<?php
namespace Admin\Controller\Index;
use \Admin\Controller\Index\AdminController;
class SaleController extends AdminController{
    private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Admin\Model\Index\SaleModel;
    }
	public function index(){
		$map ='';
        if(!empty($_GET['dt_id'])){
            $map="s.dt_id={$_GET['dt_id']}";
        }
        $info=M('xgj_admin_generalize_user')->select();
        $this->assign('info', $info);
        $total = M('xgj_sale s')->join('xgj_admin_generalize_user u on s.dt_id=u.user_id')->where($map)->count();
        $page  = getPage($total,C('ADPOS_PAGE_SIZE'));
        $data  = M('xgj_sale s')->join('xgj_admin_generalize_user u on s.dt_id=u.user_id')->where($map)->order('id')->limit($page['limit'])->select();
		
        $this->assign('page', $page['page']);
        $this->assign('list', $data);
        $this->display();
	}

	public function add(){
        $info=M('xgj_admin_generalize_user')->select();
        $this->assign('info', $info);
		$this->display();
	}
	public function insert(){
        if(!IS_POST) $this->redirect('index');
		//生成优惠区间
        if($data = $this->m->create(I('post.',1))){
            if($this->m->add()){
                $this->success('添加成功',U('index'));
                die;
            }
        }
        $this->error($this->m->getError());
    } 
	//  修改优惠券页面
     
    public function edit($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');
        $info=M('xgj_admin_generalize_user')->select();
        $this->assign('info', $info);
        $coupon = M('xgj_sale')->find($id);
        $this->assign('data', $coupon);
        $this->display();
    }

    /*
    执行修改管理员
     */
    public function update($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');
        if($this->m->create(I('post.'),2)){		
            if($this->m->save() !== false){
                $this->success('修改成功',U('index'));
                die;
            }
        }
        $this->error($this->m->getError());
    }

	//删除优惠券
    public function delete($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');
        if($this->m->delete($id)){
            $this->success('删除成功',U('index'));
            die;
        }
        $this->error($this->m->getError());
    }
	




}
?>