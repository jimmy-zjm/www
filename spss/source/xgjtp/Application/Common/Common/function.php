<?php
/**
 * 项目公共函数库
 */

/**
 * 上传单张图片并生成缩略图
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
function uploadOne($name, $dir, $thumb=array(),$exts='IMG_exts',$path='IMG_rootPath'){
    if(isset($_FILES[$name])){// && $_FILES[$name]['error']==0
        $up = new \Think\Upload();
        $up->maxSize = intval(C('IMG_fileSize'))*1024*1024 ;
        $up->exts = C($exts);
        $up->rootPath = C($path);
        $up->savePath = $dir . '/';
        $info = $up->upload();
        if(!$info) {
            $ret['code'] = 0;
            $ret['error'] = $up->getError();
        }else{
            // 上传成功
            $ret['code'] = 1;
            //原图地址
            $ret['images'] = $info[$name]['savepath'] . $info[$name]['savename'];
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
function uploadOne2($name, $dir, $thumb=array(),$exts='IMG_exts'){
    if(isset($_FILES[$name])){// && $_FILES[$name]['error']==0
        $up = new \Think\Upload();
        $up->maxSize = intval(C('IMG_fileSize'))*1024*1024 ;
        $up->exts = C($exts);
        $up->rootPath = C('IMG_rootPath');
        $up->savePath = $dir . '/';
        $info = $up->uploadOne($_FILES[$name]);
        if(!$info) {
            $ret['code'] = 0;
            $ret['error'] = $up->getError();
        }else{
            $info = array($name=>$info);
            // 上传成功
            $ret['code'] = 1;
            //原图地址
            $ret['images'] = $info[$name]['savepath'] . $info[$name]['savename'];
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
    $default_url = __ROOT__.'/Public/Home/images/default.png';
    if(empty($url) || $url == $default_url){
        return $default_url;
    }
    if($width==0 || $height==0){
        return __ROOT__.'/Public/Uploads/'. $url;
    }
    return __ROOT__.'/Public/Uploads/'. $prev.'thumb_'.$width.'_'.$height.'_'.$next;
}



/**
 * 删除图片,删除原图 和 缩略图片
 * deleteImage(数据库中的图片url, array(
 *     array(宽度, 高度)
 * ))
 */
function deleteImage($img, $thumb=array(),$path='IMG_rootPath'){
    $file = C($path) . $img;
    // echo($file);die;
    @unlink($file);
    if($thumb){
        //取得原图的名字  Brand/2015-12-23/567a0ec7511db.jpg
        $fileName = ltrim(strrchr($img, '/'),'/');
        $dirName = explode($fileName, $img);
        $dirName = rtrim($dirName[0],'/');
        //循环删除缩略图片
        foreach($thumb as $v){
            $file = C($path)  .$dirName. '/thumb_'.$v[0].'_'.$v[1].'_'.$fileName;
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
function down($path,$file_name){
    //var_dump($_SERVER['DOCUMENT_ROOT']);exit;
    header("Content-type:text/html;charset=utf-8");
    //用以解决中文不能显示出来的问题
    $file_name=iconv("utf-8","gb2312",$file_name);
    $file_sub_path=$_SERVER['DOCUMENT_ROOT'].$path;
    $file_path=$file_sub_path.$file_name;
    //var_dump($_SERVER,$file_path);exit;
    //首先要判断给定的文件存在与否
    if(!file_exists($file_path)){
        echo "没有该文件文件";
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

