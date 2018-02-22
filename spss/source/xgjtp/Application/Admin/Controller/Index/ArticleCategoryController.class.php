<?php
namespace Admin\Controller\Index;
use \Admin\Controller\Index\AdminController;
/*
广告 控制器
 */
class ArticleCategoryController extends AdminController{
    //分类显示
    public function index(){
        //实例化文章管理model类
        $articleCategoryOb=new \Admin\Model\Index\ArticleCategoryModel;
        //显示分类列表
        $articleList=$articleCategoryOb->article_cat_list();
        //模板传值
        $this->assign('articleList',$articleList);
        //显示模板
        $this->display();
    }

    //添加分类
    public function add(){
        //实例化文章管理model类
        $articleCategoryOb=new \Admin\Model\Index\ArticleCategoryModel;
        //显示子分类
        $option=$articleCategoryOb->article_cat_option();
        //模板传值
        $this->assign('permission',$permission);
        $this->assign('option',$option);
        //显示模板
        $this->display();
    }
    //处理添加分类页面提交过来的数据
    public function insert(){
        //数据源
        $data=array(
            'cat_name'=>trim($_POST['cat_name']),
            'parent_id'=>intval($_POST['parent_id']),
            'sort_order'=>intval($_POST['sort_order']),
        );
        //添加一条记录到数据库
        $rs=M('xgj_article_cat')->add($data);
        if ($rs) {
            //提示信息
            $this->success('文章分类添加成功，正在跳转...','index');
                die();
        }else{
            //提示信息
            $this->error('文章分类添加失败，正在跳转...');
                die();
        }
    }

    //修改分类
    public function edit(){
        //获取所需的唯一id
        $cat_id=intval($_GET['cat_id']);
        //实例化文章管理model类
        $articleCategoryOb=new \Admin\Model\Index\ArticleCategoryModel;
        //显示一条编辑信息
        $info=$articleCategoryOb->article_cat_id($cat_id);
        //显示子分类
        $option=$articleCategoryOb->article_cat_option(0,0,$info['parent_id']);
        //模板传值
        $this->assign('permission',$permission);
        $this->assign('info',$info);
        $this->assign('option',$option);
        $this->display();
    }
    //执行修改操作
    public function update(){
        //获取所需的唯一id
        $cat_id=intval($_GET['cat_id']);
        //数据源
        $data=array(
                'cat_name'=>trim($_POST['cat_name']),
                'parent_id'=>intval($_POST['parent_id']),
                'sort_order'=>intval($_POST['sort_order']),
        );
        //修改一条记录到数据库
        $rs=M('xgj_article_cat')->where("cat_id=$cat_id")->setField($data);
        if ($rs) {
            //提示信息
            $this->success('文章分类编辑成功，正在跳转...',U('index'));
                die();
        }else{
            //提示信息
            $this->error('文章分类编辑失败，正在跳转...');
                die();
        }
        
    }

    //删除分类
    public function del(){
        //获取所需的唯一id
        $cat_id=intval($_GET['cat_id']);
        //实例化文章管理model类
        $articleCategoryOb=new \Admin\Model\Index\ArticleCategoryModel;
        //删除一条文章分类信息
        $rs=$articleCategoryOb->article_cat_del($cat_id);
        if ($rs) {
            //提示信息
            $this->success('文章分类删除成功，正在跳转...');
                die();
        }else{
            //提示信息
            $this->error('文章分类删除失败，正在跳转...');
                die();
        }
    }

}