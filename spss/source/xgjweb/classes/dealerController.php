<?php

/**
 * @package WWW
 * @see feed_center, user_cace, photo_lib, notification_center, user_application, user_relations, user_register
 */
require_once(WWW_DIR . "/model/dealer_model.php");
require_once(WWW_DIR."/libs/page.php");

class dealerController {
 
    private $page = 5;   //售后管理每页显示数
    
    function __construct(){
        if(!isset($_GET['dealer_login']) && !isset($_GET['login']) && !empty($_GET) ){
            dealer_check_logon();
        }
    }

    private function alert($msg , $url = ''){
        header("Content-type: text/html; charset=utf-8");
        echo "<SCRIPT type='text/javascript'>alert('$msg');";
        if(empty($url)){
            echo "history.back();";
        }else{
            echo "window.location.href='".$url."';";
        }
        echo "</SCRIPT>";
        exit;
    }


    function dealer_login() {
        if(isset($_COOKIE['dealerId'])){
            header("Location: dealer.php?order");
        }
        $tpl = get_smarty();
        $tpl->display('dealer/dealer_login.tpl.html');
    }
    
    function login(){
        if(isset($_COOKIE['dealerId'])){
            header("Location: dealer.php?order");
        }else {
            $username = trim ($_POST['username']);
            $pwd = trim ($_POST['pwd']);
            if ($username!=''and $pwd!=''){
                $dealer = new dealer_model();
                $arr= $dealer->login($username, $pwd);
                if(empty($arr)){
                    $this -> alert('用户名与密码不匹配');
                }else{
                    setcookie("dealerId",$arr[0]["d_id"]);
                    setcookie("dealerName",$arr[0]["d_name"]);
                    header("Location: dealer.php?order");
                }
            }else{
                $this -> alert('用户名和密码必须填写');
            }
        }
        
    }

    function order(){
        $dealer = new dealer_model();
        //订单分页
        if(!isset($_GET['p'])){
            $page = 1;
        }else{
            $page = $_GET['p'];
        }
        $each_disNums = 8;//每页显示数据条数
        $dealer_order= $dealer->dealer_order($_COOKIE['dealerId'],$page,$each_disNums);
         
        //print_r($dealer_order);
        //exit();
        $dealerInfo = $dealer->getDealerInfo($_COOKIE['dealerId']);//经销商信息
        
        //分页
        $dealer_order_count_nav=$dealer->show_count_nav('xgj_furnish_order_info',$_COOKIE['dealerId']);//分页的总条数
        $t_nav = new Page($each_disNums, $dealer_order_count_nav, $page, 5, "dealer.php?order&p=");
        $page_nav=$t_nav->subPageCss2();//分页样式

        $tpl = get_smarty();
        $tpl->assign('dealerInfo',$dealerInfo);
        $tpl->assign("page_nav",$page_nav);//订单分页
        $tpl->assign('dealer_order',$dealer_order);
        $tpl->display('dealer/dealer_order_xin.tpl.html');
    }
    
    //订单搜索
    public function orderSearch(){
        
        if (!empty($_POST['search'])){
            
            $data = $_POST['search'];
            setcookie('data',$data);
            
        }else{
            $data = $_COOKIE['data'];
        }
        
        if(!isset($_GET['p'])){
            $page = 1;
        }else{
            $page = $_GET['p'];
        }
        
         if ($data=='订单号 / 联系人 / 手机号'){
            header("location:dealer.php?order");
            die();
        } 
       
        $dealer = new dealer_model();
        
        $each_disNums = 10;
        $dealer_order = $dealer->orderSearch($data,$page,$each_disNums);
 
        //分页
        $dealer_order_count_nav=$dealer->show_count_nav('xgj_furnish_order_info',$_COOKIE['dealerId']);//分页的总条数
        $t_nav = new Page($each_disNums, $dealer_order_count_nav, $page, 5, "dealer.php?orderSearch&p=");
        $page_nav=$t_nav->subPageCss2();//分页样式
        $dealerInfo = $dealer->getDealerInfo($_COOKIE['dealerId']);//经销商信息
        
        $tpl = get_smarty();
        $tpl->assign('dealerInfo',$dealerInfo);
        $tpl->assign("page_nav",$page_nav);//订单分页
        $tpl->assign('dealer_order',$dealer_order);
        $tpl->display('dealer/dealer_order_xin.tpl.html');
        
    }
    
    function center(){
        $dealer = new dealer_model();
        $tpl = get_smarty();
        $dealer_center= $dealer->dealer_center($_COOKIE['dealerId']);
        //print_r($_POST);
        $dealerInfo = $dealer->getDealerInfo($_COOKIE['dealerId']);//经销商信息
        $tpl->assign('dealerInfo',$dealerInfo);
        
        $tpl->assign('dealer_info',$dealer_center);
        $tpl->display('dealer/dealer_center.tpl.html');
    }

    function center_info(){
        $dealer = new dealer_model();

        if(isset($_POST) && $_POST!=null)
        {   
            //preg_match($pattern, $subject);
            if($dealer->update_info($_POST));
            header("Location:dealer.php?center");exit;
        }
       
        $dealer_info= $dealer->dealer_center($_COOKIE['dealerId']);
        //print_r($dealer_center);
        $tpl = get_smarty();
        $tpl->assign('dealer_info',$dealer_info);
        $tpl->display('dealer/dealer_info.tpl.html');
        
    }

    public function aftersaleInfo(){
        $dealer = new dealer_model();
        $dealerInfo = $dealer->getDealerInfo($_COOKIE['dealerId']);//经销商信息

        if(!preg_match("/^[0-9]+$/", $_GET['id']) || !preg_match("/^9?(10)?$/", $_GET['class'])){
            echo "<script type='text/javascript'>alert('非法操作！');history.go(-1);</script>";exit;
        }

        $class = $_GET['class'];

        $list  = $dealer->getWorkOrder($_GET['id'],$class);
        
        $tpl = get_smarty();
        $tpl ->assign('dealerInfo',$dealerInfo);
        $tpl ->assign('class',$class);
        $tpl ->assign('list',$list);
        $tpl ->display('dealer/dealer_aftersale_info.tpl.html');
    }
    

    function aftersale(){
        $dealer = new dealer_model();

        $dealerInfo = $dealer->getDealerInfo($_COOKIE['dealerId']);//经销商信息

        /***********************************/
        //保养
        $list  = $dealer->getUpkeep();
        $count = count($list);
        $page  = getAjaxPage($count,$this->page);
        $res   = $dealer->getUpkeep($page['limit']);
        /***********************************/


        /***********************************/
        //维修
        $mList  = $dealer->getMaintain();
        $mCount = count($mList);
        $mPage  = getAjaxPage01($mCount,$this->page);
        $mRes   = $dealer->getMaintain($mPage['limit']);
        /**********************************/

        $tpl = get_smarty();
        $tpl ->assign('dealerInfo',$dealerInfo);
        $tpl ->assign('list',$res);
        $tpl ->assign('page',$page['page']);
        $tpl ->assign('mList',$mRes);
        $tpl ->assign('mPage',$mPage['page']);
        $tpl ->display('dealer/dealer_aftersale_xin.tpl.html');

    }

    function aftersaleAjax(){
        
        $add =array(
            'o_id'       =>$_POST['o_id'],
            'd_id'       =>$_COOKIE['dealerId'],
            'start_time' =>$_POST['start_time'],
            'end_time'   =>$_POST['end_time'],
            'content'    =>$_POST['content'],
            'task_name'  =>$_POST['task_name'],
            'remark'     =>$_POST['remark'],
            'add_time'   =>time(),
            'class'      =>$_POST['class'],
            'p_id'       =>$_POST['p_id'],
            );

        if ($_POST['class']==10) $add['is_ok'] = $_POST['is_ok'];

        foreach ($add as $k => $v) {
            if (empty($v)) {
                echo '请填写完整，在提交！';exit;
            }
        }

        $dealer = new dealer_model();
        $re = $dealer->addData('xgj_dealer_work_order',$add);
        if ($re>0 && $_POST['class']==9) {
            $res = $dealer->getGoodsOne($_POST['p_id']);
            if ($res===false) {
                echo 1;exit;
            }
        }else if ($re>0 && $_POST['class']==10 && $_POST['is_ok']==1) {
            $where = "order_id = {$_POST['o_id']} and id = {$_POST['p_id']}";
            $re = $dealer->saveData('xgj_user_problem',array('state'=>'1'),$where);
            if ($re !== false) {
                echo 1;exit;
            }
        }
        if ($re!==false) echo '添加成功！';
        else echo '添加失败！';
    }

