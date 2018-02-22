<?php
header("Content-type:text/html;charset=utf-8");
require_once (WWW_DIR . "/libs/db.php");
require_once (WWW_DIR . "/libs/UploadFile.class.php");

/**
 * @author Administrator
 *
 */
class dealer_model {

    public $username;
    public $pwd;

    private function getdealerid(){
        $dealerId =  $_COOKIE['dealerId'];
        if(empty($dealerId)){
            die('empty dealerId');
        }else{
            return $dealerId;
        }
    }

    public function login($username, $pwd) {
        $db = new db();
        $sql = "select d_id,d_name,d_pwd from xgj_furnish_dealer where d_name='".$username."' and d_pwd='".$pwd."'";
        $arr = $db->getAll($sql);
        return $arr;
        
    }

    public function upass($oldpass, $newpass){
        $dealerid = $this->getdealerid();
        $db = new db();
        $dinfo = $db -> getRow("select d_pwd from xgj_furnish_dealer where d_id = $dealerid");
        if($dinfo['d_pwd'] != $oldpass){
            return false;
        }
        if($db -> update('xgj_furnish_dealer',array('d_pwd' => $newpass),"d_id=$dealerid")){
            return true;
        }else{
            return false;
        }
    }

     public function dealer_order($dealerid,$page,$each_disNums) {
        //$each_disNums每页显示数据条数
        $db = new db();
        $start=($page-1)*$each_disNums;
        $sql = "select * from xgj_furnish_order_info where d_id=".$dealerid." order by order_id desc limit ".$start.",".$each_disNums;
        
        $arr = $db->getAll($sql);
        return $arr;
        
    }

    public function dealer_settlement($dealerid,$page) {
        $db = new db();
        $start=($page-1)*10;
		//  $sql = "select * from xgj_furnish_finance a join xgj_furnish_finance_log b on a.finance_id=b.finance_id   where   a.d_id='".$dealerid."' order by b.log_time desc limit ".$start.",10";
        $sql = "select * from xgj_furnish_finance  where   d_id='".$dealerid."' order by  apply_time desc limit ".$start.",10";
        $arr = $db->getAll($sql);
        return $arr;

    }
	public function dealer_settle($dealerid,$page) {
        $db = new db();
        $start=($page-1)*10;
        $sql = "select a.* ,b.order_code,b.mobile_phone,b.consignee  from xgj_furnish_dealer_settle  a left join xgj_furnish_order_info b on a.order_id =b.order_id where a.d_id=".$dealerid." order by a.id desc limit ".$start.",10";
        $arr = $db->getAll($sql);
        return $arr;

    }
    //账户余额查询
    public function accountBalance($dealerid){
        $db = new db();
        //$sql = "select sum(settlement_total) as total from (select * from xgj_furnish_settlement_record where settlement_d_id='$dealerid') as tab where settlement_statu in(0,2)";
        $sql = "select d_price from xgj_furnish_dealer where d_id=$dealerid";
        $arr = $db->getRow($sql);
        
        return $arr;
    }

    public function dealer_center($dealerid) {
        $db = new db();
        $sql = "select * from xgj_furnish_dealer where d_id='".$dealerid."'";
        $info = $db->getRow($sql);
        return $info;
        
    }

    public function update_info($arr){
        $db = new db();
        $db -> update('xgj_furnish_dealer',$arr,"d_id='".$_COOKIE['dealerId']."'");
        return true;
    }

    #获取该经销商服务用户的意见反馈
    public function dealer_advices($dealerid,$page){
        // echo $dealerid;exit;
        $db = new db();
        $start=($page-1)*10;
        $sql = "select * from xgj_furnish_advice where d_id='".$dealerid."' order by advice_id desc limit ".$start.",10";
        $arr = $db->getAll($sql);
        return $arr;
    }
    
    
    function show_count_nav($tab,$d_id){
        $db     =new db();
        $sql    = "SELECT count(*) FROM $tab where d_id=$d_id";
        $result =$db->getOne($sql);
        return $result;
    }
    /*function show_count_settlement_record($tab,$d_id){
        $db     =new db();
        $sql    = "SELECT count(*) FROM $tab where settlement_d_id=$d_id";
        $result =$db->getOne($sql);
        return $result;
    }*/
    
