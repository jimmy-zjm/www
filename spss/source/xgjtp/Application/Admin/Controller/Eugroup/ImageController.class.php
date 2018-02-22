<?php
namespace Admin\Controller\Eugroup;
use \Admin\Controller\Index\AdminController;

/**
 * 后台图片空间控制器
 */
class ImageController extends AdminController{
    private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Admin\Model\Eugroup\ImageModel;
    }

    public function index(){
        //获取所有的商品图片
        $data = $this->m->getAll();
        $this->assign('image_list', $data['image_list']);
        $this->assign('page', $data['page']);
        $this->display();
    }
}

