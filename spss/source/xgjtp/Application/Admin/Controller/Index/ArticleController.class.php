<?php
namespace Admin\Controller\Index;
use \Admin\Controller\Index\AdminController;
/*
文章 控制器
 */
class ArticleController extends AdminController{
    private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Admin\Model\Index\AdModel;
    }

    /*
    文章列表
     */
    public function index(){
        //实例化文章管理model类
        $articleOb=new \Admin\Model\Index\ArticleModel;
        //显示列表内容
        $data=$articleOb->show_list();
        //实例化文章管理model类
        $articleCategoryOb=new \Admin\Model\Index\ArticleCategoryModel;
        //显示子分类
        $option=$articleCategoryOb->article_cat_option();
        //模板传值
        $this->assign('option',$option);
        $this->assign("page",$data['page']);
        $this->assign('article_list',$data['list']);
        //显示模板
        $this->display();
    }

    /*
    添加文章
     */
    public function add(){
        //实例化文章管理model类
        $articleCategoryOb=new \Admin\Model\Index\ArticleCategoryModel;
        //显示子分类
        $option=$articleCategoryOb->article_cat_option();
        //模板传值
        $this->assign('option',$option);
        //显示模板
        $this->display();
    }

    /*
    执行添加文章
     */
    public function insert(){
        //如果有图片就上传
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
            $info = uploadOne('image','Article','IMG_exts');
            if($info['code']==1){
                $_POST['image'] = $info['images'];
            }else{
                $this->error("{$info['error']}");
                return false;
            }
        }else{
            $_POST['image']='';
        }
        //数据源
        $data=array(
                'image'=>$_POST['image'],
                'order'=>trim($_POST['order']),
                'title'=>trim($_POST['title']),
                'content'=>html_filter($_POST['content']),
                'cat_id'=>intval($_POST['parent_id']),
                'add_time'=>intval(time()),
        );
        //添加一条记录到文章表中
        $rs=M('xgj_article')->add($data);
        if ($rs) {
            //提示信息
            $this->success('文章添加成功，正在跳转...','index');
                die();
        }else{
            //提示信息
            $this->error('文章添加失败，正在跳转...');
                die();
        }
    }


    /*
    执行删除文章
     */
    public function del(){
        //获取指定信息
        $article_id=intval($_GET['article_id']);
        //实例化文章管理model类
        $articleOb=new \Admin\Model\Index\ArticleModel;
        //删除一条记录
        $rs=$articleOb->del_article_article_id($article_id);
        //判断并显示提示信息
        if ($rs) {
            //提示信息
            $this->success('文章删除成功，正在跳转...');
                die();
        }else{
            //提示信息
            $this->error('文章删除失败，正在跳转...');
                die();
        }
    }


    /*
    显示修改文章的页面
     */
    public function edit(){
        //获取指定信息
        $article_id=intval($_GET['article_id']);
        //实例化文章管理model类
        $articleOb=new \Admin\Model\Index\ArticleModel;
        $articleCategoryOb=new \Admin\Model\Index\ArticleCategoryModel;
        //根据id获取一条信息
        $article=$articleOb->article_article_id($article_id);
        if (!empty($article['image'])) {
            $show=1;
        }
        $article['image'] = getImage($article['image']);
        //var_dump($article);exit;
        //显示子分类
        $option=$articleCategoryOb->article_cat_option(0,0,$article['cat_id']);
        //模板传值
        $this->assign('option',$option);
        $this->assign('show',$show);
        $this->assign('article',$article);
        //显示模板
        $this->display();
    }

    /*
    执行修改文章
     */
    public function update(){
        // echo '<pre>';
        // var_dump($_POST);exit;
        //获取指定信息
        $article_id=intval($_GET['article_id']);
        //如果有图片就上传
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
            $info = uploadOne('image','Article','IMG_exts');

            if($info['code']==1){
                $_POST['image'] = $info['images'];
            }else{
                $this->error = $info['error'];
                return false;
            }

            //删除老的图片
            $old_img = M('xgj_article')->where("article_id=$article_id")->getField('image');
            if(!empty($old_img)) deleteImage($old_img);
            //数据源
            $data=array(
                    'image'=>$_POST['image'],
                    'order'=>trim($_POST['order']),
                    'title'=>trim($_POST['title']),
                    'content'=>html_filter($_POST['content']),
                    'cat_id'=>intval($_POST['parent_id']),
                    'add_time'=>intval(time()),
            );
            //修改一条文章信息
            $rs=M('xgj_article')->where("article_id=$article_id")->setField($data);
            //判断显示提示信息并跳转
            if ($rs) {
                //提示信息
                $this->success('文章修改成功，正在跳转...',U('index'));
                    die();
            }else{
                //提示信息
                $this->error('文章修改失败，正在跳转...');
                    die();
            }
        }else{
            //数据源
            $data=array(
                    'title'=>trim($_POST['title']),
                    'order'=>trim($_POST['order']),
                    'content'=>html_filter($_POST['content']),
                    'cat_id'=>intval($_POST['parent_id']),
                    'add_time'=>intval(time()),
            );
            //修改一条文章信息
            $rs=M('xgj_article')->where("article_id=$article_id")->setField($data);
            //判断显示提示信息并跳转
            if ($rs) {
                //提示信息
                $this->success('文章修改成功，正在跳转...',U('index'));
                    die();
            }else{
                //提示信息
                $this->error('文章修改失败，正在跳转...');
                    die();
            }
        }
    }

    /**
    显示图片
    */
    function show_image(){
        //获取指定信息
        $article_id=intval($_GET['article_id']);
        //实例化文章管理model类
        $articleOb=new \Admin\Model\Index\ArticleModel;
        //根据id获取一条信息
        $article=$articleOb->article_article_id($article_id);
        $image = getImage($article['image']);
        //var_dump($image);exit;
        $this->assign('image',$image);
        $this->display();
    }

    /**
    删除图片
    */
    function del_image(){
        //获取指定信息
        $article_id=intval($_GET['article_id']);
        //删除老的图片
        $old_img = M('xgj_article')->where("article_id=$article_id")->getField('image');
        if(!empty($old_img)){
            deleteImage($old_img);
            //提示信息
            $this->success('图片删除成功，正在跳转...',U('edit',array('article_id'=>$article_id)));
                die();
        }else{
            //提示信息
            $this->success('图片删除失败，正在跳转...',U('edit',array('article_id'=>$article_id)));
                die();
        } 
    }
}