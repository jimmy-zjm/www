<?php
/**
 * Created by PhpStorm.
 * User: 唐文权
 * Date: 2015/12/21
 * Time: 16:29
 */



require_once(WWW_DIR."/model/userModel.php");
require_once(WWW_DIR."/libs/page.php");
require_once(WWW_DIR."/libs/UploadFile.class.php");//加载UploadFile类
require_once(WWW_DIR."/model/xgj_furnish.php");
class userController{

	private $num=5;	 //欧团我的订单每页显示几条数据
	/**
	 * center	显示个人中心
	 */

	//积分
	public function integral(){
		//调用判断是否登录的方法
		user_check_logon();
		$model = new userModel();
		//分页每页的条数
		$num=10;
		if(empty($_GET['p']) || $_GET['p']<=1) $page = 1;
		else $page = $_GET['p'];
		//分页的总条数
		$count = $model->integralCount(1);
		//显示列表内容
		$list=$model->integral($page,$num,1);
		//实例化分页类
		$t = new Page($num, $count, $page, 5, "user.php?integral&p=");
		//分页样式
		$page=$t->subPageCss2();//分页样式

		//模板传值
		$tpl = get_smarty();

		$trees = $this->trees();
		$tpl->assign("eu_tree",$trees['data']);
		$tpl->assign("ov_tree",$trees['cate_list']);
		
		$tpl->assign("totalIntegral",$this->totalIntegral());
		$tpl->assign("page",$page);
		$tpl->assign('list',$list);
		$tpl->display('user_integral.html');
	}


	//手机注册页面
	public function wapreg(){
		//调用判断是否登录的方法
		
		$tpl = get_smarty();

		$tpl->display('user_wapreg.html');
	}

	public function dowapreg(){
		$model = new userModel();
		$data = $model->dowapreg();
	}


	//积分收入
	public function integralIncome(){
		//调用判断是否登录的方法
		user_check_logon();
		$model = new userModel();
		//分页每页的条数
		$num=10;
		if(empty($_GET['p']) || $_GET['p']<=1) $page = 1;
		else $page = $_GET['p'];
		//分页的总条数
		$count = $model->integralCount(2);
		//显示列表内容
		$list=$model->integral($page,$num,2);
		//实例化分页类
		$t = new Page($num, $count, $page, 5, "user.php?integralIncome&p=");
		//分页样式
		$page=$t->subPageCss2();//分页样式

		//模板传值
		$tpl = get_smarty();

		$trees = $this->trees();
		$tpl->assign("eu_tree",$trees['data']);
		$tpl->assign("ov_tree",$trees['cate_list']);

		$tpl->assign("totalIntegral",$this->totalIntegral());
		$tpl->assign("page",$page);
		$tpl->assign('list',$list);
		$tpl->display('user_integralIncome.html');
	}

	//积分支出
	public function integralExpenditure(){
		//调用判断是否登录的方法
		user_check_logon();
		$model = new userModel();
		//分页每页的条数
		$num=10;
		if(empty($_GET['p']) || $_GET['p']<=1) $page = 1;
		else $page = $_GET['p'];
		//分页的总条数
		$count = $model->integralCount(3);
		//显示列表内容
		$list=$model->integral($page,$num,3);
		//实例化分页类
		$t = new Page($num, $count, $page, 5, "user.php?integralExpenditure&p=");
		//分页样式
		$page=$t->subPageCss2();//分页样式

		//模板传值
		$tpl = get_smarty();

		$trees = $this->trees();
		$tpl->assign("eu_tree",$trees['data']);
		$tpl->assign("ov_tree",$trees['cate_list']);

		$tpl->assign("totalIntegral",$this->totalIntegral());
		$tpl->assign("page",$page);
		$tpl->assign('list',$list);
		$tpl->display('user_integralExpenditure.html');
	}

	public function totalIntegral(){
		$model = new userModel();
		$count = $model->totalIntegral();
		return $count;
	}

	public function trees(){
		$pn= new home();
		$data['data']=$pn->category(1);
		$data['cate_list'] = $pn->Ov_Category();
		return $data;
	}
	
	function center()
	{
		//var_dump(substr('201604051808016555f2',0,18));exit;
		//调用判断是否登录的方法
		user_check_logon();

		//查出用户信息
		$user = new userModel();
		$userInfo = $user->centerSelInfo($_SESSION["userId"]);
		$concern = $user->selectConcern($_SESSION["userId"]);
		//var_dump($userInfo);die();

		$tpl = get_smarty();
		//给变量赋值
		$tpl->assign("userInfo", $userInfo);		//用户ID

		
		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
		
		$tpl->assign("concern",$concern);
		//var_dump('<pre>',$concern);die();
		$tpl->display('user_center.tpl.html');
	}


	/**
	 * order	我的订单  《欧洲团购，德国母婴》
	 */
	public function order(){
		user_check_logon();
		$tpl = get_smarty();
		$order = new userModel();
		//判断是否有分页
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}
		if(!isset($_GET['tab'])){
			$tab = 'all';
		}else{
			$tab = $_GET['tab'];
		}
		switch ($tab) {
			case 'all':
				$status='';
				break;
			case 'df':
				$status='0';
				break;
			case 'dfh':
				$status='1';
				break;
			case 'ds':
				$status='2';
				break;
			case 'dp':
				$status='4';
				break;
			default:
				$status='';
				break;
			return $status;
		}
//var_dump($status);exit;
		//分页每页的条数
		$num=5;
		$orderInfoAllList=$order->orderInfoAllList($page,$num,$status);//显示列表内容
		$count_order=$order->count_order($status);//分页的总条数
		$t_nav = new Page($num, $count_order, $page, 5, "user.php?order&tab=$tab&p=");
		$page=$t_nav->subPageCss2();//分页样式
		$tpl->assign('OrderAll',$orderInfoAllList); //全部订单的数据列表
	    $tpl->assign("orderAllNum", $count_order);  //订单总数量
	    $tpl->assign("page", $page);  //订单总数量
 //var_dump($orderInfoAllList);exit;
	    $pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);//欧洲建材分类
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);//海外超市分类
		$tpl->assign("tab",$tab);
		if(isset($_GET['test'])){
			$tpl->display('user_order.tpl222.html');
		}else{
			$tpl->display('user_order.tpl.html');
		}
		
	}
	/***
	确认收货
	*/
	public function theGoods(){
		user_check_logon();
		$db=new db();
		$id=$_POST['id'];
		$order_id=$_POST['order_id'];
		$data=array(
				'order_status'=>4,
		);
		if($db->update('xgj_eu_split_order',$data,"id=$id") != false){
			$arr=$db->getOne("select count(id) from xgj_eu_split_order where order_status<'4' and order_id=$order_id ");
			// $arr1=$db->getOne("select total_goods_num from xgj_eu_order where order_id=$order_id ");
			if($arr=='0'){
				$db->update('xgj_eu_order',$data,"id=$order_id");
			}
			echo "1";exit;
		}else{
			echo "2";exit;
		}
	}

	/***
	取消订单
	*/
	public function cancel(){
		user_check_logon();
		$db=new db();
		$order_id=$_POST['order_id'];
		$data=array(
				'order_status'=>6,
		);
		if($db->update('xgj_eu_order',$data,"id=$order_id") != false){
			echo "1";exit;
		}else{
			echo "2";exit;
		}
	}
	/***
	删除订单
	*/
	public function delOrder(){
		user_check_logon();
		$db=new db();
		$order_id=$_POST['order_id'];
		$data=array(
				'order_status'=>7,
		);
		if($db->update('xgj_eu_order',$data,"id=$order_id") != false){
			echo "1";exit;
		}else{
			echo "2";exit;
		}
	}

	/**
	 * order	我的订单  《欧洲团购，德国母婴》
	 */
	public function supermarketOrder(){
		user_check_logon();
		$tpl = get_smarty();
		$order = new userModel();
		//判断是否有分页
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}
		if(!isset($_GET['tab'])){
			$tab = 'all';
		}else{
			$tab = $_GET['tab'];
		}
		switch ($tab) {
			case 'all':
				$status='';
				break;
			case 'df':
				$status='0';
				break;
			case 'dfh':
				$status='1';
				break;
			case 'ds':
				$status='2';
				break;
			case 'dp':
				$status='4';
				break;
			default:
				$status='';
				break;
			return $status;
		}
