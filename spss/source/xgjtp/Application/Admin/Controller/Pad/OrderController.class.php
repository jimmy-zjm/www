<?php
namespace Admin\Controller\Pad;
use \Admin\Controller\Index\AdminController;
/*
用户名控制器
 */
class OrderController extends AdminController{

	public function index(){
        $this->display();
	}

	public function ajaxIndex(){
		$where = ' 1 = 1 '; // 搜索条件                 

        // 关键词搜索               
		$quote_name = I('quote_name') ? trim(I('quote_name')) : '';
		$company    = I('company') ? trim(I('company')) : '';
		$starttime  = I('starttime') ? trim(I('starttime')) : '';
		$endtime    = I('endtime') ? trim(I('endtime')) : '';

        if($quote_name)
        {
            $where .= " and q.name like '%$quote_name%'" ;
        }

        if($company)
        {
            $where .= " and u.company like '%$company%'" ;
        }


        if ($starttime)
        {
            $where .= " and o.add_time >= '{$starttime}'";
        } 

        if ($endtime)
        {
            $where .= " and o.add_time <= '{$endtime}'";
        }  

        $count=M('pad_order o')
                ->join('pad_user u on u.id=o.u_id')
                ->join('pad_customer_quote q on q.id=o.q_id')
                ->where($where)
                ->count();
        $Page  = getAjaxPage($count,10);
        
        $data =M('pad_order o')
        		->field('o.id,o.sn,o.add_time as post_time,u.company,o.order_status,q.name')
                ->join('pad_user u on u.id=o.u_id')
                ->join('pad_customer_quote q on q.id=o.q_id')
                ->where($where)
                ->order('o.add_time,o.order_status')
                ->limit($Page['limit'])
                ->select();
        foreach ($data as &$v) {
            $v['post_time']=date("Y-m-d H:i:s",$v['post_time']);
        }
        $data_ = M('pad_order o')
        		->field('o.id,o.sn,o.add_time as post_time,u.company,o.order_status,q.name')
                ->join('pad_user u on u.id=o.u_id')
                ->join('pad_customer_quote q on q.id=o.q_id')
                ->order('o.add_time,o.order_status')
                ->where($where)
                ->select();
        foreach ($data_ as &$v) {
            $v['post_time']=date("Y-m-d H:i:s",$v['post_time']);
            if($v['order_status']==1){
            	$v['order_status']='未处理';
            }elseif($v['order_status']==2){
            	$v['order_status']='已受理';
            }elseif($v['order_status']==3){
            	$v['order_status']='已完结';
            }
        }
        if($_GET['tab']=='daochu'){
            foreach ($data_ as $k => $v) {
                $list['data'][$k]=['‘'.$data_[$k][sn],$data_[$k][name],$data_[$k][company],$data_[$k][post_time],$data_[$k][order_status]];
            }

            $list['key']=['订单编号','产品名称','供应商','提交时间','订单状态'];
            $list['width']=['A'=>'30','B'=>'50','C'=>'30','D'=>'30','E'=>'20'];
            exl($list);die;
        }
        $this->assign('data',$data);
        $this->assign('page',$Page['page']);// 赋值分页输出
        $this->display();
	}

	public function oper(){
		$id=I('id');
		//var_dump($id);die;
		if(M('pad_order')->where(array('id'=>$id))->save(array('order_status'=>2))!==false){
			$this->success('受理成功');die;
		}else{
			$this->error('受理失败');die;
		}
	}

	public function edit(){
		$id=I('id');
		if(M('pad_order')->where(array('id'=>$id))->data(array('order_status'=>3))->save()!==false){
			$this->success('操作成功');die;
		}else{
			$this->error('操作失败');die;
		}
	}

