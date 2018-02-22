<?php
namespace Admin\Controller\Dealer;
use \Admin\Controller\Index\AdminController;

/**
 * 后台服务商控制器
 */
class OrderController extends AdminController{

    public function index(){
        $tab=isset($_GET['tab'])?intval($_GET['tab']):'';
        $tab_child=isset($_GET['tab_child'])?intval($_GET['tab_child']):'';
    	$model = new \Admin\Model\Dealer\OrderModel;
    	$new_order_data=$model->new_order_list();
    	$show_statistics_data=$model->show_statistics_list();
    	$show_refund_data=$model->show_refund_list();
    	$show_add_data=$model->show_add_list();
    	$show_self_buy_data=$model->show_self_buy_list();
        $show_my_data=$model->show_my_list();
        //新订单
        $this->assign('new_order_list',$new_order_data['new_order_list']);
        $this->assign('new_order_page',$new_order_data['new_order_page']);
        //订单统计
        $this->assign('show_statistics_list',$show_statistics_data['show_statistics_list']);
        $this->assign('show_statistics_total',$show_statistics_data['show_statistics_total']);
        $this->assign('show_statistics_page',$show_statistics_data['show_statistics_page']);
        $this->assign('show_statistics_price',$show_statistics_data['show_statistics_price']);
        //退货订单
        $this->assign('show_refund_list',$show_refund_data['show_refund_list']);
        $this->assign('show_refund_total',$show_refund_data['show_refund_total'][0]['total']);
        $this->assign('show_refund_page',$show_refund_data['show_refund_page']);
        $this->assign('show_refund_price',$show_refund_data['show_refund_price'][0]['price']);
        //补货订单
        $this->assign('show_add_list',$show_add_data['show_add_list']);
        $this->assign('show_add_total',$show_add_data['show_add_total'][0]['total']);
        $this->assign('show_add_page',$show_add_data['show_add_page']);
        $this->assign('show_add_price',$show_add_data['show_add_price'][0]['price']);
        //自购订单
        $this->assign('show_self_buy_list',$show_self_buy_data['show_self_buy_list']);
        $this->assign('show_self_buy_total',$show_self_buy_data['show_self_buy_total'][0]['total']);
        $this->assign('show_self_buy_page',$show_self_buy_data['show_self_buy_page']);
        $this->assign('show_self_buy_price',$show_self_buy_data['show_self_buy_price'][0]['price']);
        //我的订单
        $this->assign('show_my_list',$show_my_data['show_my_list']);
        $this->assign('show_my_total',$show_my_data['show_my_total']);
        $this->assign('show_my_page',$show_my_data['show_my_page']);
        $this->assign('show_my_price',$show_my_data['show_my_price']);
        //页面tab
        $this->assign('tab',$tab);
        $this->assign('tab_child',$tab_child);

        $this->display();
    }

    /**
     * 分配订单给服务商
     */
    public function allot(){
        //实例化服务商订单model类
        $dealer_orderOb=new \Admin\Model\Dealer\OrderModel;
        //接收传值
        $order_id=intval($_GET['order_id']);
        if (!IS_POST){
            //查找服务商订单信息
            $dealer_order_info=$dealer_orderOb->dealer_order_id($order_id);
            //var_dump($dealer_order_info);exit;
            //根据省份及市区查询服务商信息
            $dealer_list=$dealer_orderOb->get_dealer_list($dealer_order_info['province'], $dealer_order_info['city']);
            //查找服务商信息
            $dealer_info=$dealer_orderOb->get_dealer_info();

            //模板传值
            $this->assign('permission',$permission);
            $this->assign('dealer_info',$dealer_info);
            $this->assign('dealer_list',$dealer_list);
            $this->assign('dealer_order_info',$dealer_order_info);  
            $this->assign('order_id',$order_id);
            //显示模板
            $this->display();
        }else{

            if (!empty($_POST)){
                if (isset($_POST['d_id'])){
                    //获取值
                    $d_id=intval($_POST['d_id']);
                    $rs=$dealer_orderOb->where("order_id={$order_id}")->setField(array('d_id'=>$d_id,'allot_status'=>1));
                    if ($rs){
                        $this->success('分配成功，正在跳转...',U('index',array('tab'=>1)));
                        die;
                    }else{
                        $this->error('分配失败，正在跳转...');
                        die;
                    }

                }else if(isset($_POST['d_province']) || isset($_POST['d_city'])){
                    //获取值
                    $d_province=empty($_POST['d_province'])?'':trim($_POST['d_province']);
                    $d_city=empty($_POST['d_city'])?'':trim($_POST['d_city']);
                    //查找服务商订单信息
                    $dealer_order_info=$dealer_orderOb->dealer_order_id($order_id);
                    //查找服务商信息
                    $dealer_info=$dealer_orderOb->get_dealer_info();
                    //根据省份及市区查询服务商信息
                    $dealer_list=$dealer_orderOb->get_dealer_list($d_province, $d_city);
                    //模板传值
                    $this->assign('permission',$permission);
                    $this->assign('dealer_info',$dealer_info);
                    $this->assign('dealer_list',$dealer_list);
                    $this->assign('d_province',$d_province);
                    $this->assign('d_city',$d_city);
                    $this->assign('dealer_order_info',$dealer_order_info);
                    $this->assign('order_id',$order_id);
                    //显示模板
                    $this->display();
                }   
            }else{
                $this->error('分配失败，请选择要分配的服务商，正在跳转...');
                        die;
            }
        }
    }

