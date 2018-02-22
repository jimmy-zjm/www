<?php
namespace Admin\Controller\Pad;
use \Admin\Controller\Index\AdminController;
/*
用户名控制器
 */
class UserController extends AdminController{
    private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Admin\Model\Pad\UserModel;
    }

	public function index(){
        /****************开始****************/
        unset($_SESSION['p_service_city_all']);
		unset($_SESSION['p_service_city_alls']);
        /****************结束****************/

        if(!empty($_GET['name'])){
            $map['name'] = ['like',"%".I('get.name')."%"];
        }
		$map['pid']=0;
        $data  = M('pad_user')->where($map)->count();
		$page  = getPage($data,C('ADPOS_PAGE_SIZE'));
		$list = M('pad_user')->where($map)->order('id desc')->limit($page['limit'])->select();       

        $this->assign('page', $page['page']);
        $this->assign('user_list', $list);
        $this->display();
    }
	
	public function add(){
        unset($_SESSION['p_service_city_all']);
		unset($_SESSION['p_service_city_alls']);
        $area = getPCD();
        $this->assign("area",$area);
        $data = M('xgj_furnish_cat')->where("is_show = 1")->select();
        $this->assign('data', $data);        
		$this->display();
	}
	public function insert(){
		
		$num=count($_POST['company']);
		if($num!=count($_POST['province']))
				$this->error('公司信息请填写完整1');
		elseif($num!=count($_POST['city']))			
				$this->error('公司信息请填写完整2');
		elseif($num!=count($_POST['district']))
				$this->error('公司信息请填写完整3');
		elseif($num!=count($_POST['address']))
				$this->error('公司信息请填写完整4');
        $_POST['system'] = implode($_POST['system'], '|');
        /****************开始****************/
        if (!empty($_SESSION['p_service_city_all'])) {
            $_POST['p_service_city_all']=$_SESSION['p_service_city_all'];
        }

        if(!IS_POST) $this->redirect('index');
        if($this->m->create(I('post.',1))){
			$result=$this->m->add();
           if($result){
			   if(!empty(I('company')[0])){//用于判断是否填写公司信息
						foreach(I('company') as $key=>$val){
							if(  I('district')[$key]=='请选择地区'&&!empty(I('company')[$key])   )
									$this->error('用户添加成功，公司信息填写有误，请编辑修改',U('edit',array('id'=>$result)));
							$com[$key]['u_id']=$result;					
							$com[$key]['name']=$val;
							$com[$key]['prov']=getPCDName(I('province')[$key]);
							$com[$key]['city']=getPCDName(I('city')[$key]);
							$com[$key]['district']=getPCDName(I('district')[$key]);
							$com[$key]['address']=I('address')[$key];
						}
						$a=M('pad_company')->addAll($com);
				}
				if($a||$result)
					$this->success('添加成功',U('index'));
               exit;
            }
        }
        $this->error($this->m->getError());
    } 

	public function edit($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');

        $data_ = M('xgj_furnish_cat')->where("is_show = 1")->select();

        $this->assign('data', $data_);
		
        $data = M('pad_user')->find($id);
        $system = explode('|', $data['system']);
        $area = getPCD();
        $this->assign("area",$area);

        //省市区回填
		$com=M('pad_company')->where(array('u_id'=>$data['id']))->select();
		foreach($com as $key=>$val){
			$com[$key]['d_province_id']=M('xgj_area')->where(array('name'=>$val['prov'],'type'=>'1'))->getField('id');
			$com[$key]['d_city_list']=M('xgj_area')->where(array('pid'=>$com[$key]['d_province_id']))->select();
			$com[$key]['d_city_id']=M('xgj_area')->where(array('name'=>$val['city'],'type'=>'2'))->getField('id');
		    $com[$key]['d_area_list']=M('xgj_area')->where(array('pid'=>$com[$key]['d_city_id']))->select();
		}
	
        /****************开始****************/
        $city = explode('|', $data['p_service_city_all']);
        if ($city['0']=='') {
            unset($city['0']);
        }
        $data['start_time'] = date('Y-m-d ',$data['start_time']);
        $data['end_time']   = date('Y-m-d ',$data['end_time']);
        $this->assign('city', $city);
        /****************结束****************/
        $this->assign('com', $com);//公司信息
		
        $this->assign('system', $system);
        $this->assign('user', $data);
		
        $this->display();
    }


    //查询省市县三级联动
    public function area(){
        $id = $_GET['v'];
        $return = M('xgj_area')->where("pid=$id")->field('id,name')->select();
        echo json_encode($return);
    }
    /*
    执行修改
     */
    public function update($id){
        $id = intval($id);
		$num=count($_POST['company']);
		//echo $num;die();
		if($num!=count($_POST['province']))
				$this->error('公司信息请填写完整1');
		elseif($num!=count($_POST['city']))			
				$this->error('公司信息请填写完整2');
		elseif($num!=count($_POST['district']))
				$this->error('公司信息请填写完整3');
		elseif($num!=count($_POST['address']))
				$this->error('公司信息请填写完整4');
        if (!empty($_POST['system'])) $_POST['system'] = implode($_POST['system'], '|');
        $info = M('pad_user')->find($id);
        if(!$id) $this->redirect('index');
        
        if($_POST['is_use']==0)
                $data['is_use']=0;
         
        if($this->m->create(I('post.'))){
                if($this->m->save() !== false){
                    $data['system']=$info['system'];
                    $data['p_service_city_all']=$info['p_service_city_all'];
					//系统权限与服务地址需要与下级账号同步
                    if($this->m->where("pid={$info['id']}")->save($data)!==false){						
						//修改公司信息
						if(!empty(I('savecomid')[0])){
							//echo 11;die();
								foreach(I('savecomid') as $k=>$v){							
									$savecom['name']=I('savecompany')[$k];
									$savecom['prov']=getPCDName(I('saveprovince')[$k]);
									$savecom['city']=getPCDName(I('savecity')[$k]);
									$savecom['district']=getPCDName(I('savedistrict')[$k]);
									$savecom['address']=I('saveaddress')[$k];
									$a=M('pad_company')->where(array('id'=>$v))->save($savecom);
								}
								
						}
						//新增公司信息
						//var_dump(I('company'));die();
						if(!empty(I('company')[0])){
								foreach(I('company') as $key=>$val){
									if(I('district')[$key]=='请选择地区')
										$this->error('请选择地址');
									$com[$key]['u_id']=$info['id'];					
									$com[$key]['name']=$val;
									$com[$key]['prov']=getPCDName(I('province')[$key]);
									$com[$key]['city']=getPCDName(I('city')[$key]);
									$com[$key]['district']=getPCDName(I('district')[$key]);
									$com[$key]['address']=I('address')[$key];
								}
								$b=M('pad_company')->addAll($com);
						}
						if($a===false||$b===0)
								$this->error('修改失败');	
						else
								$this->success('修改成功',U('index'));						
                        die;
                    }else{
                        $this->error('主账号信息更新成功，子账号信息同步失败',U('index'));
                        die;
                    }
                }
          } 
      
    $this->error($this->m->getError());
    }

	//删除公司
	public function delcom(){
		$id=I('id');
		if(!empty($id)){
			if(M('pad_company')->where(array('id'=>$id))->delete()!==false);
				$this->success('删除成功');
		}
	}




    /*
    切换 是否启动账号
     */
    public function toggle($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');
        $info = M('pad_user')->field('pid,is_use')->find($id);
        $is_use=$info['is_use']==1?0:1;
        if($info['pid']==0){
            $where='';
            if($is_use==0){
                $where=" id=$id or pid=$id";
            }else{
                $where=" id=$id";
            }
            if(M()->execute("UPDATE pad_user SET is_use = $is_use WHERE $where")){
                $this->success('账户启用切换成功',U('index'));die;
            }else{
                $this->error('账户启用切换失败',U('index')); die;
            }
            die;
        }else{
            $use = M('pad_user')->where(array("id"=>$info['pid']))->getField('is_use');
            if($use==0){
                $this->error('请先启用主账户',U('index')); die;
            }else{
                if(M()->execute("UPDATE pad_user SET is_use = $is_use WHERE id=$id")){
                    $this->success('账户启用切换成功',U('index'));die;
                }else{
                    $this->error('账户启用切换失败',U('index')); die;
                }
                die;
            }
        }
    }


    /****************开始****************/
    /**
     *  添加服务城市
     **/
    public function aaaa(){

        if($_GET['city']==0){
            $_GET['city']='全部';
        }else{
            $_GET['city']=getPCDName($_GET['city']);
        }
        if (empty($_SESSION['p_service_city_all'])) {
            //$_SESSION['p_service_city'] = $_GET['city'];
            $_SESSION['p_service_city_all']=getPCDName($_GET['province']).'-'.$_GET['city'];
            // $_SESSION['p_service_city_all']=$_GET['province'].'-'.$_GET['city'].'-'.$_GET['area'].'&nbsp&nbsp&nbsp&nbsp<a href="dealer/bbbb?">删除</a>';
        }else{
            if (!strstr($_SESSION['p_service_city_all'],getPCDName($_GET['province']).'-'.$_GET['city']) && $_GET['city']!='全部') {
                //$_SESSION['p_service_city'] = $_SESSION['p_service_city'].'|'.$_GET['city'];
                if(!strstr($_SESSION['p_service_city_all'],getPCDName($_GET['province']).'-'.'全部')){
                    $_SESSION['p_service_city_all']=$_SESSION['p_service_city_all'].'|'.getPCDName($_GET['province']).'-'.$_GET['city'];
                }
                // $_SESSION['p_service_city_all']=$_SESSION['p_service_city_all'].'<br>'.$_GET['province'].'-'.$_GET['city'].'-'.$_GET['area'].'&nbsp&nbsp&nbsp&nbsp<a href="dealer/bbbb?">删除</a>';
            }else{
                $str='';
                if($_GET['city']=='全部'){
                    $data_ = explode('|', $_SESSION['p_service_city_all']);
                    foreach ($data_ as $key => &$value) {
                        if(strstr($value,getPCDName($_GET['province']))){
                            unset($data_[$key]);
                        }else{
                            $str.=$value.'|';
                        }
                    }
                    $_SESSION['p_service_city_all']=$str.getPCDName($_GET['province']).'-'.$_GET['city'];
                }
            } 
        }
        $data = explode('|', $_SESSION['p_service_city_all']);
        if ($data['0']=='') {
            unset($data['0']);
        }
        $_SESSION['p_service_city_alls'] = $data;
        foreach ($data as $key => $value) { 
            echo $value.'&nbsp&nbsp&nbsp&nbsp<a href="bbbb?city='.$key.'">删除</a></br>';
        }

    }

    /**
     *  删除服务城市
     **/
    public function bbbb(){
        $city = $_GET['city'];
        unset($_SESSION['p_service_city_alls'][$city]);


        $_SESSION['p_service_city_all'] = implode('|', $_SESSION['p_service_city_alls']);
        $this->success('删除成功',U('add'));
    }


    /**
     *  添加服务城市(修改)
     **/
    public function add_city(){
        if($_GET['city']==0){
            $_GET['city']='全部';
        }else{
            $_GET['city']=getPCDName($_GET['city']);
        }

        $data = M('pad_user')->where("id=".$_GET['id'])->select();

        $cityStr=$data['0']['p_service_city_all'];
        
        if (empty($cityStr)) {
            $cityStr=getPCDName($_GET['province']).'-'.$_GET['city'];
        }else{
            if (!strstr($cityStr,getPCDName($_GET['province']).'-'.$_GET['city']) && $_GET['city']!='全部') {
                if(!strstr($cityStr,getPCDName($_GET['province']).'-'.'全部')){
                    $cityStr=$cityStr.'|'.getPCDName($_GET['province']).'-'.$_GET['city'];
                }
            }else{
                $str='';
                if($_GET['city']=='全部'){
                    $data_ = explode('|', $cityStr);
                    foreach ($data_ as $key => &$value) {
                        if(strstr($value,getPCDName($_GET['province']))){
                            unset($data_[$key]);
                        }else{
                            $str.=$value.'|';
                        }
                    }
                    $cityStr=$str.getPCDName($_GET['province']).'-'.$_GET['city'];
                }
            } 
        }

        $citydata['p_service_city_all']=$cityStr;

        $save = M('pad_user')->where("id=".$_GET['id'])->save($citydata);
        //var_dump($save);die;
        if ($save !== false) {
            $cityarray = explode('|', $cityStr);
            foreach ($cityarray as $key => $value) { 
                echo $value.'&nbsp&nbsp&nbsp&nbsp<a href="'.U('edit_city',array('id'=>$data['0']['id'],'city'=>$key)).'">删除</a></br>';
            }
        }else{
            $this->error('删除失败',U("edit",array('id'=>$data['0']['id'])));
        }
        
    }

    /**
     *  删除服务城市(修改)
     **/
    public function edit_city(){
        $id = $_GET['id'];
        $city = $_GET['city'];
        $data = M('pad_user')->where("id=".$_GET['id'])->select();
        $cityarrayall = explode('|', $data['0']['p_service_city_all']);
        //$cityarray = explode('|', $data['0']['p_service_city']);
        unset($cityarrayall[$city]);
        //unset($cityarray[$city]);
        $citydata['p_service_city_all'] = implode('|', $cityarrayall);
        //$citydata['p_service_city'] = implode('|', $cityarray);
        $save = M('pad_user')->where("id=".$_GET['id'])->save($citydata);
        if ($save == 1) {
            $this->success('删除成功',U("edit",array('id'=>$data['0']['id'])));
        }else{
            $this->error('删除失败',U("edit",array('id'=>$data['0']['id'])));
        }
    }
    /****************结束****************/


    /*
    显示分配权限的页面
     */
    public function dispath($id){
        $id = intval($id);
        if(!$id) $this->redirect('index');

        $per  = M('pad_user')->where(array('id'=>$id))->getField('permission');
        if(!empty($per)){
            $this->assign('pri_list', array_filter(explode(',', $per)));
        }
        $data = M('pad_permissions')->where(array('pid'=>0,'status'=>'1'))->order('is_order desc,id')->select();
        $pl='';
        foreach ($data as $ke => $va) {
            $data[$ke]['list']= M('pad_permissions')->where("pid = {$va['id']} and status='1' ")->order('is_order desc,id')->select();
            foreach ($data[$ke]['list'] as $k => $v) {
                if($va['id']=$v['pid']){
                    $data[$ke]['pl'].='pri-'.$v['id'].',';
                }
            }
        }
       // var_dump($id,$per,$data);die;
        $this->assign('data',$data);
        $this->assign('id',$id);
        $this->display();
    }

    /*
    执行分配权限
     */
    public function dodispath(){
        $id = I('post.id/d');
        $pri=I('post.pri');
        //var_dump($pri);
        $str=''; 
        foreach ($pri as $k => $v) {
            $str.=ltrim($v,'pri-').',';
        }
        $pri_str=rtrim($str,',');
        //var_dump($pri_str,$id);exit;
        if(M('pad_user')->where(array('id'=>$id))->setField('permission', $pri_str)!==false){
            $this->success('修改成功','index');
            die;
        }
        $this->error('修改失败','index');
    }

}
