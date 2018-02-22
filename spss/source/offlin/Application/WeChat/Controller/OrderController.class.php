<?php
namespace WeChat\Controller;
use Think\Controller;

class OrderController extends BaseController {

    public function index(){
        layout(false);
        $list = M('pad_order')->where(['uid'=>$_SESSION['userId']])->select();
        foreach ($list as $k => $v) {
            $id .= ','.$v['id'];
        }
        $id = trim($id,',');
        $info = M('pad_order_info')->field('order_id,price,province,city,district,address')->where(['order_id'=>['in',$id]])->select();
        foreach ($info as $k => $v) {
            $money[$v['order_id']] += $v['price'];
            $addr[$v['order_id']] = $v['province'].$v['city'].$v['district'].$v['address'];
        }
        $this->assign('list',$list);
        $this->assign('money',$money);
        $this->assign('addr',$addr);

        $this->display();
    }

    public function search(){
        layout(false);
        $this->display();
    }

    public function doSearch(){
        $key = I('key');
        $map['uid'] = $_SESSION['userId'];
        $map['sn'] = ['like','%'.$key.'%'];

        $list = M('pad_order')->where($map)->select();
        foreach ($list as $k => $v) {
            $id .= ','.$v['id'];
        }
        $id = trim($id,',');
        //var_dump($id);die;
        $info = M('pad_order_info')->field('order_id,price,province,city,district,address')->where(['order_id'=>['in',$id]])->select();
        foreach ($info as $k => $v) {
            $money[$v['order_id']] += $v['price'];
            $addr[$v['order_id']] = $v['province'].$v['city'].$v['district'].$v['address'];
        }
        
        foreach ($list as $k => $v) {
            $html .= '<li>
                        <div class="item-top">
                            房屋信息
                            <p class="item-title">'.$addr[$v['id']].'</p>
                        </div>
                        <div class="item-body">
                            <div>
                                <p class="item-name">订单号：'.$v["sn"].'</p>
                                <p class="item-name">订单金额：'.$money[$v['id']].'元</p>
                                <p class="item-name">订单时间：'.date("Y-m-d H:i:s",$v["add_time"]).'</p>
                                <p class="item-btn-list">
                                    <a href="'.U("orderdetails",["oid"=>$v["id"]]).'">订单详情</a>
                                </p>
                            </div>
                        </div>
                    </li>';     
        }   
        echo $html;
    }

    public function detailedList(){
        layout(false);
        $id = I('get.id');
        $info = M('pad_order_info')->field('info,order_id')->where(['id'=>$id,'uid'=>$_SESSION['userId']])->find();
        $list = json_decode(stripslashes($info['info']),true);
        $this->assign('oid',$info['order_id']);
        $this->assign('list',$list);
        $this->display();
    }

    public function orderdetails(){
        layout(false);
        $oid = I('get.oid');
        $map = ['id'=>$oid,'uid'=>$_SESSION['userId']];
        $data=M('pad_order')->where($map)->find();
        $info = M('pad_order_info')->field('order_id,price,province,city,district,address')->where(['order_id'=>['in',$oid]])->select();
        foreach ($info as $k => $v) {
            $data['money'] += $v['price'];
            $data['addr']= $v['province'].$v['city'].$v['district'].$v['address'];
        }
        $re = M('pad_workplan')->where(['o_id'=>$oid])->select();
        foreach ($re as $k => $v) {
            $list[$v['workname']] = $v;
        }
        //查询所有系统
        $quoteinfo=M('pad_order_info i')
                ->where(['i.order_id'=>$oid])
                ->select();

        $this->assign('list',$list);
        $this->assign('quoteinfo',$quoteinfo);
        $this->assign('data',$data);
        $this->assign('oid',$oid);
        $this->display();
    }

    public function look(){
        layout(false);
      
        $oid=I('get.oid');
        $id  = I('get.id');
        $type = I('get.type');
        switch ($type) {
             case '1':
                $title='房屋面积';
                break;
            case '2':
                $title='查看图纸';
                break;
            case '3':
                $title='施工进度';
                break;
            case '4':
                $title='调试进度';
                break;
            case '5':
                $title='验收进度';
                break;
        }
       /* $list = M('pad_file')->where(['id'=>$id])->find();

        $re = M('pad_order')->where(['id'=>$list['o_id'],'uid'=>$_SESSION['userId']])->count();

        if ($re>0) {
            $this->assign('list',$list);
        }*/
        $files = M('pad_file')->where(['o_id'=>$oid,'types'=>$type])->select();
        $this->assign('id',$id);
        $this->assign('files',$files);
        $this->assign('title',$title);
        $this->display();
    }

    public function process(){
        layout(false);
        $oid  = I('get.oid');
        $type = I('get.type');
        switch ($type) {
            case '1':
                $title='房屋面积';
                break;
            case '2':
                $title='查看图纸';
                break;
            case '3':
                $title='施工进度';
                break;
            case '4':
                $title='调试进度';
                break;
            case '5':
                $title='验收进度';
                break;
        }

        $re = M('pad_order')->where(['id'=>$oid,'uid'=>$_SESSION['userId']])->count();
        if ($re > 0) {
            $list = M('pad_file')->where(['o_id'=>$oid,'types'=>$type])->order('add_time desc')->select();
            foreach ($list as $k => &$v) {
                $v['add_time'] = substr($v['add_time'], 0,10);
                $lists[$v['add_time']][] = $v;
            }
            $this->assign('list',$lists);
        }

        $this->assign('title',$title);
        $this->assign('type',$type);
        $this->assign('oid',$oid);
        $this->display();
    }

}	


