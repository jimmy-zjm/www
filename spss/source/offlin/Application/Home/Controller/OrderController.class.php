<?php
namespace Home\Controller;
use Think\Controller;

class OrderController extends BaseController {


	public function _initialize() {  
		parent::_initialize();
		$this->assign('now','3');
	}

    public function index(){
		$ids=getCatGrandson($_SESSION['dealerId'],'pad_user');
		$ids[]=$_SESSION['dealerId'];
        $map['o.u_id'] = array('in',$ids);
        $oid = M('pad_workplan')->field('o_id')->where(['uid'=>$_SESSION['dealerId']])->select();
        foreach ($oid as $k => $v) {
            $oids .= ','.$v['o_id'];
        }
        $map['o.id'] = ['in',trim($oids,',')];
        $map['_logic'] = 'OR';

        $total = M('pad_order o')
                ->join('pad_order_info i on o.id = i.order_id')
                ->where($map)
                ->count('distinct(i.order_id)');

        $page = getAjaxPage($total, C(Order_PAGE_SIZE));

        $list = M('pad_order o')
                ->field('o.id,o.sn,i.name q_name ,i.*,o.*')
                ->join('pad_order_info i on o.id = i.order_id')
                ->group('i.order_id')
                ->where($map)
                ->order('o.add_time desc')
                ->limit($page['limit'])
                ->select();

        $this->assign('list',$list);
        $this->assign('page',$page['page']);
        $this->display();
    }

    public function getCompanyData(){
        $id = I('get.id');
        if (empty($id)) die;
        $data = M('pad_user')->field('id,real_name')->where(['pid'=>$id])->select();
        echo json_encode($data);
    }

    public function getOrderList(){
        $data = I('get.data');
        foreach ($data as $k => $v) {
            $list[$v['name']] = $v['value'];
        }

        /**************************/
        //验证搜索是否是当前登录ID下级的用户
       /* if (!empty($list['company'])) {
            $pid = M('pad_user')->where(['id'=>$list['company']])->find();
            if ($pid['pid'] !== $_SESSION['dealerId']) {
                exit;
            }

            $map['o.u_id'] = $list['company'];

        }else if(!empty($list['real_name'])){
            $pid = M('pad_user')->where(['id'=>$list['real_name']])->find();
            if ($pid['pid'] !== $_SESSION['dealerId']) {
                $pid = M('pad_user')->where(['id'=>$pid['pid']])->find();
                if ($pid['pid'] !== $_SESSION['dealerId']) {
                    exit;
                }
            }

            $map['o.u_id'] = $list['real_name'];
        }*/
        /**************************/


        /********************************/
        //获取搜索条件下面分配客户
        if (!empty($map['o.u_id'])) $workplanMap['w.uid'] = $map['o.u_id'];
        else $workplanMap['w.uid'] = $_SESSION['dealerId'];
        
        if ($list['status']!='') {
            $map['o.status']         = $list['status'];
            $workplanMap['o.status'] = $list['status'];
        }

        if (!empty($list['key'])) {
            $map['o.sn|i.name|o.c_name|o.tel'] = ['like','%'.$list['key'].'%'];
            $workplanMap['o.sn|i.name|o.c_name|o.tel'] = ['like','%'.$list['key'].'%'];
        }

        $oid = M('pad_workplan w')
                ->field('w.o_id')
                ->join('pad_order o on w.o_id = o.id')
                ->join('pad_order_info i on o.id = i.order_id')
                ->group('i.order_id')
                ->where($workplanMap)
                ->select();

        if (!empty($oid)) {
            foreach ($oid as $k => $v) {
                $oids .= ','.$v['o_id'];
            }
            $mapOr['o.id'] = ['in',trim($oids,',')];
            //$map['o.id']   = ['not in',trim($oids,',')];
        }
        
        /********************************/

        
        /****************************/
        //获取搜索条件下面所有客户ID
        if (empty($list['real_name'])){
            if(empty($map['o.u_id'])){
                $map['o.u_id'] = $_SESSION['dealerId'];
            }else{
                $ids = $map['o.u_id'];
            }

            $re = M('pad_user')->field('id,pid,level')->where(['pid'=>$map['o.u_id']])->select();
            foreach ($re as $k => $v) {
                $ids .= ','.$v['id'];
            }

            if ($re['0']['level']==2 && !empty($ids)) {
                $re = M('pad_user')->field('id')->where(['pid'=>['in',trim($ids,',')]])->select();
                foreach ($re as $k => $v) {
                    $ids .= ','.$v['id'];
                }
            }

            if (empty($list['company'])) {
                $ids = $map['o.u_id'].$ids;
            }

            $map['o.u_id'] = ['in',trim($ids,',')];
        }
        /****************************/


        if (!empty($mapOr)) {
            $map['_logic'] = 'and';
            $where = $mapOr;
            $where['_complex'] = $map;
            $where['_logic'] = 'or';
        }else{
            $where = $map;
        }

        $total = M('pad_order o')
                ->join('pad_order_info i on o.id = i.order_id')
                ->where($map)
                ->count('distinct(i.order_id)');

        $page = getAjaxPage($total, C(Order_PAGE_SIZE));

        $list = M('pad_order o')
                ->field('o.id,o.sn,i.name q_name ,i.*,o.*')
                ->join('pad_order_info i on o.id = i.order_id')
                ->group('i.order_id')
                ->where($map)
                ->order('o.add_time desc')
                ->limit($page['limit'])
                ->select();

        foreach ($list as $k => $v) {
            $return .= '<div class="order-list">
                        <p class="sys-item">
                            <span class="item-name">订单编号</span>
                            <span class="ell">'.$v["sn"].'</span>
                        </p>
                        <p class="sys-item">
                            <span class="item-name">客户信息</span>
                            <span class="ell">'.$v["c_name"].'&nbsp;&nbsp;&nbsp;'.$v['tel'].'</span>
                        </p>
                        <p class="sys-item">
                            <span class="item-name">房屋地址</span>
                            <span class="ell">'.$v["province"].'-'.$v["city"].'-'.$v["district"].'-'.$v["address"].'</span>
                        </p>
                        <p class="sys-item">
                            <span class="item-name">下单账号</span>
                            <span class="ell">'.$v["u_name"].'</span>
                        </p>
                        <p class="last-row">
                            <a href="'.U('Order/detail',array('id'=>$v['id'])).'" class="orderdetail-btn">订单详情</a>
                        </p>
                        </div>';
        }
        $return .=$page['page'].'
                <script>
                    $(".page a").click(function(){
                        var p = $(this).attr("data-page");
                        alert(p);
                        var data = $("#form1").serializeArray();
                        $.get("'.U("getOrderList").'",{"data":data,"p":p},function(re){
                            $(".order-list").html(re);
                        })
                    })
                </script>
                ';
        echo $return;
    }


