<?php
/**
* @package WWW
* @see feed_center, user_cace, photo_lib, notification_center, user_application, user_relations, user_register
*/
require_once(WWW_DIR."/model/xgj_furnish.php");
// require_once(WWW_DIR . "/model/welcome_model.php");
require_once(WWW_DIR."/conf/mysql_db.php");
require_once(WWW_DIR ."/model/ShushijiaModel.class.php");
require_once(WWW_DIR."/libs/page.php");

require_once(WWW_DIR . "/model/price_model.php");
class welcome
{
    public	function  menu(){
		$pn= new home();
		//获取所有欧洲团代购分类
		$data=$pn->category(1);
		// $leg=count($data);
		// $size=ceil($leg/3);
		// $eu_tree=array_chunk($data,$size);
		//获取所有健康绿色食品的分类
		$data_=$pn->category(2);
		$cate_list=$pn->Ov_Category();
		$leg=count($data_);
		$size=ceil($leg/3);
		$food_tree=array_chunk($data_,$size);
		$result['eu_tree']=$data;
		$result['ov_tree']=$cate_list;
		$result['food_tree']=$food_tree;
		return $result;
	} 

	//机电售后服务菜单
	public function service_menu(){
		//var_dump($page);exit;
		$pn= new home();
		//日常保养
		$data['day']=$pn->article(20);
		//服务保障
		$data['service']=$pn->article(21);
		//健康舒适家产品
		$data['furGood']=$pn->getFurCate();
		return $data;
	}
	//获取产品手册详情
	public function getManualInfo(){
		$pn= new home();
		$id=intval($_GET['id']);
		//产品手册
		$manual=$pn->getManual($id);
		$html='';
		$html.="<select name='manual' id='manual1' onchange='return b()'><option>请选择产品</option>";
		foreach ($manual as $key=>$val){
                $html.="<option value={$val['manual']}>{$val['alias']}</option>";
		}
		$html.="</select>";
		echo $html;
	}

	//下载产品手册
	public function downManual(){
		if(empty($_GET['file_name'])){
			echo "<script>alert('您选的产品暂无手册');history.go(-1)</script>";exit;
		}else{
			ob_end_clean();
	        $file_name = base64_decode($_GET['file_name']);
	        $path=DOWN_IMG_URL."Public/Uploads/";
	        down($path,$file_name);
		}
	}
	
	function index(){
        $tpl = get_smarty();
		$pn= new home();
		//商品分类
		$cate=$this->menu();
		//banner
		$brandImg=$pn->getImg(4);
		//视频分类
		$pos = $pn->video_pos();

		foreach ($pos as $key => $value) {
			//视频
			$video[$key] = $pn->video($value['id']);
		}

		// echo '<pre>';
		// var_dump($video);exit;

		//健康舒适家产品
		$furGood=$pn->getFurCate();
		$tpl->assign("furGood",$furGood);
		$furAd=$pn->getFurAd();
		//var_dump($furAd);exit;
		//欧洲团代购产品
		$data=$pn->getEuFoodGood(1);
		$data1=$pn->getOvGood(2);
		//vdump($brandVideo);
		$img = $pn->image();
		$tpl->assign("img",$img);
		$tpl->assign("brandImg",$brandImg);
		$tpl->assign("video",$video);
		$tpl->assign("furAd",$furAd);
		$tpl->assign("pos",$pos);
		$tpl->assign("eu_tree",$cate['eu_tree']);
		$tpl->assign("ov_tree",$cate['ov_tree']);
		$tpl->assign("food_tree",$cate['food_tree']);
		$tpl->assign("euGood",$data);
		$tpl->assign("foodGood",$data1);
		$tpl->display('index.tpl.html');
	}


