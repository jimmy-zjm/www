<?php
namespace Admin\Model\Dealer;
use \Think\Model;
/**
 * 订单model
 */
class OrderModel extends Model{
	protected $trueTableName='xgj_furnish_order_info';
    
    
    /**
     * 新订单分页
     * @param unknown $page         
     * @return multitype:unknown
     */
    public function new_order_list(){
        //分页
        $total        = $this->where(array('allot_status' =>0))->count();
        $page         = getPage($total, C('FURNISH_ORDER_PAGE_SIZE'),array('tab' =>1));
        $data['new_order_page'] = $page['page'];
        
        // 商品数据
        $data['new_order_list'] =$this->field('*')->where(array('allot_status' =>0))->order('add_order_time asc')->limit($page['limit'])->select();

        return $data;
    }

    /**
     * 订单统计分页
     * @param unknown $page         
     * @return multitype:unknown
     */
    public function show_statistics_list(){
        //拼凑条件
        $map = array();
        if(isset($_GET['statistics'])){
            $keyword     = I('keyword');
            $start_time  = strtotime(I('start_time').' 00:00:00');
            $end_time    = strtotime(I('end_time').' 23:59:59');
            $d_province  = I('d_province');
            
            if(!empty($keyword)){
                $map['order_code|mobile_phone|order_merchandiser'] = array('like',"%{$keyword}%");
            }
            if(!empty($start_time) && !empty($end_time)){
                $map['add_order_time'] = array('between',array($start_time,$end_time));
            }
            if(!empty($d_province)){
                $map['d_province'] = array('eq',$d_province);
            }
        }
        //分页
        $data['show_statistics_total']        = $this->where($map)->count();
        $page         = getPage($data['show_statistics_total'], C('FURNISH_ORDER_PAGE_SIZE'),array('tab' =>2 ));
        $data['show_statistics_page'] = $page['page'];
        
        // 订单统计数据
        $data['show_statistics_list'] =$this->field('*')->where($map)->order('add_order_time asc')->limit($page['limit'])->select();
        foreach ( $data['show_statistics_list'] as $k => $v ) {
            if ($v ['allot_status'] == 0) {
                $data['show_statistics_list'] [$k] ['d_dealer'] = 0;
            } else {
                $data['show_statistics_list'] [$k] ['d_dealer'] = M('xgj_furnish_dealer')->where("d_id = {$v['d_id']}")->find();
            }
        }
        //订单统计总金额
        $data['show_statistics_price'] =$this->SUM('goods_amount');

        return $data;
    }

