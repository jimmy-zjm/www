<?php
namespace Admin\Controller\Index;
use \Admin\Controller\Index\AdminController;
/*
广告 控制器
 */
class AdController extends AdminController{
    private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Admin\Model\Index\AdModel;
    }

    /*
    广告列表
     */
    public function index(){
        $map = array();
        if(isset($_GET['name'])){
            $map['name'] = array('like', '%'.I('get.name').'%');
        }

        if(isset($_GET['id'])){
            $map['ad_pos_id'] = array('eq', I('get.id/d'));
        }

        //获取广告列表
        $total = M('xgj_ad')->where($map)->count();
        $page  = getPage($total,C('Ad_PAGE_SIZE'));
        $data  = M('xgj_ad')->alias('a')->field('a.*,b.name,b.width,b.height,b.pos_desc')->join('LEFT JOIN xgj_ad_pos AS b ON a.ad_pos_id=b.id')->where($map)->order('id DESC')->limit($page['limit'])->select();

        //处理数据
        $ad_type_list = C('AD_TYPE_LIST');
        foreach ($data as $key => &$value) {
            if($value['ad_pos_id']==0){
                $value['ad_pos'] = '站外广告';
            }else{
                $value['ad_pos'] = $value['name'] . '&nbsp;&nbsp;' . $value['width'].'X'.$value['height'];
            }
            $value['type']   = isset($ad_type_list[$value['type']])?$ad_type_list[$value['type']]:'类型值错误';
            $value['image']  = getImage($value['image']);
            $value['is_expire'] = intval($value['start_time']) < time() && intval($value['end_time']) > time()?0:1;
        }

        $this->assign('page', $page['page']);
        $this->assign('ad_list', $data);
        $this->display();
    }

    /*
    添加广告
     */
    public function add(){
        $ad_pos_list = M('xgj_ad_pos')->select();
        $this->assign('ad_pos_list', $ad_pos_list);
        $this->display();
    }

    /*
    执行添加广告
     */
    public function insert(){
        // dump($_FILES);
        // dump($_POST);die;
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
    执行删除广告
     */
    public function delete($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');
        $old_img = M('xgj_ad')->where(array('id'=>$id))->getField('image');
        if($this->m->delete($id)){
            //删除老的图片
            if(!empty($old_img)) deleteImage($old_img);

            $this->success('删除成功',U('index'));
            die;
        }
        $this->error($this->m->getError());
    }


    /*
    显示修改广告的页面
     */
    public function edit($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');
        $ad = M('xgj_ad')->find($id);
        $ad['image'] = getImage($ad['image']);
        $ad['start_time'] = date('Y-m-d H:i:s', $ad['start_time']);
        $ad['end_time'] = date('Y-m-d H:i:s', $ad['end_time']);
        $ad_pos_list = M('xgj_ad_pos')->select();
        $this->assign('ad_pos_list', $ad_pos_list);
        $this->assign('ad', $ad);
        $this->display();
    }

    /*
    执行修改广告
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

    /*
    切换广告开启状态
     */
    public function toggleOn($id){
        $id = intval($id);
        if(!$id) die;
        if(M('xgj_ad')->where(array('id'=>$id))->setField('is_on',array('exp','is_on^1'))){
            die('1');
        }else{
            die('-1');
        }
    }



}