<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model {
	protected $trueTableName = 'xgj_users';

	//获取当前用户的所有地址
    public function getAddressByUserId($user_id){
    	$data=M('xgj_address')->field('*')->where("user_id = $user_id")->order(array('default'=>'desc','a_id'=>'asc'))->select();
    	return $data;
    }
    
    //统计当前用户的所有地址
    public function getAddressCountByUserId($user_id){
    	$num=M('xgj_address')->field('id')->where("user_id = $user_id")->count();
    	return $num;
    }

    //获取当前Id的所有地址
    public function getAddressById($id){
    	$data=M('xgj_address')->field('*')->where("a_id = $id")->find();
    	return $data;
    }

    //获取按照id排序的最早一条记录的id
    public function getNewIdByUserId($user_id){
    	$id=M('xgj_address')->where("user_id = $user_id")->order('a_id asc')->limit('1')->getField('a_id');
    	return $id;
    }

    //添加用户收货地址
    public function addAddressInfo($data){
    	$res=M('xgj_address')->data($data)->add();
    	return $res;
    }

    //更新用户收货地址
    public function updateAddressInfo($data,$where=array()){
    	$res=M('xgj_address')->where($where)->save($data);
    	return $res;
    }

    //删除用户收货地址
    public function delAddressInfo($where=array()){
    	$res=M('xgj_address')->where($where)->delete();
    	return $res;
    }

    /**
     * checkPassWordToModify    查询密码个用户是否匹配，用于修改密码
     * @param int $userId   用户ID
     * @param string $passWord  密码
     * @return array $passWordSelResult 查询到的结果
     */

    public function checkPassWordToModify($userId, $passWord){
        $data=M('xgj_users')->field('user_id')->where("`user_id`='{$userId}' AND `password`='{$passWord}'")->find();
    	return $data;
    }

    /**
     * PassWordToModify     执行修改密码
     * @param int $userId   用户ID
     * @param string $oldPassWord   原始密码
     * @param string $newPassWord   新密码
     * @return resource 修改的结果
     */

    public function PassWordToModify($userId, $oldPassWord, $newPassWord){
		$data['password']=$newPassWord;
		$res=M('xgj_users')->where("user_id='".$userId."' AND password='".$oldPassWord."'")->save($data);
        return $res;
    }

     /**
     * userInfoList     个人信息查询
     * @param int $userId   用户ID
     * @return array $list  查询到的用户信息
     */

    public function userInfoList($userId){
		$data=M('xgj_users')->field('`user_id`,`email`,`user_name`,`face`,`sex`,`birthday`,`identity_card`,`mobile_phone`,`alias`,`addr`,`monthly_profit`,`education_status`,`real_name`,`work`')->where("`user_id` = '{$userId}'")->find();
    	return $data;
    }

    /**
     * * userInfoEdit     个人信息修改
     * @param string $alias             昵称
     * @param string $mobilePhone       手机号码
     * @param string $email             邮箱
     * @param string $addr              地址
     * @param string $identityCard      身份证
     * @param string $birthday          生日
     * @param string $sex               性别
     * @param string $monthlyProfit     月收入
     * @param string $educationStatus   教育程度
     * @param int $userId   用户ID
     * @return int  mysql_affected_rows 执行结果(受影响的行数)
     */
    public function userInfoEdit($data,$userId){
    	$res=M('xgj_users')->where("user_id=$userId")->save($data);
    	return $res;
    }


	// couponStatusCount    查询个人优惠券金额
    
    function totalUserCoupons($where){
        
       $data=M('xgj_users')->where($where)->getField('coupon');
        return $data;
    }
	 /**
     * couponInfo    查询优惠券流水表用户的使用金额
     * @param string $where   查询条件
     * @return array $couponStatusCountResult   查询到的个状态优惠券数量
     */
    function couponInfo($where){
		$data=M('xgj_coupon_info')->where($where)->select();
        $price=0;
        foreach($data AS $k=>$v){
            $price+=$v['use_money'];
        }

        return $price;
    }

	//查询个人优惠券使用记录
    function couUserInfo($where,$limit){
		$data=M('xgj_coupon_info')->where($where)->limit($limit)->order('id desc')->select();
		foreach($data as $key=>&$val){
			if($val['class_id']==1){
				$val['order_code']=M('xgj_furnish_order_info')->where(array('order_id'=>$val['order_id']))->getField('order_code');
				$val['goods_name']=M('xgj_furnish_order_detail')->where(array('order_id'=>$val['order_id']))->getField('quote_name');
			}
		}
        return $data;
    }
	//执行兑换优惠券操作
    public function activationCoupon($code,$pass,$uid){
        
		$data = M('xgj_coupon')->where(array('coupon_number'=>$code))->find();
		//VAR_DUMP($data);die();
        if (empty($data)) 
			return '无此优惠券！';
		else if($data['status']=='1')
			return '该优惠券已兑换！';
		else if($data['coupon_password']!=$pass)
			return '优惠券密码不对';
		
         $updateDate = array(
				'user_id'       => $uid,
				'status'        => '1',
				'activate_time' => time(),
				'is_status'     => '0'
          );

         $return =M('xgj_coupon')->where(array('id'=>$data['id']))->data($updateDate)->save();

         if (!empty($return)) {
                //$coupon = $mysql->getRow("select user_id, coupon ,coupon_number from xgj_users where user_id = '{$uid}'");
				$coupon = M('xgj_users')->where(array('user_id'=>$uid))->find();
                $total = $coupon['coupon'] + $data['discount_amount'];
				/*if(empty($coupon['coupon_number'])){//优惠券存入用户表 用于绑定地推人员
					 $res =  M('xgj_users')->where(array('coupon_number'=>$data['coupon_number']))->field('user_id')->find();//查询用户H5注册绑定优惠券但未激活优惠券的用户是否存在
					 if(empty($res)) {
						$userdata['coupon_number']=$data['coupon_number'];
					 }					 
				}*/
				$userdata['coupon']=$total;
                $result =M('xgj_users')->where(array('user_id'=>$uid))->data($userdata)->save(); 
                if (!empty($result))  return '兑换成功！' ;
                else return '兑换失败！';
         }else
                return '兑换失败1！';
    }
	//查询优惠券兑换明细
    public function couponList($where,$limit=''){
        $data=M('xgj_coupon')->where($where)->limit($limit)->order('activate_time desc')->select();
        return $data;
    }
	//查询积分使用明细
	public function integral($where,$limit=''){
		$data=M('xgj_user_integral')->where($where)->limit($limit)->order('integral_id desc')->select();
		foreach($data as $key=>&$val){
			if($val['class']=='1'){
				$val['order_code']=M('xgj_furnish_order_info')->where(array('order_id'=>$val['order']))->getField('order_code');
				$val['goods_name']=M('xgj_furnish_order_detail')->where(array('order_id'=>$val['order']))->getField('quote_name');
			}
		}
        return $data;
	}
	//获取用户积分总额
	public function getuserintl($where){
		$data=M('xgj_users')->where($where)->getField('integral');		
        return $data;
	}
    /*获取用户的优惠券金额*/ 
    public function getCouponByUserId($user_id){
        $coupon=M('xgj_users')->where(array("user_id"=>$user_id))->getField('coupon');
        return $coupon;
    }
    
    /*修改用户优惠券金额*/
    public function editconpon($coupon,$user_id){
        $price = M('xgj_users')->where(array("user_id"=>$user_id))->setField('coupon',$coupon);
        return $price;
    }
	 /**
     * goodsdata   表示商品信息
     * @param int $where   查询条件
     * @return array $myOrderSelResult  查询到的我的商品总详细
     */
    
    function goodsdata($where,$limit=''){
		$data=M('xgj_furnish_order_info')->field('order_id,order_code,add_order_time,order_status,goods_amount,adjust_goods_amount,pay_status,pay_method')->where($where)->order('order_id desc')->limit($limit)->select();
		return $data;    
    }
	 /**
     * goodsdata   表示商品信息
     * @param int $where   查询条件
     * @return array $myOrderSelResult  查询到的我的商品总详细
     */
    
      function homeorder($where){
		$data=M('xgj_furnish_order_info')->field('order_id,order_code,order_status,goods_amount,adjust_goods_amount,pay_status,pay_method')->where($where)->find();
		return $data;    
    }
	  

    //   countPlan  查询系统名称    
    function countPlan($where){
       $data=M('xgj_furnish_order_detail a')->field('a.detail_id,a.quote_name')->join('xgj_furnish_order_info b   ON  a.order_id=b.order_id')->join('xgj_furnish_quote c  ON  a.quote_id=c.quote_id')->where($where)->select();
    	return $data;         
    }
	   

     //  constructPlan     表示查询施工计划信息    
    function constructPlan($where){
       $data=M('xgj_furnish_order_construct')->field('start_time,end_time,task_work,status,task_name,assigner,check_status,check_time,check_proof')->where($where)->select();
    	return $data;
	}

	//表示文件区域信息
    function file($where){
    	$data=M('xgj_furnish_order_info a')->field('b.*,a.order_code')->join('xgj_furnish_order_file b on a.order_id =b.order_id')->where($where)->select();
    	return $data;    		
    }


	//Productfile  表示产品手册信息
    function   Productfile($where){
        $data=M('xgj_furnish_order_file')->where($where)->select();
    	return $data;  
    }

	//商品评价列表
    public function evaluateShow($where=null,$limit=null){
		$data=M('xgj_furnish_order_info a')->field('a.add_order_time,b.status,b.detail_id,c.img,b.quote_name')->join('xgj_furnish_order_detail b ON a.order_id = b.order_id')->join('xgj_furnish_quote c ON b.quote_id = c.quote_id')->where($where)->limit($limit)->select();
        return $data;
    }



	public function hEvaluateRow($where){
        $data=M('xgj_furnish_order_detail a')->field('a.detail_id,a.quote_name,b.order_id,a.quote_id,b.house_type,b.total_area,b.house_city')->join('left join xgj_furnish_order_info b on a.order_id =b.order_id')->where($where)->find();
        //查询经销商调整后的房屋信息
	
		if($data['order_status']=='5'){	
			 $data1=M('xgj_dealer_adjust_info')->field('total_area')->where(array('order_code'=>$data['order_code']))->find();
			//$data['house_layout']  =   $data1['house_layout'];
			$data['total_area']       =    $data1['total_area'];
			//$data['area']               =   $data1['type_area'];
			//$data['house_type']     =   $data1['house_type'];
			//$data['people']            =    $data1['people'];
		}
        return $data;
    }



	//插入表数据
    public function addtable($table,$data){
        $re = M($table)->add($data,array(),true);
        return $re;
    }



	//家居订单评论之后更改订单状态
	public function updateOrderDetail($detailid,$orderid){
     
        $data = array(
            'status'    => 1,
            'time'      => time()
            );
		$re=M('xgj_furnish_order_detail')->where(array('detail_id'=>$detailid))->save($data);

		//查询订单内商品是否全部评价完毕
      	$data=M('xgj_furnish_order_detail')->field('status')->where(array('order_id'=>$orderid))->select();
        foreach ($select as $key => $value) {
              if ($value["status"]==0) {
                  $aaa = 1;  
              }
        }
        //如果全部评价完毕更改订单状态
        if (empty($aaa)) {
            $save1 = array('is_comment'=>1);
			M('xgj_furnish_order_info')->where(array('order_id'=>$orderid))->save($save1);
        }
        return $re;
    }


	public function commentRow($id,$userid){

        $re['comment'] =M('xgj_furnish_comment')->where(array('goods_id'=>$id,'user_id'=>$userid,'status'=>'1'))->find();
        $re['quote'] = $this->hEvaluateRow(array('a.status'=>'1','user_id'=>$userid,'a.detail_id'=>$id));		
        return $re;
    }


	 //欧洲建材订单
    public function orderInfoAllList($where,$limit){

        $data = M('xgj_eu_order')->field('id,sn,add_time,total_price,deal_price,order_status,is_pay,total_goods_num')->where($where)->order('id DESC')->limit($limit)->select();

        foreach ($data as $k=> $v) {
            $data[$k]['integral']=M('xgj_user_integral')->where(array('order'=>$v['id'],'user_id'=>$v['user_id'],'status'=>'1','class'=>'2'))->getField('integral');
            $data[$k]['coupon']=M('xgj_coupon_info')->where(array('order_id'=>$v['id']))->getField('use_money');
            $data[$k]['detail']=M('xgj_eu_order_goods a')->field('a.*,a.id id,b.pro_info,c.id s_id,c.split_sn,c.order_status')->join('xgj_eu_goods_new b on a.goods_id=b.id')->join('xgj_eu_split_order c on c.detail_id=a.id')->where(array('a.order_id'=>$v['id']))->select();
        }        

        return $data;
    }
    //订单总数
    function count_order($where){
        $result=M('xgj_eu_order')->where($where)->count("id");
        return $result;
    }


	//建材订单详情
	public function euOrderDetails($where){

        $row = M('xgj_eu_split_order a')->field('order_id,order_status')->where($where)->find();    
        if ($row['order_status']=='0') {
            $list['splitAll'] = M('xgj_eu_split_order a')->field('a.*,b.*,c.goods_sn,a.id id')->join('xgj_eu_order_goods b on a.detail_id=b.id')->join('xgj_eu_goods_new c on b.goods_id=c.id')->where(array('a.order_id'=>$row['order_id']))->select();
            $list['split'] = $list['splitAll']['0'];
        }else{
            $list['split'] = M('xgj_eu_split_order a')->field('a.*,b.*,c.goods_sn,a.id id')->join('xgj_eu_order_goods b on a.detail_id=b.id')->join('xgj_eu_goods_new c on b.goods_id=c.id')->where($where)->find();
        }
  
        $list['express'] = M('xgj_eu_express')->where(array('split_order_id'=>$list['split']['id']))->find();
        $list['order'] = M('xgj_eu_order')->where(array('id'=>$list['split']['order_id']))->find();
        $list['integral'] =  M('xgj_user_integral')->where(array('order'=>$list['split']['order_id'],'status'=>'1','class'=>'2'))->getField('integral');
        $coupon = M('xgj_coupon_info')->where(array('order_id'=>$list['split']['order_id'],'class_id'=>'2'))->getField('use_money');
        
        return $list;
    }


	//待商品评价(欧团)
    public function euEvaluateList($where=null,$limit=null){
        $data =  M('xgj_eu_order a')->field('b.goods_image,b.goods_title,b.id,b.status,a.pay_time')->join('right join xgj_eu_order_goods b on a.id=b.order_id')->where($where)->order('a.pay_time DESC')->limit($limit)->select();
        return $data;
    }


     public function euGoodsRow($where){
        $data = M('xgj_eu_order_goods a')->field('a.*,b.pay_time,b.sn')->join('xgj_eu_order b on a.order_id = b.id')->where($where)->find();
        return $data;
    }


	//建材订单评价结束处理
    public function updataEuStatus($id,$uid,$orderId){
        $data = array(
            'status'    => 1,
            'time'      => time()
            );
        $re =M('xgj_eu_order_goods')->where(array(  "id" => $id , "user_id" =>$uid))->save($data);
        $data_ = array(
            'order_status'    => 5,
            );
         $re = M('xgj_eu_split_order')->where(array(  "detail_id" => $id , "user_id" =>$uid))->save($data_);

        //查询是否全部评价完毕
        $sql = "select * from xgj_eu_order_goods where order_id='{$orderId['order_id']}' and status='0' and user_id = '{$_SESSION['userId']}'";
        $orderList = M('xgj_eu_order_goods')->where(array('order_id'=>$orderId,'status'=>'0','user_id'=>$uid))->select();

        if (empty($orderList)) {
            //全部完毕后更改订单状态
            $update = array(
                'order_status'  => '5'
            );

            $re = M('xgj_eu_order')->where(array(  "id" => $orderId , "user_id" =>$uid))->save($update);

            if (!empty($re)) {

                //查询订单应付价格
                $orderPrice = M('xgj_eu_order')->where(array('id'=>$orderId,'user_id'=>$uid))->getField('deal_price');

                //添加积分
                $addData = array(
                    'user_id'   =>$uid,
                    'user_name' =>$_SESSION['user']['userName'],
                    'order'   =>$orderId,
                    'integral'  =>floor($orderPrice['deal_price']*0.05),
                    'time'      =>time(),
                    'status'  =>'2',
                    'class'     =>'2',
                );

                $re =D('xgj_user_integral')->data($addData)->add();

                if (floor($orderPrice['deal_price']*0.05)=='0') 
                    return $re;                

                if ($re) 
                    $re = M('user')->where(array('user_id'=>$uid))->setInc('integral',floor($orderPrice['deal_price']*0.05));
                
            }
        }
        return $re;
    }



	
	public function euComment($id,$uid){
       
        $re['comment'] =  M('xgj_eu_comment')->where(array('order_goods_id'=>$id,'status'=>'1','user_id'=>$uid))->find();

        if (!empty($re['comment']['images'])) $re['images'] = explode('|', $re['comment']['images']);

        return $re;
    }




	
	public function getafterService($where='',$limit=''){
       
        $rs=M('xgj_user_problem')->where($where)->limit($limit)->select();//$this->m->getAll("select * from xgj_user_problem where user_id=$user_id $where $limit");
        foreach ($rs as $k=>$v){
            $arr=explode('-',$v['quote_id']);
			foreach($arr as $ke=>$va){
                $rs[$k]['quote_name'].=M('xgj_furnish_quote')->where(array('quote_id'=>$va))->getField('quote_name');//$this->m->getOne("select quote_name from xgj_furnish_quote where quote_id=$va");
				$rs[$k]['quote_name'].='</br>';
            }
          
        }
        return $rs;
    }


    public function getSorder($class,$limit){
      $map['o.class_id'] = $class;
      $map['o.user_id']  = $_SESSION['user']['userId'];
      $map['o.is_pay']   = 1;
      if ($class == 9) {
        $re = M('xgj_s_order_goods g')->join('xgj_s_order o on g.order_id = o.id')->join('xgj_s_upkeep u on u.id=g.goods_id')->field('g.*,o.sn,o.pay_time,u.period')->where($map)->order('o.pay_time desc')->limit($limit)->select();
      }else if($class == 8){
        $re = M('xgj_s_order_goods g')->join('xgj_s_order o on g.order_id = o.id')->join('xgj_s_consumable c on c.id=g.goods_id')->field('g.*,o.sn,o.pay_time,c.product_name')->where($map)->order('o.pay_time desc')->limit($limit)->select();
      }
      return $re;
    }


    /**
     * centerSelInfo            表示在用户中心页面查询用户信息
     * @param string $userId    用户Id
     * @return array $centerSelResult     用户的信息
     */
     public function centerSelInfo($userId){
        //查询用户信息的sql语句
        $info=M('xgj_users')->field("`user_id`,`user_name`,`face`,`user_money`,`integral`,`coupon`")->where(array('user_id'=>$userId))->find();
        return $info;
    }

    public function selectConcern($userId){
         $data = M('xgj_concern')->where(array('user_id'=>$userId))->order('c_id')->limit('0,10')->select();
         return $data;
    }
}