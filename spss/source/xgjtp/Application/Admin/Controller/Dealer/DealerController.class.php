<?php
namespace Admin\Controller\Dealer;
use \Admin\Controller\Index\AdminController;

/**
 * 后台服务商控制器
 */
class DealerController extends AdminController{

    public function index(){
        /****************开始****************/
        unset($_SESSION['d_service_city_alls']);
        unset($_SESSION['d_service_city_all']);
        unset($_SESSION['d_service_city']);
        /****************结束****************/
    	$model = new \Admin\Model\Dealer\DealerModel;
    	$data = $model->getAll();
    	$this->assign('dealer_list', $data['dealer_list']);
    	$this->assign('page',$data['page']);

        $this->display();
    }


    /*
        显示添加服务商的页面
     */
    public function add(){
        //查询出顶级的分类
		$area = getPCD();
    	$this->assign('area',$area); 
        $this->display();
    }

    /**
     * 添加服务商操作
     * @return [type] [description]
     */
    public function insert(){
        if(!IS_POST) $this->redirect('index');
        $model = new \Admin\Model\Dealer\DealerModel;
        $_POST['d_province']=getPCDName($_POST['province']);		
        $_POST['d_city']=getPCDName($_POST['city']);
        $_POST['d_area']=getPCDName($_POST['district']);
        /****************开始****************/
        if (!empty($_SESSION['d_service_city_all'])) {
            $_POST['d_service_city_all']=$_SESSION['d_service_city_all'];
        }
        /*if (!empty($_SESSION['d_service_city'])) {
            $_POST['d_service_city']=$_SESSION['d_service_city'];
        }*/
        /****************结束****************/
        if($model->create(I('post.'), 1)){
            if($model->add()){
                unset($_SESSION['d_service_city_alls']);
                unset($_SESSION['d_service_city_all']);
                //unset($_SESSION['d_service_city']);
                $this->success('服务商添加成功',U('index?p='.I('get.p')));
                die;
            }
        }

        $this->error($model->getError());
    }

    function serviceCity(){
        $pcd = I('get.pcd');
        $pcd = explode('-', $pcd);
        foreach ($pcd as $k => $v) {
            $city .= '-'.getPCDName($v);
        }
        $city = trim($city,'-');
        echo $city;
    }


    /**
     * 删除服务商
     * @return [type] [description]
     */
    public function del(){
        $id = I('get.d_id/d');
        $model = new \Admin\Model\Dealer\DealerModel;
        if($model->delete($id)){
            $this->success('服务商删除成功',U('index?p/'.I('get.p')));
			die;
        }else{
            $this->error($model->getError());
        }
    }
    
	//修改页面
    public function edit(){
		$id=intval($_GET['d_id']);
		if(empty($id)) $this->redirect('参数非法');
		$model = new \Admin\Model\Dealer\DealerModel;
		$data=$model->getOne($id);
        /****************开始****************/
        $city = explode('|', $data['dealer']['d_service_city_all']);
        if ($city['0']=='') {
            unset($city['0']);
        }
        $this->assign('city', $city);
        /****************结束****************/
		//省市区回填
		$data['dealer']['d_province_id']=M('xgj_area')->where(array('name'=>$data['dealer']['d_province'],'type'=>'1'))->getField('id');
		$data['dealer']['d_city_list']=M('xgj_area')->where(array('pid'=>$data['dealer']['d_province_id']))->select();
		$data['dealer']['d_city_id']=M('xgj_area')->where(array('name'=>$data['dealer']['d_city'],'type'=>'2'))->getField('id');
		$data['dealer']['d_area_list']=M('xgj_area')->where(array('pid'=>$data['dealer']['d_city_id']))->select();
		$area = getPCD();
    	$this->assign('area',$area); 
		//省市区回填结束
        $this->assign('dealer', $data['dealer']);
        $this->assign('dealer_image_list', $data['image']);
        $this->display();
    }

    //执行修改
    public function update(){
       	if(!IS_POST) $this->redirect('index');
       	$model = new \Admin\Model\Dealer\DealerModel;
       	$_POST['d_province']=getPCDName($_POST['province']);		
        $_POST['d_city']=getPCDName($_POST['city']);
        $_POST['d_area']=getPCDName($_POST['district']);
        //var_dump($_POST);exit;
		if($d = $model->create($_POST,2)){
			if($model->save() !== false){
				$this->success('修改成功',U('index?p/'.I('get.p')));
				die;
			}
		}
		$this->error($model->getError());
    }


	/*
	 切换展厅图片的显示或者隐藏
	*/
	public function toggleImage($id){
		$id = intval($id);
		if(empty($id)) die;
		if(M()->execute('UPDATE xgj_furnish_dealer_img SET is_show = is_show^1 WHERE id='. $id)){
			echo 1;
		}else{
			echo -1;
		}
		die;
	}
	
	/*
	 删除展厅的图片
	*/
	public function deleteImage($id){
		$id = intval($id);
		if(empty($id)) die;
		$image = M('xgj_furnish_dealer_img')->find($id);
		if(M('xgj_furnish_dealer_img')->delete($id)){
			//删除图片文件
			deleteImage($image['url'],C('IMG_THUMB'));
			echo 1;
		}else{
			echo -1;
		}
		die;
	}
	
