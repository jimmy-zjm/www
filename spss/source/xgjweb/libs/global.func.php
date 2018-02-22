<?php
require_once (WWW_DIR . "/libs/UploadFile.class.php");
/**
 * 后台用户模块权限判断
 *
 * @param intiger $groupid
 * @param string $module
 */

function dsetcookie($var, $value){
	setcookie($var,$value,time()+3600);
}
/**
 * 打印
 * @param unknown $data
 */
function vdump($data){
	echo"<pre />";
	var_dump($data);exit;
}



/*
处理订单状态
 */
function OrderStatus($status){
	switch ($status) {
		case '0':
			return '等待付款';
			break;
		case '1':
			return '等待发货';
			break;
		case '2':
			return '等待收货';
			break;
		case '3':
			return '退货中';
			break;
		case '4':
			return '待评论';
			break;
		case '5':
			return '已完成';
			break;
		case '6':
			return '已取消';
			break;
		case '7':
			return '删除';
			break;
		case '8':
			return '取消订单';
			break;
		default:
			return '参数错误';
			break;
	}
}
/*
处理订单状态职位  1.店经理 2.接待 3.业务员 4.产品经理 5.设计师 6.项目管理 7.售后人员
 */
function positionStatus($status){
	switch ($status) {
		case '1':
			return '店经理';
			break;
		case '2':
			return '接待';
			break;
		case '3':
			return '业务员';
			break;
		case '4':
			return '产品经理';
			break;
		case '5':
			return '设计师';
			break;
		case '6':
			return '项目管理';
			break;
		case '7':
			return '售后人员';
			break;
	}
}
/*
获取ajax分页数据
 */
function getAjaxPage($total,$page_size){
    require WWW_DIR.'/libs/common/PageAjax.class.php';
    $page = new PageAjax($total,$page_size);
    $page->setConfig('theme', '<p>共 %TOTAL_ROW% 条记录 | 当前第%NOW_PAGE%/%TOTAL_PAGE%页</p> %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
    $page->setConfig('prev','[上一页]');
    $page->setConfig('next','[下一页]');
    $page->setConfig('first','[首页]');
    $page->setConfig('last','[尾页]');
    return array(
        'page'  => $page->show(),
        'limit' => $page->firstRow.','.$page->listRows,
        'total_page' =>$page->totalPages,
    );
}

function getAjaxPage01($total,$page_size){
    require WWW_DIR.'/libs/common/PageAjax01.class.php';
    $page = new PageAjax01($total,$page_size);
    $page->setConfig('theme', '<p>共 %TOTAL_ROW% 条记录 | 当前第%NOW_PAGE%/%TOTAL_PAGE%页</p> %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
    $page->setConfig('prev','[上一页]');
    $page->setConfig('next','[下一页]');
    $page->setConfig('first','[首页]');
    $page->setConfig('last','[尾页]');
    return array(
        'page'  => $page->show(),
        'limit' => $page->firstRow.','.$page->listRows,
        'total_page' =>$page->totalPages,
    );
}

//获取短信模板（短信接口）
function getMessagetem(){
	header("Content-Type:text/html; charset=utf-8");
	$post_data['action']   = 'getSmsTemplate';
	$post_data['account']   = 'N00000004065';
	$post_data['password']	= 'NgnYM9nE3W8OTeka';
	$post_data['md5']	= '81f0e1f0-32df-11e3-a2e6-1d21429e5f46&var1=XXX';
	$o='';
	foreach ($post_data as $k=>$v){
	   $o.= "$k=".$v."&";
	}
	$post_data=substr($o,0,-1);
	$ch = curl_init();  
	$timeout = 5;  
	curl_setopt ($ch, CURLOPT_URL, "http://115.29.14.183:3000/openService?$post_data");  
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);  
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);  
	$file_contents = curl_exec($ch);  
	curl_close($ch);  
	$rest = json_decode($file_contents,true);
	return $rest;
}


//获取短信验证码
function getMessage($tel){
	$arr=getMessagetem();
	header("Content-Type:text/html; charset=utf-8");
	$post_data['action']   = 'sendInterfaceTemplateSms';
	//$post_data['action']   = 'interfaceSms';
	$post_data['account']   = 'N00000004065';
	$post_data['password']	= 'NgnYM9nE3W8OTeka';
	$post_data['num']	= $tel;
	$post_data['templateNum']	= $arr['data'][0]['num'];
	$post_data['md5']	= '81f0e1f0-32df-11e3-a2e6-1d21429e5f46';
	//$post_data['p2']	= '3';
	$str=mt_rand(1000,9999);
	$_SESSION['msg']=$str;
	$_SESSION['tel']=$tel;
	$post_data['var1'] = $str;

	$o='';
	foreach ($post_data as $k=>$v){
	   $o.= "$k=".$v."&";
	}
	$post_data=substr($o,0,-1);
	
	// curl 方法
	$ch = curl_init();  
	$timeout = 5;  
	curl_setopt ($ch, CURLOPT_URL, "http://115.29.14.183:3000/openService?$post_data");  
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);  
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);  
	$file_contents = curl_exec($ch);  
	curl_close($ch);  
	$rest = json_decode($file_contents,true);
	return $rest;
}