//var_dump($status);exit;
		//分页每页的条数
		$num=5;
		$orderInfoAllList=$order->ovorderInfoAllList($page,$num,$status);//显示列表内容
		$count_order=$order->count_ovorder($status);//分页的总条数
		$t_nav = new Page($num, $count_order, $page, 5, "user.php?supermarketOrder&tab=$tab&p=");
		$page=$t_nav->subPageCss2();//分页样式
		$tpl->assign('OrderAll',$orderInfoAllList); //全部订单的数据列表
	    $tpl->assign("orderAllNum", $count_order);  //订单总数量
	    $tpl->assign("page", $page);  //订单总数量
 // var_dump($orderInfoAllList);exit;
	    $pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);//欧洲建材分类
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);//海外超市分类
		$tpl->assign("tab",$tab);
		if(isset($_GET['test'])){
			$tpl->display('user_order.tpl222.html');
		}else{
			$tpl->display('user_supermarketOrder.tpl.html');
		}
		
	}
	/***
	确认收货
	*/
	public function ovtheGoods(){
		user_check_logon();
		$db=new db();
		$id=$_POST['id'];
		$order_id=$_POST['order_id'];
		$data=array(
				'order_status'=>4,
		);

		if($db->update('xgj_ov_split_order',$data,"id=$id") != false){
			$arr=$db->getOne("select count(id) from xgj_ov_split_order where order_status<'4' and order_id=$order_id ");
			// $arr1=$db->getOne("select total_goods_num from xgj_ov_order where order_id=$order_id ");
			if($arr=='0'){
				$db->update('xgj_ov_order',$data,"id=$order_id");
			}
			echo "1";exit;
		}else{

			echo "2";exit;
		}
	}

	/***
	取消订单
	*/
	public function ovcancel(){
		user_check_logon();
		$db=new db();
		$order_id=$_POST['order_id'];
		$data=array(
				'order_status'=>6,
		);
		if($db->update('xgj_ov_order',$data,"id=$order_id") != false){
			echo "1";exit;
		}else{
			echo "2";exit;
		}
	}
	/***
	删除订单
	*/
	public function delovOrder(){
		user_check_logon();
		$db=new db();
		$order_id=$_POST['order_id'];
		$data=array(
				'order_status'=>7,
		);
		if($db->update('xgj_ov_order',$data,"id=$order_id") != false){
			echo "1";exit;
		}else{
			echo "2";exit;
		}
	}

	// public function order(){
	// 	user_check_logon();
	// 	$order = new userModel();
	// 	$tpl = get_smarty();
	// 	$p = 1;
	// 	//分页每页的条数
	// 	$num=$this->num;
	// 	//总条数
	// 	$orderInfoAllList=$order->orderInfoAllList();
	// 	$orderAll = count($orderInfoAllList);
	// 	//总页数
	// 	$pcount = ceil($orderAll/$num);
		
	// 	$orderInfoAllList1=$order->orderInfoAllList($p,$num);
	// 	$tpl->assign('orderInfoSelResult',$orderInfoAllList1); //全部订单的数据列表
	//     $tpl->assign("orderAll", count($orderInfoAllList));  //订单总数量
	// 	$tpl->assign("pcount",$pcount);

	//     //待付款订单信息
	//     //总条数
	// 	$orderInfoWaitPayList=$order->orderInfoWaitPayList();
	// 	$orderAll2 = count($orderInfoWaitPayList);
	// 	//总页数
	// 	$pcount2 = ceil($orderAll2/$num);
	// 	$orderInfoWaitPayList1 = $order->orderInfoWaitPayList($p,$num);
	// 	$tpl->assign("orderInfoWaitPayList", $orderInfoWaitPayList1); //待付款的数据列表
	// 	$tpl->assign("orderNotPay", count($orderInfoWaitPayList));  //待付款数量
	// 	$tpl->assign("pcount2",$pcount2);

	// 	//待收货订单信息
	// 	//总条数
	// 	$orderInfoWaitReceivList=$order->orderInfoWaitReceivList();
	// 	$orderAll3 = count($orderInfoWaitReceivList);
	// 	//总页数
	// 	$pcount3 = ceil($orderAll3/$num);
	// 	$orderInfoWaitReceivList1 = $order->orderInfoWaitReceivList($p,$num);
	// 	$tpl->assign("orderInfoWaitReceivList", $orderInfoWaitReceivList1); //待收货的数据列表
	// 	$tpl->assign("orderWaitReceiv", count($orderInfoWaitReceivList));  //待收货数量
	// 	$tpl->assign("pcount3",$pcount3);

	// 	//待评价订单信息
	// 	//总条数
	// 	$orderInfoWaitAssessList=$order->orderInfoWaitAssessList();
	// 	$orderAll4 = count($orderInfoWaitAssessList);
	// 	//总页数
	// 	$pcount4 = ceil($orderAll4/$num);
	// 	$orderInfoWaitAssessList1 = $order->orderInfoWaitAssessList($p,$num);
	// 	$tpl->assign("orderInfoWaitAssessList", $orderInfoWaitAssessList1);	//待评价的数据列表
	// 	$tpl->assign("orderWaitAssess", count($orderInfoWaitAssessList));   //待评价数量
	// 	$tpl->assign("pcount4",$pcount4);
		
	//     $tpl->assign("p",$p);

	//     $pn= new home();
	// 	$data=$pn->category(1);
	// 	$tpl->assign("eu_tree", $data);
	// 	$cate_list = $pn->Ov_Category();
	// 	$tpl->assign("ov_tree",$cate_list);
	// 	$tpl->display('user_order.tpl.html');
	// }

	//欧团我的订单分页
	function order_tabs1(){
		$page = $_GET['page'];
		$tab = $_GET['tab'];
		$num = $this->num;
		$order = new userModel();
		
		if ($tab == 1) { 	//全部订单
			$x = 'orderInfoAllList';
		}else if($tab == 2){	//待付款
			$x = 'orderInfoWaitPayList';
		}else if($tab == 3){	//待收货
			$x = 'orderInfoWaitReceivList';
		}else if($tab == 4){	//待评价
			$x = 'orderInfoWaitAssessList';
		}

		//显示列表内容
		$orderInfoAllList=$order->$x();
		//总条数
		$orderAll = count($orderInfoAllList);
		//总页数
		$pcount = ceil($orderAll/$num);

		$orderInfoAllList1=$order->$x($page,$num);
		
		echo '                  	
            <div class="user_order-center-title">
                <div class="user_order-center-title-01">
                    近三个月订单
                </div>
                
                <div class="user_order-center-title-02">
                    订单详情
                </div>
                
                <div class="user_order-center-title-03">
                    收货人
                </div>
                
                <div class="user_order-center-title-04">
                    金额
                </div>
                
                <div class="user_order-center-title-05">
                    全部状态
                </div>
                
                <div class="user_order-center-title-06">
                    操作
                </div>
            </div> 
            
            <div class="clear"></div>';
            if (!empty($orderInfoAllList1)){
            	foreach($orderInfoAllList1 as $num => $v){
        		echo ' 
		            <div class="user_order-center-list">                        
		                <div class="user_order-center-list-01">
		                    订单号：'.$v['sn'].'
		                </div>
		                
		                <div class="user_order-center-list-02">                            	
		                    '.date($v['add_time']).'
		                </div>
		                
		                <div class="user_order-center-list-03" title="'.$v['shr_name'].'">
		                    '.$v['shr_name'].'
		                </div>
		                
		                <div class="user_order-center-list-04">
		                    ￥'.$v['deal_price'].'
		                </div>
		                
		                <div class="user_order-center-list-05">';
		                    if ($v['order_status'] == 0){
		                    	echo '待付款';
		                	}else if($v['order_status'] == 1){
		                		echo '待发货';
		                	}else if($v['order_status'] == 2){
		                		echo '待收货';
		                	}else if($v['order_status'] == 3){
		                		echo '退货中';
		                	}else if($v['order_status'] == 4){
		                		echo '待评价';
		                	}else if($v['order_status'] == 5){
		                		echo '已完成';
		                	}else if($v['order_status'] == 6){
		                		echo '已取消';
		                	}
		            echo '
		                </div>
		                
		                <div class="user_order-center-list-06">';
		                    if ($v['order_status'] == 0){
		                    	echo '<a href="order.php?payOrder&order_id='.$v['id'].'">点击付款</a>';
		                    }else if($v['order_status'] == 1){
		                    	echo '确认收货';
		                    }else if($v['order_status'] == 2){
		                    	echo '<a href="user.php?order_status=2&order_id='.$v['id'].'&sn='.$v['sn'].'">确认收货</a>';
		                    }else if($v['order_status'] == 4){
		                    	echo '<a href="user.php?order_status=4&order_id='.$v['id'].'&sn='.$v['sn'].'">评价晒单</a>
		                    &nbsp&nbsp<a href="user.php?order_status=4&dorder_id='.$v['id'].'&sn='.$v['sn'].'">删除</a>';
		                    }else if($v['order_status'] == 5){
		                    	echo '<a href="user.php?order_status=5&dorder_id='.$v['id'].'&sn='.$v['sn'].'">删除</a>';
		                    }else if($v['order_status'] == 6){
		                    	echo '<a href="user.php?order_status=6&dorder_id='.$v['id'].'&sn='.$v['sn'].'">删除</a>';
		                    }
		            echo '        
		                </div>
		                
		                <div class="clear"></div>                        
		            </div>';
            	}
			}
        echo '    
            <div class="clear27"></div>
            
            <!--分页开始-->
            <div class="page">';
              if (!empty($page)){
	            echo '
	              <a href="javascript:;" onclick="page('.$tab.',1)">首页</a>';  
	            if ($page==1) {
              		echo '<a href="javascript:;">[上一页]</a>';
              	}else{
              		echo '<a href="javascript:;" onclick="page('.$tab.','.($page-1).')">[上一页]</a>';
              	}
	          }
              if ($page > 2){
	            echo '
	              <a href="javascript:;" onclick="page('.$tab.','.($page-2).')">'.($page-2).'</a>';
              }
              if ($page > 1){
	            echo '
	              <a href="javascript:;" onclick="page('.$tab.','.($page-1).')">'.($page-1).'</a>';
              }
              echo '<span>'.$page.'</span>';
              if ($pcount > $page){
	            echo '
	              <a href="javascript:;" onclick="page('.$tab.','.($page+1).')">'.($page+1).'</a>';
              }
              if ($pcount > ($page+1)){
	            echo '
	              <a href="javascript:;" onclick="page('.$tab.','.($page+2).')">'.($page+2).'</a>';
              }
              if (!empty($page)){
              	if ($page<$pcount) {
              		echo '<a href="javascript:;" onclick="page('.$tab.','.($page+1).')">[下一页]</a>';
              	}else{
              		echo '<a href="javascript:;">[下一页]</a>';
              	}
				echo '
	              <a href="javascript:;" onclick="page('.$tab.','.$pcount.')">[尾页]</a>';
              }
        echo '        
            </div>
            <!--分页结束-->       
            
            <div class="clear27"></div>
		';

	}

	/**
	 * 修改订单状态
	 */
	function order_status(){
		$option = intval($_GET['order_status']);
		if (!empty($_GET['order_id'])) {
			$order_id = intval($_GET['order_id']);
		}
		if (!empty($_GET['dorder_id'])) {
			$dorder_id = intval($_GET['dorder_id']);
			$order_id = $dorder_id;
		}

		$order_sn = $_GET['sn'];
		if (empty($order_sn) || empty($order_id) || empty($option) || $option>7 || $option<1) {
			header("Location:user.php?order");exit;
		}
		$model = new userModel();

		$order = $model->orderall($order_id,$order_sn);
		if (empty($order) || $option!=$order['0']['order_status']) {
			header("Location:user.php?order");exit;
		}

		if ($option==2) {
			$data = array('order_status'=>4,'post_status'=>3); //确认收货  order_status-4   post_status-3
		}else if($option==4 && empty($dorder_id)){
			header("Location:user.php?evaluateShow");exit;
			// $data = array('order_status'=>5,'is_comment'=>1); //评价  order_status-5  is_comment-1
		}else if($option==7 || !empty($dorder_id) && $option==4 || !empty($dorder_id) && $option==5 || !empty($dorder_id) && $option==6 ){
			$data = array('order_status'=>7); //删除    order_status-7
		}
		$save = $model->ordersave($data,$order_id,$order_sn);
		
		header("Location:user.php?order");
		
		
	}

	/**
	 * 关注的商品
	 */
	
	function concernGoods(){

		user_check_logon();

		$tpl = get_smarty();

		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);

		$concern = new userModel();
		//分页每页的条数
		$num=8;

		//全部订单
		if(empty($_GET['p']) || $_GET['p']<=1){
			$page = 1;
			$p1 = 1;
		}else{
			$page = $_GET['p'];
			$p1 = 0;
		}
		

		//显示列表内容
		$orderInfoAllList=$concern->concernGoodsList('count');

		$orderInfoAllList1=$concern->concernGoodsList($page,$num);
		//分页的总条数
		$orderAll = count($orderInfoAllList);
		// echo $orderAll;exit;
		//实例化分页类
		$t = new Page($num, $orderAll, $page, 5, "user.php?concernGoods&p=");
		//分页样式
		$page=$t->subPageCss2();//分页样式
		//模板传值
		if (empty($orderInfoAllList1) && $p1 == 1) {
			$tpl->assign("page",'');
		}else{
			$tpl->assign("page",$page);
		}
		$tpl->assign('concernGoodsList',$orderInfoAllList1); //全部订单的数据列表
	    $tpl->assign("orderAll", count($orderInfoAllList));  //订单总数量
		$tpl->display('user_concern.tpl.html');




	}
	/**
	 * 欧洲建材订单详情
	 */
	function euOrderDetails(){
		user_check_logon();

		if(empty($_GET['id']) || !preg_match("/^[0-9]+$/", $_GET['id'])){
		    echo "<SCRIPT type='text/javascript'>alert('该订单不存在!!!');history.back();</SCRIPT>";exit;
		}

		$splitId = $_GET['id'];
		$model=new userModel();
		$data = $model->euOrderDetails($splitId);

		if ($data == 'error') {
			echo "<SCRIPT type='text/javascript'>alert('该订单不存在!!!');history.back();</SCRIPT>";exit;
		}

		$tpl = get_smarty();
		$tpl->assign("data", $data);
		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
		$tpl->display('user_euOrderDetails.tpl.html');
	}

	/**
	 * 海外超市订单详情
	 */
	function ovOrderDetails(){
		user_check_logon();

		if(empty($_GET['id']) || !preg_match("/^[0-9]+$/", $_GET['id'])){
		    echo "<SCRIPT type='text/javascript'>alert('该订单不存在!!!');history.back();</SCRIPT>";exit;
		}

		$splitId = $_GET['id'];
		$model=new userModel();
		$data = $model->ovOrderDetails($splitId);
		
		if ($data == 'error') {
			echo "<SCRIPT type='text/javascript'>alert('该订单不存在!!!');history.back();</SCRIPT>";exit;
		}

		$tpl = get_smarty();
		$tpl->assign("data", $data);
		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
		$tpl->display('user_ovOrderDetails.tpl.html');
	}
	
	/**
	 *  取消关注
	 * 
	 */
	function cancergoods(){
		$deletedata=new userModel();
	
		if (isset($_GET["arr"])){
			
			$arr=$_GET['arr']; 
			$arr=explode(",", $arr);
			
		  foreach ($arr  as  $value)
		  {
		    $id=substr($value, 3,2);
		       
		    $deletedata->deletegoods($id);
		   
		  }	
			
		}
	
	   echo  "<script>window.location.href='user.php?concernGoods';</script>";
		
	}

  	/**
	 *  取消关注
	 * 
	 */
  	function cancergoods1(){
  		$model=new userModel();
  		if ($_GET['cancergoods1']=='del') {
  			$return = $model->deletegoods1($_GET['id']);
  		}else if ($_GET['cancergoods1']=='delall') {
  			$id = explode(',', trim($_GET['id'],','));
  			if ($id['0']=='on') {
  				unset($id['0']);
  			}
  			foreach ($id as $key => $value) {
  				$return = $model->deletegoods1($value);
  			}
  		}
  	}

  	/**
	 *  加入购物车
	 * 
	 */
  	function eucart(){
  		$model=new userModel();
  		if ($_GET['eucart']=='add') {
  			$return = $model->eucart($_GET['id']);
  		}else if ($_GET['eucart']=='addall') {
  			$id = explode(',', trim($_GET['id'],','));
  			if ($id['0']=='on') {
  				unset($id['0']);
  			}
  			foreach ($id as $key => $value) {
  				$return = $model->eucart($value);
  			}
  			echo $return;
  		}
  	}
      
	/**
	 * 读取个人信息
	 */
	function userInfo(){

		//调用判断是否登录的方法
		user_check_logon();

		$tpl = get_smarty();

		//查询出信息
		$userInfo = new userModel();
		$infoList = $userInfo->userInfoList($_SESSION["userId"]);
      	$tpl->assign("infoId", $infoList["user_id"]);	//ID  
		$tpl->assign("infoEmail", $infoList["email"]);	//邮箱
		$tpl->assign("infoUserName", $infoList["real_name"]);	//用户名
		$tpl->assign("infoFace", $infoList["face"]);	//头像
		$tpl->assign("infoAlias", $infoList["user_name"]);	//昵称
		$tpl->assign("infoMobilePhone", $infoList["mobile_phone"]);	//手机
		$tpl->assign("infoAddr", $infoList["addr"]);	//地址
		$tpl->assign("infoIdentityCard", $infoList["identity_card"]);	//身份证
		//性别
		if($infoList["sex"] == 0){
			$tpl->assign("infoSex", '<input type="radio" name="sex" class="jdradio" value="0" checked="checked" /><label class="mr10">男</label><input type="radio" name="sex" class="jdradio" value="1"><label class="mr10" >女</label><input type="radio" name="sex" class="jdradio" value="2" /><label class="mr10">保密</label>');
		}elseif($infoList["sex"] == 1){
			$tpl->assign("infoSex", '<input type="radio" name="sex" class="jdradio" value="0" /><label class="mr10">男</label><input type="radio" name="sex" class="jdradio" value="1" checked="checked" /><label class="mr10">女</label><input type="radio" name="sex" class="jdradio" value="2" /><label class="mr10">保密</label>');
		}elseif($infoList["sex"] == 2){
			$tpl->assign("infoSex", '<input type="radio" name="sex" class="jdradio" value="0" /><label class="mr10">男</label><input type="radio" name="sex" class="jdradio" value="1" /><label class="mr10">女</label><input type="radio" name="sex" class="jdradio" value="2"  checked="checked" /><label class="mr10">保密</label>');
		}

		//月收入
		if($infoList["monthly_profit"] == "1万以上"){
			$tpl->assign("infoMonthlyProfit",'<select id="selectmenu4" name="infoMonthlyProfitSelName"><option>请选择</option><option selected="selected">1万以上</option><option>8千——1万</option><option>6千——8千</option><option>4千——6千</option><option>4千以下</option></select>');
		}elseif($infoList["monthly_profit"] == "8千——1万"){
			$tpl->assign("infoMonthlyProfit",'<select id="selectmenu4" name="infoMonthlyProfitSelName"><option>请选择</option><option>1万以上</option><option selected="selected">8千——1万</option><option>6千——8千</option><option>4千——6千</option><option>4千以下</option></select>');
		}elseif($infoList["monthly_profit"] == "6千——8千"){
			$tpl->assign("infoMonthlyProfit",'<select id="selectmenu4" name="infoMonthlyProfitSelName"><option>请选择</option><option>1万以上</option><option >8千——1万</option><option selected="selected">6千——8千</option><option>4千——6千</option><option>4千以下</option></select>');
		}elseif($infoList["monthly_profit"] == "4千——6千"){
			$tpl->assign("infoMonthlyProfit",'<select id="selectmenu4" name="infoMonthlyProfitSelName"><option>请选择</option><option>1万以上</option><option>8千——1万</option><option>6千——8千</option><option selected="selected">4千——6千</option><option>4千以下</option></select>');
		}elseif($infoList["monthly_profit"] == "4千以下"){
			$tpl->assign("infoMonthlyProfit",'<select id="selectmenu4" name="infoMonthlyProfitSelName"><option>请选择</option><option>1万以上</option><option>8千——1万</option><option>6千——8千</option><option>4千——6千</option><option selected="selected">4千以下</option></select>');
		}else{
			$tpl->assign("infoMonthlyProfit",'<select id="selectmenu4" name="infoMonthlyProfitSelName"><option selected="selected">请选择</option><option>1万以上</option><option>8千——1万</option><option>6千——8千</option><option>4千——6千</option><option>4千以下</option></select>');
		}

		//教育程度
		if($infoList["education_status"] == "初中"){
			$tpl->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option>请选择</option><option selected="selected">初中</option><option>高中</option><option>大学</option><option>硕士</option><option>博士</option><option>其他</option></select>');
		}elseif($infoList["education_status"] == "高中"){
			$tpl->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option>请选择</option><option>初中</option><option selected="selected">高中</option><option>大学</option><option>硕士</option><option>博士</option><option>其他</option></select>');
		}elseif($infoList["education_status"] == "大学"){
			$tpl->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option>请选择</option><option>初中</option><option>高中</option><option selected="selected">大学</option><option>硕士</option><option>博士</option><option>其他</option></select>');
		}elseif($infoList["education_status"] == "硕士"){
			$tpl->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option>请选择</option><option>初中</option><option>高中</option><option>大学</option><option selected="selected">硕士</option><option>博士</option><option>其他</option></select>');
		}elseif($infoList["education_status"] == "博士"){
			$tpl->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option>请选择</option><option>初中</option><option>高中</option><option>大学</option><option>硕士</option><option selected="selected">博士</option><option>其他</option></select>');
		}elseif($infoList["education_status"] == "其他"){
			$tpl->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option>请选择</option><option>初中</option><option>高中</option><option>大学</option><option>硕士</option><option>博士</option><option selected="selected">其他</option></select>');
		}else{
			$tpl->assign("educationStatus",'<select id="selectmenu5" name="educationStatusSelName"><option selected="selected">请选择</option><option>初中</option><option>高中</option><option>大学</option><option>硕士</option><option>博士</option><option>其他</option></select>');
		}

		//生日(年份)
		$birthdayArray = explode("-",$infoList["birthday"]);
		$birYearList = $birthdayArray[0];

		$birYear = "<select id='selectmenu1' name='birYearSelName'>";
		foreach (range(99,30) as $year){
			if ($birYearList == "19".$year){
				$birYear .= "<option selected='selected'>19".$year."</option>";
			}else {
				$birYear .= "<option>19".$year."</option>";
			}
		}
		$birYear .= "</select>";

		//生日(月份)
		$birMonthList = $birthdayArray[1];

		$birMonth = "<select id='selectmenu2' name='birMonthSelName'>";
		foreach (range(1,12) as $month){
			if ($birMonthList == $month){
				$birMonth .= "<option selected='selected'>".$month."</option>";
			}else {
				$birMonth .= "<option>".$month."</option>";
			}
		}
		$birMonth .= "</select>";

		//生日(日期)
		$birDateList = $birthdayArray[2];

		$birDate = "<select id='selectmenu3' name='birDateSelName'>";
		foreach (range(1,31) as $date){
			if ($birDateList == $date){
				$birDate .= "<option selected='selected'>".$date."</option>";
			}else {
				$birDate .= "<option>".$date."</option>";
			}
		}
		$birDate .= "</select>";

		$tpl->assign("infoBirthdayYear",$birYear);
		$tpl->assign("infoBirthdayMonth",$birMonth);
		$tpl->assign("infoBirthdayDate",$birDate);
		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
		$tpl->display('user_info.tpl.html');

	}


	/**
	 * 修改个人信息
	 */
	function userInfoEdit(){
		$db = new db();
		//默认是失败状态
         $re=0;  $rs=0;
		//调用判断是否登录的方法
		user_check_logon();
		if($_POST){

			$userId = $_SESSION["userId"];
			if(!empty($_FILES['userFace']['name'])){
				$res=upload('userFace','face');
				if($res){
					$re=$db->update('xgj_users',array("face"=>$res['images']),"user_id = {$userId}");
					if ($re) {
							$oldName=$_POST['oldimg'];
							// 删除单张图片
							if(!empty($oldName)){
								@unlink(TP_APP_URL."/Public/Uploads/$oldName");
							}
					}
				}		
				
			}
			$data['user_name'] = $_POST["infoAlias"];	//昵称
			//$data['mobile_phone']  = $_POST["infoMobilePhone"];	//手机
			$data['email'] = $_POST["infoEmail"];	//邮箱
			//$infoAddr = $_POST["infoAddr"];	//地址
			$data['real_name'] = $_POST["name"];
			$data['identity_card'] = $_POST["infoIdentityCard"];	//身份证
			$birYear = $_POST["birYearSelName"];	//生日(年份)
			$birMonth = $_POST["birMonthSelName"];	//生日(月份)
			$birDate = $_POST["birDateSelName"];	//生日(日期)
			$data['birthday'] = $birYear."-".$birMonth."-".$birDate;	//生日
			$data['sex'] = $_POST["sex"];	//性别
			$data['monthly_profit'] = $_POST["infoMonthlyProfitSelName"];	//月收入
			$data['education_status']= $_POST["educationStatusSelName"];   //教育程度
			$data['work'] = $_POST["occupation"];  //所在行业
         
			$userEdit = new userModel();
			
			$rs = $userEdit->userInfoEdit($data,$userId); 
			if($rs==1||$re==1){
				echo "<SCRIPT type='text/javascript'>alert('修改成功!!!');history.back();</SCRIPT>";
			}else{
				echo "<SCRIPT type='text/javascript'>alert('修改失败,请重试!!!');history.back();</SCRIPT>";
				exit();
			}

		}

	}


	/**
	 * 收货地址查询
	 */
	function addr(){
		user_check_logon();
		$tpl = get_smarty();

		$addrInfo = new userModel();
		$addrInfoList = $addrInfo->addrInfoSel($_SESSION["userId"]);
		$addrCount = $addrInfo->addrCount($_SESSION["userId"]);
		// echo '<pre>';
  //      	var_dump($addrInfoList);exit;
		$tpl->assign("addrInfoList",$addrInfoList);
		$tpl->assign("addrCount",$addrCount["addrCount"]);
		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
		$tpl->display('user_address.tpl.html');
	}


	/**
	 * 默认地址
	 */
	function addrDefaultSet(){
		$addrId = $_GET["addrId"];
		$addrDefaultSet = new userModel();
		$addrDefaultResult = $addrDefaultSet->addrDefaultSet($addrId,$_SESSION["userId"]);

		if($addrDefaultResult > 0){
			header("Location:user.php?addr");
		}else{
			echo "<SCRIPT type='text/javascript'>alert('设置为默认地址失败,请重试!!!');history.back();</SCRIPT>";
			exit();
		}
	}


	/**
	 * 显示添加收货地址页面
	 */
	function addrInfoAddShow(){
		user_check_logon();
		$tpl = get_smarty();
		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
		$tpl->display('user_addrAdd.tpl.html');

	}

	/**
	 * 实行收货地址添加
	 */
	function doAddrInfoAdd(){
		if($_POST){
			$userId = $_SESSION["userId"];	//用户ID
			$receivingName = $_POST["receivingName"];	//收货人姓名
			$mobile = $_POST["mobile"];	//收货人手机
			$phone = $_POST["phone"];	//收货人固定电话
			$email = $_POST["email"];	//收货人邮箱
			$addr = $_POST["addr"];	//收货人详细地址

			if (empty($mobile) || empty($receivingName) || empty($addr) || empty($_POST["cho_Province"]) || empty($_POST["cho_City"]) || empty($_POST["cho_Area"])) {
				echo "<SCRIPT type='text/javascript'>alert('请填写完整再提交，谢谢！');history.back();</SCRIPT>";
				exit();
			}

			if(!preg_match("/^1[34578]\d{9}$/", $mobile)){
			    echo "<SCRIPT type='text/javascript'>alert('请填写有效的手机号码');history.back();</SCRIPT>";
				exit();
			}
			
			$data = array(
				'user_id'=>$userId, 
				'a_name'=>$receivingName, 
				'a_mobile_phone'=>$mobile, 
				'a_phone'=>$phone, 
				'a_email'=>$email, 
				'a_addr'=>$addr,
				'a_pro'=>$_POST["cho_Province"],
				'a_city'=>$_POST["cho_City"],
				'a_area'=>$_POST["cho_Area"]
				);
			$doAddrInfoAdd = new userModel();
			$doAddrInfoAddResult = $doAddrInfoAdd->doAddrInfoAdd($data);

			if($doAddrInfoAddResult > 0){
				header("Location:user.php?addr");
			}else{
				echo "<SCRIPT type='text/javascript'>alert('添加地址失败,请重试!!!');history.back();</SCRIPT>";
				exit();
			}
		}

	}


	/**
	 * 显示修改收货地址
	 */
	function addrInfoEditShow(){
		user_check_logon();
		$tpl = get_smarty();

		$addrId = $_GET["addrId"];
		$addrInfoSelforEdit = new userModel();
		$addrInfoSelList = $addrInfoSelforEdit->addrInfoSelOne($addrId);

		// echo '<pre>';
		// var_dump($addrInfoSelList);exit;

		$tpl->assign("addrInfoSelList",$addrInfoSelList);
		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
		$tpl->display('user_addrEdit.tpl.html');

	}

	/**
	 * 实行修改收货地址
	 */
	function doAddrInfoEdit(){
		// echo '<pre>';
		// var_dump($_POST);exit;
		if($_POST){
			$addrId = $_POST["addrId"];	//地址(主键)ID
			$receivingName = $_POST["receivingName"];	//收货人姓名
			$mobile = $_POST["mobile"];	//收货人手机
			$phone = $_POST["phone"];	//固定电话
			$email = $_POST["email"];	//收货人邮箱
			$addr=$_POST["addr"];//收货人详细地址

			if (empty($mobile) || empty($receivingName) || empty($addr) || empty($_POST["cho_Province"]) || empty($_POST["cho_City"]) || empty($_POST["cho_Area"])) {
				echo "<SCRIPT type='text/javascript'>alert('请填写完整再提交，谢谢！');history.back();</SCRIPT>";
				exit();
			}

			if(!preg_match("/^1[34578]\d{9}$/", $mobile)){
			    echo "<SCRIPT type='text/javascript'>alert('请填写有效的手机号码');history.back();</SCRIPT>";
				exit();
			}
			
			$data = array(
				'a_name'=>$receivingName, 
				'a_mobile_phone'=>$mobile, 
				'a_phone'=>$phone, 
				'a_email'=>$email, 
				'a_addr'=>$addr,
				'a_pro'=>$_POST["cho_Province"],
				'a_city'=>$_POST["cho_City"],
				'a_area'=>$_POST["cho_Area"]
				);

			//$addr = $_POST["addr"];	//收货人详细地址
			$doAddrInfoAdd = new userModel();
			$doAddrInfoEditResult = $doAddrInfoAdd->doAddrInfoEdit($data,$addrId);

			// echo $doAddrInfoEditResult;exit;
			
			if($doAddrInfoEditResult > 0){
				header("Location:user.php?addr");
			}else{
				echo "<SCRIPT type='text/javascript'>alert('修改地址失败,请重试!!!');history.back();</SCRIPT>";
				exit();
			}

		}

	}


	/**
	 * addrInfoDel	删除收货地址
	 */
	function addrInfoDel(){
		$addrId = $_GET["addrId"];
		$addrInfoDel = new userModel();
		$addrInfoDelResult = $addrInfoDel->addrInfoDel($addrId);

		if($addrInfoDelResult > 0){
			header("Location:user.php?addr");
		}else{
			echo "<SCRIPT type='text/javascript'>alert('删除地址失败,请重试!!!');history.back();</SCRIPT>";
			exit();
		}
	}


	/**
	 * register	显示注册页面
	 */
	function register()
	{
		$_SESSION['redirect_url'] = 'index.php';
		$tpl = get_smarty();

		$tpl->display('register.tpl.html');
	}


	/**
	 * checkRegister     表示验证用户名的方法
	 * @param	null
	 * @return	void
	 */
	function checkRegister(){
		
		$user = new userModel();
		if(!empty($_POST["usermobile_phone"])){
		  $user->registerSelCheckmobile_phone(trim($_POST["usermobile_phone"]));
		}
	    if(!empty($_POST["userName"])){
		   $user->registerSelCheckName(trim($_POST["userName"]));
	    }
	    if(!empty($_POST["coupon"]) && !empty($_POST["cpassword"])){
	     	$status=$user->registerSelCheckroll(trim($_POST["coupon"]),trim($_POST["cpassword"]));
	    }else if(!empty($_POST["coupon"])){
	    	$status=$user->checkCoupon(trim($_POST["coupon"]));
	    }
	    		
	}

	
	/**
	 * checkRegister1     表示找回密码验证用户名的方法
	 **/
	function checkRegister1(){
		$user = new userModel();
		$phone=trim($_POST["usermobile_phone"]);
		if(isset($phone)){
		  	$status = $user->registerSelCheckmobile_phone($phone);
		}
	}
   
	/**
	 * dohousedata   表示添加房屋信息的方法
	 * 
	 * 
	 */

	function  dohousedata(){

		if (!empty($_POST)) {
			$_SESSION['price_post']=$_POST;
			$_SESSION['redirect_url'] = 'user.php?dohousedata';
		}
		

		if (!empty($_SESSION['price_post'])) {
			$_POST = $_SESSION['price_post'];
			$_GET['cid'] = $_SESSION['price_post']['cid'];
		}

		

	   $userhouse = new userModel();

	   // echo '<pre>';
	   // var_dump($_POST);exit;

	   if ($_POST['house'] == 1) {
	       $_POST['type6'] = '';
	       $_POST['type7'] = '';
	       $_POST['gelou'] = '';
	       $_POST['database'] = '';
	   }

	   if(!empty($_GET['cid']))
	    {
	   		$cid=$_GET['cid'];
	    }


	   if($_POST)
	   {  
	   	
	   	$province="";
	   	$city="";
	   	$town="";
	   	$detail="";
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
	   	
	   	
	    	//省级
	   	   if(!empty($_POST['cho_Province']))
	   	   {
	   	      $province=$_POST['cho_Province'];
	   	   }
	   	  //市级
	   	  if (!empty($_POST['cho_City']))
	   	  {
              $city=$_POST['cho_City'];
	   	  }
	   	  //县 区
	   	  if(!empty($_POST['cho_Area']))
	   	  {
              $town=$_POST['cho_Area'];
	   	  }
	   	   //详细地址
	   	   if(!empty($_POST['diqu']))
	   	   {
            $detail=$_POST['diqu'];
            if($_POST['diqu']=="请输入地址")
            {
            	$detail="";
            }
	   	   }
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

	   	     

	   	     // if (empty($area)) {
	   	     // 	echo "<script>alert('请填写完整再提交，谢谢！')</script>";
	   	     // 	header("Location:price.php?cid=$cid");exit;
	   	     // }
	   	     
	   	     
	   	     
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
           	$area1 =0;
           }

           if ($arr2==0) {
           	$area2 =0;
           }

           if ($arr3==0) {
           	$area3 =0;
           }
           if ($arr4==0) {
           	$area4 =0;
           }
           if ($arr5==0) {
           	$area5 =0;
           }
           if ($arr6==0) {
           	$area6 =0;
           }
           if ($arr7==0) {
           	$area7 =0;
           }

	   	    

	   	     // if(!empty($arr2))
	   	     // {	
	   	     //     $area2=implode(",", $arr2);

	   	         
	   	         
	   	     //     if(!empty($arr3) or !empty($arr4) or!empty($arr5))
	   	     //     {
	   	     //   	     $area2.="|";
	   	       	    
	   	     //     }
              
	   	     // }
	   	   if(!empty($arr2) || $arr2==0)
           {
           	if ($arr2!=0) {
           		 $area2=implode(",", $arr2);
           	}
           	 $area2.="|";
         
           }
	   	     
	   	     
           
           if(!empty($arr3) || $arr3==0)
           {
           	if ($arr3!=0) {
           		 $area3=implode(",", $arr3);
           	}
           	 $area3.="|";
         
           }
           
  

         
           if(!empty($arr4) || $arr4==0)
           {
           	if ($arr4!=0) {
	            $area4=implode(",", $arr4);
	        }
            
             $area4.="|";
            
           }
            
           
             if(!empty($arr5))
            {
               $area5=implode(",", $arr5);
               
            } 
            
            
            if(!empty($arr6) && empty($arr7))
            {	
              $area6=implode(",", $arr6);
              $area1.=",".$area6.',0';
        
            } 
           
           if(!empty($arr7) && empty($arr6))
           {
           	
              $area7=implode(",", $arr7);
             
              $area1.=",0,".$area7;
            
           }
           if(!empty($arr7) && !empty($arr6))
           {
           	
              $area7=implode(",", $arr7);
              $area6=implode(",", $arr6);
             
              $area1.=",".$area6.",".$area7;
            
           }
            if(empty($arr7) && empty($arr6))
           {
           	
           $area1.=",0,0";
            
           }

           

          //   echo $area1;echo '<br>';
	   	     // echo $area2;echo '<br>';
	   	     // echo $area3;echo '<br>';
	   	     // echo $area4;echo '<br>';
	   	     // echo $area5;echo '<br>';
	   	     // echo $area6;echo '<br>';
	   	     // echo $area7;echo '<br>';

	   	    
           
        if(!empty($area2)or!empty($area3)or!empty($area4)or!empty($area5))
            {
              $str2=$area1."|"."".$area2."".$area3."".$area4."".$area5;
            }
            
	   	else{
	   	  	    $str2=$area1."".$area2."".$area3."".$area4."".$area5;
	   	     }
	       
	    }

	    
	  
	  	// echo $str2;exit;
	    if ($housetype=='') {
	    	header("Location:price.php?cid=$cid&error=1");exit;
	    }
	    
	   
	    if (!empty($arr1)) {
	    	foreach ($arr1 as $key => $value) {
		    	if ($value=='') {
		    		header("Location:price.php?cid=$cid&error=1");exit;
		    	}
		    }  

	    }

	    if (!empty($arr2)) {
		    foreach ($arr2 as $key => $value) {
		    	if ($value=='') {
		    		header("Location:price.php?cid=$cid&error=1");exit;
		    	}
		    }
		}

	    if (!empty($arr3)) {
		    foreach ($arr3 as $key => $value) {
		    	if ($value=='') {
		    		header("Location:price.php?cid=$cid&error=1");exit;
		    	}
		    }
	    }

	    if (!empty($arr4)) {
		    foreach ($arr4 as $key => $value) {
		    	if ($value=='') {
		    		header("Location:price.php?cid=$cid&error=1");exit;
		    	}
		    }
	    }

	    if (!empty($arr5)) {
		    foreach ($arr5 as $key => $value) {
		    	if ($value=='') {
		    		header("Location:price.php?cid=$cid&error=1");exit;
		    	}
		    }
	    }

	    if (!empty($arr6)) {
		    foreach ($arr6 as $key => $value) {
		    	if ($value=='') {
		    		header("Location:price.php?cid=$cid&error=1");exit;
		    	}
		    }
		}

	    if (!empty($arr7)) {
		    foreach ($arr7 as $key => $value) {
		    	if ($value=='') {
		    		header("Location:price.php?cid=$cid&error=1");exit;
		    	}
		    }
		}

		if (empty($_SESSION['userName'])) {
			echo "<script>alert('抱歉！您需要请登录');self.location='user.php?login';</script>";exit;
		}

	    $attic = $type6;
	    $basement = $type7;
	    $attic_area = $area6;
		$basement_area = $area7;

	    $house_data = $userhouse->select_data($_SESSION["userId"]);
	    
	    if (empty($house_data)) {

	    	$houseid=$userhouse->dohousedata($_SESSION["userId"],$province,$city,$town,$detail,$housetype,$str,$area,$str2,$attic,$basement,$attic_area,$basement_area,$area_1);

	    //如果成功，获取系统id
		    if($houseid==true){
		    	unset($_SESSION['price_post']);
	    		header("Location:price.php?cid=$cid");exit;
		    }
		}else{


	    	
	  //   	$house_data = $userhouse->select_data($_SESSION["userId"]);
		 //    $house_layout = explode('|', $house_data['0']['type_area']);

			// $area_1 = $house_data['0']['area'];
			// $attic_area = $house_data['0']['attic_area'];
		 //    $basement_area = $house_data['0']['basement_area'];

		 //    if (!empty($arr1) && empty($arr6) && empty($arr7)) {
		 //    	$house_layout['0'] = $area_1_1.','.$attic_area.','.$basement_area;
		 //    	$area_1 = $area_1_1;
		 //    }
		 //    if (!empty($arr6) && empty($arr6) && empty($arr7)) {
		 //    	$house_layout['0'] = $area_1.','.$area6.','.$basement_area;
		 //    	$attic_area = $area6;
		 //    }
		 //    if (!empty($arr7) && empty($arr6) && empty($arr7)) {
		 //    	$house_layout['0'] = $area_1.','.$attic_area.','.$area7;
		 //    	$basement_area = $area7;
		 //    }

		 //    if (!empty($arr1) && !empty($arr6) && empty($arr7)) {
		 //    	$house_layout['0'] = $area_1_1.','.$area6.','.$basement_area;
		 //    	$area_1 = $area_1_1;
		 //    	$attic_area = $area6;
		 //    }

		 //    if (!empty($arr1) && !empty($arr7) && empty($arr6)) {
		 //    	$house_layout['0'] = $area_1_1.','.$attic_area.','.$area7;
		 //    	$area_1 = $area_1_1;
		 //    	$basement_area = $area7;
		 //    }

		 //    if (!empty($arr6) && !empty($arr7) && empty($arr1)) {
		 //    	$house_layout['0'] = $area_1.','.$area6.','.$area7;
		 //    	$attic_area = $area6;
		 //    	$basement_area = $area7;
		 //    }

		 //    if (!empty($arr1) && !empty($arr6) && !empty($arr7)) {
		    	
		 //    	$house_layout['0'] = $area_1_1.','.$area6.','.$area7;
		 //    	$area_1 = $area_1_1;
		 //    	$attic_area = $area6;
		 //    	$basement_area = $area7;
		 //    }
		   	
		 //   	if (!empty($arr2)) {
		 //   		$house_layout['1'] = trim($area2,'|');
		 //   	}
		 //   	if (!empty($arr3)) {
		 //   		$house_layout['2'] = trim($area3,'|');
		 //   	}
		 //   	if (!empty($arr4)) {
		 //   		$house_layout['3'] = trim($area4,'|');
		 //   	}
		 //   	if (!empty($arr5)) {
		 //   		$house_layout['4'] = trim($area5,'|');
		 //   	}

		 //   	echo $str2;echo "<br>";

		   	// $str2 = $house_layout['0'].'|'.$house_layout['1'].'|'.$house_layout['2'].'|'.$house_layout['3'].'|'.$house_layout['4'];
		   	// echo $area6;exit;
		   	// echo $area7;exit;
		   	// echo $str2;exit;

		   	$attic = $type6;
		   	$basement = $type7;
		   	$attic_area = $area6;
		   	$basement_area = $area7;

	    	$return = $userhouse->updata_data($_SESSION["userId"],$province,$city,$town,$detail,$housetype,$str,$area,$str2,$attic,$basement,$attic_area,$basement_area,$area_1);
	    	unset($_SESSION['price_post']);
    		
	    	// $house_data = $userhouse->select_data($_SESSION["userId"]);
    		// $_SESSION['house_data'] = $house_data;

    		header("Location:price.php?cid=$cid");exit;

	    }	
	  
	}

	
	/**
	 * 显示健康家居房屋信息
	 * homeoffer
	 * 
	 */
	function homeoffer(){
		   $housedata=new userModel();
		  
		   $tpl=get_smarty(); 
		   
		   $home= $housedata->dohomedata($_SESSION["userId"]);
		   
		   //给变量赋值
		   $tpl->assign("province",$home['province']);
		   $tpl->assign("city",$home['city']);
		   $tpl->assign("district",$home['district']);
		   $tpl->assign("address",$home['address']);
		   $tpl->assign("house_type",$home['house_type']);
		 
		   //房屋户型
		   $str1=explode(",", $home['house_layout']);
		  
		   //室
		   $tpl->assign("room1",$str1[0]);
		   //厅
		   $tpl->assign("room2",$str1[1]);
		   //厨
		   $tpl->assign("room3",$str1[2]);
		   //浴
		   $tpl->assign("room4",$str1[3]);
		   //阳台
		   $tpl->assign("room5",$str1[4]);

          
		     
		   //房屋总面积
		   $tpl->assign("total_area",$home['total_area']);
		
		   $tpl->display("homeoffer.tpl.html");
		
	}
 
	/**
	 * login	表示加载登录模板
	 */
	function login()
	{
		$tpl =get_smarty();
		/************************grass 2016-4-7**************************/
		if(isset($_SESSION['userId'])){
			//已经登陆
			if(isset($_SERVER['HTTP_REFERER'])){
				header('Location:'.$_SERVER['HTTP_REFERER']);
			}else{
				header('Location:index.php');
			}
		}

		//没有跳转地址, 默认为上一个来源页面
		if(empty($_SESSION['redirect_url'])){
			$_SESSION['redirect_url'] = getenv("HTTP_REFERER");
		}

		/************************grass 2016-4-7**************************/
		 $tpl->display('login.tpl.html');
        
		
	}
     
