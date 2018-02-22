<?php
namespace Admin\Controller\Furnish;
use \Admin\Controller\Index\AdminController;

/**
 * 报价清单控制器
 */
class PadQuoteController extends AdminController{

    //报价清单首页
    public function index(){
        // $data = M('xgj_users1')->select();
        // foreach ($data as $key => $value) {
        //     if (!empty($value['reg_time'])) {
        //         $data[$key]['time'] = date('Y-m-d H:i',$value['reg_time']);
        //     }
        // }
        // foreach ($data as $key => $value) {
        //     M('xgj_users1_copy')->add($value);
        // }
        
        // echo '<pre>';
        // var_dump($data);exit;
        $map['is_putaway'] = array('eq','1');
        //分页  
        $data1 = M('xgj_pad_furnish_quote')->where($map)->select();

        $total = count($data1);

        $page  = getPage($total,C('MANAGER_PAGE_SIZE'));

        $data  = M('xgj_pad_furnish_quote')->where($map)->limit($page['limit'])->select();

        //模板传值
        $this->assign("page",$page['page']);

        $this->assign('quote_list',$data);
        //显示模板
        $this->display();
    }

    //修改报价清单
    public function edit(){
        
        $quote_id=I("get.quote_id");

        if (!empty($_GET['tab'])) $this->assign('tab',I("get.tab"));

        $data  = M('xgj_pad_furnish_quote')->where("quote_id=$quote_id")->find();

        $cate = M('xgj_pad_furnish_cat')->select();

        $data['images'] = explode('|', $data['images']);

        if ($data['images']['0'] == '') {
           unset($data['images']['0']);
        }

        if ($_GET['name']) $map['name'] = array('like', '%'.I('get.name').'%');
        if ($_GET['type']) $map['type'] = array('like', '%'.I('get.type').'%');
        $map['quote_id'] = array('eq',$quote_id);

        $explains = M('xgj_pad_quote_explain')->where($map)->order('number asc')->select();
   

        $total = count($explains);

        $page  = getPage($total,C('MANAGER_PAGE_SIZE'));

        $explain  = M('xgj_pad_quote_explain')->where($map)->order('number asc')->limit($page['limit'])->select();
        foreach ($explain as $key => $value) {
            $explain[$key]['explain'] = explode('|', $value['explain']);
            $explain[$key]['type'] = explode('|', $value['type']);
        }
        //模板传值
        $this->assign("page",$page['page']);

        $this->assign('quote',$data);
        $this->assign('explain',$explain);

        $this->assign('cate',$cate);

        $this->display();
    }

    //确认修改报价清单
    public function edit_save(){
        $id                 = I("post.quote_id");
        $quoteName          = I('post.quote_name');                     //更改后的名称
        $quoteNames         = I('post.quote_names');                    //更改前的名称
        $coupon             = I('post.coupon');                         //优惠券比例
        $gift               = I('post.gift');                           //赠品比例
        $sale               = I('post.sale');                           //打折比例
        $summary            = I('post.summary');                        //概述
        $principle          = I('post.principle');                      //原理
        $advantage          = I('post.advantage');                      //产品优势
        $parameter          = I('post.parameter');                      //参数
        $standard           = I('post.standard');                       //施工标准
        $customer           = I('post.customer');                       //售后
        $_POST['starttime'] = strtotime(I('post.starttime'));           //上架时间
        $_POST['endtime']   = strtotime(I('post.endtime'));             //下架时间    

    
        if (empty($summary))   $this->error('请填写概述',U('edit',array('quote_id'=>$id)));
        if (empty($principle)) $this->error('请填写原理',U('edit',array('quote_id'=>$id)));
        if (empty($advantage)) $this->error('请填写产品优势',U('edit',array('quote_id'=>$id)));
        if (empty($parameter)) $this->error('请填写参数',U('edit',array('quote_id'=>$id)));
        if (empty($standard))  $this->error('请填写施工标准',U('edit',array('quote_id'=>$id)));
        if (empty($customer))  $this->error('请填写售后',U('edit',array('quote_id'=>$id)));


        //查询当前系统的信息
        $quoteData = M('xgj_pad_furnish_quote')->where("quote_id=$id")->find();   

        /**********判断名称是否存在***************/
        if (!empty($quoteName) && !empty($quoteNames) && $quoteName != $quoteNames) {
            if ($quoteData['quote_name'] == $quoteName) {
                $this->error($quoteName.'系统已有,不能修改',U('edit',array('quote_id'=>$id)));
            }
        }
        /*****************************************/


        /**********判断优惠券是否合法*************/
        if (!empty($coupon)) {
            //正则匹配优惠券
            if(!preg_match("/^[0-9.]+$/", $coupon)){
                $this->error('优惠券金额不合法,请重新填写',U('edit',array('quote_id'=>$id)));
            }
        }
        /*****************************************/


        /**********判断赠品金额是否合法***********/
        if (!empty($gift)) {
            if(!preg_match("/^[0-9.]+$/", $gift) || $gift<0){
                $this->error('赠品金额不合法,请重新填写',U('edit',array('quote_id'=>$id)));
            }
        }
        /*****************************************/

       
        /**************处理上传图片***************/
        if (!empty($_FILES['img']['name']))                 $upload['img']=$_FILES['img'];
        if (!empty($_FILES['video_img']['name']))           $upload['video_img']=$_FILES['video_img'];
        if (!empty($_FILES['video']['name']))               $upload['video']=$_FILES['video'];
        if (!empty($_FILES['manual']['name']))              $upload['manual']=$_FILES['manual'];        
        if (!empty($_FILES['principle_video_img']['name'])) $upload['principle_video_img']=$_FILES['principle_video_img'];
        if (!empty($_FILES['principle_video']['name']))     $upload['principle_video']=$_FILES['principle_video'];
        if (!empty($_FILES['product_img']['name']))         $upload['product_img']=$_FILES['product_img'];
        //处理系统相册图片集
        if (!empty($_FILES['images']['name']['0'])){
            for ($i=0; $i < count($_FILES['images']['name']) ; $i++) { 
                $upload['images'.$i]['name'] = $_FILES['images']['name'][$i];
                $upload['images'.$i]['type'] = $_FILES['images']['type'][$i];
                $upload['images'.$i]['tmp_name'] = $_FILES['images']['tmp_name'][$i];
                $upload['images'.$i]['error'] = $_FILES['images']['error'][$i];
                $upload['images'.$i]['size'] = $_FILES['images']['size'][$i];
            }
            $_POST['images'] = '';
        }

        if (!empty($_FILES['editimg']['name'])){
            for ($i=0; $i < count($_FILES['editimg']['name']) ; $i++) { 
                if ($_FILES['editimg']['name'][$i]!='') {
                    $upload['editimg'.$i]['name'] = $_FILES['editimg']['name'][$i];
                    $upload['editimg'.$i]['type'] = $_FILES['editimg']['type'][$i];
                    $upload['editimg'.$i]['tmp_name'] = $_FILES['editimg']['tmp_name'][$i];
                    $upload['editimg'.$i]['error'] = $_FILES['editimg']['error'][$i];
                    $upload['editimg'.$i]['size'] = $_FILES['editimg']['size'][$i];
                }
                $oldimages[$i] = $_POST['oldimages'.$i];
            }
            $_POST['editimg'] = '';
        }

        foreach ($upload as $key => $value) {
            unset($_FILES);
            $_FILES[$key] = $value;

            if ($key == 'img') $modelName = '系统图片'; 
			else if ($key == 'video') $modelName = '视频'; 
            else if ($key == 'video_img') $modelName = '视频图片'; 
            else if ($key == 'principle_video') $modelName = '原理视频'; 
            else if ($key == 'principle_video_img') $modelName = '原理视频图片'; 
            else if ($key == 'manual') $modelName = '产品手册';
            else if (substr($key,0,6)=='images') $modelName = '添加系统相册图片';
            else if (substr($key,0,7)=='editimg') $modelName = '修改系统相册图片';
            else if ($key == 'product_img') $modelName = '产品功能图'; 

            if ($key == 'img') {
                $return = uploadOne($key,'Quote',C('IMG_THUMB_FACE'),'IMG_exts');
            }else if ($key == 'video') {
                $return = uploadOne($key,'QuoteVideo','','Video_exts');
            }else if ($key == 'video_img') {
                $return = uploadOne($key,'QuoteVideo','','IMG_exts');
            }else if ($key == 'product_img') {
                $return = uploadOne($key,'Quote','','IMG_exts');
            }else if ($key == 'principle_video') {
                $return = uploadOne($key,'QuotePrincipleVideo','','Video_exts');
            }else if ($key == 'principle_video_img') {
                $return = uploadOne($key,'QuotePrincipleVideo','','IMG_exts');
            }else if ($key == 'manual') {
                $return = uploadOne2($key,'Quote','','FILE_exts');
            }else{
                $return = uploadOne($key,'Quote','','IMG_exts');
            }

            if ($return['code'] == 0) $this->error($modelName.'上传失败: '.$return['error']);
            else if ($return['code'] > 0 && substr($key,0,6) == 'images') $_POST['images'] .= $return['images'].'|';
            else if ($return['code'] > 0 && substr($key,0,7) == 'editimg') {
                $num = ltrim($key ,'editimg');
                $oldimages[$num] = $return['images'];
                $images = explode('|', $quoteData['images']);
                foreach ($images as $k => $v) {
                    $this->unlinkFile($v);
                }
            }else {
                $_POST[$key] = $return['images'];
                $this->unlinkFile($quoteData[$key]);
            }
        }

        //处理要修改的字符串
        if (!empty($oldimages)) $editimg = implode('|', $oldimages);

        if (!empty($quoteData['images']) && !empty($editimg)) $_POST['images'] .= $editimg;
        else if (!empty($quoteData['images']) && empty($editimg)) $_POST['images'] .= $quoteData['images'];

        //去除系统图片集右边的|连接符
        $_POST['images'] = trim($_POST['images'],'|');

        /*****************************************/

        $data = D('xgj_pad_furnish_quote')->create();

        $updata = M('xgj_pad_furnish_quote')->where("quote_id=".$id)->save($data);

        if ($updata>=0) {
            $this->success('修改成功',U('index'));
        }else{
            $this->error('修改失败');
        }
    }