    function aftersaleAjaxPage(){

        if (!empty($_GET['p']) && !empty($_GET['t'])) {
            $dealer = new dealer_model();

            $t = $_GET['t'];
            if ($t==1) {
                //保养 
                $list  = $dealer->getUpkeep();
                $count = count($list);
                $page  = getAjaxPage($count,$this->page);
                $res   = $dealer->getUpkeep($page['limit']);

                $data = '';
                foreach ($res as $k => $v) {
                    $data .='<div class="dealer_aftersale-center-tabs01-list-demo">
                        <div class="dealer_aftersale-center-tabs01-list-demo-order">
                            '.$v["sn"].'
                        </div>
                        <div class="dealer_aftersale-center-tabs01-list-demo-contacts">
                            '.$v["shr_name"].'
                        </div>
                        <div class="dealer_aftersale-center-tabs01-list-demo-phone">
                            '.$v["shr_phone"].'
                        </div>
                        <div class="dealer_aftersale-center-tabs01-list-demo-address">
                            '.$v["shr_pro"].' '.$v["shr_city"].' '.$v["shr_area"].' '.$v["shr_addr"].'
                        </div>
                        <div class="dealer_aftersale-center-tabs01-list-demo-order">
                            '.$v["goods_title"].'
                        </div>
                        <div class="dealer_aftersale-center-tabs01-list-demo-time" style="width:140px;">
                            '.$v["pay_time"].'
                        </div>
                        <div class="dealer_aftersale-center-tabs01-list-demo-order">
                            <a href="javascript:;" onclick="upkeep('.$v["id"].','.$v["gid"].')">添加记录</a>
                             / <a href="dealer.php?aftersaleInfo&class=9&id='.$v["gid"].'">查看</a>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>';
                }
                $page['page'] .= "
                    <script>
                        $('.uPage a').click(function(){
                            var p = $(this).attr('data-page');
                            $.getJSON('dealer.php?aftersaleAjaxPage',{'p':p,'t':1},function(re){
                                $('#uPageDiv').html(re.data);
                                $('.uPage').html(re.page);
                            })
                        })
                    </script>
                ";
            } else if($t==2) {
                //维修
                $list  = $dealer->getMaintain();
                $count = count($list);
                $page  = getAjaxPage($count,$this->page);
                $res   = $dealer->getMaintain($page['limit']);

                $data = '';
                foreach ($res as $k => $v) {
                    $data .='<div class="dealer_aftersale-center-tabs02-list-demo">
                        <div class="dealer_aftersale-center-tabs02-list-demo-order">
                            '.$v["order_code"].'
                        </div>
                        
                        <div class="dealer_aftersale-center-tabs02-list-demo-contacts">
                            '.$v["name"].'
                        </div>
                        
                        <div class="dealer_aftersale-center-tabs02-list-demo-phone" style="width:100px;">
                            '.$v["phone"].'
                        </div>
                        
                        <div class="dealer_aftersale-center-tabs02-list-demo-address" style="width:160px;">
                            '.$v["province"].$v["city"].$v["district"].$v["address"].'
                        </div>
                        
                        <div class="dealer_aftersale-center-tabs02-list-demo-with_single" style="width:140px;">
                            '.$v["quote_name"].'
                        </div>
                        
                        <div class="dealer_aftersale-center-tabs02-list-demo-substance">
                            ';
                        if ($v['state'] == 0) {
                            $data .= '等待解决';
                        } else if($v['state'] == 1) {
                            $data .= '已解决';
                        }
                        $data .= '</div>
                        <div class="dealer_aftersale-center-tabs02-list-demo-progress">
                            <input type="hidden" id="lookData" value="'.$v["note"].'">
                            <a href="javascript:;" onclick="look()">查看</a>
                        </div>                    
                        
                        <div class="dealer_aftersale-center-tabs02-list-demo-time" style="width:140px;">
                            '.date('Y-m-d H:i:s',$v["time"]).'
                        </div>
                        <div class="dealer_aftersale-center-tabs02-list-demo-time">
                        ';
                        if ($v['state'] == 0) {
                            $data .= '<a href="javascript:;" onclick="maintain('.$v["order_id"].','.$v["id"].')">添加记录</a>';
                        } else if($v['state'] == 1) {
                            $data .= '添加记录';
                        }
                        $data .= ' / <a href="dealer.php?aftersaleInfo&class=10&id='.$v["id"].'">查看</a>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>';
                }
                $page['page'] .= "
                    <script>
                        $('.mPage a').click(function(){
                            var p = $(this).attr('data-page');
                            $.getJSON('dealer.php?aftersaleAjaxPage',{'p':p,'t':2},function(re){
                                $('#mPageDiv').html(re.data);
                                $('.mPage').html(re.page);
                            })
                        })
                    </script>
                ";
            }

            $re['data'] = $data;
            $re['page'] = $page['page'];

            echo json_encode($re);
        }
    }

    //账号
    public function account(){
        $tpl = get_smarty();
        
        $dealer = new dealer_model();
        $dealerInfo = $dealer->getDealerInfo($_COOKIE['dealerId']);//经销商信息
        $tpl->assign('dealerInfo',$dealerInfo);
        
        //订单分页
        if(!isset($_GET['op'])){
            $page = 1;
        }else{
            $page = $_GET['op'];
        }
        $each_disNums = 10;
        $dealer_order= $dealer->dealer_settle($_COOKIE['dealerId'],$page,$each_disNums);
        //分页
        $dealer_order_count_nav=$dealer->show_count_nav('xgj_furnish_dealer_settle',$_COOKIE['dealerId']);//分页的总条数
        $t_nav = new Page($each_disNums, $dealer_order_count_nav, $page, 5, "dealer.php?account&op=");
        $page_nav=$t_nav->subPageCss2();//分页样式

        //结算分页
        if(!isset($_GET['lp'])){
            $page = 1;
        }else{
            $page = $_GET['lp'];
        }
        $dealer_settlement= $dealer->dealer_settlement($_COOKIE['dealerId'],$page);
        //分页
        $dealer_order_count_nav=$dealer->show_count_nav('xgj_furnish_finance',$_COOKIE['dealerId']);//分页的总条数
       
        $t_nav = new Page(10, $dealer_order_count_nav, $page, 5, "dealer.php?account&lp=");
        $page_nav_settlement=$t_nav->subPageCss2();//分页样式
        
        //帐户余额
        $accountBalance = $dealer->accountBalance($_COOKIE['dealerId']);
        //print_r();
        $totalMoney = $accountBalance['d_price'];
        //模板传值
        $tpl->assign("page_nav",$page_nav);//订单分页
        $tpl->assign("page_nav_settlement",$page_nav_settlement);//结算分页
        $tpl->assign('dealer_order',$dealer_order);
        $tpl->assign('dealer_settlement',$dealer_settlement);
        $tpl->assign('totalMoney',$totalMoney);
        $tpl->display('dealer/dealer_account_xin.tpl.html');
    }

    #意见反馈
    function advice(){
        $dealer = new dealer_model();
        $tpl = get_smarty();
        $dealerInfo = $dealer->getDealerInfo($_COOKIE['dealerId']);//经销商信息
        $tpl->assign('dealerInfo',$dealerInfo);
        //判断是否有分页
        if(!isset($_GET['p'])){
            $page = 1;
        }else{
            $page = $_GET['p'];
        }
        $advice_list=$dealer->dealer_advices($_COOKIE['dealerId'],$page);
        // echo '<pre>';
        // print_r($advice_list);exit;

        //分页
        $advice_count_nav=$dealer->show_count_nav('xgj_furnish_advice',$_COOKIE['dealerId']);//分页的总条数
        $t_nav = new Page(10, $advice_count_nav, $page, 5, "dealer.php?advice&p=");
        $page_nav=$t_nav->subPageCss2();//分页样式

        
        //模板传值
        $tpl->assign("page_nav",$page_nav);
        $tpl->assign('advice_list',$advice_list);
        $tpl->display('dealer/dealer_advice_xin.tpl.html');
    }
    
    //意见反馈答复
    public function answerAdvice(){
        
        $dealer = new dealer_model();
         if (!empty($_POST)){
            $advice_id = $_POST['advice_id'];
            $data = array();
            $data['advice_reply'] = $_POST['advice_reply'];
            $data['advice_replytime'] = time();
            $data['advice_statu'] = 1;
            
            $where = "advice_id=$advice_id";
            $res = $dealer->updatePlan('xgj_furnish_advice', $data, $where);
            if ($res){
                echo '意见答复完成';
                header("location:dealer.php?advice");
            }
         }
    }
    
    //附件下载
    public function downLoad(){
        
        $file_name = $_GET['file_name'];
        
        //用以解决中文不能显示出来的问题
        $file_name=iconv("utf-8","gb2312",$file_name);
   
        $file_sub_path=$_SERVER['DOCUMENT_ROOT']."/xgjdd/source/xgjweb/pictures/file/upload/";
        $file_path=$file_sub_path."$file_name"; 

        //首先要判断给定的文件存在与否
        if(!file_exists($file_path)){
            echo "没有该文件文件";
            return ;
        }
        $fp=fopen($file_path,"r");
        $file_size=filesize($file_path);
        //下载文件需要用到的头
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length:".$file_size);
        Header("Content-Disposition: attachment; filename=".$file_name);
        $buffer=1024;
        $file_count=0;
        ob_clean();
        //向浏览器返回数据
        while(!feof($fp) && $file_count < $file_size){
            $file_con=fread($fp,$buffer);
            $file_count+=$buffer;
            ob_flush();
            echo $file_con;
        }
        fclose($fp); 

    }
    
    //详情页文件区文件下载
    public function detailDownLoad(){
        ob_end_clean();
		$file_name = base64_decode($_GET['file_name']);
        $path=DOWN_IMG_URL."Public/Uploads/";
        down($path,$file_name);    
  }
    
    
    

    #密码管理
    public function password(){
        $dealer = new dealer_model();
        $tpl = get_smarty();
        
        $dealerInfo = $dealer->getDealerInfo($_COOKIE['dealerId']);//经销商信息
        $tpl->assign('dealerInfo',$dealerInfo);
        $tpl->display('dealer/dealer_change_pwd_xin.tpl.html');
    }
    
    
    function apass(){
        $oldpass = $_POST['oldpass'];
        $newpass = $_POST['newpass'];
        $cnewpass = $_POST['cnewpass'];
        if(empty($oldpass) || empty($newpass) || empty($cnewpass)){
            $this -> alert('请填写完整');
        }
        if($newpass != $cnewpass){
            $this -> alert('新密码设置不一致');
        }
        if($newpass == $oldpass){
            $this -> alert('新密码与原密码不可一致');
        }
        $dealer = new dealer_model();
        if($dealer->upass($oldpass, $newpass)){
            $this -> alert('密码修改成功');
        }else{
            $this -> alert('原密码错误');
        }
    }
    