function escape($str)
{
	$out = '';
	$len = strlen($str);
	for ($i = 0 ; $i < $len ; $i++)
	$out .= '%'.@bin2hex($str[$i]);
	return $out;
}

function unescape($str)
{
	$out = '';
	$arr = explode('%', $str);
	$len = strlen($str);
	for ($i = 0 ; $i < $len ; $i++)
	$out .= @pack('H4', $arr[$i]);
	return $out;
}
function format_date($datetime, $no_year = false, $no_time = false)
{
	if (strlen($datetime) == 0) return '';
	$time = strstr($datetime, ' ');
	if(empty($time))
	$date = $datetime;
	else
	list($date, $time) = explode(' ', $datetime);

	//format date
	list($y, $m, $d) = explode("-", $date);
	$ny = date("Y");
	$nm = date("m");
	$nd = date("d");

	$datetime_stamp = mktime(0, 0, 0, $m, $d, $y);
	$now_stamp = mktime(0, 0, 0, $nm, $nd, $ny);
	$day_sec = 24*60*60;
	$day_dis = $now_stamp-$datetime_stamp;
	if ($day_dis == 0)
	$date_str = "今天";
	elseif ($day_dis == $day_sec)
	$date_str = "昨天";
	elseif ($day_dis < 0 && abs($day_dis) == $day_sec)
	$date_str = "明天";
	else
	{
		if ($y != $ny && !$no_year)
		$date_str = $y."年".$m."月".$d."日";
		else
		$date_str = $m."月".$d."日";
	}

	//format time
	if(!empty($time) && !$no_time)
	{
		$time_str = " ";
		list($h, $i, $s) = explode(":", $time);
		if($day_dis == 0)
		{
			$nh = date("H");
			$ni = date("i");
			$ns = date("s");
			if ($h==$nh && $i==$ni)
			$time_str .= "1分钟前";
			elseif ($h==$nh)
			$time_str .= ($ni-$i)."分钟前";
			else
			$time_str .= ($nh-$h)."小时前";

			$date_str = "";
		}
		else
		{
			$time_str .= $h.":".$i;
		}
	}
	else
	{
		$time_str = "";
	}

	return $date_str.$time_str;
}
function split_user_info($infostr)
{
	if(!empty($infostr)){
		$infos = explode("||",$infostr);
		foreach($infos as $info){
			$_userinfo['yy_'.substr($info,0,strpos($info,'~'))] = unescape(substr($info,strpos($info,'~')+1));
		}
	}
	return $_userinfo;
}

function implode_user_info($infoArr)
{
	if(is_array($infoArr)){
		foreach($infoArr as $key=>$value){
			if($key == 'id' || $key == 'name' || $key == 'email' || $key=='usergroup' || $key == 'remember'||$key="parentID"){
				$infoArr[$key]=$key.'~'.escape($value);
			}
		}
		$infostr=implode('||',$infoArr);
	}
	return $infostr;
}
/**
 * 后台用户登陆验证
 *
 * @return unknown
 */
function admin_check_logon(){
	if (!isset($_COOKIE['adminUserName']) || !isset($_COOKIE['adminUserId'])) {
		admin_go_login();
		exit();
	}else{
		$tpl = get_admin_smarty();
		$db=new db();
		$sql="SELECT `permission` FROM `xgj_admin_user` WHERE `user_id` = '{$_COOKIE['adminUserId']}' LIMIT 1";
		$result=$db->getRow($sql);
		return $result['permission'];
	}

	$username = unescape($_COOKIE['adminUserName']);			// username
	$userpassword = unescape($_COOKIE['adminUserId']);			// user infos

}

function admin_go_login(){
	$_redirect_url = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	header("Location: index.php?login");
}


/*
 * 验证经销商是否登陆
 * */
function dealer_check_logon(){
    if (!isset($_COOKIE['dealerId']) || !isset($_COOKIE['dealerName'])) {
        header("Location: dealer.php");
        exit();
    }
}
/**
 * 前台用户登陆验证
 *
 * @return unknown
 */
function user_check_logon(){
	if (!isset($_SESSION['userName']) || !isset($_SESSION['userId'])) {
		user_go_login();
		exit();
	}

	$username = unescape($_SESSION['userName']);	     // username
	$userId = unescape($_SESSION['userId']);			// user infos

}

function user_go_login(){
	$_redirect_url = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	header("Location: user.php?login");
}
/**
 * 获得后台用户登陆信息
 *
 * @return array $_userinfo
 */
