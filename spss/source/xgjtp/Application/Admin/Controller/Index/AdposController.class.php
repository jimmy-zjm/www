<?php
namespace Admin\Controller\Index;
use \Admin\Controller\Index\AdminController;
/*
广告位置 控制器
 */
class AdposController extends AdminController{
    private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Admin\Model\Index\AdposModel;
    }

    /*
    广告位置列表
     */
    public function index(){
        $map = array();
        if(isset($_GET['name'])){
            $map['name'] = array('like', '%'.I('get.name').'%');
        }

        $total = M('xgj_ad_pos')->where($map)->count();
        $page  = getPage($total,C('ADPOS_PAGE_SIZE'));
        $data  = M('xgj_ad_pos')->where($map)->order('id DESC')->limit($page['limit'])->select();
        $this->assign('page', $page['page']);
        $this->assign('adpos_list', $data);
        $this->display();
    }

    /*
    添加广告位置
     */
    public function add(){
        $this->display();
    }

    /*
    执行添加广告位置
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
    执行删除广告位置
     */
    public function delete($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');
        if($this->m->delete($id)){
            $this->success('删除成功',U('index'));
            die;
        }
        $this->error($this->m->getError());
    }


    /*
    显示修改广告位置的页面
     */
    public function edit($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');
        $adpos = M('xgj_ad_pos')->find($id);
        $this->assign('adpos', $adpos);
        $this->display();
    }

    /*
    执行修改广告位置
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

}