    //订单详情
    public function detail(){
        $id=I('id');//订单id
        $data=M('pad_order o')
                ->field('o.*,i.*,o.id oid,i.status i_status,o.status status')
                ->join('pad_order_info i on i.order_id = o.id')
                ->order('o.add_time,o.order_status')
                ->where(array('o.id'=>$id))
                ->find();

        $layout       = explode(',', $data['layout']);
        $houseAreaAll = $data['area'];//每个房间面积
        $build        = $data['build'];//建筑面积
        $this->assign('build',$build); 
        $houseAreaArray = explode('|', $houseAreaAll);
        foreach ($houseAreaArray as $key => $value) {
            $houseArea[$key] = explode(',', $value);
        }   
        //计算总面积
        $areaAll = 0;
        foreach ($houseArea as $k => $v) {
            if (is_array($v)) {
                $areaAll += array_sum($v);
            }
        }
        $this->assign('houseArea',$houseArea);
        $this->assign('areaAll',$areaAll); 
        foreach ($layout as $key => $val) {
            if ($key=='0') $name = '室';
            else if ($key=='1') $name = '厅';
            else if ($key=='2') $name = '厨';
            else if ($key=='3') $name = '卫';
            else if ($key=='4') $name = '阳台';
            else if ($key=='5') $name = '阁楼';
            else if ($key=='6') $name = '地下室';
            if($val!=0){
                $houseString .= $val.$name;
            }
        }
        //查询所有系统
        $quoteinfo=M('pad_order_info i')
                ->where(['i.order_id'=>$id])
                ->select();
        $this->assign('houseString',$houseString);
        $this->assign('data',$data);
        $this->assign('quoteinfo',$quoteinfo);
        $level=M('pad_user')->where(['id'=>$_SESSION['dealerId']])->getField('level');
        $this->assign('level',$level);
        $info=M('pad_workplan')->where(['o_id'=>$id])->select();
        $this->assign('info',$info);
        $this->display();
    }
    //房屋面积
    public function homeinfo(){
    	$id=I('id');//订单id
        $data=M('pad_file')->where(['o_id'=>$id,'types'=>'1'])->order('add_time desc')->select();
        $this->assign('oid',$id);
        $this->assign('data',$data);
        $this->display();
    }
    // **上传
     public function infoupload(){
        $oid=I('oid');//订单id
        $types=I('type');
        switch ($types) {
            case '1':
                $title='房屋面积';
                break;
            case '2':
                $title='施工图纸';
                break;
            case '3':
                $title='施工安装';
                break;
            case '4':
                $title='设备调试';
                break;
            case '5':
                $title='验收进度';
                break;
        }
        $this->assign('oid',$oid);
        $this->assign('types',$types);
        $this->assign('title',$title);
        $this->display();
    }
    //上传操作
    public function doUpload(){
        layout(false);

        if($_POST){

            $userId = $_SESSION['dealerId'];
            switch ($_POST['types']) {
                case '1':
                    $url='Order/homeinfo';
                    break;
                case '2':
                    $url='Order/seedraw';
                    break;
                case '3':
                    $url='Order/schedule';
                    break;
                case '4':
                    $url='Order/debugs';
                    break;
                case '5':
                    $url='Order/checklist';
                    break;
            }
            $data['title'] = $_POST["title"];   //标题
            $data['content'] = $_POST["content"];   //内容
            //var_dump($_POST["content"],$data['content']);die;
            $data['u_id'] = $userId;
            $data['types'] = $_POST["types"];    //类别
            $data['o_id'] = $_POST["oid"];   //性别
 
            if(!empty($_FILES['file']['name']['0'])){
                $pics = array();
                for ($i=0; $i < count($_FILES['file']['name']); ++$i) {
                    $pics[] = array(
                            'name'     => $_FILES['file']['name'][$i],
                            'type'     => $_FILES['file']['type'][$i],
                            'tmp_name' => $_FILES['file']['tmp_name'][$i],
                            'error'    => $_FILES['file']['error'][$i],
                            'size'     => $_FILES['file']['size'][$i],
                    );
                }
                foreach ($pics as $key=>$value) {
                    $_FILES[$key] = $value;
                    if($_FILES[$key]['error']==0){
                        //将上传成功的商品相册图片地址保存起来
                        $image = uploadOne($key,'PadFile',C('IMG_THUMB_FACE'));
                        if($image['code']!=1){
                             //头像上传失败
                             $error = $image['error'];
                             $status=false;
                             return false;
                        }
                        $data['img'] = $image['images'];
                        if(M('pad_file')->add($data)!==false){
                            if($_POST['types']==5){
                                $re=M('pad_order')->where(['id'=>$_POST["oid"]])->save(['status'=>4]);
                            }
                            $status=true;
                        }else{
                            $error='上传失败';
                            $status=false;
                            return false;
                        }
                    }else{
                        $error ='图片上传失败:'.$image['error'];
                        $status=false;
                        return false;
                    }
                }
                if($status){
                    $this->success('上传成功',U($url,['id'=>$_POST["oid"]]));
                }else{
                    $this->error($error);
                }

            }else{
                $this->error('请选择图片！！');
            }
        }
    }