function admin_get_user_info(){
	$_userinfo = array();
	foreach($_COOKIE as $key => $val) {
		if( strpos($key,"bb_")===0 ) {
			if($key != 'bb_userinfo') {
				$_userinfo[$key] = unescape($val);
			} else {
				$infos = explode("||",unescape($val));
				foreach($infos as $info)
				$_userinfo['bb_'.substr($info,0,strpos($info,'~'))] = unescape(substr($info,strpos($info,'~')+1));
			}
		}
	}
	unset($_userinfo['bb_remember']);
	return $_userinfo;
}
/**
 * 后台用户登陆成功后操作
 *
 */
function admin_set_user_info($userid, $username, $userinfo, $remember){
	global $_timestamp;

	$mMD5_KEY = '&*^%#^KHHFm(^@_internal';
	$authtime = $_timestamp;
	$authkey = strtoupper(md5($userid.$username.$authtime.$mMD5_KEY));
	$userinfo['remember'] = $remember;

	dsetcookie('bb_userid', $userid, $remember ? 86400 * 365 : (-$_timestamp - 86400));
	dsetcookie('bb_username', $username, $remember? 86400 * 365 : (-$_timestamp - 86400));
	dsetcookie('bb_userinfo', implode_user_info($userinfo), $remember ? 86400 * 365 : (-$_timestamp - 86400));
	dsetcookie('bb_auth', "authtime~".$authtime."||authkey~".$authkey, $remember? 86400 * 365 : (-$_timestamp - 86400) );
}

function admin_clear_user_info()
{
	dsetcookie('bb_userid', '', 0);
	dsetcookie('bb_username', '', 0);
	dsetcookie('bb_userinfo', '', 0);
	dsetcookie('bb_auth', '', 0);
	dsetcookie('bb_usergroup', '', 0);
}
function filterarr($arr1,$arr2){
	foreach($arr1 as $key => $value){
		if(in_array($value,$arr2)){
			unset($arr1[$key]);
		}
	}
	return $arr1;
}
function sys_get_smarty()
{
	$tpl = new Smarty();
	//$tpl->error_reporting	= E_ERROR | E_WARNING | E_PARSE;
	$tpl->error_reporting	= E_ALL;
	$tpl->template_dir		= SMARTY_TPL_DIR;
	$tpl->compile_dir		= SMARTY_COMPILE_DIR;
	$tpl->config_dir		= SMARTY_CONFIG_DIR;
	$tpl->left_delimiter	= '{:';
	$tpl->right_delimiter	= ':}';
	$tpl->debugging   =   false;
	$admininfo = array();
	//添加头部需要增加的用户信息总数统计
	$tinfo = null;
	$top_userinfo = admin_get_user_info();
	if (isset($top_userinfo)){
		if (isset($top_userinfo['bb_userid'])){
			$userid = $top_userinfo['bb_userid'];
			$mkt_admin = new mkt_admin();
			$db = new mysql_db();
			$tinfo = array();
			$tinfo = $mkt_admin->get_info($db,$userid);
			if($tinfo) $tinfo['lastlogin'] = format_date($tinfo['lastlogin']);
		}
	}
	//添加头部需要增加的用户信息总数统计
	
	$tpl->assign('admininfo', $tinfo);
	$tpl->assign('err','');
	return $tpl;
}
function sys_get_pages_section($now_page, $total_page, $url)
{
	if ($total_page == 1 || $total_page == 0) return "";
	$html = '';
	'<a href="#">←</a>
	<a href="#">→</a>';
	if($now_page > 1){
		$html.="共".$total_page."页";
		$html.= '<a href="'.$url.($now_page-1).'">←</a>';
	}
	/*	for($i = 1; $i < $total_page + 1;$i++){
	$html .= "<a href='".$url.$i."'>[";
	if($i == $now_page)
	$html .= "<strong>";
	$html .= $i;
	if($i == $now_page)
	$html .= "</strong>";
	$html .= "] </a>";
	}*/
	if($now_page < $total_page){
		$html.= '<a href="'.$url.($now_page+1).'">→</a>';
		$html.= '<a href="'.$url.($total_page).'">末页</a>';
	}
	return $html;
}
function legal_email($email) {
	return strlen($email) > 8 && preg_match("/^[-_+.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+([a-z]{2,4})|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i", $email);
}
function xlsBOF() {
	echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
	return;
}
function xlsEOF() {
	echo pack("ss", 0x0A, 0x00);
	return;
}
function xlsWriteNumber($Row, $Col, $Value) {
	echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
	echo pack("d", $Value);
	return;
}
function xlsWriteLabel($Row, $Col, $Value ) {
	$L = strlen($Value);
	echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
	echo $Value;
	return;
}
function test_var($var,$patt='print'){
	echo "<pre>";
	if($patt == 'print'){
		print_r($var);
	}elseif($patt=='echo'){
		echo $var;
	}elseif($patt=='var_dump'){
		var_dump($var);
	}else{
		var_dump($var);
	}
	echo "</pre>";
}
function send_msg($data,$mobile){
	$URL="http://sms.baihe.com/smssend/sendmsg.jsp";
	$xmlstr =
	'<?xml version="1.0" encoding="utf-8">'.
	"<user>heiyou</user>".
	"<pass>heiyou123</pass>".
	"<msgcontent>$data</msgcontent>".
	"<srctermid>$mobile</srctermid>".
	"<desttermid>$mobile</desttermid>".
	"<feetermid>$mobile</feetermid>".
	"<msgformat></msgformat>".
	"<mttype></mttype>".
	"<spnumber></spnumber>".
	"<linkid></linkid>".
	"</soap:Envelope>\r\n\r\n"
	;
	$dataStr1 = "msg=".$xmlstr;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$URL);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $dataStr1);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	$outcontent1 = curl_exec($ch);
	$outcontent1 = str_replace(array("\r\n","<success>","</success>"),"",$outcontent1);
	curl_close($ch);
	return $outcontent1;
}