    /**
     * 健康舒适家居订单详情
     */
    public function info(){
        //接收传值
        $order_id=intval($_GET['order_id']);
        //实例化服务商订单model类
        $dealer_orderOb=new \Admin\Model\Dealer\OrderModel;
        $dealer_orderOb->get_dealer_order_price($order_id);
        //订单详情的一条记录
        $dealer_order_info=$dealer_orderOb->get_dealer_order_info($order_id);
        foreach ($dealer_order_info as $k => $v) {
           $dealer_order_info[$k]['img']=getImage($v['face_image'],54,54);
        }
        //var_dump($dealer_order_info);exit;
        if($dealer_order_info[0]['order_status']==5){
            //调整订单的一条信息
            $dealer_order_adjust_info=$dealer_orderOb->get_order_adjust_info($order_id);

            //经销商调整订单的一条信息
            $dealer_adjust_info=$dealer_orderOb->get_dealer_adjust_info($dealer_order_info[0]['order_code']);
        }else{
           //调整订单的一条信息
            $dealer_order_adjust_info=array();
            //经销商调整订单的一条信息
            $dealer_adjust_info=array(); 
        }
        
        //var_dump($dealer_adjust_info);exit;
        //订单详情的已付金额
        $dealer_order_price=$dealer_orderOb->get_dealer_order_price($order_id);
        if($dealer_order_info[0]['pay_status']==1 || $dealer_order_info[0]['pay_status']==4){
            $price=$dealer_order_price;
        }else if($dealer_order_info[0]['pay_status']==2){
            $price=500;
        }else if($dealer_order_info[0]['pay_status']==3){
            $price=$dealer_order_price*0.5+500;
        }else{
            $price='0';
        }
        $detail_id=empty($_GET['detail_id'])?$dealer_order_info[0]['detail_id']:intval($_GET['detail_id']);
        if(!empty($detail_id)){
            //获取施工计划信息
            $construct_list=$dealer_orderOb->get_dealer_order_construct($detail_id);
            //获取上传文件信息
            $file_list=$dealer_orderOb->get_file($order_id);
            //获取产品手册的文件
            $manual_list=$dealer_orderOb->get_file($order_id,2);
        }else{
            $construct_list=array();
        }
		$message=M('xgj_furnish_order_refund')->where("order_id='".$order_id."' and (first_audit_status=1 or review_audit_status=1) ")->group('refund_status')->select();
        //var_dump('<pre>',$message);exit;
        //模板传值
		$this->assign("message",$message);
        $this->assign("dealer_order_info",$dealer_order_info);
        $this->assign("dealer_order_adjust_info",$dealer_order_adjust_info);
        $this->assign("dealer_adjust_info",$dealer_adjust_info);
        $this->assign("construct_list",$construct_list);
        $this->assign("file_list",$file_list);
        $this->assign("manual_list",$manual_list);
        $this->assign("detail_id",$detail_id);
        $this->assign("order_id",$order_id);
        $this->assign("price",$price);
        $this->assign("price_all",$dealer_order_price);
        //显示模板
        $this->display();
    }

    
    /**
     * 健康舒适家居材料清单页面
     */
    function stuff_list(){
        //var_dump($_SERVER['DOCUMENT_ROOT']);exit;
        //接收传值
        $detail_id=intval($_GET['detail_id']);
        $order_id=intval($_GET['order_id']);
        //$quote_name=trim($_GET['quote_name']);
        $title=$_GET['goods_lv']==1?'主材清单':'辅材清单';

        if (empty($_GET['batch'])) $batch = 1;
        else if($_GET['batch']==1 || $_GET['batch']==2 || $_GET['batch']==3) $batch = $_GET['batch']-1;
        else $batch = 1;
  
        $stuff_id=$_GET['goods_lv']==1?1:2;
        //实例化服务商订单model类
        $dealer_orderOb=new \Admin\Model\Dealer\OrderModel;
        //订单详情的一条记录
        $dealer_order_list=$dealer_orderOb->get_dealer_order_info($order_id);
        //系统名称
        $quote =M('xgj_furnish_order_detail')->where("detail_id = $detail_id")->field('quote_name,quote_id,level')->find();

        $quote_name = $quote['quote_name'];
        //材料清单列表
        $dealer_order_stuff_list=$dealer_orderOb->get_dealer_order_stuff_list($detail_id,$stuff_id,$quote,$batch);

        if (!empty($_GET['xls']) && $_GET['xls']=='1') {
            $this->exl($dealer_order_stuff_list,$quote_name);
        }
        
        //退货清单
        $refund_list=$dealer_orderOb->get_dealer_order_refund_list($detail_id,$stuff_id,1);
        //换货清单
        $add_list=$dealer_orderOb->get_dealer_order_refund_list($detail_id,$stuff_id,2);
        //自购清单
        $selfbuy_list=$dealer_orderOb->get_dealer_order_refund_list($detail_id,$stuff_id,3);
        //var_export($dealer_order_list);exit;
        $refund_price='';
        foreach ($refund_list as $v){
            if (!empty($v['list'])){
                $refund_price+=$v['num']*$v['list'][0]['shop_price'];
            }
        }
        $add_price='';
        foreach ($add_list as $v){
            if (!empty($v['list'])){
                $add_price+=$v['num']*$v['list'][0]['shop_price'];
            }
        }
        $selfbuy_price='';
        foreach ($selfbuy_list as $v){
            if (!empty($v['list'])){
                $selfbuy_price+=$v['num']*$v['list'][0]['shop_price'];
            }
        }

        foreach ($add_list as $key=>$val){
            foreach ($dealer_order_stuff_list as $k=>$v){
                if(!empty($v['list'])){
                    if ($add_list[$key]['goods_sn'] == $dealer_order_stuff_list[$k]['goods_sn']){
                        $dealer_order_stuff_list[$k]['adjust_num']=$val['num']+$v['num'];
                    }
                }
            }
        }
        foreach ($selfbuy_list as $key=>$val){
            foreach ($dealer_order_stuff_list as $k=>$v){
                if(!empty($v['list'])){
                    if ($selfbuy_list[$key]['goods_sn'] == $dealer_order_stuff_list[$k]['goods_sn']){
                        $dealer_order_stuff_list[$k]['outbound']="自购";
                    }
                }
            }
        }


        //var_dump($dealer_order_stuff_list);
        //模板传值
        $this->assign("dealer_order_stuff_list",$dealer_order_stuff_list);
        $this->assign("dealer_order_list",$dealer_order_list);
        $this->assign("refund_list",$refund_list);
        $this->assign("add_list",$add_list);
        $this->assign("selfbuy_list",$selfbuy_list);
        $this->assign("refund_price",$refund_price);
        $this->assign("add_price",$add_price);
        $this->assign("selfbuy_price",$selfbuy_price);
        $this->assign("title",$title);
        $this->assign("quote_name",$quote_name);
        $this->assign("detail_id",$_GET['detail_id']);
        $this->assign("order_id",$_GET['order_id']);
        $this->assign("goods_lv",$_GET['goods_lv']);
        //显示模板
        $this->display();
    }