            //头像
        //     if(isset($_FILES['file'])&&$_FILES['file']['error']==0){
        //         $image = uploadOne('file','PadFile',C('IMG_THUMB_FACE'));
        //         if($image['code']!=1){
        //              //头像上传失败
        //              $this->error = $image['error'];
        //              return false;
        //         }
        //          $data['img'] = $image['images'];
        //     }else{
        //         $this->error('上传失败,请选择图片！！'); 
        //     }
        //     $data['title'] = $_POST["title"];   //标题
        //     $data['content'] = $_POST["content"];   //内容
        //     $data['u_id'] = $userId;
        //     $data['types'] = $_POST["types"];    //类别
        //     $data['o_id'] = $_POST["oid"];   //性别
        //     $rs = M('pad_file')->add($data); 
            
        //     if($rs!==false){
        //         if($_POST['types']==5){
        //             $re=M('pad_order')->where(['id'=>$_POST["oid"]])->save(['status'=>4]);
        //         }
        //         $this->success('上传成功',U($url,['id'=>$_POST["oid"]]));
        //     }else{
        //         $this->error('上传失败');
        //     }
        // }
    // }
    //查看图纸
    public function seedraw(){
        $id=I('id');//订单id
        $data=M('pad_file')->where(['o_id'=>$id,'types'=>'2'])->order('add_time desc')->select();
        $this->assign('oid',$id);
        $this->assign('data',$data);
        $this->display();
    }
    //施工进度
    public function workinfo(){
    	
        $this->display();
    }
    //查看施工进度
    public function schedule(){
        $id=I('id');//订单id
        $data=M('pad_file')->where(['o_id'=>$id,'types'=>'3'])->order('add_time desc')->select();
        $this->assign('oid',$id);
        $this->assign('data',$data);
        $this->display();
    }
    //分配施工人员
    public function assigns(){
        $rid=I('rid');//工作名称
        $sid=I('sid');//当前状态
        $oid=I('oid');//订单id
        $info=M('pad_user')->where(['id'=>$_SESSION['dealerId']])->select();
        $orderinfo=M('pad_order')->where(['id'=>$oid])->find();
        if($orderinfo['status'] == ($sid-1)){
            $data=getCatGrandson($_SESSION['dealerId'],'pad_user');
			$data[]=$_SESSION['dealerId'];
            $map['pid']=['in',$data];
            $data_=M('pad_user')->where($map)->select();
            $res=array_merge($info,$data_);
            // foreach ($res as $k => $v) {
            //     $arr=explode(',', $v['rose']);
            //     if(in_array($rid,$arr)){
            //         $datas[$k]=$v;
            //     }
            // }
            //dump($res);
            $this->assign('data',$res);
            $this->assign('rid',$rid);
            $this->assign('sid',$sid);
            $this->assign('oid',$oid);
            $this->display();
        }else{
            layout(false);
            $this->error('请按照顺序分配人员！',U('Order/detail',['id'=>$oid]));
        }
    }

