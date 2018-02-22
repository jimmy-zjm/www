<?php
namespace Admin\Controller\Furnish;
use \Admin\Controller\Index\AdminController;

/**
 * 后台分类控制器
 */
class AutoputawayController extends AdminController{

    /**
     * 上下架列表
     */
    public function index(){
        $tab=$_GET['tab']==''?'':intval($_GET['tab']);
        //$tab_child=$_GET['tab_child']==''?'':intval($_GET['tab_child'])
        $GoodsModel= new \Admin\Model\Furnish\GoodsModel;
        $data  = $GoodsModel->getAll(0,FURNISH_GOODSPUTAWAY_PAGE_SIZE);
        $total=M('xgj_furnish_quote')->count();
        $page  = getPage($total,C('FURNISH_GOODSPUTAWAY_PAGE_SIZE'),array('tab' =>2 ));

        $data_  = M('xgj_furnish_quote')->limit($page['limit'])->select();
        $this->assign('goods_page', $data['page']);
        $this->assign('goods_list', $data['goods_list']);
        $this->assign("quote_page",$page['page']);
        $this->assign('quote_list',$data_);
        $this->assign('tab',$tab);
        $this->display();
    }
    
    /**
     * 修改一条材料商品上下架
     */
    public function goods_edit(){
    	//获取指定值
    	$goods_id=intval($_GET['goods_id']);
    	//获取数据源
    	$data=array(
    			'starttime'=>strtotime($_GET['starttime']),
    			'endtime'=>strtotime($_GET['endtime']),
    	);
    	//实例化数据库操作类
    	$GoodsModel= new \Admin\Model\Furnish\GoodsModel;
    	//执行并获取更新结果
    	$rs=$GoodsModel->where("goods_id=$goods_id")->setField($data);
    	//判断并输出
    	if($rs){
    		//返回成功
    		echo 1;
    	}else{
    		//返回失败
    		echo -1;
    	}
    	exit;
    }
    
    /**
     *批量修改材料商品上下架
     */
    public function goods_edit_all(){
        if(!IS_POST || empty($_POST['batchtime'])){
           $this->error('请选择上架或下架的商品'); 
           die;
        }
		//实例化数据库操作类
		$GoodsModel= new \Admin\Model\Furnish\GoodsModel;
		//获取批量上下架时间
		$batchtime=strtotime($_POST['batchtime']);
		//获取批量上下架的条件
		$str='';
		foreach ($_POST['goods'] as $v){
			$str.="'$v',";
		}
		$where=rtrim($str,',');
		//判断是上架还是下架
		if (isset($_POST['batch']) && !empty($_POST['batch']) && trim($_POST['batch'])=='批量上架'){
			$field="starttime='$batchtime'";//上架字段及值
		}else if(isset($_POST['batch']) && !empty($_POST['batch']) && trim($_POST['batch'])=='批量下架'){
			$field="endtime='$batchtime'";//下架字段及值
		}
		//执行并获取更新结果
		$rs=$GoodsModel->execute("update xgj_furnish_goods set $field where goods_id in ({$where})");
        //var_dump($GoodsModel->getLastSql());exit;
		//判断并跳转提示
		if($rs !== false){
			$this->success("添加{$_POST['batch']}成功，正在跳转...",U('index'));
			die;
		}else{
			$this->error($GoodsModel->getError());
		}
    }

    /**
     * 修改系统商品上下架
     */
    function quote_edit(){
        //获取指定值
        $quote_id=intval($_GET['quote_id']);
        //数据源
        $data=array(
                //'is_putaway'=>intval($_GET['is_putaway']),
                'starttime'=>strtotime($_GET['starttime']),
                'endtime'=>strtotime($_GET['endtime']),
        );
        //执行并获取更新结果
        $rs=M('xgj_furnish_quote')->where("quote_id=$quote_id")->setField($data);
        //判断并输出
        if($rs){
            //返回成功
            echo trim('success');
        }else{
            //返回失败
            echo trim('lose');
        }
    }
    
    /**
     *批量修改系统商品上下架
     */
    function quote_edit_all(){
        if(!IS_POST || empty($_POST['batchtime'])){
           $this->error('请选择上架或下架的商品'); 
           die;
        }
        //获取批量上下架时间
        $batchtime=strtotime($_POST['batchtime']);
        //获取批量上下架的条件
        $str='';
        foreach ($_POST['quote'] as $v){
            $str.="'$v',";
        }
        $where=rtrim($str,',');
        //判断是上架还是下架
        if (isset($_POST['batch']) && !empty($_POST['batch']) && trim($_POST['batch'])=='批量上架'){
            $field="starttime='$batchtime'";//上架字段及值
        }else if(isset($_POST['batch']) && !empty($_POST['batch']) && trim($_POST['batch'])=='批量下架'){
            $field="endtime='$batchtime'";//下架字段及值
        }
        //执行并获取更新结果
        //$rs=$GoodsModel->execute("update xgj_furnish_goods set $field where goods_id in ({$where})");
        $rs=M()->execute("update xgj_furnish_quote set $field where quote_id in ({$where})");
        //判断并跳转提示
        if($rs !== false){
            $this->success("添加{$_POST['batch']}成功，正在跳转...",U('index',array('tab'=>2)));
            die;
        }else{
            $this->error("添加{$_POST['batch']}失败，正在跳转，正在跳转...",U('index',array('tab'=>2)));
        }
    }
}