	function video_row(){
		$n=$_GET['num'];
		$id=$_GET['id'];
		$pn= new home();
		$video = $pn->video($id);
		echo '
		<div id="clickAdd_video02_Id'.$n.'" class="clickAdd_video02_Id" style="display:block;">   
	    	<div class="clickAdd_video-center-bk"> 
	            <div class="clickAdd_video-center">
	            	<div class="clickAdd_video-center-title">
	                	<div class="clickAdd_video-center-title-cha" id="video_AddOut02Id'.$n.'" onclick="close1('.$n.')">
	                        <a href="javascript:;">
	                            X
	                        </a>
	                    </div>
	                </div>
	                <div class="clickAdd_video-center-video" >
	                	<video id="example_video_1" class="video-js vjs-default-skin" controls preload="none" width="680" height="380" poster="'.getimages($video['0']['image']).'" data-setup="">
	                        <source class="video" src="'.getimages($video['0']['video']).'" type="video/mp4" />
	                        <source class="video" src="'.getimages($video['0']['video']).'" type=video/webm" />
	                        <source class="video" src="'.getimages($video['0']['video']).'" type="video/ogg" />
	                        <source class="video" src="'.getimages($video['0']['video']).'" type="video/avi" />
	                        <track kind="captions" src="demo.captions.vtt" srclang="en" label="English"></track><!-- Tracks need an ending tag thanks to IE9 -->
	                        <track kind="subtitles" src="demo.captions.vtt" srclang="en" label="English"></track><!-- Tracks need an ending tag thanks to IE9 -->
	                    </video>
	                </div>
	                
	                <div class="clickAdd_video-center-name" id="title">
	                	'.$video['0']["title"].'
	                </div>
	            </div>
	        </div>
	    </div>
	    ';
	}

	
	//机电售后服务在线保修
	/*function service(){
		$tpl = get_smarty();
		$homeOb=new home();
				//欧洲团代购产品
		
		$data=$this->service_menu();
		$article_id=43;
		$info=$homeOb->articleInfo($article_id);
		$tpl->assign('info',$info);
		
		$cate=$this->menu();
		$tpl->assign("eu_tree",$cate['eu_tree']);
		//$tpl->assign("manual",$data['manual']);
		$tpl->assign("furGood",$data['furGood']);
		$tpl->assign("service",$data['service']);
		$tpl->assign("day",$data['day']);
		$tpl->assign("id",$article_id);
		$tpl->display('service.tpl.html');
	}*/

	//机电售后服务在线保修
	function repair(){
		$tpl = get_smarty();
		if(!empty($_SESSION['userId'])){
			$user_id=$_SESSION['userId'];
			$homeOb=new home();
			$cate=$this->menu();
			$tpl->assign("eu_tree",$cate['eu_tree']);
			$tpl->assign("ov_tree",$cate['ov_tree']);
			$quoteInfo=$homeOb->getQuoteName($user_id);
			$data=$this->service_menu();
			$tpl->assign("service",$data['service']);
			$tpl->assign("furGood",$data['furGood']);
			//$tpl->assign("manual",$data['manual']);
			$tpl->assign("day",$data['day']);
			$tpl->assign("quoteInfo",$quoteInfo);
		}else{
			header("Location:user.php?login");exit;
		}
		$tpl->display('repair.tpl.html');
	}

	//查找用户订单的地址信息
	function addressInfo(){
		$homeOb=new home();
		$order_id=$_GET['order_id'];
		$addressArr=$homeOb->getHouseAddr($order_id);
		$addressArr['user_name']=$_SESSION['userName'];
		//var_dump($addressArr);exit;
		$addressInfo=json_encode($addressArr);
		echo $addressInfo;
		
	}
	
