<?php
namespace Admin\Controller\AfterSale;
use \Admin\Controller\Index\AdminController;
/*
 后台机电耗材控制器
 */
class ConsumableController extends AdminController{

	public function index(){
		$quoteData=M('xgj_furnish_quote')->field('quote_id,quote_name')->select();
		$this->assign("quoteData",$quoteData);
		$this->display();
	}

	public function ajaxIndex(){
        $where = ' 1 = 1 '; // 搜索条件   
            
        $is_put = I('is_put')?trim(I('is_put')):'';

		// 关键词搜索               
		$quote_name   = I('quote_name') ? trim(I('quote_name')) : '';
		$c_name = I('c_name') ? trim(I('c_name')) : '';
		$product_name = I('product_name') ? trim(I('product_name')) : '';

        if($quote_name)
        {
            $where .= " and c.quote_name = $quote_name" ;
        }

        if($c_name)
        {
            $where .= " and c.c_name like '%$c_name%'" ;
        }

        if($product_name)
        {
            $where .= " and c.product_name like '%$product_name%'" ;
        }


        if ($is_put==1){
            $where .= " and c.is_put = {$is_put}";
        }else if($is_put==2){
        	$where .= " and c.is_put != 1";
        }else{
        	$where .= "";
        } 

        $count=M('xgj_s_consumable c')
        		->join("xgj_furnish_quote q on c.quote_name=q.quote_id")
                ->where($where)
                ->count();
        $Page  = getAjaxPage($count,10);
        
        $List = M('xgj_s_consumable c')
                ->field('c.*,q.quote_name as quote_name')
                ->join("xgj_furnish_quote q on c.quote_name=q.quote_id")
                ->where($where)
                ->order('is_put desc')
                ->limit($Page['limit'])
                ->select();
        foreach ($List as $k => &$v) {
        	$v['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
       //var_dump($where);die;
        $this->assign('List',$List);
        $this->assign('page',$Page['page']);// 赋值分页输出
        $this->display();
    }

    public function add(){
    	$quoteData=M('xgj_furnish_quote')->field('quote_id,quote_name')->select();
		$this->assign("quoteData",$quoteData);
    	$this->display();
    }

    public function insert(){
    	if(!IS_POST) $this->redirect('index');
        $model = new \Admin\Model\AfterSale\ConsumableModel;
		if($model->create($_POST,1)){
			if($model->add() !== false){
				$this->success('添加成功',U('index'));
				die;
			}
		}
		$this->error($model->getError());
    }

    /*
	 展示编辑的页面
	*/
	public function edit($id){
		//接收商品的id
		$id = intval($id);
		if(empty($id)) $this->redirect('参数非法');
        $model = new \Admin\Model\AfterSale\ConsumableModel;
        $quoteData=M('xgj_furnish_quote')->field('quote_id,quote_name')->select();
		$this->assign("quoteData",$quoteData);
		//用于回填数据
		$info = $model->getInfo($id);
		$this->assign('info', $info);
		$this->display();
	}
	
	/*
	 执行修改
	*/
	public function update(){
		if(!IS_POST) $this->redirect('index');
        $model = new \Admin\Model\AfterSale\ConsumableModel;
		if($model->create(I('post.'),1)){
			if($model->save() !== false){
				$this->success('修改成功',U('index'));
				die;
			}
		}
		$this->error($model->getError());
	}

	/*
	 上下架
	*/
	public function tog($id){
		$id = intval($id);
		if(M()->execute('UPDATE xgj_s_consumable SET is_put = is_put^1 WHERE id='. $id)){
			echo 1;
		}else{
			echo -1;
		}
		die;
	}
}