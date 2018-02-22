<?php
namespace Admin\Controller\Overseas;
use \Admin\Controller\Index\AdminController;

/**
 * 后台订单控制器
 */
class OrderController extends AdminController{
    private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Admin\Model\Overseas\OrderModel;
    }


    /*
    订单列表
     */
    public function index(){
        $where = $where1 = '';
        if($_POST['reset']==1){
            $where = $where1 = '1=1';
        }else{
            if (!empty($_POST)) {
                $_GET['p']    = 1;
                $sn           = I('post.sn');
                $keyword      = I('post.keyword');
                $userName     = I('post.userName');
                $shr_name     = I('post.shr_name');
                $phone        = I('post.phone');
                $express_sn   = I('post.express_sn');
                $order_status = I('order_status') != '' ?I('order_status').'a' : false;
                $starttime    = I('post.starttime');
                $endtime      = I('post.endtime');
                $startprice   = I('post.startprice');
                $endprice     = I('post.endprice');
                if (!empty($sn))          $where .= " o.sn like '%".$sn."%'";
                if (!empty($userName))    $where .= " and u.user_name like '%".$userName."%'";
                if (!empty($shr_name))    $where .= " and  o.shr_name like '%".$shr_name."%'";
                if (!empty($phone))       $where .= " and u.mobile_phone = $phone";
                if (!empty($express_sn))  $where .= " and e.express_sn = $express_sn";
                if (!empty($starttime))   $where .= " and o.add_time >= '{$starttime}'";
                if (!empty($endtime))     $where .= " and o.add_time <= '{$endtime}'";
                if (!empty($startprice))  $where .= " and o.total_price >= '{$startprice}'";
                if (!empty($endprice))    $where .= " and o.total_price <= '{$endprice}'";
                if($order_status){
                    $order_status=rtrim($order_status,'a');

                    if($order_status=='cf'){
                        $numArr=M('xgj_ov_split_order')->getField('order_id',true);
                        // 统计一个数组中相同元素的个数 
                        $unique_arr = array_count_values( $numArr ); 
                        $orderid='';
                        foreach($unique_arr as $k=>$v){
                            if($v>1){
                               $orderid.=$k.','; 
                            }
                        }
                        if(!empty($orderid)){
                            $orderids=rtrim($orderid,',');
                            $where .=" and o.id in ($orderids)";
                        }else{
                            echo "<script>alert('您所搜索的状态结果为空！');window.history.go(-1)</script>";exit;
                        }
                    }else{
                        $where .= " and o.order_status = '{$order_status}'" ;
                    }
                }
                if (!empty($keyword))     $where1 .= " goods_title like '%".$keyword."%' or goods_sn like '%".$keyword."%'";
            }

            if(!empty($where1)){
                $id=M('xgj_ov_goods')->field('id')->where($where1)->select();
                $idss=$orderid='';
                foreach($id as $k=>$v){
                    $idss.=$v['id'].',';
                }
                if(!empty($id)){
                    $ids=rtrim($idss,',');
                    $info=M('xgj_ov_order_goods')->field('order_id')->where("goods_id in ($ids)")->group('order_id')->select();
                    if(!empty($info)){
                        foreach($info as $k=>$v){
                            $orderid.=$v['order_id'].',';
                        }
                        $orderids=rtrim($orderid,',');
                        if(!empty($orderids)){
                            $where .=" and o.id in ($orderids)";
                        }
                    }else{
                        echo "<script>alert('您所搜索的关键字结果为空！');window.history.go(-1)</script>";exit;
                    }
                }else{
                    echo "<script>alert('您所搜索的关键字结果为空！');window.history.go(-1)</script>";exit;
                }   
            }
            if(!empty($where)){
                $where = ltrim($where,' and');
                $_SESSION['ovOrderWhere'] = $where;
            } 
            if (!empty($_GET['p']) && $_GET['p']!=1) $where = $_SESSION['ovOrderWhere'];
            else unset($_SESSION['ovOrderWhere']);
        }

        $total = count(M('xgj_ov_order o')->join('xgj_users u on o.user_id=u.user_id')->join('xgj_ov_split_order e on o.id=e.order_id')->where($where)->group('o.sn')->select());

        $total_price_s = M('xgj_ov_order o')->field('o.total_price')->join('xgj_users u on o.user_id=u.user_id')->join('xgj_ov_split_order e on o.id=e.order_id')->where($where)->group('o.sn')->select();
        $aa=$bb=0;
        foreach($total_price_s as $k=>$v){
            $aa+=$v['total_price'];
        }
        $deal_price_s = M('xgj_ov_order o')->field('o.deal_price')->join('xgj_users u on o.user_id=u.user_id')->join('xgj_ov_split_order e on o.id=e.order_id')->where($where)->group('o.sn')->select();
        foreach($deal_price_s as $k=>$v){
            $bb+=$v['deal_price'];
        }
        $price['total_price_s']=$aa;
        $price['deal_price_s']=$bb;
        $price['total_price'] = M('xgj_ov_order')->sum('total_price');
        $price['deal_price'] = M('xgj_ov_order')->sum('deal_price');
        $page  = getPage($total, C('MANAGER_PAGE_SIZE'));

        $data  = M('xgj_ov_order o')->field('o.*,u.user_name')->join('xgj_users u on o.user_id=u.user_id')->join('xgj_ov_split_order e on o.id=e.order_id')->where($where)->group('o.sn')->order('o.add_time DESC')->limit($page['limit'])->select();

        foreach ($data as $k => $v) {
            $splitOrderCount = M('xgj_ov_split_order')->where("order_id='{$v['id']}'")->count();
            $data[$k]['num'] = $splitOrderCount;
            $goodsList[$v['id']] = M('xgj_ov_order_goods og')->join('xgj_ov_goods g on og.goods_id=g.id')->join('xgj_ov_dealer d on g.d_id=d.id')->field('identifier')->where("order_id='{$v['id']}'")->select();
        }

        $data1  = M('xgj_ov_order o')->field('o.*,u.user_name')->join('xgj_users u on o.user_id=u.user_id')->join('xgj_ov_split_order e on o.id=e.order_id')->where($where)->group('o.sn')->order('o.add_time DESC')->select();

        foreach ($data1 as $k => $v) {
            $splitOrderCount1 = M('xgj_ov_split_order')->where("order_id='{$v['id']}'")->count();
            $data1[$k]['num'] = $splitOrderCount1;
            $goodsList1[$v['id']] = M('xgj_ov_order_goods og')->join('xgj_ov_goods g on og.goods_id=g.id')->join('xgj_ov_dealer d on g.d_id=d.id')->field('identifier')->where("order_id='{$v['id']}'")->select();
        }
        if($_POST['exl']==1){
            foreach ($data1 as $k => $v) {
                $e = '';
                foreach ($goodsList1[$v['id']] as $key => $value) {
                    $e .= $value['identifier'].'，';
                }
                $list['data'][$k]=[$data1[$k]['is_return']==2?"退货":"",'’'.$data1[$k]['sn'],$e,$data1[$k]['add_time'],$data1[$k]['shr_name'],$data1[$k]['user_name'],$data1[$k]['total_price'],$data1[$k]['deal_price'],floor($data1[$k]['deal_price']*0.05)>0?'+'.floor($data1[$k]['deal_price']*0.05):'0',$data1[$k]['num']>1?'拆分订单':$data1[$k]['is_return']==2?eu_order_status(3):eu_order_status($data1[$k]['order_status']),];
            }

            $list['key']=['是否退货','订单号','供应商识别号','下单时间','下单人','收货人','订单金额','应付金额','积分','状态'];
            $list['data'][]=['合计','订单金额：￥'.$price['total_price_s'] ,'应付金额：￥'.$price['deal_price_s'],'','','','','','总计订单金额：￥'.$price['total_price'] ,'总计应付金额：￥'.$price['deal_price']];
            $list['width']=['B'=>'10','C'=>'30','D'=>'25','E'=>'20','F'=>'20','G'=>'20','J'=>'30','K'=>'30'];
            exl($list);
        }
        $this->assign('order_list', $data);
        $this->assign('goodsList', $goodsList);
        $this->assign('price', $price);
        $this->assign('page', $page['page']);
        $this->display();
    }

    /*
    订单详情
    */
    public function info(){
        $orderId = I('get.id');
        $orderList = M('xgj_ov_order o')->join('xgj_users u on o.user_id=u.user_id')->where("id=$orderId")->find();
        $pay_sn=$orderList['sn']."08";
        if($orderList['pay_method']!=1){
            $orderList['pay_code']=M('xgj_chinapay')->where("order_id=$pay_sn")->getField('trade_no');
        }else{
            $orderList['pay_code']=M('xgj_eu_payment')->where("order_id=$pay_sn")->getField('trade_no');
        }
        $splitOrderList = M('xgj_ov_split_order o')->join('xgj_ov_order_goods g on o.detail_id=g.id')->join('xgj_ov_goods n on g.goods_id=n.id')->field('o.*,g.*,n.goods_sn,n.is_putaway,n.d_id,o.id id')->where("o.order_id=$orderId")->select();
        foreach ($splitOrderList as $key => $val) {
            $express = M('xgj_ov_express')->where("split_order_id = '{$val['id']}'")->find();
            $splitOrderList[$key]['time']         = $express['time'];
            $splitOrderList[$key]['express_name'] = $express['express_name'];
            $splitOrderList[$key]['express_code'] = $express['express_code'];

            $identifier = M('xgj_ov_dealer')->where("id = '{$val['d_id']}'")->find();
            $splitOrderList[$key]['identifier'] = $identifier['identifier'];
        }

        $integralList = M('xgj_user_integral')->where("`order`=$orderId and status='1' and class='3'")->select();
        $integral = 0;
        foreach ($integralList as $k => $v) {
            $integral += $v['integral'];
        }

        $couponList = M('xgj_coupon_info')->where("order_id=$orderId and class_id='3'")->select();
        $coupon = 0;
        foreach ($couponList as $k => $v) {
            $coupon += $v['use_money'];
        }

        $remark=M('xgj_remarks')->where("order_id=$orderId")->select();
        foreach($remark as $k=>$v){
            $remarks[]=$v['split_id'];
        }
        $this->assign('remarks',$remarks);
        $this->assign('coupon',$coupon);
        $this->assign('integral',$integral);
        $this->assign('info',$orderList);
        $this->assign('list',$splitOrderList);
        $this->assign('id',$orderId);
        $this->display();
    }

    //修改超市页面
    public function orderEdit(){
        $orderId = I('get.id');
        $data = M('xgj_ov_order')->where("id=$orderId")->find();

        $integralList = M('xgj_user_integral')->where("`order`=$orderId and status='1' and class='3'")->select();
        $integral = 0;
        foreach ($integralList as $k => $v) {
            $integral += $v['integral'];
        }

        $couponList = M('xgj_coupon_info')->where("order_id=$orderId and class_id='3'")->select();
        $coupon = 0;
        foreach ($couponList as $k => $v) {
            $coupon += $v['use_money'];
        }

        $journal = M('xgj_journal')->where("order_id=$orderId and class_id='2'")->select();

        $this->assign('journal',$journal);
        $this->assign('orderId',$orderId);
        $this->assign('coupon',$coupon);
        $this->assign('integral',$integral);
        $this->assign('data',$data);
        $this->display();
    }

    //确认修改超市订单
    public function doOrderEdit(){

        $orderId = I('post.orderId');

        if(!preg_match("/^1[34578]\d{9}$/", $_POST['shr_phone'])){
            $this->error('手机号码不正确',U('orderEdit',array('id'=>$orderId)));
        }

        $_POST['shr_pro']  = I('post.cho_Province');
        $_POST['shr_city'] = I('post.cho_City');
        $_POST['shr_area'] = I('post.cho_Area');
        
        $data = M('xgj_ov_order')->create();
        $oldData = M('xgj_ov_order')->where("id=$orderId")->find();

        $save = M('xgj_ov_order')->where("id=$orderId")->save($data);
        if (!empty($save)) {
            $text = '';
            if ($data['shr_name']!=$oldData['shr_name'])     $text .= '订单收货人修改为'.$data['shr_name'].',';
            if ($data['shr_phone']!=$oldData['shr_phone'])   $text .= '订单手机修改为'.$data['shr_phone'].',';
            if ($data['deal_price']!=$oldData['deal_price']) $text .= '订单金额修改为'.$data['deal_price'].',';
            if ($data['remark']!=$oldData['remark'])         $text .= '订单备注修改为'.$data['remark'].',';

            if ($data['shr_pro']!=$oldData['shr_pro'] || $data['shr_city']!=$oldData['shr_city'] || $data['shr_area']!=$oldData['shr_area'] || $data['shr_addr']!=$oldData['shr_addr'])    $text .= '订单收货地址修改为'.$data['shr_pro'].'-'.$data['shr_city'].'-'.$data['shr_area'].'-'.$data['shr_addr'];
            
            $addData = array(
                'class_id'=>'2',
                'order_id'=>$orderId,
                'name'=>$_SESSION['admin_user']['user_name'],
                'time'=>time(),
                'text'=>rtrim($text,',')
                );
            $add = M('xgj_journal')->add($addData);
            if (!empty($add)) $this->success('操作成功',U('index'));
            else $this->error('添加日志失败',U('orderEdit',array('id'=>$orderId)));
        }else{
            $this->error('修改订单失败',U('orderEdit',array('id'=>$orderId)));
        }
    }
    
    /*
    提交OMS
    */
    public function oms(){
        $id=I('id/d');
        $info=$this->m->getOne($id);
        $data=OMS($info,'C');
        if($data->flag==1){
            $orderId = M('xgj_oms_batch')->where("order_id={$info['info']['id']} and class='3'")->find();
            if(empty($orderId)){
                $arr['order_id']=$info['info']['id'];
                $arr['class']=3;
                $arr['time']=time();
                $arr['name']=$_SESSION['admin_user']['user_name'];
                $rs = M('xgj_oms_batch')->add($arr);
                if($rs){
                    $this->success('生成OMS成功');
                }else{
                    $this->success('生成OMS成功,插入表失败');
                }
            }else{
                $this->error('已生成OMS订单，请勿再次生成');
            }
        }elseif($data->flag==2){
            $this->error('此订单未付款无法生成OMS订单');
        }else{
            $this->error('生成OMS失败');
        }
    }


        //海外超市评价
    public function supermarket(){
        $where = '';
        if (!empty($_POST)) {
            $_GET['p'] = 1;
            $sn        = I('post.sn');
            $display   = I('post.display');
            $star      = I('post.star');
            $starttime = I('post.starttime');
            $endtime   = I('post.endtime');

            if (!empty($sn))        $where .= "g.goods_title like '%".$sn."%'";
            if (!empty($display))   $where .= " and c.display = '{$display}'";
            if (!empty($star))      $where .= " and c.star = '{$star}'";
            if (!empty($starttime)) $where .= " and o.pay_time >= '{$starttime}'";
            if (!empty($endtime))   $where .= " and o.pay_time <= '{$endtime}'";
        }
        
        if(!empty($where)){
            $where = ltrim($where,' and');
            $_SESSION['buildingWhere'] = $where;
        } 

        if (!empty($_GET['p'])) $where = $_SESSION['buildingWhere'];
        else unset($_SESSION['buildingWhere']);

        //分页
        $total = M('xgj_ov_comment c')->join('xgj_ov_order_goods g ON c.order_goods_id = g.id')->join('xgj_ov_order o ON g.order_id = o.id')->where($where)->count();

        $page  = getPage($total,C('MANAGER_PAGE_SIZE'));

        $list  = M('xgj_ov_comment c')->join('xgj_ov_order_goods g ON c.order_goods_id = g.id')->join('xgj_ov_order o ON g.order_id = o.id')->where($where)->limit($page['limit'])->order('o.pay_time')->select();

        //模板传值
        $this->assign("page",$page['page']);
        $this->assign('list',$list);
        $this->display();
    }

    //发货
    public function fahuo(){
        $id = I('post.infoid');

        if (empty($_POST['express_name'])) {
            $this->error('请填写快递公司',U('info',array('id'=>$id)));exit;
        }

        if (empty($_POST['express_code'])) {
            $this->error('请填写快递单号',U('info',array('id'=>$id)));exit;
        }

        $_POST['time']           = time();
        $_POST['user_name']      = $_SESSION['admin_user']['user_name'];
        $_POST['split_order_id'] = $id;

        $orderId = M('xgj_ov_split_order')->where("id=$id")->find();
        
        $tableName = M('xgj_ov_express');
        $data = $tableName->create();

        if (!empty($data)){
            if ($tableName->add($data)){
                $status['order_status'] = 2;
                if (M('xgj_ov_split_order')->where("id=$id")->save($status)) {
                    $all = M('xgj_ov_split_order')->where("order_id='{$orderId['order_id']}' and order_status='1'")->select();
                    if (empty($all)) $orderSave = M('xgj_ov_order')->where("id='{$orderId['order_id']}'")->save($status);
                    $this->success('操作成功',U('info',array('id'=>$orderId['order_id'])));exit;
                }
            }
        }

        $this->error('操作失败',U('info',array('id'=>$orderId['order_id'])));
    }
    //退货
    function tuihuo(){
        $id = I('post.split_id');
        $order_id=I('post.order_id');
        $info['return_time']           = time();
        $info['admin_id']      = $_SESSION['admin_user']['user_id'];
        $info['admin_name']      = $_SESSION['admin_user']['user_name'];
        $info['split_id'] = $id;
        $info['class']=2;
        $info['content']=$_POST['content'];
        $info['order_id']=$order_id;
        $data['is_return']=2;
        $status['order_status'] = 3;
        M()->startTrans();
        if(M('xgj_remarks')->add($info)!==false && M('xgj_ov_order')->where("id=$order_id")->save($data)!==false && M('xgj_ov_split_order')->where("id=$id")->save($status)!==false){
            M()->commit();
            $this->success('操作成功',U('info',array('id'=>$order_id)));exit;
        }else{
            M()->rollback();
            $this->error('操作失败',U('info',array('id'=>$order_id)));
        }
    }

    //查看备注
    function checkinfo(){
        $id = I('get.id');
        $remark=M('xgj_remarks')->where("split_id=$id")->order('return_time desc')->find();
        $remark['time']=date("Y-m-d H:i:s", $remark['return_time']);
        echo json_encode($remark);
    }
    //查看海外超市评价详情
    public function supermarketDetails(){
        $id = I('get.id');
        $list = M('xgj_ov_comment c')->join('xgj_ov_order_goods g ON c.order_goods_id = g.id')->join('xgj_ov_order o ON g.order_id = o.id')->where("comment_id=$id")->find();

        $img = explode('|', $list['images']);

        $this->assign('img',$img);
        $this->assign('list',$list);
        $this->display();
    }

    //欧洲建材评价显示或隐藏
    public function supermarketDisplay(){
        $id = I('get.id');
        $dis = I('get.display');
        if ($dis==1) $num = '0';
        else $num = '1';
        $data = array('display'=>$num);
        $list = M('xgj_ov_comment')->where("comment_id=$id")->save($data);
        echo $list;

    }

    //删除海外超市评价
    public function supermarketDelete(){
        $id = I('get.id');
        $p  = I('get.p');
        if (M('xgj_ov_comment')->where("comment_id=$id")->delete()) {
            $this->success('删除成功',U('supermarket',array('p'=>$p)));exit;
        }else{
            $this->error('删除失败',U('supermarket',array('p'=>$p)));exit;
        }

    }
}