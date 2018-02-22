<?php
/**
 * 公共函数库
 * @date 2016-3-9
 * @author grass <14712905@qq.com>
 * 2016-3-10
 *     增加C方法
 * 2016-3-11
 *     增加自动加载 ,自动加载自定义模型
 *     增加api方法,用于输出接口数据
 *     增加processPhone方法, 用于处理手机号码加密显示
 *     修改I函数, 考虑数组接受方式
 *     修改M,D函数的 多模型问题
 * 2016-3-14
 *     增加E()方法, 触发异常
 * 2016-3-17
 *     修改api()方法, 增加成员redirect_url
 * 2016-4-7
 *     添加session()方法
 */


/*
自动加载
 */
spl_autoload_register(function($className){
    $classFile = '';
    if(substr($className,-5)== 'Model'){
        $classFile = WWW_DIR . '/model/'.$className . '.class.php';

    }elseif(substr($className,-10)=='Controller'){
        $classFile = WWW_DIR . '/classes/'.$className . '.class.php';
    }

    if(empty($classFile)) return;

    if(file_exists($classFile)){
        require $classFile;
    }else{
        if(C('APP_DEBUG')){
            die('自动加载: 类文件不存在: '.$classFile);
        }
    }
});


/**
 * 接收GET或者POST数据,GET优先,也可以通过get.name或者post.name接收
 * @author  grass <14712905@qq.com>
 * @param string $name 要取的name值
 * @param bool [$isInt=false] 接收的数据类型是否为整型
 * @param mixed [$default=''] 没有接收到值的时候返回的默认值
 * @return mixed  name值不存在返回false,否则返回其值
 */
function I($name,$isInt=false,$default=''){
    //尝试接收get
    $value = isset($_GET[$name])?$_GET[$name]:false;
    if($value===false){
        //没有get,尝试接收post
        $value = isset($_POST[$name])?$_POST[$name]:false;
        if($value===false){
            //没有get,post,尝试接收对象方式
            if(strcasecmp(substr($name,0,4),'get.')===0){
                //对象方式get
                $name  = substr($name,4);
                $value = isset($_GET[$name])?$_GET[$name]:false;
            }elseif(strcasecmp(substr($name,0,5),'post.')===0){
                //对象方式get
                $name  = substr($name,5);
                $value = isset($_POST[$name])?$_POST[$name]:false;
            }else{
                return $default;
            }
        }
    }
    if($isInt){
        return intval($value);
    }else{
        $filter_str = C('DEFAULT_FILTER');
        $filter = explode(',', $filter_str);
        foreach ($filter as $f) {
            $func = trim($f);
            if(function_exists($func)){
                $value = is_array($value)?array_map_recursive($func,$value):$func($value);
            }
        }
        return $value;
    }
}

function array_map_recursive($filter, $data) {
    $result = array();
    foreach ($data as $key => $val) {
        $result[$key] = is_array($val)
         ? array_map_recursive($filter, $val)
         : call_user_func($filter, $val);
    }
    return $result;
 }


/**
 * 生成json接口数据
 * @param  integer $code    代码
 * @param  string  $message 信息
 * @param  string  $data    数据
 */
function api($code=0,$message='',$data='',$redirect_url=''){
    $arr = array(
            'code'         => $code,
            'message'      => $message,
            'data'         => $data,
            'redirect_url' => $redirect_url,
        );
    echo json_encode($arr);
    die;
}

/*
实例化基础模型
 */
function M($tableName=''){
    static $model = array();
    if(!isset($model[$tableName])){
        $model[$tableName] = new Model($tableName);
    }
    return $model[$tableName];
}

/*
实例化自定义模型
 */
function D($modelName){
    static $model = array();
    $modelName .=  'Model';
    if(!isset($model[$modelName])){
        $model[$modelName] = new $modelName;
    }
    return $model[$modelName];
}



/*
读取配置
 */
function C($key){
    if(isset($GLOBALS['conf'][$key])){
        return $GLOBALS['conf'][$key];
    }else{
        return '';
    }
}

/*
触发异常
 */
function E($msg, $code=0) {
    throw new \Exception($msg, $code);
}

/*
读取cookie数据
 */
function cookie($key){
    return isset($_COOKIE[$key])?$_COOKIE[$key]:'';
}

/*
读取session数据
 */
function session($key){
    return isset($_SESSION[$key])?$_SESSION[$key]:'';
}


/*
处理手机号码,把中间4为显示为*号
 */
function processPhone($t){
    return substr($t,0,3).str_repeat('*',strlen($t)-7).substr($t,strlen($t)-7,4);
}

/*
处理名字,只显示第一个和最后一个字符,其他的显示为*号
 */
function processName($t){
    if(preg_match('/^\w$/', substr($t,-1))){
        $b = substr($t,-1);
    }else{
        $b = substr($t,-3);
    }
    return msubstr($t,0,1).'***'.$b;
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=false) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}

/*
通过原图获得指定的尺寸的缩略图片
 */
function getImage($url, $width=0, $height=0){
    $pos = strrpos($url, '/')+1;
    $prev = substr($url,0,$pos);
    $next = substr($url,$pos);
    $default_url = C('TP_APP_URL').'Public/Home/images/default.png';
    if(empty($url) || $url == $default_url){
        return $default_url;
    }
    if($width==0 || $height==0){
        return C('TP_APP_URL').'Public/Uploads/'. $url;
    }
    return C('TP_APP_URL').'Public/Uploads/'. $prev.'thumb_'.$width.'_'.$height.'_'.$next;
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


/**
 *健康舒适家居服务商订单结算状态转换
 *
 */
function switch_money(){
	/*雅虎汇率转换*/
$file = fopen('http://download.finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s=EURCNY=x','r'); //参数s=EURCNY, 欧元换人民币, 根据需要更换

while ($data = fgetcsv($file)) //读取CSV文件里的每一行内容
{
    $goods_list[] = $data;
}
 //$shijian = $goods_list[0][2] .' '. $goods_list[0][3];根据 quotes.csv文件得到的数据, 当前时间 在3,4列位置
$huilv = !empty($goods_list[0][1])?$goods_list[0][1]:7.6; //根据 quotes.csv文件得到的数据, 汇率 在2列位置

fclose($file);
return $huilv;

   /*百度汇率转换代码 
   $ch = curl_init();
    $url = 'http://apis.baidu.com/apistore/currencyservice/currency?fromCurrency=EUR&toCurrency=CNY';
    $header = array(
        'apikey: 942f71d6e29e9443300e22f042ffebbe',
    );
    // 添加apikey到header
    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch , CURLOPT_URL , $url);
    $res = curl_exec($ch);
    //echo "<pre>";
    $price=json_decode($res, true);
	$price['retData']['currency']=!empty($price['retData']['currency'])?$price['retData']['currency']:8.01;

    return $price['retData']['currency'];*/
}