	//保存用户提交的问题信息
	function saveProblem(){
		//var_dump($_POST);exit;
		$db=new db();
		$homeOb=new home();
		if(empty($_POST['phone'])){
			echo 2;die;
		}else if(!preg_match("/^1[34578]\d{9}$/",$_POST['phone'])){
			echo 3;die;
		}
		$quote_id='';
		foreach ($_POST['quote_id'] as $k => $v) {
			$arr=explode('-',$v);
			$order_id=$arr[0];
			$quote_id.=$arr[1].'-';
		}
		$type='';
		
		// var_dump($order_id,$quote_id,$type);exit;
		$addressArr=$homeOb->getHouseAddr($order_id);
		$data=array (
			'order_id'=>intval($order_id),
			'quote_id'=>rtrim($quote_id,'-'),
			'name' => trim($_POST['name']),
			'phone' => trim($_POST['phone']),
			'type' => rtrim($type,'-'),
			'address' => $addressArr['province'].$addressArr['city'].$addressArr['district'].$addressArr['address'],
			'note' => html_filter($_POST['note']),
			'user_id'=>$_SESSION['userId'],
			'time'=>strtotime($_POST['time']),
			);
		foreach ($_POST['type'] as $k => $v) {
			$data['type']=$v;
			$rs=$db->add('xgj_user_problem',$data);
		}
		//var_dump($data);exit;
		
		if($rs){
			echo 1;die;
		}else{
			echo -1;die;
		}
	}

	
	//常见问题列表页
	function faq(){
	
		$tpl = get_smarty();
		$pn=new home();
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}
		$cate=$this->menu();
		$tpl->assign("eu_tree",$cate['eu_tree']);
		$tpl->assign("ov_tree",$cate['ov_tree']);
		$data=$this->service_menu();
		//常见问题
		$problem=$pn->article(22,$page);
		$count = count($pn->article(22));
		//实例化分页类
		$t = new Page(10,$count,$page,10,'index.php?faq&p=');
		//分页样式
		$page=$t->subPageCss2();
		$tpl->assign("service",$data['service']);
		$tpl->assign("furGood",$data['furGood']);
		//$tpl->assign("manual",$data['manual']);
		$tpl->assign("problem",$problem);
		$tpl->assign("day",$data['day']);
		$tpl->assign("page",$page);
		$tpl->assign("fid",'02');
		$tpl->display('faq.tpl.html');
	
	}
	//常见问题详情页
	function article(){
		$tpl = get_smarty();
		$homeOb=new home();
		$article_id=intval($_GET['id']);
		if(isset($_GET['fid'])){
			$tpl->assign('fid','02');
		}
		$cate=$this->menu();
		$tpl->assign("eu_tree",$cate['eu_tree']);
		$tpl->assign("ov_tree",$cate['ov_tree']);
		$info=$homeOb->articleInfo($article_id);
		$tpl->assign('info',$info);
		$tpl->assign('id',$article_id);
		$data=$this->service_menu();
		$tpl->assign("furGood",$data['furGood']);
		//$tpl->assign("manual",$data['manual']);
		$tpl->assign("service",$data['service']);
		$tpl->assign("day",$data['day']);
		$tpl->display('article.tpl.html');
	
	}
	//服务状态
	function service_state(){
	
		$tpl = get_smarty();
		$cate=$this->menu();
		$tpl->assign("eu_tree",$cate['eu_tree']);
		$tpl->assign("ov_tree",$cate['ov_tree']);
		$data=$this->service_menu();
		$tpl->assign("furGood",$data['furGood']);
		//$tpl->assign("manual",$data['manual']);
		$tpl->assign("service",$data['service']);
		$tpl->assign("day",$data['day']);
		$tpl->display('service_state.tpl.html');
	
	}
	
	//品质生活
	function  qualitylife(){

		$tpl = get_smarty();
		
		$data=$this->menu();		
		$tpl->assign("eu_tree",$data['eu_tree']);
		$tpl->assign("food_tree",$data['food_tree']);
		$tpl->display('qualityLife.tpl.html');
	}
	
	//健康舒适家居
	function homeindex(){
		
		header("Content-type:text/html;charset=utf-8");
		if(empty($_GET['vid'])){
			$vid=0;
		}else{
			$vid=$_GET['vid'];
		}
		$shushijia = new ShushijiaModel();
		$tpl = get_smarty();
		//视频
		$videos = $shushijia->getVedio(3);
		if(!empty($videos)){
			$video = $videos;
		}else{
			$video = array();
		}
		//产品线
		$product=$shushijia->getProduct();
		//合作品牌
		$homeOb=new home();
		$cate=$this->menu();
		$tpl->assign("eu_tree",$cate['eu_tree']);
		$tpl->assign("ov_tree",$cate['ov_tree']);
		$cbrandList=$homeOb->getCbrandAll(1);
		$tpl->assign('product',$product);
		$tpl->assign('cbrandList',$cbrandList);
		$tpl->assign('vid',$vid);
		$tpl->assign("video",$video);
		$tpl->display("homeindex.tpl.html");
	}	
		

	//合作品牌详情页面
	function cbrandInfo(){
		$tpl = get_smarty();
		$homeOb=new home();
		$cate=$this->menu();
			$tpl->assign("eu_tree",$cate['eu_tree']);
			$tpl->assign("ov_tree",$cate['ov_tree']);
		$id=intval($_GET['id']);
		//合作品牌图册
		$cbrandImage=$homeOb->getCbrandImage($id);
		//合作品牌
		$cbrandInfo=$homeOb->getCbrandOne($id);
		$cbrandInfo['logo']=getImages($cbrandInfo['logo']);
		$cbrandInfo['product']=explode('|',$cbrandInfo['product']);
		//分页每页的条数
		$num=1;

		//全部订单
		$p = 1;
		$list=$homeOb->getCbrandList($id,$p,$num);//显示列表内容
		$count=$homeOb->getCbrandCount($id);//分页的总条数
		//总页数
		$pcount = ceil($count/$num);

		//var_dump($list,$count,$pcount);exit;
		$tpl->assign('cbrandImage',$cbrandImage);
		$tpl->assign('cbrandInfo',$cbrandInfo);
		$tpl->assign('list',$list);
		$tpl->assign('pcount',$pcount);
		$tpl->assign("p",$p);
		$tpl->display("cbrand.tpl.html");
	}

	function ajaxCbrand(){
		$tpl = get_smarty();
		$homeOb=new home();
		$page = $_GET['page'];
		$id = $_GET['id'];
		$num = 1;
		$list=$homeOb->getCbrandList($id,$page,$num);//显示列表内容
		$count=$homeOb->getCbrandCount($id);//分页的总条数
		$pcount = ceil($count/$num);
		
        if (!empty($list)){
        	foreach($list as $k => $v){
    		echo '
				<div class="cbrandProApp_left"><img src="'.$v[image].'" width="360" height="240"></div>
				<div class="cbrandProApp_right">'.     
					htmlspecialchars_decode($v['content']).
				'</div>';
        	}
		}
        echo '                
            <!--分页开始-->
            <div class="page">';
              if (!empty($page)){
	            echo '
	              <a href="javascript:;" onclick="page('.$id.',1)">首页</a>';  
	            if ($page==1) {
              		echo '<a href="javascript:;">[上一页]</a>';
              	}else{
              		echo '<a href="javascript:;" onclick="page('.$id.','.($page-1).')">[上一页]</a>';
              	}
	          }
              if ($page > 2){
	            echo '
	              <a href="javascript:;" onclick="page('.$id.','.($page-2).')">'.($page-2).'</a>';
              }
              if ($page > 1){
	            echo '
	              <a href="javascript:;" onclick="page('.$id.','.($page-1).')">'.($page-1).'</a>';
              }
              echo '<span>'.$page.'</span>';
              if ($pcount > $page){
	            echo '
	              <a href="javascript:;" onclick="page('.$id.','.($page+1).')">'.($page+1).'</a>';
              }
              if ($pcount > ($page+1)){
	            echo '
	              <a href="javascript:;" onclick="page('.$id.','.($page+2).')">'.($page+2).'</a>';
              }
              if (!empty($page)){
              	if ($page<$pcount) {
              		echo '<a href="javascript:;" onclick="page('.$id.','.($page+1).')">[下一页]</a>';
              	}else{
              		echo '<a href="javascript:;">[下一页]</a>';
              	}
				echo '
	              <a href="javascript:;" onclick="page('.$id.','.$pcount.')">[尾页]</a>';
              }
        echo '        
            </div>
            <!--分页结束-->       
		';
	}

	//家具建材
	function furniture()
	{
		$tpl = get_smarty();
		$pn= new home();
		$data=$pn->category(1);
		$tpl->assign('eu_tree',$data);
		$tpl->display('furniture.tpl.html');
	}
	//更多反馈
	function fedback(){
		$tpl = get_smarty();
		$homeOb= new home();
		$num=4;
		//判断是否有分页
		if(!isset($_GET['p'])){
			$p = 1;
		}else{
			$p = $_GET['p'];
		}
		//显示列表内容
		$data=$homeOb->getFedback($p,$num);
		//分页的总条数
		$count=$homeOb->getFedbackCount();
		//实例化分页类
		$t = new Page($num, $count, $p, 5, "index.php?fedback&p=");
		//分页样式
		$page=$t->subPageCss2();
		$cate=$this->menu();
			$tpl->assign("eu_tree",$cate['eu_tree']);
			$tpl->assign("ov_tree",$cate['ov_tree']);
		//所有反馈
		//var_dump($data);exit;
		$tpl->assign('page',$page);
		$tpl->assign('data',$data);
		$tpl->display('fedback.tpl.html');
	}
	//支付服务
	function pay(){
		$tpl = get_smarty();
		$homeOb= new home();
		//所有分类
		$cate=$this->menu();
			$tpl->assign("eu_tree",$cate['eu_tree']);
			$tpl->assign("ov_tree",$cate['ov_tree']);
		$data=$homeOb->article(1);
		$article_id = !empty($_GET['a'])?$_GET['a']:$data[0]['article_id'];
		$content=$homeOb->integral_detail($article_id);
		//文章详细列表

		$tpl->assign('data',$data);
		$tpl->assign('content',$content);
		$tpl->display('pay.tpl.html');
	}
	//资源分享
	function share(){
		$tpl = get_smarty();
		$homeOb= new home();
		$article=$homeOb->article(2);
		$data=$this->menu();

		$tpl->assign("article",$article);
		$tpl->assign("eu_tree",$data['eu_tree']);
		$tpl->assign("food_tree",$data['food_tree']);
		$tpl->display('sharing.tpl.html');
	}
	//积分和券
	function integral(){
		$tpl = get_smarty();
		$homeOb= new home();
		
		$cate=$this->menu();
		$tpl->assign("eu_tree",$cate['eu_tree']);
		$tpl->assign("ov_tree",$cate['ov_tree']);
		//所有分类
		$data=$homeOb->article(3);
		$article_id = !empty($_GET['a'])?$_GET['a']:$data[0]['article_id'];
		$content=$homeOb->integral_detail($article_id);
		//文章详细列表
		$tpl->assign('data',$data);
		$tpl->assign('content',$content);
		$tpl->display('integral.tpl.html');
	}

	//百科知识
	function knowledge()
	{
	
		header("Content-type:text/html;charset=utf-8");
		$type = null;//$type为1表示健康舒适家居，2表示欧团代购，3表示售后服务
		if (isset($_GET['type'])){
			$type = $_GET['type'];
		}
		
		$knowledge = new ShushijiaModel();
		$tpl = get_smarty();
		//分页
		$each_disNums = 6;//每页显示数据条数
		if (!isset($_GET['p'])){
			$page = 1;
		}else {
			$page = $_GET['p'];
		}
		
		//百科知识（健康舒适家居）
		$ssj = $knowledge->getAllArticle($cat_id=49,$page,$each_disNums);
		//print_r($ssj);
		//百科知识（欧洲团代购）
		$ozt = $knowledge->getAllArticle($cat_id=50,$page,$each_disNums);
		//百科知识（售后服务）
		$shf = $knowledge->getAllArticle($cat_id=51,$page,$each_disNums);
		
		if ($type==1){
			$total = $knowledge->show_total_count($tab='xgj_article',$cat_id=49);
			$t_nav = new Page($each_disNums, $total, $page, 5, "index.php?knowledge&type=1&p=");
			$page_nav=$t_nav->subPageCss2();//分页样式
			
			$tpl->assign('page_nav',$page_nav);
			$tpl->assign('kndata',$ssj);
		}elseif ($type==2){
			$total = $knowledge->show_total_count($tab='xgj_article',$cat_id=50);
			$t_nav = new Page($each_disNums, $total, $page, 5, "index.php?knowledge&type=2&p=");
			$page_nav=$t_nav->subPageCss2();//分页样式
				
			$tpl->assign('page_nav',$page_nav);
			$tpl->assign('kndata',$ozt);
		}elseif ($type==3){
			$total = $knowledge->show_total_count($tab='xgj_article',$cat_id=51);
			$t_nav = new Page($each_disNums, $total, $page, 5, "index.php?knowledge&type=3&p=");
			$page_nav=$t_nav->subPageCss2();//分页样式
				
			$tpl->assign('page_nav',$page_nav);
			$tpl->assign('kndata',$shf);
		}else {
			$total = $knowledge->show_total_count($tab='xgj_article',$cat_id=49);
			$t_nav = new Page($each_disNums, $total, $page, 5, "index.php?knowledge&type=1&p=");
			$page_nav=$t_nav->subPageCss2();//分页样式
				
			$tpl->assign('page_nav',$page_nav);
			$tpl->assign('kndata',$ssj);
		}
		$cate=$this->menu();
			$tpl->assign("eu_tree",$cate['eu_tree']);
			$tpl->assign("ov_tree",$cate['ov_tree']);

		//$tpl->assign('profile',$user_info);
		//$tpl->assign('report',$report);
		$tpl->display('knowledge.tpl.html');
	}

	//家具建材
	function productCont(){
		$tpl = get_smarty();
		$cat_id=isset($_GET['cat_id'])?intval($_GET['cat_id']):24;
		$id=isset($_GET['id'])?intval($_GET['id']):25;
		$shushijia = new ShushijiaModel();
		$cate = $shushijia->getArticleCate($cat_id);
		$data = $shushijia->getArticleInfo('',$id);//升级前
		$tpl->assign('cate',$cate);
		$tpl->assign('data',$data);
		$cate1=$this->menu();
			$tpl->assign("eu_tree",$cate1['eu_tree']);
			$tpl->assign("ov_tree",$cate1['ov_tree']);
		$tpl->display('productCont.tpl.html');
	}
	//全国展示中心
	function show(){
		$tpl =get_smarty();
		/******************************grass****2016-4-6*******************************/
		$con=mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD,DB_NAME);
		mysqli_set_charset($con,"utf8");
		//$con=new mysqli(DB_HOST, DB_USER, DB_PASSWORD,DB_NAME);
		/*接受参数*/
		$city = isset($_GET['city'])?$_GET['city']:'';
		$p    = isset($_GET['p'])?(int)$_GET['p']:1;
		/*拼凑条件*/
		$where    = '';
		$page_url = 'index.php?show';
		if(!empty($city)){
			$where    .= "And d_city LIKE '%{$city}%' ";
			$page_url .= "&city={$city}";
		}
		
		$page_url .= '&p=';
		
		/*分页数据*/
		$sql        = "SELECT COUNT(*) FROM xgj_furnish_dealer  where  d_store_figure <> ''  $where";
		$total      = mysqli_fetch_row(mysqli_query($con,$sql));
		$total      = $total[0]>0?$total[0]:0;
		$config     = require './libs/common/config.php';
		$page_size  = $config['DEALER_PAGE_SIZE'];
		$total_page = ceil($total/$page_size);
		$p          = $p<=1?1:($p>=$total_page?$total_page:$p);
		$page       = new Page($page_size, $total, $p, 5, $page_url);
		$page_str   = $page->subPageCss2();
		$limit      = 'LIMIT '.($p-1)*$page_size.','.$page_size;

		/*地区数据 $district*/
		$sql    = "SELECT * FROM xgj_furnish_dealer  where  d_store_figure <> '' ";
		$result = mysqli_query($con,$sql);

		$district=$mapcity=array();
		
		if($result && mysqli_num_rows($result)>0){
			while ($row = mysqli_fetch_assoc($result)) {
				$mapcity[]=$row;
			}
		}
		//var_dump('<pre>',$mapcity);die();
		/*城市分组, 去重*/
		
			if(!empty($mapcity)){
				foreach ($mapcity as &$v2) {
					if(@!in_array($v2['d_city'], $district[$v2['d_province']])){
						$district[$v2['d_province']][] = $v2['d_city'];
					}
				}
			}
	

		/*服务商列表$dealer*/
		$dealer = $dealer_img = array();
		$sql    = "SELECT * FROM xgj_furnish_dealer where  d_store_figure <> '' $where $limit";
		$result = mysqli_query($con,$sql);
		if($result && mysqli_num_rows($result)>0){
			while ($row = mysqli_fetch_assoc($result)) {
				$sql = "SELECT * FROM xgj_furnish_dealer_img WHERE d_id={$row['d_id']} AND is_show=1";
				$dealer_img = array();
				if($s = mysqli_query($con,$sql)){
					while ($r = mysqli_fetch_assoc($s)) {
						$r['url'] = getImages($r['url']);
						$dealer_img[] = $r;
					}
				}
				$row['d_store_figure'] = getImages($row['d_store_figure']);
				$row['images'] = $dealer_img;
				$dealer[]      = $row;
			}
		}


		mysqli_close($con);
		$tpl->assign('district', $district);
		$tpl->assign('dealer', $dealer);
		$tpl->assign('page', $page_str);
		$cate=$this->menu();
			$tpl->assign("eu_tree",$cate['eu_tree']);
			$tpl->assign("ov_tree",$cate['ov_tree']);
		/******************************grass****2016-4-6*******************************/
		$tpl->display('show.tpl.html');
	}
	
	//关于我们
	function aboutus()
	{
		$tpl = get_smarty();
		$pn= new home();
		$aboutus=$pn->article(23);
		$cate=$this->menu();
			$tpl->assign("eu_tree",$cate['eu_tree']);
			$tpl->assign("ov_tree",$cate['ov_tree']);
		//合作品牌
		$cbrandList=$pn->getCbrandAll(1);
		$tpl->assign('cbrandList',$cbrandList);
		//var_dump('<pre>',$aboutus);die();
		$tpl->assign('aboutus_list',$aboutus);
		$tpl->display('aboutus.tpl.html');
	}
	
	//30家品牌合作伙伴
	function authorization()
	{ 
	    
		$tpl=get_smarty();
	$cate=$this->menu();
			$tpl->assign("eu_tree",$cate['eu_tree']);
			$tpl->assign("ov_tree",$cate['ov_tree']);
		//$tpl->assign('welcom_message',$welcom_message);
		//$tpl->assign('profile',$user_info);
		//$tpl->assign('report',$report);
		$tpl->display('authorization.tpl.html');
	}
	 
	//视频列表
	function video(){	

		!empty($_GET["video"])?$video_id = $_GET["video"]:$video_id = '';

		!empty($_GET['v'])?$id = $_GET['v']:$id = '';

		$model = new home();
		$video = $model->video_data($video_id,$id);
		
		$cate=$this->menu();
		$tpl = get_smarty();
		$tpl->assign("eu_tree",$cate['eu_tree']);
		$tpl->assign("ov_tree",$cate['ov_tree']);
		$tpl->assign("video",$video);
		$tpl->assign('pid',$video_id);
		$tpl->display('video.tpl.html');
	}

	//视频列表详细
	function videoinfo(){	

		!empty($_GET['videoinfo'])?$id = $_GET['videoinfo']:$id = '';

		$model = new home();
		$video = $model->video_data_info($id);
		$cate=$this->menu();
		$tpl = get_smarty();
		$tpl->assign("eu_tree",$cate['eu_tree']);
		$tpl->assign("ov_tree",$cate['ov_tree']);
		$tpl->assign("video",$video);
		$tpl->display('videoinfo.tpl.html');
	}
	


    //问题提交
	function add(){
		// if (empty($_SESSION['userName'])) {
		// 	header("Location:user.php?login");exit;
		// }
		
		$user_id = $_SESSION['userId'];
		$name = $_POST['create-name-01'];
		$phone = $_POST['create-name-02'];
		$province = $_POST['cho_Province'];
		$city = $_POST['cho_City'];
		$area = $_POST['cho_Area'];
		$address = $_POST['address'];
		$content = trim($_POST['content']);

		foreach ($_POST as $value) {
			if ($value == '您的姓名' || $value == '您的手机号码' || $value == '请选择省份' || $value == '请选择城市' || $value == '请选择地区' || $value == '请输入地址') {
				// echo '请填写完整在提交，谢谢！';exit;
				header("Location:index.php?service=index&error1");exit;
			}
		}

		if ($content == '') {
			// echo '请填写完整在提交，谢谢！';exit;
			header("Location:index.php?service=index&error1");exit;
		}

		if (strlen($phone) != '11') {
			// echo '请正确填写手机号码，谢谢！';exit;
			header("Location:index.php?service=index&error2");exit;
		}

		$data = array('user_id'=>$user_id,'name'=>$name,'phone'=>$phone,'province'=>$province,'city'=>$city,'area'=>$area,'address'=>$address,'content'=>$content,'time'=>time());

		$table = 'xgj_user_problem';

		$mysql = new db();
		$result = $mysql->add($table,$data);

		if (!empty($result)) {
			// echo '提交成功';exit;
			header("Location:index.php?service=index&success");exit;
		}else{
			// echo '提交失败';exit;
			header("Location:index.php?service=index&error3");exit;
		}
	}

	/**
	 *	比较省市内是否有经销商
	 **/
	function province(){
		$homeOb= new home();

		$x = $_GET['province'];
		$c = $_GET['city'];
		$d = $_GET['city3'];

		$xcd = $x.'-'.$c.'-'.$d;
		$dealer_province = $homeOb->xgj_furnish_dealer($xcd);

		if ($dealer_province==true) echo '1';
		else echo '2';exit;
	}


	/**
	 *	跳转404页面
	 **/
	public function error(){

		$tpl = get_smarty();
		$cate=$this->menu();
		$tpl->assign("eu_tree",$cate['eu_tree']);
		$tpl->assign("ov_tree",$cate['ov_tree']);
		$tpl->display('error.tpl.html');
	}

	/**
	 * 联系我们
	 **/
	public function contactus(){
		$homeOb = new home();
		$model = $homeOb->contactus();
		$tpl = get_smarty();
		$cate=$this->menu();
		$tpl->assign("eu_tree",$cate['eu_tree']);
		$tpl->assign("ov_tree",$cate['ov_tree']);
		$tpl->assign('data',$model);
		$tpl->display('contactus.tpl.html');
	}

	/**
	 * 人才招聘列表页
	 **/
	public function joblist(){
		
		// echo '<pre>';
		// var_dump($model);exit;
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}

		$homeOb = new home();
		$model = $homeOb->joblist();
		$result = $homeOb->select_joblist($page);

		$count = count($model);
		//实例化分页类
		$t = new Page(5,$count,$page,5,'index.php?joblist&p=');

		//分页样式
		$page=$t->subPageCss2();//分页样式

		$tpl = get_smarty();
		$cate=$this->menu();
		$tpl->assign("eu_tree",$cate['eu_tree']);
		$tpl->assign("ov_tree",$cate['ov_tree']);
		$tpl->assign('data',$result);
		$tpl->assign('page',$page);
		$tpl->display('joblist.tpl.html');
	}

	/**
	 * 人才招聘详细页
	 **/
	public function jobinfo(){
		$id = intval($_GET['id']);
		$homeOb = new home();
		$model = $homeOb->jobinfo($id);
		$tpl = get_smarty();
		$cate=$this->menu();
		$tpl->assign("eu_tree",$cate['eu_tree']);
		$tpl->assign("ov_tree",$cate['ov_tree']);
		$tpl->assign('data',$model);
		$tpl->display('jobinfo.tpl.html');
	}

	/**
	 * 招商加盟
	 **/
	public function join(){

		$homeOb = new home();
		$model = $homeOb->join();
		if (empty($model)) {
			$model['0'] = '';
		}
		$tpl = get_smarty();
		$cate=$this->menu();
		$tpl->assign("eu_tree",$cate['eu_tree']);
		$tpl->assign("ov_tree",$cate['ov_tree']);
		$tpl->assign('data',$model['0']);
		$tpl->display('join.tpl.html');
	}

	/**
	 * 招商加盟添加合作申请表
	 **/
	public function join_add(){

		$homeOb = new home();
		$aaa = $_GET;
		unset($aaa['join_add']);
		foreach ($aaa as $value) {
			if ($value=='') {
				echo '3';exit;
			}
		}
		
		$data = array(
			'company'=>$_GET['company'],
			'c_business'=>$_GET['c_business'],
			'c_join'=>$_GET['join1'],
			'people'=>$_GET['people'],
			'tel'=>$_GET['tel'],
			'email'=>$_GET['email'],
			'time'=>time()
			);

		$model = $homeOb->join_add($data);

		if (!empty($model)) {
			echo '1';exit;
		}else{
			echo '2';exit;
		}
	}

	/**
	 * 商务合作列表页
	 **/
	public function cooperationlist(){
		
		if(!isset($_GET['p'])){
			$page = 1;
		}else{
			$page = $_GET['p'];
		}

		$homeOb = new home();

		$model = $homeOb->cooperationlist();

		$result = $homeOb->select_cooperationlist($page);

		$count = count($model['0']);

		//实例化分页类
		$t = new Page(5,$count,$page,5,'index.php?cooperationlist&p=');

		//分页样式
		$page=$t->subPageCss2();//分页样式

		$tpl = get_smarty();
		$cate=$this->menu();
			$tpl->assign("eu_tree",$cate['eu_tree']);
			$tpl->assign("ov_tree",$cate['ov_tree']);
		$tpl->assign('data',$result);
		$tpl->assign('data_img',$model['1']);
		$tpl->assign('page',$page);
		$tpl->display('cooperationlist.tpl.html');
	}

	/**
	 * 商务合作详细页
	 **/
	public function cooperationinfo(){
		$article_id = intval($_GET['article_id']);
		if (empty($article_id)) {
			echo '<script>history.go(-1)</script>';
		}
		$homeOb = new home();
		$model = $homeOb->cooperationinfo($article_id);
		$tpl = get_smarty();
		$cate=$this->menu();
			$tpl->assign("eu_tree",$cate['eu_tree']);
			$tpl->assign("ov_tree",$cate['ov_tree']);
		$tpl->assign('data',$model);
		$tpl->display('cooperationinfo.tpl.html');
	}
	/**
	 * 开发商合作案例
	 **/
	public function developer(){		
		$tpl = get_smarty();
		$homeOb = new home();
		$data = $homeOb->article_cat(33);
		foreach($data as $key =>$val){
			$data[$key]['list']=$homeOb->article($val['cat_id']);
		}
		$tpl->assign('data',$data);
		$cate=$this->menu();
			$tpl->assign("eu_tree",$cate['eu_tree']);
		$tpl->assign("ov_tree",$cate['ov_tree']);
		$tpl->display('developer.tpl.html');
	}