	public function info(){
		$id=I('id');
        $info=M('pad_workplan')->where(['o_id'=>$id])->select();
        $this->assign('info',$info);
		$data=M('pad_order o')
                ->field('o.*,q.*,o.id id,q.id quote_id')
				->join('pad_user u on u.id=o.u_id')
                ->join('pad_customer_quote q on q.id=o.q_id')
                ->order('o.add_time,o.order_status')
                ->where(array('o.id'=>$id))
                ->find();
        $layout					 = explode(',', $data['layout']);
        $houseAreaArray = explode('|', $data['area']);
        foreach ($houseAreaArray as $key => $value) {
			$houseArea[$key] = explode(',', $value);
		}
		foreach ($houseArea as $key => $val) {
			if ($key=='0') $name = '卧 室';
			else if ($key=='1') $name = '客 厅';
			else if ($key=='2') $name = '厨 房';
			else if ($key=='3') $name = '卫生间';
			else if ($key=='5') $name = '阁 楼';
			else if ($key=='6') $name = '地下室';
			if (!empty($houseArea[$key]['0'])) {
				foreach ($houseArea[$key] as $k => $v) {
					$houseString .= $name.($k+1).'：'.$v.'㎡ ';
				}
				$houseString .= "\r\n";
				$areaArray[] = $val;
			}
		}
		$fucai=$data['price']-$data['zprice'];
		$zdan=$data['zprice']/$data['build'];
		$fdan=($data['price']-$data['zprice'])/$data['build'];
		$priceStr="主材费用： ￥{$data['zprice']}元 单价：￥{$zdan}元/㎡ 辅材及施工费用：￥{$fucai}元  单价：￥{$fdan}元/㎡";
   		if($_GET['tab']=='daochu'){
   			$data_=json_decode($data["info"]);
   			foreach ($data_ as $k => $v) {
                $list['data'][$k]=['‘'.$v->sn,$v->name,$v->model,$v->brand,$v->origin,$v->price,$v->num];
            }
            $list['mergeCells']=['A1:H1','A2:H2','A3:H3'];
            $list['key']=['产品编码','名称','型号','品牌','产地','单价(元)','数量'];
            $list['width']=['A'=>'10','B'=>'20','C'=>'50','D'=>'20','E'=>'20','F'=>'10','G'=>'10','H'=>'10'];
            exls($list,$data['name'],$houseString,$priceStr);die;
        }
         	 	 	 	 	 	 	
        $this->assign('houseArea',$houseArea);
		$this->assign('id',$id);
        $this->assign('data',$data);
        $this->assign('list',json_decode($data['info']));
        $this->display();
	}

	public function remark(){
		$id=I('id');
		$data=M('pad_order_remark')->where(array('o_id'=>$id))->select();

        $this->assign('data',$data);
		$this->assign('id',$id);
		$this->display();
	}

	public function addRemark(){
		$data['u_id']=$_SESSION['admin_user']['user_id'];
		$data['u_name']=$_SESSION['admin_user']['user_name'];
		$data['addtime']=time();
		$data['content']=I('content');
		$data['o_id']=I('id');
		if(M('pad_order_remark')->add($data)!==false){
			$this->success('备注成功');die;
		}else{
			$this->error('备注失败');die;
		}
	}