    //获取经销商信息
    public function getDealerInfo($dealerId){
        $db     = new db();
        $sql    = "select * from xgj_furnish_dealer where d_id='".$dealerId."'";
        $result = $db->getAll($sql);
        return $result;
    }
    
    //一般搜索
    public function orderSearch($data,$page,$each_disNums){
        $db = new db();
        $start=($page-1)*$each_disNums;
        $id = $_COOKIE['dealerId'];
        //"select * from xgj_furnish_order_info order by order_id desc limit ".$start.",".$each_disNums;
        $sql = "select * from xgj_furnish_order_info where d_id = $id and order_code LIKE '%".$data."%' or d_id = $id and  consignee LIKE '%".$data."%' or d_id = $id and  mobile_phone LIKE '%".$data."%' order by order_id desc limit ".$start.",".$each_disNums;
        $result = $db->getAll($sql);
        return $result;
    }
    
    //获取系统分类
    public function systemData($order_id){
        $db = new db();
        
        $sql = "select * from xgj_furnish_order_info a left join xgj_furnish_order_detail b on a.order_id=b.order_id where a.order_id=".$order_id." order by detail_id desc";// limit ".$start.",".$each_disNums
        $result = $db->getAll($sql);
        return $result;
    }
    //取施工待安排的数据
    public function constructSettle($order_id){
        $db = new db();
        //$start = ($page-1)*$each_disNums;
        $sql = "select * from xgj_furnish_order_info a left join xgj_furnish_order_detail b on a.order_id=b.order_id where b.plan_settle=0 and a.order_id=".$order_id." order by detail_id desc";// limit ".$start.",".$each_disNums
        $result = $db->getAll($sql);
        return $result;
        
    }
    
    //筛选施工步骤
    public function chooseWork($order_id,$detail_id){
        $db = new db();
        $sql = "select * from xgj_furnish_task_work where id not in (select task_work from xgj_furnish_order_construct where order_id=$order_id and detail_id=$detail_id)";
        $result = $db->getAll($sql);
        return $result;
    }
    
    //将安排好的施工数据插入数据库
    public function addPlan($table,$data){
        $db = new db();
        $result = $db->add($table, $data);
        return $result;
    }
    
    //施工计划、质量验收
    public function constructPlan($order_id,$page,$each_disNums){
        $db = new db();
        $start = ($page-1)*$each_disNums;
        $sql = "select *,a.status as status,c.status as nimabi from xgj_furnish_order_construct a left join (xgj_furnish_order_info b left join xgj_furnish_order_detail c on b.order_id=c.order_id) on a.detail_id=c.detail_id where c.plan_settle=1 and b.order_id=".$order_id." order by a.task_work asc";
        $result = $db->getAll($sql);
        return $result;
    }
    
  /*   //质量验收
    public function constructCheck($order_id,$page,$each_disNums){
        $db = new db();
        $start = ($page-1)*$each_disNums;
        $sql = "select * from (xgj_furnish_order_check a left join xgj_furnish_order_construct b on a.construct_id=b.construct_id) left join xgj_furnish_order_info c on b.order_id =c.order_id where b.order_id=".$order_id." order by b.construct_id desc";
        
        $result = $db->getAll($sql);
        return $result;
    } */
    
    //订单信息
    public function orderInfo($order_id){
        $db = new db();
     //   $sql = "select * from (xgj_furnish_order_info a left join xgj_furnish_order_detail b on a.order_id=b.order_id) left join xgj_furnish_quote c on b.quote_id=c.quote_id where a.order_id=".$order_id;
		  $sql = "select  *,a.order_id as order_id from xgj_furnish_order_info a left join xgj_furnish_order_detail b on a.order_id=b.order_id left join xgj_furnish_quote c on b.quote_id=c.quote_id where a.order_id=".$order_id;
          $result = $db->getRow($sql);
        return $result;
    }
    //订单详情信息
    public function orderInfoDetail($order_id){
        $db = new db();
        $sql = "select * from (xgj_furnish_order_info a left join xgj_furnish_order_detail b on a.order_id=b.order_id) left join xgj_furnish_order_construct c on b.detail_id=c.detail_id where a.order_id=".$order_id;
        $result = $db->getAll($sql);
        // echo '<pre>';
        // var_dump($result);exit;
        return $result;
    }
    
