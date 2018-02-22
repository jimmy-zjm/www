<?php
namespace Admin\Controller\Index;
use \Admin\Controller\Index\AdminController;
/*
视频位置 控制器
 */
class VideoposController extends AdminController{
    private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Admin\Model\Index\VideoposModel;
    }

    /*
    视频位置列表
     */
    public function index(){
        
        $videoCategoryOb=$this->m;
        //显示分类列表
        $videoList=$videoCategoryOb->video_cat_list();
        //模板传值
        $this->assign('videoList',$videoList);
        //显示模板
        $this->display();
    }

    /*
    添加视频位置
     */
    public function add(){
        //实例化文章管理model类
        $videoCategoryOb=$this->m;
        //显示子分类
        $option=$videoCategoryOb->video_cat_option();
        //模板传值
        $this->assign('permission',$permission);
        $this->assign('option',$option);
        //显示模板
        $this->display();
    }

    /*
    执行添加视频位置
     */
    public function insert(){
        if(!IS_POST) $this->redirect('index');

        if (!empty($_FILES['image'])) {
            if($_FILES['image']['error']==0){
                //将上传成功的商品相册图片地址保存起来
                $image_info = uploadOne('image','VideoPosImage',array(),'IMG_exts');
            }
            if ($image_info['code']!=1) {
                $this->error("图片{$image_info['error']}");
            }
            $_POST['image'] = $image_info['images'];
        }

        if($data = $this->m->create(I('post.',1))){
            if($this->m->add()){
                $this->success('添加成功',U('index'));
                die;
            }
        }

        $this->error($this->m->getError());
    }


    /*
    执行删除视频位置
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
    显示修改视频位置的页面
     */
    public function edit($id){

        $id = intval($id);

        if(!$id) $this->redirect('index');
        //实例化文章管理model类
        $videoCategoryOb=$this->m;

        $pid = M('xgj_video_pos')->field('pid')->find($id);
        $ids=$pid['pid'];
        //显示子分类
        $option=$videoCategoryOb->video_cat_option(0,0,$ids);
        //模板传值
        $this->assign('option',$option);

        $videopos = M('xgj_video_pos')->find($id);
        $this->assign('videopos', $videopos);
        $this->display();
    }

    /*
    执行修改视频位置
     */
    public function update($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');

        $select = M('xgj_video_pos')->where("id=$id")->select();

        if (!empty($_FILES['image']['name'])) {

            if($_FILES['image']['error']==0){
                
                $file = C('IMG_rootPath').$select['0']['image'];
                @unlink($file);
                //将上传成功的商品相册图片地址保存起来
                $image_info = uploadOne('image','VideoPosImage',array(),'IMG_exts');
            }
            if ($image_info) {
                $_POST['image'] = $image_info['images'];
            }
        }

        if($this->m->create(I('post.'),2)){
            if($this->m->save() !== false){
                $this->success('修改成功',U('index'));
                die;
            }
        }
        $this->error($this->m->getError());
    }

    /*
    执行删除广告父类
     */
    
    public function delete_pos($id){
        $id = intval($_GET['id']);
        if(!$id) $this->redirect('index');
        $select = M("xgj_video")->where("video_pos_id=$id")->select();

        $file = C('IMG_rootPath').$select['0']['image'];
        $file1 = C('IMG_rootPath').$select['0']['video'];
        @unlink($file);
        @unlink($file1);

        $select_pos = M("xgj_video_pos")->where("id=$id")->select();
        $file2 = C('IMG_rootPath').$select_pos['0']['image'];
        @unlink($file2);

        $del_pos = M('xgj_video_pos')->where("id=$id")->delete();
        $del = M('xgj_video')->where("video_pos_id=$id")->delete();

        if ($del_pos) {
            $this->success('删除成功',U('index'));
        }else{
            $this->error('删除失败功',U('index'));
        }
        
    }

}