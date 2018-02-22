<?php
namespace Admin\Controller\Eugroup;
use \Admin\Controller\Index\AdminController;

/**
 * 后台商品类型控制器
 */
class TypeController extends AdminController{
    private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Admin\Model\Eugroup\TypeModel;
    }

    public function index(){
        $data = $this->m->getAll();
        $this->assign('type_list', $data['data']);
        $this->assign('page', $data['page']);
        $this->display();
    }
    /**
     * 展示添加的页面
     */
    public function add(){
        $this->display();
    }


    /**
     * 执行类型的添加
     * @return [type] [description]
     */
    public function insert(){
        $model = $this->m;
        if($data = $model->create(I('post.'),1)){
            if($model->add()){
                $this->success('添加成功',U('add'));
                die;
            }
        }
        $this->error($model->getError());
    }


    //同时删除该类型下面的所有属性
    public function delete($id){
        $id = (int)$id;
        if($this->m->where('id='.$id)->delete()){
            //删除成功
            //删除相应的属性
            M('xgj_eu_attribute')->where(array('type_id'=>$id))->delete();
            $this->success('删除成功',U('index'));
            die;
        }

        $this->error($model->getError());
    }

    /*展示编辑的页面*/
    public function edit($id){
        $id = (int)$id;
        $data = $this->m->where('id='.$id)->find();
        $this->assign('type', $data);
        $this->display();
    }

    /*执行修改*/
    public function update($id){
        $id = (int)$id;
        $model = $this->m;
        if($model->create(I('post.',2))){
            if($model->save()!==false){
                $this->success('修改成功',U('index'));
                die;
            }
        }
        $this->error($model->getError());
    }


    //切换类型的显示状态(启用/不启用)
    public function toggle(){
        $id = I('get.id/d');
        if(M()->execute("UPDATE xgj_eu_type SET is_use = is_use^1 WHERE id=$id")){
            echo 1;
        }else{
            echo -1;
        }
    }

}