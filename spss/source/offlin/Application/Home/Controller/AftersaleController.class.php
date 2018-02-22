<?php
namespace Home\Controller;
use Think\Controller;

class AftersaleController extends BaseController {

	public function _initialize() {  
		parent::_initialize();
		$this->assign('now','4');
	}
    //售后订单列表
    public function index(){
        $user = M('pad_user')->where(['id'=>$_SESSION['dealerId']])->find();
        
        if ($user['level']==='0' || in_array('47', explode(',', $user['permission']))) {
            $permission = true;
        }
        
        $ids=getCatGrandson($_SESSION['dealerId'],'pad_user'," and c_id={$user['c_id']}");
		$ids[]=$_SESSION['dealerId'];

        $map['o.u_id'] = ['in',$ids];
        //分配人员
        $uids=M('pad_sale_order')->field('id')->where(['uid'=>$_SESSION['dealerId']])->select();
        if(!empty($uids)){
            foreach ($uids as $k => $v) {
                $wheres .= ','.$v['id'];
            }
            $where['o.id'] = ['in',trim($wheres,',')];
            $map['_complex'] = $where;
            $map['_logic'] = 'or';
        }


        $total = M('pad_sale_order o')
                ->join('pad_sale_order_info i on o.id = i.o_id')
                ->where($map)
                ->count('distinct(i.o_id)');

        $page = getAjaxPage($total, 10);

        $list = M('pad_sale_order o')
                ->field('o.id,o.sale_sn,i.name q_name ,i.*,o.*')
                ->join('pad_sale_order_info i on o.id = i.o_id')
                ->group('i.o_id')
                ->where($map)
                ->order('o.add_time desc')
                ->limit($page['limit'])
                ->select();
        foreach ($list as $k=>$v) {
            if($v['uid']!==0){
                $info=M('pad_user')->where(['id'=>$v['uid']])->find();
                $list[$k]['s_name']=$info['real_name'];
                $list[$k]['s_tel']=$info['tel'];
            }
        }
        $this->assign('list',$list);
        $this->assign('permission',$permission);
        $this->assign('page',$page['page']);
        $this->display();
    }

    //售后订单
    public function getOrderList(){
        $data = I('get.data');
        foreach ($data as $k => $v) {
            $list[$v['name']] = $v['value'];
        }

        if (!empty($list['key'])) {
            $map['o.sale_sn|i.name|o.c_name|o.tel'] = ['like','%'.$list['key'].'%'];
        }
        $user = M('pad_user')->where(['id'=>$_SESSION['dealerId']])->find();
        $ids=getCatGrandson($_SESSION['dealerId'],'pad_user'," and c_id={$user['c_id']}");
		$ids[]=$_SESSION['dealerId'];
        $map['o.u_id'] = ['in',$ids];
        //分配人员
        $uids=M('pad_sale_order')->field('id')->where(['uid'=>$_SESSION['dealerId']])->select();
        if(!empty($uids)){
            foreach ($uids as $k => $v) {
                $wheres .= ','.$v['id'];
            }
            $where['o.id'] = ['in',trim($wheres,',')];
            $map['_complex'] = $where;
            $map['_logic'] = 'or';
        }
        /********************************/
        $total = M('pad_sale_order o')
                ->join('pad_sale_order_info i on o.id = i.o_id')
                ->where($map)
                ->count('distinct(i.o_id)');

        $page = getAjaxPage($total, 10);

        $list = M('pad_sale_order o')
                ->field('o.id,o.sale_sn,i.name q_name ,i.*,o.*')
                ->join('pad_sale_order_info i on o.id = i.o_id')
                ->group('i.o_id')
                ->where($map)
                ->order('o.add_time desc')
                ->limit($page['limit'])
                ->select();
        foreach ($list as $k=>$v) {
            if($v['uid']!==0){
                $info=M('pad_user')->where(['id'=>$v['uid']])->find();
                $list[$k]['s_name']=$info['real_name'];
                $list[$k]['s_tel']=$info['tel'];
            }
        }

        
        if ($user['level']==='0' || in_array('47', explode(',', $user['permission']))) {
            $permission = true;
        }
        foreach ($list as $k => $v) {
            $return .= '<p class="sys-item">
                            <span class="item-name">订单编号</span>
                            <span class="ell">'.$v["sale_sn"].'</span>
                        </p>
                        <p class="sys-item">
                            <span class="item-name">客户信息</span>
                            <span class="ell">'.$v["c_name"].'&nbsp;&nbsp;&nbsp;'.$v['tel'].'</span>
                        </p>
                        <p class="sys-item">
                            <span class="item-name">房屋地址</span>
                            <span class="ell">'.$v["province"].'-'.$v["city"].'-'.$v["district"].'-'.$v["address"].'</span>
                        </p>
                        <p class="sys-item">
                            <span class="item-name">订单状态</span>
                            <span class="ell">';
                    if($v['status']==1){
                        $return .= '未分配';
                    }else if($v['status']==2){
                        $return .= '未完成';
                    }else if($v['status']==3){
                        $return .= '已完成';
                    }
                    $return .= '</span></p>';
                    if (empty($permission) && !empty($v['s_name'])){
                        $return .='<p class="sys-item">
                            <span class="item-name">施工人员</span>
                            <span class="ell">'.$v['s_name'].'&nbsp;&nbsp;&nbsp;'.$v['s_tel'].'</span>
                        </p>';
                    }
                    $return .= ' <p class="redbtn-box">
                            <a href="'.U('Aftersale/detail',array('id'=>$v['id'])).'" class="bgred">查看订单</a>';
                    if (!empty($permission)){            
                        $return .= '<a href="javascript:;" onclick="goset('.$v['id'].')">完成订单</a>';
                    }
                    $return .= '</p>
                        <div class="clearfix"></div>';
                }
        $return .=$page['page'].'
                <script>
                    $(".page a").click(function(){
                        var p = $(this).attr("data-page");
                        var data = $("#form1").serializeArray();
                        $.get("'.U("getOrderList").'",{"data":data,"p":p},function(re){
                            $(".order-list").html(re);
                        })
                    })
                </script>
                ';
        echo $return;
    }
        
