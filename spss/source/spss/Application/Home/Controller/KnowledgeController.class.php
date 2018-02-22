<?php
namespace Home\Controller;

class KnowledgeController extends BaseController {

    
    public function index(){
		$data=D('index')->article_cat(58);
		foreach($data as $key=>&$val){
			$val['list']=D('index')->article($val['cat_id']);
		}
		/**********************/
        //查询头部广告
        $map['is_on']     = 1;
        $map['ad_pos_id'] = 16;
        $image = M('xgj_ad')->where($map)->getField('image');
        $this->assign('image',getimage($image)); 
        /**********************/
		//var_dump($data);die();
        $this->assign('data',$data); 
        $this->display();
    }
    public function detail(){
		$id=I('id');
		if(empty($id)) 
			$this->redirect("knowledge/index");
		$data=D('index')->artdetail($id);
		
        $this->assign('data',$data); 
        $this->display();
    }

    public function  getuserinfo(){
        $user_id=$_SESSION['user']['userId']; 
        $user_name=$_SESSION['user']['userName'];
        $faceurl=M('xgj_users')->where(['user_id'=>$user_id])->getField('face');
        $faceUrl=$faceurl?$faceurl:"UserFace/face.jpg";
        if($user_id){
            $ret=array(  
            "is_login"=>1, //已登录，返回登录的用户信息
            "user"=>array(
            "user_id"=>$user_id,
            "nickname"=>$user_name,
            "img_url"=>'/Public/Uploads/'.$faceUrl,
            "profile_url"=>"",
            "sign"=>"**" //注意这里的sign签名验证已弃用，任意赋值即可
            ));
        }else{
            $ret=array("is_login"=>0);//未登录
        }
        echo $_GET['callback'].'('.json_encode($ret).')';
    }

    public function getout(){
        $user_id=$_SESSION['user']['userId']; 
        if($user_id){
            unset($_SESSION['user']);
            $_SESSION['auto'] = 'false';
            $return=array(
            'code'=>1,
            'reload_page'=>1
            );
        }else{
            $return=array(
            'code'=>1,
            'reload_page'=>1
            );
        }
        echo json_decode($return);
    }
    

}