function word_filter($context)
{   
	$context=htmlspecialchars(trim($context));
	return$context;
}

function __autoload($className)
{
	if (is_file(BASE_DIR."/libs/" . $className . ".php"))
		require_once(BASE_DIR."/libs/" . $className . ".php");
	elseif (is_file(BASE_DIR."/classes/" . $className . ".php"))
		require_once(BASE_DIR."/classes/" . $className . ".php");
}

//字符截取函数
function globla_cut_str($string, $sublen, $start = 0, $code = 'UTF-8')
{
    if($code == 'UTF-8')
    {
        $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
        preg_match_all($pa, $string, $t_string);

        if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen))."...";
        return join('', array_slice($t_string[0], $start, $sublen));
    }
    else
    {
        $start = $start*2;
        $sublen = $sublen*2;
        $strlen = strlen($string);
        $tmpstr = '';

        for($i=0; $i< $strlen; $i++)
        {
            if($i>=$start && $i< ($start+$sublen))
            {
                if(ord(substr($string, $i, 1))>129)
                {
                    $tmpstr.= substr($string, $i, 2);
                }
                else
                {
                    $tmpstr.= substr($string, $i, 1);
                }
            }
            if(ord(substr($string, $i, 1))>129) $i++;
        }
        if(strlen($tmpstr)< $strlen ) $tmpstr.= "...";
        return $tmpstr;
    }
}

