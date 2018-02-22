<?php
namespace Admin\Controller;
use Think\Controller;
/*
后台首页控制器
 */
class IndexController extends Controller {
    public function index(){
        $this->redirect('Admin/Index/Public/login');
            return;
    }
}