    //施工计划调整
    public function updatePlan($table, $data, $where){
        $db = new db();
        $result = $db->update($table, $data, $where);
        return $result;     
    }
    
    //订单信息页文件区
    public function dealerOrderFile($order_id){
        $db = new db();
        $sql = "select * from xgj_furnish_order_file a left join xgj_furnish_order_info b on a.order_id=b.order_id where a.status=1 and b.order_id=".$order_id;
        $result = $db->getAll($sql);
		foreach($result as $key =>$val){
			$result[$key]['file_img']=base64_encode($val['file_img']);
		}
        return $result;
    }
    
    // //查找材料清单sn
    // public function qingdan($order_id,$detail_id){
    //     $db = new db();
    //     $sql = "select * from xgj_furnish_order_info a left join xgj_furnish_order_detail b on a.order_id=b.order_id where a.order_id= $order_id and detail_id=$detail_id";
    //     $result = $db->getRow($sql);
    //     return $result;
    // }

    /**
     * 根据id获取清单列表
     * @param unknown $order_id
     * @return Ambigous<multitype:, mixed>
    */
    function get_dealer_order_stuff_list($detail_id,$stuff){
        $db = new db ();
        $sql = "SELECT stuff_goods
        FROM xgj_furnish_order_detail 
        where detail_id = $detail_id";
        $result = $db->getRow( $sql );
        $stuff_list=array();
        if(empty($result)){
            $stuff_list=array();
        }else{
            $stuff_goods = explode ( ';', $result['stuff_goods'] );
            $goods_sn = explode ( ',', $stuff_goods[0] );
            $goods_num=explode ( ',', $stuff_goods[1] );
            $stuff_list=array();
            foreach ($goods_sn as $k => $v){
                $stuff_list[$k]['num']=$goods_num[$k];
                $stuff_list[$k]['goods_sn']=$v;
            }
            foreach ( $stuff_list as $k => $v ) {
                // $stuff_list [$k] ['list'] = M('xgj_furnish_goods')->field('goods_img,goods_name,goods_model,goods_unit,goods_brand,shop_price')->where("goods_sn={$v['goods_sn']} and goods_lv=$stuff")->select();
                $stuff_list [$k] ['list'] = $db->getRow( "select goods_img,goods_name,goods_model,goods_unit,goods_brand,shop_price from xgj_furnish_goods where goods_sn={$v['goods_sn']} and goods_lv=$stuff" );
            }
        }
        return $stuff_list;
    }

    //查找具体材料清单信息
    public function qingdanDetail($range){
        $db = new db();
        $sql = "select * from xgj_furnish_goods where goods_sn in (".$range.")";
        $result = $db->getAll($sql);
        return $result;
    }
    
    //查找补货清单信息
    public function replenishList($order_id){
        $db = new db($order_id);
        $sql = "select * from xgj_furnish_order_refund where refund_status=2 and order_id=".$order_id;
        $result = $db->getRow($sql);
        return $result;
    }
    //插入数据到数据库
    public function insertGoods($table,$data){
        $db = new db();
        $result = $db->add($table,$data,$compatible = false);
        return $result;
    }
    
    //文件上传
    public function UploadFile($file,$savePath){
        $db = new db();
        $upload = new UploadFile();
        $result = $upload->uploadOne($file,$savePath);
        return $result;
    }
    
    //高级搜索
    public function advancedSearch($start_time,$end_time,$pay_status,$schedule_status){
        $db = new db();

        $id = $_COOKIE['dealerId'];

        $sql = "select * from xgj_furnish_order_info where d_id = $id and pay_status=$pay_status and schedule_status=$schedule_status and add_order_time>=".$start_time." and add_order_time<=".$end_time;

        // $sql = "select * from (xgj_furnish_order_info a left join xgj_furnish_order_detail b on a.order_id=b.order_id) left join xgj_furnish_order_construct c on b.detail_id=c.detail_id where a.pay_status=".$pay_status." and a.schedule_status=".$schedule_status." and c.start_time>=".$start_time." and c.end_time<=".$end_time;
        
        $result = $db-> getAll($sql);
        // echo '<pre>';
        // var_dump($result);exit;
        return $result;
        
    }
    
