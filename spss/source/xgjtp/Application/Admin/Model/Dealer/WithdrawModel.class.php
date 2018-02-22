<?php
namespace Admin\Model\Dealer;
use \Think\Model;
/**
 * model
 */
class WithdrawModel extends Model{
    protected $trueTableName='xgj_furnish_get_money';

    /**
     * 获取经销商所有的提现信息
     * @return [type] [description]
     */
    public function getAll(){
        //拼凑条件
        $map = array();
        if(isset($_GET['send'])){
            $dealer_name = I('dealer_name');
            $status    = I('status/d')==0?'':I('status/d')-1;

            if(!empty($dealer_name)){
                $map['d_name'] = array('eq',$dealer_name);
            }
            if(!empty($status)){
                $map['status'] = array('eq',$status);
            }
        }

        //分页
        $total        = $this->where($map)->count();
        $page         = getPage($total, C('DEALER_WITHDRAW_PAGE_SIZE'));
        $data['page'] = $page['page'];

        // 提现数据
        $data['list'] = $this->field('m.*,d.d_name')->alias('m')->join('LEFT JOIN xgj_furnish_dealer AS d ON d.d_id=m.d_id')->where($map)->order('apply_time DESC')->limit($page['limit'])->select();
        //处理数据
        foreach ($data['list'] as &$v) {
            $v['apply_time']    = date('Y-m-d H:i', $v['apply_time']);
        }

        return $data;
    }
    /**
     * 
     */
    public function getOne($id){
        $data=$this->where("id=$id")->find();
        return $data;
    }

}