    public function unlinkFile($file){
        $files = explode('/', $file);
        @unlink('./Public/Uploads/'.$files['0'].'/'.$files['1'].'/thumb_54_54_'.$files['2']);
        @unlink('./Public/Uploads/'.$files['0'].'/'.$files['1'].'/thumb_100_100_'.$files['2']);
        @unlink('./Public/Uploads/'.$files['0'].'/'.$files['1'].'/thumb_160_160_'.$files['2']);
        @unlink('./Public/Uploads/'.$files['0'].'/'.$files['1'].'/thumb_220_220_'.$files['2']);
        @unlink('./Public/Uploads/'.$files['0'].'/'.$files['1'].'/thumb_340_400_'.$files['2']);
        @unlink('./Public/Uploads/'.$files['0'].'/'.$files['1'].'/thumb_350_350_'.$files['2']);
        @unlink('./Public/Uploads/'.$file);
    }


    //添加报价清单
    public function add(){

        $cate = M('xgj_pad_furnish_cat')->select();

        $this->assign('cate',$cate);

        $this->display();
    }

    //确认添加报价清单
    public function add_save(){

        $quote_name = I('post.quote_name');
        $coupon     = I('post.coupon');
        $gift       = I('post.gift');
        $sale       = I('post.sale');                           //打折比例
        $unitprice  = I('post.unitprice');
        $starttime  = I('post.starttime');
        $endtime    = I('post.endtime');
        $text       = I('post.text');
        $summary    = I('post.summary');                        //概述
        $principle  = I('post.principle');                      //原理
        $advantage  = I('post.advantage');                      //产品优势
        $parameter  = I('post.parameter');                      //参数
        $standard   = I('post.standard');                       //施工标准
        $customer   = I('post.customer');                       //售后

        if (empty($summary))   $this->error('请填写概述');
        if (empty($principle)) $this->error('请填写原理');
        if (empty($advantage)) $this->error('请填写产品优势');
        if (empty($parameter)) $this->error('请填写参数');
        if (empty($standard))  $this->error('请填写施工标准');
        if (empty($customer))  $this->error('请填写售后');

        $starttime = strtotime($starttime);
        $endtime = strtotime($endtime);

        $substr_1 = substr($coupon, 0,1);
        $substr_2 = substr($gift, 0,1);
        $substr_3 = substr($unitprice, 0,1);

        $array = array('quote_name'=>$quote_name,'coupon'=>$coupon,'sale'=>$sale,'gift'=>$gift,'unitprice'=>$unitprice,'text'=>$text,'starttime'=>$starttime,'endtime'=>$endtime);
        if ($substr_1 == '-' || $substr_2 == '-'  || $substr_3 == '-') {
            $this->error('金额不能为负数');
        }

        if (!is_numeric($coupon) || !is_numeric($gift) || !is_numeric($unitprice) || $substr_1 == '+' || $substr_2 == '+'  || $substr_3 == '+') {
             $this->error('金额只能为数字');
        }

        $sel = M('xgj_pad_furnish_quote')->field('quote_name')->select();

        foreach ($data as $key => $value) {
            if ($value['quote_name'] == $quote_name) {
                 $this->error($quote_name.'系统已有,不能添加');
            }
        }

        $img['Quote'] = $_FILES['img'];
        $img['QuoteVideoImg'] = $_FILES['video_img'];
        $img['video'] = $_FILES['video'];
        $img['manual'] = $_FILES['manual'];
        $img['product_img'] = $_FILES['product_img'];

        if(!empty($_FILES['images']['name'])){
            for ($i=0; $i < count($_FILES['images']['name']) ; $i++) {
                $img['images'.$i]['name'] = $_FILES['images']['name'][$i];
                $img['images'.$i]['type'] = $_FILES['images']['type'][$i];
                $img['images'.$i]['tmp_name'] = $_FILES['images']['tmp_name'][$i];
                $img['images'.$i]['error'] = $_FILES['images']['error'][$i];
                $img['images'.$i]['size'] = $_FILES['images']['size'][$i];
            }
        }

        unset($_FILES);
        $address = '';
        foreach ($img as $key=>$value) {
            $_FILES[$key] = $value;
            // if($_FILES[$key]['error']==0){
                if ($key == 'Quote') {
                    $image = uploadOne($key,'Quote',C('IMG_THUMB_FACE'),'IMG_exts');
                    if($image['code']==0) $this->error('系统图片上传失败: '.$image['error']);
                }else if($key == 'QuoteVideoImg'){
                    $video_img = uploadOne($key,'QuoteVideoImg',array(),'IMG_exts');
                    if($video_img['code']==0) $this->error('视屏图片上传失败: '.$video_img['error']);
                }else if($key == 'product_img'){
                    $product_img = uploadOne($key,'Quote',array(),'IMG_exts');
                    if($product_img['code']==0) $this->error('视屏图片上传失败: '.$product_img['error']);
                }else if($key == 'video'){
                    $video = uploadOne($key,'QuoteVideo',array(),'Video_exts');
                    if($video['code']==0) $this->error('视屏上传失败: '.$video['error']);
                }else if($key == 'manual'){
                    $manual = uploadOne($key,'Quote',array(),'FILE_exts');
                    if($manual['code']==0) $this->error('产品手册上传失败: '.$manual['error']);
                }else{
                    $imagename = uploadOne($key,'Quote',array(),'FILE_exts');
                    if ($imagename['code']==1) {
                        $address .= $imagename['images'].'|';
                    }else{
                        $this->error('系统相册修改失败: '.$imagename['error']);
                    }
                }
                // sleep(1);
            // }
        }

        $_POST['images'] = rtrim($address,'|');

        if ($image['code']==1 && $video['code']==1 && $video_img['code']==1 &&  $manual['code']==1 && $product_img['code']==1) {    

            $_POST['img']         =  $image['images'];
            $_POST['manual']      =  $manual['images'];
            $_POST['video']       =  $video['images'];
            $_POST['video_img']   =  $video_img['images'];
            $_POST['product_img'] =  $product_img['images'];
            
            $_POST['starttime'] = strtotime($_POST['starttime']);
            
            $_POST['endtime']   = strtotime($_POST['endtime']);

            $data = D('xgj_pad_furnish_quote')->create();
    
            $add = M('xgj_pad_furnish_quote')->add($data);

            if (!empty($add)) {
                $this->success('添加成功',U('index'));
            }else{
                $this->error('添加失败');
            }
        }else{
            $this->error('添加失败');
        }
           
       
    }