/*************************************grass***2016-4-7*************************************/
	/**
	 * 执行登陆
	 */
	function doLogin(){
		if(!empty($_POST)){
			/*接受数据*/
			$username = trim($_POST['userName']);
			$password = trim($_POST['passWord']);

			/*验证用户名或者密码不能为空*/
			if(empty($username) || empty($password)){
				die("<script>alert('用户名或者密码不能为空!!!');history.back();</script>");
			}

			// if (!is_numeric($username) || substr($username,0,1)=='-' || strlen($username)!='11') {
			if(!preg_match("/^1[34578]\d{9}$/", $username)){
				die("<script>alert('请输入正确手机号码!!!');history.back();</script>");
			}
			/*验证用户名*/
			// $username = mysql_real_escape_string($username);
			$password = md5($password.MD5_PASSWORD);

			$model = new userModel();

			$info = $model->dologinyz($username);

			if(!$info){
				//用户名不存在
				die("<script>alert('用户名不存在!!!');history.back();</script>");
			}

			/*验证密码*/
			if($info['password'] != $password){
				die("<script>alert('密码错误!!!');history.back();</script>");
			}

			/*保存用户信息*/
			$_SESSION['userId'] = $info['user_id'];
			$_SESSION['userName'] = $info['user_name'];
			$ip=get_client_ip();
			
			$db = new db();
			$db->query("UPDATE xgj_users SET last_time=".time().",last_ip='{$ip}' WHERE user_id={$_SESSION['userId']}");
		
			if(isset($_POST['auto_login'])){
			    //自动登陆, 保存登陆信息一个礼拜
			    setcookie(session_name(),session_id(),time()+86400*7,'/');
			}

			/*登陆成功之后*/
			//将本地购物车商品保存到数据库中
			if(!empty($_SESSION['cart'])){
				$user_id = $_SESSION['userId'];
				$cart    = $_SESSION['cart'];
				foreach ($cart as $k=>$v) {
					$tmp           = explode('-', $k);
					$goods_id      = (int)$tmp[0];
					$goods_attr_id = mysql_real_escape_string($tmp[1]);
					$goods_num     = (int)$v;
				    
				    $sql = "SELECT * FROM xgj_eu_cart WHERE user_id={$user_id} AND goods_id={$goods_id} AND goods_attr_id='{$goods_attr_id}'";
				    if($result = $db->query($sql)){
				        	//已经存在相同的商品
				    		$sql = "UPDATE xgj_eu_cart SET goods_num=goods_num+{$goods_num} WHERE id={$result['id']}";
				    	}else{
				    		$sql = "INSERT INTO xgj_eu_cart (user_id,goods_id,goods_attr_id,goods_num) VALUES ('{$user_id}','{$goods_id}','{$goods_attr_id}','{$goods_num}')";
				    	}
				    	$db->query($sql);
				  
				}
			}

			//执行跳转
			if (isset($_SESSION['redirect_url'])) {
				$redirect_url = $_SESSION['redirect_url'];
				unset($_SESSION['redirect_url']);
			    header("Location:".$redirect_url);die;
			}else{
			    header("Location:index.php");die;       
			}
		}
	}

