<?php
namespace Admin\Controller\Index;
use \Admin\Controller\Index\AdminController;
class CouponController extends AdminController{
    private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Admin\Model\Index\CouponModel;
    }
	public function index(){
		$map = array();
        if(isset($_GET['coupon_number'])){
            $map['coupon_number'] = array('like', '%'.I('get.coupon_number').'%');
        }

        $total = M('xgj_coupon')->where($map)->count();
        $page  = getPage($total,C('ADPOS_PAGE_SIZE'));
        $data  = M('xgj_coupon')->where($map)->order('id')->limit($page['limit'])->select();
		
        $this->assign('page', $page['page']);
        $this->assign('coupon_list', $data);
        $this->display();
	}

    public function export(){
        
        $start = (int)I('post.coupon_start');
        $end   = (int)I('post.coupon_end');
        if      (!empty($start) && empty($end))    $where['coupon_number'] = array("egt",$start);
        else if (!empty($end)   && empty($start))  $where['coupon_number'] = array("elt",$end);
        else if (!empty($start) && !empty($end))   $where['coupon_number'] = array(array("egt",$start),array("elt",$end));
        

        $data  = M('xgj_coupon')->where($where)->select();
        foreach ($data as $k => $v) {
            $list['data'][$k] = array($v['coupon_number'],$v['coupon_password'],$v['discount_amount'],date("Y-m-d H:i:s",$v['start_time']),date("Y-m-d H:i:s",$v['end_time']),empty($v['status'])?'未使用':'已使用',date("Y-m-d H:i:s",$v['add_time']));
        }
        $list['key'] = array('优惠券号', '优惠券密码', '优惠券金额', '开始时间', '结束时间', '状态', '添加时间');
        $list['width'] = array('B'=>'15','C'=>'15','D'=>'15','E'=>'20','F'=>'20','H'=>'20');
        exl($list,'优惠券');
    }

    

	public function add(){

		$this->display();
	}
	public function insert(){
        if(!IS_POST) $this->redirect('index');

		$start=$_POST['coupon_number_start'];

		$end =$_POST['coupon_number_end'];
		$amount=$_POST['discount_amount'];
		$start_time=$_POST['start_time'];
		$end_time=$_POST['end_time'];
		//验证优惠券号 是否存在
		$check=$this->m->checknumber($start,$end);
		if($check!=true){
			$this->error("优惠券号已存在");
			exit;
		}
		//生成优惠券
		$data=$this->m->generate($start,$end,$amount,$start_time,$end_time);
        if($this->m->addAll($data)){
            $this->success('添加成功',U('index'));
            die;
        }
        $this->error($this->m->getError());
    } 
	//  修改优惠券页面
     
    public function edit($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');
        $coupon = M('xgj_coupon')->find($id);
        $this->assign('coupon', $coupon);
        $this->display();
    }

    /*
    执行修改管理员
     */
    public function update($id){
        $id = intval($id);//var_dump($id);die();
        if(!$id) $this->redirect('index');
        if($this->m->create(I('post.'),2)){		
			/*$this->m->start_time = strtotime($_POST['start_time']);
			$this->m->end_time   = strtotime($_POST['end_time']);*/
            if($this->m->save() !== false){
                $this->success('修改成功',U('index'));
                die;
            }
        }
        $this->error($this->m->getError());
    }

	//删除优惠券
    public function delete($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');
        if($this->m->delete($id)){
            $this->success('删除成功',U('index'));
            die;
        }
        $this->error($this->m->getError());
    }
	




}
?>