<?php
namespace Admin\Controller\Furnish;
use \Admin\Controller\Index\AdminController;

/**
 * 后台分类控制器
 */
class CategoryController extends AdminController{
	
	private $m;
	public function __construct(){
		parent::__construct();
		$this->m = new \Admin\Model\Furnish\CategoryModel;
	}

    /**
     * 分类列表
     */
    public function index(){
    	//获取商品的分类
    	$data  = $this->m->getAll();
    	 
    	$this->assign('page', $data['page']);
    	$this->assign('cat_list', $data['cat_list']);
        $this->display();
    }
	
    /**
     * 添加分类页面
     */
    public function add(){
    	$this->display();
    }
    
	/**
	 * 添加分类操作
	 */
	public function insert(){
		if(!IS_POST) $this->redirect('index');
		if($this->m->create(I('post.'),1)){
			if($this->m->add() !== false){
				$this->success('添加成功',U('index'));
				die;
			}
		}
		$this->error($this->m->getError());
	}
	
	/**
	 * 编辑分类页面
	 */
	public function edit(){
		//接收分类的id
		$id = intval(I('cat_id/d'));
		if(empty($id)) $this->redirect('参数非法');
		$cat=$this->m->getOne($id);
		$this->assign('cat', $cat);
		$this->display();
	}
	
	/**
	 * 编辑分类操作过程
	 */
	public function update(){
		if(!IS_POST) $this->redirect('index');
		if($this->m->create(I('post.'),2)){
			if($this->m->save() !== false){
				$this->success('编辑成功',U('index'));
				die;
			}
		}
		$this->error($this->m->getError());
	}
	
	//切换分类的显示状态(隐藏/显示), 返回整数表示成功, 负数表示失败
	public function toggle(){
		$id = I('get.id/d');
		if(M()->execute("UPDATE xgj_furnish_cat SET is_show = is_show^1 WHERE cat_id=$id")){
			echo 1;
		}else{
			echo -1;
		}
	}
	
	/**
	 * 删除分类
	 */
	public function del(){
		//接收分类的id
		$id = intval(I('cat_id/d'));

		if($this->m->where(array('cat_id'=>$id))->delete()){
			$this->success('删除成功',U('index'));
			die;
		}
		$this->error($this->m->getError());
	}
	
}