<?php
namespace Home\Controller;
use Think\Controller;
class DealerController extends Controller {

    private $dealerPage = 5; //经销商账户分页每页显示数
    
   public  function _initialize(){
	   layout(false); 
        if(ACTION_NAME !='index' && ACTION_NAME !='login' ){
            if (!isset($_SESSION['dealerId']) || !isset($_SESSION['dealerName'])) 
			   $this->redirect('Dealer/index');
			
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


    function index() {
        if (!empty($_SESSION['dealerId'])) {
            $this->redirect('Dealer/order');
        }else{
            $this->display();
        }
    }
    
    function login(){
        if (!empty($_SESSION['dealerId'])) {
            $this->redirect('Dealer/order');
        }else{
            $pwd = I('pwd');
			$where['d_name'] = I('username');
			if ($where['d_name']!=''and $pwd!=''){
            
				$arr = D('dealer')->login($where);
				$pwd = md5($pwd);

                if (empty($arr)) {
                    $this -> alert('用户名不存在');
                }else if ($pwd === $arr['d_pwd']) {
                    $_SESSION['dealerId']   = $arr["d_id"];
                    $_SESSION['dealerName'] = $arr["d_name"];
                    $_SESSION['d_company']  = $arr["d_company"];
                    $this->redirect('Dealer/order');
                }else{
                    $this -> alert('用户名与密码不匹配');
                }
			}else{
				$this -> alert('用户名和密码必须填写');
			}
        }
        
    }

  public  function order(){
        
       
		$where['d_id']=$_SESSION['dealerId'];
		$count = M('xgj_furnish_order_info')->where($where)->count();
		$show       = getPage($count,8);	
		$data = D('dealer')->dealer_order($where,$show['limit']);	
		//var_dump($data);die();
		$this->assign("dealer_order",$data);	
		$this->assign("page_nav",$show['page']);
		$this->assign('actname','订单管理');
        $this->display();
    }
    
    //订单搜索
    public function orderSearch(){
        
        if (!empty(I('search'))&&I('search')!='订单号/ 联系人/手机号'){
            
            $data = I('search');
            
        }else{
			 redirect('order','3','搜索内容不能为空');
		}
               
        
		$where="d_id = '".$_SESSION['dealerId']."' and (order_code LIKE '%".$data."%' or  consignee LIKE '%".$data."%' or  mobile_phone LIKE '%".$data."%' )";
        $count = M('xgj_furnish_order_info')->where($where)->count();
		$show       = getPage($count,8);	
        $dealer_order = D('dealer')->orderSearch($where,$show['limit']);
         
        $this->assign("page_nav",$show['page']);//订单分页
        $this->assign('dealer_order',$dealer_order);
		$this->assign('actname','订单管理');
        $this->display('order');
        
    }
    
	//高级搜索
    public function advancedSearch(){
        
        if (!empty($_POST)){
            //print_r($_POST);
              if(empty(I('start_time'))||empty(I('end_time'))){
                redirect('advancedSearch','3','请选择起始时间');
            }
            //d_id = $id and pay_status=$pay_status and schedule_status=$schedule_status and add_order_time>=".$start_time." and add_order_time<=".$end_time;"
			$where['d_id']=$_SESSION['dealerId'];
            $where['add_order_time'] = strtotime($_POST['start_time']);
            $where['add_order_time'] = strtotime($_POST['end_time']);
            $where['pay_status'] = $_POST['pay_status'];
            $where['schedule_status'] = $_POST['quote_status'];
            $searchResult = D('dealer')->advancedSearch($where);
            //print_r($searchResult);
            $this->assign('searchResult',$searchResult);
        }
        
    		
		$this->assign('actname','订单管理');
        $this->display();
    }


 //订单详情
    public function orderDetail(){
        
        if (!empty($_GET['order_id'])){
            $order_id = I('order_id');
        }else {
             redirect('order');
        }
        
  
         //系统分类
		$where['a.order_id']=$order_id;       
        $systemData = D('dealer')->systemData($where);

       ;
        
        
        //施工计划  
		
		$where['a.d_id']= $_SESSION['dealerId'];  
        $dataPlan = D('dealer')->constructPlan($where);
		//var_dump( '<pre>',$_SESSION['dealerId'],$dataPlan);die();
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
            
            $this->assign('dataPlans',$planDatas);
            $this->assign('dataPlanss',$planDatass);
        }
        
     
        
        
        //订单基本信息
		$order['a.order_id']=$order_id;       
        $orderInfo = D('dealer')->orderInfo($order);
        
        $orderInfo['house_layout'] = explode(',', $orderInfo['house_layout']);
        // echo '<pre>';
        // var_dump($orderInfo);exit;
                
        $orderInfoDetail = D('dealer')->orderInfoDetail($order);


        //文件区域
        $dealerOrderFile = D('dealer')->dealerOrderFile($order_id);
        
            for ($i=0;$i < count($dealerOrderFile);$i++){
                $dealerOrderFile[$i]['file_txt'] = 'txt.jpg';
                $dealerOrderFile[$i]['file_doc'] = 'doc.jpg';
                $dealerOrderFile[$i]['file_docx'] = 'docx.jpg';
            }

        
        
        $this->assign('orderInfo',$orderInfo);
        $this->assign('dealerInfo',$dealerInfo);
        $this->assign('orderInfoDetail',$orderInfoDetail);
        
        $this->assign('systemData',$systemData);

        $this->assign('dataCheck',$dataCheck);
        
        
        $this->assign('dealerOrderFile',$dealerOrderFile);
		$this->assign('actname','订单详情');
        $this->display();
    }

    //文件上传
    public function uploadFile(){
        
        
        if (empty($_POST['order_id'])||empty($_FILES)||empty($_POST['file_name'])||empty($_POST['file_type'])||empty($_POST['upload_name'])){
           $this -> alert('上传内容请完整填写');
        }
        //上传文件
        //$res=upload('file','File',array(array(50,50)));
		$res = uploadOne('file','File','',C('DEALER_img'),'');
	    if($res['code']!=1){
	           //头像上传失败
	           $this->error = $res['error'];
			   
	           return false;
	    }
        
        $data['order_id'] = I('order_id');
        $data['file_time'] = time();
		$data['file_name'] =I('file_name');
        $data['file_img'] = $res['images'];
		$data['upload_name'] = I('upload_name');
		$data['class'] = I('class');
		$data['file_describe'] = I('file_describe');
        $data['file_type']=pathinfo($res['images'], PATHINFO_EXTENSION);
            
        $inser = D('dealer')->insertGoods('xgj_furnish_order_file', $data);
        if ($inser){
            redirect('orderDetail/order_id/'. I('order_id').'.html');
       }
              
    }


//接收并处理施工安排部分的数据
    public function addPlan(){
        
        
        
        $quote_name = $_POST['quote_name'];
        $arr = explode('-', $quote_name);
        $order_id = $arr[0];
        $detail_id = $arr[1];
        
        $rs=D('dealer')->checkConstructPlan($arr[1],$_POST['task_work']);
        
        if($rs > 0){
             redirect('orderDetail/order_id/'.$order_id.'.html');
        }else{
            $data = array();
            if (!empty($_POST)){
                $data['order_id'] = $arr[0];
                $data['detail_id'] = $arr[1];
                $data['d_id'] = $_SESSION['dealerId'];
                $data['task_work'] = $_POST['task_work'];
                $data['task_name'] = $_POST['task_name'];
                $data['assigner'] = $_POST['assigner'];
                $data['start_time'] = strtotime($_POST['start_time']);
                $data['end_time'] = strtotime($_POST['end_time']);
                $data['status'] = 1;
                if($_POST['task_work']=='1' || $_POST['task_work']=='2'){
                    $info=D('dealer')->getQuote($data['order_id']);
					//var_dump( $info);die();
                    foreach ($info as $k=>$v){
                        $data_['order_id']   = $arr[0];
                        $data_['detail_id']  = $v['detail_id'];
                        $data_['d_id']       = $_SESSION['dealerId'];
                        $data_['task_work']  = $_POST['task_work'];
                        $data_['task_name']  = $_POST['task_name'];
                        $data_['assigner']   = $_POST['assigner'];
                        $data_['start_time'] = strtotime($_POST['start_time']);
                        $data_['end_time']   = strtotime($_POST['end_time']);
                        $data_['status']     = 1;
                        $addPlan = D('dealer')->insertGoods('xgj_furnish_order_construct', $data_);
                        if ($addPlan && $v['plan_settle']!=1){
                            //施工计划安排成功后需要将订单详情表里的plan_settle字段设置为1
                            $planSettle = array();
                            $planSettle['plan_settle'] = 1;
                            $where = "order_id=$order_id and detail_id={$v['detail_id']}";
                            $settleChange = D('dealer')->updatePlan('xgj_furnish_order_detail', $planSettle, $where);
                            //var_dump($settleChange);exit;
                            if (!$settleChange){
                               $this->redirect('Dealer/orderDetail',array('order_id' => $order_id), 3, '订单安排状态修改失败...'); 
                            }
                        }
                         
                    }
                    $this->redirect('Dealer/orderDetail',array('order_id' => $order_id)); 
                }else{
                    $addPlan = D('dealer')->insertGoods('xgj_furnish_order_construct', $data);
                    if ($addPlan){
                        //echo '<hr />';
                        echo '施工计划安排添加成功';
                        //施工计划安排成功后需要将订单详情表里的plan_settle字段设置为1
                        $planSettle = array();
                        $planSettle['plan_settle'] = 1;
                        $where = "order_id=$order_id and detail_id=$detail_id";
                        $settleChange = D('dealer')->updatePlan('xgj_furnish_order_detail', $planSettle, $where);
                        if ($settleChange){
                            echo '订单安排状态已改变'.'<br />';
                        }
                        //exit();
                        $this->redirect('Dealer/orderDetail',array('order_id' => $order_id)); 
                    }
                }
                
            }
        }
        
    }

    //施工步骤筛选
    public function chooseWork(){
        
        
        $arr = explode('-', $_POST['data']);
        
        
        $order_id = $arr[0];
        $detail_id = $arr[1];
        
        $workData = D('dealer')->chooseWork($order_id,$detail_id);
        
        $str = '';
        foreach ($workData as $v){
                
            $str .= " <option value='".$v['id']."'>".$v['task_name']."</option>";
                
        }
        
        
        echo $str;
    }

    function center(){
        $dealer_center = D('dealer')->dealer_center($_SESSION['dealerId']);
        $dealerInfo    = D('dealer')->getDealerInfo($_SESSION['dealerId']);//经销商信息
        $this->assign('dealerInfo',$dealerInfo);
        $this->assign('dealer_info',$dealer_center);
        $this->assign('actname','我的中心');
        $this->display();
    }

    function center_info(){

        if(isset($_POST) && $_POST!=null)
        {   
            //preg_match($pattern, $subject);
            if(D('dealer')->update_info($_POST));
            header("Location:dealer.php?center");exit;
        }
       
        $dealer_info= D('dealer')->dealer_center($_SESSION['dealerId']);
        //print_r($dealer_center);
        $tpl = get_smarty();
        $this->assign('dealer_info',$dealer_info);
        $this->display('dealer/dealer_info.tpl.html');
        
    }




    public function aftersaleInfo(){
        $dealerInfo = D('dealer')->getDealerInfo($_SESSION['dealerId']);//经销商信息

        if(!preg_match("/^[0-9]+$/", $_GET['id']) || !preg_match("/^9?(10)?$/", $_GET['class'])){
            echo "<script type='text/javascript'>alert('非法操作！');history.go(-1);</script>";exit;
        }

        $class = $_GET['class'];

        $list  = D('dealer')->getWorkOrder($_GET['id'],$class);
        
        $this ->assign('dealerInfo',$dealerInfo);
        $this ->assign('class',$class);
        $this ->assign('list',$list);
        $this->assign('actname','维修记录');
        $this->display();
    }
    

    function aftersale(){
        $dealerInfo = D('dealer')->getDealerInfo($_SESSION['dealerId']);//经销商信息

        /***********************************/
        //保养
        $list  = D('dealer')->getUpkeep();
        $count = count($list);
        $page  = getAjaxPage($count,$this->dealerPage);
        $res   = D('dealer')->getUpkeep($page['limit']);
        /***********************************/

        /***********************************/
        //维修
        $mList  = D('dealer')->getMaintain();
        $mCount = count($mList);
        $mPage  = getAjaxPage($mCount,$this->dealerPage);
        $mRes   = D('dealer')->getMaintain($mPage['limit']);
        /**********************************/
//var_dump($res);die;
        $this->assign('dealerInfo',$dealerInfo);
        $this->assign('list',$res);
        $this->assign('page',$page['page']);
        $this->assign('mList',$mRes);
        $this->assign('actname','售后管理');
        $this->assign('mPage',$mPage['page']);
        $this->display();

    }

    function aftersaleAjax(){
        
        $add =array(
            'o_id'       =>$_POST['o_id'],
            'd_id'       =>$_SESSION['dealerId'],
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

        $re = D('dealer')->addData('xgj_dealer_work_order',$add);
        if ($re>0 && $_POST['class']==9) {
            $res = D('dealer')->getGoodsOne($_POST['p_id']);
            if ($res===false) {
                echo 1;exit;
            }
        }else if ($re>0 && $_POST['class']==10 && $_POST['is_ok']==1) {
            $where = "order_id = {$_POST['o_id']} and id = {$_POST['p_id']}";
            $re = D('dealer')->saveData('xgj_user_problem',array('state'=>'1'),$where);
            if ($re !== false) {
                echo 1;exit;
            }
        }
        if ($re!==false) echo '添加成功！';
        else echo '添加失败！';
    }

    function aftersaleAjaxPage(){

        if (!empty($_GET['p']) && !empty($_GET['t'])) {
            $t = $_GET['t'];
            if ($t==1) {
                //保养 
                $list  = D('dealer')->getUpkeep();
                $count = count($list);
                $page  = getAjaxPage($count,$this->dealerPage);
                $res   = D('dealer')->getUpkeep($page['limit']);

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
                             / <a href="'.U('aftersaleInfo',array('class'=>9,'id'=>$v['gid'])).'">查看</a>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>';
                }
                $page['page'] .= "
                    <script>
                        $('.uPage .page a').click(function(){
                            var p = $(this).attr('data-page');
                            $.getJSON('".U('aftersaleAjaxPage')."',{'p':p,'t':1},function(re){
                                $('#uPageDiv').html(re.data);
                                $('.uPage').html(re.page);
                            })
                        })
                    </script>
                ";
            } else if($t==2) {
                //维修
                $list  = D('dealer')->getMaintain();
                $count = count($list);
                $page  = getAjaxPage($count,$this->dealerPage);
                $res   = D('dealer')->getMaintain($page['limit']);

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
                            '.date('Y-m-d ',$v["time"]).'
                        </div>
                        <div class="dealer_aftersale-center-tabs02-list-demo-time">
                        ';
                        if ($v['state'] == 0) {
                            $data .= '<a href="javascript:;" onclick="maintain('.$v["order_id"].','.$v["id"].')">添加记录</a>';
                        } else if($v['state'] == 1) {
                            $data .= '添加记录';
                        }
                        $data .= ' / <a href="'.U('aftersaleInfo',array('class'=>10,'id'=>$v['id'])).'">查看</a>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>';
                }
                $page['page'] .= "
                    <script>
                        $('.mPage .page a').click(function(){
                            var p = $(this).attr('data-page');
                            $.getJSON('".U('aftersaleAjaxPage')."',{'p':p,'t':2},function(re){
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










    // public function aftersaleInfo(){
    //     $dealerInfo = D('dealer')->getDealerInfo($_SESSION['dealerId']);//经销商信息

    //     if(!preg_match("/^[0-9]+$/", $_GET['id']) || !preg_match("/^9?(10)?$/", $_GET['class'])){
    //         echo "<script type='text/javascript'>alert('非法操作！');history.go(-1);</script>";exit;
    //     }

    //     $class = $_GET['class'];

    //     $list  = D('dealer')->getWorkOrder($_GET['id'],$class);
        
    //     $tpl ->assign('dealerInfo',$dealerInfo);
    //     $tpl ->assign('class',$class);
    //     $tpl ->assign('list',$list);
    //     $tpl ->display('dealer/dealer_aftersale_info.tpl.html');
    // }
    

    // function aftersale(){
    //     $dealerInfo = D('dealer')->getDealerInfo($_SESSION['dealerId']);//经销商信息

    //     /***********************************/
    //     //保养
    //     $list  = D('dealer')->getUpkeep();
    //     $count = count($list);
    //     $page  = getAjaxPage($count,$this->page);
    //     $res   = D('dealer')->getUpkeep($page['limit']);
    //     /***********************************/


    //     /***********************************/
    //     //维修
    //     $mList  = D('dealer')->getMaintain();
    //     $mCount = count($mList);
    //     $mPage  = getAjaxPage($mCount,$this->page);
    //     $mRes   = D('dealer')->getMaintain($mPage['limit']);
    //     /**********************************/

    //     $tpl = get_smarty();
    //     $tpl ->assign('dealerInfo',$dealerInfo);
    //     $tpl ->assign('list',$res);
    //     $tpl ->assign('page',$page['page']);
    //     $tpl ->assign('mList',$mRes);
    //     $tpl ->assign('mPage',$mPage['page']);
    //     $tpl ->display('dealer/dealer_aftersale_xin.tpl.html');

    // }

    // function aftersaleAjax(){
        
    //     $add =array(
    //         'o_id'       =>$_POST['o_id'],
    //         'd_id'       =>$_SESSION['dealerId'],
    //         'start_time' =>$_POST['start_time'],
    //         'end_time'   =>$_POST['end_time'],
    //         'content'    =>$_POST['content'],
    //         'task_name'  =>$_POST['task_name'],
    //         'remark'     =>$_POST['remark'],
    //         'add_time'   =>time(),
    //         'class'      =>$_POST['class'],
    //         'p_id'       =>$_POST['p_id'],
    //         );

    //     if ($_POST['class']==10) $add['is_ok'] = $_POST['is_ok'];

    //     foreach ($add as $k => $v) {
    //         if (empty($v)) {
    //             echo '请填写完整，在提交！';exit;
    //         }
    //     }

    //     $re = D('dealer')->addData('xgj_dealer_work_order',$add);
    //     if ($re>0 && $_POST['class']==9) {
    //         $res = D('dealer')->getGoodsOne($_POST['p_id']);
    //         if ($res===false) {
    //             echo 1;exit;
    //         }
    //     }else if ($re>0 && $_POST['class']==10 && $_POST['is_ok']==1) {
    //         $where = "order_id = {$_POST['o_id']} and id = {$_POST['p_id']}";
    //         $re = D('dealer')->saveData('xgj_user_problem',array('state'=>'1'),$where);
    //         if ($re !== false) {
    //             echo 1;exit;
    //         }
    //     }
    //     if ($re!==false) echo '添加成功！';
    //     else echo '添加失败！';
    // }

    // function aftersaleAjaxPage(){

    //     if (!empty($_GET['p']) && !empty($_GET['t'])) {
    //         $t = $_GET['t'];
    //         if ($t==1) {
    //             //保养 
    //             $list  = D('dealer')->getUpkeep();
    //             $count = count($list);
    //             $page  = getAjaxPage($count,$this->page);
    //             $res   = D('dealer')->getUpkeep($page['limit']);

    //             $data = '';
    //             foreach ($res as $k => $v) {
    //                 $data .='<div class="dealer_aftersale-center-tabs01-list-demo">
    //                     <div class="dealer_aftersale-center-tabs01-list-demo-order">
    //                         '.$v["sn"].'
    //                     </div>
    //                     <div class="dealer_aftersale-center-tabs01-list-demo-contacts">
    //                         '.$v["shr_name"].'
    //                     </div>
    //                     <div class="dealer_aftersale-center-tabs01-list-demo-phone">
    //                         '.$v["shr_phone"].'
    //                     </div>
    //                     <div class="dealer_aftersale-center-tabs01-list-demo-address">
    //                         '.$v["shr_pro"].' '.$v["shr_city"].' '.$v["shr_area"].' '.$v["shr_addr"].'
    //                     </div>
    //                     <div class="dealer_aftersale-center-tabs01-list-demo-order">
    //                         '.$v["goods_title"].'
    //                     </div>
    //                     <div class="dealer_aftersale-center-tabs01-list-demo-time" style="width:140px;">
    //                         '.$v["pay_time"].'
    //                     </div>
    //                     <div class="dealer_aftersale-center-tabs01-list-demo-order">
    //                         <a href="javascript:;" onclick="upkeep('.$v["id"].','.$v["gid"].')">添加记录</a>
    //                          / <a href="dealer.php?aftersaleInfo&class=9&id='.$v["gid"].'">查看</a>
    //                     </div>
    //                     <div class="clear"></div>
    //                 </div>
    //                 <div class="clear"></div>';
    //             }
    //             $page['page'] .= "
    //                 <script>
    //                     $('.uPage a').click(function(){
    //                         var p = $(this).attr('data-page');
    //                         $.getJSON('dealer.php?aftersaleAjaxPage',{'p':p,'t':1},function(re){
    //                             $('#uPageDiv').html(re.data);
    //                             $('.uPage').html(re.page);
    //                         })
    //                     })
    //                 </script>
    //             ";
    //         } else if($t==2) {
    //             //维修
    //             $list  = D('dealer')->getMaintain();
    //             $count = count($list);
    //             $page  = getAjaxPage($count,$this->page);
    //             $res   = D('dealer')->getMaintain($page['limit']);

    //             $data = '';
    //             foreach ($res as $k => $v) {
    //                 $data .='<div class="dealer_aftersale-center-tabs02-list-demo">
    //                     <div class="dealer_aftersale-center-tabs02-list-demo-order">
    //                         '.$v["order_code"].'
    //                     </div>
                        
    //                     <div class="dealer_aftersale-center-tabs02-list-demo-contacts">
    //                         '.$v["name"].'
    //                     </div>
                        
    //                     <div class="dealer_aftersale-center-tabs02-list-demo-phone" style="width:100px;">
    //                         '.$v["phone"].'
    //                     </div>
                        
    //                     <div class="dealer_aftersale-center-tabs02-list-demo-address" style="width:160px;">
    //                         '.$v["province"].$v["city"].$v["district"].$v["address"].'
    //                     </div>
                        
    //                     <div class="dealer_aftersale-center-tabs02-list-demo-with_single" style="width:140px;">
    //                         '.$v["quote_name"].'
    //                     </div>
                        
    //                     <div class="dealer_aftersale-center-tabs02-list-demo-substance">
    //                         ';
    //                     if ($v['state'] == 0) {
    //                         $data .= '等待解决';
    //                     } else if($v['state'] == 1) {
    //                         $data .= '已解决';
    //                     }
    //                     $data .= '</div>
    //                     <div class="dealer_aftersale-center-tabs02-list-demo-progress">
    //                         <input type="hidden" id="lookData" value="'.$v["note"].'">
    //                         <a href="javascript:;" onclick="look()">查看</a>
    //                     </div>                    
                        
    //                     <div class="dealer_aftersale-center-tabs02-list-demo-time" style="width:140px;">
    //                         '.date('Y-m-d H:i:s',$v["time"]).'
    //                     </div>
    //                     <div class="dealer_aftersale-center-tabs02-list-demo-time">
    //                     ';
    //                     if ($v['state'] == 0) {
    //                         $data .= '<a href="javascript:;" onclick="maintain('.$v["order_id"].','.$v["id"].')">添加记录</a>';
    //                     } else if($v['state'] == 1) {
    //                         $data .= '添加记录';
    //                     }
    //                     $data .= ' / <a href="dealer.php?aftersaleInfo&class=10&id='.$v["id"].'">查看</a>
    //                     </div>
    //                     <div class="clear"></div>
    //                 </div>
    //                 <div class="clear"></div>';
    //             }
    //             $page['page'] .= "
    //                 <script>
    //                     $('.mPage a').click(function(){
    //                         var p = $(this).attr('data-page');
    //                         $.getJSON('dealer.php?aftersaleAjaxPage',{'p':p,'t':2},function(re){
    //                             $('#mPageDiv').html(re.data);
    //                             $('.mPage').html(re.page);
    //                         })
    //                     })
    //                 </script>
    //             ";
    //         }

    //         $re['data'] = $data;
    //         $re['page'] = $page['page'];

    //         echo json_encode($re);
    //     }
    // }

    //账号
    public function account(){
        $where['d_id'] = $_SESSION['dealerId'];
        $dealerInfo = D('dealer')->getDealerInfo($where);//经销商信息
        $this->assign('dealerInfo',$dealerInfo);
        

        //订单分页
        $list         = D('dealer')->dealer_settle($_SESSION['dealerId']);
        $count        = count($list);
        $page         = getAjaxPage($count,$this->dealerPage);
        $dealer_order = D('dealer')->dealer_settle($_SESSION['dealerId'],$page['limit']);

        //结算分页
        $list2             = D('dealer')->dealer_settlement($_SESSION['dealerId']);
        $count2            = count($list2);
        $page2             = getAjaxPage($count2,$this->dealerPage);
        $dealer_settlement = D('dealer')->dealer_settlement($_SESSION['dealerId'],$page2['limit']);

        //帐户余额
        $totalMoney = D('dealer')->accountBalance($_SESSION['dealerId']);


        //模板传值
        $this->assign("page_nav",$page['page']);//订单分页
        $this->assign("page_nav_settlement",$page2['page']);//结算分页
        $this->assign('dealer_order',$dealer_order);
        $this->assign('dealer_settlement',$dealer_settlement);
        $this->assign('totalMoney',$totalMoney);
        $this->assign('actname','账户管理');
        $this->display();

        // $res   = D('dealer')->getMaintain($page['limit']);

        // if(!isset($_GET['op'])){
        //     $page = 1;
        // }else{
        //     $page = $_GET['op'];
        // }
        // $each_disNums = 10;
        // $dealer_order= D('dealer')->dealer_settle($_SESSION['dealerId'],'0,10');

        // //分页
        // $dealer_order_count_nav=D('dealer')->show_count_nav('xgj_furnish_dealer_settle',$_SESSION['dealerId']);//分页的总条数
        // $t_nav = new Page($each_disNums, $dealer_order_count_nav, $page, 5, "dealer.php?account&op=");
        // $page_nav=$t_nav->subPageCss2();//分页样式

        // //结算分页
        // if(!isset($_GET['lp'])){
        //     $page = 1;
        // }else{
        //     $page = $_GET['lp'];
        // }
        // $dealer_settlement= D('dealer')->dealer_settlement($_SESSION['dealerId'],$page);
        // //分页
        // $dealer_order_count_nav=D('dealer')->show_count_nav('xgj_furnish_finance',$_SESSION['dealerId']);//分页的总条数
       
        // $t_nav = new Page(10, $dealer_order_count_nav, $page, 5, "dealer.php?account&lp=");
        // $page_nav_settlement=$t_nav->subPageCss2();//分页样式
        
        // //帐户余额
        // $accountBalance = D('dealer')->accountBalance($_SESSION['dealerId']);
        // //print_r();
        // $totalMoney = $accountBalance['d_price'];
    }


    //经销商结算分页
    public function accountPage2(){
        $list2             = D('dealer')->dealer_settlement($_SESSION['dealerId']);
        $count2            = count($list2);
        $page2             = getAjaxPage($count2,$this->dealerPage);
        $dealer_settlement = D('dealer')->dealer_settlement($_SESSION['dealerId'],$page2['limit']);
        $re = '';
        foreach ($dealer_settlement as $k => $v) {
        $re .= "<div class='dealer_account-center-tabs02-list-demo'>
                    <div class='dealer_account-center-tabs02-list-demo-time'>
                    ".date('Y-m-d H:i',$v['apply_time'])."
                    </div>
                    <div class='dealer_account-center-tabs02-list-demo-settlement'>";
        if($v['finance_status'] == 0){
            $re .= '申请中';
        }else if($v['finance_status'] == 1){ 
            $re .= '同意结算';
        }else if($v['finance_status'] == 2){ 
            $re .= '已结算';
        }else if($v['finance_status'] == 3){ 
            $re .= '拒绝结算';
        }
        $re .= "</div>
                    <div class='dealer_account-center-tabs02-list-demo-money'>
                        ".$v['money']."
                    </div>
                    <div class='clear'></div>
                </div>
                <div class='clear'></div>";
        }

        $re .= "<div class='clear32'></div>
                
                <div class='page2'>
                    ".$page2['page']."
                </div>
        
                <div class='clear2'></div>";
        
        $re .= '<script type="text/javascript">
                $(".page2 .page a").click(function(){
                    var page = $(this).attr("data-page");
                    $.get("'.U("accountPage2").'",{"p":page},function(re){
                        $(".accountPage2").html(re);
                    })
                })
                </script>';
        echo $re;
    }

    //经销商订单分页
    public function accountPage(){
        $list         = D('dealer')->dealer_settle($_SESSION['dealerId']);
        $count        = count($list);
        $page         = getAjaxPage($count,$this->dealerPage);
        $dealer_order = D('dealer')->dealer_settle($_SESSION['dealerId'],$page['limit']);
        $re = '';
        foreach ($dealer_order as $k => $v) {

        $re .= "<div class='dealer_account-center-tabs01-list-demo'>
            <div class='dealer_account-center-tabs01-list-demo-order'>
            ".$v['order_code']."
            </div>
            
            <div class='dealer_account-center-tabs01-list-demo-contacts'>
            ".$v['consignee']."
            </div>
            
            <div class='dealer_account-center-tabs01-list-demo-phone'>
            ".$v['mobile_phone']."
            </div>
            
            <div class='dealer_account-center-tabs01-list-demo-settlement'>";
            if($v['pay_status'] == 0){
                $re .= '未结算';
            }else if($v['pay_status'] == 1){ 
                $re .= '全款结算';
            }else if($v['pay_status'] == 2){ 
                $re .= '第一笔结算';
            }else if($v['pay_status'] == 3){ 
                $re .= '第二笔结算';
            }else if($v['pay_status'] == 4){ 
                $re .= '第三笔结算';
            }
            $re .= "</div>
            
            <div class='dealer_account-center-tabs01-list-demo-money'>
            ".$v['price']."
            </div>
            
            <div class='dealer_account-center-tabs01-list-demo-time'>
            ".date('Y-m-d H:i',$v['add_time'])."
            </div>
            
            <div class='clear'></div>
        </div>
        
        <div class='clear'></div>";
        }
        $re .= "<div class='clear32'></div>
                
                <div class='page'>
                    ".$page['page']."
                </div>
        
                <div class='clear2'></div>";
        
        $re .= '<script type="text/javascript">
                $(".page .page a").click(function(){
                    var page = $(this).attr("data-page");
                    $.get("'.U("accountPage").'",{"p":page},function(re){
                        $(".accountPage").html(re);
                    })
                })
                </script>';
        echo $re;
    }


    #意见反馈
    function advice(){
        $where['d_id'] = $_SESSION['dealerId'];
        $dealerInfo = D('dealer')->getDealerInfo($where);//经销商信息

        //分页  
        $count         = D('dealer')->dealer_advices_count($where);
        $page          = getPage($count,12);
        $advice_list   = D('dealer')->dealer_advices($where,$page['limit']);

        //模板传值
        $this->assign('dealerInfo',$dealerInfo);
        $this->assign("page_nav",$page);
        $this->assign('advice_list',$advice_list);
        $this->assign('actname','意见反馈');
        $this->display();
    }
    
    //意见反馈答复
    public function answerAdvice(){
        
        if (!empty($_POST)){
            $advice_id = I('post.advice_id');

            $data['advice_reply']     = I('post.advice_reply');
            $data['advice_replytime'] = time();
            $data['advice_statu']     = 1;
            
            $where['advice_id'] = $advice_id;
            $res = D('dealer')->updatePlan('xgj_furnish_advice', $data, $where);
            if ($res){
                $this->success('回复成功');
            }else{
                $this->error('回复失败');
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
        $where['d_id'] = $_SESSION['dealerId'];
        $dealerInfo = D('dealer')->getDealerInfo($where);//经销商信息
        $this->assign('dealerInfo',$dealerInfo);
        $this->assign('actname','密码管理');
        $this->display();
    }
    
    
    function apass(){
        $oldpass  = I('post.oldpass');
        $newpass  = I('post.newpass');
        $cnewpass = I('post.cnewpass');
        if(empty($oldpass) || empty($newpass) || empty($cnewpass)){
            $this -> alert('请填写完整');
        }
        if($newpass != $cnewpass){
            $this -> alert('新密码设置不一致');
        }
        if($newpass == $oldpass){
            $this -> alert('新密码与原密码不可一致');
        }

        if(D('dealer')->upass($oldpass, $newpass)){
            $this -> alert('密码修改成功');
        }else{
            $this -> alert('原密码错误');
        }
    }
    
    /*
    public function getDealerHeaderInfo(){
        $dealer = new dealer_model();
        
        $dealerInfo = D('dealer')->getDealerInfo($_SESSION['dealerId']);//经销商信息
        print_r($dealerInfo);
        
        $tpl = get_smarty();
        
        $this->assign('dealerInfo',$dealerInfo);
        display('dealer_header.tpl.html');
    }
    */
   
    
    
    

    
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
            $updatePlan = D('dealer')->updatePlan('xgj_furnish_order_construct', $data, $where);
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
            
            $updateCheck = D('dealer')->updatePlan('xgj_furnish_order_construct', $data, $where);
            if ($updateCheck){
                echo '审核操作成功';
                //exit();
                header("location:dealer.php?orderDetail");
            }
        }
    }
    
    
    //辅材清单页面
    public function fuCaiList(){
        $order_id  = I('get.order_id');
        $detail_id = I('get.detail_id');

        //订单信息
        $where['a.order_id']   = $order_id;
        $where['a.d_id']       = $_SESSION['dealerId'];
        $where['b.detail_id']  = $detail_id;
        $orderInfo = D('dealer')->orderInfo($where);

        if (empty($orderInfo)) $this->error('请勿非法操作！');

        $orderInfo['house_layout'] = explode(',', $orderInfo['house_layout']);

        //辅材列表
        $map['detail_id'] = $detail_id;
        $map['order_id']  = $order_id;
        $data = D('dealer')->get_dealer_order_stuff_list($map);

        !empty($data['adjust_stuff_goods'])?$fuCaiGoods = $data['adjust_stuff_goods']:$fuCaiGoods = $data['stuff_goods'];

        $array = explode(';', $fuCaiGoods);
        foreach ($array as $k => $v) {
            $goods[$k] = explode(',', $v);
        }

        $cond['goods_sn'] = array('in',$goods['0']);
        $cond['goods_lv'] = 2;

        $fuCaiList = M('xgj_furnish_goods')->field('goods_sn,goods_img,goods_name,goods_model,goods_brand,shop_price,goods_unit,goods_lv')->where($cond)->select();
        
        for ($i=0; $i < count($goods['0']); $i++) { 
            $goodsList[$goods['0'][$i]] += $goods['1'][$i];
        }

        foreach ($fuCaiList as $k => $v) {
            $fuCaiList[$k]['num'] = $goodsList[$v['goods_sn']];
        }

        $this->assign('orderInfo',$orderInfo);
        $this->assign('fuCaiList',$fuCaiList);
        $this->assign('actname','辅材清单');
        $this->display();

        //以下代码之前大哥所写，请确认后再删除

        // if (!empty($_GET['order_id'])){
        //     $order_id = $_GET['order_id'];
        //     setcookie('orderDetail_orderId',$order_id);
        // }else {
        //     $order_id = $_COOKIE['orderDetail_orderId'];
        // }
        // if (!empty($_GET['detail_id'])){
        //     $detail_id = $_GET['detail_id'];
        //     setcookie('detail_id',$detail_id);
        // }else {
        //     $detail_id = $_COOKIE['detail_id'];
        // }
        // if (!empty($_GET['level'])){
        //     $level = $_GET['level'];
        //     setcookie('level',$level);
        // }else {
        //     $level = $_COOKIE['level'];
        // }
        // $dealer = new dealer_model();
        // $tpl = get_smarty();
        // //经销商信息
        // $dealerInfo = D('dealer')->getDealerInfo($_SESSION['dealerId']);
        // $this->assign('dealerInfo',$dealerInfo);
        // //补货删除操作
        //  if (isset($_GET['delete'])&&$_GET['delete']==1&&isset($_SESSION['replenish'][$_GET['goods_sn']])){
        //     unset($_SESSION['replenish'][$_GET['goods_sn']]);
        // }
        // //退货删除操作
        // if (isset($_GET['delete'])&&$_GET['delete']==2&&isset($_SESSION['refund'][$_GET['goods_sn']])){
        //     unset($_SESSION['refund'][$_GET['goods_sn']]);
        // }
        // //自购删除操作
        // if (isset($_GET['delete'])&&$_GET['delete']==6&&isset($_COOKIE['selfBuy'][$_GET['goods_sn']])){
        //     unset($_COOKIE['selfBuy'][$_GET['goods_sn']]);
        // }
        // //经销商信息
        // $dealerInfo = D('dealer')->getDealerInfo($_SESSION['dealerId']);
        // //订单信息
        // $orderInfo = D('dealer')->orderInfo($order_id);
        // $orderInfo['house_layout'] = explode(',', $orderInfo['house_layout']);
        // //辅材列表
        // $fucaiList=D('dealer')->get_dealer_order_stuff_list($detail_id,$level);

        // if (!empty($_POST) && isset($_POST['numberChange']) && isset($_POST['numberOld'])){
        //     if ($_POST['numberChange'] > $_POST['numberOld']){
        //         //补货数据
        //         $_POST['numbers'] = $_POST['numberChange']-$_POST['numberOld'];
        //         $_SESSION['replenish'][$_POST['goods_sn']] = $_POST;
        //         $replenishTemp = $_SESSION['replenish'];
        //         //获取补货的总金额
        //         $totalReplenish = null;
        //         foreach ($replenishTemp as $goodsTemp){
        //             $totalReplenish+= $goodsTemp['shop_price']*$goodsTemp['numbers'];
        //         }
        //         $_SESSION['totalReplenish'] = $totalReplenish;
        //         //vdump($_SESSION);
        //         $this->assign('replenishTemp',$replenishTemp);
        //         $this->assign('totalReplenish',$totalReplenish);
        //     }else if($_POST['numberChange'] < $_POST['numberOld']){
        //         //退货数据
        //         $_POST['numbers'] = $_POST['numberOld']-$_POST['numberChange'];
        //         $_SESSION['refund'][$_POST['goods_sn']] = $_POST;
        //         $refundTemp = $_SESSION['refund'];
        //         //获取退货的总金额
        //         $totalRefund = null;
        //         foreach ($refundTemp as $goodsTemp){
        //             $totalRefund+= $goodsTemp['shop_price']*$goodsTemp['numbers'];
        //         }
        //         $_SESSION['totalRefund'] = $totalRefund;
        //         $tpl -> assign('refundTemp',$refundTemp);
        //         $tpl -> assign('totalRefund',$totalRefund);
        //     }
        // }
        
        // //判断SESSION中是否有补货信息
        // if (!empty($_SESSION['replenish'])){
        //     $replenishTemp = $_SESSION['replenish'];
        //     //获取补货的总金额
        //     $totalReplenish = null;
        //     foreach ($replenishTemp as $goodsTemp){
        //         $totalReplenish+= $goodsTemp['shop_price']*$goodsTemp['numbers'];
        //     }
        //     $_SESSION['totalReplenish'] = $totalReplenish;
        //     $this->assign('replenishTemp',$replenishTemp);
        //     $this->assign('totalReplenish',$totalReplenish);
        // }
        // if (!empty($_SESSION['refund'])){
        //     $refundTemp = $_SESSION['refund'];
        //     //获取退货的总金额
        //     $totalRefund = null;
        //     foreach ($refundTemp as $goodsTemp){
        //         $totalRefund+= $goodsTemp['shop_price']*$goodsTemp['numbers'];
        //     }
        //     $_SESSION['totalRefund'] = $totalRefund;
        //     $this->assign('refundTemp',$refundTemp);
        //     $this->assign('totalRefund',$totalRefund);
        // }
        
        // //向前台模板页面注册自购材料数据
        // if (!empty($_SESSION['selfBuy'])){
        //     $selfBuyTemp = $_SESSION["selfBuy"];
        //     //$selfBuyTemp = unserialize($selfBuyTemp);        
        //     //获取退货的总金额
        //     $totalSelfBuy = null;
        //     foreach ($selfBuyTemp as $goodsTemp){
        //         $totalSelfBuy+= $goodsTemp['shop_price']*$goodsTemp['numbers'];
        //     }
        //     $_SESSION['totalSelfBuy'] = $totalSelfBuy;
        //     $this->assign('selfBuyTemp',$selfBuyTemp);
        //     $this->assign('totalSelfBuy',$totalSelfBuy);
        // }
        // //处理订单号
        // $microtime               = explode('.',microtime(true));
        // /**
        //  * 补货材料入库数据
        //  * */
        // if (!empty($_SESSION['replenish'])){
        //     $replenishData = array();
        //     $replenishData['level']=$level;
        //     $replenishData['order_id'] = $orderInfo['order_id'];
        //     $replenishData['user_id'] = $orderInfo['user_id'];
        //     $replenishData['d_id'] = $orderInfo['d_id'];
        //     $replenishData['quote_id'] = $orderInfo['quote_id'];
        //     $replenishData['refund_price'] = $_SESSION['totalReplenish'];
        //     $replenishData['refund_status'] = 2;
        //     $replenishData['refund_time'] = time();
        //     date_default_timezone_set("Asia/ShangHai");//设置时区
        //     $replenishData['refund_code'] = date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT);
        //     $replenishData['first_audit_status'] = 1;
        //     $replenishData['detail_id'] = $orderInfo['detail_id'];
        //     $replenishData['refund_goods'] = '';
        //     //获取$replenishData['refund_goods']数据
        //     if (!empty($_SESSION['replenish'])){
        //         $goodsSn = '';
        //         $goodsNum = '';
        //         foreach ($_SESSION['replenish'] as $k=>$v){
        //             $goodsSn.=','.$v['goods_sn'];
        //             $goodsNum.=','.$v['numbers'];
        //         }
        //         $goodsSn = trim($goodsSn,',');
        //         $goodsNum = trim($goodsNum,',');
        //         $replenishData['refund_goods'] .= $goodsSn;
        //         $replenishData['refund_goods'] .= ';';
        //         $replenishData['refund_goods'] .= $goodsNum;
        //         if (!empty($replenishData)){
        //             $arrReplenish = serialize($replenishData);
        //             setcookie("arrReplenish",$arrReplenish,time()+3600);
        //         }
        //     }
        //     //vdump($_COOKIE['arrReplenish']);
        // }
        
        // /**
        //  * 自购材料入库数据
        //  * */
        // if (!empty($_SESSION['selfBuy'])){
        //     $selfBuyData = array();
        //     $replenishData['level']=$level;
        //     $selfBuyData['order_id'] = $orderInfo['order_id'];
        //     $selfBuyData['user_id'] = $orderInfo['user_id'];
        //     $selfBuyData['d_id'] = $orderInfo['d_id'];
        //     $selfBuyData['quote_id'] = $orderInfo['quote_id'];
        //     $selfBuyData['refund_price'] = $_SESSION['totalSelfBuy'];
        //     $selfBuyData['refund_status'] = 3;
        //     $selfBuyData['refund_time'] = time();
        //     date_default_timezone_set("Asia/ShangHai");//设置时区
        //     $selfBuyData['refund_code'] = date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT);
        //     $selfBuyData['first_audit_status'] = 1;
        //     $selfBuyData['detail_id'] = $orderInfo['detail_id'];
        //     $selfBuyData['refund_goods'] = '';
        //     //获取$replenishData['refund_goods']数据
        //     if (!empty($_SESSION['selfBuy'])){
        //         $goodsSn = '';
        //         $goodsNum = '';
        //         foreach ($_SESSION['selfBuy'] as $k=>$v){
        //             $goodsSn.=','.$v['goods_sn'];
        //             $goodsNum.=','.$v['numbers'];
        //         }
        //         $goodsSn = trim($goodsSn,',');
        //         $goodsNum = trim($goodsNum,',');
        //         $selfBuyData['refund_goods'] .= $goodsSn;
        //         $selfBuyData['refund_goods'] .= ';';
        //         $selfBuyData['refund_goods'] .= $goodsNum;
        //         if (!empty($selfBuyData)){
        //             $arrSelfBuy = serialize($selfBuyData);
        //             setcookie("arrSelfBuy",$arrSelfBuy,time()+3600);
        //         }
        //     }
        // }

        // /**
        //  * 退货材料入库数据
        //  * */
        // if (!empty($_SESSION['refund'])){
        //     $refundData = array();
        //     $replenishData['level']=$level;
        //     $refundData['order_id'] = $orderInfo['order_id'];
        //     $refundData['user_id'] = $orderInfo['user_id'];
        //     $refundData['d_id'] = $orderInfo['d_id'];
        //     $refundData['quote_id'] = $orderInfo['quote_id'];
        //     $refundData['refund_price'] = $_SESSION['totalRefund'];
        //     $refundData['refund_status'] = 1;
        //     $refundData['refund_time'] = time();
        //     date_default_timezone_set("Asia/ShangHai");//设置时区
        //     $refundData['refund_code'] = date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT);
        //     $refundData['first_audit_status'] = 1;
        //     $refundData['detail_id'] = $orderInfo['detail_id'];
        //     $refundData['refund_goods'] = '';
        //     //获取$refundData['refund_goods']数据
        //     if (!empty($_SESSION['refund'])){
        //         $goodsSn = '';
        //         $goodsNum = '';
        //         foreach ($_SESSION['refund'] as $k=>$v){
        //             $goodsSn.=','.$v['goods_sn'];
        //             $goodsNum.=','.$v['numbers'];
        //         }
        //         $goodsSn = trim($goodsSn,',');
        //         $goodsNum = trim($goodsNum,',');
        //         $refundData['refund_goods'] .= $goodsSn;
        //         $refundData['refund_goods'] .= ';';
        //         $refundData['refund_goods'] .= $goodsNum;
        //         if (!empty($refundData)){
        //             $arrRefund = serialize($refundData);
        //             setcookie("arrRefund",$arrRefund,time()+3600);
        //         }
        //     }
        // }        
        // $this->assign('orderInfo',$orderInfo);
        // $this->assign('fucaiList',$fucaiList);
        // $this->display('dealer/dealer_fucai_xin.tpl.html');
    }


    public function buhuo(){
        $numberChange = I('get.numberChange');
        $numberOld    = I('get.numberOld');
        $goods_sn     = I('get.goods_sn');

        if (empty($numberChange) || empty($numberOld) || empty($goods_sn) || $numberOld < 0 || $numberChange < 0) $this->error('非法操作！');
        
        if ($numberChange == $numberOld) $this->error('调整量与标准量不能一样');

        if ($numberChange > $numberOld) {
            //补货
            $numbers = $numberChange - $numberOld;
            $status  = 2;
        }else if($numberChange < $numberOld) {
            //退货
            $numbers = $numberOld - $numberChange;
            $status  = 1;
        }

        

        $_SESSION['buHuoList'][$goods_sn] = array(
            'numberChange' => $numberChange,
            'numbers'      => $numbers,
            'status'       => $status,
            );

        $sn = array_keys($_SESSION['buHuoList']);

        $map['goods_sn'] = array('in',$sn);
        $map['goods_lv'] = 1;
        $re = M('xgj_furnish_goods')->field('goods_sn,goods_img,goods_name,goods_model,goods_brand,shop_price,goods_unit,goods_lv')->where($map)->select();

        foreach ($re as $k => $v) {
            $re[$k]['num']    = $_SESSION['buHuoList'][$v['goods_sn']]['numbers'];
            $re[$k]['status'] = $_SESSION['buHuoList'][$v['goods_sn']]['status'];
        }

        $return[1] = '';
        $return[2] = '';
        $return['total'][1] = 0;
        $return['total'][2] = 0;
        
        foreach ($re as $k => $v) {
            $return[$v['status']] .= '
                <div class="dealer_zhucai-center-tabs02-demo-img">
                    <img src="'.getImage($v["goods_img"]).'"/>
                </div>
                
                <div class="dealer_zhucai-center-tabs02-demo-name">
                    '.$v["goods_name"].'
                </div>
                
                <div class="dealer_zhucai-center-tabs02-demo-model">
                    '.$v["goods_model"].'
                </div>
                
                <div class="dealer_zhucai-center-tabs02-demo-brand">
                    '.$v["goods_brand"].'
                </div>
                
                <div class="dealer_zhucai-center-tabs02-demo-univalent">
                    '.$v["shop_price"].'
                </div>
                
                <div class="dealer_zhucai-center-tabs02-demo-unit">
                    '.$v["goods_unit"].'
                </div>
            ';

            if ($v['status']==2) {
                $return[$v['status']] .= '
                    <div class="dealer_zhucai-center-tabs02-demo-complement">
                        '.$v["num"].'
                    </div>
                    <div class="dealer_zhucai-center-tabs02-demo-library">
                        上品上生
                    </div>
                    <div class="dealer_zhucai-center-tabs02-demo-operation">
                        <a href="javascript:;" onclick="delBuHuo('.$v["goods_sn"].')">
                            删除
                        </a>
                    </div>
                ';
            }else if($v['status']==1){
                $return[$v['status']] .= '
                    <div class="dealer_zhucai-center-tabs03-demo-quantity">
                        '.$v["num"].'
                    </div>
                    <div class="dealer_zhucai-center-tabs03-demo-operation">
                        <a href="javascript:;" onclick="delBuHuo('.$v["goods_sn"].')">
                            删除
                        </a>
                    </div>
                ';
            }

            $return[$v['status']] .= '
                <div class="clear"></div>
            ';

            $return['total'][$v['status']] += ($v["num"]*$v["shop_price"]);
        }
        echo json_encode($return);
    }
    
    /**
     *主材清单页面控制器 
     */
    public function zhuCaiList(){
        $order_id  = I('get.order_id');
        $detail_id = I('get.detail_id');

        //订单信息
        $where['a.order_id']   = $order_id;
        $where['a.d_id']       = $_SESSION['dealerId'];
        $where['b.detail_id']  = $detail_id;
        $orderInfo = D('dealer')->orderInfo($where);

        if (empty($orderInfo)) $this->error('请勿非法操作！');

        $orderInfo['house_layout'] = explode(',', $orderInfo['house_layout']);

        //辅材列表
        $map['detail_id'] = $detail_id;
        $map['order_id']  = $order_id;
        $data = D('dealer')->get_dealer_order_stuff_list($map);

        !empty($data['adjust_stuff_goods'])?$fuCaiGoods = $data['adjust_stuff_goods']:$fuCaiGoods = $data['stuff_goods'];

        $array = explode(';', $fuCaiGoods);
        foreach ($array as $k => $v) {
            $goods[$k] = explode(',', $v);
        }

        $cond['goods_sn'] = array('in',$goods['0']);
        $cond['goods_lv'] = 1;

        $zhuCaiList = M('xgj_furnish_goods')->field('goods_sn,goods_img,goods_name,goods_model,goods_brand,shop_price,goods_unit,goods_lv')->where($cond)->select();
        
        for ($i=0; $i < count($goods['0']); $i++) { 
            $goodsList[$goods['0'][$i]] += $goods['1'][$i];
        }

        foreach ($zhuCaiList as $k => $v) {
            $zhuCaiList[$k]['num'] = $goodsList[$v['goods_sn']];
        }

        $this->assign('orderInfo',$orderInfo);
        $this->assign('zhuCaiList',$zhuCaiList);
        $this->assign('actname','主材清单');
        $this->display();
        // if (!empty($_GET['order_id'])){
        //     $order_id = $_GET['order_id'];
        //     setcookie('orderDetail_orderId',$order_id);
        // }else {
        //     $order_id = $_COOKIE['orderDetail_orderId'];
        // }
        // if (!empty($_GET['detail_id'])){
        //     $detail_id = $_GET['detail_id'];
        //     setcookie('detail_id',$detail_id);
        // }else {
        //     $detail_id = $_COOKIE['detail_id'];
        // }
        // if (!empty($_GET['level'])){
        //     $level = $_GET['level'];
        //     setcookie('level',$level);
        // }else {
        //     $level = $_COOKIE['level'];
        // }
        // $dealer = new dealer_model();
        // $tpl = get_smarty();       
        // $dealerInfo = D('dealer')->getDealerInfo($_SESSION['dealerId']);//经销商信息
        // $this->assign('dealerInfo',$dealerInfo);
        
        // //补货删除操作
        // if (isset($_GET['delete'])&&$_GET['delete']==1&&isset($_SESSION['replenishZhu'][$_GET['goods_sn']])){
        //     unset($_SESSION['replenishZhu'][$_GET['goods_sn']]);
        // }
         
        // //退货删除操作
        // if (isset($_GET['delete'])&&$_GET['delete']==2&&isset($_SESSION['refundZhu'][$_GET['goods_sn']])){
        //     unset($_SESSION['refundZhu'][$_GET['goods_sn']]);
        // }
        
        // $dealerInfo = D('dealer')->getDealerInfo($_SESSION['dealerId']);//经销商信息
        
        // $orderInfo = D('dealer')->orderInfo($order_id);
       
        // $zhucaiList=D('dealer')->get_dealer_order_stuff_list($detail_id,$level);

        // if (!empty($_POST)&&isset($_POST['numberChange'])&&isset($_POST['numberOld'])){
        //     if ($_POST['numberChange']>$_POST['numberOld']){
        //         //补货数据
        //         $_POST['numbers'] = $_POST['numberChange']-$_POST['numberOld'];
        //         $_SESSION['replenishZhu'][$_POST['goods_sn']] = $_POST;
        //         $replenishTemp = $_SESSION['replenishZhu'];
        //         //获取补货的总金额
        //         $totalReplenish = null;
        //         foreach ($replenishTemp as $goodsTemp){
        //             $totalReplenish+= $goodsTemp['shop_price']*$goodsTemp['numbers'];
        //         }
        //         $_SESSION['totalReplenishZhu'] = $totalReplenish;
        //         $this->assign('replenishTemp',$replenishTemp);
        //         $this->assign('totalReplenish',$totalReplenish); 
                                 
        //     }else if($_POST['numberChange'] < $_POST['numberOld']){
        //         //退货数据
        //         $_POST['numbers'] = $_POST['numberOld']-$_POST['numberChange'];
        //         $_SESSION['refundZhu'][$_POST['goods_sn']] = $_POST;
                
        //         $refundTemp = $_SESSION['refundZhu'];
                 
        //         //获取退货的总金额
        //         $totalRefund = null;
        //         foreach ($refundTemp as $goodsTemp){
        //             $totalRefund+= $goodsTemp['shop_price']*$goodsTemp['numbers'];
        //         }
        //         $_SESSION['totalRefundZhu'] = $totalRefund; 
        //         $this->assign('refundTemp',$refundTemp);
        //         $this->assign('totalRefund',$totalRefund);
        //     }
        // }
        
        // //判断SESSION里面有没有存入补货信息
        // if (!empty($_SESSION['replenishZhu'])){
        //     $replenishTemp = $_SESSION['replenishZhu'];
        //     //获取补货的总金额
        //     $totalReplenish = null;
        //     foreach ($replenishTemp as $goodsTemp){
        //         $totalReplenish+= $goodsTemp['shop_price']*$goodsTemp['numbers'];
        //     }
        //     $_SESSION['totalReplenishZhu'] = $totalReplenish; 
        //     $this->assign('replenishTemp',$replenishTemp);
        //     $this->assign('totalReplenish',$totalReplenish);
            
        // }
        // if (!empty($_SESSION['refundZhu'])){
        //     $refundTemp = $_SESSION['refundZhu'];
        //     //获取退货的总金额
        //     $totalRefund = null;
        //     foreach ($refundTemp as $goodsTemp){
        //         $totalRefund+= $goodsTemp['shop_price']*$goodsTemp['numbers'];
        //     }
        //     $_SESSION['totalRefundZhu'] = $totalRefund;
        //     $this->assign('refundTemp',$refundTemp);
        //     $this->assign('totalRefund',$totalRefund);
        // }

        // //处理订单号
        // $microtime               = explode('.',microtime(true));
        // /**
        //  * 补货材料入库数据
        //  * */
        // if (!empty($_SESSION['replenishZhu'])){
        //     $replenishData = array();
        //     $replenishData['level']=$level;
        //     $replenishData['order_id'] = $orderInfo['order_id'];
        //     $replenishData['user_id'] = $orderInfo['user_id'];
        //     $replenishData['d_id'] = $orderInfo['d_id'];
        //     $replenishData['quote_id'] = $orderInfo['quote_id'];
        //     $replenishData['refund_price'] = $_SESSION['totalReplenishZhu'];
        //     $replenishData['refund_status'] = 2;
        //     $replenishData['refund_time'] = time();
        //     date_default_timezone_set("Asia/ShangHai");//设置时区
        //     $replenishData['refund_code'] = date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT);
        //     $replenishData['first_audit_status'] = 1;
        //     $replenishData['detail_id'] = $orderInfo['detail_id'];
        //     $replenishData['refund_goods'] = '';
        //     //获取$replenishData['refund_goods']数据
        //     if (!empty($_SESSION['replenishZhu'])){
        //         $goodsSn = '';
        //         $goodsNum = '';
        //         foreach ($_SESSION['replenishZhu'] as $k=>$v){
        //             $goodsSn.=','.$v['goods_sn'];
        //             $goodsNum.=','.$v['numbers'];
        //         }
        //         $goodsSn = trim($goodsSn,',');
        //         $goodsNum = trim($goodsNum,',');
        //         $replenishData['refund_goods'] .= $goodsSn;
        //         $replenishData['refund_goods'] .= ';';
        //         $replenishData['refund_goods'] .= $goodsNum;
        //         if (!empty($replenishData)){
        //             $arrReplenishZhu = serialize($replenishData);
        //             setcookie("arrReplenishZhu",$arrReplenishZhu,time()+3600);
        //         }
                 
        //     }

        // }
        
        
        // /**
        //  * 退货材料入库数据
        //  * */
        // if (!empty($_SESSION['refundZhu'])){
        //     $refundData = array();
        //     $replenishData['level']=$level;
        //     $refundData['order_id'] = $orderInfo['order_id'];
        //     $refundData['user_id'] = $orderInfo['user_id'];
        //     $refundData['d_id'] = $orderInfo['d_id'];
        //     $refundData['quote_id'] = $orderInfo['quote_id'];
        //     $refundData['refund_price'] = $_SESSION['totalRefundZhu'];
        //     $refundData['refund_status'] = 1;
        //     $refundData['refund_time'] = time();
        //     date_default_timezone_set("Asia/ShangHai");//设置时区
        //     $refundData['refund_code'] = date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT);
        //     $refundData['first_audit_status'] = 1;
        //     $refundData['detail_id'] = $orderInfo['detail_id'];
        //     $refundData['refund_goods'] = '';
        //     //获取$refundData['refund_goods']数据
        //     if (!empty($_SESSION['refundZhu'])){
        //         $goodsSn = '';
        //         $goodsNum = '';
        //         foreach ($_SESSION['refundZhu'] as $k=>$v){
        //             $goodsSn.=','.$v['goods_sn'];
        //             $goodsNum.=','.$v['numbers'];
        //         }
        //         $goodsSn = trim($goodsSn,',');
        //         $goodsNum = trim($goodsNum,',');
        //         $refundData['refund_goods'] .= $goodsSn;
        //         $refundData['refund_goods'] .= ';';
        //         $refundData['refund_goods'] .= $goodsNum;
        //         if (!empty($refundData)){
        //             $arrRefundZhu = serialize($refundData);
        //             setcookie("arrRefundZhu",$arrRefundZhu,time()+3600);
        //         }
        //     }
        // }
        
        // $this->assign('orderInfo',$orderInfo);
        // $this->assign('zhucaiList',$zhucaiList);
        // $this->display('dealer/dealer_zhucai_xin.tpl.html');
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
            $res = D('dealer')->insertGoods('xgj_furnish_order_refund',$replenishData);
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
            $res = D('dealer')->insertGoods('xgj_furnish_order_refund',$refundData);
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
            $res = D('dealer')->insertGoods('xgj_furnish_order_refund',$replenishDataZhu);
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
            $res = D('dealer')->insertGoods('xgj_furnish_order_refund',$refundDataZhu);
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
            $res = D('dealer')->insertGoods('xgj_furnish_order_refund',$selfBuyData);
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
    
    

    

    
    // //调整订单
    // public function editOrder(){
    //  
    //  $dealer = new dealer_model();
    //  $tpl = get_smarty();
        
    //  //print_r($_GET);
    //  $order_id = $_GET['order_id'];
    //  $dealerInfo = D('dealer')->getDealerInfo($_SESSION['dealerId']);//经销商信息
        
    //  $orderInfo = D('dealer')->getOrderInfo($order_id);
        
    //  $arr = explode(',', $orderInfo['house_layout']);
    //  //print_r($arr);
    //  $orderInfo['shi'] = $arr[0];
    //  $orderInfo['ting'] = $arr[1];
    //  $orderInfo['chu'] = $arr[2];
    //  $orderInfo['wei'] = $arr[3];
    //  $orderInfo['yangtai'] = $arr[4];
        
    //  //print_r($orderInfo);
        
    //  $this->assign('dealerInfo',$dealerInfo);
    //  $this->assign('orderInfo',$orderInfo);
    //  $this->display('editOrder.tpl.html');
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
    //      $res = D('dealer')->editOrder('xgj_furnish_order_info', $arr, $where);
    //      if ($res){
    //          echo '订单调整成功';
    //          header("location:dealer.php?orderDetail");
    //      }
    //  }
        
   // }
    
    /*********************************************************************************************/
    //调整订单
    function adjust_order(){
        //$dealer = new dealer_model();
        //$tpl = get_smarty();
        $order_id  = $_GET['order_id'];
        //$detail_id = 13;

        $true = D('dealer')->isTrue(array('order_id'=>$order_id),$_SESSION['dealerId']);

        if (empty($true)) {
            //echo jump(2,'找不到此订单','dealer.php?order');exit;
            $this->error('找不到此订单');
        }

        $dealerInfo = D('dealer')->getDealerInfo($_SESSION['dealerId']);//经销商信息

        $orderInfo = D('dealer')->getOrderInfo($order_id);

        $dealerAdjustInfo = D('dealer')->getDealerAdjustInfo($orderInfo['order_code']);

        /*********房屋信息**********/
        $homeInfo = M('xgj_furnish_order_info')->where(array('order_id'=>$order_id))->find();

        $homeInfo['type'] = $homeInfo['house_type'];
        $homeInfo['layout'] = $homeInfo['house_layout'];

        //D('dealer')->getFind('xgj_furnish_order_info','order_id = '.$order_id);

        if (!empty($dealerAdjustInfo)) {
            $this->assign('dealerAdjustInfo',1);
            $homeInfo = $dealerAdjustInfo;
        }

        $house_layout = explode(',', $homeInfo['layout']);
        $Tarea = explode('|', $homeInfo['area']);

        $area['1'] =  explode(',', $Tarea['0']);
        $area['2'] =  explode(',', $Tarea['1']);
        $area['3'] =  explode(',', $Tarea['2']);
        $area['4'] =  explode(',', $Tarea['3']);
        $area['5'] =  explode(',', $Tarea['4']);
        $area['6'] =  explode(',', $Tarea['5']);
        $area['7'] =  explode(',', $Tarea['6']);

        $quoteInfo = D('dealer')->getQuote($order_id);
        $getimage = D('dealer')->getimage($order_id);
        /***************************/
// dump($quoteInfo);die;
        $this->assign('order_id',$order_id);
        $this->assign('quoteInfo',$quoteInfo);
        $this->assign('getimage',$getimage);
        $this->assign('homeInfo',$homeInfo);
        $this->assign('area',$area);
        $this->assign('house_layout',$house_layout);
        $this->assign('orderInfo',$orderInfo);
        $this->assign('dealerInfo',$dealerInfo);
        $this->assign('order_sn',$orderInfo['order_code']);
        $this->assign('actname','调整订单');
        $this->display();
    }

    public function do_adjust_order(){    

        $type1  = I('post.type1');
        $type2  = I('post.type2');
        $type3  = I('post.type3');
        $type4  = I('post.type4');
        $type5  = I('post.type5');
        $type6  = I('post.type6');
        $type7  = I('post.type7');
        $room1  = I('post.bedroom');
        $room2  = I('post.liveroom');
        $room3  = I('post.kitchen');
        $room4  = I('post.bathroom');
        $room5  = I('post.balcony');
        $room6  = I('post.gelou');
        $room7  = I('post.database');
        $people = I('post.people');

        $order_sn = I('post.order_sn');
        $order_id = I('post.order_id');

        //查看信息是否填写完整
        if (empty($order_sn) || empty($order_id)) $this->error('请勿非法操作！');

        if(!preg_match("/^[0-9]*$/", $people) && $people < 1) $this->error('常住人口只能为整数');

        $map['order_code'] = $order_sn;
        $map['order_id']   = $order_id;
        $map['d_id']       = $_SESSION['dealerId'];
        $orderInfo = M('xgj_furnish_order_info')->where($map)->find();
        if (empty($orderInfo)) $this->error('请勿非法操作！');

        $error = false;
        if (!empty($room1) && array_null($room1) == true || !empty($type1) && count($room1) != $type1) $error = true;
        else $roomStr1 = implode(',',$room1);
        if (!empty($room2) && array_null($room2) == true || !empty($type2) && count($room2) != $type2) $error = true;
        else $roomStr2 = implode(',',$room2);
        if (!empty($room3) && array_null($room3) == true || !empty($type3) && count($room3) != $type3) $error = true;
        else $roomStr3 = implode(',',$room3);
        if (!empty($room4) && array_null($room4) == true || !empty($type4) && count($room4) != $type4) $error = true;
        else $roomStr4 = implode(',',$room4);
        if (!empty($room5) && array_null($room5) == true || !empty($type5) && count($room5) != $type5) $error = true;
        else $roomStr5 = implode(',',$room5);
        if (!empty($room6) && array_null($room6) == true || !empty($type6) && count($room6) != $type6) $error = true;
        else $roomStr6 = implode(',',$room6);
        if (!empty($room7) && array_null($room7) == true || !empty($type7) && count($room7) != $type7) $error = true;
        else $roomStr7 = implode(',',$room7);
        if ($error == true) $this->error('请认真填写信息并填写完整');


        /******************************************/
        //上传文件
        if (!empty($_FILES['img']['name'])){
            $file = $_FILES['img'];
        }else{
            $this->error('请上传图片');
        }

        if(isset($_FILES['img'])&&$_FILES['img']['error']==0){
            $image = uploadOne('img','Autograph');
            if($image['code']!=1) $this->error('图片上传失败');

            $orderFile = array(
                'order_id'      => $order_id,
                'file_img'      => $image['images'],
                'file_name'     => '订单房型面积确认单',
                'file_time'     => time(),
                'upload_name'   => $_SESSION['dealerName'],
                'class'         => 2,
                'file_type'     => pathinfo($res['images'], PATHINFO_EXTENSION),
                'file_describe' => '订单房型面积确认单',
                'status'        => 3,
                );

            $img = D('dealer')->getimage($order_id);

            if (!empty($img)) {
                $re = M('xgj_furnish_order_file')->where(array('status'=>3,'order_id'=>$order_id))->save($orderFile);
                if ($re !== false) {
                    deleteImage($img['file_img']);
                }else{
                    deleteImage($image['images']);
                    $this->error('图片上传失败！');
                }
            }else{
                $inser = M('xgj_furnish_order_file')->add($orderFile);
                if (empty($inser)) $this->error('图片上传失败！');
            }
        }
        /******************************************/


        //每个房屋面积
        $area   = $roomStr1.'|'.$roomStr2.'|'.$roomStr3.'|'.$roomStr4.'|'.$roomStr5.'|'.$roomStr6.'|'.$roomStr7;

        //几室几厅
        $layout = $type1.','.$type2.','.$type3.','.$type4.','.$type5.','.$type6.','.$type7;
        
        //全部面积
        $total_area = array_sum($room1)+array_sum($room2)+array_sum($room3)+array_sum($room4)+array_sum($room5)+array_sum($room6)+array_sum($room7);

        //处理添加或修改的数据
        $houseData = array(
            'user_id'    => $orderInfo['user_id'],
            'order_code' => $order_sn,
            'layout'     => $layout,
            'total_area' => $total_area,
            'area'       => $area,
            'province'   => $orderInfo['province'],
            'city'       => $orderInfo['city'],
            'district'   => $orderInfo['district'],
            'address'    => $orderInfo['address'],
            'type'       => $orderInfo['house_type'],
            'people'     => $people,
            ); 

        $where['order_code'] = $order_sn;
        $adjustInfo = M('xgj_dealer_adjust_info')->where($where)->find();

        $addData = M('xgj_dealer_adjust_info')->create($houseData);
        if (empty($adjustInfo)) {
            $re = M('xgj_dealer_adjust_info')->add($addData);
            if (empty($re)) $this->error('提交失败');
        }else{
            $re = M('xgj_dealer_adjust_info')->where($where)->save($addData);
            if ($re === false) $this->error('提交失败');
        }

        /*******************************/
        //用调整完的房屋信息将所有此订单下的系统重新报价
        $quoteId = D('dealer')->getQuote($order_id);

        $array['tableName'] = 'xgj_dealer_adjust_info';
        $array['where']     = "id = {$adjustInfo['id']}";

        $tableName = M('xgj_furnish_order_detail');
        $tableName->startTrans();
        foreach ($quoteId as $k => $v) {
            $lists[$k] = A('Furnish')->getQuote($v['quote_id'],$v['level'],$array,false,false);

            $detaildata['adjust_stuff_goods'] = '';
            $detaildata['adjust_quote_price'] = '';
            $detaildata['adjust_cost']        = '';

            if ($lists[$k] !== 'error') {
                //处理调整后系统总价
                $sale = M('xgj_furnish_quote')->where(array('quote_id'=>$v['quote_id']))->getField('sale');
                $detaildata['adjust_quote_price'] = ceil(($lists[$k]['all']-$lists[$k]['install'])/100*$sale)+$lists[$k]['install'];
                $detaildata['adjust_cost']        = $lists[$k]['install'];

                $detaildata['adjust_stuff_goods'] = $lists[$k]['allStr'];

            }

            $data   = M('xgj_furnish_order_detail')->create($detaildata);
            $return = M('xgj_furnish_order_detail')->where(array('detail_id'=>$v['detail_id']))->save($detaildata);
            if ($return === false) {
                $tableName->rollback();
                $this->error('提交失败');
            }

            $adjust_goods_amount[] = $detaildata['adjust_quote_price'];
        }
        /*******************************/

        $saveOrderInfo['adjust_goods_amount'] = round(array_sum($adjust_goods_amount));
        $saveOrderInfo['order_status']        = 5;
        $OrderInfoData = M('xgj_furnish_order_info')->create($saveOrderInfo);
        $return = M('xgj_furnish_order_info')->where(array('order_id'=>$order_id))->save($OrderInfoData);
        if ($return === false) {
            $tableName->rollback();
            $this->error('提交失败');
        }
        
        $tableName->commit();
        $this->success('提交成功');

//         // $type = array(
//         //     )
// // xgj_dealer_adjust_info
//         // $order_id = $_POST['order_id'];

//         //$detail_id = $_POST['detail_id'];
// // var_dump($order_id);die;
//         if (empty($order_id) || !preg_match('/^[0-9]+$/', $order_id)) {
//             echo '<script>alert("请勿非法操作！");history.go(-1)</script>';exit;
//         }

//         // if (empty($detail_id) || !preg_match('/^[0-9]+$/', $detail_id)) {
//         //     echo '<script>alert("请勿非法操作！");history.go(-1)</script>';exit;
//         // }
       

//         if (!empty($_FILES['img']['name'])){
//             $file = $_FILES['img'];
//         }else{
//             // echo jump(2,'请上传图片','dealer.php?editOrder&order_id='.$order_id);
//             // header('refresh:3;url="dealer.php?editOrder&order_id='.$order_id );
//             $this->error('请上传图片');
//             $this->redirect('adjust_order',array('order_id'=>$order_id));
//             exit;
//         }
       
//         //上传文件
//         //$res=upload('img','Autograph');
//         if(isset($_FILES['img'])&&$_FILES['img']['error']==0){
//             $image = uploadOne('img','Autograph',C('IMG_THUMB_FACE'));
//             if($image['code']!=1){
//                  //头像上传失败
//                  $this->error = $image['error'];
//                  return false;
//             }
//              $res['images'] = $image['images'];
//          }
//         if ($res){
//             $data['order_id'] = $order_id;
//             $data['file_img'] = $res['images'];
//             $data['file_name'] = '订单房型面积确认单';
//             $data['file_time'] = time();
//             $data['upload_name'] = $_SESSION['dealerName'];
//             $data['class'] = 2;
//             $data['file_type'] = pathinfo($res['images'], PATHINFO_EXTENSION);
//             $data['file_describe'] = '订单房型面积确认单';
//             $data['status']=3;

//             $select = D('dealer')->getimage($order_id);
//             if (!empty($select)) {
//                 $inser = M('xgj_furnish_order_file')->where(array('file_id'=>$select['file_id']))->save($data);
//                 if (!empty($inser)) {
//                     $i = IMG_rootPath.$select['file_img'];
//                     @unlink($i);
//                 }
//             }else{
//                 $inser = M('xgj_furnish_order_file')->add($data);
//             }

//             if (empty($inser)){
//                 //echo jump(2,'图片上传失败','dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id);
//                 //header('refresh:3;url="dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id.'"' );
//                 $this->error('图片上传失败');
//                 $this->redirect('adjust_order',array('order_id'=>$order_id));
//                     exit;
//                 }
//             }else{
//                 //echo jump(2,'图片上传失败','dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id);
//                 //header('refresh:3;url="dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id.'"' );
//                 $this->error('图片上传失败');
//                 $this->redirect('adjust_order',array('order_id'=>$order_id));
//                 exit;
//             }

//        if ($_POST['house'] == 1) {
//            $_POST['type6'] = '';
//            $_POST['type7'] = '';
//            $_POST['gelou'] = '';
//            $_POST['database'] = '';
//        }

//        if($_POST){  
//         $housetype="";  
//         $area1="";
//         $area2="";
//         $area3="";
//         $area4="";
//         $area5="";
//         $area6="";
//         $area7="";
//         $type1="";
//         $type2="";
//         $type3="";
//         $type4="";
//         $type5="";
//         $type6="";
//         $type7="";
        
           
//            //房屋类型
//            if(!empty($_POST['house']))
//            {
//             $housetype=$_POST['house'];
//            }
           
    
//            // 室
//             if(!empty($_POST['type1']))
//             {
//                $type1=$_POST['type1'];
//             }else{
//                 $type1=0;
//             }
//             // 厅
//            if(!empty($_POST['type2']))
//             {
//                $type2=$_POST['type2'];
//             }else{
//                 $type2=0;
//             }
//             // 厨
//           if(!empty($_POST['type3']))
//             {
//                $type3=$_POST['type3'];
//             }else{
//                 $type3=0;
//             }
//             // 卫
//           if(!empty($_POST['type4']))
//             {
//                $type4=$_POST['type4'];
//             }else{
//                 $type4=0;
//             }
//             // 阳台
//           if(!empty($_POST['type5']))
//             {
//                $type5=$_POST['type5'];

//             }else{
//                 $type5=0;
//             }

         
//             // 阁楼
//           if(!empty($_POST['type6']))
//             {
//                $type6=$_POST['type6'];
//             }else{
//                 $type6='0';
//             }
            
//             // 地下室
            
//           if(!empty($_POST['type7']))
//             {
//                $type7=$_POST['type7'];
//             }else{
//                 $type7=0;
//             }
                
//              if(empty($type1))
//              {
//                 $type1="0";
//              }
                
//              $sum=$type1+$type6+$type7;
             
//              $str=$sum;

         
              
//              if(!empty($type2))
//              {
//                  $str.=",".$type2;
             
//              }else{
//                 $str.=",0";
//              }

//              if(!empty($type3))
//              {
//                 $str.=",".$type3;
//              }else{
//                 $str.=",0";
//              }

//              if(!empty($type4))
//              {
//                 $str.=",".$type4;
//              }else{
//                 $str.=",0";
//              }
        
//              if(!empty($type5))
//              {
//                 $str.=",".$type5;
//              }else{
//                 $str.=",0";
//              }
             
            
//              // 全屋面积
//              if(!empty($_POST['area']))
//              {
//                  $area=$_POST['area'];
//              }
             
             
//              //室面积
//              if(!empty($_POST['bedroom']))
//              {
//                $arr1=$_POST['bedroom'];
//              }else{
//                 $arr1=0;
//              }

//              if(!empty($arr1))
//              {  
//                 $area1=implode(",", $arr1);
//                 $area_1=$area1;
//                 $area_1_1 = $area_1;
//              }
     
//              //厅面积
//              if(!empty($_POST['liveroom']))
//              {
//                 $arr2=$_POST['liveroom'];  
//              }else{
//                 $arr2=0;
//              }
               
//              //厨房面积
//              if(!empty($_POST['kitchen']))
//              {
//               $arr3=$_POST['kitchen'];
//              }else{
//                 $arr3=0;
//              }
//              //浴室面积
//              if (!empty($_POST['bathroom']))
//              {
//               $arr4=$_POST['bathroom'];
//              }else{
//                 $arr4=0;
//              }
//              //阳台面积
//              if(!empty($_POST['balcony']))
//              {
//                $arr5=$_POST['balcony'];
//              }else{
//                 $arr5=0;
//              }
//              //阁楼面积
//              if(!empty($_POST['gelou']))
//              {
//               $arr6=$_POST['gelou'];
//              }else{
//                 $arr6=0;
//              }
//              if(!empty($_POST['database']))
//              {
//              //地下室面积
//               $arr7=$_POST['database'];
//              }else{
//                 $arr7=0;
//              }

//              if ($arr1==0) {
//             $area1 ='';
//            }

//            if ($arr2==0) {
//             $area2 ='';
//            }

//            if ($arr3==0) {
//             $area3 ='';
//            }
//            if ($arr4==0) {
//             $area4 ='';
//            }
//            if ($arr5==0) {
//             $area5 ='';
//            }
//            if ($arr6==0) {
//             $area6 ='';
//            }
//            if ($arr7==0) {
//             $area7 ='';
//            }


//            if(!empty($arr2) || $arr2==0)
//            {
//             if ($arr2!=0) {
//                  $area2=implode(",", $arr2);
//             }
         
//            }
             
             
           
//            if(!empty($arr3) || $arr3==0)
//            {
//             if ($arr3!=0) {
//                  $area3=implode(",", $arr3);
//             }
         
//            }
           
  

         
//            if(!empty($arr4) || $arr4==0)
//            {
//             if ($arr4!=0) {
//                 $area4=implode(",", $arr4);
//             }
            
//            }
            
           
//              if(!empty($arr5))
//             {
//                $area5=implode(",", $arr5);
               
//             } 
            
//             if(!empty($arr6))
//             {
//                $area5=implode(",", $arr6);
               
//             } 

//             if(!empty($arr7))
//             {
//                $area5=implode(",", $arr7);
               
//             } 
            
//             $str2=$area1."|".$area2."|".$area3."|".$area4."|".$area5."|".$area6."|".$area7;
          
//         }
//         //var_dump($housetype);die;
//         if ($housetype=='') {
//             // echo jump(2,'提交失败','dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id);
//             // header('refresh:3;url="dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id.'"' );
//             $this->error('提交失败1');
//             $this->redirect('adjust_order',array('order_id'=>$order_id));
//             exit;
//         }
        
       
//         if (!empty($arr1)) {
//             foreach ($arr1 as $key => $value) {
//                 if ($value=='') {
//                     // echo jump(2,'提交失败','dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id);
//                     // header('refresh:3;url="dealer.php?editOrder&order_id='.$order_id.'&detail_id='.$detail_id.'"' );
//                     $this->error('提交失败2');
//                     $this->redirect('adjust_order',array('order_id'=>$order_id));
//                     exit;
//                 }
//             }  

//         }

//         if (!empty($arr2)) {
//             foreach ($arr2 as $key => $value) {
//                 if ($value=='') {
//                     $this->error('提交失败3');
//                     $this->redirect('adjust_order',array('order_id'=>$order_id));
//                     exit;
//                 }
//             }
//         }

//         if (!empty($arr3)) {
//             foreach ($arr3 as $key => $value) {
//                 if ($value=='') {
//                     $this->error('提交失败4');
//                     $this->redirect('adjust_order',array('order_id'=>$order_id));
//                     exit;
//                 }
//             }
//         }

//         if (!empty($arr4)) {
//             foreach ($arr4 as $key => $value) {
//                 if ($value=='') {
//                     $this->error('提交失败5');
//                     $this->redirect('adjust_order',array('order_id'=>$order_id));
//                     exit;
//                 }
//             }
//         }

//         if (!empty($arr5)) {
//             foreach ($arr5 as $key => $value) {
//                 if ($value=='') {
//                     $this->error('提交失败6');
//                     $this->redirect('adjust_order',array('order_id'=>$order_id));
//                     exit;
//                 }
//             }
//         }

//         if (!empty($arr6)) {
//             foreach ($arr6 as $key => $value) {
//                 if ($value=='') {
//                     $this->error('提交失败7');
//                     $this->redirect('adjust_order',array('order_id'=>$order_id));
//                     exit;
//                 }
//             }
//         }

//         if (!empty($arr7)) {
//             foreach ($arr7 as $key => $value) {
//                 if ($value=='') {
//                     $this->error('提交失败8');
//                     $this->redirect('adjust_order',array('order_id'=>$order_id));
//                     exit;
//                 }
//             }
//         }

//         $attic = $type6;
//         $basement = $type7;
//         $attic_area = $area6;
//         $basement_area = $area7;

//         $house_data = D('dealer')->getDealerAdjustInfo($_POST['order_sn']);

//         $detailCity = M('xgj_furnish_order_info')->where(array('order_id'=>$order_id))->find();
//         //D('dealer')->getFind('xgj_furnish_order_info','order_id = '.$order_id);

//         $pcda = explode('-', $detailCity['house_city']);

//         $orderInfo = D('dealer')->getOrderInfo($order_id);

//         $homeInfo['user_id']      =$orderInfo['user_id'];
//         $homeInfo['order_code']   =$_POST['order_sn'];
//         $homeInfo['house_layout'] =$str;
//         $homeInfo['total_area']   =$area;
//         $homeInfo['type_area']    =$str2;
//         $homeInfo['province']     =$pcda['0'];
//         $homeInfo['city']         =$pcda['1'];
//         $homeInfo['district']     =$pcda['2'];
//         $homeInfo['address']      =$pcda['3'];
//         $homeInfo['house_type']   =$housetype;
//         $homeInfo['people']       =$detailCity['people'];

//         if (empty($house_data)) {
//             $houseid=M('xgj_dealer_adjust_info')->add($homeInfo);
//             //D('dealer')->addOrder('xgj_dealer_adjust_info',$homeInfo);
//         //如果成功，获取系统id
//             if($houseid==true){
//                 $house_data['id'] = $houseid;
//                 $jump = 1;
//             }else{
//                 $this->error('提交失败9');
//                 $this->redirect('adjust_order',array('order_id'=>$order_id));
//                 exit;
//             }
//         }else{
//             $return = M('xgj_dealer_adjust_info')->where(array('id'=>$house_data['id']))->save($homeInfo);
//             //D('dealer')->editOrder('xgj_dealer_adjust_info',$homeInfo,'id='.$house_data['id']);
//             if($return==true){
//                 $jump = 1;
//             }else{
//                 $this->error('提交失败10');
//                 $this->redirect('adjust_order',array('order_id'=>$order_id));
//                 exit;
//             }

//         }
        
//         if ($jump == 1) {
            
//             $homesarea = explode('|', $homeInfo['type_area']);
//             array_pop($homesarea);
//             $homesarea['0'] = array_sum(explode(',', $homesarea['0']));
//             $homesareas = array_sum($homesarea);

//             $quote_id = D('dealer')->getQuote($order_id);

//             //require_once(WWW_DIR . "/classes/priceController.php");
//             //A('Furnish')->getQuote();

//             //$order = new priceController();

//             foreach ($quote_id as $key => $value) {

//                 $lists[$key] = A('Furnish')->getQuote($value['quote_id'],$value['level'],$house_data['id']);

//                 if ($lists[$key]=='error') {
//                     $detaildata['adjust_stuff_goods'] = '';
//                     $detaildata['adjust_quote_price'] = '';
//                     $detaildata['adjust_cost'] = '';
//                 }else{
//                     $detaildata['adjust_stuff_goods'] = $lists[$key]['1'];

//                     //处理调整后系统总价
//                     $sale = M('xgj_furnish_quote')->where(array('quote_id'=>$value['quote_id']))->getField('sale');
//                    // D('dealer')->getField('xgj_furnish_quote',"quote_id = ".$value['quote_id'],'sale');

//                     $detaildata['adjust_quote_price'] = ceil(($lists[$key]['all']-$lists[$key]['install'])/100*$sale)+$lists[$key]['install'];
//                     $detaildata['adjust_cost'] = $lists[$key]['install'];

//                 }


//                 $data = M('xgj_furnish_order_detail')->create($detaildata);
//                 $return1[$key] = M('xgj_furnish_order_detail')->where(array('detail_id'=>$value['detail_id']))->save($detaildata);
//                 dump($data);exit;
//                 //D('dealer')->editOrder('xgj_furnish_order_detail', $detaildata, "detail_id=".$value['detail_id']);
//             }
            
//             $quoteInfo = D('dealer')->getQuote($order_id);

//             foreach ($quoteInfo as $key => $value) {
//                 // $adjust_zp_money[] = $value['adjust_quote_price']*0.1;
//                 $adjust_goods_amount[] = $value['adjust_quote_price'];
//             }

//             // $detaildatas['adjust_zp_money'] = round(array_sum($adjust_zp_money));
//             $detaildatas['adjust_goods_amount'] = round(array_sum($adjust_goods_amount));

//             $detaildatas['order_status'] = 5;

//             $return2 = M('xgj_furnish_order_info')->where(array('order_code'=>$_POST['order_sn']))->save($detaildatas);
//             //D('dealer')->editOrder('xgj_furnish_order_info', $detaildatas, "order_code=".$_POST['order_sn']);
//             if ($return2 !== false) $this->success('提交成功');
//         }


    }
   
    
    //退出
    public function logOut(){
        unset($_SESSION['dealerId']);
        unset($_SESSION['dealerName']);
        unset($_SESSION['d_company']);
		unset($_SESSION['pad_post']);
		unset($_SESSION['moneyList']);
        $this->redirect('index');
    }
    
    //申请结算
    public function getaccount(){

        $getMoneyNum = I('post.getMoneyNum');

        //帐户余额
        $totalMoney = D('dealer')->accountBalance($_SESSION['dealerId']);

        if ($getMoneyNum <= 0) {
            $this->error('金额错误');exit;
        }

        if($getMoneyNum > $totalMoney){
            $this->error('余额不足，不能提交申请');exit;
        }

        $info=D('dealer')->checkAccount($_SESSION['dealerId']);

        if($info > 0){
            $this->success('已提交申请，审核完成后，方可再次申请');exit;
        }
        // $check = D('dealer')->checkOrderId($_GET['order_id']);
        // if (!empty($check)){
        //  //header("location:dealer.php?account");
        //  echo " <script type='text/javascript'>alert('已经申请过结算，请不要重复申请');history.back();</script>";
        //  exit();
        // }
        //print_r($_GET);
        $data = array();
        //$data['order_id'] = $_GET['order_id'];
        $data['d_id']           = $_SESSION['dealerId'];
        $data['money']          = I('post.getMoneyNum');
        $data['finance_status'] = 0;
        $data['apply_time']     = time();
        
        //exit();
        $result = D('dealer')->getaccount('xgj_furnish_finance',$data);
        if ($result){ 
            $this->success('申请结算成功，请耐心等待审核');
            // echo jump(1,'申请结算成功，请耐心等待审核','dealer.php?account');
            // header("refresh:3;url='dealer.php?account'" );
            // exit;
        }else {
            $this->error('申请结算失败，请重新申请');
            // echo jump(2,'申请结算失败，请重新申请','dealer.php?account');
            // header("refresh:3;url='dealer.php?account'" );
            // exit;
        }
    }

    
    
    // //申请提现
    // public function getMoney(){
    //  $dealer = new dealer_model();
    //  $tpl = get_smarty();
        
    //  $dealerInfo = D('dealer')->getDealerInfo($_SESSION['dealerId']);//经销商信息
        
    //  if (!empty($_POST)){
    //      if (empty($_POST['getMoneyNum'])){
    //          echo "<script type='text/javascript'>alert('请填写提现的金额');history.back();</script>";
    //          exit();
    //      }
            
    //      $dealerId = $_SESSION['dealerId'];
            
    //      $totalMoney = $_POST['totalMoney'];
    //       if ($_POST['getMoneyNum']>$totalMoney){
    //          $this->display('dealer_error.tpl.html');
    //          echo "<script type='text/javascript'>alert('申请提现金额大于账户余额，请重新填写！');history.back();</script>";
    //          exit();
    //      }  
    //      //正则过滤数据
    //       if (!preg_match('/^[1-9][0-9]*(.?[0-9]*)?$/', $_POST['getMoneyNum'])){
                
    //          $this->display('dealer_error.tpl.html');
    //          echo "<script type='text/javascript'>alert('提现金额数据非法');history.back();</script>";
    //      }else {
    //          echo '提现金额数据合法';
    //      } 
    //      //print_r($_POST);
            
            
    //      $data = array();
    //      $data['d_id'] = $_SESSION['dealerId'];;
    //      $data['money_num'] = $_POST['getMoneyNum'];
    //      $data['remarks'] = $_POST['remarks'];
    //      $data['apply_time'] = time();
    //      $result = D('dealer')->getMoney('xgj_furnish_get_money',$data);
    //      if ($result){
    //          echo "<script type='text/javascript'>alert('申请提现成功，请耐心等待审核');history.back();</script>";
    //      }else {
    //          echo "<script type='text/javascript'>alert('申请提现失败，请重新申请');history.back();</script>";
                
    //      }
    //  }   
        //$this->display();
        
    // }
    
    
}