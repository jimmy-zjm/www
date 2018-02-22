<?php
namespace Admin\Controller\Index;
use \Think\Controller;
/*
后台公共控制器, 不需要验证的控制器,用于显示登陆页面,执行登陆验证....
 */
class PublicController extends Controller{

    /*
    显示登陆页面
     */
    public function login(){
        $this->display();
    }

    /*
    退出登陆
     */
    public function loginOut(){
        unset($_SESSION['admin_user']);
        $this->success('退出成功',U('login'));
        die;
    }

    /*
    验证登陆
     */
    public function dologin(){
        $model = new \Admin\Model\Index\ManagerModel;
        if($model->validate($model->login_validate)->create(I('post.'),5)){
            if($user = $model->where(array('user_name'=>I('post.username')))->find()){
                $password = md5(I('post.password').C('MD5_KEY'));
                if($user['password'] == $password){
                    if($user['is_lock']==0 || $user['user_id']==1){
                        //登陆成功
                        $this->logonSuccess($user);
                    }else{
                        $this->error('你的账号已经锁定, 请联系管理员');
                    }
                }else{
                    $this->error('密码不正确');
                }
            }else{
                $this->error('用户名不存在');
            }
        }else{
            $this->error($model->getError());
        }

    }

    /*
    登陆成功之后
     */
    private function logonSuccess($user){
        unset($user['password']);

        //记住密码功能
        if(I('post.remember_pass')==1){
            setcookie(session_name(),session_id(),time()+7*86400,'/');
        }

        //记录session
        session('admin_user', $user);

        //记录最后登陆时间
        M('xgj_admin_user')->where(array('user_id'=>$user['user_id']))->setField('last_login',time());
        $this->redirect('Admin/Index/Index/index');
    }

    /*
    显示验证码
     */
    public function showVerify(){
        $config = array(
            'length'=>4,
            'fontSize'=>24,
            'useCurve'=>false,
            'useNoise'=>false,
        );
        $verify = new \Think\Verify($config);
        $verify->entry();
    }

    /*
    防止空操作
     */
    public function __call($func_name, $args){
        if(APP_DEBUG){
            die('方法:'.$func_name.'不存在');
        }else{
            echo '非法请求';
            send_http_status('404');
            die;
        }
    }
}