<?php
namespace Admin\Controller\Index;
use \Admin\Controller\Index\AdminController;
/*
视频 控制器
 */
class VideoController extends AdminController{
    private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Admin\Model\Index\VideoModel;
    }

    /*
    视频列表
     */
    public function index(){
        $map = array();
        if(isset($_GET['name'])){
            $map['name'] = array('like', '%'.I('get.name').'%');
        }

        if(isset($_GET['id'])){
            $map['video_pos_id'] = array('eq', I('get.id/d'));
        }

        //获取视频列表
        $total = M('xgj_video')->where($map)->count();
        $page  = getPage($total,C('VIDEO_PAGE_SIZE'));
        $data  = M('xgj_video')->alias('a')->field('a.*,b.name,b.width,b.height,b.pos_desc')->join('LEFT JOIN xgj_video_pos AS b ON a.video_pos_id=b.id')->where($map)->order('id DESC')->limit($page['limit'])->select(); 
        foreach ($data as $key => &$value) {
            if($value['video_pos_id']==0){
                $value['video_pos'] = '站外视频';
            }else{
                $value['video_pos'] = $value['name'] . '&nbsp;&nbsp;' . $value['width'].'X'.$value['height'];
            }
            $value['image']  = getImage($value['image']);
            $value['is_expire'] = intval($value['start_time']) < time() && intval($value['end_time']) > time()?0:1;
        }       
//var_dump($data);
        $this->assign('page', $page['page']);
        $this->assign('video_list', $data);
        $this->display();
    }

    /*
    添加视频
     */
    public function add(){

        $videoCategoryOb= new \Admin\Model\Index\VideoposModel;
        //显示子分类
        $option=$videoCategoryOb->video_cat_option();
        //模板传值
        $this->assign('option',$option);
        
        // $video_pos_list = M('xgj_video_pos')->select();
        // $this->assign('video_pos_list', $video_pos_list);
        $this->display();
    }

    /*
    执行添加视频
     */
    public function insert(){
        if(!IS_POST) $this->redirect('index');
        //var_dump($_FILES['image'],$_FILES['video']);exit;
        if (!empty($_FILES['image']) && !empty($_FILES['video'])) {
            if($_FILES['image']['error']==0 && $_FILES['video']['error']==0){
                //将上传成功的商品相册图片地址保存起来
                $image_info = uploadOne('image','Video',array(),'IMG_exts');
                $video_info = uploadOne('video','Video',array(),'Video_exts');
            }
            if ($image_info['code']!=1) {
                $this->error("图片{$image_info['error']}");
            }
            if ($video_info['code']!=1) {
                $this->error("视频{$video_info['error']}");
            }
            if(empty($_POST['video_pos_id'])){
                $this->error('视频位置不能为空');
            }
            $data_=array(
                'image'=>$image_info['images'],
                'video'=>$video_info['images'],
                'title'=>trim($_POST['title']),
                'text'=>trim($_POST['text']),
                'video_pos_id'=>intval($_POST['video_pos_id']),
                'start_time' => strtotime(I('post.start_time')),
                'end_time'=> strtotime(I('post.end_time')),
                'is_on'=> $_POST['is_on']==1?1:0,
                'is_home_page'=> $_POST['is_home_page']==1?1:0
            );
            
            if($data = $this->m->create($data_)){
                    if($this->m->add($data_)){
                        $this->success('添加成功',U('index'));
                        die;
                    }
                }
            $this->error('请选择正确的格式');
        }else{
            $this->error('请添加图片和视频');
        }        
    }


    /*
    执行删除视频
     */
    public function delete($id){
        $id = intval($_GET['id']);
        if(!$id) $this->redirect('index');
        $old_img = M('xgj_video')->where(array('id'=>$id))->getField('image');
        $old_video = M('xgj_video')->where(array('id'=>$id))->getField('video');
        if($this->m->delete($id)){
            //删除老的图片
            if(!empty($old_img) && !empty($old_video)) deleteImage($old_img); deleteImage($old_video);

            $this->success('删除成功',U('index'));
            die;
        }
        $this->error($this->m->getError());
    }

    /*
    显示修改视频的页面
     */
    public function edit(){

        $id = intval($_GET['id']);
        if(!$id) $this->redirect('index');
        $video = M('xgj_video')->find($id);
        $video['image'] = getImage($video['image']);
        $video['video'] = getImage($video['video']);
        $video['start_time'] = date('Y-m-d H:i:s', $video['start_time']);
        $video['end_time'] = date('Y-m-d H:i:s', $video['end_time']);
        $video_pos_list = M('xgj_video_pos')->select();

        $videoCategoryOb= new \Admin\Model\Index\VideoposModel;
        //显示子分类
        $option=$videoCategoryOb->video_cat_option(0,0,$video['video_pos_id']);
        //模板传值
        $this->assign('option',$option);
        
        $this->assign('video_pos_list', $video_pos_list);
        $this->assign('video', $video);
        $this->display();
    }


    /*
    执行修改视频
     */
    public function update(){
        $id = intval($_REQUEST['id']);
        //var_dump($id,$_POST,$_FILES);Exit;
        $fid = intval($_POST['fid']);
        if(!$id) $this->redirect('index');
        //var_dump($id,$_POST,$_FILES);Exit;
        if (!empty($_FILES['image']['name'])) {
            if($_FILES['image']['error']==0 ){
                //将上传成功的商品相册图片地址保存起来
                $image_info = uploadOne('image','Video',array(),'IMG_exts');
            }
            //var_dump($image_info);exit;
            if ($image_info['code']!=1) {
                $this->error("新图片{$image_info['error']}");
                    return false;
            }else{
                $img_url = M('xgj_video')->where("id=$id")->getField('image');
                deleteImage($img_url);
                $_POST['image']=$image_info['images'];
            }
        }
        if (!empty($_FILES['video']['name'])) {
            if($_FILES['video']['error']==0 ){
                //将上传成功的商品相册图片地址保存起来
                $video_info = uploadOne('video','Video',array(),'Video_exts');
            }
            if ($video_info['code']!=1) {
                $this->error("新视频{$video_info['error']}");
                    return false;
            }else{
                $video_url = M('xgj_video')->where("id=$id")->getField('video');
                deleteImage($video_url);
                $_POST['video']=$video_info['images'];
            }
        }
        $_POST['start_time'] = strtotime(I('post.start_time'));
        $_POST['end_time']= strtotime(I('post.end_time'));
        if(empty($_POST['video_pos_id'])){
                $this->error('视频位置不能为空');
            }
        if($this->m->where("id=$id")->save($_POST) !== false){
            if (empty($fid)) {
                $this->success('修改成功',U("index"));
            }else{
                $this->success('修改成功',U("index?id=$fid"));
            }
            die;
        }
        $this->error('修改失败');
    }

    /*
    切换视频开启状态
     */
    public function toggleOn($id){
        $id = intval($id);
        //var_dump($id);
        if(!$id) die;
        if(M('xgj_video')->where(array('id'=>$id))->setField('is_on',array('exp','is_on^1'))){
            die('1');
        }else{
            die('-1');
        }
    }

}