/*************************************grass***2016-4-7*************************************/


        

	/**
	 * userQuit	退出
	 */
	function userQuit(){
		session_destroy();
		//var_dump($_SESSION);exit;
		// setcookie("userId","",time()-10);
		// setcookie("userName","",time()-10);
		//unset($_SESSION);
		// unset($_SESSION['userId']);
		// unset($_SESSION['userName']);


		// unset($_SESSION['cat_id']);
		// unset($_SESSION['furnish_quote']);
		// unset($_SESSION['discount_amount']);
		// unset($_SESSION['quote_gift']);
		// unset($_SESSION['quote_name']);
		// unset($_SESSION['house_datas']);
		// unset($_SESSION['house_data']);
		// unset($_SESSION['homebill_data']);
		// unset($_SESSION['quote_gift']);
		// unset($_SESSION['homebill_num']);
		// unset($_SESSION['homebill_aaa']);
		// unset($_SESSION['homebill']);
		// unset($_SESSION['error']);
		// unset($_SESSION['nonono']);
		// unset($_SESSION['price_post']);
		// unset($_SESSION['homebill_money']);
		 
		header("Location:index.php");
		 
	}


	/**
	 * 显示找回密码页面
	 */
	function findpassword(){
		$tpl = get_smarty();
   

		$tpl->display('findpassword.tpl.html');
	}
  	
  	public function dotel(){

  		if (!empty($_SESSION['tel']) && $_SESSION['tel'] != $_GET['tel']) {
			echo '1';exit; //手机号码与验证时的不一致
		}
		$findpassword= new userModel();
		$tel = $findpassword->selecttel($_GET['tel']);
		if (empty($tel)) {
			echo '2';exit;	//该手机未注册
		}else{
			echo '3';exit;	//成功
		}
  	}

  	public function domsg(){
  		$msg = $_GET['msg'];

  		if (empty($_SESSION['msg'])) {
			echo '2';exit;	//未获取验证码
		} 

  		if (!empty($_SESSION['msg']) && $_SESSION['msg'] != $msg) {
			echo '1';exit; //验证码错误
		}

		if (!empty($_SESSION['msg']) && $_SESSION['msg'] == $msg) {
			echo '3';exit; //成功
		}
		
		
		
  	}
	/**
	 *   dofindpassword  执行找回密码操作
	 * 
	 */

	public function dofindpassword(){
	

		$_SESSION['dofindpasswordbak']=$_POST;

		$findpassword= new userModel();

		if ($_POST['number_sj'] && $_POST['pass'] && $_POST['rpass'] && $_POST['rpass']){

			if (empty($_SESSION['msg'])) {
				header("Location:user.php?findpassword=6");exit;
			}
			
			if ($_POST['number_sj'] != $_SESSION['tel']) {
				header("Location:user.php?findpassword=5");exit;
			}

			if ($_POST['msg'] != $_SESSION['msg']) {
				header("Location:user.php?findpassword=1");exit;
			}

			if ($_POST['pass']!=$_POST['rpass']){
				header("Location:user.php?findpassword=2");exit;
			}

			// $iPhonenum=mysql_real_escape_string($_POST["number_sj"]);

			if(!preg_match("/^1[34578]\d{9}$/", $_POST["number_sj"])){
				header("Location:user.php?findpassword=7");exit;
			}else{
				$iPhonenum = $_POST["number_sj"];
			}

			// $password=mysql_real_escape_string($_POST["rpass"]);
			$password=$_POST["rpass"];

			$difindpassword=$findpassword->findpassworddata($iPhonenum,$password);

			if ($difindpassword == '1') {
				unset($_SESSION['msg']);
				unset($_SESSION['tel']);
				unset($_SESSION['dofindpasswordbak']);
				echo '<script>alert("密码修改成功!");window.location.href="user.php?login"</script>';exit;
			}else{
				header("Location:user.php?findpassword=3");exit;
			}
		}else {
			header("Location:user.php?findpassword=4");exit;
		}
		
	}

	/**
	 * 显示修改密码页面
	 */
	function passWordModifyShow(){

		user_check_logon();

		$tpl = get_smarty();

		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);

		$tpl->display('userPassWordModify.tpl.html');

	}
	
	/**
	 *    doregister     执行注册 
	 * 
	 */
	function doregister(){
		if($_POST){
			$phone       =  trim($_POST["mobile_phone"]);
			$username    =  trim($_POST["username"]);
			$password    =  trim($_POST["password"]);
			$password02  =  trim($_POST["password02"]);
			if($password!=$password02){
				echo jump(2,'两次密码不一致！',"user.php?register");
				header("refresh:2;url='user.php?register'" );exit;
			}
			$userdata= new userModel();
			$msg=isset($_SESSION['msg'])?$_SESSION['msg']:'';
			if($msg==$_POST['msg']){
				if($_POST['type']==1){
		             //无卷注册
					if(isset($phone) && !empty($phone)){
					  $nregister=$userdata->doRegister($phone,$username,$password);	
					}
					if($nregister==true && !empty($_SESSION['price_post'])){
						unset($_SESSION['msg']);
						echo jump(1,'注册成功！',"user.php?dohousedata",array('name'=>$username,'phone'=>$phone,'os'=>'1'));
						header("refresh:2;url='user.php?dohousedata'" );exit;
					}else{
						unset($_SESSION['msg']);
						echo jump(1,'注册成功！',"index.php",array('name'=>$username,'phone'=>$phone,'os'=>'1'));
						header("refresh:2;url='index.php'" );exit;
					}
				}elseif($_POST['type']==2){
					$db = new db();
					//var_dump($_POST);die;
					//有卷注册
					if(isset($phone) && !empty($phone)){
						$hregister=$userdata->doRegister($phone,$username,$password);
					}
					//如果注册成功  状态需要改变  (status)
					if($hregister==true){
						if(!empty($_POST["coupon"]) && !empty($_POST["cpassword"])){
			                $info=$db->getRow("select discount_amount,coupon_number from xgj_coupon where coupon_number='".$_POST["coupon"]."' and coupon_password='".$_POST["cpassword"]."'");
			                //var_dump($info);exit;
			                $res=$db->update('xgj_users',array('coupon'=>$info['discount_amount'],'coupon_number'=>$info["coupon_number"]),"user_id={$_SESSION['userId']}");
			                $db->update('xgj_coupon',array('status'=>1),"coupon_number={$_POST['coupon']}");
			            }
			            if($res){
			            	if (!empty($_SESSION['price_post'])) {
								unset($_SESSION['msg']);
								echo jump(1,'注册成功！',"user.php?dohousedata",array('name'=>$username,'phone'=>$phone,'os'=>'1'));
								header("refresh:2;url='user.php?dohousedata'" );exit;
							}else{
								unset($_SESSION['msg']);
								echo jump(1,'注册成功！',"user.php?center",array('name'=>$username,'phone'=>$phone,'os'=>'1'));
								header("refresh:2;url='user.php?center'" );
							}
			            }else{
			            	echo jump(2,'注册成功！优惠券激活失败！请到个人中心再次激活！',"user.php?center",array('name'=>$username,'phone'=>$phone,'os'=>'1'));
							header("refresh:2;url='user.php?center'" );
			            }
					}else{
						echo jump(2,'注册失败！',"user.php?register");
						header("refresh:2;url='user.php?register'" );
					}
				}
			}else{
				echo jump(2,'验证码错误，请重新填写！',"user.php?register");
				header("refresh:2;url='user.php?register'" );
			}
		}		  		
	}
    

	/**
	 * ajaxCheckPassWord	使用ajax检测原始密码是否正确
	 */
	function ajaxCheckPassWord(){
		//调用判断是否登录的方法
		user_check_logon();

		//实例化Model类
		$ajaxCheckPassWord = new userModel();

		//判断原始密码是否正确
		$checkResult = $ajaxCheckPassWord->checkPassWordToModify($_SESSION["userId"], md5($_POST["oldPassWord"].MD5_PASSWORD));

		if($checkResult > 1){
			echo 1;
		}else{
			echo 0;
		}
	}


	/**
	 * doPassWordModify	执行修改密码
	 */
	function doPassWordModify(){

		//调用判断是否登录的方法
		user_check_logon();

		//判断是否有POST数据提交
		if(!empty($_POST)){
          	
			$oldpass = trim($_POST["oldPassWordName"]);
			$newpass = trim($_POST["newPassWordName"]);
			$renewpass = trim($_POST["rPassWordName"]);

			if ($newpass != $renewpass) {
				echo "<SCRIPT type='text/javascript'>alert('修改密码失败,2次密码不一致,请重试!');history.back();</SCRIPT>";
				exit;
			}

			if(!preg_match("/^\w{6,15}$/", $newpass)){
			    echo "<SCRIPT type='text/javascript'>alert('修改密码失败,密码为6-15位数字字母下划线组成,请重试!');history.back();</SCRIPT>";
				exit;
			}

			$oldPassWord = md5($oldpass.MD5_PASSWORD);	//原始密码
			$newPassWord = md5($newpass.MD5_PASSWORD);	//新密码
			$renewPassWord = md5($renewpass.MD5_PASSWORD);	//确认新密码

			//实例化Model类
			$passWordModify = new userModel();

			//判断原始密码是否正确
			$checkResult = $passWordModify->checkPassWordToModify($_SESSION["userId"], $oldPassWord);

			if(!empty($checkResult)){	//如果原始密码正确
				$passWordModifyResult = $passWordModify->PassWordToModify($_SESSION["userId"], $oldPassWord, $newPassWord);

				if($passWordModifyResult > 0){
					echo "<SCRIPT type='text/javascript'>alert('修改密码成功!');window.location.href='user.php?center';</SCRIPT>";
					exit;
				}else{
					echo "<SCRIPT type='text/javascript'>alert('修改密码失败,请重试!');history.back();</SCRIPT>";
					exit;
				}
			}else{	//如果原始密码错误
				echo "<SCRIPT type='text/javascript'>alert('原始密码错误,请重试!');history.back();</SCRIPT>";
				exit;
			}

		}

	}

	function ajaxeditpass(){
		$pass = md5(trim($_POST['pass']));
		$passWordModify = new userModel();
		$checkResult = $passWordModify->checkPassWordToModify($_SESSION["userId"], $pass);
		if (!empty($checkResult)) {
			echo '1';exit;
		}else{
			echo '0';exit;
		}
	}


	/**
	 * 欧团评价页面数据显示
	 */
	function euEvaluateShowAll(){
		user_check_logon();
		$tpl = get_smarty();
		$model = new userModel();
		$data = $model->select_evaluateShow();   //欧团3月内待评价
		$data1 = $model->select_evaluateShow1(); //欧团3月内已评价

		$num=8;

		if(empty($_GET['p']) || $_GET['p']<=1) $page = 1;
		else $page = $_GET['p'];

		/************待评价分页*************/
		//全部订单
		$data_page=$model->select_evaluateShowAll($page,$num);

		//分页的总条数
		$orderAll = count($data)+count($data1);
		//实例化分页类
		$t = new Page($num, $orderAll, $page, 5, "user.php?euEvaluateShowAll&p=");
		//分页样式
		$page=$t->subPageCss2();//分页样式

		/*********************************/

		$tpl->assign("page",$page);	
		$tpl->assign("os",3);		
		$tpl->assign("data",$data_page);		
		$tpl->assign("cdata",count($data));	
		$tpl->assign("cdata1",count($data1));	
		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
		$tpl->display('user_evaluate.tpl.html');
	}

	function euEvaluateShowNone(){
		user_check_logon();
		$tpl = get_smarty();
		$model = new userModel();
		$data = $model->select_evaluateShow();   //欧团3月内待评价
		$data1 = $model->select_evaluateShow1(); //欧团3月内已评价

		$num=8;

		if(empty($_GET['p']) || $_GET['p']<=1) $page = 1;
		else $page = $_GET['p'];

		/************待评价分页*************/
		//全部订单
		$data_page=$model->select_evaluateShow($page,$num);

		//分页的总条数
		$orderAll = count($data);
		//实例化分页类
		$t = new Page($num, $orderAll, $page, 5, "user.php?euEvaluateShowNone&p=");
		//分页样式
		$page=$t->subPageCss2();//分页样式

		/*********************************/

		$tpl->assign("page",$page);	
		$tpl->assign("os",1);		
		$tpl->assign("data",$data_page);		
		$tpl->assign("cdata",count($data));	
		$tpl->assign("cdata1",count($data1));	
		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
		$tpl->display('user_evaluate.tpl.html');
	}

	function euEvaluateShowAlready(){
		user_check_logon();
		$tpl = get_smarty();
		$model = new userModel();
		$data = $model->select_evaluateShow();   //欧团3月内待评价
		$data1 = $model->select_evaluateShow1(); //欧团3月内已评价

		$num=8;

		if(empty($_GET['p']) || $_GET['p']<=1) $page = 1;
		else $page = $_GET['p'];

		/************待评价分页*************/
		//全部订单
		$data_page=$model->select_evaluateShow1($page,$num);

		//分页的总条数
		$orderAll = count($data1);
		//实例化分页类
		$t = new Page($num, $orderAll, $page, 5, "user.php?euEvaluateShowAlready&p=");
		//分页样式
		$page=$t->subPageCss2();//分页样式

		/*********************************/

		$tpl->assign("page",$page);	
		$tpl->assign("os",2);		
		$tpl->assign("data",$data_page);		
		$tpl->assign("cdata",count($data));	
		$tpl->assign("cdata1",count($data1));
		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
		$tpl->display('user_evaluate.tpl.html');
	}



	public function supermarketOrderPage(){
		user_check_logon();
		$tpl = get_smarty();
		$model= new userModel();

		if (empty($_GET['page']) || !preg_match("/^[0-9]+$/", $_GET['page']) || empty($_GET['num']) || !preg_match("/^[0-9]+$/", $_GET['num']) || empty($_GET['tab']) || !preg_match("/^[0-9]+$/", $_GET['tab'])) {
			$page      = 1;
			$num       = 8;
			$tab       = 1;
		}else{
			$page      = $_GET['page'];
			$num       = $_GET['num'];
			$tab       = $_GET['tab'];
		}

		if ($tab == 1) $where = 'order_status <= 5';
		else if($tab == 2) $where = 'order_status = 0';
		else if($tab == 3) $where = 'order_status > 1 and order_status <= 2';
		else if($tab == 4) $where = 'order_status = 4';

		$orderList = $model->supermarketOrderPage('','',$where);
		$count     = count($orderList);
		$pcount    = ceil($count/$num);

		$orderList = $model->supermarketOrderPage($page,$num,$where);
		// echo $page;
		// echo $pcount;exit;

		echo '<div class="user_order-center-title">
                <div class="user_order-center-title-01">
                    近三个月订单
                </div>
                <div class="user_order-center-title-02">
                    订单详情
                </div>
                <div class="user_order-center-title-03">
                    收货人
                </div>
                <div class="user_order-center-title-04">
                    金额
                </div>
                <div class="user_order-center-title-05">
                    全部状态
                </div>
                <div class="user_order-center-title-06">
                    操作
                </div>
                <div class="clear"></div> 
            </div> 
            <div class="clear"></div>';
        if(!empty($orderList)){
			foreach ($orderList as $k => $v) {
				echo   '<div class="user_order-center-list">
						<div class="user_order-center-list-01">订单号：'.$v['sn'].'</div>
	                    <div class="user_order-center-list-02">'.$v['add_time'].'</div>
	                    <div class="user_order-center-list-03" title="'.$v['shr_name'].'">'.$v['shr_name'].'</div>
	                    <div class="user_order-center-list-04">￥'.$v['deal_price'].'</div>
	                    <div class="user_order-center-list-05">';
				        		if ($v['order_status']==0) echo '待付款';
				            elseif ($v['order_status']==1) echo '待发货';
				            elseif ($v['order_status']==2) echo '待收货';
				            elseif ($v['order_status']==3) echo '退货中';
				            elseif ($v['order_status']==4) echo '待评价';
				            elseif ($v['order_status']==5) echo '已完成';
				            elseif ($v['order_status']==6) echo '已取消';
	            echo   '</div>
	                    <div class="user_order-center-list-06">';
	                        	if ($v['order_status']==0) echo '<a href="order.php?payOrder&order_id='.$v['id'].'">点击付款</a>&nbsp;<a href="javascript:;" onclick="delOrder('.$v['id'].')">取消</a>';
				            elseif ($v['order_status']==1) echo '确认收货';
				            elseif ($v['order_status']==2) echo '<a href="user.php?ov_order_status=2&order_id='.$v['id'].'&sn='.$v['sn'].'">确认收货</a>';
				            elseif ($v['order_status']==4) echo '<a href="user.php?ov_order_status=4&order_id='.$v['id'].'&sn='.$v['sn'].'">评价晒单</a>&nbsp&nbsp<a href="user.php?ov_order_status=4&dorder_id='.$v['id'].'&sn='.$v['sn'].'">删除</a>';
				            elseif ($v['order_status']==5) echo '<a href="user.php?ov_order_status=5&dorder_id='.$v['id'].'&sn='.$v['sn'].'">删除</a>';
				            elseif ($v['order_status']==6) echo '<a href="user.php?ov_order_status=6&dorder_id='.$v['id'].'&sn='.$v['sn'].'">删除</a>';
	            echo   '</div>
	                    <div class="clear"></div></div>';
			}
			echo   '<div class="clear27"></div>
	                <div class="page">';
		        	if ($page == 1)         echo '<a href="javascript:;">首页</a><a href="javascript:;">[上一页]</a>';
		        	if ($page > 1)          echo '<a href="javascript:;" onclick="page('.$tab.',1)">首页</a><a href="javascript:;" onclick="page('.$tab.','.($page-1).')">[上一页]</a>';
		        	if ($page-2 > 0) 	    echo '<a href="javascript:;" onclick="page('.$tab.','.($page-2).')">'.($page-2).'</a>';
		        	if ($page-1 > 0)        echo '<a href="javascript:;" onclick="page('.$tab.','.($page-1).')">'.($page-1).'</a>';
	    								    echo '<span class="laypage_curr">'.$page.'</span>';
	            	if ($page+1 <= $pcount) echo '<a href="javascript:;" onclick="page('.$tab.','.($page+1).')">'.($page+1).'</a>';
	            	if ($page+2 <= $pcount) echo '<a href="javascript:;" onclick="page('.$tab.','.($page+2).')">'.($page+2).'</a>';
	              
	              	if ($pcount > $page)    echo '<a href="javascript:;" onclick="page('.$tab.','.($page+1).')">[下一页]</a><a href="javascript:;" onclick="page('.$tab.','.$pcount.')">[尾页]</a>';
	            	else 				    echo '<a href="javascript:;">[下一页]</a><a href="javascript:;">[尾页]</a>';
	        echo   '</div>      
	                <div class="clear27"></div>';
        }else{
        	echo '';
        }

	}


	//海外超市评价（全部评论）
	public function supermarketOrderEvaluateNone(){
		user_check_logon();
		$tpl = get_smarty();
		$model= new userModel();

		$displayNum = 8;	//每页显示几条数据
		//总条数
		$countNone = $model->countsupermarketOrderEvaluateNone();
		$countBlock = $model->countsupermarketOrderEvaluateBlock();
		$list = $model->supermarketOrderEvaluateAll(" limit $displayNum");
		$tpl->assign('list',$list);
		$tpl->assign("pcount", ceil(($countNone+$countBlock)/$displayNum));
		$tpl->assign("displayNum", $displayNum);
		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
		$tpl->assign("countNone",$countNone);
		$tpl->assign("countBlock",$countBlock);
		$tpl->display('user_ovEvaluate_none.tpl.html');
	}

	//海外超市评价
	public function supermarketOrderEvaluateAlready(){
		user_check_logon();
		$tpl = get_smarty();
		$model= new userModel();

		if (empty($_GET['page']) || !preg_match("/^[0-9]+$/", $_GET['page']) || empty($_GET['num']) || !preg_match("/^[0-9]+$/", $_GET['num']) || empty($_GET['tab']) || !preg_match("/^[0-9]+$/", $_GET['tab'])) {
			$page      = 1;
			$num       = 8;
			$tab       = 1;
		}else{
			$page      = $_GET['page'];
			$num       = $_GET['num'];
			$tab       = $_GET['tab'];
		}

		$orderList = $model->supermarketOrderEvaluateAlready('','',$tab);

		$count     = count($orderList);
		$pcount    = ceil($count/$num);

		$orderList = $model->supermarketOrderEvaluateAlready($page,$num,$tab);
		if(!empty($orderList)){
			foreach ($orderList as $k => $v) {
				echo'<div class="userevaluate-center-list">
		            	<div class="clear"></div>
		            	<div class="userevaluate-center-list-img">
		                	<a href="javascript:;"><img src="'.getimages($v['goods_image']).'" /></a>
		                </div>
		                <div class="userevaluate-center-list-name">                            	
		                    <a href="javascript:;">'.$v['goods_title'].'</a> 
		                </div>
		                <div class="userevaluate-center-list-time">'.$v['pay_time'].'</div>
		                <div class="userevaluate-center-list-operation">';
		                if ($tab == 1) 	   echo '<a href="user.php?ovEvaluate&id='.$v['id'].'">点击评价</a>';
		                elseif ($tab == 2) echo '<a href="user.php?doOvEvaluate&id='.$v['id'].'">查看评价</a>';
		                elseif ($tab == 3) {
		                	if ($v['status']=='1') echo '<a href="user.php?doOvEvaluate&id='.$v['id'].'">查看评价</a>';
		                	else echo '<a href="user.php?ovEvaluate&id='.$v['id'].'">点击评价</a>';
		                }
		        echo'   </div>
		                <div class="clear"></div> 
		                <div class="userevaluate-center-list-write">
		                    <div class="clear"></div> 
		                </div>  
		                <div class="clear"></div>                        
	            	</div>';
	        }
	            
            echo '<div class="clear"></div>
		            <div class="page">';
		            if ($page == 1)         echo '<a href="javascript:;">首页</a><a href="javascript:;">[上一页]</a>';
		        	if ($page > 1)          echo '<a href="javascript:;" onclick="page('.$tab.',1)">首页</a><a href="javascript:;" onclick="page('.$tab.','.($page-1).')">[上一页]</a>';
		        	if ($page-2 > 0) 	    echo '<a href="javascript:;" onclick="page('.$tab.','.($page-2).')">'.($page-2).'</a>';
		        	if ($page-1 > 0)        echo '<a href="javascript:;" onclick="page('.$tab.','.($page-1).')">'.($page-1).'</a>';
	    								    echo '<span class="laypage_curr">'.$page.'</span>';
	            	if ($page+1 <= $pcount) echo '<a href="javascript:;" onclick="page('.$tab.','.($page+1).')">'.($page+1).'</a>';
	            	if ($page+2 <= $pcount) echo '<a href="javascript:;" onclick="page('.$tab.','.($page+2).')">'.($page+2).'</a>';
	              
	              	if ($pcount > $page)    echo '<a href="javascript:;" onclick="page('.$tab.','.($page+1).')">[下一页]</a><a href="javascript:;" onclick="page('.$tab.','.$pcount.')">[尾页]</a>';
	            	else 				    echo '<a href="javascript:;">[下一页]</a><a href="javascript:;">[尾页]</a>';
		    echo '</div>
		          <div class="clear27"></div>';
		}
		
	}

	public function ovEvaluate(){
		user_check_logon();
		$tpl = get_smarty();
		$model = new userModel();

		if(empty($_GET['id']) || !preg_match("/^[0-9]+$/", $_GET['id'])){
			echo "<SCRIPT type='text/javascript'>history.back();</SCRIPT>";exit;
		}
		$id = $_GET['id'];

		$getOvGoodsId = $model->getOvGoodsId($id);

		if(empty($getOvGoodsId)){
			echo "<SCRIPT type='text/javascript'>alert('该订单不存在');history.back();</SCRIPT>";exit;
		}

		$ovComment = $model->ovComment($id);

		if(!empty($ovComment)){
			echo "<SCRIPT type='text/javascript'>alert('该商品已评价');history.back();</SCRIPT>";exit;
		}

		$ovEvaluate = $model->ovEvaluate($id);

		$tpl->assign("ovEvaluate", $ovEvaluate);
		$tpl->assign("ogid", $id);
		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
		$tpl->display('user_ovEvaluate.tpl.html');
	}

	public function addOvEvaluate(){
		user_check_logon();
		$model = new userModel();

		if(empty($_POST['goods_id']) || !preg_match("/^[1-5]$/", $_POST['describe']) || !preg_match("/^[1-5]$/", $_POST['logistics']) || !preg_match("/^[1-5]$/", $_POST['goods'])){
		   	echo "<SCRIPT type='text/javascript'>history.back();</SCRIPT>";exit;
		}
		$ogid = $_POST['goods_id'];

		$goodsId = $model->getOvGoodsId($ogid);
		if(empty($goodsId)){
		   	echo "<SCRIPT type='text/javascript'>alert('没有此订单商品');history.back();</SCRIPT>";exit;
		}

		if (!empty($_FILES['images']['name'])) {
			for ($i=0; $i < 5; $i++) { 
				if (!empty($_FILES['images']['type'][$i])) {
					$type = explode('/', $_FILES['images']['type'][$i]);
					if ($type['0'] != 'image'){
						echo "<SCRIPT type='text/javascript'>alert('只能上传图片！');history.go(-1);</SCRIPT>";exit;
					}
				}
				$images[$i]['name']     = $_FILES['images']['name'][$i];
				$images[$i]['type']     = $_FILES['images']['type'][$i];
				$images[$i]['tmp_name'] = $_FILES['images']['tmp_name'][$i];
				$images[$i]['error']    = $_FILES['images']['error'][$i];
				$images[$i]['size']     = $_FILES['images']['size'][$i];
			}

			foreach ($images as $key => $value) {
				if (!empty($value['name'])){
					unset($_FILES);
					$_FILES[$key] = $value;
					$res[$key]=upload($key,'OvEvaluate');
					if ($res) $return[$key] = $res[$key]['images'];
				}
			}
		}

		if (trim($_POST['content'])=='请在此处写下您的宝贵意见及建议') $_POST['content'] = '';

		$addData = array(
			'goods_id'       => $goodsId,
			'order_goods_id' => $ogid,
			'user_name'      => $_SESSION['userName'],
			'add_time'       => time(),
			'user_id'        => $_SESSION['userId'],
			'`status`'       => 1,
			'`describe`'     => $_POST['describe'],
			'logistics'      => $_POST['logistics'],
			'goods'          => $_POST['goods'],
			'content'        => trim($_POST['content']),
			'`display`'      => '1',
			'star'        	 => ceil(($_POST['describe']+$_POST['logistics']+$_POST['goods'])/3)
			);

		if (!empty($_POST['none']) && $_POST['none']=='on') $addData['is_none'] = '1';

		if (!empty($return)) $addData['images'] = implode('|', $return);

		if (!empty($addData)) $addOvData = $model->addOvData($addData);

		if (!empty($addOvData)) $updataOvStatus = $model->updataOvStatus($_POST['goods_id']);
		// echo $updataOvStatus;exit;
		if (!empty($updataOvStatus)) echo "<SCRIPT type='text/javascript'>alert('恭喜您评价成功！');history.go(-2);</SCRIPT>";
		else  echo "<SCRIPT type='text/javascript'>alert('评价失败！');history.go(-2);</SCRIPT>";
	}


	public function doOvEvaluate(){
		user_check_logon();
		$tpl = get_smarty();
		$model = new userModel();

		if(empty($_GET['id']) || !preg_match("/^[0-9]+$/", $_GET['id'])){
			echo "<SCRIPT type='text/javascript'>history.back();</SCRIPT>";exit;
		}
		$id = $_GET['id'];

		$commentRow = $model->getOvGoodsComment($id);
		$ovEvaluate = $model->ovEvaluate($id);

		if ($commentRow['comment']==false) {
			echo "<SCRIPT type='text/javascript'>alert('此评论已被管理员删除！');history.go(-1);</SCRIPT>";exit;
		}
		// echo '<pre>';
		// var_dump($commentRow);exit;
		$tpl->assign("ovEvaluate", $ovEvaluate);
		$tpl->assign("commentRow", $commentRow);
		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
		$tpl->display('user_ovEvaluation.tpl.html');
	}

