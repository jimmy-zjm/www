<?php
namespace Admin\Model\Dealer;
use \Think\Model;
/**
 * model
 */
class FinanceModel extends Model{
    protected $trueTableName='xgj_furnish_finance';
    /**
     * 结算申请列表
     * @param unknown $page         
     * @param unknown $condition            
     * @return multitype:
     */
    public function finance_apply_list() {
      
            @$keywords=trim($_GET['keywords']);
            @$finance_status=intval($_GET['finance_status']);
            //var_dump($finance_status);
            $condition="1=1";
            if(!empty($keywords)){
            $condition.=" and d.d_company like '%$keywords%' ";
            }
            if(!empty($finance_status)){
                $finance_status-=1;
            $condition.=" and finance_status = $finance_status ";
            }
         //分页
         $sql   = "SELECT count(*) as total from xgj_furnish_finance f join xgj_furnish_dealer d on f.d_id=d.d_id where $condition";
         $total = M()->query($sql);
         $page  = getPage($total[0]['total'], C('FURNISH_FINANCE_PAGE_SIZE'));
         $data['finance_apply_page'] = $page['page'];
         
         // 商品数据
         $data['finance_apply_list'] =M()->query ("SELECT * FROM xgj_furnish_finance f join xgj_furnish_dealer d on f.d_id=d.d_id where $condition order by f.finance_status asc limit {$page['limit']}");
         return $data;
    }
    
    /**
     * 结算详情公司
     * 
     * @param unknown $d_id         
     * @return Ambigous <multitype:, mixed>
     */
    public function finance_company($d_id) {
        $result = M('xgj_furnish_dealer')->query ( "select d_company from xgj_furnish_dealer where d_id=$d_id" );
        return $result;
    }
    
    /**
     * 财务结算详情列表
     * 
     * @param unknown $page         
     * @param unknown $d_id         
     * @return multitype:
     */
    public function finance_info_list($d_id,$finance_id) {
        //分页
        $total = M('xgj_furnish_order_info')->query("SELECT count(*) as total FROM xgj_furnish_order_info i join (xgj_furnish_dealer d ,xgj_furnish_order_detail de ,xgj_furnish_finance_construct_rate r) on (i.d_id=d.d_id 
                and i.order_id=de.order_id and de.quote_id = r.quote_id) where d.d_id={$d_id} ");
        $page         = getPage($total[0]['total'], C('FURNISH_FINANCE_INFO_PAGE_SIZE'));
        $data['finance_info_page'] = $page['page'];

        $sql = "SELECT * FROM 
                xgj_furnish_order_info i 
                join (
                xgj_furnish_dealer d ,
                xgj_furnish_order_detail de ,
                xgj_furnish_finance_construct_rate r
                ) on (
                i.d_id=d.d_id 
                and 
                i.order_id=de.order_id
                and 
                de.quote_id = r.quote_id) 
                where 
                i.d_id={$d_id} 
                order by 
                i.add_order_time asc 
                limit {$page['limit']}
                ";
        $data['finance_info_list'] = M('xgj_furnish_order_info')->query ( $sql );

        // $result = M('xgj_furnish_order_info')->query ( "SELECT * FROM
        //         xgj_furnish_order_info i 
        //         join (
        //         xgj_furnish_dealer d ,
        //         xgj_furnish_order_detail de ,
        //         xgj_furnish_finance_construct_rate r
        //         ) on (
        //         i.d_id=d.d_id 
        //         and 
        //         i.order_id=de.order_id
        //         and 
        //         de.quote_id = r.quote_id)
        //         where
        //         d.d_id={$d_id}
        //     " );
        //$result = M('xgj_furnish_order_info')->query ( $sql );
        // $finance_price = 0;
        // foreach ( $result as $v ) {
        //     if ($v ['quote_status'] == 41) {
        //         $finance_price += $v ['quote_price'] * $v ['first_rate'] * $v ['construct_rate'] * 0.85 + $v ['quote_price'] * $v ['first_rate'] * 0.1;
        //     } else if ($v ['quote_status'] == 61) {
        //         $finance_price += $v ['quote_price'] * $v ['mid_rate'] * $v ['construct_rate'] * 0.85 + $v ['quote_price'] * $v ['mid_rate'] * 0.1;
        //     }
        // }
        // $data['finance_info_price']=round ( $finance_price, 2 );
        $data['finance_info_price']=M('xgj_furnish_dealer')->where("d_id=$d_id")->getField('d_price');
        $data['pay']=M('xgj_furnish_finance')->where("finance_id=$finance_id")->find();

        return $data;
    }
   