    public function doAssigns(){
        layout(false);
        $rid=I('rid');//工作名称
        $sid=I('sid');//当前状态
        $oid=I('oid');//订单id
        $uid=I('person');//选中的实施者
        $info=M('pad_user')->where(['id'=>$uid])->find();
        $res=M('pad_workplan')->where(['o_id'=>$oid,'workname'=>$sid])->count('id');
        if($res>0){
            $this->error('已分配，不可重复分配！',U('Order/detail',['id'=>$oid]));
        }
        //var_dump($info);die;
        $data=[
            'u_id'=>$_SESSION['dealerId'],
            'worker'=>$info['real_name'],
            'phone'=>$info['tel'],
            'workname'=>$sid,
            'o_id'=>$oid,
            'uid'=>$uid,
        ];
        $re=M('pad_workplan')->add($data);
        if($re!==false){
            if(M('pad_order')->where(['id'=>$oid])->save(['status'=>$sid])!==false){
                $this->success('提交成功',U('Order/detail',['id'=>$oid]));
            }else{
                $this->error('提交失败',U('Order/assigns',['oid'=>$oid,'rid'=>$rid,'sid'=>$sid]));
            }
        }else{
            $this->error('提交失败',U('Order/assigns',['oid'=>$oid,'rid'=>$rid,'sid'=>$sid]));
        }
    }
    //施工人员
    public function personnel(){
    	
        $this->display();
    }
    //查看调试进度
    public function debugs(){
        $id=I('id');//订单id
        $data=M('pad_file')->where(['o_id'=>$id,'types'=>'4'])->order('add_time desc')->select();
        $this->assign('oid',$id);
        $this->assign('data',$data);
        $this->display();
    }
    //验收单
    public function checklist(){
    	$id=I('id');//订单id
        $data=M('pad_file')->where(['o_id'=>$id,'types'=>'5'])->order('add_time desc')->select();
        $this->assign('oid',$id);
        $this->assign('data',$data);
        $this->display();
    }

    // public function verifyUser($userId){
    //     if ($userId != $_SESSION['dealerId']) {

