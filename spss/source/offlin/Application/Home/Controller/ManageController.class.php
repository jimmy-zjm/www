<?php
namespace Home\Controller;
use Think\Controller;
class ManageController extends BaseController {


	public function _initialize() {  
		parent::_initialize();
		$this->assign('now','1');
	}

	//客户管理
	public function index(){
		$name=!empty($_GET['name'])?$_GET['name']:'';
		$tel=!empty($_GET['tel'])?$_GET['tel']:'';	
        
        
       	$this->assign('name',$name);
       	$this->assign('tel',$tel);
		$this->display();
	}

	//客户管理列表分页
	public function managePage(){
		layout(false);
		$where['u_id'] = $_SESSION['dealerId'];
		if(!empty($_POST['search'])){
			$name=$_POST['search'];
			$map['name']  = array('like', "%$name%");
			$map['tel']  = array('like',"%$name%");
			$map['_logic'] = 'or';
			$where['_complex'] = $map;
		}
		
        $total              = M('pad_customer')->where($where)->count();
        $page               = getAjaxPage($total, 10);
        $data['list']  =M('pad_customer')->where($where)->order(' time desc ')->limit($page['limit'])->select();
        foreach ($data['list']  as $k => &$v) {
        	$v['time']=date('Y-m-d H:i:s',$v['time']);
        }
        $data['total']      = $total;
        
       // var_dump(M()->getLastSql());die();
       	$this->assign('name',$name);
       	$this->assign('data',$data);
       	$this->assign('page',$page['page']);// 赋值分页输出
		$this->display();
	}
	
	//客户管理查看备注信息
	public function checkinfo(){
		layout(false);
		
		$id=$_GET['id'];
		$data['list']= M('pad_remark r')->join('pad_user u on r.u_id=u.id')->where(array('r.c_id'=>$id))->order('addtime desc')->select();//$db->getAll("select r.*,u.name ,u.real_name from pad_remark r join xgj_furnish_dealer u on r.u_id=u.id where r.c_id=$id order by r.addtime desc");
		/*foreach ($data['list']  as $key => &$val) {
        	$val['addtime']=date('m-d H:i',$val['addtime']);
        }*/
		$html="<div class='container'>
			<p class='p-item'>查看备注<span class='closemask-btn'onclick='clo()' ></span></p>
			<textarea name='content' id='content'></textarea>
			<input type='hidden' name='c_id' id='c_id' value='{$id}'/>
			<div class='newaddbtn-box'>
				<a href='javascript:;' class='fl newadd-submit' onclick='sub()'>提交</a>
				<a href='javascript:;' onclick='clo()' class='fr newadd-cancel'>取消</a>
			</div>
			<p class='p-item'>修改日志</p>
			<ul class='remarks-log'>";
		foreach ($data['list'] as $k => $v) {
			$html.="<li>
					<p>{$v['u_name']}跟新了备注 <span>".date('m-d H:i',$v['addtime'])." </span></p>
					{$v['content']}
				</li>";			
		}    
               $html.="</ul>			
			</div>" ;
    echo $html;die;

	}

	//客户管理查看
	public function addRemark(){
		layout(false);
		$data['content']=I('content');
		if(empty($data['content'])){
			echo "备注不能为空";die;}
		$name=M('pad_user')->where(array('id'=>$_SESSION['dealerId']))->getField('real_name');
		$data['u_id']=$_SESSION['dealerId'];
		$data['u_name']=$name;
		$data['c_id']=$_POST['c_id'];
		$data['addtime']=time();
		$re=M('pad_remark')->add($data);
		if($re){
			echo 1;die;
		}else{
			echo 2;die;
		}
	}

	//删除客户管理系统
	public function del(){
		layout(false);
		$id=$_GET['id'];
		$rew = M('pad_customer_quote')->where(array('id'=>$id))->delete();
		if($rew){
			$this->success("删除成功",U('Manage/check',['c_id'=>$_GET['c_id']]));
		}else{
			$this->error("删除失败");
		}
	}



