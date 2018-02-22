<?php
namespace Admin\Controller\Overseas;
use \Admin\Controller\Index\AdminController;

/**
 * 后台国家列表控制器
 */

class DealerController extends AdminController{

    //显示列表页
    public function index(){

        if (!empty($_GET['name'])) {
            $name = I('get.name');
        }else{
            $name = '';
        }

        $like["company"] = array('like', '%'.$name.'%');

        $list = M('xgj_ov_dealer')->where($like)->select();

        $total = count($list);

        $page  = getPage($total,C('MANAGER_PAGE_SIZE'));

        $list  = M('xgj_ov_dealer')->where($like)->limit($page['limit'])->select();

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

        $name = I('post.company');
        $identifier = I('post.identifier');
        if(empty($name)){
            $this->error('请填写公司名称',U('add'));exit;
        }else{
            $one = M('xgj_ov_dealer')->where(array("company"=>$name))->find();
            if (!empty($one)) {
                $this->error('该公司名称已存在',U('add'));exit;
            }
        }
        
        if(empty($identifier)){
            $this->error('请填写经销商编号',U('add'));exit;
        }else{
            $one = M('xgj_ov_dealer')->where(array("identifier"=>$identifier))->find();
            if (!empty($one)) {
                $this->error('该经销商编号已存在',U('add'));exit;
            }
        }

        /******************存入数据************************/
        $tableName = M('xgj_ov_dealer');
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
        $row = M('xgj_ov_dealer')->where("id=$id")->find();
        $this->assign('row',$row);
        $this->display();
    }

    //执行修改
    public function doEdit(){

        $id = I('post.id');

        /******************存入数据************************/
        $tableName = M('xgj_ov_dealer');
        $data = $tableName->create();

        if (!empty($data)){
            if ($tableName->save($data)!=false){
                $this->success('修改成功','index');exit;
            }
        }

        $this->error('修改失败',U('edit',array('id'=>$id)));
        /**************************************************/
    }

    //修改显示隐藏
    public function editShow(){
        $id = I('get.id');
        $row = M('xgj_ov_dealer')->where("id=$id")->find();
        $o = $row['is_show'];
        if ($o == 0) {
            $op = 1;
        }else{
            $op = 0;
        }
        $is['is_show'] = $op;
        $return = M('xgj_ov_dealer')->where("id=$id")->save($is);
        echo $return;
    }

    //删除
    public function doDel(){
        $id = I('get.id');
        $return = M('xgj_ov_dealer')->where("id=$id")->delete();
        echo $return;
    }

}