    /**
     * 提交结算信息
     * 
     * @param unknown $table            
     * @param unknown $data         
     * @param unknown $where            
     * @return Ambigous <boolean, PDOStatement>
     */
    public function updateOne($table, $data, $where) {
        $result = M($table)->where($where)->setField($data);
        return $result;
    }


    /**
     * 财务支付的一些操作
     * 
     * @param unknown $d_id         
     * @param unknown $finance_info_price           
     * @return boolean
     */
    public function finance_pay($finance_id,$d_id,$finance_info_price) {
        //$sql = "select d_price from xgj_furnish_dealer where d_id={$d_id}";
        //开启事务
        M()->startTrans();
        //结算
        $d_price = M('xgj_furnish_dealer')->where("d_id={$d_id}")->getField('d_price');
        //var_dump($d_price);exit;
        $update_price = ($d_price - $finance_info_price);
        //var_dump($update_price);exit;
        $rs = $this->updateOne( 'xgj_furnish_dealer', array ('d_price' => $update_price), "d_id={$d_id}" );
        //var_dump(M()->_sql());exit;
        $res=M('xgj_furnish_finance')->where("finance_id=$finance_id")->setField(array("finance_status"=>2));
        $select = "SELECT i.order_id,de.detail_id,de.quote_status FROM
                xgj_furnish_order_info i 
                join (
                xgj_furnish_dealer d ,
                xgj_furnish_order_detail de ,
                xgj_furnish_finance_construct_rate r
                ) on (
                i.d_id=d.d_id 
                and 
                i.order_id=de.order_id
                and 
                de.quote_id = r.quote_id)
                where
                d.d_id={$d_id}
        ";
        $result = M('xgj_furnish_order_info')->query ( $select );
        foreach ( $result as $v ) {
            if ($v ['quote_status'] == 4 || $v ['quote_status'] == 6) {
                $re = $this->updateOne( 'xgj_furnish_order_detail', array (
                        'quote_status' => $v ['quote_status'] . '1' 
                ), "detail_id={$v['detail_id']}" );
            } else {
                $re = 1;
            }
        }
        //var_dump($rs,$re,$res);
        if ($rs && $re &&$res) {
            //添加结算历史
            $data = array (
                    'd_id' => $d_id,
                    'finance_id'=>$finance_id,
                    'finance_price' => $finance_info_price,
                    'log_time' => time () 
            );
            $ru = M('xgj_furnish_finance_log')->add ( $data );
            if($ru){
               M()->commit(); 
               return true;
            }
        } else {
            M()->rollBack();
            return false;
        }
    }
    
    /**
     * 财务结算历史列表
     * 
     * @param unknown $page         
     * @param unknown $d_id         
     * @return multitype:
     */
    public function finance_log_list($d_id) {
        
         //分页
        $sql = "SELECT count(*) as total FROM xgj_furnish_finance_log where d_id={$d_id}";
        $total        = M()->query($sql);
        $page         = getPage($total[0]['total'], C('FURNISH_FINANCE_LOG_PAGE_SIZE'));
        $data['finance_log_page'] = $page['page'];

        $data['finance_log_list'] = M()->query ( "select * from xgj_furnish_finance_log where d_id={$d_id} order by log_time desc limit {$page['limit']}" );

        return $data;
    }
    
