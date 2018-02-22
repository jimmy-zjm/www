<?php 

//查询id是否正确，存在返回true
function quoteId($id){
    $array = [5,15,19,23,31,32,36];
    if (in_array($id, $array)) return true;
}

//查询数组内是否存在空，存在返回true
function array_null($array){
	foreach ($array as $k => $v) {
		if ($v<=0 || empty($v)) return true;
	}
}

//拆分房屋信息
function houseInfo($data){
	
	$house['province']    = $data['province'];
	$house['city']        = $data['city'];
	$house['district']    = $data['district'];
	$house['address']     = $data['address'];
	$house['type']        = $data['type'];
	$house['layout']      = explode(',', $data['layout']);
	$house['address']  	  = $data['address'];
    $house['total_area']  = $data['total_area'];
	$house['people']      = $data['people'];

	$area = explode('|', $data['area']);
	foreach ($area as $k => $v) {
		$house['area'][]  = explode(',', $v);
	}
	
	return $house;
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
//获取短信模板（短信接口）
function getMessagetem(){
    header("Content-Type:text/html; charset=utf-8");
    $post_data['action']   = 'getSmsTemplate';
    $post_data['account']   = 'N00000004065';
    $post_data['password']  = 'NgnYM9nE3W8OTeka';
    $post_data['md5']   = '81f0e1f0-32df-11e3-a2e6-1d21429e5f46&var1=XXX';
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
    $post_data['password']  = 'NgnYM9nE3W8OTeka';
    $post_data['num']   = $tel;
    $post_data['templateNum']   = $arr['data'][0]['num'];
    $post_data['md5']   = '81f0e1f0-32df-11e3-a2e6-1d21429e5f46';
    //$post_data['p2']  = '3';
    $str=mt_rand(1000,9999);
    $_SESSION['msg']    =$str;
    $_SESSION['tel']    =$tel;
    $_SESSION['msgTime']=time();
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


/**
 *  导出excel表格
 *  $list['data']  要导出的数据，数据为索引数组
 *  例: $list['data']['0'] = array(a,b,c,d);
 *  $list['key']   列标题，数据为索引数组
 *  例: $list['key'] = array('优惠券号', '优惠券密码', '优惠券金额', '开始时间');
 *  $list['width'] 设置列宽，数据为关联数组
 *  例: $list['width'] = array('B'=>'15','C'=>'15','D'=>'15','E'=>'20');
 *
 *  $name 导出的excel表格的名称
 **/
function exl($list,$name=null){
    $charactors = array('B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA');
    //导入PHPExcel类库
    //相当于引入了vendor目录下面PHPExcel\PHPExcel.php
    vendor('Excel.PHPExcel');
    $objPHPExcel = new \PHPExcel();
    
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', $name)
        ->setCellValue('A2', 'ID');

    if (!empty($list['key'])) {
        for ($i=0; $i < count($list['key']) ; $i++) { 
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($charactors[$i].'2', $list['key'][$i]);
        }
    }

    if (!empty($list['width'])) {
        foreach ($list['width'] as $k => $v) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($k)->setWidth($v);//设置列宽
        }
    }

    foreach($list['data'] as $k=>$v){
        $k = $k+3;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A".$k, $k-2);
        for ($i=0; $i <count($v) ; $i++) { 
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($charactors[$i].$k, $v[$i]);
        }
    }

    $objPHPExcel->getActiveSheet()->setTitle("$name"); 
    $objPHPExcel->setActiveSheetIndex(0);   
    ob_end_clean() ;
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename='.$name.'列表.xls');
    header('Cache-Control: max-age=0');
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
}

//获取省市县中的省份
function getPCD(){
    $area = M('xgj_area')->where('pid=100000')->field('id,name')->select();
    return $area;
}

//根据id获取省市县名称
function getPCDName($id){
    $area = M('xgj_area')->where("id=$id")->getField('name');
    return $area;
}
/**
 * 上传单张图片并生成缩略图
	$num 为空 单张上传
	$num=2 多张上传
 *   $info = uploadOne('logo', 'Brand', array(
 *              array(50,200),
 *         ));
 *    if($info['code'] == 1){
 *         //上传图片成功
 *         $data['logo'] = $info['images'];
 *         return true;
 *     }
 *     $this->error = $info['error'];
 *    return false;
 */
function uploadOne($name, $dir, $thumb=array(),$exts='IMG_exts',$num=''){
	//var_dump(  $name );die();
    if(isset($_FILES[$name])){// && $_FILES[$name]['error']==0
        $up = new \Think\Upload();
        $up->maxSize = intval(C('IMG_fileSize'))*1024*1024 ;
        $up->exts = C($exts);
        $up->rootPath = C('IMG_rootPath');
        $up->savePath = $dir . '/';
        $info = $up->upload();
        if(!$info) {
            $ret['code'] = 0;
            $ret['error'] = $up->getError();
        }else{
            // 上传成功
            $ret['code'] = 1;
            //原图地址
			if(empty($num)){
				 $ret['images'] = $info[$name]['savepath'] . $info[$name]['savename'];
			}else{
				foreach($info as $k=>$file){
						$ret['images'][$k]= $file['savepath'].$file['savename'];
				}
			}
            if($thumb){
                //生成缩略图
                $img = new \Think\Image();
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
            'error' => 'name值错误',
        );
}

//通过原图获得指定的尺寸的缩略图片
function getImage($url, $width=0, $height=0){
    $pos = strrpos($url, '/')+1;
    $prev = substr($url,0,$pos);
    $next = substr($url,$pos);
    $default_url = __ROOT__.'/Public/img/default.png';
    if(empty($url) || $url == $default_url){
        return $default_url;
    }
    if($width==0 || $height==0){		
		return C('TP_APP_URL').'/Public/Uploads/'. $url;
    }
	return C('TP_APP_URL').'/Public/Uploads/'. $prev.'thumb_'.$width.'_'.$height.'_'.$next;
}

/**
 * 删除图片,删除原图 和 缩略图片
 * deleteImage(数据库中的图片url, array(
 *     array(宽度, 高度)
 * ))
 */
function deleteImage($img, $thumb=array()){
    $file = C('IMG_rootPath') . $img;
    // echo($file);die;
    @unlink($file);
    if($thumb){
        //取得原图的名字  Brand/2015-12-23/567a0ec7511db.jpg
        $fileName = ltrim(strrchr($img, '/'),'/');
        $dirName = explode($fileName, $img);
        $dirName = rtrim($dirName[0],'/');
        //循环删除缩略图片
        foreach($thumb as $v){
            $file = C('IMG_rootPath')  .$dirName. '/thumb_'.$v[0].'_'.$v[1].'_'.$fileName;
            @unlink($file);
        }
    }
}

/**
 * 获取分页数据
 * @param  integer $total    总记录数
 * @param  integer $pageSize 每页显示数量
 * @return array           返回的分页数据
 */
function getPage($total, $pageSize,$tab=array()){
    $page = new \Think\Page($total, $pageSize, $tab);
    $page->setConfig('theme', '<p>共 %TOTAL_ROW% 条记录 | 当前第%NOW_PAGE%/%TOTAL_PAGE%页</p> %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
    // $page->setConfig('header','共 %TOTAL_ROW% 条记录');
    $page->setConfig('prev','[上一页]');
    $page->setConfig('next','[下一页]');
    $page->setConfig('first','[首页]');
    $page->setConfig('last','[尾页]');
    //$page->p = $pag;
    return array(
        'page'  => $page->show(),
        'limit' => $page->firstRow.','.$page->listRows,
    );
}

/**
 * 获取Ajax分页数据
 * @param  integer $total    总记录数
 * @param  integer $pageSize 每页显示数量
 * @return array           返回的分页数据
 */
function getAjaxPage($total, $pageSize,$tab=array()){
    $page = new \Think\PageAjax($total, $pageSize, $tab);
    $page->setConfig('header','<p>共 %TOTAL_ROW% 条记录 | 当前第%NOW_PAGE%/%TOTAL_PAGE%页</p>');
    $page->setConfig('prev','[上一页]');
    $page->setConfig('next','[下一页]');
    $page->setConfig('first','[首页]');
    $page->setConfig('last','[尾页]');
    $page->setConfig('theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
    return array(
        'page'  => $page->show(),
        'limit' => $page->firstRow.','.$page->listRows,
    );
}


/*下载文件*/
//$type为空下载后台目录下文件
function down($path,$file_name,$type=''){
   
    //用以解决中文不能显示出来的问题
    $file_name=iconv("utf-8","gb2312",$file_name);
	if(empty($type)) 
		$rootpath="/xgjtp";
	else 
		$rootpath="/spss";
    $file_sub_path=$_SERVER['DOCUMENT_ROOT'].$rootpath.$path;//$_SERVER['DOCUMENT_ROOT'].$path;
    $file_path=$file_sub_path.$file_name;
	//  var_dump($file_path);exit;
    //首先要判断给定的文件存在与否
    //if(!file_exists($file_path))     此判断须用$_SERVER['DOCUMENT_ROOT']实际路径来配合使用  
      //  return 2;    

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