    //删除报价清单
    public function delete(){
        $id = I("get.id");

        $data = M('xgj_pad_furnish_quote')->field('img,video,video_img')->where("quote_id=".$id)->select();

        $video = $data['0']['video'];
        $video_0 = './Public/Uploads/'.$video;

       
        @unlink($video_0);

        $manual = $data['0']['manual'];
        $manual_0 = './Public/Uploads/'.$manual;

       
        @unlink($manual_0);
       
        $img = $data['0']['img'];

        $img_0 = './Public/Uploads/'.$img;
        $img = explode('/', $img);
        $img_1 = './Public/Uploads/'.$img['0'].'/'.$img['1'].'/'.'thumb_54_54_'.$img['2'];
        $img_2 = './Public/Uploads/'.$img['0'].'/'.$img['1'].'/'.'thumb_100_100_'.$img['2'];
        $img_3 = './Public/Uploads/'.$img['0'].'/'.$img['1'].'/'.'thumb_160_160_'.$img['2'];
        $img_4 = './Public/Uploads/'.$img['0'].'/'.$img['1'].'/'.'thumb_220_220_'.$img['2'];
        $img_5 = './Public/Uploads/'.$img['0'].'/'.$img['1'].'/'.'thumb_350_350_'.$img['2'];

        @unlink($img_0);
        @unlink($img_1);
        @unlink($img_2);
        @unlink($img_3);
        @unlink($img_4);
        @unlink($img_5);

        $video_img = $data['0']['video_img'];
        $video_img_0 = './Public/Uploads/'.$video_img;
        @unlink($video_img_0);



        $del = M('xgj_pad_furnish_quote')->where("quote_id=$id")->delete();

        if ($del == 1) {     

            $this->success('删除成功',U('index'));

        }else{

            $this->error('删除失败',U('index'));

        }
    }


    /**
     * 报价清单子列表
     */
    public function quote_list(){

        //搜索
        @$keyword=trim($_POST['keyword']);

        if(!empty($keyword)){
            $condition['goods_sn|goods_name'] = array('like',"%$keyword%");
        }
        //获取指定值
        $quote_id=$_GET['quote_id'];
        $level=$_GET['level'];
        //获取清单列表
        if (!empty($condition)) {
            $goods=M('xgj_furnish_goods')->where($condition)->select();
            $this->assign('goods',$goods);
        }
        //获取系统名称
        $quote=M('xgj_pad_furnish_quote')->where("quote_id=$quote_id")->select();
        //查询一条记录
        $child_list=M('xgj_pad_quote_child_list ch,xgj_furnish_goods g')->where("ch.goods_sn=g.goods_sn and ch.quote_id=$quote_id and ch.level=$level")->order(array('sort'=>'asc'))->select();

        $quote_type = M('xgj_pad_quote_type')->where("quote_id=$quote_id")->select();
        
        foreach ($child_list as $key => $value) {
            foreach ($quote_type as $k => $v) {
                if ($value['type'] == $v['id']) {
                    $child_list[$key]['text'] = $v['text'];
                }
            }
        }
        //模板传值
        $this->assign('keyword',$keyword);
        $this->assign('quote_name',$quote['0']['quote_name']);
        $this->assign('child_list',$child_list);
        $this->assign('level',$level);
        $this->assign('quote_id',$quote_id);

        $this->display('list');
    }