/*********************footer区 文章分类内容***********************/

	function quetion(){
		$tpl = get_smarty();
		$homeOb= new home();
		$cate=$this->menu();
		$tpl->assign("eu_tree",$cate['eu_tree']);
		$tpl->assign("ov_tree",$cate['ov_tree']);
		//所有分类
		if(!empty($_GET['type'])){
				$cat_id=$_GET['type'];
				$cat=$homeOb->article_catInfo($cat_id);
				$data=$homeOb->article($cat['cat_id']);

				$article_id = !empty($_GET['a'])?$_GET['a']:$data[0]['article_id'];
				$content=$homeOb->integral_detail($article_id);
				//文章详细列表
				$tpl->assign('cat',$cat);
				$tpl->assign('data',$data);
				$tpl->assign('content',$content);
				$tpl->display('quetion.tpl.html');		
		}else{
				$article_id=!empty($_GET['a'])?$_GET['a']:1;
				$content=$homeOb->articleInfo($article_id);
				$tpl->assign('content',$content);
				$tpl->display('quetionone.tpl.html');	
		}
	}

	function videoSrc(){
		$video_img  = getImages($_SESSION['furnish_quote']['video_img']);
		$video      = getImages($_SESSION['furnish_quote']['video']);
		$quote_name = $_SESSION['furnish_quote']['quote_name'];
		echo <<<html
		<script src="js/video.js" type="text/javascript"></script>
			<div class="clickAdd_video-center-bk"> 
		        <div class="clickAdd_video-center">
		        	<div class="clickAdd_video-center-title">
		            	<div class="clickAdd_video-center-title-cha" id="video_AddOut01Id">
		                    <a href="javascript:;"  onclick="videoNone()">
		                        X
		                    </a>
		                </div>
		            </div>
		            <div class="clickAdd_video-center-video" >
		            	<video id="example_video_1" class="video-js vjs-default-skin" controls preload="none" width="680" height="380" poster="{$video_img}" data-setup="">
		                    <source class='video' src="{$video}" type='video/mp4' />
		                    <source class='video' src="{$video}" type='video/webm' />
		                    <source class='video' src="{$video}" type='video/ogg' />
		                    <source class='video' src="{$video}" type='video/avi' />
		                    <track kind="captions" src="demo.captions.vtt" srclang="en" label="English"></track><!-- Tracks need an ending tag thanks to IE9 -->
		                    <track kind="subtitles" src="demo.captions.vtt" srclang="en" label="English"></track><!-- Tracks need an ending tag thanks to IE9 -->
		                </video>
		            </div>
		            <div class="clickAdd_video-center-name" id='title'>{$quote_name}</div>
		        </div>
		    </div>
html;


	}

}
?>
