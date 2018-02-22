<?php
namespace Admin\Model\Index;
use \Think\Model;

class ArticleModel extends Model{
	protected $trueTableName = 'xgj_article';
	/**
	 * 分页
	 * @param unknown $page
	 * @return multitype:unknown
	 */
	function show_list(){
        //拼凑条件
        $map = array();
        if(isset($_GET['send'])){
            $cat_id   = I('cat_id')?I('cat_id/d'):'';
            
            if(!empty($cat_id)){
                $ids=$this->getParentId($cat_id);
                $map['a.cat_id'] = array('in',$ids);
            }
        }
        //var_dump($map);exit;
	   //获取文章列表
        $total = M('xgj_article')->alias('a')->where($map)->count();
        $page  = getPage($total,C('Article_PAGE_SIZE'));
        $data['page']=$page['page'];
        $data['list']  = M('xgj_article')->alias('a')->field('a.*,c.*')->join('LEFT JOIN xgj_article_cat AS c ON a.cat_id=c.cat_id')->where($map)->order('a.cat_id DESC')->limit($page['limit'])->select();
        return $data;
    }
    function getParentId($id){
        $result=M('xgj_article_cat')->where("parent_id=$id")->select();
        $cat_id='';
        if(empty($result)){
            $cat_id=$id.',';
        }else{
            foreach($result as $k=>$v){
                $cat_id.=$id.','.$v['cat_id'].',';
            }
        }
        $cat_id=rtrim($cat_id,',');
        return  $cat_id;
    }

// $result=M('xgj_article_cat')->where("cat_id=$cat_id")->find();
//         $cat_id='';
//         if($result['parent_id']==0){
//             $cat_id=$result['cat_id'].',';
//         }else{
//             $data=M('xgj_article_cat')->where("parent_id=$cat_id")->find();
//             foreach($data as $k=>$v){
//                 $cat_id.=$v['cat_id'].',';
//             }
//         }        
//         $cat_id=rtrim($cat_id,',');
//         return  $cat_id;

    /**
     * 根据id查询一条文章信息
     * @return array
     */
    function article_article_id($article_id){
    	$result=M('xgj_article')->where("article_id=$article_id")->find();
    	return $result;
    }
    
    /**
     * 根据id删除文章列表中的一条数据
     * @return array
     */
    function del_article_article_id($article_id){
    	$result=M('xgj_article')->where("article_id=$article_id")->delete();
    	return $result;
    }

}