    /*
    public function getDealerHeaderInfo(){
        $dealer = new dealer_model();
        
        $dealerInfo = $dealer->getDealerInfo($_COOKIE['dealerId']);//经销商信息
        print_r($dealerInfo);
        
        $tpl = get_smarty();
        
        $tpl->assign('dealerInfo',$dealerInfo);
        display('dealer_header.tpl.html');
    }
    */
    //订单详情
    public function orderDetail(){
        
        $tpl = get_smarty();
        $dealer = new dealer_model();
        if (!empty($_GET['order_id'])){
            $order_id = $_GET['order_id'];
            setcookie('orderDetail_orderId',$order_id);
        }else {
            $order_id = $_COOKIE['orderDetail_orderId'];
        }
        $dealerInfo = $dealer->getDealerInfo($_COOKIE['dealerId']);//经销商信息
        
        //判断是否有分页
        if(!isset($_GET['p'])){
            $page = 1;
        }else{
            $page = $_GET['p'];
        }
        
        //分页
        $each_disNums = 15;
        $advice_count_nav=$dealer->show_count_nav('xgj_furnish_order_construct',$_COOKIE['dealerId']);//分页的总条数
        $t_nav = new Page($each_disNums, $advice_count_nav, $page, 5, "dealer.php?orderDetail&p=");
        $page_nav=$t_nav->subPageCss2();//分页样式
        
        //系统分类
        $systemData = $dealer->systemData($order_id);
        
        //安排施工
        $dataSettle = $dealer->constructSettle($order_id);
        //print_r($dataSettle);
        
        
        //施工计划   
        $dataPlan = $dealer->constructPlan($order_id,$page,$each_disNums);
//var_dump($order_id,$each_disNums);exit;
        if (!empty($dataPlan)) {
            //施工计划分类
            $planData = array();
            foreach($dataPlan as $v){
                $planData[$v['quote_name']][] = $v;
            }
            
            
            foreach ($planData as $k => $v) {
                $planDatas[] = $k;
            }

            foreach ($planDatas as $k=>$v) {
                foreach($planData as $k1=>$v1){
                    if ($v == $k1) {
                        $planDatass[] = $v1;
                    }
                }
            }
            
            $tpl->assign('dataPlans',$planDatas);
            $tpl->assign('dataPlanss',$planDatass);
        }
        
       // print_r($planDatass);
        
        
        //质量验收
        $dataCheck = $dealer->constructPlan($order_id,$page,$each_disNums);
        //print_r($dataCheck);
        if (!empty($dataCheck)) {
        //质量验收分类
            $checkData = array();
            foreach($dataCheck as $v){
                $checkData[$v['quote_name']][] = $v;
            }

            foreach ($checkData as $k => $v) {
                $checkDatas[] = $k;
            }

            foreach ($checkDatas as $k=>$v) {
                foreach($checkData as $k1=>$v1){
                    if ($v == $k1) {
                        $checkDatass[] = $v1;
                    }
                }
            }
            $tpl->assign('checkData',$checkDatas);
            $tpl->assign('checkDatas',$checkDatass);
        }
        // echo '<pre>';
        // var_dump($dataCheck);exit;
        
        //print_r($checkData);
        
        
        //订单基本信息
        $orderInfo = $dealer->orderInfo($order_id);
        
        $orderInfo['house_layout'] = explode(',', $orderInfo['house_layout']);
        // echo '<pre>';
        // var_dump($orderInfo);exit;
                
        $orderInfoDetail = $dealer->orderInfoDetail($order_id);


        //文件区域
        $dealerOrderFile = $dealer->dealerOrderFile($order_id);
        
            for ($i=0;$i < count($dealerOrderFile);$i++){
                $dealerOrderFile[$i]['file_txt'] = 'txt.jpg';
                $dealerOrderFile[$i]['file_doc'] = 'doc.jpg';
                $dealerOrderFile[$i]['file_docx'] = 'docx.jpg';
            }

        
        
        $tpl->assign('orderInfo',$orderInfo);
        $tpl->assign('dealerInfo',$dealerInfo);
        $tpl->assign('orderInfoDetail',$orderInfoDetail);
        
        $tpl->assign('systemData',$systemData);

        $tpl->assign('dataSettle',$dataSettle);
        $tpl->assign('dataCheck',$dataCheck);
        
        
        $tpl->assign('dealerOrderFile',$dealerOrderFile);
        //$tpl->assign('page_nav',$page_nav);
        $tpl->display('dealer/dealer_orderDetail.tpl.html');
    }
    
    //接收并处理施工安排部分的数据
    public function addPlan(){
        
        $dealer = new dealer_model();
        
        $quote_name = $_POST['quote_name'];
        $arr = explode('-', $quote_name);
        $order_id = $arr[0];
        $detail_id = $arr[1];
        
        $rs=$dealer->checkConstructPlan($arr[1],$_POST['task_work']);
        //var_dump($rs);exit;
        if($rs > 0){
            echo jump(2,'该施工任务已添加','dealer.php?orderDetail');
            header("refresh:4;url='dealer.php?orderDetail'" );
            exit;
        }else{
            $data = array();
            if (!empty($_POST)){
                $data['order_id'] = $arr[0];
                $data['detail_id'] = $arr[1];
                $data['d_id'] = $_COOKIE['dealerId'];
                $data['task_work'] = $_POST['task_work'];
                $data['task_name'] = $_POST['task_name'];
                $data['assigner'] = $_POST['assigner'];
                $data['start_time'] = strtotime($_POST['start_time']);
                $data['end_time'] = strtotime($_POST['end_time']);
                $data['status'] = 1;
                if($_POST['task_work']==1 || $_POST['task_work']==2){
                    $info=$dealer->getQuote($data['order_id']);
                    foreach ($info as $k=>$v){
                        $data_['order_id']   = $arr[0];
                        $data_['detail_id']  = $v['detail_id'];
                        $data_['d_id']       = $_COOKIE['dealerId'];
                        $data_['task_work']  = $_POST['task_work'];
                        $data_['task_name']  = $_POST['task_name'];
                        $data_['assigner']   = $_POST['assigner'];
                        $data_['start_time'] = strtotime($_POST['start_time']);
                        $data_['end_time']   = strtotime($_POST['end_time']);
                        $data_['status']     = 1;
                        $addPlan = $dealer->addPlan('xgj_furnish_order_construct', $data_);
                        if ($addPlan && $v['plan_settle']!=1){
                            //施工计划安排成功后需要将订单详情表里的plan_settle字段设置为1
                            $planSettle = array();
                            $planSettle['plan_settle'] = 1;
                            $where = "order_id=$order_id and detail_id={$v['detail_id']}";
                            $settleChange = $dealer->updatePlan('xgj_furnish_order_detail', $planSettle, $where);
                            //var_dump($settleChange);exit;
                            if (!$settleChange){
                                echo '订单安排状态修改失败'.'<br />';
                                header("location:dealer.php?orderDetail");exit();
                            }
                        }
                        header("location:dealer.php?orderDetail");
                    }
                    
                }else{
                    $addPlan = $dealer->addPlan('xgj_furnish_order_construct', $data);
                    if ($addPlan){
                        //echo '<hr />';
                        echo '施工计划安排添加成功';
                        //施工计划安排成功后需要将订单详情表里的plan_settle字段设置为1
                        $planSettle = array();
                        $planSettle['plan_settle'] = 1;
                        $where = "order_id=$order_id and detail_id=$detail_id";
                        $settleChange = $dealer->updatePlan('xgj_furnish_order_detail', $planSettle, $where);
                        if ($settleChange){
                            echo '订单安排状态已改变'.'<br />';
                        }
                        //exit();
                        header("location:dealer.php?orderDetail");
                    }
                }
                
            }
        }
        
    }
    
    //施工步骤筛选
    public function chooseWork(){
        
        $dealer = new dealer_model();
        
        $arr = explode('-', $_POST['data']);
        
        
        $order_id = $arr[0];
        $detail_id = $arr[1];
        
        $workData = $dealer->chooseWork($order_id,$detail_id);
        
        $str = '';
        foreach ($workData as $v){
                
            $str .= "
    <option value='".$v['id']."'>".$v['task_name']."</option>
    ";
                
        }
        
        
        echo $str;
    }
    
