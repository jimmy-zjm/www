<?php
namespace Admin\Controller\Index;
use \Think\Controller;
/**
 * 后台基础控制器, 需要验证权限的控制都继承该控制器
 */
class AdminController extends Controller{
    protected $class_id = 1;
    //初始化方法 可以在所有方法执行前执行
    public function __construct(){
        parent::__construct();
        if (empty($_SESSION['admin_user'])) {
            $this->redirect('Admin/Index/Public/login');
            return;
        }

        //调试模式不验证权限
        if(APP_DEBUG){
            return;
        }

        //开放root的权限, 判断root权限的根据是用户id为1
        if($_SESSION['admin_user']['user_id']=='1') return;

        //当前的权限
        $curr_pri = MODULE_NAME.'/'.CONTROLLER_NAME;

        //开放后台首页的权限
        if($curr_pri =='Admin/Index/Index') return;

        //权限列表
        $pri_arr = array(
            // 权限号 1健康舒适家居 2欧洲团代购 3健康绿色食品  4机电售后服务  5文章管理  6广告管理  7权限管理
            // 权限号 => '模块名/操作名', 注意大小写
            2 =>'Admin/Eugroup',
            7 =>'Admin/Index/Manager',
			8 =>'Admin/Index/Coupon',
        );

        //当前用户的权限列表
        $pri_list = explode(',',$_SESSION['admin_user']['permission']);

        //验证权限
        $flag = false;
        foreach ($pri_list as $pri) {
            if(isset($pri_arr[$pri])){
                if(strpos($curr_pri, $pri_arr[$pri]) !== false){
                    $flag = true;
                    break;
                }else{
                    $flag = false;
                }
            }
        }

        //验证成功
        if($flag) return;

        //验证失败
        $this->error('没有权限访问','',100);
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