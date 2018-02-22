<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");
require_once (WWW_DIR . "/libs/db.php");

class padModel{
	private $m;

    public function __construct(){
       $this->m = new db();
    }
	//获取省市县中的省份
	function getPCD(){
		//$area = M('xgj_area')->where('pid=100000')->field('id,name')->select();
		//return $area;
		$db=new db();
		$sql = "SELECT id,name FROM xgj_area WHERE pid = '100000'";
		$result=$db->getAll($sql);
		return $result;
	}

	//根据id获取省市县名称
	function getPCDName($id){
		//$area = M('xgj_area')->where("id=$id")->getField('name');
        $db=new db();
		$sql = "SELECT name FROM xgj_area WHERE id = $id ";
		$result=$db->getOne($sql);
		return $result;

	}

    //根据pid获取省市县名称
    function getPCDFind($pid){
        $db=new db();
        $sql = "SELECT id,name FROM xgj_area WHERE pid = $pid ";
        $result=$db->getAll($sql);
        return $result;
    }

    //根据名称获取省市县id
    function getPCDId($name,$lv=2){
        $db=new db();
        $where = "name = '{$name}'";
        if ($lv==1) $where .= " and pid = '100000'";
        else if ($lv==2) $where .= " and pid <> '100000'";
        $sql = "SELECT id FROM xgj_area WHERE $where ";
        $result=$db->getOne($sql);
        return $result;

    }

    //查询所有系统分类
	public function quoteCat(){
		$db=new db();
		$sql = "SELECT * FROM xgj_furnish_cat WHERE is_show = '1'";
		$result=$db->getAll($sql);
		return $result;
	}

    //查询所有系统分类
    public function getCatName($c_id){
        $db=new db();
        $sql = "SELECT * FROM xgj_furnish_cat WHERE cat_id = $c_id";
        $result=$db->getRow($sql);
        return $result;
    }

	public function quoteList($catId){
		$db=new db();
		$sql = "SELECT quote_id,quote_name,is_putaway,cat_id FROM xgj_furnish_quote WHERE cat_id = $catId and is_putaway = '1'";
		$result=$db->getAll($sql);
		return $result;
	}

    public function userCity(){
        $db=new db();
        $sql = "SELECT * FROM pad_user WHERE id = '{$_SESSION['pad_id']}'";
        $result=$db->getRow($sql);
        
        $service = explode('|', $result['p_service_city_all']);
        foreach ($service as $k => $v) {
            $city = explode('-', $v);
            $result['prov'][] = $city['0'];
            $result['service'][$city['0']][] = $city['1'];
        }
        return $result;
    }

    public function getCustomerRow($name,$tel){
        $sql = "SELECT * FROM pad_customer WHERE name = '{$name}' AND tel = '{$tel}'";
        $result = $this->m->getRow($sql);

        if (!empty($result )&&$result['u_id']!=$_SESSION['pad_id']) {
            
            $sql = "SELECT * FROM pad_user WHERE id='{$result['u_id']}'";
            $re = $this->m->getRow($sql);
            if ($re['pid']!=$_SESSION['pad_id']) {
                // echo "<SCRIPT type='text/javascript'>alert('该客户意向产品不存在!!!');history.back();</SCRIPT>";exit;
                echo "<SCRIPT type='text/javascript'>alert('您无权限对此客户操作!!!');history.back();</SCRIPT>";exit;
            }
        }

        return $result;
    }

    public function getCustomer($id){
        $sql = "SELECT * FROM pad_customer WHERE id = $id";
        $result = $this->m->getRow($sql);
        return $result;
    }

    public function addCustomer($data){
        $result = $this->m->add('pad_customer',$data);
        return $result;
    }

    public function updateCustomer($data,$id){
        $result = $this->m->update('pad_customer',$data,"id='{$id}'");
        return $result;
    }

    public function setup($data){
        $result = $this->m->update('pad_user',$data,"id='{$_SESSION['pad_id']}'");
        return $result;
    }

    public function selectSetUp(){
        $db = new db();
        $sql = "select * from pad_user where id='{$_SESSION['pad_id']}'";
        $result = $db->getRow($sql);
        return $result;
    }

    public function getFurnishQuote($id){
        $db = new db();
        $sql = "select * from xgj_furnish_quote where quote_id='{$id}'";
        $result = $db->getRow($sql);
        return $result;
    }

    public function getCustomerQuote($name){
        $db = new db();
        $sql = "select * from pad_customer_quote where name = '{$name}' and u_id = '{$_SESSION['pad_id']}'";
        $result = $db->getRow($sql);
        return $result;
    }

    public function getCustomerQuoteData($id){
        $db = new db();
        $sql = "select * from pad_customer_quote where id = '{$id}'"; 
        $result = $db->getRow($sql);
		if(empty($result))
			  return '';
		if($result['u_id']==$_SESSION['pad_id'])
				return $result;
		else{
				$sql1 = "select * from pad_user where id = '{$result['u_id']}'"; 
				$data = $db->getRow($sql1);
				if($data['pid']==$_SESSION['pad_id'])
					return $result;
				else
					return '';
		}
		
    }