    public function child_list_adds(){

        $quote_id=intval($_GET['id']);

        $level=intval($_GET['level']);
        $chf_id=intval($_GET['chf_id']);
        $goods_id=intval($_GET['goods_id']);

        $quote = M('xgj_pad_quote_child_list')->where("level=$level and chf_id=$chf_id and quote_id=$quote_id and goods_id=$goods_id")->select();
        
        //判断是否有相同项
        if(count($quote)>0){
            echo "1";
        }else{
            echo '2';
        }
    }
     /**
     * 添加子列表
     */
    public function child_list_add(){

        $quote_id=intval($_GET['id']);

        $level=intval($_GET['level']);
        $chf_id=intval($_GET['chf_id']);
        $goods_id=intval($_GET['goods_id']);

        
        //获取一条记录
        $goods=M('xgj_furnish_goods')->where("goods_id=$goods_id")->select();
        //获取商品货号
        $goods_sn=$goods['0']['goods_sn'];
        //获取数据源
        $data=array(
                'goods_id'=>$goods_id,
                'level'=>$level,
                'chf_id'=>$chf_id,
                'quote_id'=>$quote_id,
                'goods_sn'=>$goods_sn
        );
        //添加一条记录
        $child_id=M('xgj_pad_quote_child_list')->add($data);
        //清楚之前的输出
        ob_start();
        //获取一条信息    
        $item=M('xgj_pad_quote_child_list ch,xgj_furnish_goods g')->where("ch.goods_id=g.goods_id and g.goods_id=$goods_id and ch.child_id=$child_id")->select();

        //输出一条信息内容
        echo "<tr id='child_list_tr_".$item['0']['child_id']."'>
        <td>".$item['0']['goods_id']."</td>
        <td style='width:300px;'>".$item['0']['goods_name']."</td>
        <td>".$item['0']['goods_model']."</td>
        
        <td><select name='batch_".$item['0']['child_id']."' class='bainput'>
                    <option  value='0' >第一批</option>
                    <option  value='1' >第二批</option>    
                    <option  value='2' >第三批</option>
            </select>
        </td>
        <td>
            未分类
        </td>
        <td>
            <form action=".U('quote_ini_edit')." method='get'>
                <input id='goods_sn".$item['0']['child_id']."' class='quinput' type='text' name='goods_sn'>
                <input id='child_id".$item['0']['child_id']."' class='quinput' type='hidden' name='child_id' value='".$item['0']['child_id']."'>
                <input id='level".$item['0']['child_id']."' class='quinput' type='hidden' name='level' value='".$item['0']['level']."'>
                <input id='quote_id".$item['0']['child_id']."' class='quinput' type='hidden' name='quote_id' value='".$item['0']['quote_id']."'>
                <a href='javascript:;' class='tablelink' onclick=bbb(".$item['0']['child_id'].")>确认修改 </a>
            </form>
        </td>
        <td>
        <a href='".U('optionfenlei')."?id=".$item['0']['child_id']."&quote_id=".$item['0']['quote_id']."&level=".$item['0']['level']."'>分类</a>
        |
        <input type='button' value='移除' onclick='remove_list(".$item['0']['child_id'].")'>
        </td>
        </tr>";
        ob_get_flush();

        // <td><select id='f_formula' name='f_formula' class='quinput'>
        //             <option value='' >公式</option>
        //             <option value='{$item['f_formula']}' >{$item['f_formula']}</option> 
        //     </select>
        // </td>

    }

    /**
     * 删除子清单中的一条记录
     */
    public function child_list_del(){   
        //获取指定值
        $child_id=intval($_GET['child_id']);

        $row = M('xgj_pad_quote_child_list')->where("child_id=$child_id")->find();

        $list = M('xgj_pad_quote_child_list')->where("quote_id={$row['quote_id']} and `level`={$row['level']}")->select();

        foreach ($list as $k => $v) {
            if (!empty($v['guanlian']) && $v['guanlian']==$child_id) {
                $sortAll[] = $v['sort'];
            }else if(!empty($v['guanlian']) && $v['guanlian'] == 'complex'){
                $complexSortList = explode('|', $v['complex']);
                foreach ($complexSortList as $key => $val) {
                    if ($val == $child_id) {
                        $sortAll[] = $v['sort'];
                    }
                }
            }
        }

        if (!empty($sortAll)) {
            $sortAll = implode(',',array_unique($sortAll));
            echo '请先更改编号为'.$sortAll.'的关联项！';exit;
        }
        
        //删除一条记录
        $del=M('xgj_pad_quote_child_list')->where("child_id=$child_id")->delete();
        //返回值
        if ($del == 1) {      
            echo 'success';
        }

    }

    /**
     * 报价商品配置候选清单列表
     */
    public function quote_ini_edit(){
        $childId = I('get.childId');
        $list = M('xgj_pad_quote_child_list l')->join('xgj_furnish_goods g on l.goods_sn = g.goods_sn')->where("l.child_id = $childId")->find();

        $this->assign('list',$list);
        $this->display();
    }

    public function doupdata_sn(){
        $goodsSn = trim(I('get.newsn'));

        $return = M('xgj_furnish_goods')->where("goods_sn=$goodsSn")->find();
        if (empty($return)) {
            echo 'error';exit;
        }
        echo json_encode($return);
    }

    public function saveGoodsSn(){
        $goods_sn = trim(I('post.newSn'));
        $child_id = I('post.childId');
        $level    = I('post.level');
        $quote_id = I('post.quote_id');
        $isAll    = I('post.isAll');
        $old_sn   = I('post.goods_sn');

        if ($isAll==='1') {
            $map['quote_id'] = $quote_id;
            $map['level']    = $level;
            $map['goods_sn'] = $old_sn;
        }else{
            $map['child_id'] = $child_id;
        }

        $updata['goods_sn'] = $goods_sn;

        $list = M('xgj_pad_quote_child_list')->where($map)->save($updata);

        if ($list !== '') $this->success('恭喜您，编辑成功',U("quote_list",array('level'=>$level,'quote_id'=>$quote_id)));
        else $this->error('抱歉，编辑失败',U("quote_ini_edit",array('childId'=>$child_id)));
    }

    public function do_quote_ini_edit(){

        $goods_sn = trim(I('get.goods_sn'));
        $child_id = I('get.child_id');

        $row = M('xgj_furnish_goods')->where("goods_sn = $goods_sn")->select();

        $quote_id = $_GET['quote_id'];

        if (empty($row)) {
            echo '3';exit;
            // $this->error('编码不存在',U("quote_list?level=$level&quote_id=$quote_id"));exit;
        }
        
        $updata['goods_id'] = $row['0']['goods_id'];
        $updata['goods_sn'] = $goods_sn;

        $list = M('xgj_pad_quote_child_list')->where("child_id=$child_id")->save($updata);

        if ($list == 1) {       
            echo '1';exit;

            // $this->success('修改成功',U("quote_list?level=$level&quote_id=$quote_id"));

        }else{
            echo '0';exit;
            // $this->error('修改失败',U("quote_list?level=$level&quote_id=$quote_id"));

        }
    }

    public function delsession(){
        unset($_SESSION['quote_ini_edit']);
    }

    //跳转添加分类
    public function fenlei(){
        $id = I('get.quote_id');
        $list = M('xgj_pad_quote_type')->where("quote_id=$id")->select();
        $this->assign('data',$list);
        $this->display();
    }