    //接收并处理施工计划部分的调整数据
    public function editPlan(){
        
        $dealer = new dealer_model();
        $data = array();
        if (!empty($_POST)){
            //print_r($_POST);
            $construct_id = $_POST['c_id'];
            $data['start_time'] = strtotime($_POST['start_time']);
            $data['end_time'] = strtotime($_POST['end_time']);
           
            $data['task_name'] = $_POST['task_name'];
            $data['assigner'] = $_POST['assigner'];
            $where = "construct_id=$construct_id ";
            $updatePlan = $dealer->updatePlan('xgj_furnish_order_construct', $data, $where);
            if ($updatePlan){
              echo  jump(1,'数据更新成功',"dealer.php?orderDetail");
            }else
		        echo	 jump(2,'更新失败',"dealer.php?orderDetail");
        }else
			  echo jump(2,'内容未修改',"dealer.php?orderDetail");
        
    }
    //接受质量验收数据并处理
    public function editCheck(){
        
        $dealer = new dealer_model();
        $data = array();
        if (!empty($_POST)){
            //print_r($_POST);
            $construct_id = $_POST['construct_id'];
            $order_id = $_POST['order_id'];
            $detail_id = $_POST['detail_id'];
            $data['check_status'] = $_POST['check_status'];
            $data['status']=2;
            $where = "construct_id=$construct_id and order_id=$order_id and detail_id=$detail_id";
            
            $updateCheck = $dealer->updatePlan('xgj_furnish_order_construct', $data, $where);
            if ($updateCheck){
                echo '审核操作成功';
                //exit();
                header("location:dealer.php?orderDetail");
            }
        }
    }
    
    
    //辅材清单页面
    public function fuCaiList(){
        if (!empty($_GET['order_id'])){
            $order_id = $_GET['order_id'];
            setcookie('orderDetail_orderId',$order_id);
        }else {
            $order_id = $_COOKIE['orderDetail_orderId'];
        }
        if (!empty($_GET['detail_id'])){
            $detail_id = $_GET['detail_id'];
            setcookie('detail_id',$detail_id);
        }else {
            $detail_id = $_COOKIE['detail_id'];
        }
        if (!empty($_GET['level'])){
            $level = $_GET['level'];
            setcookie('level',$level);
        }else {
            $level = $_COOKIE['level'];
        }
        $dealer = new dealer_model();
        $tpl = get_smarty();
        //经销商信息
        $dealerInfo = $dealer->getDealerInfo($_COOKIE['dealerId']);
        $tpl->assign('dealerInfo',$dealerInfo);
        //补货删除操作
         if (isset($_GET['delete'])&&$_GET['delete']==1&&isset($_SESSION['replenish'][$_GET['goods_sn']])){
            unset($_SESSION['replenish'][$_GET['goods_sn']]);
        }
        //退货删除操作
        if (isset($_GET['delete'])&&$_GET['delete']==2&&isset($_SESSION['refund'][$_GET['goods_sn']])){
            unset($_SESSION['refund'][$_GET['goods_sn']]);
        }
        //自购删除操作
        if (isset($_GET['delete'])&&$_GET['delete']==6&&isset($_COOKIE['selfBuy'][$_GET['goods_sn']])){
            unset($_COOKIE['selfBuy'][$_GET['goods_sn']]);
        }
        //经销商信息
        $dealerInfo = $dealer->getDealerInfo($_COOKIE['dealerId']);
        //订单信息
        $orderInfo = $dealer->orderInfo($order_id);
        $orderInfo['house_layout'] = explode(',', $orderInfo['house_layout']);
        //辅材列表
        $fucaiList=$dealer->get_dealer_order_stuff_list($detail_id,$level);

        if (!empty($_POST) && isset($_POST['numberChange']) && isset($_POST['numberOld'])){
            if ($_POST['numberChange'] > $_POST['numberOld']){
                //补货数据
                $_POST['numbers'] = $_POST['numberChange']-$_POST['numberOld'];
                $_SESSION['replenish'][$_POST['goods_sn']] = $_POST;
                $replenishTemp = $_SESSION['replenish'];
                //获取补货的总金额
                $totalReplenish = null;
                foreach ($replenishTemp as $goodsTemp){
                    $totalReplenish+= $goodsTemp['shop_price']*$goodsTemp['numbers'];
                }
                $_SESSION['totalReplenish'] = $totalReplenish;
                //vdump($_SESSION);
                $tpl->assign('replenishTemp',$replenishTemp);
                $tpl->assign('totalReplenish',$totalReplenish);
            }else if($_POST['numberChange'] < $_POST['numberOld']){
                //退货数据
                $_POST['numbers'] = $_POST['numberOld']-$_POST['numberChange'];
                $_SESSION['refund'][$_POST['goods_sn']] = $_POST;
                $refundTemp = $_SESSION['refund'];
                //获取退货的总金额
                $totalRefund = null;
                foreach ($refundTemp as $goodsTemp){
                    $totalRefund+= $goodsTemp['shop_price']*$goodsTemp['numbers'];
                }
                $_SESSION['totalRefund'] = $totalRefund;
                $tpl -> assign('refundTemp',$refundTemp);
                $tpl -> assign('totalRefund',$totalRefund);
            }
        }
        
        //判断SESSION中是否有补货信息
        if (!empty($_SESSION['replenish'])){
            $replenishTemp = $_SESSION['replenish'];
            //获取补货的总金额
            $totalReplenish = null;
            foreach ($replenishTemp as $goodsTemp){
                $totalReplenish+= $goodsTemp['shop_price']*$goodsTemp['numbers'];
            }
            $_SESSION['totalReplenish'] = $totalReplenish;
            $tpl->assign('replenishTemp',$replenishTemp);
            $tpl->assign('totalReplenish',$totalReplenish);
        }
        if (!empty($_SESSION['refund'])){
            $refundTemp = $_SESSION['refund'];
            //获取退货的总金额
            $totalRefund = null;
            foreach ($refundTemp as $goodsTemp){
                $totalRefund+= $goodsTemp['shop_price']*$goodsTemp['numbers'];
            }
            $_SESSION['totalRefund'] = $totalRefund;
            $tpl->assign('refundTemp',$refundTemp);
            $tpl->assign('totalRefund',$totalRefund);
        }
        
        //向前台模板页面注册自购材料数据
        if (!empty($_SESSION['selfBuy'])){
            $selfBuyTemp = $_SESSION["selfBuy"];
            //$selfBuyTemp = unserialize($selfBuyTemp);        
            //获取退货的总金额
            $totalSelfBuy = null;
            foreach ($selfBuyTemp as $goodsTemp){
                $totalSelfBuy+= $goodsTemp['shop_price']*$goodsTemp['numbers'];
            }
            $_SESSION['totalSelfBuy'] = $totalSelfBuy;
            $tpl->assign('selfBuyTemp',$selfBuyTemp);
            $tpl->assign('totalSelfBuy',$totalSelfBuy);
        }
        //处理订单号
        $microtime               = explode('.',microtime(true));
        /**
         * 补货材料入库数据
         * */
        if (!empty($_SESSION['replenish'])){
            $replenishData = array();
            $replenishData['level']=$level;
            $replenishData['order_id'] = $orderInfo['order_id'];
            $replenishData['user_id'] = $orderInfo['user_id'];
            $replenishData['d_id'] = $orderInfo['d_id'];
            $replenishData['quote_id'] = $orderInfo['quote_id'];
            $replenishData['refund_price'] = $_SESSION['totalReplenish'];
            $replenishData['refund_status'] = 2;
            $replenishData['refund_time'] = time();
            date_default_timezone_set("Asia/ShangHai");//设置时区
            $replenishData['refund_code'] = date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT);
            $replenishData['first_audit_status'] = 1;
            $replenishData['detail_id'] = $orderInfo['detail_id'];
            $replenishData['refund_goods'] = '';
            //获取$replenishData['refund_goods']数据
            if (!empty($_SESSION['replenish'])){
                $goodsSn = '';
                $goodsNum = '';
                foreach ($_SESSION['replenish'] as $k=>$v){
                    $goodsSn.=','.$v['goods_sn'];
                    $goodsNum.=','.$v['numbers'];
                }
                $goodsSn = trim($goodsSn,',');
                $goodsNum = trim($goodsNum,',');
                $replenishData['refund_goods'] .= $goodsSn;
                $replenishData['refund_goods'] .= ';';
                $replenishData['refund_goods'] .= $goodsNum;
                if (!empty($replenishData)){
                    $arrReplenish = serialize($replenishData);
                    setcookie("arrReplenish",$arrReplenish,time()+3600);
                }
            }
            //vdump($_COOKIE['arrReplenish']);
        }
        
