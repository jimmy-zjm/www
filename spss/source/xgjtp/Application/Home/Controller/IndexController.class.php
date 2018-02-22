<?php
namespace Home\Controller;
use Think\Controller;
/*
后台首页控制器
 */
class IndexController extends Controller {
    public function index(){
       $this->display('index');
		 //$this->redirect('Admin/Index/Index/index');
    }
}