    /**
     * 退货订单统计
     * 
     * @param unknown $page         
     * @param unknown $where            
     * @param string $search            
     * @return multitype:
     */
    public function refund_show_list( $wheres,$searchs = '') {
        if (empty ( $searchs )) {
            $sql = "SELECT count(*) as total FROM xgj_furnish_order_refund r where r.refund_status=1 and r.first_audit_status=2 {$wheres}  ";
        } else {
            $sql = "SELECT count(*) as total FROM xgj_furnish_order_refund r where r.d_id in (select d_id from xgj_furnish_dealer {$searchs}) and r.refund_status=1 and r.first_audit_status=2 ";
        }
        //分頁
        $data['refund_show_count']       = M()->query($sql);
        $page         = getPage($data['refund_show_count'][0]['total'], C('FURNISH_FINANCE_REFUND_PAGE_SIZE'),array('tab' =>2,'tab_child'=>4));
        $data['refund_show_page'] = $page['page'];


        if (empty ( $searchs )) {
            $select = "SELECT * FROM
            xgj_furnish_order_refund r join
            (xgj_furnish_order_info i ,xgj_furnish_dealer d ,xgj_furnish_order_detail de )
            on (de.order_id=i.order_id and r.d_id=d.d_id and de.detail_id=r.detail_id)
            where r.refund_status=1 and r.first_audit_status=2 {$wheres} order by r.refund_time desc limit {$page['limit']}";
        } else {
            $select = "SELECT * FROM
            xgj_furnish_order_refund r join
            (xgj_furnish_order_info i ,xgj_furnish_dealer d ,xgj_furnish_order_detail de)
            on (de.order_id=i.order_id and r.d_id=d.d_id and de.detail_id=r.detail_id)
            where r.refund_status=1 and r.first_audit_status=2 and r.d_id in (select d_id from xgj_furnish_dealer {$searchs}) order by r.refund_time desc limit {$page['limit']}";
        }
        $data['refund_show_list'] = M('xgj_furnish_order_refund')->query ( $select );

        if (empty ( $searchs )) {
            $sql2 = "SELECT sum(r.refund_price) as price FROM xgj_furnish_order_refund r where r.refund_status=1 and r.first_audit_status=2 {$wheres}";
        } else {
            $sql2 = "SELECT sum(r.refund_price) as price FROM xgj_furnish_order_refund r where r.refund_status=1 and r.first_audit_status=2 and r.d_id in (select d_id from xgj_furnish_dealer {$searchs})";
        }
        $data['refund_show_price'] = M()->query ( $sql2 );

        return $data;
    }
    
    
    /**
     * 退换货详细信息
     * 
     * @param unknown $refund_id            
     * @return Ambigous <multitype:, mixed>
     */
    public function refund_info($refund_id) {
        $sql = "select `refund_goods`,`refund_price`,`refund_msg`,`refund_code` from xgj_furnish_order_refund where refund_id=$refund_id";
        $result = M()->query ( $sql );
        //var_dump($result);exit;
        $arr = explode ( ';', $result [0]['refund_goods'] );
        $list = explode ( ',', $arr [0] );
        $refund_num = explode ( ',', $arr [1] );
        $cc = array ();
        foreach ( $list as $k => $v ) {
            $cc [$k] ['goods_sn'] = $v;
            $cc [$k] ['num'] = $refund_num [$k];
            $cc [$k] ['refund_price'] = $result[0]['refund_price'];
            $cc [$k] ['refund_code'] = $result[0]['refund_code'];
            $cc [$k] ['refund_msg'] = $result[0]['refund_msg'];
        }
        foreach ( $cc as $k => $v ) {
            $cc [$k] ['list'] = M()->query ("select goods_img,goods_name,goods_model,goods_unit,goods_brand,shop_price from xgj_furnish_goods where goods_sn={$v['goods_sn']}");
        }
        return $cc;
    }
    
    
    /**
     * 补货订单统计
     * 
     * @param unknown $page         
     * @param unknown $where            
     * @param string $search            
     * @return multitype:
     */
    public function add_show_list( $wheres, $searchs = '') {
        if (empty ( $searchs )) {
            $sql = "SELECT count(*) as total FROM xgj_furnish_order_refund r where r.refund_status=2 and r.first_audit_status=2 {$wheres} ";
        } else {
            $sql = "SELECT count(*) as total FROM xgj_furnish_order_refund r where r.d_id in (select d_id from xgj_furnish_dealer {$searchs}) and r.refund_status=2 and r.first_audit_status=2 ";
        }
        //分頁
        $data['add_show_count']        = M()->query($sql);
        $page         = getPage($data['add_show_count'][0]['total'], C('FURNISH_FINANCE_REFUND_PAGE_SIZE'),array('tab' =>2,'tab_child'=>5));
        $data['add_show_page'] = $page['page'];


        if (empty ( $searchs )) {
            $select = "SELECT * FROM
            xgj_furnish_order_refund r join
            (xgj_furnish_order_info i ,xgj_furnish_dealer d ,xgj_furnish_order_detail de )
            on (de.order_id=i.order_id and r.d_id=d.d_id and de.detail_id=r.detail_id)
            where r.refund_status=2 and r.first_audit_status=2 {$wheres} order by r.refund_time desc limit {$page['limit']}";
        } else {
            $select = "SELECT * FROM
            xgj_furnish_order_refund r join
            (xgj_furnish_order_info i ,xgj_furnish_dealer d ,xgj_furnish_order_detail de)
            on (de.order_id=i.order_id and r.d_id=d.d_id and de.detail_id=r.detail_id)
            where r.refund_status=2 and r.first_audit_status=2 and r.d_id in (select d_id from xgj_furnish_dealer {$searchs}) order by r.refund_time desc limit {$page['limit']}";
        }
        $data['add_show_list'] = M('xgj_furnish_order_refund')->query ( $select );

        if (empty ( $searchs )) {
            $sql2 = "SELECT sum(r.refund_price) as price FROM xgj_furnish_order_refund r where r.refund_status=2 and r.first_audit_status=2 {$wheres}";
        } else {
            $sql2 = "SELECT sum(r.refund_price) as price FROM xgj_furnish_order_refund r where r.refund_status=2 and r.first_audit_status=2 and r.d_id in (select d_id from xgj_furnish_dealer {$searchs})";
        }
        $data['add_show_price'] = M()->query ( $sql2 );

        return $data;
    }
    
    
    
