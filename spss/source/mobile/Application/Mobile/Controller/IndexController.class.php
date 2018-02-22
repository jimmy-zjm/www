<?php
namespace Mobile\Controller;

class IndexController extends BaseController {

    public function index(){
    	$this->assign('home','1');
        $this->display();
    }

    public function company(){
    	$this->assign('header','公司介绍');
        $this->display();
    }

    public function contactUs(){
    	$this->assign('header','联系我们');
        $this->display();
    }

    public function followUs(){
    	$this->assign('header','关注我们');
        $this->display();
    }
    public function info(){
    	$cid = I('get.cid');
    	if (!quoteId($cid)) $this->redirect('index');
    	$this->assign('cid',$cid);
    	$this->display('info'.$cid);
    }
}