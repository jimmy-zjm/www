<?php
namespace Admin\Controller\Eugroup;
use \Admin\Controller\Index\AdminController;

/**
 * 后台分类控制器
 */
class CategoryController extends AdminController{

    //分类显示
    public function index(){
    	$cateList = D('Xgj_eu_category');
    	// $map['class_id'] = '1';
    	$data = $cateList->select();

    	$modelCat = new \Admin\Model\Eugroup\CategoryModel('Category','xgj_eu_');
    	$cateData = $modelCat->getCatTree($data);


    	$this->assign('cateData',$cateData);

    	$this->display();
    }

	//添加分类
	public function add(){
		$cateList = D('Xgj_eu_category');
    	// $map['class_id'] = '1';
		$data = $cateList->select();

		$modelCat = new \Admin\Model\Eugroup\CategoryModel('Category','xgj_eu_');
		$cateData = $modelCat->getCatTree($data);

		$this->assign('cateData',$cateData);
		$this->display();
	}
	//处理添加分类页面提交过来的数据
	public function insert(){
		$doAdd = D('Xgj_eu_category');
		$data = $doAdd->create();

		if ($data){
			if ($doAdd->add()){
				$this->success('分类添加成功','index');
				die();
			}
		}
		$this->error($doAdd->getError());
	}

	//修改分类
	public function edit(){
		//获取待修改的分类信息
		$id = $_GET['pid'];
		$data=D('Xgj_eu_category');
		$dataOne = $data->find($id);//子类的父类信息
		$dataOne['father'] = $dataOne['name'];

		$data2 = D('Xgj_eu_category');
		$id2 = $_GET['id'];
		$dataTwo = $data2->find($id2);

		$dataOne['name'] = $dataTwo['name'];
		$dataOne['order'] = $dataTwo['order'];
		$dataOne['pid'] = $_GET['pid'];
		$dataOne['id'] = $_GET['id'];

		$this->assign('dataOne',$dataOne);

		//为上级分类下拉选项获取分类信息
		$cateList = D('Xgj_eu_category');
    	// $map['class_id'] = '1';
		$data = $cateList->select();

		$modelCat = new \Admin\Model\Eugroup\CategoryModel('Category','xgj_eu_');
		$cateData = $modelCat->getCatTree($data);

		$this->assign('cateData',$cateData);

		$this->display();
	}
	//执行修改操作
	public function update(){

		$doMod = D('Xgj_eu_category');
		$data = $doMod->create();

		if (!empty($data)){
			if ($doMod->save()){
				$this->success('分类修改成功','index');
				die();
			}
		}
		$this->error($doMod->getError());
	}

	//删除分类
	public function delete(){

		if(empty($_GET)){
			$this->redirect('index');
		}

		$del = D('Xgj_eu_category');

		$where = "pid=$_GET[id]";
		$arr = $del->where($where)->find();

		if(!empty($arr)){
			echo "<script type='text/javascript'>alert('该分类下有子分类不能删除！');history.back();</script>";

			return false;
		}

		$where = "id=$_GET[id]";
		if ($del->where($where)->delete()){
			$this->success('删除成功');
		}
		exit();
	}


}