    //执行添加分类
    public function dofenlei(){
        // var_dump($_GET);exit;
        $id = I('get.quote_id');
        $text = I('get.text');
        $data = array(
            'quote_id'=>$id,
            'text'=>$text
            );

        $add = M('xgj_pad_quote_type')->add($data);

        $list = M('xgj_pad_quote_type')->where("quote_id=$id")->select();

        foreach ($list as $key => $value) {
            echo '<li>
                <label>类名</label>
                <input id="dddd'.$key.'" name="formula" type="text" class="dfinput" value='.$value['text'].' required="required"/>
                <a href="javascript:;" onclick="save('.$value['id'].','.$key.')">修改</a>
                <a href="javascript:;" onclick="del('.$value['id'].')">删除</a>
            </li>';
        }
        

        // var_dump($list);exit;
    }

    //删除分类
    public function delfenlei(){
        $id = I('get.id');
        $quote_id = I('get.quote_id');
        $del = M('xgj_pad_quote_type')->where("id=$id")->delete();
        if ($del==1) {
            $list = M('xgj_pad_quote_type')->where("quote_id=$quote_id")->select();
            foreach ($list as $key => $value) {
                echo '<li>
                    <label>类名</label>
                    <input id="dddd'.$key.'" name="formula" type="text" class="dfinput" value='.$value['text'].' required="required"/>
                    <a href="javascript:;" onclick="save('.$value['id'].','.$key.')">修改</a>
                    <a href="javascript:;" onclick="del('.$value['id'].')">删除</a>
                </li>';
            }
        }else{
            echo 'error';
        }
    }

    //修改分类
    public function savefenlei(){
        $id = I('get.id');
        $quote_id = I('get.quote_id');
        $text = I('get.text');
        $data['text'] = $text;
        $save = M('xgj_pad_quote_type')->where("id=$id")->save($data);
        if ($save==1) {
            $list = M('xgj_pad_quote_type')->where("quote_id=$quote_id")->select();
            foreach ($list as $key => $value) {
                echo '<li>
                    <label>类名</label>
                    <input id="dddd'.$key.'" name="formula" type="text" class="dfinput" value='.$value['text'].' required="required"/>
                    <a href="javascript:;" onclick="save('.$value['id'].','.$key.')">修改</a>
                    <a href="javascript:;" onclick="del('.$value['id'].')">删除</a>
                </li>';
            }
        }else{
            echo 'error';
        }
    }

    //跳转选择分类
    public function optionfenlei(){
        // echo '<pre>';
        // var_dump($_GET);exit;
        $cid = I('get.id');
        $id = I('get.quote_id');
        $level = I('get.level');    
        $list = M('xgj_pad_quote_type')->where("quote_id=$id")->select();
        $data = M('xgj_pad_quote_child_list ch,xgj_furnish_goods g')->where("ch.goods_sn=g.goods_sn and ch.child_id=$cid")->select();
        // echo $data['0']['type'];exit;
        if (!empty($data['0']['type'])) {
            $type = M('xgj_pad_quote_type')->where('id='.$data['0']['type'])->select();
            $this->assign('text',$type['0']['text']);
        }
        
        $this->assign('name',$data['0']['goods_name']);
        $this->assign('cid',$cid);
        $this->assign('quote_id',$id);
        $this->assign('data',$list);
        $this->assign('level',$level);
        $this->display('type');
    }

    //执行选择分类
    public function dooptionfenlei(){
        $id = I('post.id');     //分类ID
        $cid = I('post.cid');   //清单材料ID
        $quote_id = I('post.quote_id');   //清单类别ID
        $level = I('post.level');

        if (empty($id)) {
            $this->error('请选择类名',U("optionfenlei",array('id'=>$cid,'level'=>$level,'quote_id'=>$quote_id)));exit;
        }
       
        $data = array(
            'type'=>$id
            );

        $updata = M('xgj_pad_quote_child_list')->where("child_id=$cid")->save($data);
        if ($updata == 1) {
            $this->success('恭喜您，编辑成功',U("quote_list",array('level'=>$level,'quote_id'=>$quote_id)));
        }else{
            $this->error('抱歉，编辑失败',U("optionfenlei",array('id'=>$cid,'level'=>$level,'quote_id'=>$quote_id)));
        }
    }

    
    /*********************************************/
    public function rulechange(){
        $val = I('get.val');
        $qid = I('get.qid');
        $lv = I('get.lv');
        $where = "l.goods_sn = g.goods_sn and l.quote_id=$qid and l.level = $lv and l.goods_sn like '%".$val."%' or l.goods_sn = g.goods_sn and l.level = $lv and l.quote_id=$qid and g.goods_name like '%".$val."%'";
        $list = M('xgj_pad_quote_child_list l,xgj_furnish_goods g')->field("l.child_id id,g.goods_name name,g.goods_model model,g.goods_sn sn")->where($where)->select();
        
        foreach ($list as $key => $v) {
            echo '<option value="'.$v['id'].'">材料：'.$v['name'].'，型号：'.$v['model'].' ，编码：'.$v['sn'].'</option>';
        }

    }

    public function rule(){    

        unset($_SESSION['docomplex']);
        $id       = I('get.id');
        $quote_id = I('get.quote_id');
        $sort     = I('get.sort');
        $level    = I('get.level');

        $list = M('xgj_pad_quote_child_list l,xgj_furnish_goods g')->field("l.child_id id,g.goods_name name,g.goods_model model,g.goods_sn sn")->where("l.quote_id = $quote_id and l.level = $level and l.goods_sn = g.goods_sn and l.child_id<>$id")->select();
        
        $row  = M('xgj_pad_quote_child_list l,xgj_furnish_goods g')->field("l.child_id id,l.complex complex,g.goods_name name,g.goods_model model,g.goods_sn sn")->where("l.goods_sn = g.goods_sn and l.child_id=$id")->find();
        
        $data = M('xgj_pad_quote_child_list')->where("child_id=$id")->select();

        if (!empty($row['complex'])) {
            $complex = explode('|', $row['complex']);
            $_SESSION['docomplex'][$id] = $complex;
            foreach ($list as $key => $val) {
                foreach ($complex as $k => $v) {
                    if ($val['id'] == $v) {
                        $complexList[] = $list[$key];
                    }
                }
            }
        }

        foreach ($list as $key => $value) {
            if ($value['id']==$data['0']['guanlian']) {
                $this->assign('id',$value['id']);
                $this->assign('name',$value['name']);
                $this->assign('model',$value['model']);
                $this->assign('sn',$value['sn']);
            }
        }

        $formula = $data['0']['formula'];

        if (substr($formula, 0,4) == 'ceil') {
            $data['0']['formula'] = substr($formula, 4);
            $this->assign('ceil','1');
        }
        if (substr($formula, 0,5) == 'floor') {
            $data['0']['formula'] = substr($formula, 5);
            $this->assign('floor','1');
        }
        if(preg_match("/^[0-9]*$/", $formula)){
            $data['0']['formula'] = '';
            $data['0']['gongshi'] = '';
            $this->assign('num',$formula);
        }

        if (!empty($data['0']['guanlian']) && $data['0']['guanlian'] == 'room') {
            $room = explode('|', $data['0']['room']);
            $this->assign('room',$room);
        }

        $this->assign('formula',$data['0']);
        $this->assign('row',$row);
        $this->assign('data',$list);
        $this->assign('sort',$sort);
        $this->assign('complexList',$complexList);
        $this->assign('g_id',$id);
        $this->assign('quote_id',$quote_id);
        $this->assign('sort',$sort);
        $this->assign('level',$level);
        $this->display();
    }


