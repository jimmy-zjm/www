<?php
namespace Admin\Controller\Eugroup;
use \Admin\Controller\Index\AdminController;

/**
 * 后台国家列表控制器
 */

class CountryController extends AdminController{

    //显示列表页
    public function index(){

        if (!empty($_GET['name'])) {
            $name = I('get.name');
        }else{
            $name = '';
        }

        $like["name"] = array('like', '%'.$name.'%');

        $list = M('xgj_eu_country')->where($like)->select();

        $total = count($list);

        $page  = getPage($total,C('MANAGER_PAGE_SIZE'));

        $list  = M('xgj_eu_country')->where($like)->limit($page['limit'])->select();

        //模板传值
        $this->assign("page",$page['page']);
        $this->assign('list',$list);
        $this->display();
    }

    //显示添加页面
    public function add(){
        $this->display();
    }

    //执行添加
    public function doAdd(){

        $name = I('post.name');

        if(empty($name)){
            $this->error('请填写名称',U('add'));exit;
        }else{
            $one = M('xgj_eu_country')->where(array("name"=>$name))->find();
            if (!empty($one)) {
                $this->error('该名称已存在',U('add'));exit;
            }
        }

        /******************处理上传图片********************/
        if (!empty($_FILES['image']['name'])) {
            $image = uploadOne('image', 'Country', '',C('IMG_THUMB_BRAND'));
            if ($image['code']==1){
                $_POST['image'] = $image['images'];
            }else{
                $this->error('图片上传失败，请重试！',U('add'));exit;
            } 
        }else{
            $this->error('请上传图片',U('add'));exit;
        }
        /**************************************************/

        /******************存入数据************************/
        $tableName = M('xgj_eu_country');
        $data = $tableName->create();

        if (!empty($data)){
            if ($tableName->add($data)){
                $this->success('添加成功','index');exit;
            }
        }

        $this->error('添加失败',U('add'));
        /**************************************************/
    }

    //显示修改页面
    public function edit(){
        $id = I('get.id');
        $row = M('xgj_eu_country')->where("id=$id")->find();
        $this->assign('row',$row);
        $this->display();
    }

    //执行修改
    public function doEdit(){

        $oldImage = I('post.oldImage');
        $id = I('post.id');

        /******************处理上传图片********************/
        if (!empty($_FILES['image']['name'])) {
            $image = uploadOne('image', 'Country', '',C('IMG_THUMB_BRAND'));
            if ($image['code']==1){
                @unlink("./Public/Uploads/$oldImage");
                $_POST['image'] = $image['images'];
            }else{
                $this->error('图片上传失败，请重试！',U('edit',array('id'=>$id)));exit;
            } 
        }
        /**************************************************/

        /******************存入数据************************/
        $tableName = M('xgj_eu_country');
        $data = $tableName->create();

        if (!empty($data)){
            if ($tableName->save($data)){
                $this->success('修改成功','index');exit;
            }
        }

        $this->error('修改失败',U('edit',array('id'=>$id)));
        /**************************************************/
    }

    //修改显示隐藏
    public function editShow(){
        $id = I('get.id');
        $row = M('xgj_eu_country')->where("id=$id")->find();
        $o = $row['is_show'];
        if ($o == 0) {
            $op = 1;
        }else{
            $op = 0;
        }
        $is['is_show'] = $op;
        $return = M('xgj_eu_country')->where("id=$id")->save($is);
        echo $return;
    }

    //删除
    public function doDel(){
        $id = I('get.id');
        $return = M('xgj_eu_country')->where("id=$id")->delete();
        echo $return;
    }

}