        /**
         * 自购材料入库数据
         * */
        if (!empty($_SESSION['selfBuy'])){
            $selfBuyData = array();
            $replenishData['level']=$level;
            $selfBuyData['order_id'] = $orderInfo['order_id'];
            $selfBuyData['user_id'] = $orderInfo['user_id'];
            $selfBuyData['d_id'] = $orderInfo['d_id'];
            $selfBuyData['quote_id'] = $orderInfo['quote_id'];
            $selfBuyData['refund_price'] = $_SESSION['totalSelfBuy'];
            $selfBuyData['refund_status'] = 3;
            $selfBuyData['refund_time'] = time();
            date_default_timezone_set("Asia/ShangHai");//设置时区
            $selfBuyData['refund_code'] = date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT);
            $selfBuyData['first_audit_status'] = 1;
            $selfBuyData['detail_id'] = $orderInfo['detail_id'];
            $selfBuyData['refund_goods'] = '';
            //获取$replenishData['refund_goods']数据
            if (!empty($_SESSION['selfBuy'])){
                $goodsSn = '';
                $goodsNum = '';
                foreach ($_SESSION['selfBuy'] as $k=>$v){
                    $goodsSn.=','.$v['goods_sn'];
                    $goodsNum.=','.$v['numbers'];
                }
                $goodsSn = trim($goodsSn,',');
                $goodsNum = trim($goodsNum,',');
                $selfBuyData['refund_goods'] .= $goodsSn;
                $selfBuyData['refund_goods'] .= ';';
                $selfBuyData['refund_goods'] .= $goodsNum;
                if (!empty($selfBuyData)){
                    $arrSelfBuy = serialize($selfBuyData);
                    setcookie("arrSelfBuy",$arrSelfBuy,time()+3600);
                }
            }
        }

        /**
         * 退货材料入库数据
         * */
        if (!empty($_SESSION['refund'])){
            $refundData = array();
            $replenishData['level']=$level;
            $refundData['order_id'] = $orderInfo['order_id'];
            $refundData['user_id'] = $orderInfo['user_id'];
            $refundData['d_id'] = $orderInfo['d_id'];
            $refundData['quote_id'] = $orderInfo['quote_id'];
            $refundData['refund_price'] = $_SESSION['totalRefund'];
            $refundData['refund_status'] = 1;
            $refundData['refund_time'] = time();
            date_default_timezone_set("Asia/ShangHai");//设置时区
            $refundData['refund_code'] = date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT);
            $refundData['first_audit_status'] = 1;
            $refundData['detail_id'] = $orderInfo['detail_id'];
            $refundData['refund_goods'] = '';
            //获取$refundData['refund_goods']数据
            if (!empty($_SESSION['refund'])){
                $goodsSn = '';
                $goodsNum = '';
                foreach ($_SESSION['refund'] as $k=>$v){
                    $goodsSn.=','.$v['goods_sn'];
                    $goodsNum.=','.$v['numbers'];
                }
                $goodsSn = trim($goodsSn,',');
                $goodsNum = trim($goodsNum,',');
                $refundData['refund_goods'] .= $goodsSn;
                $refundData['refund_goods'] .= ';';
                $refundData['refund_goods'] .= $goodsNum;
                if (!empty($refundData)){
                    $arrRefund = serialize($refundData);
                    setcookie("arrRefund",$arrRefund,time()+3600);
                }
            }
        }        
        $tpl->assign('orderInfo',$orderInfo);
        $tpl->assign('fucaiList',$fucaiList);
        $tpl->display('dealer/dealer_fucai_xin.tpl.html');
    }
    
   
    
    
    /**
     *主材清单页面控制器 
     */
    public function zhuCaiList(){
        if (!empty($_GET['order_id'])){
            $order_id = $_GET['order_id'];
            setcookie('orderDetail_orderId',$order_id);
        }else {
            $order_id = $_COOKIE['orderDetail_orderId'];
        }
        if (!empty($_GET['detail_id'])){
            $detail_id = $_GET['detail_id'];
            setcookie('detail_id',$detail_id);
        }else {
            $detail_id = $_COOKIE['detail_id'];
        }
        if (!empty($_GET['level'])){
            $level = $_GET['level'];
            setcookie('level',$level);
        }else {
            $level = $_COOKIE['level'];
        }
        $dealer = new dealer_model();
        $tpl = get_smarty();       
        $dealerInfo = $dealer->getDealerInfo($_COOKIE['dealerId']);//经销商信息
        $tpl->assign('dealerInfo',$dealerInfo);
        
        //补货删除操作
        if (isset($_GET['delete'])&&$_GET['delete']==1&&isset($_SESSION['replenishZhu'][$_GET['goods_sn']])){
            unset($_SESSION['replenishZhu'][$_GET['goods_sn']]);
        }
         
        //退货删除操作
        if (isset($_GET['delete'])&&$_GET['delete']==2&&isset($_SESSION['refundZhu'][$_GET['goods_sn']])){
            unset($_SESSION['refundZhu'][$_GET['goods_sn']]);
        }
        
        $dealerInfo = $dealer->getDealerInfo($_COOKIE['dealerId']);//经销商信息
        
        $orderInfo = $dealer->orderInfo($order_id);
       
        $zhucaiList=$dealer->get_dealer_order_stuff_list($detail_id,$level);

        if (!empty($_POST)&&isset($_POST['numberChange'])&&isset($_POST['numberOld'])){
            if ($_POST['numberChange']>$_POST['numberOld']){
                //补货数据
                $_POST['numbers'] = $_POST['numberChange']-$_POST['numberOld'];
                $_SESSION['replenishZhu'][$_POST['goods_sn']] = $_POST;
                $replenishTemp = $_SESSION['replenishZhu'];
                //获取补货的总金额
                $totalReplenish = null;
                foreach ($replenishTemp as $goodsTemp){
                    $totalReplenish+= $goodsTemp['shop_price']*$goodsTemp['numbers'];
                }
                $_SESSION['totalReplenishZhu'] = $totalReplenish;
                $tpl->assign('replenishTemp',$replenishTemp);
                $tpl->assign('totalReplenish',$totalReplenish); 
                                 
            }else if($_POST['numberChange'] < $_POST['numberOld']){
                //退货数据
                $_POST['numbers'] = $_POST['numberOld']-$_POST['numberChange'];
                $_SESSION['refundZhu'][$_POST['goods_sn']] = $_POST;
                
                $refundTemp = $_SESSION['refundZhu'];
                 
                //获取退货的总金额
                $totalRefund = null;
                foreach ($refundTemp as $goodsTemp){
                    $totalRefund+= $goodsTemp['shop_price']*$goodsTemp['numbers'];
                }
                $_SESSION['totalRefundZhu'] = $totalRefund; 
                $tpl->assign('refundTemp',$refundTemp);
                $tpl->assign('totalRefund',$totalRefund);
            }
        }
        
        //判断SESSION里面有没有存入补货信息
        if (!empty($_SESSION['replenishZhu'])){
            $replenishTemp = $_SESSION['replenishZhu'];
            //获取补货的总金额
            $totalReplenish = null;
            foreach ($replenishTemp as $goodsTemp){
                $totalReplenish+= $goodsTemp['shop_price']*$goodsTemp['numbers'];
            }
            $_SESSION['totalReplenishZhu'] = $totalReplenish; 
            $tpl->assign('replenishTemp',$replenishTemp);
            $tpl->assign('totalReplenish',$totalReplenish);
            
        }
        if (!empty($_SESSION['refundZhu'])){
            $refundTemp = $_SESSION['refundZhu'];
            //获取退货的总金额
            $totalRefund = null;
            foreach ($refundTemp as $goodsTemp){
                $totalRefund+= $goodsTemp['shop_price']*$goodsTemp['numbers'];
            }
            $_SESSION['totalRefundZhu'] = $totalRefund;
            $tpl->assign('refundTemp',$refundTemp);
            $tpl->assign('totalRefund',$totalRefund);
        }

        //处理订单号
        $microtime               = explode('.',microtime(true));
        /**
         * 补货材料入库数据
         * */
        if (!empty($_SESSION['replenishZhu'])){
            $replenishData = array();
            $replenishData['level']=$level;
            $replenishData['order_id'] = $orderInfo['order_id'];
            $replenishData['user_id'] = $orderInfo['user_id'];
            $replenishData['d_id'] = $orderInfo['d_id'];
            $replenishData['quote_id'] = $orderInfo['quote_id'];
            $replenishData['refund_price'] = $_SESSION['totalReplenishZhu'];
            $replenishData['refund_status'] = 2;
            $replenishData['refund_time'] = time();
            date_default_timezone_set("Asia/ShangHai");//设置时区
            $replenishData['refund_code'] = date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT);
            $replenishData['first_audit_status'] = 1;
            $replenishData['detail_id'] = $orderInfo['detail_id'];
            $replenishData['refund_goods'] = '';
            //获取$replenishData['refund_goods']数据
            if (!empty($_SESSION['replenishZhu'])){
                $goodsSn = '';
                $goodsNum = '';
                foreach ($_SESSION['replenishZhu'] as $k=>$v){
                    $goodsSn.=','.$v['goods_sn'];
                    $goodsNum.=','.$v['numbers'];
                }
                $goodsSn = trim($goodsSn,',');
                $goodsNum = trim($goodsNum,',');
                $replenishData['refund_goods'] .= $goodsSn;
                $replenishData['refund_goods'] .= ';';
                $replenishData['refund_goods'] .= $goodsNum;
                if (!empty($replenishData)){
                    $arrReplenishZhu = serialize($replenishData);
                    setcookie("arrReplenishZhu",$arrReplenishZhu,time()+3600);
                }
                 
            }

        }
        
        
        /**
         * 退货材料入库数据
         * */
        if (!empty($_SESSION['refundZhu'])){
            $refundData = array();
            $replenishData['level']=$level;
            $refundData['order_id'] = $orderInfo['order_id'];
            $refundData['user_id'] = $orderInfo['user_id'];
            $refundData['d_id'] = $orderInfo['d_id'];
            $refundData['quote_id'] = $orderInfo['quote_id'];
            $refundData['refund_price'] = $_SESSION['totalRefundZhu'];
            $refundData['refund_status'] = 1;
            $refundData['refund_time'] = time();
            date_default_timezone_set("Asia/ShangHai");//设置时区
            $refundData['refund_code'] = date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT);
            $refundData['first_audit_status'] = 1;
            $refundData['detail_id'] = $orderInfo['detail_id'];
            $refundData['refund_goods'] = '';
            //获取$refundData['refund_goods']数据
            if (!empty($_SESSION['refundZhu'])){
                $goodsSn = '';
                $goodsNum = '';
                foreach ($_SESSION['refundZhu'] as $k=>$v){
                    $goodsSn.=','.$v['goods_sn'];
                    $goodsNum.=','.$v['numbers'];
                }
                $goodsSn = trim($goodsSn,',');
                $goodsNum = trim($goodsNum,',');
                $refundData['refund_goods'] .= $goodsSn;
                $refundData['refund_goods'] .= ';';
                $refundData['refund_goods'] .= $goodsNum;
                if (!empty($refundData)){
                    $arrRefundZhu = serialize($refundData);
                    setcookie("arrRefundZhu",$arrRefundZhu,time()+3600);
                }
            }
        }
        
        $tpl->assign('orderInfo',$orderInfo);
        $tpl->assign('zhucaiList',$zhucaiList);
        $tpl->display('dealer/dealer_zhucai_xin.tpl.html');
    }
    
    //接收并处理辅材清单页面自购材料
    public function selfBuy(){
        if(!isset($_SESSION)){
            session_start();
        }
        if (!empty($_POST)){
            $_SESSION['selfBuy'][$_POST['goods_sn']] = $_POST;
        }
        header("location:dealer.php?fuCaiList");
    }
    
    //补/退货订单入库
    public function insertReplenish(){
        $dealer = new dealer_model();
        //补货辅材清单插入数据库
        if (isset($_GET['add'])&&$_GET['add']==1){
            $replenishData = unserialize($_COOKIE['arrReplenish']);
            $res = $dealer->insertGoods('xgj_furnish_order_refund',$replenishData);
            if ($res){
                //执行完数据库插入操作后清空SESSION和COOKIE里的对应内容
                unset($_COOKIE['arrReplenish']);
                unset($_SESSION['replenish']);
                header("location:dealer.php?fuCaiList&order_id={$_COOKIE['orderDetail_orderId']}&level={$_COOKIE['level']}&detail_id={$_COOKIE['detail_id']}");
            }else {
                echo '补货清单插入数据库失败';
            }
        }else if (isset($_GET['add'])&&$_GET['add']==2){
            //退货辅材数据入库
            $refundData = unserialize($_COOKIE['arrRefund']);
            $res = $dealer->insertGoods('xgj_furnish_order_refund',$refundData);
            if ($res){                
                //执行完数据库插入操作后清空SESSION和COOKIE里的对应内容
                unset($_COOKIE['arrRefund']);
                unset($_SESSION['refund']);
                header("location:dealer.php?fuCaiList&order_id={$_COOKIE['orderDetail_orderId']}&level={$_COOKIE['level']}&detail_id={$_COOKIE['detail_id']}");
            }else {
                echo '退货材料插入数据库失败';
            }
        }else if (isset($_GET['add'])&&$_GET['add']==3){
            //补货主材料材料数据入库
            $replenishDataZhu = unserialize($_COOKIE['arrReplenishZhu']);
            $res = $dealer->insertGoods('xgj_furnish_order_refund',$replenishDataZhu);
            if ($res){
                //echo '补货主材料已成功插入数据库';
                //执行完数据库插入操作后清空SESSION和COOKIE里的对应内容
                unset($_COOKIE['arrReplenishZhu']);
                unset($_SESSION['replenishZhu']);
                header("location:dealer.php?zhuCaiList&order_id={$_COOKIE['orderDetail_orderId']}&level={$_COOKIE['level']}&detail_id={$_COOKIE['detail_id']}");
            }else {
                echo '补货主材料插入数据库失败';
            }
        }else if (isset($_GET['add'])&&$_GET['add']==4){
            //退货主材料数据入库
            $refundDataZhu = unserialize($_COOKIE['arrRefundZhu']);
            $res = $dealer->insertGoods('xgj_furnish_order_refund',$refundDataZhu);
            if ($res){
                //echo '退货主材料已成功插入数据库';
                //执行完数据库插入操作后清空SESSION和COOKIE里的对应内容
                unset($_COOKIE['arrRefundZhu']);
                unset($_SESSION['refundZhu']);
                header("location:dealer.php?zhuCaiList&order_id={$_COOKIE['orderDetail_orderId']}&level={$_COOKIE['level']}&detail_id={$_COOKIE['detail_id']}");
            }else {
                echo '退货主材料插入数据库失败';
            }
        }else if (isset($_GET['add']) && $_GET['add']==5 && isset($_COOKIE['arrSelfBuy'])){
            //自购辅材数据入库
            $selfBuyData = unserialize($_COOKIE['arrSelfBuy']);
            $res = $dealer->insertGoods('xgj_furnish_order_refund',$selfBuyData);
            if ($res){
                //echo '自购辅材已成功插入数据库';
                //执行完数据库插入操作后清空SESSION和COOKIE里的对应内容
                unset($_COOKIE['arrSelfBuy']);
                unset($_SESSION['selfBuy']);
                header("location:dealer.php?fuCaiList&order_id={$_COOKIE['orderDetail_orderId']}&level={$_COOKIE['level']}&detail_id={$_COOKIE['detail_id']}");
            }else {
                echo '自购辅材插入数据库失败';
            }
        }else{
            header("location:dealer.php?fuCaiList&order_id={$_COOKIE['orderDetail_orderId']}&level={$_COOKIE['level']}&detail_id={$_COOKIE['detail_id']}");
        }
    }
    
    
    //增加辅材补货材料
    public function addReplenish(){
    	//重新组合POST过来的数据
        $arr = array();
        $goods_sn = $_POST['goods_sn'];
        $goods_img = $_POST['goods_img'];
        $goods_name = $_POST['goods_name'];
        $goods_model = $_POST['goods_model'];
        $goods_brand = $_POST['goods_brand'];
        $shop_price = $_POST['shop_price'];
        $goods_unit = $_POST['goods_unit'];
        $numbers = $_POST['numbers'];
         
        for ($i=0;$i < count($goods_sn);$i++){
            $arr[$i]['goods_sn']=$goods_sn[$i];
            $arr[$i]['goods_img']=$goods_img[$i];
            $arr[$i]['goods_name']=$goods_name[$i];
            $arr[$i]['goods_model']=$goods_model[$i];
            $arr[$i]['goods_brand']=$goods_brand[$i];
            $arr[$i]['shop_price']=$shop_price[$i];
            $arr[$i]['goods_unit']=$goods_unit[$i];
            $arr[$i]['numbers']=$numbers[$i];
        }

        foreach ($arr as $v){
            //如果临时补货列表里已经存在某一材料则只增加其数量
            if ( array_key_exists($v['goods_sn'], $_SESSION['replenish'])){
                //echo '临时补货列表里已经存在某一材料';
                $_SESSION['replenish'][$v['goods_sn']]['numbers'] = $_SESSION['replenish'][$v['goods_sn']]['numbers']+$v['numbers'];
            }else if ($v['numbers']!=0){
                $_SESSION['replenish'][$v['goods_sn']] = $v;
            }
        }
        header("location:dealer.php?fuCaiList");
    }
    
    //增加退货材料
    public function addRefund(){
        //重新组合POST过来的数据
        $arr = array();
        $goods_sn = $_POST['goods_sn'];
        $goods_img = $_POST['goods_img'];
        $goods_name = $_POST['goods_name'];
        $goods_model = $_POST['goods_model'];
        $goods_brand = $_POST['goods_brand'];
        $shop_price = $_POST['shop_price'];
        $goods_unit = $_POST['goods_unit'];
        $numbers = $_POST['numbers'];
        
        for ($i=0;$i<count($goods_sn);$i++){
            $arr[$i]['goods_sn']=$goods_sn[$i];
            $arr[$i]['goods_img']=$goods_img[$i];
            $arr[$i]['goods_name']=$goods_name[$i];
            $arr[$i]['goods_model']=$goods_model[$i];
            $arr[$i]['goods_brand']=$goods_brand[$i];
            $arr[$i]['shop_price']=$shop_price[$i];
            $arr[$i]['goods_unit']=$goods_unit[$i];
            $arr[$i]['numbers']=$numbers[$i];
        
        }

        foreach ($arr as $v){
            //如果临时补货列表里已经存在某一材料则只增加其数量
            if ( array_key_exists($v['goods_sn'], $_SESSION['refund'])){
                //echo '临时补货列表里已经存在某一材料';
                $_SESSION['refund'][$v['goods_sn']]['numbers'] = $_SESSION['refund'][$v['goods_sn']]['numbers']+$v['numbers'];
            }else if ($v['numbers']!=0){
                $_SESSION['refund'][$v['goods_sn']] = $v;
            }
        }
        header("location:dealer.php?fuCaiList");
    }
    
    
    
    
    
    //增加主材补货材料
    public function addReplenishZhu(){
        //重新组合POST过来的数据
        $arr = array();
        $goods_sn = $_POST['goods_sn'];
        $goods_img = $_POST['goods_img'];
        $goods_name = $_POST['goods_name'];
        $goods_model = $_POST['goods_model'];
        $goods_brand = $_POST['goods_brand'];
        $shop_price = $_POST['shop_price'];
        $goods_unit = $_POST['goods_unit'];
        $numbers = $_POST['numbers'];
    
        for ($i=0;$i < count($goods_sn);$i++){
            $arr[$i]['goods_sn']=$goods_sn[$i];
            $arr[$i]['goods_img']=$goods_img[$i];
            $arr[$i]['goods_name']=$goods_name[$i];
            $arr[$i]['goods_model']=$goods_model[$i];
            $arr[$i]['goods_brand']=$goods_brand[$i];
            $arr[$i]['shop_price']=$shop_price[$i];
            $arr[$i]['goods_unit']=$goods_unit[$i];
            $arr[$i]['numbers']=$numbers[$i];
    
        }
        foreach ($arr as $v){
            //如果临时补货列表里已经存在某一材料则只增加其数量
            if ( array_key_exists($v['goods_sn'], $_SESSION['replenishZhu'])){
                //echo '临时补货列表里已经存在某一材料';
                $_SESSION['replenishZhu'][$v['goods_sn']]['numbers'] = $_SESSION['replenishZhu'][$v['goods_sn']]['numbers']+$v['numbers'];
            }else if ($v['numbers']!=0){
                $_SESSION['replenishZhu'][$v['goods_sn']] = $v;
            }
        }
        header("location:dealer.php?zhuCaiList");
         
    }
    
    
    //增加主材退货材料
    public function addRefundZhu(){
        //重新组合POST过来的数据
        $arr = array();
        $goods_sn = $_POST['goods_sn'];
        $goods_img = $_POST['goods_img'];
        $goods_name = $_POST['goods_name'];
        $goods_model = $_POST['goods_model'];
        $goods_brand = $_POST['goods_brand'];
        $shop_price = $_POST['shop_price'];
        $goods_unit = $_POST['goods_unit'];
        $numbers = $_POST['numbers'];
         
        for ($i=0;$i < count($goods_sn);$i++){
            $arr[$i]['goods_sn']=$goods_sn[$i];
            $arr[$i]['goods_img']=$goods_img[$i];
            $arr[$i]['goods_name']=$goods_name[$i];
            $arr[$i]['goods_model']=$goods_model[$i];
            $arr[$i]['goods_brand']=$goods_brand[$i];
            $arr[$i]['shop_price']=$shop_price[$i];
            $arr[$i]['goods_unit']=$goods_unit[$i];
            $arr[$i]['numbers']=$numbers[$i];
             
        }
        foreach ($arr as $v){
            //如果临时补货列表里已经存在某一材料则只增加其数量
            if ( array_key_exists($v['goods_sn'], $_SESSION['refundZhu'])){
                //echo '临时补货列表里已经存在某一材料';
                $_SESSION['refundZhu'][$v['goods_sn']]['numbers'] = $_SESSION['refundZhu'][$v['goods_sn']]['numbers']+$v['numbers'];
            }else if ($v['numbers']!=0){
                $_SESSION['refundZhu'][$v['goods_sn']] = $v;
            }
        }  
        header("location:dealer.php?zhuCaiList");        
    }
    
    
    //文件上
    public function uploadFile(){
        $dealer = new dealer_model();
        $data = array();
        if (!empty($_POST)){
            $_POST['upload_name']?$data['upload_name']=$_POST['upload_name']:$data['upload_name']=NULL;
            $_POST['file_describe']?$data['file_describe']=$_POST['file_describe']:$data['file_describe']=NULL;
            $data['class']=$_POST['class'];
            $data['order_id']=$_COOKIE['orderDetail_orderId'];
        }
        if (!empty($_FILES)){
            $file = $_FILES['file'];
        }
        //上传文件
        $res=upload('file','File',array(array(50,50)));
        if ($res){
            //print_r($res) ;
            
            $data['file_time'] = time();
            $data['file_img'] = $res['images'];
            $data['file_type']=pathinfo($res['images'], PATHINFO_EXTENSION);
            $_POST['file_name']?$data['file_name']=$_POST['file_name']:$data['file_name']=$file['name'];
            
            
            $inser = $dealer->insertGoods('xgj_furnish_order_file', $data);
            if ($inser){
                //echo '上传信息已成功插入数据库';
                header("location:dealer.php?orderDetail");
            }
        }        
    }
    
    //高级搜索
    public function advancedSearch(){
        
        $dealer = new dealer_model();
        $tpl = get_smarty();
        $dealerInfo = $dealer->getDealerInfo($_COOKIE['dealerId']);//经销商信息
        if (!empty($_POST)){
            //print_r($_POST);
            if($_POST['start_time']=='时间选择器'||$_POST['end_time']=='时间选择器'||empty($_POST['start_time'])||empty($_POST['end_time'])){
                echo "<script type='text/javascript'>alert('请将开始时间和结束时间都选择');window.location.href='dealer.php?advancedSearch';</script>";
                //header("Location:dealer.php?advancedSearch");
                exit();
            }
            
            $data = $_POST;
            $start_time = strtotime($data['start_time']);
            //echo $start_time;
            $end_time = strtotime($data['end_time']);
            $pay_status = $data['pay_status'];
            $schedule_status = $data['quote_status'];
            $searchResult = $dealer->advancedSearch($start_time,$end_time,$pay_status,$schedule_status);
            //print_r($searchResult);
            $tpl->assign('searchResult',$searchResult);
        }
        
    
        $tpl->assign('dealerInfo',$dealerInfo);
 
        $tpl->display('dealer/dealer_advancedSearch_xin.tpl.html');
    }
    
    // //调整订单
    // public function editOrder(){
    //  
    //  $dealer = new dealer_model();
    //  $tpl = get_smarty();
        
    //  //print_r($_GET);
    //  $order_id = $_GET['order_id'];
    //  $dealerInfo = $dealer->getDealerInfo($_COOKIE['dealerId']);//经销商信息
        
    //  $orderInfo = $dealer->getOrderInfo($order_id);
        
    //  $arr = explode(',', $orderInfo['house_layout']);
    //  //print_r($arr);
    //  $orderInfo['shi'] = $arr[0];
    //  $orderInfo['ting'] = $arr[1];
    //  $orderInfo['chu'] = $arr[2];
    //  $orderInfo['wei'] = $arr[3];
    //  $orderInfo['yangtai'] = $arr[4];
        
    //  //print_r($orderInfo);
        
    //  $tpl->assign('dealerInfo',$dealerInfo);
    //  $tpl->assign('orderInfo',$orderInfo);
    //  $tpl->display('editOrder.tpl.html');
    // }
    
    // //执行订单调整
    // public function doEditOrder(){
    //  
    //  $dealer = new dealer_model();
    //  print_r($_POST);
    //  $houseLayout = '';
    //  if (!empty($_POST)){
    //      $houseLayout .=$_POST['shi'].','; 
    //      $houseLayout .=$_POST['ting'].',';
    //      $houseLayout .=$_POST['chu'].',';
    //      $houseLayout .=$_POST['wei'].',';
    //      $houseLayout .=$_POST['yangtai'];
            
            
    //      $order_id = $_POST['order_id'];
            
    //      $arr = array();
    //      $arr['consignee'] = $_POST['consignee'];
    //      $arr['mobile_phone'] = $_POST['mobile_phone'];
    //      $arr['total_area'] = $_POST['total_area'];
    //      $arr['house_layout'] = $houseLayout;
            
    //      $where = "order_id=$order_id";
    //      $res = $dealer->editOrder('xgj_furnish_order_info', $arr, $where);
    //      if ($res){
    //          echo '订单调整成功';
    //          header("location:dealer.php?orderDetail");
    //      }
    //  }
        
   // }
    
    /*********************************************************************************************/
    //调整订单
    function adjust_order(){
        $dealer = new dealer_model();
        $tpl = get_smarty();
        $order_id  = $_GET['order_id'];
        $detail_id = $_GET['detail_id'];

        $true = $dealer->isTrue(array('order_id'=>$order_id,'detail_id'=>$detail_id),$_COOKIE['dealerId']);

        if (empty($true)) {
            echo jump(2,'找不到此订单','dealer.php?order');exit;
        }

        $dealerInfo = $dealer->getDealerInfo($_COOKIE['dealerId']);//经销商信息

        $orderInfo = $dealer->getOrderInfo($order_id);

        $dealerAdjustInfo = $dealer->getDealerAdjustInfo($orderInfo['order_code']);

        /*********房屋信息**********/
        $homeInfo = $dealer->getFind('xgj_furnish_order_info','order_id = '.$order_id);

        if (!empty($dealerAdjustInfo)) {
            $tpl->assign('dealerAdjustInfo',1);
            $homeInfo = $dealerAdjustInfo;
            $homeInfo['area'] = $dealerAdjustInfo['type_area'];
        }else{
            $homeInfo = $homeInfo;
        }
    
        $house_layout = explode(',', $homeInfo['house_layout']);
   
        $Tarea = explode('|', $homeInfo['area']);

        $area['1'] =  explode(',', $Tarea['0']);
        $area['2'] =  explode(',', $Tarea['1']);
        $area['3'] =  explode(',', $Tarea['2']);
        $area['4'] =  explode(',', $Tarea['3']);
        $area['5'] =  explode(',', $Tarea['4']);
        $area['6'] =  explode(',', $Tarea['5']);
        $area['7'] =  explode(',', $Tarea['6']);

        $quoteInfo = $dealer->getQuote($order_id);
        $getimage = $dealer->getimage($order_id);
        /***************************/

        $tpl->assign('detail_id',$detail_id);
        $tpl->assign('quoteInfo',$quoteInfo);
        $tpl->assign('getimage',$getimage);
        $tpl->assign('homeInfo',$homeInfo);
        $tpl->assign('area',$area);
        $tpl->assign('house_layout',$house_layout);
        $tpl->assign('orderInfo',$orderInfo);
        $tpl->assign('dealerInfo',$dealerInfo);
        $tpl->assign('order_sn',$orderInfo['order_code']);
        $tpl->display('dealer/dealer_adjust_order.tpl.html');
    }

    function  do_adjust_order(){    

        $dealer = new dealer_model();

        $order_id = $_POST['order_id'];

        $detail_id = $_POST['detail_id'];

        if (empty($order_id) || !preg_match('/^[0-9]+$/', $order_id)) {
            echo '<script>alert("请勿非法操作！");history.go(-1)</script>';exit;
        }

        if (empty($detail_id) || !preg_match('/^[0-9]+$/', $detail_id)) {
            echo '<script>alert("请勿非法操作！");history.go(-1)</script>';exit;
        }
       

        if (!empty($_FILES['img']['name'])){
            $file = $_FILES['img'];
        }else{
            echo jump(2,'请上传图片','dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id);
            header('refresh:3;url="dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id.'"' );
            exit;
        }
       
        //上传文件
        $res=upload('img','Autograph');

        if ($res){
            $data['order_id'] = $order_id;
            $data['file_img'] = $res['images'];
            $data['file_name'] = '订单房型面积确认单';
            $data['file_time'] = time();
            $data['upload_name'] = $_COOKIE['dealerName'];
            $data['class'] = 2;
            $data['file_type'] = pathinfo($res['images'], PATHINFO_EXTENSION);
            $data['file_describe'] = '订单房型面积确认单';
            $data['status']=3;

            $select = $dealer->getimage($order_id);
            if (!empty($select)) {
                $inser = $dealer->editOrder('xgj_furnish_order_file', $data, 'file_id='.$select['file_id']);
                if (!empty($inser)) {
                    $i = IMG_rootPath.$select['file_img'];
                    @unlink($i);
                }
            }else{
                $inser = $dealer->insertGoods('xgj_furnish_order_file', $data);
            }

            if (empty($inser)){
                echo jump(2,'图片上传失败','dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id);
            header('refresh:3;url="dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id.'"' );
                exit;
            }
        }else{
            echo jump(2,'图片上传失败','dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id);
            header('refresh:3;url="dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id.'"' );
            exit;
        }

       if ($_POST['house'] == 1) {
           $_POST['type6'] = '';
           $_POST['type7'] = '';
           $_POST['gelou'] = '';
           $_POST['database'] = '';
       }

       if($_POST){  
        $housetype="";  
        $area1="";
        $area2="";
        $area3="";
        $area4="";
        $area5="";
        $area6="";
        $area7="";
        $type1="";
        $type2="";
        $type3="";
        $type4="";
        $type5="";
        $type6="";
        $type7="";
        
           
           //房屋类型
           if(!empty($_POST['house']))
           {
            $housetype=$_POST['house'];
           }
           
    
           // 室
            if(!empty($_POST['type1']))
            {
               $type1=$_POST['type1'];
            }else{
                $type1=0;
            }
            // 厅
           if(!empty($_POST['type2']))
            {
               $type2=$_POST['type2'];
            }else{
                $type2=0;
            }
            // 厨
          if(!empty($_POST['type3']))
            {
               $type3=$_POST['type3'];
            }else{
                $type3=0;
            }
            // 卫
          if(!empty($_POST['type4']))
            {
               $type4=$_POST['type4'];
            }else{
                $type4=0;
            }
            // 阳台
          if(!empty($_POST['type5']))
            {
               $type5=$_POST['type5'];

            }else{
                $type5=0;
            }

         
            // 阁楼
          if(!empty($_POST['type6']))
            {
               $type6=$_POST['type6'];
            }else{
                $type6='0';
            }
            
            // 地下室
            
          if(!empty($_POST['type7']))
            {
               $type7=$_POST['type7'];
            }else{
                $type7=0;
            }
                
             if(empty($type1))
             {
                $type1="0";
             }
                
             $sum=$type1+$type6+$type7;
             
             $str=$sum;

         
              
             if(!empty($type2))
             {
                 $str.=",".$type2;
             
             }else{
                $str.=",0";
             }

             if(!empty($type3))
             {
                $str.=",".$type3;
             }else{
                $str.=",0";
             }

             if(!empty($type4))
             {
                $str.=",".$type4;
             }else{
                $str.=",0";
             }
        
             if(!empty($type5))
             {
                $str.=",".$type5;
             }else{
                $str.=",0";
             }
             
            
             // 全屋面积
             if(!empty($_POST['area']))
             {
                 $area=$_POST['area'];
             }
             
             
             //室面积
             if(!empty($_POST['bedroom']))
             {
               $arr1=$_POST['bedroom'];
             }else{
                $arr1=0;
             }

             if(!empty($arr1))
             {  
                $area1=implode(",", $arr1);
                $area_1=$area1;
                $area_1_1 = $area_1;
             }
     
             //厅面积
             if(!empty($_POST['liveroom']))
             {
                $arr2=$_POST['liveroom'];  
             }else{
                $arr2=0;
             }
               
             //厨房面积
             if(!empty($_POST['kitchen']))
             {
              $arr3=$_POST['kitchen'];
             }else{
                $arr3=0;
             }
             //浴室面积
             if (!empty($_POST['bathroom']))
             {
              $arr4=$_POST['bathroom'];
             }else{
                $arr4=0;
             }
             //阳台面积
             if(!empty($_POST['balcony']))
             {
               $arr5=$_POST['balcony'];
             }else{
                $arr5=0;
             }
             //阁楼面积
             if(!empty($_POST['gelou']))
             {
              $arr6=$_POST['gelou'];
             }else{
                $arr6=0;
             }
             if(!empty($_POST['database']))
             {
             //地下室面积
              $arr7=$_POST['database'];
             }else{
                $arr7=0;
             }

             if ($arr1==0) {
            $area1 ='';
           }

           if ($arr2==0) {
            $area2 ='';
           }

           if ($arr3==0) {
            $area3 ='';
           }
           if ($arr4==0) {
            $area4 ='';
           }
           if ($arr5==0) {
            $area5 ='';
           }
           if ($arr6==0) {
            $area6 ='';
           }
           if ($arr7==0) {
            $area7 ='';
           }


           if(!empty($arr2) || $arr2==0)
           {
            if ($arr2!=0) {
                 $area2=implode(",", $arr2);
            }
         
           }
             
             
           
           if(!empty($arr3) || $arr3==0)
           {
            if ($arr3!=0) {
                 $area3=implode(",", $arr3);
            }
         
           }
           
  

         
           if(!empty($arr4) || $arr4==0)
           {
            if ($arr4!=0) {
                $area4=implode(",", $arr4);
            }
            
           }
            
           
             if(!empty($arr5))
            {
               $area5=implode(",", $arr5);
               
            } 
            
            if(!empty($arr6))
            {
               $area5=implode(",", $arr6);
               
            } 

            if(!empty($arr7))
            {
               $area5=implode(",", $arr7);
               
            } 
            
            $str2=$area1."|".$area2."|".$area3."|".$area4."|".$area5."|".$area6."|".$area7;
          
        }
        if ($housetype=='') {
            echo jump(2,'提交失败','dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id);
            header('refresh:3;url="dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id.'"' );
            exit;
        }
        
       
        if (!empty($arr1)) {
            foreach ($arr1 as $key => $value) {
                if ($value=='') {
                    echo jump(2,'提交失败','dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id);
                    header('refresh:3;url="dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id.'"' );
                    exit;
                }
            }  

        }

        if (!empty($arr2)) {
            foreach ($arr2 as $key => $value) {
                if ($value=='') {
                    echo jump(2,'提交失败','dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id);
                    header('refresh:3;url="dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id.'"' );
                    exit;
                }
            }
        }

        if (!empty($arr3)) {
            foreach ($arr3 as $key => $value) {
                if ($value=='') {
                    echo jump(2,'提交失败','dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id);
                    header('refresh:3;url="dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id.'"' );
                    exit;
                }
            }
        }

        if (!empty($arr4)) {
            foreach ($arr4 as $key => $value) {
                if ($value=='') {
                    echo jump(2,'提交失败','dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id);
                    header('refresh:3;url="dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id.'"' );
                    exit;
                }
            }
        }

        if (!empty($arr5)) {
            foreach ($arr5 as $key => $value) {
                if ($value=='') {
                    echo jump(2,'提交失败','dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id);
                    header('refresh:3;url="dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id.'"' );
                    exit;
                }
            }
        }

        if (!empty($arr6)) {
            foreach ($arr6 as $key => $value) {
                if ($value=='') {
                    echo jump(2,'提交失败','dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id);
                    header('refresh:3;url="dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id.'"' );
                    exit;
                }
            }
        }

        if (!empty($arr7)) {
            foreach ($arr7 as $key => $value) {
                if ($value=='') {
                    echo jump(2,'提交失败','dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id);
                    header('refresh:3;url="dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id.'"' );
                    exit;
                }
            }
        }

        $attic = $type6;
        $basement = $type7;
        $attic_area = $area6;
        $basement_area = $area7;

        $house_data = $dealer->getDealerAdjustInfo($_POST['order_sn']);

        $detailCity = $dealer->getFind('xgj_furnish_order_info','order_id = '.$order_id);

        $pcda = explode('-', $detailCity['house_city']);

        $orderInfo = $dealer->getOrderInfo($order_id);

        $homeInfo['house_id']     =$orderInfo['house_id'];
        $homeInfo['user_id']      =$orderInfo['user_id'];
        $homeInfo['order_code']   =$_POST['order_sn'];
        $homeInfo['house_layout'] =$str;
        $homeInfo['total_area']   =$area;
        $homeInfo['type_area']    =$str2;
        $homeInfo['province']     =$pcda['0'];
        $homeInfo['city']         =$pcda['1'];
        $homeInfo['district']     =$pcda['2'];
        $homeInfo['address']      =$pcda['3'];
        $homeInfo['house_type']   =$housetype;
        $homeInfo['people']       =$detailCity['people'];

        if (empty($house_data)) {
            $houseid=$dealer->addOrder('xgj_dealer_adjust_info',$homeInfo);
        //如果成功，获取系统id
            if($houseid==true){
                $house_data['id'] = $houseid;
                $jump = 1;
            }else{
                echo jump(2,'提交失败','dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id);
                header('refresh:3;url="dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id.'"' );
                exit;
            }
        }else{
            $return = $dealer->editOrder('xgj_dealer_adjust_info',$homeInfo,'id='.$house_data['id']);
            if($return==true){
                $jump = 1;
            }else{
                echo jump(2,'提交失败','dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id);
                header('refresh:3;url="dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id.'"' );
                exit;
            }

        }
        
        if ($jump == 1) {
            
            $homesarea = explode('|', $homeInfo['type_area']);
            array_pop($homesarea);
            $homesarea['0'] = array_sum(explode(',', $homesarea['0']));
            $homesareas = array_sum($homesarea);

            $quote_id = $dealer->getQuote($order_id);

            require_once(WWW_DIR . "/classes/priceController.php");

            $order = new priceController();

            foreach ($quote_id as $key => $value) {

                $lists[$key] = $order->get_price($value['quote_id'],$house_data['id'],$value['level']);

                if ($lists[$key]=='error' || $value['quote_id']>=29 && $value['quote_id']<=31 && $value['level']==3 && $homesareas>230) {
                    $detaildata['adjust_stuff_goods'] = '';
                    $detaildata['adjust_quote_price'] = '';
                    $detaildata['adjust_cost'] = '';
                }else{
                    $money[$key] = $lists[$key]['2'];
                    $detaildata['adjust_stuff_goods'] = $lists[$key]['1'];

                    //处理调整后系统总价
                    $sale = $dealer->getField('xgj_furnish_quote',"quote_id = ".$value['quote_id'],'sale');
  
                    $detaildata['adjust_quote_price'] = ceil(($money[$key]['all']-$money[$key]['install'])/100*$sale)+$money[$key]['install'];
                    $detaildata['adjust_cost'] = $money[$key]['install'];

                }



                $return1[$key] = $dealer->editOrder('xgj_furnish_order_detail', $detaildata, "detail_id=".$value['detail_id']);
            }
            
            $quoteInfo = $dealer->getQuote($order_id);

            foreach ($quoteInfo as $key => $value) {
                // $adjust_zp_money[] = $value['adjust_quote_price']*0.1;
                $adjust_goods_amount[] = $value['adjust_quote_price'];
            }

            // $detaildatas['adjust_zp_money'] = round(array_sum($adjust_zp_money));
            $detaildatas['adjust_goods_amount'] = round(array_sum($adjust_goods_amount));

            $detaildatas['order_status'] = 5;

            $return2 = $dealer->editOrder('xgj_furnish_order_info', $detaildatas, "order_code=".$_POST['order_sn']);

            echo jump(1,'提交成功','dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id);
            header('refresh:3;url="dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id.'"' );
            exit;
        }


    }
   
    
    //退出
    public function logOut(){
        setcookie("dealerId","",time()-1000);
        setcookie("dealerName","",time()-1000);
        header("Location:dealer.php");
        
    }
    
    //申请结算
    public function getaccount(){
        $dealer = new dealer_model();
        $tpl = get_smarty();
        if($_POST['getMoneyNum'] > $_POST['totalMoney']){
            echo "<script type='text/javascript'>alert('余额不足，不能提交申请');history.back();</script>";
            exit();
        }
        $info=$dealer->checkAccount($_COOKIE['dealerId']);
        if($info > 0){
            echo jump(2,'已提交申请，审核完成后，方可再次申请','dealer.php?account');
                header("refresh:3;url='dealer.php?account'" );
                exit;
        }
        // $check = $dealer->checkOrderId($_GET['order_id']);
        // if (!empty($check)){
        //  //header("location:dealer.php?account");
        //  echo " <script type='text/javascript'>alert('已经申请过结算，请不要重复申请');history.back();</script>";
        //  exit();
        // }
        //print_r($_GET);
        $data = array();
        //$data['order_id'] = $_GET['order_id'];
        $data['d_id'] = $_COOKIE['dealerId'];
        $data['money']=$_POST['getMoneyNum'];
        $data['finance_status'] = 0;
        $data['apply_time'] = time();
        
        //exit();
        $result = $dealer->getaccount('xgj_furnish_finance',$data);
        if ($result){ 
                echo jump(1,'申请结算成功，请耐心等待审核','dealer.php?account');
                header("refresh:3;url='dealer.php?account'" );
                exit;
            }else {
                echo jump(2,'申请结算失败，请重新申请','dealer.php?account');
                header("refresh:3;url='dealer.php?account'" );
                exit;
            }
    }
    
    
    // //申请提现
    // public function getMoney(){
    //  $dealer = new dealer_model();
    //  $tpl = get_smarty();
        
    //  $dealerInfo = $dealer->getDealerInfo($_COOKIE['dealerId']);//经销商信息
        
    //  if (!empty($_POST)){
    //      if (empty($_POST['getMoneyNum'])){
    //          echo "<script type='text/javascript'>alert('请填写提现的金额');history.back();</script>";
    //          exit();
    //      }
            
    //      $dealerId = $_COOKIE['dealerId'];
            
    //      $totalMoney = $_POST['totalMoney'];
    //       if ($_POST['getMoneyNum']>$totalMoney){
    //          $tpl->display('dealer_error.tpl.html');
    //          echo "<script type='text/javascript'>alert('申请提现金额大于账户余额，请重新填写！');history.back();</script>";
    //          exit();
    //      }  
    //      //正则过滤数据
    //       if (!preg_match('/^[1-9][0-9]*(.?[0-9]*)?$/', $_POST['getMoneyNum'])){
                
    //          $tpl->display('dealer_error.tpl.html');
    //          echo "<script type='text/javascript'>alert('提现金额数据非法');history.back();</script>";
    //      }else {
    //          echo '提现金额数据合法';
    //      } 
    //      //print_r($_POST);
            
            
    //      $data = array();
    //      $data['d_id'] = $_COOKIE['dealerId'];;
    //      $data['money_num'] = $_POST['getMoneyNum'];
    //      $data['remarks'] = $_POST['remarks'];
    //      $data['apply_time'] = time();
    //      $result = $dealer->getMoney('xgj_furnish_get_money',$data);
    //      if ($result){
    //          echo "<script type='text/javascript'>alert('申请提现成功，请耐心等待审核');history.back();</script>";
    //      }else {
    //          echo "<script type='text/javascript'>alert('申请提现失败，请重新申请');history.back();</script>";
                
    //      }
    //  }   
        //$tpl->display();
        
    // }
    
    
}