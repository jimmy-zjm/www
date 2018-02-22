<?php
namespace Admin\Controller\Furnish;
use \Admin\Controller\Index\AdminController;

/**
 * 后台商品品牌控制器
 */
class CbrandInfoController extends AdminController{

    public function index(){
        $model = new \Admin\Model\Furnish\CbrandInfoModel;
        $list = $model->getCbrand();
        $this->assign('list', $list);
        $data = $model->getAll();
        $this->assign('brand_list', $data['brand_list']);
        $this->assign('page',$data['page']);
        $this->display();
    }


    /*
    显示添加品牌的页面
     */
    public function add(){
        //查询出顶级的分类
        $model = new \Admin\Model\Furnish\CbrandInfoModel;
        $data = $model->getCbrand();
        $this->assign('brand_list', $data);
        $this->display();
    }
    public function ajaxCbrand(){
        $model = new \Admin\Model\Furnish\CbrandInfoModel;
        $id=intval($_GET['id']);
        $data = $model->getCbrand($id);
        $html='';
        $html.="<label>合作品牌<b>*</b></label><select name='b_id' class='dfinput'><option>请选择..</option>";
        foreach ($data as $key=>$v){
                $html.="<option value={$v['brand_id']}>{$v['name']}</option>";
        }
        $html.="</select>";
        echo $html;
    }

    /**
     * 添加品牌
     * @return [type] [description]
     */
    public function insert(){
        if(!IS_POST) $this->redirect('index');
        $model = new \Admin\Model\Furnish\CbrandInfoModel;
        if($model->create(I('post.'), 1)){
            if($model->add()){
               $this->success('添加成功',U('add'));
               die;
            }
        }
        $this->error($model->getError());
    }

    /**
     * 删除品牌
     * @return [type] [description]
     */
    public function delete(){
        $id = I('get.id/d');
        $info = M('xgj_cbrand_info')->where('id='.$id)->find();
        if(M('xgj_cbrand_info')->delete($id)){
            //删除图片和缩略图
            deleteImage($info['logo'],C('IMG_THUMB_CBRAND'));
            $this->success('删除成功',U('index?page='.I('get.page')));
            die;
        }
        $this->error(M('xgj_cbrand_info')->getError());
    }

    /*显示编辑品牌的页面*/
    public function edit($id){
        $id = (int)$id;
        $model = new \Admin\Model\Furnish\CbrandInfoModel;
        $list = $model->getCbrand();
        $this->assign('brand_list', $list);
        $data = M('xgj_cbrand_info')->where('id='.$id)->find();
        //查询出顶级的分类
        $this->assign('brand', $data);
        $this->display();
    }

    //执行修改品牌
    public function update(){
        $model = new \Admin\Model\Furnish\CbrandInfoModel;
        if($model->create(I('post.',2))){
            if($model->save()!==false){
                $this->success('修改成功',U('index?page='.I('get.page')));
                die;
            }
        }
        $this->error($model->getError());
    }


    //切换品牌的显示状态(隐藏/显示), 返回整数表示成功, 负数表示失败
    public function toggle(){
        $id = I('get.id/d');
        if(M()->execute("UPDATE xgj_cbrand_info SET is_show = is_show^1 WHERE id=$id")){
            echo 1;
        }else{
            echo -1;
        }
    }
}