    //售后页面搜索
    public function afterSaleSearch($pay_status,$schedule_status,$page='',$each_disNums=''){
        $db = new db();
        
        $where = '';
        $limit = '';

        if (!empty($pay_status) || $pay_status=='0') {
            $where .= "pay_status='{$pay_status}' and ";
        }

        if (!empty($schedule_status)) {
            $where .= "schedule_status='{$schedule_status}' and ";
        }

        $start=($page-1)*$each_disNums;

        if (!empty($page) && !empty($each_disNums)) {
            $limit = "limit $start,$each_disNums"; 
        }
        // echo $each_disNums;exit;

        $sql = "select * from xgj_furnish_order_info where $where d_id='{$_COOKIE['dealerId']}' order by order_id desc $limit";

        $result = $db->getAll($sql);
        return $result;
    }
    
    //查找维修工单数据
    public function getFixInfo($d_id,$page='',$each_disNums=''){
        $limit = '';

        $start=($page-1)*$each_disNums;

        if (!empty($page) && !empty($each_disNums)) {
            $limit = "limit $start,$each_disNums"; 
        }

        $db = new db();
        $sql = "select * from xgj_user_problem p join xgj_furnish_quote q on p.quote_id=q.quote_id join xgj_furnish_order_info o on o.order_id=p.order_id where p.d_id=$d_id $limit";
        $result = $db->getAll($sql);
        return $result;
        
    }
    
    //查询某一指定订单信息
    public function getOrderInfo($order_id){
        $db = new db();
        $sql = "select * from xgj_furnish_order_info where order_id=".$order_id;
        $result = $db->getRow($sql);
        return $result;
    }

    //查询某一指定图片
    public function getimage($order_id){
        $db = new db();
        $sql = "select * from xgj_furnish_order_file where status = 3 and order_id=".$order_id;
        $result = $db->getRow($sql);
        return $result;
    }

    //查询某一指定订单信息
    public function getOrderInfos($order_sn){
        $db = new db();
        $sql = "select * from xgj_furnish_order_info where order_code=".$order_sn;
        $row = $db->getRow($sql);
        $result = $this->getHomeInfo($row['house_id']);
        return $result;
    }

    //查询某一指定订单信息
    public function getDealerAdjustInfo($order_sn){
        $db = new db();
        $sql = "select * from xgj_dealer_adjust_info where order_code=".$order_sn;
        $result = $db->getRow($sql);
        return $result;
    }

    //查询某一指定订单信息
    public function getHomeInfo($id){
        $db = new db();
        $sql = "select * from xgj_users_house where house_id=".$id;
        $result = $db->getRow($sql);
        return $result;
    }

    //查询某一指定订单信息
    public function getQuote($id){
        $db = new db();
        $sql = "select * from xgj_furnish_order_detail where order_id=".$id;
        $result = $db->getAll($sql);
        return $result;
    }

    //调整订单
    public function addOrder($table, $data){
        $db = new db();
        $result = $db->add($table, $data);
        return $result;
    }
    
    //调整订单
    public function editOrder($table, $data, $where){
        $db = new db();
        $result = $db->update($table, $data, $where);
        return $result;
    }
    //查看是否已提交结算申请
    public function checkAccount($d_id){
        $db = new db();
        $info=$db->getOne("select count(*) from xgj_furnish_finance where d_id=$d_id and finance_status=0");
        return $info;
    }

    //申请结算共用
    public function getaccount($table,$data){
        
        $db = new db();
        
        $result = $db->add($table, $data);
        
        return $result;
    }
    
    
    public function checkConstructPlan($detail_id,$task_work){
        $db = new db();
        $sql = "select count(*) from xgj_furnish_order_construct where detail_id=$detail_id and task_work=$task_work";
        $result = $db->getOne($sql);
        return $result;
    }
  