    public function dorule(){
        $id         = I('post.id');       
        $quote_id   = I('post.quote_id');      
        $level      = I('post.level');   
        
        $numOrArea  = I('post.numOrArea');   //关联材料的数量还是面积
        $order      = I('post.order');       //关联材料
        $orders     = I('post.orders');      //关键字关联材料
        $host       = I('post.host');        //是否主机
        $arealt     = I('post.arealt');      //面积小于使用
        $areagt     = I('post.areagt');      //面积大于使用
        $num        = I('post.num');         //固定数量
        $ceilfloor  = I('post.ceilfloor');   //取整
        $formula    = I('post.formula');     //公式
        $num        = I('post.num');         //固定数量
        
        $numberlt   = I('post.numberlt');    //数量小于使用
        $numbergt   = I('post.numbergt');    //数量大于使用
        $ss         = I('post.ss');          //是否是关联多个材料
        
        $custom     = I('post.custom');      //自定义
        $maxcustom  = I('post.maxcustom');   //自定义小于使用
        $mincustom  = I('post.mincustom');   //自定义大于使用
        
        $singlelt   = I('post.singlelt');    //单个面积小于等于
        $singlegt   = I('post.singlegt');    //单个面积大于
		
        empty($_POST['judge'])?$judge='':$judge=I('post.judge');      //南北

        empty($_POST['house1'])?$house[]='0':$house[]=I('post.house1');      //卧室
        empty($_POST['house2'])?$house[]='0':$house[]=I('post.house2');      //客厅
        empty($_POST['house3'])?$house[]='0':$house[]=I('post.house3');      //厨房
        empty($_POST['house4'])?$house[]='0':$house[]=I('post.house4');      //卫生间
        empty($_POST['house5'])?$house[]='0':$house[]=I('post.house5');      //阳台
        empty($_POST['house6'])?$house[]='0':$house[]=I('post.house6');      //客厅+厨房


        if(!empty($custom) && !preg_match("/^[0-9]+$/", $custom)){
            $this->error('自定义只能输数字',U("rule",array('id'=>$id,'quote_id'=>$quote_id,'level'=>$level)));exit;
        }

        if(!empty($maxcustom) && !preg_match("/^[0-9]+$/", $maxcustom)){
            $this->error('自定义小于只能输数字',U("rule",array('id'=>$id,'quote_id'=>$quote_id,'level'=>$level)));exit;
        }

        if(!empty($mincustom) && !preg_match("/^[0-9]+$/", $mincustom)){
            $this->error('自定义大于只能输数字',U("rule",array('id'=>$id,'quote_id'=>$quote_id,'level'=>$level)));exit;
        }

        if(!empty($singlelt) && !preg_match("/^[0-9]{0,}(.?[0-9]{0,2})?$/", $singlelt)){
            $this->error('单个面积小于等于只能输数字',U("rule",array('id'=>$id,'quote_id'=>$quote_id,'level'=>$level)));exit;
        }

        if(!empty($singlegt) && !preg_match("/^[0-9]{0,}(.?[0-9]{0,2})?$/", $singlegt)){
            $this->error('单个面积大于只能输数字',U("rule",array('id'=>$id,'quote_id'=>$quote_id,'level'=>$level)));exit;
        }

        if(!empty($areagt) && !preg_match("/^[0-9]{0,}(.?[0-9]{0,2})?$/", $areagt)){
            $this->error('总面积小于等于只能输数字',U("rule",array('id'=>$id,'quote_id'=>$quote_id,'level'=>$level)));exit;
        }

        if(!empty($arealt) && !preg_match("/^[0-9]{0,}(.?[0-9]{0,2})?$/", $arealt)){
            $this->error('总面积小于等于只能输数字',U("rule",array('id'=>$id,'quote_id'=>$quote_id,'level'=>$level)));exit;
        }

        if (!empty($singlelt) && !empty($singlegt) && $singlelt<$singlegt) {
            $this->error('请认真填写单个面积',U("rule",array('id'=>$id,'quote_id'=>$quote_id,'level'=>$level)));exit;
        }
        
        $number     = '';
        $complex    = '';
        $room       = '';
        $average    = '';
        $averageNum = '';
        if ($ss==3) {
            $complex   = implode('|', $_SESSION['docomplex'][$id]);
            $order     = 'complex';    
            $numOrArea =  I('post.numOrAreas');
        }else if($ss==4) {
            $room       = implode('|', $house);
            $order      = 'room';
            $average    = I('post.average');     //单个面积平均等于
            $averageNum = I('post.averagenum');  //单个面积平均使用多少的总数等于多少使用
        }

        //关键字关联材料是否存在
        empty($orders)?'':$order=$orders;  
        /**********检查关联材料编号是否在当前材料编号之前********/
        if (!empty($order)) {
            //当前材料编号
            $idSort = M('xgj_pad_quote_child_list')->where("child_id=$id")->find();

            $returnSort = ture;
            //关联多个材料
            if ($order=='complex') {
                $where = ' child_id = '.implode(' or child_id = ', $_SESSION['docomplex'][$id]);
                $complexSort = M('xgj_pad_quote_child_list')->field('sort')->where("$where")->select();
                foreach ($complexSort as $k => $v) {
                    if ($v['sort'] >= $idSort['sort'] ) $returnSort = false;
                }
            }else if(preg_match("/^[0-9]+$/", $order)){
                //关联单个材料
                $orderSort = M('xgj_pad_quote_child_list')->where("child_id=$order")->find();
                if ($orderSort['sort'] >= $idSort['sort'] ) $returnSort = false;
            }

            if ($returnSort == false) {
                $this->error('关联材料编号不能大于等于当前材料编号',U("rule",array('id'=>$id,'quote_id'=>$quote_id,'level'=>$level)));exit;
            }
        }
        /*****************************************************/

        if (!empty($numberlt) && $order=='area' || !empty($numbergt) && $order=='area') {
            $this->error('请选择正确的关联材料',U("rule",array('id'=>$id,'quote_id'=>$quote_id,'level'=>$level)));exit;
        }

        if (empty($order) && !empty($numberlt) || empty($order) && !empty($numbergt)) {
            $this->error('请选择关联材料',U("rule",array('id'=>$id,'quote_id'=>$quote_id,'level'=>$level)));exit;
        }

        if (!empty($arealt) && !empty($areagt) && $arealt<$areagt) {
            $this->error('请认真填写面积',U("rule",array('id'=>$id,'quote_id'=>$quote_id,'level'=>$level)));exit;
        }

        if (!empty($numberlt) && !empty($numbergt) && $numberlt<$numbergt) {
            $this->error('请认真填写数量',U("rule",array('id'=>$id,'quote_id'=>$quote_id,'level'=>$level)));exit;
        }

        if (empty($formula) && empty($num) || !empty($formula) && !empty($num)) {
            $this->error('固定数量和公式请填写一个',U("rule",array('id'=>$id,'quote_id'=>$quote_id,'level'=>$level)));exit;
        }

        if(!empty($order)){
            $selectRow = M('xgj_pad_quote_child_list')->where("child_id = $id")->find();
            if ($order == $selectRow['guanlian']){
                
            }
        }

        if (!empty($order) && $order == 'area') {
            $material = '$area';                                 //面积
        }else if(!empty($order) && $order == 'bedroom'){
            $material = '$bedroom';                              //卧室
        }else if(!empty($order) && $order == 'living'){
            $material = '$living';                               //客厅
        }else if(!empty($order) && $order == 'kitchen'){
            $material = '$kitchen';                              //厨房
        }else if(!empty($order) && $order == 'toilet'){
            $material = '$toilet';                               //卫生间
        }else if(!empty($order) && $order == 'balcony'){
            $material = '$balcony';                              //阳台
        }else if(!empty($order) && $order == 'host'){
            $material = '$host';                                 //主机
        }else if(!empty($ss)    && $order == 'complex'){
            $material = '$complex';                              //关联多项
        }else if(!empty($ss)    && $order == 'room'){
            $material = '$room';                                 //关联多项
        }else if(!empty($order)){
            $select = M('xgj_pad_quote_child_list')->where("child_id = $order")->select();
            $material = $select['0']['formula'];                 //其他全部等于关联材料
        }

        if (!empty($host)) {
            $host = '1';
        }else{
            $host = '0';
        }

        //没有拼接的公式 
        if (!empty($formula) && empty($num)) {
            $gongshi = $formula;
        }

        if (empty($formula) && !empty($num)) {
            $gongshi = $num;
        }


        if (!empty($formula) && empty($num)) {
            if (!empty($ceilfloor) && $ceilfloor=='ceil') {
                $ceilorfloor = "ceil(($material)$formula)";       //往大取整
            }else if (!empty($ceilfloor) && $ceilfloor=='floor'){
                $ceilorfloor = "floor(($material)$formula)";      //往小取整
            }else{
                $ceilorfloor = $material.$formula;
            }

            $formulas = array('formula'=>$ceilorfloor,'guanlian'=>$order,'gongshi'=>$gongshi,'host'=>$host);
        }else if (empty($formula) && !empty($num)){
            $ceilorfloor = $num;
            // $formulas = array('formula'=>$num,'guanlian'=>$order,'gongshi'=>$gongshi,'host'=>$host);
        }
 
        $formulas = array(
            'maxnum'      =>$numberlt,
            'minnum'      =>$numbergt,
            'formula'     =>$ceilorfloor,
            'minarea'     =>$areagt,
            'maxarea'     =>$arealt,
            'guanlian'    =>$order,
            'gongshi'     =>$gongshi,
            'host'        =>$host,
            'numorarea'   =>$numOrArea,
            'complex'     =>$complex,
            'custom'      =>$custom,
            'maxcustom'   =>$maxcustom,
            'mincustom'   =>$mincustom,
            'singlelt'    =>$singlelt,
            'singlegt'    =>$singlegt,
            'room'        =>$room,
            'average'     =>$average,
            'average_num' =>$averageNum,
            'judge'       =>$judge
            );

        $save = M('xgj_pad_quote_child_list')->where("child_id=$id")->save($formulas);
       
        if ($save == 1) {
            unset($_SESSION['docomplex']);
            $this->success('恭喜您，编辑成功',U("quote_list",array('level'=>$level,'quote_id'=>$quote_id)));exit;
        }else{
            $this->error('抱歉，编辑失败',U("rule",array('id'=>$id,'quote_id'=>$quote_id,'level'=>$level)));exit;
        }

    }


