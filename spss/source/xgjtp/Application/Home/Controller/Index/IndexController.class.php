<?php
namespace Home\Controller\Index;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->redirect('Admin/Index/Index/index');
    }
}