		//客户管理查看
	public function check(){
        if(!preg_match("/^[0-9]+$/", $_GET['c_id']) || $_GET['c_id']=='0'){
		   echo "<SCRIPT type='text/javascript'>alert('该客户不存在!!!');history.back();</SCRIPT>";exit;
		}
		$c_id=$_GET['c_id'];
		$addr = M('pad_customer')->where(['id'=>$c_id])->find();

		if ($addr['u_id'] != $_SESSION['dealerId']){
			$user = M('pad_user')->field('pid,level')->where(['id'=>$addr['u_id']])->find();
			if ($user['pid'] != $_SESSION['dealerId']) {
				$user = M('pad_user')->field('pid')->where(['id'=>$user['pid']])->find();
				if ($user['pid'] != $_SESSION['dealerId']) {
					echo "<SCRIPT type='text/javascript'>alert('该客户不存在!!!');history.back();</SCRIPT>";exit;
				}
			}
			$this->assign('tijiao',1);
		}
		$data['list']=M('pad_customer_quote')->where(['c_id'=>$c_id])->select(); 
		foreach ($data['list']  as $k => &$v) {
        	$v['time']=date('Y-m-d H:i:s',$v['time']);
        	$v['tijiao'] = M('pad_order_info')->where(['q_id'=>$v['id']])->count();
        }
		$this->assign('addr',$addr);
		$this->assign('data',$data);
		$this->assign('c_id',$c_id);
		$this->display();
	}



	public function order(){
		layout(false);
		$cqid = I('post.cqid');
		if(empty($cqid)) 
			$this->error('请选择系统!');
		$cqid = implode(',', $cqid);

		$count = M('pad_order_info')->where(['q_id'=>['in',$cqid]])->count();

		if (!empty($count)) {
			$this->error('请不要重复提交');
		}

		$customerQuoteData = M('pad_customer_quote')->where(['id'=>['in',$cqid]])->select();

		$c_id = $customerQuoteData['0']['c_id'];

		foreach ($customerQuoteData as $k => $v) {
			if ($v['c_id'] != $c_id) {
				$this->error('请选择同一客户下的系统进行提交!');
			}
		}

		/****************************/
		//查看是否是当前账户或子账户下的
		$u_id = $customerQuoteData['0']['u_id'];
		if ($u_id != $_SESSION['dealerId']) {
			$this->error('抱歉！只能提交您自己的客户');
		}		
		/****************************/

		$customerData = M('pad_customer')->where(['id'=>$c_id])->find();

		/***************************/
		//查询用户是否注册 没有注册的话执行注册 
		$uid = M('xgj_users')->where(['mobile_phone'=>$customerData['tel']])->getField('user_id');
		if(empty($uid)){
			$info=[
				'mobile_phone'=>$customerData['tel'],
				'user_name'=>$customerData['name'],
				'password'=>md5(substr($customerData['tel'],5,6).C(MD5_PASSWORD)),
				'reg_time'=>time(),
				'is_pad'=>'2',
			];

			$uid=M('xgj_users')->add($info);
			if($uid===false){
				$this->error('用户添加失败！');
			}
		}
		/***************************/


		/***************************/
		//添加订单
		$info_=M('pad_user')->where(['id'=>$_SESSION['dealerId']])->find();
		if($info_['level']==0){
			$where['u_id']=$_SESSION['dealerId'];
		}else{
			$where['id']=$info_['c_id'];
		}
		$data_=M('pad_company')->where($where)->limit(1)->find();

		$addOrderData = array(
			'sn'           => date('YmdHis').str_pad($microtime[1],4,0,STR_PAD_LEFT),//订单编号
			'add_time'     => time(),
			'order_status' => '1',
			'u_id'         => $_SESSION['dealerId'],
			'uid'          => $uid,
			'company'      => $data_['name'],
			'u_name'       => $info_['real_name'],
			'c_name'       => $customerData['name'],
			'tel'          => $customerData['tel'],
			);

		$model = new \Think\Model();
		$model->startTrans();  //开启事务
		$orderId = M('pad_order')->add($addOrderData);

		if ($orderId<1) {
			$this->error('订单提交失败！');
		}
		/***************************/


		/***************************/
		//添加订单详情
		foreach ($customerQuoteData as $k => $v) {
			$addOrderInfoData = $v;
			$addOrderInfoData['order_id'] = $orderId;
			$addOrderInfoData['info']     = $v['info'];
			$addOrderInfoData['q_id']     = $v['id'];

			$addData = M('pad_order_info')->create($addOrderInfoData);
			$re = M('pad_order_info')->add($addData);
			if ($re<1) {
				$model->rollback();  //添加失败事务回滚
				$this->error('订单提交失败！');
			}
		}

		$model->commit();//成功后提交事务
		$this->success('订单提交成功！');
		/***************************/
		
	}
	
}