    //关联多个材料
    function docomplex($o=null,$id=null,$i=null){
        // unset($_SESSION['docomplex']);exit;
        if ($o=='true') {
            foreach ($_SESSION['docomplex'][$id] as $k => $v) {
                if ($v==$i) {
                    unset($_SESSION['docomplex'][$id][$k]);
                }
            }
        }else{
            $id = I('get.id');
            $val = I('get.val');
            if(!preg_match("/^[0-9]+$/", $val)){
                echo 'error2';exit;
            }
            foreach ($_SESSION['docomplex'][$id] as $k => $v) {
                if ($v==$val) {
                    echo 'error';exit;
                }
            }
            $_SESSION['docomplex'][$id][] = $val;
        }
        
        foreach ($_SESSION['docomplex'][$id] as $k => $v) {
            $row = M('xgj_pad_quote_child_list l,xgj_furnish_goods g')->field("l.child_id id,g.goods_name name,g.goods_model model,g.goods_sn sn")->where("l.goods_sn = g.goods_sn and l.child_id=$v")->find();
            echo '材料：'.$row['name'].'，型号：'.$row['model'].' ，编码：'.$row['sn'].' &nbsp&nbsp&nbsp&nbsp<a href="javascript:;" onclick="del('.$row['id'].','.$id.')">删除</a><br>';
        }
    }

    function docomplexDel(){
        $id = I('get.id');
        $i = I('get.i');
        $this->docomplex('true',$id,$i);
    }

    /*********************************************/
    function deleteimages(){
        $img = './Public/Uploads/'.$_GET['img'];
        @unlink($img);
        $data = M('xgj_pad_furnish_quote')->field('images')->where("quote_id=".$_GET['id'])->find();
        $images = explode('|', $data['images']);
        foreach ($images as $key => $value) {
            if ($value == $_GET['img']) {
                unset($images[$key]);
            }
        }
        $image['images'] = implode('|', $images);
        $save = M('xgj_pad_furnish_quote')->where("quote_id=".$_GET['id'])->save($image);
        echo $save==true?'1':'2';
    }

    function editBatch(){
        $child_id = I('get.id');
        $batch['batch'] = I('get.batch');
        $save = M('xgj_pad_quote_child_list')->where("child_id=$child_id")->save($batch);
        echo $save;
    }