//建材评价提交页面
function   euEvaluate(){
	user_check_logon();
	$tpl = get_smarty();

	if(empty($_GET['id']) || !preg_match("/^[0-9]+$/", $_GET['id'])){
		echo "<SCRIPT type='text/javascript'>history.back();</SCRIPT>";exit;
	}

	$id = $_GET['id'];

	$model = new userModel();

	$getEuGoodsId = $model->getEuGoodsId($id);

	if(empty($getEuGoodsId)){
		echo "<SCRIPT type='text/javascript'>alert('该订单不存在');history.back();</SCRIPT>";exit;
	}

	$euComment = $model->euComment($id);

	if(!empty($euComment)){
		echo "<SCRIPT type='text/javascript'>alert('该商品已评价');history.back();</SCRIPT>";exit;
	}

	$euGoodsRow = $model->euGoodsRow($id);

	if (empty($euGoodsRow)) {
		echo "<SCRIPT type='text/javascript'>history.back();</SCRIPT>";exit;
	}

	$tpl->assign("euGoodsRow", $euGoodsRow);
	$tpl->assign("euGoodsId", $euGoodsRow['id']);
	$tpl->assign("euOrderId", $euGoodsRow['order_id']);

	$pn= new home();
	$data=$pn->category(1);
	$tpl->assign("eu_tree", $data);
	$cate_list = $pn->Ov_Category();
	$tpl->assign("ov_tree",$cate_list);
	
	$tpl->display('user_euEvaluate.tpl.html');
}