//获取Ip
function get_client_ip()
{
    foreach (array(
                'HTTP_CLIENT_IP',
                'HTTP_X_FORWARDED_FOR',
                'HTTP_X_FORWARDED',
                'HTTP_X_CLUSTER_CLIENT_IP',
                'HTTP_FORWARDED_FOR',
                'HTTP_FORWARDED',
                'REMOTE_ADDR') as $key) {
        if (array_key_exists($key, $_SERVER)) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                //会过滤掉保留地址和私有地址段的IP，例如 127.0.0.1会被过滤
                //也可以修改成正则验证IP
                if ((bool) filter_var($ip, FILTER_VALIDATE_IP,
                                FILTER_FLAG_IPV4 |
                                FILTER_FLAG_NO_PRIV_RANGE |
                                FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
    }
    return null;
}

//根据ip获取所在省份
function get_city_ip($ip='')
{
	if (empty($ip)){
		$getIp=get_client_ip();
	}else{ 
		$getIp=$ip;
	}
	$content = file_get_contents("http://api.map.baidu.com/location/ip?ak=7IZ6fgGEGohCrRKUE9Rj4TSQ&ip={$getIp}&coor=bd09ll");
	$json = json_decode($content);
	$city=$json->{'content'}->{'address'};//按层级关系提取address数据
	return $city;
}

/*
通过原图获得指定的尺寸的缩略图片
 */
function getImages($url, $width=0, $height=0){

    $pos = strrpos($url, '/')+1;
    $prev = substr($url,0,$pos);
    $next = substr($url,$pos);
    $default_url = TP_APP_URL.'Public/Home/images/default.png';
    if(empty($url) || $url == $default_url){
        return $default_url;
    }
    if($width==0 || $height==0){
    	//var_dump(WWW_DIR.TP_APP_URL.'Public/Uploads/'. $url);exit;
        return TP_APP_URL.'Public/Uploads/'. $url;
    }

    return TP_APP_URL.'Public/Uploads/'. $prev.'thumb_'.$width.'_'.$height.'_'.$next;
}

/**
 * 将二维数组转化为特定字符串
 * @param 數組 $arr
 * @param 符號 $symbol1
 * @param 符號 $symbol2
 * @return 字符串 a|b|c|1|2|3|4;e|f|g|5|6|7
 * 	
内容源数据
1|28|0003|GUCCI包包|499.00|399.00|images/goods/28/566fe93cda3f9.jpg|images/goods/28/566fe93cda3f6.jpg|images
/goods/28/566fe93cda3f7.jpg|images/goods/28/566fe93cda3f8.jpg;1|29|0002|浪琴女士表|299.00|199.00|images/goods
/28/566fe93cda3f7.jpg
 */
function arr2str ($arr,$symbol1,$symbol2)
{
	foreach ($arr as $v)
	{
		$v = join("$symbol1",$v); //可以用implode将一维数组转换为用逗号连接的字符串
		$temp[] = $v;
	}
	$t="";
	foreach($temp as $v){
		$t.="".$v.""."$symbol2";
	}
	$t=substr($t,0,-1);
	return $t;
}

/**
 * 危险 HTML代码过滤器
 *
 * @param   string  $html   需要过滤的html代码
 *
 * @return  string
 */
function html_filter($html)
{
	$filter = array(
			"/\s/",
			"/<(\/?)(script|i?frame|style|html|body|title|link|\?|\%)([^>]*?)>/isU",//object|meta|
			"/(<[^>]*)on[a-zA-Z]\s*=([^>]*>)/isU",
	);

	$replace = array(
			" ",
			"&lt;\\1\\2\\3&gt;",
			"\\1\\2",
	);

	$str = preg_replace($filter,$replace,$html);
	return $str;
}

/**
 * 转换健康舒适家居分类名称
 */
function shift_furnish_cat_name($code){
	$ret='';
	switch($code){
		case 1:
			$ret='新风/除霾/除湿/加湿';
			break;
		case 2:
			$ret='净水/软水/超滤/纯水/开水';
			break;
		case 3:
			$ret='制冷/制热';
			break;
		case 4:
			$ret='制冷/采暖';
			break;
		case 5:
			$ret='采暖/循环热水';
			break;
		case 6:
			$ret='采暖/即热式热水';
			break;
		case 7:
			$ret='中央吸尘';
			break;
		case 8:
			$ret='电采暖';
			break;
		case 9:
			$ret='循环热水';
			break;
		case 10:
			$ret='智能控制';
			break;
	}
	return $ret;
}

/**
 * 转换广告页面类型
 */
function shift_ad_type($code){
	$ret='';
	switch($code){
		case 0:
			$ret='暖扬扬导航';
			break;
		case 1:
			$ret='健康舒适家居导航';
			break;
		case 2:
			$ret='欧洲团代购导航';
			break;
		case 3:
			$ret='健康绿色食品导航';
			break;
	}
	return $ret; 
}

/**
 * 转换自定义广告页面位置
 */
function shift_ad_position($position){
	$ret='';
	switch($position){
		case 0:
			$ret='位置一';
			break;
		case 1:
			$ret='位置二';
			break;
		case 2:
			$ret='位置三';
			break;
		case 3:
			$ret='位置四';
			break;
		case 4:
			$ret='位置五';
			break;
		case 5:
			$ret='位置六';
			break;
		case 6:
			$ret='位置七';
			break;
		case 7:
			$ret='位置八';
			break;
		case 8:
			$ret='位置九';
			break;
		case 9:
			$ret='位置十';
			break;
		case 10:
			$ret='底部广告';
			break;
	}
	return $ret;
}

/**
 * 操作成功或失败跳转页面
 * 
 */
function jump($status,$message,$url,$os=array('name'=>null,'phone'=>null,'os'=>'2')){
	$heredoc = <<<END
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>跳转页面</title>
</head>
<body>
	<div style="width:700px;margin:0 auto;margin-top:50px;">
		<div style="150px;float:left;">
			<img src="images/$status.png" style="width:150px;"/>
		</div>
		<div style="width:530px;float:left;margin-left:20px;margin-top:10px;">
			<div style="width:550px;">
				<span style="font-size:22px;font-weight:bold;color:#2b2b2b;">操作提示</span>
			</div>
			<div style="width:550px;margin-top:5px;">
				<span style="line-height:40px;height:40px;font-size:15px;color:#717678;">$message</span>
			</div>
			<div style="background:#eb5409;border:1px solid #eb5409;border-radius:3px;width:100px;height:25px;line-height:25px;text-align:center;margin-top:30px;">
				<a href="$url" style="color:#fff;font-size:18px;display:bold;text-decoration:none;">跳转...</a>
			</div>
			<div style="clear:both;">
		</div>
		<div style="clear:both;"></div>
		</div>
	</div>
	<input type="hidden" id='os' value="{$os['os']}"/>
	<input type="hidden" id='userName' value="{$os['name']}"/>
	<input type="hidden" id='photoNo' value="{$os['phone']}"/>
	<input type="hidden" id='email' value=""/>

    <script language=javascript>
		var os = document.getElementById('os').value;
    	if (os==1) {
    		var userName = document.getElementById('userName').value;
			var photoNo = document.getElementById('photoNo').value;
			var email = document.getElementById('email').value;

    		_ozprm="userName="+userName+"&photoNo="+photoNo+"&email="+email;
    	}
    </script>
 	<script type="text/javascript" src="js/o_code.js"></script>

 	<script language=javascript>
    	setTimeout("window.location='$url'",3000)
    </script>
</body>
</html>
    		
	
END;
	return $heredoc;
}

/**
 *根据class_id判断表名 
 *
 */
function judge_table($class_id){
	switch ($class_id) {
		case 1 :
			return 'xgj_furnish_goods';
			break;
		case 2 :
			return 'xgj_eu_goods';
			break;
		case 3 :
			return 'xgj_greenfood_goods';
			break;

	}
}
/**
 *健康舒适家居服务商订单结算状态转换
 *
 */
function dealer_order_pay_status($order_pay_status){
	switch ($order_pay_status) {
		case 0 :
			return '未结算';
			break;
// 		case 1 :
// 			return '部分结算';
// 			break;
// 		case 2 :
// 			return '已结算';
// 			break;
		case 1 :
			return '一次性结算';
			break;
		case 2 :
			return '第一笔结算';
			break;
		case 3 :
			return '第二笔结算';
			break;

	}
}

/**
 *健康舒适家居服务商订单进度状态转换
 *
 */
function dealer_order_schedule_status($order_schedule_status){
	switch ($order_schedule_status) {
		case 1 :
			return '未设计';
			break;
		case 2 :
			return '已设计';
			break;
		case 3 :
			return '辅材施工中';
			break;
		case 4 :
			return '辅材完工';
			break;
		case 5 :
			return '主设备施工中';
			break;
		case 6 :
			return '主设备完工';
			break;
		case 7 :
			return '订单已完成';
			break;
	}
}

/**
 *健康舒适家居服务商订单状态转换
 *
 */
function dealer_order_state($order_state){
	switch ($order_state) {
		case 0 :
			return '<font color="red">new</font>';
			break;
		case 1 :
			return '无';
			break;
		/* case 3 :
			return 'xgj_greenfood_goods';
			break;
		case 5 :
			return 'new';
			break; */
	}
}

/**
 *健康舒适家居服务商订单详情表结算状态转换
 *
 */
function dealer_order_deatil_state($state){
	switch ($state) {
		case 0 :
			return '未付款';
			break;
		case 1 :
			return '一次性付款';
			break;
		case 2 :
			return '第一笔付款';
			break;
		case 3 :
			return '第二笔付款';
			break;
		case 4 :
			return '第三笔付款';
			break;
		case 5 :
			return '结算完毕';
			break;
	}
}

/**
 *健康舒适家居服务商订单详情进度状态转换
 *
 */
function dealer_order_detail_quote_status($order_detail_quote_status){
	switch ($order_detail_quote_status) {
		case 1 :
			return '未设计';
			break;
		case 2 :
			return '已设计';
			break;
		case 3 :
			return '辅材未施工';
			break;
		case 4 :
			return '辅材完工';
			break;
		case 5 :
			return '主设备未施工';
			break;
		case 6 :
			return '主设备完工';
			break;
		case 41 :
			return '第一笔已清算';
			break;
		case 61 :
			return '全部清算结束';
			break;
	}
}

/**
 * 健康舒适家居财务结算状态转换
 * @param unknown $finance_status
 * @return string
 */
function dealer_finance_status($finance_status){
	switch ($finance_status){
		case 1:
			return '申请中';
			break;
		case 2:
			return '未结算';
			break;
		case 3:
			return '已结算';
			break;
	}
}

/**
 * 健康舒适家居财务管理行为状态转换
 * @param unknown $finance_status
 * @return string
 */
function dealer_finance_action_status($finance_status){
	switch ($finance_status){
		case 1:
			return '<font color="red">new</font>';
			break;
		case 2:
			return '<font color="red">new</font>';
			break;
		case 3:
			return '无';
			break;
	}
}
/**
 * 工作计划任务
 * @param unknown $task_work
 * @return string
 */
function dealer_order_task_work($task_work){
	switch ($task_work) {
		case 1 :
			return '上门测量';
			break;
		case 2 :
			return '深度设计';
			break;
		case 3 :
			return '辅材施工';
			break;
		case 4 :
			return '主设备安装';
			break;
	}
}
/**
 * 将字符串装换为几室几厅
 * @param unknown $str
 * @return string
 */
function house_layout($str){
	$arr=explode(',', $str);
	$count=count($arr);
	switch ($count){
		case 1 :
			return $arr[0].'室';
			break;
		case 2 :
			return $arr[0].'室'.$arr[1].'厅';
			break;
		case 3 :
			return $arr[0].'室'.$arr[1].'厅'.$arr[2].'厨';
			break;
		case 4 :
			return $arr[0].'室'.$arr[1].'厅'.$arr[2].'厨'.$arr[3].'卫';
			break;
		case 5 :
			return $arr[0].'室'.$arr[1].'厅'.$arr[2].'厨'.$arr[3].'卫'.$arr[4].'阳台';
			break;
	}
}

/**
 * 将字符串转换为卧室：[][][]客厅[][]
 * @param unknown $aa
 * @return string
 */
function type_area($aa){
	$arr=explode('|', $aa);
	$arr1=explode(',', $arr[0]);
	$bb1='卧室：';
	foreach ($arr1 as $v){
		$bb1.="[<a style='color:red'>".$v.'</a>m²]';
	}
	$arr2=explode(',', $arr[1]);
	$bb2='客厅：';
	foreach ($arr2 as $v){
		$bb2.="[<a style='color:red'>".$v.'</a>m²]';
	}
	$arr3=explode(',', $arr[2]);
	$bb3='厨房：';
	foreach ($arr3 as $v){
		$bb3.="[<a style='color:red'>".$v.'</a>m²]';
	}
	$arr4=explode(',', $arr[3]);
	$bb4='卫生间：';
	foreach ($arr4 as $v){
		$bb4.="[<a style='color:red'>".$v.'</a>m²]';
	}
	$arr5=explode(',', $arr[4]);
	$bb5='阳台：';
	foreach ($arr5 as $v){
		$bb5.="[<a style='color:red'>".$v.'</a>m²]';
	}
	$bb=$bb1.$bb2.$bb3.$bb4.$bb5;
	return $bb;
}

function uploadOne($name, $dir, $thumb=array(),$exts='IMG_exts'){
    if(isset($_FILES[$name]) && $_FILES[$name]['error']==0){
        $up = new UploadFile();
        $up->maxSize = IMG_maxSize ;
        $up->exts = $exts;
        $up->rootPath = IMG_rootPath;
        $up->savePath = $dir . '/';
        $info = $up->upload();
        if(!$info) {
            $ret['code'] = 0;
            $ret['error'] = $up->getErrorMsg();
        }else{
            // 上传成功
            $ret['code'] = 1;
            //原图地址
            $ret['images'] = $info[$name]['savepath'] . $info[$name]['savename'];
            if($thumb){
                //生成缩略图
                //$img = new \Think\Image();
                foreach($thumb as $k=>$v){
                    if(!file_exists($up->rootPath . $ret['images'])) break;

                    $img->open($up->rootPath . $ret['images']);

                    $fileName = $info[$name]['savepath'].'thumb_'.$v[0].'_'.$v[1].'_'.$info[$name]['savename'];
                    $img->thumb($v[0], $v[1])->save($up->rootPath . $fileName);
                }
            }
        }
        return $ret;
    }
    return array(
            'code' => 0,
            'error' => '图片name值错误',
        );
}

function upload($name, $dir, $thumb=array(),$exts=array(IMG_exts)){
    if(isset($_FILES[$name]) && $_FILES[$name]['error']==0){
        $up = new UploadFile();
        $up->maxSize = IMG_maxSize*1024*1024 ;
        $up->allowType = $exts;
        $up->savePath = IMG_rootPath.$dir. '/';
        // 使用对上传图片进行缩略图处理
		
		if(!empty($thumb)){
			$up->thumb=true;
			$width='';
			$height='';
			$tbname='';
			foreach ($thumb as $k => $v) {
				$width.=$v[0].',';
				$height.=$v[1].',';
				$tbname.='thumb_'.$v[0].'_'.$v[1].'_,';
			}	
			// 缩略图最大宽度
		$up->thumbMaxWidth=rtrim($width,',');
		// 缩略图最大高度
		$up->thumbMaxHeight=rtrim($height,',');
		// 缩略图前缀
		$up->thumbPrefix=rtrim($tbname,',');
		// 缩略图保存路径
		$up->thumbPath=$up->savePath. date('Y-m-d',time()).'/';
		}
		
        $rs = $up->upload($name);
        if(!$rs) {
            $ret['code'] = 0;
            $ret['error'] = $up->getErrorMsg();
        }else{
        	$info=$up->getUploadFileInfo();
            // 上传成功
            $ret['code'] = 1;
            //原图地址
            $ret['images'] = $dir.'/'. $info[$name]['savename'];
        }
        return $ret;
    }
    return array(
            'code' => 0,
            'error' => '图片name值错误',
        );
}



function down($path,$file_name){
	header("Content-type:text/html;charset=utf-8");
	//用以解决中文不能显示出来的问题
	$file_name=iconv("utf-8","gb2312",$file_name);
	$file_sub_path=$path;
	$file_path=$file_sub_path.$file_name;
	//var_dump($file_path);exit;
	//首先要判断给定的文件存在与否
	if(!file_exists($file_path)){
		echo "没有该文件文件";
		echo "<script>history.go(-1)</script>";
		return ;
	}
	$fp=fopen($file_path,"r");
	$file_size=filesize($file_path);
	//下载文件需要用到的头
	//返回的文件
	header("Content-type:application/octet-stream");
	//按照字节大小返回
	header("Accept-Ranges:bytes");
	//返回文件大小
	header("Accept-Length:$file_size");
	//这里客户端的弹出对话框
	header("Content-Disposition:attachment;filename=".$file_name);
	//向客户端回送数据
	$buffer=1024;
	$file_count=0;
	//向浏览器返回数据
	while(!feof($fp) && $file_count<$file_size){
		$file_con=fread($fp,$buffer);
		$file_count+=$buffer;
		echo $file_con;
	}
	fclose($fp);
}

function dl_file($file){
	//这是下载文件的函数,$file是文件路径.
	//First, see if the file exists
	if (!is_file($file)) { die("<b>404 File not found!</b>"); }

	//Gather relevent info about file
	$len = filesize($file);
	$filename = basename($file);
	$file_extension = strtolower(substr(strrchr($filename,"."),1));

	//This will set the Content-Type to the appropriate setting for the file
	switch( $file_extension ) {
		case "pdf": $ctype="application/pdf"; break;
		case "exe": $ctype="application/octet-stream"; break;
		case "zip": $ctype="application/zip"; break;
		case "doc": $ctype="application/msword"; break;
		case "xls": $ctype="application/vnd.ms-excel"; break;
		case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
		case "gif": $ctype="image/gif"; break;
		case "png": $ctype="image/png"; break;
		case "jpeg":
		case "jpg": $ctype="image/jpg"; break;
		case "mp3": $ctype="audio/mpeg"; break;
		case "wav": $ctype="audio/x-wav"; break;
		case "mpeg":
		case "mpg":
		case "mpe": $ctype="video/mpeg"; break;
		case "mov": $ctype="video/quicktime"; break;
		case "avi": $ctype="video/x-msvideo"; break;

		//The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
		case "php":
		case "htm":
		case "html":
		case "txt": die("<b>Cannot be used for ". $file_extension ." files!</b>"); break;

		default: $ctype="application/force-download";
	}

	//Begin writing headers
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-rev alidate, post-check=0, pre-check=0");
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	 
	//Use the switch-generated Content-Type
	header("Content-Type: $ctype");

	//Force the download
	$header="Content-Disposition: attachment; filename=".$filename.";";
	header($header);
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".$len);
	@readfile($file);
	exit;
}



/**
 * ...废除----勿删
 */
function ccc_aa(){
	if(isset($_COOKIE['pre_order'])){
		$array=explode(";", $_COOKIE['pre_order']);
		foreach ($array as $v){
			$pre_order[] = explode("|", $v);
		}
		//var_dump($pre_order);exit;
		echo	$heredoc =
		'<div class="collect-tip" id="collect-tip" >
    <div class="collect-tip-bk">
        <div class="collect-tip-titile">
        	我的健康舒适家
        </div>
	
        <div class="collect-tip-shopping">
        	<div class="collect-tip-shopping-title">
            	预购总汇
            </div>
	
            <div class="collect-tip-shopping-table">
            	<div class="collect-tip-shopping-table-title">
                	<div class="collect-tip-shopping-table-title-01">
                    	系统名称
                    </div>
	
                    <div class="collect-tip-shopping-table-title-02">
                    	总价（单位：元）
                    </div>
	
                    <div class="collect-tip-shopping-table-title-03">
                    	抵扣券（单位：元）
                    </div>
	
                    <div class="collect-tip-shopping-table-title-04">
                    	操作
                    </div>
	
                    <div class="clear"></div>
                </div>
	
                <div class="clear"></div>';
		foreach($pre_order as $key=>$v){
			echo  $heredoc.=" <div class='collect-tip-shopping-table-list'>
			<div class='collect-tip-shopping-table-list-01'>
			{$v[3]}
			</div>
	
			<div class='collect-tip-shopping-table-list-02'>
			{$v[2]}元
			</div>
	
			<div class='collect-tip-shopping-table-list-03'>
			2000
			</div>
	
			<div class='collect-tip-shopping-table-list-04'>
			<a href='?del&key={$key}&q_id={$v[1]}'>
			移除
			</a>
			</div>
	
			<div class='clear'></div>
			</div>
	
			<div class='clear'></div>";
		}
	
		echo $heredoc.='   <div class="collect-tip-shopping-table-collect">
                	<div class="collect-tip-shopping-table-collect-01">
                    	总计
                    </div>
	
                    <div class="collect-tip-shopping-table-collect-02">
                    	12000
                    </div>
	
                    <div class="collect-tip-shopping-table-collect-03">
                    	4000
                    </div>
                </div>
            </div>
	
            <div class="collect-tip-shopping-buy">
            	<a href="#">
                	放进购物车
                </a>
            </div>
        </div>
       <div class="clear2"></div>
    </div>
</div>';
	}
}
?>