    public function exl($list,$quote_name){
        //导入PHPExcel类库
        //相当于引入了vendor目录下面PHPExcel\PHPExcel.php
        vendor('Excel.PHPExcel');
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', $quote_name)
            // ->setCellValue('A2', '类名')
            ->setCellValue('B2', 'ID')
            ->setCellValue('C2', '材料名称')
            ->setCellValue('D2', '产品编码')
            ->setCellValue('E2', '型号')
            ->setCellValue('F2', '品牌/产地')
            ->setCellValue('G2', '批次')
            ->setCellValue('H2', '单位')
            ->setCellValue('I2', '数量')
            ->setCellValue('J2', '单价')
            ->setCellValue('K2', '总价');

        foreach ($list as $k => $v) {
            if (!empty($v['list'])) {
                $data[] = $v;
            }
        }

        foreach($data as $k=>$v){
            $k = $k+3;
            $objPHPExcel->setActiveSheetIndex(0)
            // ->setCellValue("A".$k, empty($v['text'])?'':$v['text'])
            ->setCellValue("B".$k, $k-2)
            ->setCellValue("C".$k, $v['list']['0']['goods_name'])
            ->setCellValue("D".$k, "'".$v['goods_sn'])
            ->setCellValue("E".$k, $v['list']['0']['goods_model'])
            ->setCellValue("F".$k, $v['list']['0']['goods_brand'])
            ->setCellValue("G".$k, !empty($v['batch']) || $v['batch']=='0'?$v['batch']+1:'抱歉！找不到此材料批次')
            ->setCellValue("H".$k, $v['list']['0']['goods_unit'])
            ->setCellValue("I".$k, $v['num']) 
            ->setCellValue("J".$k, $v['list']['0']['shop_price'])
            ->setCellValue("K".$k, $v['list']['0']['shop_price']*$v['num']);
        
        }
        $objPHPExcel->getActiveSheet()->setTitle("$quote_name"); 
        $objPHPExcel->setActiveSheetIndex(0);   
        ob_end_clean() ;
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='."材料清单表".'.xls');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    
    /**
     * 退换货伙伴自购材料审核
     */
    function audit(){
        //接收传值
        $refund_id=intval($_GET['refund_id']);
        $first_audit_status=intval($_GET['first_audit_status']);
        //实例化服务商订单model类
        $dealer_orderOb=new \Admin\Model\Dealer\OrderModel;
        //查询当前的材料申请状态
        $status=$dealer_orderOb->get_first_audit_status($refund_id);
        //var_dump($status);
        if ($status==2 || $status==3){
            echo 'already';
        }else{
            //修改材料申请状态
            $rs=M('xgj_furnish_order_refund')->where("refund_id=$refund_id")->setField(array("first_audit_status"=>$first_audit_status));
            //判断并返回值
            if($rs){
                echo 'success';
            }else{
                echo 'lose';
            }
        }
    }
    
    
    /**
     * 质量审核 （跟单人审核，会改变订单的进度，以及给经销商结算一笔钱）
     */
    function check(){
        //接收传值
        $order_id=intval($_GET['order_id']);
        $detail_id=intval($_GET['detail_id']);
        $task_work=intval($_GET['task_work']);
        $construct_id=intval($_GET['construct_id']);
        //实例化服务商订单model类
        $dealer_orderOb=new \Admin\Model\Dealer\OrderModel;
        $status=$dealer_orderOb->check_construct_id($task_work, $order_id);
        if ($status){
            if ($task_work==1)$quote_status=1;
            elseif ($task_work==2)$quote_status=2;
            elseif ($task_work==3)$quote_status=4;
            elseif ($task_work==4)$quote_status=6;
            //开启事务
            M()->startTrans();
            //进行质量审核
            $re=M('xgj_furnish_order_construct')->where("construct_id=$construct_id")->setField(array("status"=>3,'check_time'=>time()));
            //修改系统施工进度 
            if($quote_status!=1){
                $rs=M('xgj_furnish_order_detail')->where("detail_id=$detail_id")->setField(array("quote_status"=>$quote_status));
            }else{
                $rs=1;
            }
            //审核成功后支付该系统该阶段的金额
            $d_price=M('xgj_furnish_dealer as d')->join("xgj_furnish_order_construct as c on c.d_id=d.d_id")->where("c.construct_id=$construct_id and c.detail_id=$detail_id")->getField('d.d_price');

            $info=M('xgj_furnish_order_detail as d')->join("xgj_furnish_order_construct as c on c.detail_id=d.detail_id")->join("xgj_furnish_finance_construct_rate as r on r.quote_id=d.quote_id")->where("c.construct_id=$construct_id and d.detail_id=$detail_id")->find();
            // vdump($info['order_id']);
            // vdump($info);exit;
            // $dealer_orderOb=new \Admin\Model\Dealer\OrderModel;
            // $data = array(
            //         "price"=>$d_money,
            //         "order_id"=>$info['order_id'],
            //         "d_id"=>$info['d_id'],
            //         "operator"=>$_SESSION["admin_user"]['user_name'],
            //         "pay_status"=>0,
            //         "price"=>$d_money,
            //         "add_time"=>time()
            //     );
            if($quote_status==4){
                $d_money = $info['quote_price'] * $info['first_rate'] * $info['construct_rate'] * 0.85 + $info['quote_price'] * $info['first_rate'] * 0.1;
                $data['price'] = $d_money;
                $d_price += $d_money;
                $rsz=M('xgj_furnish_dealer')->where("d_id={$info['d_id']}")->setField(array("d_price"=>$d_price));
               
                $status=$dealer_orderOb->add_dealer('xgj_furnish_dealer_settle',$data);
            }else if($quote_status==6){
                $d_money = $info['quote_price'] * $info['mid_rate'] * $info['construct_rate'] * 0.85 + $info['quote_price'] * $info['mid_rate'] * 0.1;
                $d_price += $d_money;
                $rsz=M('xgj_furnish_dealer')->where("d_id={$info['d_id']}")->setField(array("d_price"=>$d_price));

                $status=$dealer_orderOb->add_dealer('xgj_furnish_dealer_settle',$data);
            }else{
                $rsz=1;
            }

            $info_status=$dealer_orderOb->get_dealer_order_info_status($order_id);
            if ($quote_status!=1){
                for ($i=0;$i<count($info_status);$i++){
                    if ($quote_status!=$info_status[$i]['quote_status']){
                        $result=0;
                        break;
                    }else{
                        $result=1;
                    }
                }
            }else{
                $result=1;
            }
            if ($result==1 && $quote_status!=1){
                $schedule_status=$quote_status==1?intval($quote_status):intval($quote_status)+1;
                $ru=$dealer_orderOb->where("order_id=$order_id")->setField(array("schedule_status"=>$schedule_status));
            }else{
                $ru=1;
            }
            //var_dump($re,$rs,$rsz,$ru);exit;
            if ($re && $rs && $rsz && $ru){
                M()->commit();
                $this->success('审核成功，正在跳转...');
                     die;
            }else{
                M()->rollBack();
                 $this->error('审核失败，正在跳转...');
                     die;
            }
       }else{
            //提示信息
            $this->error('请按照施工计划审核，同一订单多套系统需同步完成，方可审核，正在跳转...');
                    die;
        } 
    }         
    
    /**
     * 上传文件
     */
    function file(){
        //接收传值
        $order_id=intval($_GET['order_id']);
        if(isset($_FILES['file_img'])&&$_FILES['file_img']['error']==0){
            $image = uploadOne('file_img','File','','FILE_exts');
            if($image['code']!=1){
                //商品封面图片上传失败
                $this->error = $image['error'];
                return false;
            }
            $_POST['file_img'] = $image['images'];
            
            $file_type=pathinfo($_POST['file_img'], PATHINFO_EXTENSION);
            $date=array(
                "file_name"=>trim($_POST['file_name']),
                'file_img'=>$_POST['file_img'],
                'detail_id'=>$_POST['detail_id'],
                'order_id'=>$order_id,
                'upload_name'=>$_SESSION['admin_user']['user_name'],
                'file_type'=>$file_type,
                'class'=>3,
                'status'=>$_POST['type'],
                'file_time'=>time(),
            );
            $rs=M('xgj_furnish_order_file')->add($date);
            if ($rs) {
                $this->success('文件上传成功，正在跳转...');
                die;
            }else{
                $this->error('文件上传失败，正在跳转...');
                die;
            }
        }

    }
    
    /**
     * 订单调整审核
     */
    function adjust(){
        //接收传值
        $order_id=intval($_GET['order_id']);
        //实例化服务商订单model类
        $dealer_orderOb=new \Admin\Model\Dealer\OrderModel;
        if(isset($_GET['refuse']) && $_GET['refuse']==1){
            $rs=$dealer_orderOb->where("order_id=$order_id")->setField(array("order_status"=>7));
            if ($rs) {
                $this->success('拒绝调整成功，正在跳转...');
                die;
            }else{
                $this->error('拒绝调整失败，正在跳转...');
                die;
            }
        }else{
            $rs=$dealer_orderOb->where("order_id=$order_id")->setField(array("order_status"=>6));
            if ($rs) {
                $this->success('同意调整成功，正在跳转...');
                die;
            }else{
                $this->error('同意调整失败，正在跳转...');
                die;
            }
        }
    }
    
    /**
     * 下载文件
     */
    function file_down(){
        ob_end_clean();
        //接收传值
        $name=$_GET['file_img'];
        $file_img=base64_decode($name);
        //var_dump($file_img);exit;
        $path="/xgjtp/Public/Uploads/";
        down($path,$file_img);
    }


    public function city(){
        $province = $_GET['province'];
        $aaa = M('xgj_furnish_dealer')->field('d_city')->group('d_city')->where("d_province='$province'")->select();
        // var_dump($aaa);
        echo "<option value=''>请选择...</option>";
        foreach ($aaa as $v){
            echo "<option value='".$v['d_city']."'>".$v['d_city']."</option>";
        }
    }

    /**
     *显示图片
    */
    function show_image(){
        //接收传值
        $order_id=intval($_GET['order_id']);
        $aaa = M('xgj_furnish_order_file')->where("order_id=$order_id and status=3")->order('file_time asc')->getField('file_img');
        $image = getImage($aaa);
        $this->assign('image',$image);
        $this->display();
    }

    public function omsHtml(){
        $order_id=I('oid');
        $data = M('xgj_oms_batch')->where("order_id=$order_id")->select();
        $batch = '';
        if ($data) {
            $batch = 1;
            foreach ($data as $k => $v) {
                if ($v['batch']==2) {
                    $this->error('已全部发送完毕，无需发送');exit;
                }
            }
        }
        
        $this->assign('order_id',$order_id);
        $this->assign('batch',$batch);
        $this->display();
    }

	public function oms(){
        
        $order_id=I('oid');
        $os=I('os');
        $text=I('text');
        if (empty($os) || $os > 2) {
            $this->error('请选择批次');exit;
        }

        $batch = M('xgj_oms_batch')->where("order_id=$order_id and batch='{$os}'")->find();
        if ($os=='2') {
            $batch_1 = M('xgj_oms_batch')->where("order_id=$order_id and batch='1'")->find();
            if (!$batch_1) {
                $this->error('请先发送第一批',U('info',array('order_id'=>$order_id)));exit;
            }
        }
        if (!empty($batch)) {
            $this->error('该批次已发送过');exit;
        }

        $m=new \Admin\Model\Dealer\OrderModel;
        $data=$m->sendoms($order_id,$os,$text);
        if($data['flag']==1){
            $data = array(
                'order_id' => $order_id, 
                'batch'    => $os, 
                'text'     => $text, 
                'class'    => '1', 
                'time'     => time(), 
                'name'     => $_SESSION['admin_user']['user_name']
                );
            if (M('xgj_oms_batch')->add($data)) {
                $this->success('生成OMS成功',U('info',array('order_id'=>$order_id)));
            }else{
                $this->success('生成OMS成功但数据保存失败',U('info',array('order_id'=>$order_id)));
            }
        }elseif($data['flag']==2)
            $this->error('此订单未付款无法生成OMS订单');
        else
            $this->error('生成OMS失败');
    }
}