function addEuEvaluate(){
	user_check_logon();
	$model = new userModel();

	if(empty($_POST['goods_id']) || empty($_POST['order_id']) || !preg_match("/^[1-5]$/", $_POST['describe']) || !preg_match("/^[1-5]$/", $_POST['logistics']) || !preg_match("/^[1-5]$/", $_POST['goods'])){
	   	echo "<SCRIPT type='text/javascript'>history.back();</SCRIPT>";exit;
	}
	$ogid = $_POST['goods_id'];

	$goodsId = $model->getEuGoodsId($ogid);
	
	if (!empty($_FILES['images']['name'])) {
		for ($i=0; $i < 5; $i++) { 
			if (!empty($_FILES['images']['type'][$i])) {
				$type = explode('/', $_FILES['images']['type'][$i]);
				if ($type['0'] != 'image'){
					echo "<SCRIPT type='text/javascript'>alert('只能上传图片！');history.go(-1);</SCRIPT>";exit;
				}
			}
			$images[$i]['name']     = $_FILES['images']['name'][$i];
			$images[$i]['type']     = $_FILES['images']['type'][$i];
			$images[$i]['tmp_name'] = $_FILES['images']['tmp_name'][$i];
			$images[$i]['error']    = $_FILES['images']['error'][$i];
			$images[$i]['size']     = $_FILES['images']['size'][$i];
		}

		foreach ($images as $key => $value) {
			if (!empty($value['name'])){
				unset($_FILES);
				$_FILES[$key] = $value;
				$res[$key]=upload($key,'EuEvaluate');
				if ($res) $return[$key] = $res[$key]['images'];
			}
		}
	}

	if (trim($_POST['content'])=='请在此处写下您的宝贵意见及建议') $_POST['content'] = '';

	$addData = array(
		'goods_id'       => $goodsId,
		'order_goods_id' => $ogid,
		'user_name'      => $_SESSION['userName'],
		'add_time'       => time(),
		'user_id'        => $_SESSION['userId'],
		'`status`'       => 1,
		'`describe`'     => $_POST['describe'],
		'logistics'      => $_POST['logistics'],
		'goods'          => $_POST['goods'],
		'content'        => trim($_POST['content']),
		'display'        => '1',
		'star'        	 => ceil(($_POST['describe']+$_POST['logistics']+$_POST['goods'])/3)
		);

	if (!empty($_POST['none']) && $_POST['none']=='on') $addData['is_none'] = '1';

	if (!empty($return)) $addData['images'] = implode('|', $return);

	

	if (!empty($addData)) $addEuData = $model->addEuData($addData);

	if (!empty($addEuData)) $updataEuStatus = $model->updataEuStatus($_POST['goods_id']);
	
	if (!empty($updataEuStatus)) echo "<SCRIPT type='text/javascript'>alert('恭喜您评价成功！');history.go(-1);</SCRIPT>";
}

//建材评价详情页面
function   euEvaluation(){
	user_check_logon();
	$tpl = get_smarty();
	$model = new userModel();
	if(empty($_GET['id']) || !preg_match("/^[0-9]+$/", $_GET['id'])){
	   	echo "<SCRIPT type='text/javascript'>history.back();</SCRIPT>";exit;
	}

	$id = $_GET['id'];

	$fuCommentRow = $model->fuCommentRow($id);
	if (empty($fuCommentRow['comment'])) {
		echo "<SCRIPT type='text/javascript'>alert('此评论已被管理员删除！');history.back();</SCRIPT>";exit;
	}
	// echo $id;exit;
	$euGoodsRow = $model->euGoodsRow($id);

	// if (!empty($fuCommentRow['comment']['images'])) $fuCommentRow['images'] = explode('|', $fuCommentRow['comment']['images']);

	$tpl->assign("commentRow",$fuCommentRow);	
	$tpl->assign("euGoodsRow",$euGoodsRow);	

	$tpl->display('user_euEvaluation.tpl.html');
}
	/**
	 * 家居评价页面数据显示
	 */
	function evaluateShowAll(){
		user_check_logon();
		$tpl = get_smarty();
		$model = new userModel();
		$data2 = $model->select_evaluateShow2(); //家居3月内待评价
		$data3 = $model->select_evaluateShow3(); //家居3月内已评价

		$num=8;

		if(empty($_GET['p']) || $_GET['p']<=1) $page = 1;
		else $page = $_GET['p'];

		/************待评价分页*************/
		//全部订单
		$data2_page=$model->evaluateShowAll($page,$num);

		//分页的总条数
		$orderAll = count($data2)+count($data3);
		//实例化分页类
		$t = new Page($num, $orderAll, $page, 5, "user.php?evaluateShowAll&p=");
		//分页样式
		$page=$t->subPageCss2();//分页样式

		/*********************************/

		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);

		$tpl->assign("page",$page);
		$tpl->assign("data2",$data2_page);	
		$tpl->assign("cdata",count($data2));	
		$tpl->assign("cdata1",count($data3));	
		$tpl->assign("os",3);	
		$tpl->display('user_evaluate_none.tpl.html');
	}


	function evaluateShowNone(){
		user_check_logon();
		$tpl = get_smarty();
		$model = new userModel();
		$data2 = $model->select_evaluateShow2(); //家居3月内待评价
		$data3 = $model->select_evaluateShow3(); //家居3月内已评价

		$num=8;

		if(empty($_GET['p']) || $_GET['p']<=1) $page = 1;
		else $page = $_GET['p'];

		/************待评价分页*************/
		//全部订单
		$data2_page=$model->select_evaluateShow2($page,$num);

		//分页的总条数
		$orderAll = count($data2);
		//实例化分页类
		$t = new Page($num, $orderAll, $page, 5, "user.php?evaluateShowNone&p=");
		//分页样式
		$page=$t->subPageCss2();//分页样式

		/*********************************/

		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);

		$tpl->assign("page",$page);
		$tpl->assign("data2",$data2_page);	
		$tpl->assign("cdata",count($data2));	
		$tpl->assign("cdata1",count($data3));	
		$tpl->assign("os",1);	
		$tpl->display('user_evaluate_none.tpl.html');
	}

	function evaluateShowAlready(){
		user_check_logon();
		$tpl = get_smarty();
		$model = new userModel();
		$data2 = $model->select_evaluateShow2(); //家居3月内待评价
		$data3 = $model->select_evaluateShow3(); //家居3月内已评价

		$num=8;

		if(empty($_GET['p']) || $_GET['p']<=1) $pages = 1;
		else $pages = $_GET['p'];

		/************已评价分页*************/
		//全部订单
		$data3_page=$model->select_evaluateShow3($pages,$num);

		//分页的总条数
		$orderAlls = count($data3);
		//实例化分页类
		$ts = new Page($num, $orderAlls, $pages, 5, 'user.php?evaluateShowAlready&p=');
		//分页样式
		$pages=$ts->subPageCss2();//分页样式
		/*********************************/

		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);

		$tpl->assign("page",$pages);
		$tpl->assign("data2",$data3_page);	
		$tpl->assign("cdata",count($data2));	
		$tpl->assign("cdata1",count($data3));	
		$tpl->assign("os",2);	
		$tpl->display('user_evaluate_none.tpl.html');
	}


//家居评价提交页面
function   fuEvaluate(){
	user_check_logon();
	$tpl = get_smarty();
	if(empty($_GET['id']) || !preg_match("/^[0-9]+$/", $_GET['id'])){
	   	echo "<SCRIPT type='text/javascript'>history.back();</SCRIPT>";exit;
	}

	$id = $_GET['id'];

	$model = new userModel();
	$FuRow = $model->FuRow($id);

	if (empty($FuRow)) {
		echo "<SCRIPT type='text/javascript'>history.back();</SCRIPT>";exit;
	}
	
	$tpl->assign("list", $FuRow);

	$pn= new home();
	$data=$pn->category(1);
	$tpl->assign("eu_tree", $data);
	$cate_list = $pn->Ov_Category();
	$tpl->assign("ov_tree",$cate_list);

	$tpl->display('user_fuEvaluate.tpl.html');
}


function doaddfuEvaluation(){
	user_check_logon();
	$model = new userModel();

	if(empty($_POST['is_time']) && @$_POST['is_time']!='0'){
	   	echo "<SCRIPT type='text/javascript'>alert('您好，请选择是否按时完工！');history.back();</SCRIPT>";exit;
	}

	if(empty($_POST['is_clean']) && @$_POST['is_clean']!='0'){
	   	echo "<SCRIPT type='text/javascript'>alert('您好，请选择工地现场是否整洁！');history.back();</SCRIPT>";exit;
	}

	if(empty($_POST['detail_id']) || !preg_match("/^[0-9]+$/", $_POST['detail_id'])){
	   	echo "<SCRIPT type='text/javascript'>history.back();</SCRIPT>";exit;
	}

	/************处理上传图片************/
	if (!empty($_FILES['images']['name'])) {
		for ($i=0; $i < 5; $i++) { 
			if (!empty($_FILES['images']['type'][$i])) {
				$type = explode('/', $_FILES['images']['type'][$i]);
				if ($type['0'] != 'image'){
					echo "<SCRIPT type='text/javascript'>alert('只能上传图片！');history.go(-1);</SCRIPT>";exit;
				}
			}
			$images[$i]['name']     = $_FILES['images']['name'][$i];
			$images[$i]['type']     = $_FILES['images']['type'][$i];
			$images[$i]['tmp_name'] = $_FILES['images']['tmp_name'][$i];
			$images[$i]['error']    = $_FILES['images']['error'][$i];
			$images[$i]['size']     = $_FILES['images']['size'][$i];
		}

		foreach ($images as $key => $value) {
			if (!empty($value['name'])){
				unset($_FILES);
				$_FILES[$key] = $value;
				$res[$key]=upload($key,'FuEvaluate');
				if ($res) $return[$key] = $res[$key]['images'];
			}
		}
	}
	/**********************************/

	$list = $model->detailList($_POST['detail_id']);

	if(empty($list)){
	   	echo "<SCRIPT type='text/javascript'>history.back();</SCRIPT>";exit;
	}
	if (trim($_POST['content'])=='请在此处写下您的宝贵意见及建议') $_POST['content'] = '';

	$addData = array(
		'class_id'     => '1',
		'goods_id'     => $_POST['detail_id'],
		'quote_id'     => $list['quote_id'],
		'user_name'    => $_SESSION['userName'],
		'add_time'     => time(),
		'user_id'      => $_SESSION['userId'],
		'`status`'     => '1',
		'`order_id`'   => $list['order_id'],
		'goods'        => $_POST['goods'],
		'distributor'  => $_POST['distributor'],
		'personnel'    => $_POST['personnel'],
		'construction' => $_POST['construction'],
		'content'      => trim($_POST['content']),
		'is_time'      => $_POST['is_time'],
		'is_clean'     => $_POST['is_clean'],
		'display'      => '1',
		'star'    	   => ceil(($_POST['goods']+$_POST['distributor']+$_POST['personnel']+$_POST['construction'])/4)

		);

	if (!empty($_POST['none']) && $_POST['none']=='on') $addData['is_none'] = '1';

	if (!empty($return)) $addData['images'] = implode('|', $return);

	if (!empty($addData)) $addComment = $model->addComment('xgj_furnish_comment',$addData);

	if (!empty($addComment)) $updataFuStatus = $model->updataFuStatus($_POST['detail_id']);

	if (!empty($updataFuStatus)) echo "<SCRIPT type='text/javascript'>alert('恭喜您评价成功！');history.go(-2);</SCRIPT>";
}