    public function sort(){
        if (empty($_GET['id']) || !preg_match("/^[0-9]+$/",$_GET['id']) || !empty($_GET['val']) && !preg_match("/^[0-9]+$/",$_GET['val'])) {
           echo '修改失败！';exit;
        }
        $id           = I('get.id');
        $data['sort'] = I('get.val');

        $sort    = M('xgj_pad_quote_child_list')->where("child_id=$id")->find();
        $where   = "`quote_id` = '{$sort['quote_id']}' and `level` = '{$sort['level']}'";

        $os = M('xgj_pad_quote_child_list')->where("$where and sort='{$data['sort']}'")->find();

        if (!empty($os)) {
            echo '修改失败！该编号已存在！';exit;
        }

        $sortAll = M('xgj_pad_quote_child_list')->where($where)->select();

        foreach ($sortAll as $k => $v) {
            if ($v['guanlian'] == $id && $data['sort'] >= $v['sort']) {
                $sorts[] = $v['sort']; 
            }else if(!empty($v['guanlian']) && $v['guanlian'] == 'complex'){
                $complex = 'child_id = '.str_replace("|", " or child_id = ", $v['complex']);
                $complexAll = M('xgj_pad_quote_child_list')->where($complex)->select();
                foreach ($complexAll as $key => $val) {
                    if ($val['child_id'] == $id && $data['sort'] >= $v['sort']){
                        $sorts[] = $v['sort'];
                    }
                }
            }
        }
        
        if (!empty($sorts)) {
            $sorts = implode(',',array_unique($sorts));
            echo '请先更改编号为'.$sorts.'的编号！';exit;
        }

        $update = M('xgj_pad_quote_child_list')->where("child_id=$id")->save($data);
        echo $update;
    }

    //设置南北城市
    public function judge(){
        $id       = I('get.g_id');
        $quote_id = I('get.quote_id');
        $sort     = I('get.sort');
        $level    = I('get.level');

        $judge = M('xgj_pad_quote_judge')->select();
        foreach ($judge as $k => $v) {
            if ($v['judge']==1) $nan[] = $v;
            if ($v['judge']==2) $bei[] = $v;
        }

        $this->assign('bei',$bei);
        $this->assign('nan',$nan);
        $this->assign('id',$id);
        $this->assign('quote_id',$quote_id);
        $this->assign('sort',$sort);
        $this->assign('level',$level);
        $this->display();
    }

    public function addJudge(){
        $prov  = I('get.prov');
        $city  = I('get.city');
        $judge = I('get.judge');
        $data = array(
            'prov'  => $prov,
            'city'  => $city,
            'judge' => $judge,
            );
        $isCity = M('xgj_pad_quote_judge')->where("city='{$city}'")->find();
        if (!empty($isCity)) {
            echo 'error';exit;
        }

        $add = M('xgj_pad_quote_judge')->add($data);
        $judge = M('xgj_pad_quote_judge')->select();
        foreach ($judge as $k => $v) {
            if ($v['judge']==1) $nan[] = $v;
            if ($v['judge']==2) $bei[] = $v;
        }

        echo '<label>南方</label>';
        echo '<label style="width:300px;margin-botton:2px;"><div>';
        foreach ($nan as $key => $value) {
            echo '<span id="judge1'.$value['id'].'" style="height:25px;">省份：'.$value['prov'].'; 城市：'.$value['city'].'<a style="margin-left:30px;" href="javascript:;" onclick="del('.$value['id'].',1)">删除</a></span>';
        }
        echo '</div></label>';

        echo '<label>北方</label>';
        echo '<label style="width:300px;margin-botton:2px;"><div>';
        foreach ($bei as $key => $value) {
            echo '<span id="judge2'.$value['id'].'" style="height:25px;">省份：'.$value['prov'].'; 城市：'.$value['city'].'<a style="margin-left:30px;" href="javascript:;" onclick="del('.$value['id'].',2)">删除</a></span>';
        }
        echo '</div></label>';
    }

    public function delJudge(){
        $id = I('get.id');
        $del = M('xgj_pad_quote_judge')->where("id='{$id}'")->delete();
        echo $del;
    } 

    public function demand_add(){
        $quote_id = I('get.quote_id');
        $this->assign('quote_id',$quote_id);
        $this->display();
    }

    public function insertDemand(){
        if (empty($_POST['config']))  $this->error('请选择配置');
        if (empty($_POST['number']))  $this->error('请填写展示序号');
        if (empty($_POST['name']))    $this->error('请填写功能名称');
        if (empty($_POST['type']))    $this->error('请选择需求类型');
        if (empty($_POST['explain'])) $this->error('请填写功能解释');
        if (empty($_FILES['img']['name']))     $this->error('请上传图片');
        $_POST['type']=implode('|', $_POST['type']);
        $_POST['explain']=implode('|', $_POST['explain']);
        $return = uploadOne('img','QuoteDemandImg','','IMG_exts');
        if ($return['code']==1) $_POST['img'] = $return['images'];
        else $this->error('图片上传失败，请重试');
        $data = M('xgj_pad_quote_explain')->create();
        $re = M('xgj_pad_quote_explain')->add($data);
        if($re>0){
            $this->success('添加成功',U('edit',array('quote_id'=>$data['quote_id'],'tab'=>'9')));
        }else{
            $this->error('添加失败',U('edit',array('quote_id'=>$data['quote_id'],'tab'=>'9')));
        }
    }

    public function demandEdit(){
        $id = I('get.id');
        $map['id'] = $id;
        $data = M('xgj_pad_quote_explain')->where($map)->find();
        $data['type'] = explode('|', $data['type']);
        $data['explain'] = explode('|', $data['explain']);
        $this->assign('data',$data);
        $this->display();
    }

    public function doDemandEdit(){
        if (empty($_POST['config']))  $this->error('请选择配置');
        if (empty($_POST['number']))  $this->error('请填写展示序号');
        if (empty($_POST['name']))    $this->error('请填写功能名称');
        if (empty($_POST['type']))    $this->error('请选择需求类型');
        if (empty($_POST['explain'])) $this->error('请填写功能解释');
        $map['id'] = I('post.id');
        $_POST['type']=implode('|', $_POST['type']);
        $_POST['explain']=implode('|', $_POST['explain']);
        if (!empty($_FILES['img']["name"])) {
            $oldImg = M('xgj_pad_quote_explain')->where($map)->getField('img');
            unlink('./Public/Uploads/'.$oldImg);
            $return = uploadOne('img','QuoteDemandImg','','IMG_exts');
            if ($return['code']==1) $_POST['img'] = $return['images'];
            else $this->error('图片上传失败，请重试');
        }
        $data = M('xgj_pad_quote_explain')->create();
        unset($data['id']);
        $re = M('xgj_pad_quote_explain')->where($map)->save($data);
        if($re!==false){
            $this->success('修改成功',U('edit',array('quote_id'=>$data['quote_id'],'tab'=>'9')));
        }else{
            $this->error('修改失败',U('edit',array('quote_id'=>$data['quote_id'],'tab'=>'9')));
        }
    }

    public function demandDel(){
        $map['id'] = I('post.id');
        $quote_id = I('post.quote_id');
        $oldImg = M('xgj_pad_quote_explain')->where($map)->getField('img');
        $re = M('xgj_pad_quote_explain')->where($map)->delete();
        if($re>0){
            unlink('./Public/Uploads/'.$oldImg);
            $this->success('删除成功',U('edit',array('quote_id'=>$quote_id,'tab'=>'9')));
        }else{
            $this->error('删除失败',U('edit',array('quote_id'=>$quote_id,'tab'=>'9')));
        }
    }
}