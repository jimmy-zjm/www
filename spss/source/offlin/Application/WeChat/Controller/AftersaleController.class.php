<?php
namespace WeChat\Controller;
use Think\Controller;

class AftersaleController extends BaseController {
    //售后订单列表
    public function index(){
        $info = M('pad_sale_order_info')->group('o_id')->select();
        $id=$ids='';
        foreach ($info as $k => $v) {
            if($v['types']=='维修'){
                $id .= ','.$v['o_id'];
            }else if($v['types']=='保养'){
                $ids .= ','.$v['o_id'];
            }
        }
        $list = M('pad_sale_order')->where(['id'=>['in',$id],'x_id'=>$_SESSION['userId']])->select();
        $lists = M('pad_sale_order')->where(['id'=>['in',$ids],'x_id'=>$_SESSION['userId']])->select();
        $map = ['uid'=>$_SESSION['userId']];
        $data=M('pad_order')->where($map)->order('add_time')->count();
        $this->assign('data',$data);
        $this->assign('list',$list);
        $this->assign('lists',$lists);
        $this->display();
    }

    //订单详情 
    public function detail(){
        $id=I('id');//售后订单id
        $info = M('pad_sale_order_info')->where(['o_id'=>$id,'x_id'=>$_SESSION['userId']])->select();
        $list = M('pad_sale_order')->where(['id'=>$id])->find();
		$list['image']=explode('|',trim($list['image'],'|'));
		$list['img']=explode('|',trim($list['img'],'|'));
        if($list['uid']==0){
            $user ='';
        }else{
           $user = M('pad_user')->where(['id'=>$list['uid']])->find(); 
        }
        $this->assign('info',$info);
        $this->assign('user',$user);
        $this->assign('list',$list);
        $this->display();
    }

    //订单页
    public function editInfo(){
        $_SESSION['ide']='biaoshi';
        $area = getPCD();
        $this->assign("area",$area);
        $tab=I('tab');
        $map = ['uid'=>$_SESSION['userId']];
        $data=M('pad_order')->where($map)->order('add_time')->select();
        foreach ($data as $k => $v) {
            $id .= ','.$v['id'];
        }

        $info = M('pad_order_info')->field('name')->where(['order_id'=>['in',trim($id,',')]])->select();
        $this->assign('data',$info);
        $this->assign('u_id',$data[0]['u_id']);
        $this->assign('ide',$_SESSION['ide']);
        $this->display();
    }

    //查询省市县三级联动
    public function area(){
        $id = $_GET['v'];
        $return = M('xgj_area')->where("pid=$id")->field('id,name')->select();
        echo json_encode($return);
    }

    //提交订单
    public function getsub(){
        layout(false);
        if($_POST){
            if(empty($_POST['ide'])){
                $this->error('不可重复提交',U("Aftersale/index"));
            }
            $_POST['yy_time'] = strtotime($_POST["yy_time"]);   //上门日期
            $quoteName = I('post.quoteName');
            $u_id = I('post.u_id');
            if(!empty($_FILES['file']['name']['0'])){
                $pics = array();
                for ($i=0; $i < count($_FILES['file']['name']); ++$i) {
                    $pics[] = array(
                        'name'     => $_FILES['file']['name'][$i],
                        'type'     => $_FILES['file']['type'][$i],
                        'tmp_name' => $_FILES['file']['tmp_name'][$i],
                        'error'    => $_FILES['file']['error'][$i],
                        'size'     => $_FILES['file']['size'][$i],
                    );
                }
                $data['image']='';
                foreach ($pics as $key=>$value) {
                    $_FILES[$key] = $value;
                    if($_FILES[$key]['error']==0){
                        //将上传成功的商品相册图片地址保存起来
                        $image = uploadOne($key,'Aftersale',C('IMG_THUMB_FACE'));
                        if($image['code']!=1){
                             //头像上传失败
                             $error = $image['error'];
                             $status=false;
                             return false;
                        }
                        $data['image'] .= $image['images'].'|';
                        $status=true;
                    }else{
                        $error ='图片上传失败:'.$image['error'];
                        $status=false;
                        return false;
                    }
                }
                $list             = M('pad_sale_order')->create();
                $list['x_id']     = $_SESSION['userId'];
                $list['u_id']     = $u_id;
                $list['image']    = $data['image'];
                $microtime        = explode('.',microtime(true));//转化时间戳 
                $list['sale_sn']  = date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT);
                $list['province'] = M('xgj_area')->where(['id'=>$list['province']])->getField('name');
                $list['city']     = M('xgj_area')->where(['id'=>$list['city']])->getField('name');
                $list['district'] = M('xgj_area')->where(['id'=>$list['district']])->getField('name');
                $model = M();
                $model->startTrans();
                $re=$model->table('pad_sale_order')->add($list);
                if($status && $re!==false){
                    $addInfo['o_id'] = $re;
                    if (I('post.tab')==1) {
                        $addInfo['types'] = '维修';
                    }else{
                        $addInfo['types'] = '保养';
                    }
                    $addInfo['buy_time'] = time();
                    foreach ($quoteName as $k => $v) {
                        $addInfo['name'] = $v;
                        $re = $model->table('pad_sale_order_info')->add($addInfo);
                        if ($re === false) {
                            $model->rollback();
                        }
                    }
                    unset($_SESSION['ide']);
                    $model->commit();
                    $this->success('上传成功',U("Aftersale/index"));
                }else{
                    $this->error($error);
                }

            }else{
                $this->error('请选择图片！！');
            }
        }    
    }
    
}	