    public function addCustomerQuote($data){
        $result = $this->m->add('pad_customer_quote',$data);
        return $result;
    }

    public function editCustomerQuote($data,$id){
        $where = "id = '{$id}' and u_id='{$_SESSION['pad_id']}'";
        $result = $this->m->update('pad_customer_quote',$data,$where);
        return $result;
    }

    public function staffList(){
        $sql = "select * from pad_user where pid = '{$_SESSION['pad_id']}'";
        $res=$this->m->getAll($sql);
        return $res;
    }

    public function updateUserOpen($id,$font){
        if ($font=='启用') $data['is_open']='1';
        else if($font=='停用') $data['is_open']='2';
        $result = $this->m->update('pad_user',$data,"id='{$id}'");
        return $result;
    }

    public function staffInfo($id){
        $sql = "select * from pad_user where id = '{$id}' and pid = '{$_SESSION['pad_id']}'";
        $re['user'] = $this->m->getRow($sql);

        if (empty($re['user'])) {
            echo "<SCRIPT type='text/javascript'>alert('该用户不存在!!!');history.back();</SCRIPT>";exit;
        }

        $re['user']['birthday1'] = substr($re['user']['birthday'], 0,4); 
        $re['user']['birthday2'] = substr($re['user']['birthday'], 4,2); 
        $re['user']['birthday3'] = substr($re['user']['birthday'], 6,2); 

        $sql = "select * from pad_customer where u_id = '{$id}' order by time desc";
        $re['customer'] = $this->m->getAll($sql);
  
        return $re;
    }

    public function updateUser($data,$id){

        if ($data['birthday2'] < '10') $data['birthday2'] = '0'.$data['birthday2'];
        if ($data['birthday3'] < '10') $data['birthday3'] = '0'.$data['birthday3'];
        
        $data['birthday'] = $data['birthday1'].$data['birthday2'].$data['birthday3'];

        unset($data['id']);
        unset($data['birthday1']);
        unset($data['birthday2']);
        unset($data['birthday3']);

        $result = $this->m->update('pad_user',$data,"id='{$id}'");
        return $result;
    }

    public function user(){
        $db=new db();
        $sql = "SELECT * FROM pad_user WHERE id = '{$_SESSION['pad_id']}'";
        $result=$db->getRow($sql);
        return $result;
    }

    public function getQuoteId($name){
        $db=new db();
        $sql = "SELECT quote_id,quote_name FROM xgj_furnish_quote WHERE quote_name like '%$name%'";
        $result=$db->getRow($sql);
        return $result;
    }
	
    public function check_login($name,$psd,$auto=''){
        $sql="select * from pad_user where name='{$name}' and psd='{$psd}' and is_open='1' and is_use='1' limit 1";
        $res=$this->m->getRow($sql);
        if($res){
            if($auto==1){
                //自动登陆, 保存登陆信息一个礼拜
                setcookie('auto_name',$res['name'],time()+86400*7,'/');
                setcookie('auto_psd',$res['psd'],time()+86400*7,'/');
                $str='1cok';
            }else{
                setcookie('auto_name',$res['name'],time()-86400*7,'/');
                setcookie('auto_psd',$res['psd'],time()-86400*7,'/');
                $str='';
            }
            if($res['is_try']==1 && $res['start_time'] > time()){
                if($this->m->update('pad_user',array('is_use'=>'0','is_try'=>1),"id={$res['id']}") != false){
                        echo "5";
                    }else{
                        echo "4";
                    }
            }elseif($res['is_try']==1 && time() > $res['end_time']){
                if($this->m->update('pad_user',array('is_use'=>'0','is_try'=>2),"id={$res['id']}") != false){
                        echo "5";
                    }else{
                        echo "4";
                    }
            }else{
                $_SESSION['pad_id']=$res['id'];
                $_SESSION['pad_user']=$res['name'];
                if($res['sessionid']!==session_id()){
                    if($this->m->update('pad_user',array('sessionid'=>session_id(),'auto_login'=>$str),"id={$_SESSION['pad_id']}") != false){
                        echo '1';
                    }else{
                        echo '4';
                    }
                }else{
                    echo "3";
                }
            }
        }else{
            echo '4';
        }
    }


    public function addPadOrder($data){
        // $list=json_encode($newLists);
        //$list  =json_decode($list);
        //var_dump($data);die;
        $result = $this->m->add('pad_order',$data);
        return $result;
    }

    public function getPadOrder($id){
        $db=new db();
        $sql = "SELECT * FROM pad_order WHERE q_id = '{$id}' and u_id = '{$_SESSION['pad_id']}'";
        $result=$db->getRow($sql);
        return $result;
    }

}