    /**
     * 伙伴自购订单统计
     * 
     * @param unknown $page         
     * @param unknown $where            
     * @param string $search            
     * @return multitype:
     */
    public function self_buy_show_list($conditions) {
        $sql = "SELECT count(*) as total FROM xgj_furnish_order_refund r where r.refund_status=3 and r.first_audit_status=2 {$conditions}";
        $detail['self_buy_show_count'] = M()->query ( $sql );
        $page         = getPage($detail['self_buy_show_count'][0]['total'], C('FURNISH_FINANCE_REFUND_PAGE_SIZE'),array('tab' =>4));
        $detail['self_buy_show_page'] = $page['page'];

        $sql2 = "SELECT * FROM
        xgj_furnish_order_refund r join
        (xgj_furnish_order_info i ,xgj_furnish_dealer d ,xgj_furnish_order_detail de)
        on (de.order_id=i.order_id and r.d_id=d.d_id and de.detail_id=r.detail_id)
        where r.refund_status=3 and r.first_audit_status=2 {$conditions} order by r.refund_time desc limit {$page['limit']}";
        $detail['self_buy_show_list'] = M()->query( $sql2 );
       
        $sql3 = "SELECT sum(r.refund_price) as price FROM xgj_furnish_order_refund r where r.refund_status=3 and r.first_audit_status=2 {$conditions}";
        $detail['self_buy_show_price'] = M()->query( $sql3 );

        return $detail;
    }
        
    /**
     * 获取退换货状态信息
     *
     * @param unknown $refund_id
     * @return multitype:
     */
    public function get_one_refund_id($refund_id) {
        $re = M()->query ( "select `first_audit_status` ,`review_audit_status` from xgj_furnish_order_refund where refund_id=$refund_id " );
        return $re;
    }
    
}