    public function getField($table,$where,$field){
        $db = new db();
        $sql = "select $field from $table where $where";
        $result = $db->getOne($sql);
        return $result;
    }

    public function getFind($table,$where,$limit=null,$os=1,$field='*',$sort=null){
        $db = new db();
        $sql = "select $field from $table where $where";
        if (!empty($sort))  $sql .= " order by $sort";
        if (!empty($limit)) $sql .= " limit $limit";
        if ($os==1) $result = $db->getRow($sql);
        else if($os==2) $result = $db->getAll($sql);
        return $result;
    }

    public function addData($table,$data){
        $db = new db();
        $result = $db->add($table,$data);
        return $result;
    }

    public function saveData($table,$data,$where){
        $db = new db();
        $re = $db->update($table,$data,$where);
        return $re;
    }

    // public function getUpkeep($limit=null){
    //     $d_id  = $_COOKIE['dealerId'];
    //     $table = 'xgj_s_order o join xgj_users u on u.user_id=o.user_id';
    //     $where = "o.class_id = 9 and o.d_id = $d_id";
    //     return $this->getFind($table,$where,$limit,2);
    // }

    public function getUpkeep($limit=null){
        $d_id  = $_COOKIE['dealerId'];
        $table = 'xgj_s_order_goods g join xgj_s_order o on g.order_id=o.id';
        $where = "o.class_id = 9 and o.d_id = $d_id";
        $field = "*,g.id gid";
        $sort  = "o.pay_time desc";
        $re    = $this->getFind($table,$where,$limit,2,$field,$sort);
        foreach ($re as $k => $v) {
            $num = $this->getFind('xgj_dealer_work_order',"p_id={$v['gid']} and class = 9",'',2);
            $re[$k]['num'] = count($num);
        }
        return $re;
    }


    public function getMaintain($limit=null){
        $d_id  = $_COOKIE['dealerId'];
        $table = 'xgj_user_problem p join xgj_furnish_quote q on p.quote_id=q.quote_id join xgj_furnish_order_info o on o.order_id=p.order_id';
        $where = "o.d_id = $d_id";
        return $this->getFind($table,$where,$limit,2);
    }

    public function getGoodsOne($id){
        $table    = 'xgj_s_order_goods';
        $where    = "id = $id";
        $field    = 'goods_num';
        $goodsNum = $this->getField($table,$where,$field);
        $allNum   = $this->getFind('xgj_dealer_work_order',"p_id={$id} and class = 9",'',2);
        $count    = count($allNum);
        if ($goodsNum > $count) return true;
        else return false;
    }

    public function getWorkOrder($id,$class){
        //$class=10 维修  $class=9 保养
        if ($class == 9) {
            $table = 'xgj_dealer_work_order w join xgj_furnish_order_info i on w.o_id=i.order_id join xgj_s_order_goods g on w.p_id = g.id';
            $where = "w.p_id = $id and w.class = $class";
            $field = 'w.*,i.order_code sn,g.goods_title goods_name';
            $re    = $this->getFind($table,$where,'',2,$field);
        }else if ($class = 10) {
            $table = 'xgj_dealer_work_order w join xgj_user_problem p on w.p_id = p.id join xgj_furnish_order_info i on w.o_id=i.order_id';
            $where = "p.id = $id and w.class = $class";
            $field = 'w.*,p.note goods_name,i.order_code sn';
            $re    = $this->getFind($table,$where,'',2,$field);
        }

        return $re;
    }


    public function isTrue($data,$dId){
        $db = new db();
        $re = false;
        foreach ($data as $k => $v) {
            $wheres[] = $k.'='.$v;
        }
        $where = implode(' and ', $wheres);
        $detail = $this->getFind('xgj_furnish_order_detail',$where);

        if (!empty($detail)) {
            $where = "order_id = {$detail['order_id']}";
            $info = $this->getFind('xgj_furnish_order_info',$where);
            if ($dId == $info['d_id']) $re = true;
        }
        return $re;
    }
}