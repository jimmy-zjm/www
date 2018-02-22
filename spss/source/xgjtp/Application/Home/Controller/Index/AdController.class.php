<?php
namespace Home\Controller\Index;
use Think\Controller;
class AdController extends Controller{
    /*
    显示广告
     */
    public function showAd(){
        $id = I('get.id');
        if(empty($id)) die;
        $time = time();
        $ad = M('xgj_ad')->where(array(
            'id'=>$id,
            'is_on'=>1,
            'start_time'=>array('lt',$time),
            'end_time'=>array('gt',$time),
            ))->find();
        if(!empty($ad)){
            //增加点击次数
            M('xgj_ad')->where(array('id'=>$ad['id']))->setInc('click_count');

            //执行跳转
            header('Location:'.$ad['url']);
        }
        die;
    }

    /*
    显示广告的图片
     */
    public function showAdImg(){
        $id = I('get.id/d');
        if(empty($id)) die('非法参数');
        $time = time();
        $file = M('xgj_ad')->where(array(
            'id'=>$id,
            'is_on'=>1,
            'start_time'=>array('lt',$time),
            'end_time'=>array('gt',$time),
            ))->find();
        if(empty($file))die('广告已失效');
        $file = C('IMG_rootPath').$file['image'];
        if(file_exists($file) && is_file($file)){
            readfile($file);
            die();
        }
        die('广告文件不存在');
    }
}