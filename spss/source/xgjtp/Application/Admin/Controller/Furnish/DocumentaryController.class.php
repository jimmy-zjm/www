<?php
namespace Admin\Controller\Furnish;
use \Admin\Controller\Index\AdminController;

/**
 * 跟单人设置
 */
class DocumentaryController extends AdminController{
	
	private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Admin\Model\Furnish\DocumentaryModel;
    }

    //跟单人首页
    public function index(){
        //分页  
		$map = array();
        if(!empty($_GET['kefu_name'])){
            $vall['linkman'] = array('like', '%'.I('get.kefu_name').'%');
			$map['kefu_id']=M('xgj_admin_user')->where(array('linkman'=>$vall['linkman']))->getField('user_id');
        }
		if(!empty($_GET['gendan_name'])){
            $vall['linkman'] = array('like', '%'.I('get.gendan_name').'%');
			$map['gendan_id']=M('xgj_admin_user')->where(array('linkman'=>$vall['linkman']))->getField('user_id');

        }
        $total = M('xgj_documentary')->where($map)->count();

        $page  = getPage($total,C('MANAGER_PAGE_SIZE'));

        $data  = M('xgj_documentary')->where($map)->limit($page['limit'])->select();
		foreach($data as $key  =>$val){
			$data[$key]['kefuname']=M('xgj_admin_user')->where(array('user_id'=>$val['kefu_id']))->getField('linkman');
			$data[$key]['gendanname']=M('xgj_admin_user')->where(array('user_id'=>$val['gendan_id']))->getField('linkman');
		}
		//echo '<pre>';var_dump($data);
        //模板传值
        $this->assign("page",$page['page']);

        $this->assign('list',$data);
        //显示模板
        $this->display();
    }

    //修改跟单人
    public function edit($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');
		
        $data = M('xgj_documentary')->find($id);
        $data1 = M('xgj_admin_user')->select();
	/*	$data2 = M('xgj_admin_user')->where(array('user_id'=>$data['gendan_id']))->find();
echo "<pre>";var_dump($data1);echo '----------------------------<br>';var_dump($data2);*/
        $this->assign('data', $data);
        $this->assign('data1', $data1);
		$this->assign('data2', $data2);
		
        $this->display();
    }

     /*
    执行修改
     */
    public function update($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');
		//var_dump(I('post.'));die();
        if($this->m->create(I('post.'),2)){	
            if($this->m->save() !== false){
                $data = M('xgj_documentary as d')->join("xgj_admin_user as u on u.user_id=d.gendan_id")->find($id);//var_dump($data);die();
                $re=M('xgj_furnish_order_info')->where("order_id={$data['order_id']}")->setField(array("admin_id"=>$data['gendan_id'],"order_merchandiser"=>$data['linkman']));
                if($re){
                   $this->success('修改成功',U('index'));
                die; 
                }
            }
        }
        $this->error($this->m->getError());
    }


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