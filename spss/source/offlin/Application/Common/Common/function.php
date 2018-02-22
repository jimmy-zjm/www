<?php 

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



//获取短信模板（短信接口）
/*function getMessagetem(){
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
}*/

//获取短信验证码
function getMessage($tel){
    $re = M('xgj_base')->where(['type'=>'msg'])->find();
    if ($re['other'] >= $re['content']) die('2');

    //$arr=getMessagetem();
    header("Content-Type:text/html; charset=utf-8");
    $post_data['action']   = 'sendInterfaceTemplateSms';
    //$post_data['action']   = 'interfaceSms';
    $post_data['account']   = 'N00000004065';
    $post_data['password']  = 'NgnYM9nE3W8OTeka';
    $post_data['num']   = $tel;
    $post_data['templateNum']   = '2';//$arr['data'][0]['num'];
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

    if($rest['success']===true){
        $time   = date('Y-m-d');
        $reTime = date('Y-m-d', $re['time']);

        if ($time == $reTime) {
            $data['other'] = $re['other']+1;
        }else{
            $data['other'] = 1;
        }
        
        $data['time']  = time();
        M('xgj_base')->where(['type'=>'msg'])->save($data);
    }

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
	$num=2 多张上传 多张上传无效
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

				 //等比例压缩图片开始
				$image = new \Think\Image(); 
				//var_dump($up->rootPath.$ret['images']);die();
				$image->open($up->rootPath.$ret['images']);
				$width = $image->width(); // 返回图片的宽度
				$thumb_w=1200;
				if($width > $thumb_w){
					$width = $width/$thumb_w; //取得图片的长宽比
					$height = $image->height();
					$thumb_h = ceil($height/$width);
				}

				//如果文件路径不存在则创建
				//$save_path_info = pathinfo($save_path);			
				//if(!is_dir($save_path_info['dirname'])) mkdir ($save_path_info['dirname'], 0777);
				$image->thumb($thumb_w, $thumb_h)->save($up->rootPath.$ret['images']);
				//if($is_del) @unlink($img_path);
				 //等比例压缩图片结束
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
    $default_url = __ROOT__.'/Public/Dealer/img/default.png';
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



function getQuote($post_data){
    $url = C(SPSS_URL).'/Furnish/getQuote';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // post数据
    curl_setopt($ch, CURLOPT_POST, 1);
    // post的变量
    curl_setopt($ch, CURLOPT_POSTFIELDS, ['data'=>json_encode($post_data)]);
    $output = curl_exec($ch);
    curl_close($ch);
    //打印获得的数据
    return json_decode($output,true);
}

/**
 * 获取某个商品分类的 儿子 孙子  重子重孙 的 id
 * @param type $cat_id
 */
function getCatGrandson ($cat_id,$tableName='xgj_ov_category',$where='')
{
	$result = M($tableName)->where("pid = $cat_id $where")->getField('id',true);
	
	if(!empty($result)){
		
		foreach($result as $key=>$val){			
			$GLOBALS['data'][]=$val;
			getCatGrandson($val,$tableName,$where);
		}		
		
	}
	return $GLOBALS['data'];
    /*$GLOBALS['catGrandson'] = array();
    $GLOBALS['category_id_arr'] = array();
    // 先把自己的id 保存起来
    $GLOBALS['catGrandson'][] = $cat_id;
    // 把整张表找出来
    $GLOBALS['category_id_arr'] = M($tableName)->getField('id,pid');
    // 先把所有儿子找出来
    $son_id_arr = M($tableName)->where("pid = $cat_id $where")->getField('id',true);
    foreach($son_id_arr as $k => $v)
    {
        getCatGrandson2($v);
    }
    return $GLOBALS['catGrandson'];*/
}


/**
 * 递归调用找到 重子重孙
 * @param type $cat_id
 */
function getCatGrandson2($cat_id)
{
    $GLOBALS['catGrandson'][] = $cat_id;
    foreach($GLOBALS['category_id_arr'] as $k => $v)
    {
        // 找到孙子
        if($v == $cat_id)
        {
            getCatGrandson2($k); // 继续找孙子
        }
    }
}


function isPhone() {
  // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
  if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
    return true;
  }
  //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
  if (isset($_SERVER['HTTP_VIA'])) {
    //找不到为flase,否则为true
    if(stristr($_SERVER['HTTP_VIA'], "wap"))
    {
      return true;
    }
  }
  //脑残法，判断手机发送的客户端标志,兼容性有待提高
  if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $clientkeywords = array (
      'nokia',
      'sony',
      'ericsson',
      'mot',
      'samsung',
      'htc',
      'sgh',
      'lg',
      'sharp',
      'sie-',
      'philips',
      'panasonic',
      'alcatel',
      'lenovo',
      'iphone',
      'ipod',
      'blackberry',
      'meizu',
      'android',
      'netfront',
      'symbian',
      'ucweb',
      'windowsce',
      'palm',
      'operamini',
      'operamobi',
      'openwave',
      'nexusone',
      'cldc',
      'midp',
      'wap',
      'mobile',
      'phone',
    );
    // 从HTTP_USER_AGENT中查找手机浏览器的关键字
    if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
      return true;
    }
  }
  //协议法，因为有可能不准确，放到最后判断
  if (isset($_SERVER['HTTP_ACCEPT'])) {
    // 如果只支持wml并且不支持html那一定是移动设备
    // 如果支持wml和html但是wml在html之前则是移动设备
    if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
      return true;
    }
  }
  return false;
}