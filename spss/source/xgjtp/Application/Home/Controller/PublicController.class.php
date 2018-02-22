<?php
namespace Home\Controller;
use Think\Controller;
/*
前台公共控制器, 不需要验证的控制器,用于显示登陆页面,执行登陆验证....
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
        unset($_SESSION['userId']);
        unset($_SESSION['userName']);
        $this->success('退出成功',U('login'));
        die;
    }

    /*
    验证登陆
     */
    public function dologin(){
        //var_dump($_POST);exit;
        $login=D('Public');
        // 自动验证 创建数据集
        if ($data = $login->create(I('post.'))) {
            echo '1';die;
            if($user = M('xgj_users')->where(array('user_name'=>I('post.userName')))->find()){
                echo '2';die;
                $password = md5(I('post.passWord'));
                if($user['password'] == $password){
                    echo '3';die;
                    $_SESSION['userId']=$user['user_id'];
                    $_SESSION['userName']=$user['user_name'];
                    //登陆成功
                    $this->redirect('Home/Index/index');
                }else{
                    echo '4';die;
                    $this->error('密码不正确');
                }
            }else{
                echo '5';die;
                $this->error('用户名不存在');
            }
        }else{
            var_dump($data);exit;
            echo '6';die;
            $this->error($login->getError());
        }
        
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