    /**
     * 退货订单统计
     * @param unknown $page         
     * @param unknown $where            
     * @param string $search            
     * @return multitype:
     */
    public function show_refund_list(){
        //拼凑条件
        $map = array();
        if(isset($_GET['refund'])){
            $refund_searchs     = I('refund_keyword');
            $refund_start_time  = strtotime(I('refund_start_time'));
            $refund_end_time    = strtotime(I('refund_end_time'));
            $d_province  = I('d_province');
            
            $refund_wheres="";
            if(!empty($refund_start_time) && !empty($refund_end_time)){
                $refund_wheres.=" and r.refund_time between $refund_start_time and $refund_end_time ";
            }
            if(!empty($refund_searchs)){
                $refund_searchs=" where d_company like '%$refund_searchs%' ";
            }
        }
        //分页
        if (empty ( $refund_searchs )) {
            $data['show_refund_total']= M()->query("SELECT COUNT(*) as total FROM xgj_furnish_order_refund r where r.refund_status=1 {$refund_wheres} ");
        }else{
            $data['show_refund_total']= M()->query("SELECT count(*) as total FROM xgj_furnish_order_refund r where r.d_id in (select d_id from xgj_furnish_dealer {$refund_searchs}) and r.refund_status=1 ");
        }
        $page         = getPage($data['show_refund_total'][0]['total'],C('FURNISH_ORDER_PAGE_SIZE'),array('tab' =>3,'tab_child'=>5 ));
        $data['show_refund_page'] = $page['page'];
        
        // 退货订单统计数据
        if (empty ( $refund_searchs )) {
            $data['show_refund_list'] =M()->query("SELECT * FROM
            xgj_furnish_order_refund r join
            (xgj_furnish_order_info i ,xgj_furnish_dealer d ,xgj_furnish_order_detail de)
            on (r.detail_id=de.detail_id and r.d_id=d.d_id and de.order_id=i.order_id)
            where r.refund_status=1 {$refund_wheres} order by r.refund_time desc limit {$page['limit']}");
        }else{
            $data['show_refund_list'] =M()->query("SELECT * FROM
            xgj_furnish_order_refund r join
            (xgj_furnish_order_info i ,xgj_furnish_dealer d ,xgj_furnish_order_detail de)
            on (r.detail_id=de.detail_id and r.d_id=d.d_id and de.order_id=i.order_id)
            where r.refund_status=1 and r.d_id in (select d_id from xgj_furnish_dealer {$refund_searchs}) order by r.refund_time desc limit {$page['limit']}");
        }
        // 退货订单统计总金额
        if (empty ( $refund_searchs )) {
            $data['show_refund_price'] = M()->query("SELECT sum(r.refund_price) as price FROM xgj_furnish_order_refund r where r.refund_status=1 {$refund_wheres}");
        } else {
            $data['show_refund_price'] = M()->query("SELECT sum(r.refund_price) as price FROM xgj_furnish_order_refund r where r.refund_status=1 and r.d_id in (select d_id from xgj_furnish_dealer {$refund_searchs})");
        }

        return $data;
    }

    /**
     * 补货订单统计
     * @param unknown $page         
     * @param unknown $where            
     * @param string $search            
     * @return multitype:
     */
    public function show_add_list(){
        //拼凑条件
        $map = array();
        if(isset($_GET['add'])){
            $add_searchs     = I('add_keyword');
            $add_start_time  = strtotime(I('add_start_time'));
            $add_end_time    = strtotime(I('add_end_time'));
            
            $add_wheres="";
            if(!empty($add_start_time) && !empty($add_end_time)){
                $add_wheres.=" and r.refund_time between $add_start_time and $add_end_time ";
            }
            if(!empty($add_searchs)){
                $add_searchs=" where d_company like '%$add_searchs%' ";
            }
        }
        //分页
        if (empty ( $add_searchs )) {
            $data['show_add_total']= M()->query("SELECT count(*) as total FROM xgj_furnish_order_refund r where r.refund_status=2 {$add_wheres}");
        }else{
            $data['show_add_total']= M()->query("SELECT count(*) as total FROM xgj_furnish_order_refund r where r.d_id in (select d_id from xgj_furnish_dealer {$add_searchs}) and r.refund_status=2 ");
        }
        $page         = getPage($data['show_add_total'][0]['total'], C('FURNISH_ORDER_PAGE_SIZE'),array('tab' =>3,'tab_child'=>6 ));
        $data['show_add_page'] = $page['page'];
        
        // 补换货订单统计数据
        if (empty ( $add_searchs )) {
            $data['show_add_list'] =M()->query("SELECT * FROM
            xgj_furnish_order_refund r join
            (xgj_furnish_order_info i ,xgj_furnish_dealer d ,xgj_furnish_order_detail de)
            on (r.detail_id=de.detail_id and r.d_id=d.d_id and de.order_id=i.order_id)
            where r.refund_status=2 {$add_wheres} order by r.refund_time desc limit {$page['limit']}");
        }else{
            $data['show_add_list'] =M()->query("SELECT * FROM
            xgj_furnish_order_refund r join
            (xgj_furnish_order_info i ,xgj_furnish_dealer d ,xgj_furnish_order_detail de)
            on (r.detail_id=de.detail_id and r.d_id=d.d_id and de.order_id=i.order_id)
            where r.refund_status=2 and r.d_id in (select d_id from xgj_furnish_dealer {$add_searchs}) order by r.refund_time desc limit {$page['limit']}");
        }
        // 补换货订单统计总金额
        if (empty ( $add_searchs )) {
            $data['show_add_price'] = M()->query("SELECT sum(r.refund_price) as price FROM xgj_furnish_order_refund r where r.refund_status=2 {$add_wheres}");
        } else {
            $data['show_add_price'] = M()->query("SELECT sum(r.refund_price) as price FROM xgj_furnish_order_refund r where r.refund_status=2 and r.d_id in (select d_id from xgj_furnish_dealer {$add_searchs})");
        }
        
        return $data;
    }
    
    /**
     * 伙伴自购订单统计
     * @param unknown $page         
     * @param unknown $where            
     * @param string $search            
     * @return multitype:
     */
    public function show_self_buy_list(){
        //拼凑条件
        $map = array();
        if(isset($_GET['self_buy'])){
            $first_audit_status   = I('first_audit_status/d');
            $self_buy_start_time  = strtotime(I('self_buy_start_time'));
            $self_buy_end_time    = strtotime(I('self_buy_end_time'));
            $d_province  = I('d_province');
            
            $conditions="";
            if(!empty($self_buy_start_time) && !empty($self_buy_end_time)){
                $conditions.=" and r.refund_time between $self_buy_start_time and $self_buy_end_time ";
            }
            if(!empty($first_audit_status)){
                $conditions.=" and r.first_audit_status =$first_audit_status ";
            }
        }
        //分页
        $data['show_self_buy_total']= M()->query("SELECT count(*) as total FROM xgj_furnish_order_refund r where r.refund_status=3 {$conditions} ");
        $page         = getPage($data['show_self_buy_total'][0]['total'], C('FURNISH_ORDER_PAGE_SIZE'),array('tab' =>3,'tab_child'=>7 ));
        $data['show_self_buy_page'] = $page['page'];
        
        // 伙伴自购订单订单统计数据
        $data['show_self_buy_list'] =M()->query("SELECT * FROM xgj_furnish_order_refund r join
        (xgj_furnish_order_info i ,xgj_furnish_dealer d,xgj_furnish_order_detail de)on (r.detail_id=de.detail_id and r.d_id=d.d_id and de.order_id=i.order_id) where r.refund_status=3 {$conditions} order by r.refund_time desc limit {$page['limit']}");
        // 伙伴自购订单订单统计总金额
        $data['show_self_buy_price'] = M()->query("SELECT sum(r.refund_price) as price FROM xgj_furnish_order_refund r where r.refund_status=3 {$conditions}");
        
        return $data;
    }

    /**
     * 我的订单分页
     * @param unknown $page         
     * @return multitype:unknown
     */
    public function show_my_list(){
        //拼凑条件
        $map = array();
        if(isset($_GET['my'])){
            $my_keyword     = I('my_keyword');
            $my_schedule_status  = I('my_schedule_status/d');
            $my_order_code  = I('my_order_code');

            $my_condition="";
            if(!empty($my_schedule_status)){
                $my_condition.=" and i.schedule_status = $my_schedule_status ";
            }
            if(!empty($my_order_code)){
                $my_condition.=" and i.order_code = $my_order_code ";
            }
            if(!empty($my_keyword)){
                $my_condition.=" and d.d_company like '%$my_keyword%' ";
            }
        }
        //分页
        $data['show_my_total']= M()->query("SELECT count(*) as total FROM xgj_furnish_order_info i join xgj_furnish_dealer d on i.d_id=d.d_id where i.allot_status = 1 and i.admin_id={$_SESSION['admin_user']['user_id']} $condition");
        $page         = getPage($data['show_self_buy_total'][0]['total'], C('FURNISH_ORDER_PAGE_SIZE'),array('tab' =>4));
        $data['show_my_page'] = $page['page'];
        
        // 我的订单统计数据
        $data['show_my_list'] =M()->query("SELECT i.*,d.* FROM xgj_furnish_order_info i 
            join xgj_furnish_dealer d on i.d_id=d.d_id where i.allot_status = 1 and i.admin_id={$_SESSION['admin_user']['user_id']} $condition order by i.allot_time asc limit {$page['limit']}");
        
        return $data;
    }

    /**
     * 根据id查询一条服务商订单信息
     * @return array
     */
    public function dealer_order_id($order_id) {
        
        $result = $this->where("order_id={$order_id}")->find();
        
        return $result;
    }

    /**
     * 获取服务商信息
     * @return array
     */
    public function get_dealer_info() {
        $result = M('xgj_furnish_dealer')->group('d_province')->select();
        //var_dump($result);exit;
        return $result;
    }
    
    /**
     * 根据城市和省份查找服务商信息
     * @param unknown $province
     *          省份
     * @param unknown $city         
     * @return multitype:
     */
    function get_dealer_list($province = '', $city = '') {
        //var_dump($province,$city);exit;
        if ($province == '') {
            $sql = "d_city like'%{$city}%'";
        } else if ($city == '') {
            $sql = " d_province like'%{$province}%' ";
        } else {
            $sql = " d_province like'%{$province}%' and d_city like'%{$city}%' ";
        }
        $result = M('xgj_furnish_dealer')->where($sql)->select();
        foreach ( $result as $k => $v ) {
            $result [$k] ['count'] = M('xgj_furnish_order_info')->where("d_id={$v['d_id']}")->count('order_code');
        }
        return $result;
    }
    
    /**
     * 根据id获取订单金额
     * @param unknown $order_id
     * @return Ambigous <multitype:, mixed>
     */
    function get_dealer_order_price($order_id){
    	//$sql = "SELECT i.goods_amount FROM xgj_furnish_order_info i where i.order_id = $order_id";
    	$result = $this->where("order_id = $order_id")->getField('goods_amount');
        //var_dump($result['goods_amount']);exit;
    	return $result;
    }
    
    /**
     * 根据id获取清单列表
     * @param unknown $order_id
     * @return Ambigous <multitype:, mixed>
     */
    function get_dealer_order_stuff_list($detail_id,$stuff,$quote,$batch){
    	$result =M('xgj_furnish_order_detail')->where("detail_id = $detail_id")->getField('stuff_goods');
        if(empty($result)){
            $stuff_list=array();
        }else{
            $stuff_goods = explode ( ';', $result );
            $goods_sn = explode ( ',', $stuff_goods[0] );
            $goods_num=explode ( ',', $stuff_goods[1] );
            $goods_batch=explode ( ',', $stuff_goods[2] );
            $stuff_list=array();

            // $goods_list = M('xgj_furnish_goods')->query("select goods_sn,goods_img,goods_name,goods_model,goods_unit,goods_brand,shop_price from __TABLE__ where goods_lv=$stuff and goods_sn IN ($stuff_goods[0]) order by field(goods_sn,$stuff_goods[0])");
            $goods_list = M('xgj_furnish_goods')->query("select goods_sn,goods_img,goods_name,goods_model,goods_unit,goods_brand,shop_price from __TABLE__ where goods_sn IN ($stuff_goods[0]) order by field(goods_sn,$stuff_goods[0])");

            foreach ($goods_sn as $k => $v){
                    $stuff_list[$k]['num']=$goods_num[$k];
                    $stuff_list[$k]['batch']=$goods_batch[$k];
                    $stuff_list[$k]['goods_sn']=$v;
                if ($goods_batch[$k]==$batch) {
                    foreach ($goods_list as $key => $val) {
                        if ($v==$val['goods_sn']) {
                            $stuff_list[$k]['list']['0']=$val;
                        }
                    }
                }
            }
            
            // foreach ( $stuff_list as $k => $v ) {
            //     $stuff_list [$k] ['list'] = M('xgj_furnish_goods')->field('goods_img,goods_name,goods_model,goods_unit,goods_brand,shop_price')->where("goods_sn={$v['goods_sn']} and goods_lv=$stuff")->select();
            //     $stuff_list [$k] ['batch'] = M('xgj_quote_child_list')->where("goods_sn='{$v['goods_sn']}' and quote_id = '{$quote['quote_id']}' and level = '{$quote['level']}'")->getField('batch');
            // }
            // echo '<pre>';
            // var_dump($list);exit;
        }
    	
    	return $stuff_list;
    }
    
    /**
     * 根据id获取退换货自购清单列表
     * @param unknown $detail_id
     * @return Ambigous <multitype:, mixed>
     */
    function get_dealer_order_refund_list($detail_id,$stuff,$refund_status){
    	$sql = "SELECT r.refund_goods,r.refund_msg,r.refund_id
    	FROM xgj_furnish_order_refund r
    	where r.detail_id = $detail_id and r.refund_status=$refund_status and r.level=$stuff";
        //echo $sql;
    	$result = M()->query($sql);
        //var_dump($result);exit;
        if (empty($result)) {
            $stuff_list=array();
        }else{
            $stuff_goods = explode ( ';', $result[0]['refund_goods'] );
            //var_dump($stuff_goods);exit;
            $goods_sn = explode ( ',', $stuff_goods[0] );
            $goods_num=explode ( ',', $stuff_goods[1] );
            //var_dump($goods_sn);exit;
            $stuff_list=array();
            foreach ($goods_sn as $k => $v){
                $stuff_list[$k]['num']=$goods_num[$k];
                $stuff_list[$k]['goods_sn']=$v;
                $stuff_list[$k]['refund_msg']=$result[0]['refund_msg'];
                $stuff_list[$k]['refund_id']=$result[0]['refund_id'];
            }
            foreach ( $stuff_list as $k => $v ) {
                $stuff_list [$k] ['list'] = M()->query ("select goods_img,goods_name,goods_model,goods_unit,goods_brand,shop_price from xgj_furnish_goods where goods_sn={$v['goods_sn']} and goods_lv=$stuff" );
            }
        }
    	
        //var_dump($stuff_list);die;
    	return $stuff_list;
    }
    
    /**
     * 根据id获取订单详情
     * @param unknown $order_id
     * @return Ambigous <multitype:, mixed>
     */
    function get_dealer_order_info($order_id){
    	$sql = "SELECT d.*,i.*
    	FROM xgj_furnish_order_detail d
    	join xgj_furnish_order_info i
    	on d.order_id=i.order_id
    	where d.order_id = $order_id";
    	$result = M()->query ( $sql );
    	//var_dump($result);exit;
    	return $result;
    }
    
    /**
     * 根据id获取订单详情的所有系统状态
     * @param unknown $order_id
     * @return Ambigous <multitype:, mixed>
     */
    function get_dealer_order_info_status($order_id){
    	$sql = "SELECT d.quote_status
    	FROM xgj_furnish_order_detail d
    	where d.order_id = $order_id";
    	$result = M()->query ( $sql );
    	//var_export($result);exit;
    	return $result;
    }
    
    /**
     * 根据id获取施工计划质量检测信息
     * @param unknown $order_id
     * @return array
     */
    function get_dealer_order_construct($detail_id){
    	$sql = "SELECT c.*,d.*,c.status as status
    	FROM xgj_furnish_order_construct c
    	join xgj_furnish_order_detail d
    	on c.detail_id=d.detail_id
    	where c.detail_id = $detail_id";
        $result = M()->query ( $sql );    	//var_dump($sql);exit;
    	return $result;
    }
    
    /**
     * 根据上传文件信息
     * @param unknown $class
     * @return array
     */
    function get_file($order_id,$status=1){
        //var_dump(base64_encode('Goods/2016-03-17/56eac0d5665bb.png'),base64_decode('R29vZHMvMjAxNi0wMy0xNy81NmVhYzBkNTY2NWJiLnBuZw=='));exit;
    	$sql = "SELECT *
    		FROM xgj_furnish_order_file where order_id=$order_id and status=$status";
    	$result = M()->query ( $sql );
        foreach ($result as $key => $v) {
            $result[$key]['file_img']=base64_encode($v['file_img']);
        }
        //var_dump($result);exit;
    	return $result;
    }
    
    /**get_dealer_order_price
     * 订单调整表的订单信息
     * @param unknown $order_id
     * @return multitype:
     */
    function get_order_adjust_info($order_id){
    	$sql = "SELECT *
    	FROM xgj_furnish_order_info i
    	join xgj_furnish_order_detail d
    	on i.order_id=d.order_id
    	where i.order_id = $order_id";
    	$result = M()->query ( $sql );
    	//var_dump($result);exit;
    	return $result;
    }

    /**
     * 订单调整表的订单信息
     * @param unknown $order_id
     * @return multitype:
     */
    function get_dealer_adjust_info($order_code){
        $sql = "SELECT *
        FROM xgj_dealer_adjust_info i
        where i.order_code = $order_code";
        $result = M()->query ( $sql );
        //var_dump($result);exit;
        return $result;
    }
    
    /**
     * 获取退换货伙伴自购的申请状态
     * @return bool
     */
    function get_first_audit_status($refund_id) {
    	$sql = "select first_audit_status from xgj_furnish_order_refund where refund_id={$refund_id} ";
    	$result = M()->query ( $sql );
        //var_dump($result[0]["first_audit_status"]);
    	return $result[0]["first_audit_status"];
    }
    
    
    /**
     * 检测质量审核，第一步没完成不能审核下一步施工信息
     * @return array
     */
    function check_construct_id($task_work,$order_id) {
    	$sql = "select status from
    	xgj_furnish_order_construct
    	where task_work < $task_work and order_id={$order_id}";
    	$result = M()->query ( $sql );
    	if ($task_work!=1){
    		for ($i=0;$i<count($result);$i++){
    			if (3!=$result[$i]['status']){
    				$re=0;
    				break;
    			}else{
    				$re=1;
    			}
    		}
    	}else{
    		$re=1;
    	}
    	//var_dump($result);exit;
    	return $re;
    }
    
	public function sendoms($oid,$os=null,$text=null){
		$data=M('xgj_furnish_order_info')->find($oid);
		$user=M('xgj_users')->field('user_name')->find($data['user_id']);
		$detail=M('xgj_furnish_order_detail')->where(array('order_id'=>$data['order_id']))->select();
		if($data['pay_status']==0 or $data['pay_status']==2){
			$orders['flag']='2';
			return $orders;
		}
		if(!empty($detail[0]['adjust_stuff_goods'])){
			//调整后
			$money='adjust_quote_price';
			$fileid='adjust_stuff_goods';
		}else{
			//调整前
			$money='quote_price';
			$fileid='stuff_goods';
		}

		$b='';
		foreach($detail as $key=>$val){
				$a[$key]=explode(';',$val[$fileid]);
				$b['goods'].=$a[$key][0].',';
                $b['goodsnum'].=$a[$key][1].',';
				$b['batch'].=$a[$key][2].',';
		}
		$b['goods']=rtrim($b['goods'],',');
        $b['goodsnum']=rtrim($b['goodsnum'],',');
		$b['batch']=rtrim($b['batch'],',');
        $c=explode(',',$b['goods']);
        $e=explode(',',$b['goodsnum']);
		$f=explode(',',$b['batch']);
		$d='';
        
        // $goods = $b['goods'];
        // $goods_list = M('xgj_furnish_goods')->query("select goods_id,goods_sn,goods_mnemonic,goods_name,shop_price from __TABLE__ where goods_sn IN ($goods) order by field(goods_sn,$goods)");

        // echo '<pre>';
        // var_dump(explode(',',$goods));exit;

        // foreach ($goods_list as $key => $value) {
        //     if(empty($os)?$f[$key]!=0:$f[$key]==$os){
        //         $d[$key]['entryId']=$value['goods_id'];
        //         $d[$key]['goodNumber']=$value['goods_mnemonic'];
        //         $d[$key]['outerNumber']=$value['goods_sn'];
        //         $d[$key]['goodName']=$value['goods_name'];
        //         $d[$key]['price']=$value['shop_price'];
        //         $d[$key]['qty']=$e[$key];
        //         $d[$key]['discountAmount']='1';
        //         $d[$key]['batch']=$f[$key];
        //     }
        // }

		foreach($c as $key=>$val){
			$result=M('xgj_furnish_goods')->field('goods_id ,goods_sn ,goods_mnemonic ,goods_name ,shop_price')->where(array('goods_sn'=>$val))->find();
            if(empty($os)?$f[$key]!=0:$f[$key]==$os){
				$d[$key]['entryId']=$result['goods_id'];
				$d[$key]['goodNumber']=$result['goods_mnemonic'];
				$d[$key]['outerNumber']=$result['goods_sn'];
				$d[$key]['goodName']=$result['goods_name'];
				$d[$key]['price']=$result['shop_price'];
				$d[$key]['qty']=$e[$key];
				$d[$key]['discountAmount']='1';
				$d[$key]['batch']=$f[$key];
			}
		}

		//order by batch
		foreach ($d as $row_array){ 		
			$key_array[] = $row_array['batch']; 
		}
		array_multisort($key_array,SORT_ASC,$d); 
		//删除用于排序的 batch字段
		foreach ($d as $ke=>&$va){ 	
			unset($d[$ke]['batch']);
		}

		$class_id='A';
		$secret = "4tjizi1t5otxe43awhgjq7ms2talxa0x";
		$uri ="http://180.166.221.226:8088/OMS/eic/rest/addTrades.action";
		//print_r($array_temp);die();
		$post_data['appKey']     =  '86497276';
		$post_data['partnerID']  =  'eic-sdk-java-20130701';
		$post_data['format']     =  'json';
		$post_data['signMethod'] =  'md5';
		$post_data['requestID']	 =  'LCd33vUUYuWC5z83Iwg8ay4FHnwn1OVV';
		$post_data['timestamp']	 =   date("Y-m-d H:i:s");
		$post_data['version']	 =  '2.0';
		$a=array(
			array(
			"tradeNumber"      =>$class_id.$data['order_code'].'-'.$os,//订单编号
			"ecplatShopName"   =>"家居事业部",
			"orderCreateDate"  =>date("Y-m-d H:i:s",$data['add_order_time']),//下单时间
			"orderPayDate"     =>date("Y-m-d H:i:s",$data['pay_time']),//付款时间
			"orderFinishDate"  =>date("Y-m-d H:i:s",$data['pay_time']),//完成时间--------------------
			"orderAmount"      =>ceil($data['goods_amount']+$data['cj_money']),//订单金额
			"deliveryFee"      =>$data['shipping_fee'],//运费-----|||||
			"discountAmount"   =>'1',//折扣额-----------------------
			"payAmount"        =>ceil($data['goods_amount']+$data['cj_money']),//付款金额
			"orderStatus"      =>'WAIT_SELLER_SEND_GOODS',//交易状态 WAIT_SELLER_SEND_GOODS -待发货 WAIT_BUYER_CONFIRM_GOODS-待确认收货 TRADE_CLOSED-交易关闭 TRADE_FINISHED-交易成功   ???????????
			"buyerName"        =>$user['user_name'],//购买人账号---------------------------------!!!!!!!
			"receiverName"     =>$data['consignee'],//收件人姓名
			"receiverCountry"  =>"中国",//收件人国家
			"receiverProvince" =>$data['province'],//收件人省份
			"receiverCity"     =>$data['city'],//收件人城市
			"receiverDistrict" =>$data['district'],//收件人地区
			"receiverAddress"  =>$data['address'],//收货人详细地址
			"receiverMobile"   =>$data['mobile_phone'],//收货人手机号
			"deliveryCompany"  =>'1',//快递公司编号-----------------------
			"deliveryNumber"   =>'1',//运单号--------------------------N
			"isInvoice"        =>$data['is_invo'],//是否开票 1-是 0-否
			"invoiceType"      =>'赠值税普通发票',//发票类型-------------------------------------------
			"invoiceTitle"     =>$data['inv_payee'],//发票抬头
			"invoiceDetail"    =>$data['quote_name'],//发票内容---------------------------------------------
			"buyerRemark"      =>$data['postscript'].'-'.$text,//买家备注  $data['postscript'] 
			"orderRemark"      =>'',//订单备注  $data['pay_note']
			"sellerRemark"     =>'',//卖家备注  $data['to_buyer']
			"platdisAmt"       =>'1',//平台优惠------------------------------
			"shopDisamt"       =>'1',//店铺优惠------------------------------------
			"isReachPay"       =>'1',//是否货到付款 1-是 0-否--------------------------
			"createTime"       =>date("Y-m-d H:i:s",time()),//创建时间
			"lastUpdateTime"   =>date("Y-m-d H:i:s",time()),//最后修改时间----------------------------
			"soEntryJsons"     =>$d,
			),			
		);
	
		$post_data['restStr']    =  json_encode($a);
		$result=$this->sign($post_data,$secret);
		$post_data['authCode']   =   $result['str'];


		
		$post=$result['post'].'authCode='.$post_data['authCode'];

		// curl 方法
		$ch = curl_init();
		curl_setopt ( $ch, CURLOPT_URL, $uri );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		// curl_setopt ( $ch, CURLOPT_HEADER, 1 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post );
		curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Expect:')); 
		$return = curl_exec ( $ch );
		curl_close ( $ch );

		$orders = json_decode($return,true);
		return $orders;

	}
	function sign($data,$secret){
			//$data['restStr']=urldecode($data['restStr']);

			ksort($data);
			$result['str'] =$secret;
			$result['post']='';
			if($data){
				foreach($data as $key =>$v){
					$v=urldecode($v);
					$result['str'].=  $key.$v;
					if($key=='restStr')
						$v=urlencode($v);
					$result['post'].=  $key.'='.$v.'&';
				}
			}
			
			$result['str'].=$secret;
			//var_dump(urlencode($result['str']));
			$result['str']=strtoupper(md5($result['str'],false));//md5加密 2进制转为16进制 转为大写字母
			
			return $result;
	}
    
}