//家居评价详情页面
function   fuEvaluation(){
	user_check_logon();
	$tpl = get_smarty();
	$model = new userModel();

	if(empty($_GET['id']) || !preg_match("/^[0-9]+$/", $_GET['id'])){
	   	echo "<SCRIPT type='text/javascript'>history.back();</SCRIPT>";exit;
	}
	$id = $_GET['id'];

	$commentRow = $model->commentRow($id);
	if (empty($commentRow['comment'])) {
		echo "<SCRIPT type='text/javascript'>alert('此评论已被管理员删除！');history.back();</SCRIPT>";exit;
	}
	if (!empty($commentRow['comment']['images'])) $commentRow['images'] = explode('|', $commentRow['comment']['images']);

	$tpl->assign("commentRow",$commentRow);	
	$tpl->display('user_fuEvaluation.tpl.html');
}
	/**
	 * doEvaluate	执行评论(修改)操作
	 */
	function doEvaluate(){

		//调用判断是否登录的方法
		user_check_logon();
		//如果有POST方式数据提交
		if(!empty($_POST)){
	         $doEvaluate = new userModel();
			//健康舒适家评论
            if(isset($_GET["fu_id"]))   {
             	
            
			 $doEvaluateResult = $doEvaluate->doEvaluate($_GET["fu_id"], $_POST["content"], $_POST["detail_id"], $_POST["order_id"]);

			if($doEvaluateResult > 0){
				echo "<SCRIPT type='text/javascript'>alert('评论成功!');window.location.href='user.php?evaluateShow1';</SCRIPT>";
				exit();
			}else{
				echo "<SCRIPT type='text/javascript'>alert('评论失败,请重试!');history.back();</SCRIPT>";
				exit();
			 }
		
             }

            //欧洲团代购 ，德国母婴 评论
             if(isset($_GET["eu_id"]))   {
             	
             	$EvaluateResult = $doEvaluate->europe($_GET["eu_id"], $_POST["content"], $_POST["detail_id"], $_POST["order_id"]);

             	if($EvaluateResult > 0){
             		echo "<SCRIPT type='text/javascript'>alert('评论成功!');window.location.href='user.php?evaluateShow';</SCRIPT>";
             		exit();
             	}else{
             		echo "<SCRIPT type='text/javascript'>alert('评论失败,请重试!');history.back();</SCRIPT>";
             		exit();
             	}
             
             }
             
           
           }
		
	}


	/**
	 * 显示取消订单界面
	 * 
	 */
	function cancelOrderShow(){

		//调用判断是否登录的方法
		user_check_logon();

		$cancel = new userModel();
		

		//调用model层里面查询取消订单详情的列表
		$cancelOrderInfo = $cancel->cancelOrderShow($_SESSION["userId"]);

		$tpl = get_smarty();

		$tpl->assign("cancelOrderInfo",$cancelOrderInfo);	//取消订单详情
		$tpl->display('user_cancelOrder.tpl.html');
         
	}


	/**
	 * returnShow	显示返修退换货列表页界面
	 */
	
	function returnShow(){
		//调用判断是否登录的方法
		user_check_logon();
	
		$returnGoods = new userModel();
	
		//可退货(已付款)的订单
		//分页每页的条数
		$num=5;

		//全部订单
		if(empty($_GET['p']) || $_GET['p']<=1){
			$page = 1;
			$p1 = 1;
		}else{
			$page = $_GET['p'];
			$p1 = 0;
		}
		

		//显示列表内容
		$orderInfoAllList=$returnGoods->returnShow('count');

		$orderInfoAllList1=$returnGoods->returnShow($page,$num);

		// echo '<pre>';
		// var_dump($orderInfoAllList1);exit;
		//分页的总条数
		$orderAll = count($orderInfoAllList);
		//实例化分页类
		$t = new Page($num, $orderAll, $page, 5, "user.php?returnShow&p=");
		//分页样式
		$page=$t->subPageCss2();//分页样式

		$returnedList = $returnGoods->returnedList($_SESSION["userId"]);

		//模板传值
		$tpl = get_smarty();
		if (empty($orderInfoAllList1) && $p1 == 1) {
			$tpl->assign("page",'');
		}else{
			$tpl->assign("page",$page);
		}
		$tpl->assign('returnGoodsList',$orderInfoAllList1); //全部订单的数据列表
	    $tpl->assign("pageCount", count($orderInfoAllList));  //订单总数量
		$tpl->assign("returnedList", $returnedList);	//查询到的退货的信息数据列表
		$tpl->display('user_return.tpl.html');
		
		// $returnGoodsList = $returnGoods->returnShow($_SESSION["userId"], 7, 6);
		// //退货的订单记录
		// $returnedList = $returnGoods->returnedList($_SESSION["userId"]);
		// $tpl = get_smarty();
	  
		// $tpl->assign("returnGoodsList", $returnGoodsList["dataSelResult"]);	//查询到的可退货信息数据列表
		// $tpl->assign("page", $returnGoodsList["pageInfo"]["page"]);	//当前页
		// $tpl->assign("pageCount", $returnGoodsList["pageInfo"]["pageCount"]);	//总页数
	
		// $tpl->assign("returnedList", $returnedList);	//查询到的退货的信息数据列表
		
		
	
		// $tpl->display('user_return.tpl.html');
	}
	

	/**
	 * returnApplicationShow	显示返修退换货操作(详情页)界面
	 */
	function returnApplicationShow(){
		//调用判断是否登录的方法
		user_check_logon();

		$tpl = get_smarty();

		$apply = new userModel();

		if (is_numeric($_GET["gid"]) && is_numeric($_GET["id"])) {
			$gid = trim($_GET["gid"],'-');
			$order_id = trim($_GET["id"],'-');
		}else{
			echo "<script>window.location.href='index.php?error';</script>";exit;
		}

		$applyInfo = $apply->applyInfo($gid,$order_id);

		if (empty($applyInfo)) {
			echo "<SCRIPT type='text/javascript'>alert('没有找到商品,请重试!');window.location.href='user.php?returnShow';</SCRIPT>";exit;
		}

		$tpl->assign("applyInfo", $applyInfo);	//查询到的申请返修退换货的数据信息

		$tpl->display('user_returnApplication.tpl.html');
	}


	/**
	 * 执行返修退换货操作
	 */
	function doApply(){
		

		//调用判断是否登录的方法
		user_check_logon();
		$apply = new userModel();
		
		foreach ($_POST as $key => $value) {
			if ($value == '') {
				echo "<SCRIPT type='text/javascript'>alert('请填写完整再提交，谢谢！');history.back();</SCRIPT>";
			}
		}

        //商品订单编号
		$order_id= $_POST["order_id"];
		if (is_numeric($order_id)) {
			$order_id = trim($order_id,'-');
		}else{
			echo "<script>window.location.href='index.php?error';</script>";exit;
		}

		//商品编号
		$goods_id= $_POST["gid"];
		if (is_numeric($goods_id)) {
			$goods_id = trim($goods_id,'-');
		}else{
			echo "<script>window.location.href='index.php?error';</script>";exit;
		}

		// 服务类型 
		$returnType = $_POST["returnType"];
		if ($returnType<1 || $returnType>3) {
			echo "<SCRIPT type='text/javascript'>alert('请正确填写服务类型');history.back();</SCRIPT>";exit;
		}
		//提交数量
		$num = $_POST["num"];
		$applyInfo = $apply->applyInfo($_POST['gid'],$order_id);
		if ($num<1 || $num>$applyInfo['goods_num']) {
			echo "<SCRIPT type='text/javascript'>alert('请正确填写数量');history.back();</SCRIPT>";exit;
		}
		//问题描述
		$content = $_POST["content"];	
		//发票
		if (!empty($_POST["invoice"])) {
			$invoice = '1';
		}else{
			$invoice = '0';
		}
		//检测报告
		if (!empty($_POST["report"])) {
			$report = '1';
		}else{
			$report = '0';
		}
		//返货方式
		$express = $_POST["express"];
		// if ($num!=1) {
		// 	echo "<SCRIPT type='text/javascript'>alert('请正确填写返货方式');history.back();</SCRIPT>";exit;
		// }
		//收货地址
		$address = $_POST["address"];
		//客户姓名
		$name = $_POST["name"];
		//手机
		$tel = $_POST["tel"];
		if(!preg_match("/^1[34578]\d{9}$/", $tel)){
		    echo "<SCRIPT type='text/javascript'>alert('请正确填写手机号码！');history.back();</SCRIPT>";exit;
		}

		//检测上传图片是否存在
        if (!empty($_FILES)){
            $file = $_FILES['img'];
            //上传文件
            $res=@upload('img','Img');
            if ($res['code']==1){
	        	$img = $res['images'];
	        }else{
	        	echo "<SCRIPT type='text/javascript'>alert('图片上传失败，请重试！');history.back();</SCRIPT>";exit;
	        }
        }else{
        	echo "<SCRIPT type='text/javascript'>alert('请上传图片！');history.back();</SCRIPT>";exit;
        }

		$data = array(
			'order_id'=>$order_id,
			'goods_id'=>$goods_id,
			'user_id'=>$_SESSION['userId'],
			'type'=>$returnType,
			'num'=>$num,
			'content'=>$content,
			'invoice'=>$invoice,
			'report'=>$report,
			'express'=>$express,
			'address'=>$address,
			'name'=>$name,
			'tel'=>$tel,
			'img'=>$img
			);
		
		$applyResult = $apply->doApply($data);
		
		if($applyResult > 0){
			$where = "id=$goods_id and order_id=$order_id and user_id=".$_SESSION['userId'];
			$updata = $apply->doApplyupdata($where);
			if (!empty($updata)) {
				echo "<SCRIPT type='text/javascript'>alert('操作成功,请等待审核!');window.location.href='user.php?returnShow';</SCRIPT>";
				exit();
			}else{
				echo "<SCRIPT type='text/javascript'>alert('操作失败,请重试!');history.back();</SCRIPT>";
				exit();
			}			
		}else{
			echo "<SCRIPT type='text/javascript'>alert('操作失败,请重试!');history.back();</SCRIPT>";
			exit();
		}
	}

	/**
	 * 显示个人资产(余额)页面
	 */
	function assetShow(){
		//调用判断是否登录的方法
		user_check_logon();

		$tpl = get_smarty();

		$concern = new userModel();

		//分页每页的条数
		$num=8;

		//全部订单
		if(empty($_GET['p']) || $_GET['p']<=1){
			$page = 1;
			$p1 = 1;
		}else{
			$page = $_GET['p'];
			$p1 = 0;
		}
		

		//显示列表内容
		$orderInfoAllList=$concern->assetinformation('count');

		$orderInfoAllList1=$concern->assetinformation($page,$num);
		//分页的总条数
		$orderAll = count($orderInfoAllList);
		// echo $orderAll;exit;
		//实例化分页类
		$t = new Page($num, $orderAll, $page, 5, "user.php?assetShow&p=");
		//分页样式
		$page=$t->subPageCss2();//分页样式
		//模板传值
		if (empty($orderInfoAllList1) && $p1 == 1) {
			$tpl->assign("page",'');
		}else{
			$tpl->assign("page",$page);
		}

		//查询优惠券总金额 

		$tpl->assign('moneyList',$orderInfoAllList1); //全部订单的数据列表
	    $tpl->assign("pageCount", count($orderInfoAllList));  //订单总数量
	    $pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
		$tpl->display('user_asset.tpl.html');
		
	}


	/**
	 * couponShow	显示优惠券页面
	 */
	function couponShow(){

		//调用判断是否登录的方法
		user_check_logon();
        
		$tpl = get_smarty();

		$model = new userModel();

		//查询可用优惠券金额
		$couponSum = $model->couponStatusCount("WHERE `user_id` = '{$_SESSION["userId"]}'");

		//查询兑换优惠券金额
		$countCouponMoney = $model->countCouponMoney();

		//查询已使用优惠券余额
		$use_money = $model->couponInfo("WHERE `user_id` = '{$_SESSION["userId"]}'");
		$couponAvailable = $couponSum["coupon"];

		//查询优惠券信息
		$couponList = $model->couponList();

		//分页每页的条数
		$num=8;

		//全部订单
		if(empty($_GET['p']) || $_GET['p']<=1){
			$page = 1;
			$p1 = 1;
		}else{
			$page = $_GET['p'];
			$p1 = 0;
		}

		
		//显示列表内容
		$data = $model->couponList($page,$num);

		//分页的总条数
		$orderAll = count($couponList);
		//实例化分页类
		$t = new Page($num, $orderAll, $page, 5, "user.php?couponShow&p=");
		//分页样式
		$page=$t->subPageCss2();//分页样式
		//模板传值
		if (empty($data) && $p1 == 1) {
			$tpl->assign("page",'');
		}else{
			$tpl->assign("page",$page);
		}

		$tpl->assign("countCouponMoney",$countCouponMoney);			//兑换优惠券金额
		$tpl->assign("couponList",$data);							//优惠券兑换信息
		$tpl->assign("useMoney",$use_money);						//所以已使用优惠券总金额
		$tpl->assign("couponAvailable",$couponAvailable);			//所以未使用(可使用)的优惠券总金额

		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
		$tpl->display('user_coupon.tpl.html');
	}

	/**
	 * 激活优惠券
	 */
   	function activateCoupon(){
   		if(!empty($_POST["coupon"]) && !empty($_POST["cpassword"])){
            $info=$db->getRow("select discount_amount from xgj_coupon where coupon_number='".$_POST["coupon"]."' and coupon_password='".$_POST["cpassword"]."'");
            $res=$db->update('xgj_users',array('coupon'=>$info['discount_amount']),"user_id={$_SESSION['userId']}");
            $db->update('xgj_coupon',array('status'=>1),"coupon_number={$_POST['coupon']}");
            if($res){
				echo jump(1,'优惠券激活成功',"user.php?couponShow");
				header("refresh:2;url='user.php?couponShow'" );exit;
            }else{
            	echo jump(2,'优惠券激活失败',"user.php?couponShow");
				header("refresh:2;url='user.php?couponShow'" );exit;
            }
        }else{
        	echo jump(2,'优惠券账号或密码不正确',"user.php?couponShow");
			header("refresh:2;url='user.php?couponShow'" );exit;
        }
   	}
	

   	function actCoulist(){

   		//调用判断是否登录的方法
		user_check_logon();
        
		$tpl = get_smarty();

		$couponList = new userModel();

		//查询优惠券总金额 
		$couponSum = $couponList->couponStatusCount("WHERE `user_id` = '{$_SESSION["userId"]}'");

		//查询可用优惠券总金额
		$use_money = $couponList->couponInfo("WHERE `user_id` = '{$_SESSION["userId"]}'");
		$couponAvailable = $couponSum["coupon"];

		//查询优惠券信息
		$couponInfo = $couponList->couponInfoList();

		//分页每页的条数
		$num=8;

		//全部订单
		if(empty($_GET['p']) || $_GET['p']<=1){
			$page = 1;
			$p1 = 1;
		}else{
			$page = $_GET['p'];
			$p1 = 0;
		}
		
		//显示列表内容
		$data = $couponList->couponInfoList($page,$num);
		//分页的总条数
		$orderAll = count($couponInfo);
		//实例化分页类
		$t = new Page($num, $orderAll, $page, 5, "user.php?actCoulist&p=");
		//分页样式
		$page=$t->subPageCss2();//分页样式
		//模板传值
		if (empty($data) && $p1 == 1) {
			$tpl->assign("page",'');
		}else{
			$tpl->assign("page",$page);
		}

		$tpl->assign("couponInfo",$data);					//所以优惠券详细使用信息
		$tpl->assign("useMoney",$use_money);				//所以已使用优惠券总金额
		$tpl->assign("couponAvailable",$couponAvailable);	//所以未使用(可使用)的优惠券总金额

		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);

	 	$tpl->display('user_actCoulist.tpl.html');
   	}

    /**
	 * 激活优惠券
	 */
   	public function activationCoupon(){
		$uid  = $_SESSION['userId'];
		$code = $_GET['code'];
		$pass = $_GET['pass'];
   		if (empty($uid)) {
   			echo '1';exit;
   		}
		$model = new userModel();
		$return = $model->activationCoupon($code,$pass,$uid);
		echo $return;
   	}

	/**
	 * 显示家居订单页面
	 */
	function homeOrderShow(){
		//调用判断是否登录的方法
		header("Content-type:text/html;charset=utf-8");
		user_check_logon();
		$tpl = get_smarty();
		$db=new db();
		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
		$order=new userModel();
        //全部订单
        $contentdata = $order->goodsdata($_SESSION["userId"]);
        if(!empty($contentdata)){
        	$order_id=$contentdata["order_id"];
        	$quote_info=$db->getAll("select * from xgj_furnish_order_detail d join xgj_furnish_quote q on d.quote_id=q.quote_id where d.order_id=$order_id");
	        $ecprice='';
	        $trprice='';
	        foreach ($quote_info as $k=>$v){
	            $ecprice += (($v['quote_price']-$v['cost'])*0.9+$v['cost'])*$v['ecprice']/100;
	            $trprice += (($v['quote_price']-$v['cost'])*0.9+$v['cost'])*(100-$v['ecprice'])/100;
	        }
	        $contentdata['ecpri']=ceil(($ecprice+$contentdata['cj_money']));
	        $contentdata['trpri']=ceil($trprice-500);
	        //调整订单信息
        	$adjustData=$order->getAdjustData($order_id);
        }
        
        if(empty($adjustData)){
        	$adjustData='';
        }
        //施工系统
        $countPlan =$order->countPlan($_SESSION["userId"]);
        //施工计划
        if ($countPlan){
	        foreach ($countPlan  as $k=>$v){
	         	$Plan[]=$dataPlan = $order->constructPlan($_SESSION["userId"],$v["quote_id"]);
	        }
	        //质量审核
	        foreach ($countPlan as $k=>$v){
	          	$Mgcheck[]=$checkData = $order->zhilPlan($_SESSION["userId"],$v["quote_id"]);
	        }
	        //文件区域
	        $dealerOrderFile = $order->file($_SESSION["userId"]);
	        //产品手册
	        foreach ($countPlan as $k=>$v){
	          	$Product[]= $Producttxt =$order->Productfile($order_id,$v["quote_id"]);
		    }
		    //施工计划
		    $tpl->assign('Plan',$Plan);
		    //质量审核
		    $tpl->assign('Mgcheck',$Mgcheck);
		    //文件区域
		    $tpl->assign('dealerOrderFile',$dealerOrderFile);
		    //产品手册
		    $tpl->assign('Producttxt',$Product);
        }
        //订单信息
        $tpl->assign("orderInfo",$contentdata );
        //调整订单信息
        $tpl->assign("adjustData",$adjustData );
        //系统名称
        $tpl->assign('countPlan',$countPlan);
		$tpl->display('user_homeorder.tpl.html');
	}
	  
	
	/**
	 *  downLoad  文件下载
	 */
    
	function downLoad(){
		 
		header("Content-type:text/html;charset=utf-8");
		$file_name = $_GET['file_name'];
	
		//用以解决中文不能显示出来的问题
		$file_name=iconv("utf-8","gb2312",$file_name);
	
		$file_sub_path=$_SERVER['DOCUMENT_ROOT']."/xgj/source/xgjweb/pictures/file/upload/";
		$file_path=$file_sub_path."$file_name";
	
		//首先要判断给定的文件存在与否
		if(!file_exists($file_path)){
			echo "<script>alert('没有该文件');</script>";
			echo "<script>window.location.href='user.php?homeOrderShow';</script>";
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
		while(!feof($fp) && $file_count<$file_size){
			$file_con=fread($fp,$buffer);
			$file_count+=$buffer;
			ob_flush();
			echo $file_con;
		}
		fclose($fp);
	
	}
	
	/**
	 * 显示房屋例表页面
	 */
	function user_house(){
		
		//调用判断是否登录的方法
        user_check_logon();

        $user_house = new userModel();
		$house_info=$user_house->show_house_info($_SESSION['userId']);
		
		//print_r($house_info);

		if($house_info == null){
			header("Location:users.php/house_add");
		}else{

           $tpl = get_smarty();
		   $tpl->assign('cateid',$_GET['cateid']);
		   $tpl->assign('house_info',$house_info);
		   $tpl->display('user_house.tpl.html');
		}

	}
	
	 /**
	  * 显示房屋修改信息页面
	  * 
	  */
/*	  function  houseinformation(){
	  	
	  	user_check_logon();
	  	
	  	$tpl = get_smarty();
	  	
	  	$coupondata=new userModel();
	  	
	  	$data=$coupondata->docoupondata($_SESSION["userId"]);
	  	
	  	$jifen=$data['coupon']/2;
	  
	  	$tpl->assign('coupon',$data['coupon']);
	  	$tpl->assign('jifen',$jifen);
	  	$tpl->display("houseinformation.tpl.html");
	  	
	  	
	  }*/
    
	function house_add(){
		

        user_check_logon();
        
        if(isset($_POST) && $_POST!=null)
		{   
			$house_info=array();
			$house_info['house_name']=trim($_POST['house_name']);
			$house_info['province']=$_POST['provinces'];
			$house_info['city']=$_POST['citys'];
			$house_info['district']=$_POST['town'];
			$house_info['user_id']=$_SESSION['userId'];
			$house_info['total_area']=$_POST['total_area'];
		    $user_house = new userModel();
		    $result=$user_house->add_house_info($house_info);
			header("Location:user.php?house");
			
		}
	 	$str=file_get_contents('js/area_data.js');
		$place_data=json_decode($str);
        
        $tpl = get_smarty();
		$tpl->assign('place_data',$place_data);
		$tpl->display('user_house_add.tpl.html');
	}

	//获取短信验证码
	function message(){
		$tel=$_POST['tel'];
		$rest=getMessage($tel);
		if($rest['success']===true){
			echo 1;
			exit;
		}else{
			echo -1;
			exit;
		}
	}


	function afterService(){
		$tpl = get_smarty();
		$userOb = new userModel();
		
		//分页每页的条数
		$num=$this->num;

		//全部订单
		$p = 1;
		$data=$userOb->getafterService($_SESSION['userId'],$p,$num);
		$count=$userOb->getafterService($_SESSION['userId']);
		//分页的总条数
		$orderAll = count($count);
		//总页数
		$pcount = ceil($orderAll/$num);


		$data1=$userOb->getafterService($_SESSION['userId'],$p,$num,'维修');
		$count_1=$userOb->getafterService($_SESSION['userId'],'','','维修');
		//分页的总条数
		$count1 = count($count_1);
		//总页数
		$pcount1 = ceil($count1/$num);


		$data2=$userOb->getafterService($_SESSION['userId'],$p,$num,'保养');
		$count_2=$userOb->getafterService($_SESSION['userId'],'','','保养');
		//分页的总条数
		$count2 = count($count_2);
		//总页数
		$pcount2 = ceil($count2/$num);


		$data3=$userOb->getafterService($_SESSION['userId'],$p,$num,'耗材');
		$count_3=$userOb->getafterService($_SESSION['userId'],'','','耗材');
		//分页的总条数
		$count3 = count($count_3);
		//总页数
		$pcount3 = ceil($count3/$num);
		//var_dump($data1,$data2,$data3);exit;
		$tpl->assign('pcount',$pcount);
		$tpl->assign('pcount1',$pcount1);
		$tpl->assign('pcount2',$pcount2);
		$tpl->assign('pcount3',$pcount3);
		$tpl->assign('data',$data);
		$tpl->assign('data1',$data1);
		$tpl->assign('data2',$data2);
		$tpl->assign('data3',$data3);
		$tpl->assign("p",$p);
		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign("eu_tree", $data);
		$cate_list = $pn->Ov_Category();
		$tpl->assign("ov_tree",$cate_list);
		$tpl->display('user_afterService.tpl.html');
	}

	//欧团我的订单分页
	function afterTab(){
		$page = $_GET['page'];
		$tab = $_GET['tab'];
		$num = $this->num;
		$userOb = new userModel();
		//全部订单
		if ($tab == 1) { 	//全部订单
			$data=$userOb->getafterService($_SESSION['userId'],$page,$num);
			$count=$userOb->getafterService($_SESSION['userId']);
		}else if($tab == 2){	//待付款
			$data=$userOb->getafterService($_SESSION['userId'],$page,$num,'维修');
			$count=$userOb->getafterService($_SESSION['userId'],'','','维修');
		}else if($tab == 3){	//待收货
			$data=$userOb->getafterService($_SESSION['userId'],$page,$num,'保养');
			$count=$userOb->getafterService($_SESSION['userId'],'','','保养');
		}else if($tab == 4){	//待评价
			$data=$userOb->getafterService($_SESSION['userId'],$page,$num,'耗材');
			$count=$userOb->getafterService($_SESSION['userId'],'','','耗材');
		}
		//分页的总条数
		$orderAll = count($count);
		//总页数
		$pcount = ceil($orderAll/$num);
		
		echo '                  	
            <div class="user_afterService-center-title">
            	<div class="user_afterService-center-title-name">
                	产品名称
                </div>
                
                <div class="user_afterService-center-title-time">
                	预约时间
                </div>
                
                <div class="user_afterService-center-title-time">
                	服务时间
                </div>
                
                <div class="user_afterService-center-title-star">
                	服务评价
                </div>
                
                <div class="user_afterService-center-title-category">
                	服务类别
                </div>
                
                <div class="user_afterService-center-title-remarks">
                	预约备注
                </div>
                
                <div class="clear"></div>
            </div>
            
            <div class="clear"></div>
            
            <div class="user_afterService-center-list">';
            if (!empty($data)){
            	foreach($data as $k => $v){
        		echo ' 
		            <div class="user_afterService-center-list-demo">
                            	<div class="user_afterService-center-list-demo-name">
                                    '.$v['quote_name'].'
                                </div>
                                
                                <div class="user_afterService-center-list-demo-time">
                                    '.$v['time'].'
                                </div>
                                
                                <div class="user_afterService-center-list-demo-time">
                                    '.$v['s_time'].'
                                </div>
                                
                                <div class="user_afterService-center-list-demo-star">
                                	<div class="user_afterService-center-list-demo-star-demo">
                                    	<img src="images/xing03.png">
                                    </div>
                                    
                                    <div class="user_afterService-center-list-demo-star-demo">
                                    	<img src="images/xing03.png">
                                    </div>
                                    
                                    <div class="user_afterService-center-list-demo-star-demo">
                                    	<img src="images/xing03.png">
                                    </div>
                                    
                                    <div class="user_afterService-center-list-demo-star-demo">
                                    	<img src="images/xing03.png">
                                    </div>
                                    
                                    <div class="user_afterService-center-list-demo-star-demo">
                                    	<img src="images/xing03.png">
                                    </div>
                                    
                                    <div class="clear"></div>
                                </div>
                                
                                <div class="user_afterService-center-list-demo-category">
                                    '.$v['type'].'
                                </div>
                                <input type="hidden" id="note'.$v['id'].'" value="'.$v['note'].'"/>
                                <div class="user_afterService-center-list-demo-remarks" onclick="a('.$v['id'].')">
                                    <a href="javascript:;">
                                    	查看
                                    </a>
                                </div>
                                
                                <div class="clear"></div>
                            </div>
                            
                            <div class="clear"></div>';
            	}
			}
        echo '                
            <!--分页开始-->
            <div class="page">';
              if (!empty($page)){
	            echo '
	              <a href="javascript:;" onclick="page('.$tab.',1)">首页</a>';  
	            if ($page==1) {
              		echo '<a href="javascript:;">[上一页]</a>';
              	}else{
              		echo '<a href="javascript:;" onclick="page('.$tab.','.($page-1).')">[上一页]</a>';
              	}
	          }
              if ($page > 2){
	            echo '
	              <a href="javascript:;" onclick="page('.$tab.','.($page-2).')">'.($page-2).'</a>';
              }
              if ($page > 1){
	            echo '
	              <a href="javascript:;" onclick="page('.$tab.','.($page-1).')">'.($page-1).'</a>';
              }
              echo '<span>'.$page.'</span>';
              if ($pcount > $page){
	            echo '
	              <a href="javascript:;" onclick="page('.$tab.','.($page+1).')">'.($page+1).'</a>';
              }
              if ($pcount > ($page+1)){
	            echo '
	              <a href="javascript:;" onclick="page('.$tab.','.($page+2).')">'.($page+2).'</a>';
              }
              if (!empty($page)){
              	if ($page<$pcount) {
              		echo '<a href="javascript:;" onclick="page('.$tab.','.($page+1).')">[下一页]</a>';
              	}else{
              		echo '<a href="javascript:;">[下一页]</a>';
              	}
				echo '
	              <a href="javascript:;" onclick="page('.$tab.','.$pcount.')">[尾页]</a>';
              }
        echo '        
            </div>
            <!--分页结束-->       
		';
	}
	/**
	 *	跳转404页面
	 **/
	public function error(){
		$tpl = get_smarty();
		$tpl->display('error.tpl.html');
	}
	

	public function delcoupon(){
		$id = $_GET['id'];
		$model = new userModel();
		$return = $model->delcoupon($id);
		echo $return;
	}


	public function delEuOrder(){
		$id = $_GET['id'];
		$userOb = new userModel();
		$re = $userOb->delEuOrder($id);
		echo $re;
	}


	function ov_order_status(){
		$option = intval($_GET['ov_order_status']);
		if (!empty($_GET['order_id'])) {
			$order_id = intval($_GET['order_id']);
		}
		if (!empty($_GET['dorder_id'])) {
			$dorder_id = intval($_GET['dorder_id']);
			$order_id = $dorder_id;
		}

		$order_sn = $_GET['sn'];
		if (empty($order_sn) || empty($order_id) || empty($option) || $option>7 || $option<1) {
			header("Location:user.php?supermarketOrder");exit;
		}
		$model = new userModel();

		$order = $model->ovorderall($order_id,$order_sn);
		if (empty($order) || $option!=$order['0']['order_status']) {
			header("Location:user.php?supermarketOrder");exit;
		}

		if ($option==2) {
			$data = array('order_status'=>4,'post_status'=>3); //确认收货  order_status-4   post_status-3
		}else if($option==4 && empty($dorder_id)){
			header("Location:user.php?supermarketOrderEvaluateNone");exit;
			// $data = array('order_status'=>5,'is_comment'=>1); //评价  order_status-5  is_comment-1
		}else if($option==7 || !empty($dorder_id) && $option==4 || !empty($dorder_id) && $option==5 || !empty($dorder_id) && $option==6 ){
			$data = array('order_status'=>7); //删除    order_status-7
		}
		$save = $model->ovordersave($data,$order_id,$order_sn);
		
		header("Location:user.php?supermarketOrder");
		
		
	}

}
?>