    //         $pid = M('pad_user')->where(['id'=>$userId])->getField('pid');
    //         if ($pid != $_SESSION['dealerId']) {
    //             $pid = M('pad_user')->where(['id'=>$pid])->getField('pid');
    //             if ($pid != $_SESSION['dealerId']) $this->error('您无法查看此系统');
    //         }
    //     }
    // }

    public function verifyUser($u_id,$o_id){
		$level=M('pad_user')->where(['id'=>$_SESSION['dealerId']])->getField('level');		
		if($level==0)
			$error = true;
        if ($u_id != $_SESSION['dealerId']) {
			$ids=getCatGrandson($_SESSION['dealerId'],'pad_user');
			if(in_array($u_id,$ids))
				$error = true;           
        }else
				$error = true;
		if($error != true){
			if($u_id != $_SESSION['dealerId']){
				$info=M('pad_workplan')->field('uid')->where(['o_id'=>$o_id])->select();
				foreach ($info as $k => $v) {
				  $res[]=$v['uid'];
				}
				if(in_array($_SESSION['dealerId'],$res))
					$error = true;
			}
		}
		if($error !=true)
			$this->error('暂无权限可以查看!');
    }

    //查看清单
    public function listdetail(){
        layout(false);
        $id = I('get.id');

        $map['id'] = $id;
        $data = M('pad_order_info')->where($map)->find();

        $userId = M('pad_order')->where(['id'=>$data['order_id']])->getField('u_id');

        $this->verifyUser($userId,$data['order_id']);
		
		/*if($u_id != $_SESSION['dealerId']){
				$info=M('pad_workplan')->field('uid')->where(['o_id'=>$data['order_id']])->select();
				foreach ($info as $k => $v) {
				  $res[]=$v['uid'];
				}
				if(in_array($_SESSION['dealerId'],$res)==false)
					$this->error('暂无权限可以查看!');
		}*/
        $list = json_decode(stripslashes($data['info']),true);

        foreach ($list as $k => $v) {
            $reList[$v['type']][] = $v; 
            if (!empty($v['edit'])) $money[$v['type']] += $v['price']*$v['edit'];
            else                    $money[$v['type']] += $v['price']*$v['num'];
        }

        $keys = array_keys($reList);
        $re = M('xgj_quote_type')->where(['id'=>['in',$keys]])->select();
        foreach ($re as $k => $v) {
            $listType[$v['id']] = $v['text'];
        }

        /**********************************/
        //房屋信息
        $layout       = explode(',', $data['layout']);
        $houseAreaAll = $data['area'];//每个房间面积
        $build        = $data['build'];//建筑面积
        
        $houseAreaArray = explode('|', $houseAreaAll);
        foreach ($houseAreaArray as $key => $value) {
            $houseArea[$key] = explode(',', $value);
        }   
        //计算总面积
        $areaAll = 0;
        foreach ($houseArea as $k => $v) {
            if (is_array($v)) {
                $areaAll += array_sum($v);
            }
        }
        foreach ($layout as $key => $val) {
            if ($key=='0') $name = '室';
            else if ($key=='1') $name = '厅';
            else if ($key=='2') $name = '厨';
            else if ($key=='3') $name = '卫';
            else if ($key=='4') $name = '阳台';
            else if ($key=='5') $name = '阁楼';
            else if ($key=='6') $name = '地下室';
            if($val!=0){
                $houseString .= $val.$name;
            }
        }

        /*****************************/
        //是否可以调整清单
        $workplan = M('pad_workplan w')->field('uid')->join('pad_order_info i on w.o_id=i.order_id')->where(['i.q_id'=>$id])->select();
        $level = M('pad_user')->where(['id'=>$_SESSION['dealerId']])->getField('level');
        if (count($workplan)==1 && $workplan['0']['uid']==$_SESSION['dealerId'] || count($workplan)==0 || count($workplan)==1 && $level==0) {
            $this->assign('count',1);
        }
        /*****************************/

        $this->assign('build',$build); 
        $this->assign('houseArea',$houseArea);
        $this->assign('areaAll',$areaAll); 
        $this->assign('houseString',$houseString);
        /**********************************/
        
        $this->assign('data',$data);
        $this->assign('list',$reList);
        $this->assign('money',$money);
        $this->assign('type',$listType);
        $this->display();
    }

