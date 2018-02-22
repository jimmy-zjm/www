<?php
namespace Admin\Controller\Furnish;
use \Admin\Controller\Index\AdminController;

/**
 * 后台商品品牌控制器
 */
class CbrandController extends AdminController{

    public function index(){
        $model = new \Admin\Model\Furnish\CbrandModel;
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
        $this->display();
    }

    /**
     * 添加品牌
     * @return [type] [description]
     */
    public function insert(){
        if(!IS_POST) $this->redirect('index');
        $model = new \Admin\Model\Furnish\CbrandModel;
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
        $info = M('xgj_cbrand')->where('brand_id='.$id)->find();
        if(M('xgj_cbrand')->delete($id)){
            //删除图片和缩略图
            deleteImage($info['logo'],C('IMG_THUMB_CBRAND'));
            $this->success('删除成功',U('index?page='.I('get.page')));
            die;
        }
        $this->error(M('xgj_cbrand')->getError());
    }

    /*显示编辑品牌的页面*/
    public function edit($id){
        $id = (int)$id;
        $data = M('xgj_cbrand')->where('brand_id='.$id)->find();
        $image_list = M('xgj_cbrand_image')->where('b_id='.$id)->select();
        foreach($image_list as &$v){
            $v['url']=getImage($v['url']);
        }
        //查询出顶级的分类
        $this->assign('brand', $data);
        $this->assign('image_list', $image_list);
        $this->display();
    }

    //执行修改品牌
    public function update(){
        $model = new \Admin\Model\Furnish\CbrandModel;
        if($model->create(I('post.'),2)){
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
        if(M()->execute("UPDATE xgj_cbrand SET is_show = is_show^1 WHERE brand_id=$id")){
            echo 1;
        }else{
            echo -1;
        }
    }

   /*
     删除品牌相册中的图片
    */
    public function deleteImage($id){
        $id = intval($id);
        if(empty($id)) die;
        $image = M('xgj_cbrand_image')->find($id);
        if(M('xgj_cbrand_image')->delete($id)){
            //删除图片文件
            deleteImage($image['url'],C('IMG_THUMB_CBRAND'));
            echo 1;
        }else{
            echo -1;
        }
        die;
    }
    
    /*
     切换是品牌相册的图片的显示或者隐藏
    */
    public function toggleImage($id){
        $id = intval($id);
        if(empty($id)) die;
        if(M()->execute('UPDATE xgj_cbrand_image SET is_show = is_show^1 WHERE id='. $id)){
            echo 1;
        }else{
            echo -1;
        }
        die;
    }
}