	public function info(){
		$id=intval($_GET['d_id']);
		if(empty($id)) $this->redirect('参数非法');
		$model = new \Admin\Model\Dealer\DealerModel;
		$data=$model->getOne($id);
        /****************开始****************/
        $city = explode('|', $data['dealer']['d_service_city_all']);
        if ($city['0']=='') {
            unset($city['0']);
        }
        $this->assign('city', $city);
        /****************结束****************/
		$this->assign('dealer', $data['dealer']);
		$this->assign('d_id',$id);
		$this->assign('dealer_image_list', $data['image']);
		$this->display();
	}


    /****************开始****************/
    /**
     *  添加服务城市
     **/
   /* public function aaaa(){
        if (empty($_SESSION['d_service_city_all'])) {
            $_SESSION['d_service_city'] = $_GET['city'];
            $_SESSION['d_service_city_all']=$_GET['province'].'-'.$_GET['city'].'-'.$_GET['area'];
            // $_SESSION['d_service_city_all']=$_GET['province'].'-'.$_GET['city'].'-'.$_GET['area'].'&nbsp&nbsp&nbsp&nbsp<a href="dealer/bbbb?">删除</a>';
        }else{
            $_SESSION['d_service_city'] = $_SESSION['d_service_city'].'|'.$_GET['city'];
            $_SESSION['d_service_city_all']=$_SESSION['d_service_city_all'].'|'.$_GET['province'].'-'.$_GET['city'].'-'.$_GET['area'];
            // $_SESSION['d_service_city_all']=$_SESSION['d_service_city_all'].'<br>'.$_GET['province'].'-'.$_GET['city'].'-'.$_GET['area'].'&nbsp&nbsp&nbsp&nbsp<a href="dealer/bbbb?">删除</a>';
        }
        $data = explode('|', $_SESSION['d_service_city_all']);
        if ($data['0']=='') {
            unset($data['0']);
        }
        $_SESSION['d_service_city_alls'] = $data;
        foreach ($data as $key => $value) { 
            echo $value.'&nbsp&nbsp&nbsp&nbsp<a href="bbbb?city='.$key.'">删除</a></br>';
        }

    }*/

    /**
     *  删除服务城市
     **/
    /*public function bbbb(){
        $city = $_GET['city'];
        unset($_SESSION['d_service_city_alls'][$city]);

        $data = explode('|', $_SESSION['d_service_city']);
        if ($data['0']=='') {
            unset($data['0']);
        }
        unset($data[$city]);
        $_SESSION['d_service_city'] = implode('|', $data);
        $_SESSION['d_service_city_all'] = implode('|', $_SESSION['d_service_city_alls']);
        $this->success('删除成功',U('add'));
    }*/


    /**
     *  添加服务城市(修改)
     **/
    public function add_city(){
        $data = M('xgj_furnish_dealer')->where("d_id=".$_GET['id'])->select();
        if (empty($data['0']['d_service_city_all'])) {
            $city=$_GET['province'].'-'.getPCDName($_GET['city']).'-'.getPCDName($_GET['area']);
            //$city1=$_GET['city'];
        }else{
            $city=$data['0']['d_service_city_all'].'|'.getPCDName($_GET['province']).'-'.getPCDName($_GET['city']).'-'.getPCDName($_GET['area']);
            //$city1=$data['0']['d_service_city'].'|'.$_GET['city'];;
        }

        $citydata['d_service_city_all']=$city;
        //$citydata['d_service_city']=$city1;

        $save = M('xgj_furnish_dealer')->where("d_id=".$_GET['id'])->save($citydata);
        if ($save == 1) {
            $cityarray = explode('|', $city);
            foreach ($cityarray as $key => $value) { 
                echo $value.'&nbsp&nbsp&nbsp&nbsp<a href="'.U('edit_city',array('id'=>$data['0']['d_id'],'city'=>$key)).'">删除</a></br>';
            }
        }else{
            $this->error('删除失败',U("edit",array('d_id'=>$data['0']['d_id'])));
        }
        
    }

    /**
     *  删除服务城市(修改)
     **/
    public function edit_city(){
        $id = $_GET['id'];
        $city = $_GET['city'];
        $data = M('xgj_furnish_dealer')->where("d_id=".$_GET['id'])->select();
        $cityarrayall = explode('|', $data['0']['d_service_city_all']);
        $cityarray = explode('|', $data['0']['d_service_city']);
        unset($cityarrayall[$city]);
        unset($cityarray[$city]);
        $citydata['d_service_city_all'] = implode('|', $cityarrayall);
        $citydata['d_service_city'] = implode('|', $cityarray);
        $save = M('xgj_furnish_dealer')->where("d_id=".$_GET['id'])->save($citydata);
        if ($save == 1) {
            $this->success('删除成功',U("edit",array('d_id'=>$data['0']['d_id'])));
        }else{
            $this->error('删除失败',U("edit",array('d_id'=>$data['0']['d_id'])));
        }
    }


    //查询省市县三级联动
    public function area(){
    	$id = $_GET['v'];
    	$return = M('xgj_area')->where("pid=$id")->field('id,name')->select();
    	echo json_encode($return);
    }



    /****************结束****************/
}