    public function editdraw(){
        $id=I('id');
        $info=M('pad_file')->where(['id'=>$id])->find();
        switch ($info['types']) {
            case '1':
                $title='房屋面积';
                break;
            case '2':
                $title='施工图纸';
                break;
            case '3':
                $title='施工安装';
                break;
            case '4':
                $title='设备调试';
                break;
            case '5':
                $title='验收进度';
                break;
        }
        $this->assign('info',$info);
        $this->assign('title',$title);
        $this->display();
    }

    //上传操作
    public function editUpload(){
        layout(false);
        if($_POST){
            $userId = $_SESSION['dealerId'];
            //头像
             if(isset($_FILES['file'])&&$_FILES['file']['error']==0){
                $oldName=$_POST['oldimg'];
                $image = uploadOne('file','PadFile',C('IMG_THUMB_FACE'));
                if($image['code']!=1){
                     //头像上传失败
                     $this->error = $image['error'];
                     return false;
                }
                deleteImage($oldName,C('IMG_THUMB_FACE'));
                $data['img'] = $image['images'];
             }
            $data['title'] = $_POST["title"];   //标题
            $data['content'] = $_POST["content"];   //内容
            $data['u_id'] = $userId;
            $data['types'] = $_POST["types"];    //类别
            $data['o_id'] = $_POST["oid"];   //性别
            $rs = M('pad_file')->where(['id'=>$_POST['id']])->save($data); 
            switch ($_POST['types']) {
                case '1':
                    $url='Order/homeinfo';
                    break;
                case '2':
                    $url='Order/seedraw';
                    break;
                case '3':
                    $url='Order/schedule';
                    break;
                case '4':
                    $url='Order/debugs';
                    break;
                case '5':
                    $url='Order/checklist';
                    break;
            }
            if($rs!==false){
                $this->success('修改成功',U($url,['id'=>$_POST["oid"]]));
            }else{
                $this->error('修改失败');
            }
        }
    }
    public function deldraw(){
        layout(false);
        $id=I('id');
        $info=M('pad_file')->where(['id'=>$id])->find();
        if(!empty($info)){
            deleteImage($info['img'],C('IMG_THUMB_FACE'));
            $re=M('pad_file')->where(['id'=>$id])->delete();
            if($rs!==false){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('删除失败,所选内容为空!');
        }
    }

    public function editNum(){
        layout(false);
        $id = I('post.id');
        $num = I('post.num');
        $map['id'] = $id;

        $data = M('pad_order_info')->where($map)->find();
        $info = json_decode(stripslashes($data['info']),true);

        foreach ($info as $k => $v) {
            if (empty($info[$k]['edit'])) {
                if ($num[$v['id']]!=$v['num']) {
                    $goods[$v['lv']] += ($num[$v['id']]-$num[$v['id']])*$v['price'];
                }
            }else{
                if ($num[$v['id']]!=$info[$k]['edit']) {
                    $goods[$v['lv']] += ($num[$v['id']]-$info[$k]['edit'])*$v['price'];
                }
            }
            $info[$k]['edit'] = $num[$v['id']];
        }


        $edit['info'] = addslashes(json_encode($info));
        $edit['price'] = $data['price']+array_sum($goods);
        if (!empty($goods['1'])) $edit['zprice']  = ceil($data['zprice']+$goods['1']);
        if (!empty($goods['3'])) $edit['install'] = ceil($data['install']+$goods['3']);


        $re = M('pad_order_info')->where($map)->save($edit);
        if ($re !== false) {
            $this->success('提交成功');
        }else{
            $this->success('提交失败');
        }
    }


    public function goodsAdd(){
        layout(false);
        $id   = I('get.id');
        $type = I('get.type');

        $map['id'] = $id;
        $data   = M('pad_order_info')->where($map)->find();
        $userId = M('pad_order')->where(['id'=>$data['order_id']])->getField('u_id');
        $this->verifyUser($userId);


        $workplan = M('pad_workplan w')->field('uid')->join('pad_order_info i on w.o_id=i.order_id')->where(['i.q_id'=>$id])->select();
        $level = M('pad_user')->where(['id'=>$_SESSION['dealerId']])->getField('level');
        if (count($workplan)==1 && $workplan['0']['uid']==$_SESSION['dealerId'] || count($workplan)==0 || count($workplan)==1 && $level==0) {
            $this->assign('count',1);
        }else{
            $this->error('系统已分配，不能修改');
        }

        $list = M('xgj_quote_child_list l')->field('child_id id,goods_name name,goods_model model')->join('xgj_furnish_goods g on l.goods_sn = g.goods_sn')->where(['l.type'=>$type])->select();
        $type = M('xgj_quote_type')->where(['id'=>$type])->getField('text');
        $this->assign('type',$type);
        $this->assign('list',$list);
        $this->display('add');
    }

    public function doGoodsAdd(){
        layout(false);
        $id = I('post.id');
        $num = I('post.num');

        foreach ($num as $k => $v) {
            if ($v==0) {
                unset($num[$k]);
            }
        }

        $data = M('pad_order_info')->where(['id'=>$id])->find();

        $info = json_decode(stripslashes($data['info']),true);

        foreach ($info as $k => $v) {
            if (!empty($num[$v['id']])) {
                if (empty($v['edit']))  $info[$k]['edit'] = $v['num']+$num[$v['id']];
                else                    $info[$k]['edit'] += $num[$v['id']];
                $goods[$v['lv']] += $num[$v['id']]*$v['price'];
                unset($num[$v['id']]);
            }
        }

        $childId = implode(',', array_keys($num));

        if ($childId!='') {
            $childList = M('xgj_quote_child_list l')
                ->field('child_id id,goods_name name,goods_model model,g.goods_sn sn,shop_price price,shop_price money,batch,host,goods_brand brand,goods_img img,goods_unit unit,goods_lv lv,type,origin,features,maxcustom,mincustom')
                ->join('xgj_furnish_goods g on l.goods_sn = g.goods_sn')
                ->where(['l.child_id'=>['in',$childId]])
                ->select();
        
            foreach ($childList as $k => $v) {
                $childList[$k]['num']  = 0;
                $childList[$k]['edit'] = $num[$v['id']];
                $goods[$v['lv']] += $num[$v['id']]*$v['price'];
            }

            $info = array_merge($info,$childList);
        }

        $edit['info'] = addslashes(json_encode($info));
        $edit['price'] = $data['price']+array_sum($goods);
        if (!empty($goods['1'])) $edit['zprice']  = ceil($data['zprice']+$goods['1']);
        if (!empty($goods['3'])) $edit['install'] = ceil($data['install']+$goods['3']);

        $re = M('pad_order_info')->where(['id'=>$id])->save($edit);
        if ($re !== false) {
            $this->success('提交成功',U('listdetail',['id'=>$id]));
        }else{
            $this->error('提交失败');
        }
    }

    public function search(){
        $key = I('get.key');
        $type = I('get.type');
        $map['l.type']=$type;
        $map['g.goods_name|g.goods_model|g.goods_sn']=['like','%'.$key.'%'];

        $list = M('xgj_quote_child_list l')->field('child_id id,goods_name name,goods_model model')->join('xgj_furnish_goods g on l.goods_sn = g.goods_sn')->where($map)->select();

        $type = M('xgj_quote_type')->where(['id'=>$type])->getField('text');

        foreach ($list as $k => $v){
            $re.="<li>
                <p>产品名称：".$v['name']."</p>
                <p>产品型号：".$v['model']."</p>
                <p>分类：".$v['type']."</p>
                <div class='sys-item'>数量
                    <p class='fr btn-box'>
                        <span class='minusbtn'>-</span>
                        <span class='shownum'><input type='text' name='num[".$v['id']."]' value='0'></span>
                        <span class='addbtn'>+</span>
                    </p>
                </div>
            </li>";
        }
        $re.="<script>
            //减法
            $('.minusbtn').click(function(){
                var num = Math.round(($(this).next().children().val()-1)*100)/100;
                if (num <= 0) var num = 0;
                $(this).next().children().val(num);
            })
            //加法
            $('.addbtn').click(function(){
                var num = parseFloat($(this).prev().children().val())+parseFloat(1);
                $(this).prev().children().val(num);
            })

            $('.shownum').children().change(function(){
                var num = $(this).val();
                if (num <= 0 || isNaN(num)) $(this).val(0);
            })
        </script>";
        echo $re;
    }
}	