    //订单详情
    public function detail(){
        $id=I('id');//售后订单id

        $map['o.id']=$id;
        $data=M('pad_sale_order o')
                ->order('o.add_time')
                ->where($map)
                ->find();

        $files=explode('|',trim($data['img'],'|'));
        $images=explode('|',trim($data['image'],'|'));
        //施工人员
        $info = M('pad_user')->where(['id'=>$data['uid']])->find();
        if($data['uid']!=$_SESSION['dealerId']){
            $this->checks($data['u_id']);
        }
        //查询所有系统
        $quoteinfo=M('pad_sale_order_info i')
                ->where(['i.o_id'=>$id])
                ->select();

        $user = M('pad_user')->where(['id'=>$_SESSION['dealerId']])->find();
        
        if ($user['level']==='0' || in_array('46', explode(',', $user['permission']))) {
            $permission = true;
        }
        $this->assign('permission',$permission);
        $this->assign('data',$data);
        $this->assign('info',$info);
        $this->assign('files',$files);
        $this->assign('images',$images);
        $this->assign('quoteinfo',$quoteinfo);
        $this->display();
    }

    //提交反馈信息
    public function getsub(){
        layout(false);
        if($_POST){
            $id=I('id');
            $data['feedback'] = $_POST["feedback"];   //施工反馈
            $data['go_time'] = strtotime($_POST["go_time"]);   //上门日期
            $data['message'] = $_POST["message"];    //备注信息
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
                $data['img']='';
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
                        $data['img'] .= $image['images'].'|';
                        $status=true;
                    }else{
                        $error ='图片上传失败:'.$image['error'];
                        $status=false;
                        return false;
                    }
                }
                $re=M('pad_sale_order')->where(['id'=>$id])->save($data);
                if($status && $re!==false){
                    $this->success('上传成功',U("Aftersale/index"));
                }else{
                    $this->error($error);
                }

            }else{
                $this->error('请选择图片！！');
            }
        }    
    }

    public function goset(){
        $id=I('id');//售后订单id
        $info=M('pad_sale_order ')
                ->field('status')
                ->where(array('id'=>$id))
                ->find();
        if($info['status']==3){
            echo '该订单已完成';
        }elseif($info['status']==1){
            echo '该订单尚未分配，不可提交完成';
        }else{
            if(M('pad_sale_order')->where(['id'=>$id])->save(['status'=>'3'])!=false){
                echo '提交成功';
            }else{
                echo '提交失败';
            }
        }
    }

    //分配施工人员
    public function assigns(){
        $id=I('id');//售后订单id
        $data=getCatGrandson($_SESSION['dealerId'],'pad_user');
		$data[]=$_SESSION['dealerId'];
        $map['pid']=['in',$data];
        $data_=M('pad_user')->where($map)->select();
        $this->assign('data',$data_);
        $this->assign('id',$id);
        $this->display();
    }

    public function doAssigns(){
        layout(false);
        $id=I('id');//订单id
        $uid=I('person');//选中的实施者
        if(M('pad_sale_order')->where(['id'=>$id])->save(['status'=>'2','uid'=>$uid])!==false){
            $this->success('提交成功',U('Aftersale/detail',['id'=>$id]));
        }else{
            $this->error('提交失败',U('Aftersale/assigns',['id'=>$id]));
        }
    }

    //售后登记
    public function afterReg(){
		if($_SESSION['dealerId']!=101 && $_SESSION['dealerId']!=102)
			$this->error('无此权限');
        $area = getPCD();
        $this->assign("area",$area);
        $this->assign("info",$info);
        $this->display();
    }

    //查询省市县三级联动
    public function area(){
        $id = $_GET['v'];
        $return = M('xgj_area')->where("pid=$id")->field('id,name')->select();
        echo json_encode($return);
    }

    public function doAfterReg(){
        layout(false);
        $microtime            = explode('.',microtime(true));//转化时间戳 
        $data=[
            'sale_sn'        =>date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT),
            'c_name'         =>$_POST['c_name'],
            'tel'            =>$_POST['tel'],
            'dealer_company' =>$_POST['dealer_company'],
            'province'       =>getPCDName($_POST['province']),
            'city'           =>getPCDName($_POST['city']),
            'district'       =>getPCDName($_POST['district']),
            'address'        =>$_POST['address'],
            'content'        =>$_POST['content'],            
            'yy_time'        =>strtotime($_POST['yy_time']),            
            'u_id'           =>$_SESSION['dealerId'],            
        ];
        $re=M('pad_sale_order')->add($data);
        if($re){
            for ($i=0; $i < count($_POST['name']); ++$i) {
                $info[]=[
                    'o_id'     =>$re,
                    'name'     =>$_POST['name'][$i],
                    'model'    =>$_POST['model'][$i],
                    'serial'   =>$_POST['serial'][$i],
                    'buy_time' =>strtotime($_POST['buy_time'][$i]),
                    'types'    =>"{$_POST['types'][$i]}",
                ];
            }
            if(M('pad_sale_order_info')->addAll($info)){
                $this->success('提交成功',U('Aftersale/index'));
            }else{
                $this->error('提交失败');
            }
        }else{
            $this->error('提交失败');
        }
    }

    public function rpc(){
        $queryString = I('queryString');
        $map['name']=['like',"%".$queryString."%"]; 
        $info=M('pad_company')->field('name')->where($map)->select();
        $html='';
        if(!empty($info)){
            foreach ($info as $k => $v) {
                $html.='<li class="ell" onclick="fill(\''.$v['name'].'\');">'.$v['name'].'</li>';
            }
        }
        echo $html;
    }

    public function checks($u_id){
        // $info_ = M('pad_user')->where(['id'=>$u_id])->find();
        // if ($u_id != $_SESSION['dealerId']) {
        //     $ids = M('pad_user')->field('id')->where(['c_id'=>$info_['c_id'],'pid'=>['lt',$u_id]])->select();
        //     foreach ($ids as $k => $v) {
        //         if ($v['id'] == $_SESSION['dealerId']) {
        //             $error = true;
        //         }
        //     }
        //     if ($error !== true) {
        //         layout(false);
        //         $this->error('暂无权限可以查看!');
        //     }
        // }

        // if($error != true){
        //     if($u_id != $_SESSION['dealerId']){
        //         $info=M('pad_workplan')->field('uid')->where(['o_id'=>$data['order_id']])->select();
        //         foreach ($info as $k => $v) {
        //           $res[]=$v['uid'];
        //         }
        //         if(in_array($_SESSION['dealerId'],$res))
        //             $error = true;
        //     }
        // }
        $level=M('pad_user')->where(['id'=>$_SESSION['dealerId']])->getField('level');      
        if($level==0)
            $error = true;
        if ($u_id != $_SESSION['dealerId']) {
            $ids=getCatGrandson($_SESSION['dealerId'],'pad_user');
            if(in_array($u_id,$ids))
                $error = true;           
        }else{
            $error = true;
        }
        if($error !=true)
            $this->error('暂无权限可以查看!');
    }

    
}	


