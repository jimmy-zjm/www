<?php
namespace Admin\Controller\Eugroup;
use \Admin\Controller\Index\AdminController;

/**
 * 后台属性控制器
 */
class AttributeController extends AdminController{
    private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Admin\Model\Eugroup\AttributeModel;
    }

    /*
    根据类型id, 显示属性列表
    */
    public function index($id){
        $type_id = (int)$id;
        $model = $this->m;
        $map = array();
        if($type_id != 0){
            $map['type_id'] = $type_id;
        }

        $total = $model->where($map)->count();
        $page = getPage($total, C('ATTR_PAGE_SIZE'));
        $data = $model->where($map)->limit($page['limit'])->select();
        //取出商品类型的数据
        $type = M('xgj_eu_type')->where(array('id'=>$type_id))->find();
        $type_list = M('xgj_eu_type')->where(array('is_use'=>1,'class_id'=>'1'))->select();
        $this->assign('attr_list', $data);
        $this->assign('_type', $type);
        $this->assign('type_list', $type_list);
        $this->assign('type_id', $type_id);
        $this->assign('page', $page['page']);
        $this->display();
    }

    /*显示添加属性的页面*/
    public function add(){
        $data = M('xgj_eu_type')->where(array('is_use'=>1,'class_id'=>'1'))->select();
        $this->assign('type_list', $data);
        $this->display();
    }

    /**
     * 执行添加属性
     * @return [type] [description]
     */
    public function insert(){
        if(!IS_POST) $this->redirect('index');
        $model = $this->m;
        if($data = $model->create(I('post.'), 1)){
            if($model->add()){
               $this->success('添加成功',U('add',array('type_id'=>I('post.type_id'))));
               die;
            }
        }
        $this->error($model->getError());
    }


    /**
     * 删除属性
     * @return [type] [description]
     */
    public function delete(){
        $id = I('get.id/d');
        if(empty($id)) $this->redirect('index');
        $args['id'] = I('get.type_id/d');//回传类型id
        if(M('xgj_eu_attribute')->delete($id)){
            $this->success('删除成功',U('index', $args));
            die;
        }
        $this->error(M('xgj_eu_attribute')->getError());
    }

    /*
    显示属性的页面
    */
    public function edit($id, $type_id){
        $id = (int)$id;
        $type_id = (int)$type_id;
        $data = M('xgj_eu_attribute')->where('id='.$id)->find();

        //商品类型列表
        $type_list = M('xgj_eu_type')->where(array('is_use'=>1,'class_id'=>'1'))->select();

        $this->assign('attr', $data);
        $this->assign('type_list', $type_list);
        $this->assign('type_id', $type_id);
        $this->display();
    }

    /*执行修改属性*/
    public function update(){
        $model = $this->m;
        if($model->create(I('post.'),2)){
            if($model->save() !==false){
                $this->success('修改成功',U('index',array('id'=>I('post.type_id'))));
                die;
            }
        }
        $this->error($model->getError());
    }



}