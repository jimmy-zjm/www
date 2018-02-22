<?php
namespace Admin\Controller\Furnish;
use \Admin\Controller\Index\AdminController;

/**
 * 后台分类控制器
 */
class CommentController extends AdminController{

    /**
	 * 用户评价列表
	 */
    function index(){
    	$where = '';

        if (!empty($_POST)) {
        	$_GET['p'] = 1;
        	
            $sn        = I('post.sn');
            $display   = I('post.display');
            $star      = I('post.star');
            $starttime = I('post.starttime');
            $endtime   = I('post.endtime');

            if (!empty($sn))        $where .= "g.quote_name like '%".$sn."%'";
            if (!empty($display))   $where .= " and c.display = '{$display}'";
            if (!empty($star))      $where .= " and c.star = '{$star}'";
            if (!empty($starttime)) $where .= " and o.add_order_time >= '{$starttime}'";
            if (!empty($endtime))   $where .= " and o.add_order_time <= '{$endtime}'";
        }
        
        if(!empty($where)){
            $where = ltrim($where,' and');
            $_SESSION['buildingWhere'] = $where;
        } 

        if (!empty($_GET['p'])) $where = $_SESSION['buildingWhere'];
        else unset($_SESSION['buildingWhere']);

        //分页
        $total = M('xgj_furnish_comment c')->join('xgj_furnish_order_detail g ON c.goods_id = g.detail_id')->join('xgj_furnish_order_info o ON g.order_id = o.order_id')->where($where)->count();

        $page  = getPage($total,C('MANAGER_PAGE_SIZE'));

        $list  = M('xgj_furnish_comment c')->join('xgj_furnish_order_detail g ON c.goods_id = g.detail_id')->join('xgj_furnish_order_info o ON g.order_id = o.order_id')->where($where)->limit($page['limit'])->order('o.add_order_time desc')->select();

        //模板传值
        $this->assign("page",$page['page']);
        $this->assign('list',$list);
        $this->display();
    }

    //查看欧洲建材评价详情
    public function fuDetails(){
        $id = I('get.id');
        $list = M('xgj_furnish_comment c')->join('xgj_furnish_order_detail g ON c.goods_id = g.detail_id')->join('xgj_furnish_order_info o ON g.order_id = o.order_id')->where("comment_id=$id")->find();

        $house = M('xgj_dealer_adjust_info')->where("order_code = '{$list['order_code']}' and user_id = '{$list['user_id']}'")->find();
        
        if (empty($house)) $house = M('xgj_users_house')->where(" user_id = '{$list['user_id']}'")->find();
        
        $img = explode('|', $list['images']);

        $this->assign('img',$img);
        $this->assign('list',$list);
        $this->assign('house',$house);
        $this->display();
    }

    //欧洲建材评价显示或隐藏
    public function fuDisplay(){
        $id = I('get.id');
        $dis = I('get.display');
        if ($dis==1) $num = '0';
        else $num = '1';
        $data = array('display'=>$num);
        $list = M('xgj_furnish_comment')->where("comment_id=$id")->save($data);
        echo $list;

    }

    //删除欧洲建材评价
    public function fuDelete(){
        $id = I('get.id');
        $p  = I('get.p');
        if (M('xgj_furnish_comment')->where("comment_id=$id")->delete()) {
            $this->success('删除成功',U('index',array('p'=>$p)));exit;
        }else{
            $this->error('删除失败',U('index',array('p'=>$p)));exit;
        }

    }

	// function index(){
	// 	//实例化用户评价model类
	// 	$commentOb=new \Admin\Model\Furnish\CommentModel;
	// 	//显示列表内容
	// 	$data=$commentOb->furnish_show_list();
	// 	//模板传值
	// 	$this->assign("page",$data['comment_page']);
	// 	$this->assign('furnish_comment_list',$data['comment_show_list']);
	// 	//显示模板
	// 	$this->display();
	// }
	
	/**
	 * 用户评论详细信息
	 */
	function info(){
		//实例化用户评价model类
		$commentOb=new \Admin\Model\Furnish\CommentModel;
		//获取指定值
		$comment_id=intval($_GET['comment_id']);
		//获取一条信息
		$info_list=$commentOb->comment_id($comment_id);
		//var_dump($info_list);
		//模板传值
		$this->assign("info_list",$info_list);
		//显示模板
		$this->display();
	}
	
	function change(){
		//获取指定值
		$comment_id=intval($_GET['comment_id']);
		$status=intval($_GET['status']);
		//var_dump($status);exit;
		//数据源
		$data=array(
				"status"=>$status,
		);
		//更新一条信息
		$rs=M('xgj_furnish_comment')->where(array('comment_id'=>$comment_id))->setField($data);
		if ($rs){
			//跳转页面
			$this->success('操作成功');
			die;
		}else{
			$this->error('操作失败');
		}
	}
	
	function del(){
		//获取指定值
		$comment_id=intval($_GET['comment_id']);
		$status=intval($_GET['status']);
		//var_dump($status);exit;
		//数据源
		$data=array(
				"status"=>$status,
		);
		//更新一条信息
		$rs=M('xgj_furnish_comment')->where(array('comment_id'=>$comment_id))->setField($data);
		if ($rs){
			//跳转页面
			$this->success('删除成功');
			die;
		}else{
			$this->error('删除失败');
		}
	}

}