    //房屋面积
    public function homeinfo(){
        $id=I('id');//订单id
        $types=I('types');
        switch ($types) {
            case '1':
                $title='房屋面积';
                break;
            case '2':
                $title='查看图纸';
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
        $data=M('pad_file')->where(['o_id'=>$id,'types'=>$types])->order('add_time desc')->select();
        foreach ($data as &$v) {
            $v['img']=C('Pad_rootPath').$v['img'];
        }
        $this->assign('oid',$id);
        $this->assign('title',$title);
        $this->assign('types',$types);
        $this->assign('data',$data);
        $this->display();
    }
    // **上传
     public function infoupload(){
        $oid=I('oid');//订单id
        $types=I('types');
        switch ($types) {
            case '1':
                $title='房屋面积';
                break;
            case '2':
                $title='查看图纸';
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
        $this->display();
    }
    //上传操作
    public function doUpload(){
        layout(false);
        if($_POST){
            $userId = $_SESSION['admin_user']['user_id'];
            //头像
             if(isset($_FILES['file'])&&$_FILES['file']['error']==0){
                $image = uploadOne('file','PadFile',C('IMG_THUMB_FACE'),'IMG_exts','Pads_rootPath');
                //var_dump($image,C('Img_rootPath'));die;
                if($image['code']!=1){
                     //头像上传失败
                     $this->error = $image['error'];
                     return false;
                }
                 $data['img'] = $image['images'];
             }
            $data['title'] = $_POST["title"];   //标题
            $data['content'] = $_POST["content"];   //内容
            $data['u_id'] = $userId;
            $data['oper'] = '2';
            $data['types'] = $_POST["types"];    //类别
            $data['o_id'] = $_POST["oid"];   //性别
            $rs = M('pad_file')->add($data); 
            if($rs!==false){
                if($_POST['types']==5){
                    $re=M('pad_order')->where(['id'=>$_POST["oid"]])->save(['status'=>4]);
                }
                $this->success('上传成功',U('homeinfo',['id'=>$_POST["oid"],'types'=>$_POST["types"]]));
            }else{
                $this->error('上传失败');
            }
        }
    }

    //分配施工人员
    public function allot(){
        $rid=I('rid');//工作名称
        $sid=I('sid');//当前状态
        $oid=I('oid');//订单id
        $orderinfo=M('pad_order')->where(['id'=>$oid])->find();
        if($orderinfo['status'] == ($sid-1)){
            $info=M('pad_user')->select();
            foreach ($info as $k => $v) {
                $arr=explode(',', $v['rose']);
                if(in_array($rid,$arr)){
                    $datas[$k]=$v;
                }
            }
            $this->assign('data',$datas);
            $this->assign('rid',$rid);
            $this->assign('sid',$sid);
            $this->assign('oid',$oid);
            $this->display();
        }else{
            layout(false);
            $this->error('请按照顺序分配人员！');
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
            $this->error('已分配，不可重复分配！',U('info',['id'=>$oid]));
        }
        //var_dump($info);die;
        $data=[
            'u_id'=>$_SESSION['admin_user']['user_id'],
            'worker'=>$info['real_name'],
            'phone'=>$info['tel'],
            'oper'=>'2',
            'workname'=>$sid,
            'o_id'=>$oid,
            'uid'=>$uid,
        ];
        $re=M('pad_workplan')->add($data);
        if($re!==false){
            if(M('pad_order')->where(['id'=>$oid])->save(['status'=>$sid])!==false){
                $this->success('提交成功',U('info',['id'=>$oid]));
            }else{
                $this->error('提交失败',U('allot',['oid'=>$oid,'rid'=>$rid,'sid'=>$sid]));
            }
        }else{
            $this->error('提交失败',U('allot',['oid'=>$oid,'rid'=>$rid,'sid'=>$sid]));
        }
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
            $userId = $_SESSION['admin_user']['user_id'];
            //头像
             if(isset($_FILES['file'])&&$_FILES['file']['error']==0){
                $oldName=$_POST['oldimg'];
                $image = uploadOne('file','PadFile',C('IMG_THUMB_FACE'),'IMG_exts','Pads_rootPath');
                if($image['code']!=1){
                     //头像上传失败
                     $this->error = $image['error'];
                     return false;
                }
                deleteImage($oldName,C('IMG_THUMB_FACE'),'Pads_rootPath');
                $data['img'] = $image['images'];
             }
            $data['title'] = $_POST["title"];   //标题
            $data['content'] = $_POST["content"];   //内容
            $data['u_id'] = $userId;
            $data['types'] = $_POST["types"];    //类别
            $data['o_id'] = $_POST["oid"];   //性别
            $rs = M('pad_file')->where(['id'=>$_POST['id']])->save($data); 
            if($rs!==false){
                $this->success('修改成功',U('homeinfo',['id'=>$_POST["oid"],'types'=>$_POST["types"]]));
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
            deleteImage($info['img'],C('IMG_THUMB_FACE'),'Pads_rootPath');
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
}