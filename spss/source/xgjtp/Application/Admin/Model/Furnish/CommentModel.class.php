<?php
namespace Admin\Model\Furnish;
use \Think\Model;
/**
 * 品牌model
 */
class CommentModel extends Model{
	protected $trueTableName='xgj_furnish_comment';
	/**
     * 分页
     * @param unknown $page
     * @return multitype:unknown
     */
    function furnish_show_list(){
        //分页
        $total        = $this->where('status > 0')->count();
        $page         = getPage($total, C('FURNISH_COMMENT_PAGE_SIZE'));
        $data['comment_page'] = $page['page'];
        $sql = "SELECT * FROM 
                    xgj_furnish_comment c 
                left join 
                    xgj_furnish_goods g 
                on 
                    c.goods_id=g.goods_id 
                left join 
                    xgj_furnish_quote q 
                on 
                    c.quote_id=q.quote_id 
                where 
                    c.status > 0
                order by 
                    c.comment_id desc 
                limit {$page['limit']}";
        $data['comment_show_list']=M()->query($sql);
        return $data;
    }
    
    /**
     * 根据id查询一条用户评价信息
     * @return array
     */
    function comment_id($comment_id){
         
        $sql="SELECT * FROM xgj_furnish_comment c left join xgj_furnish_goods g on c.goods_id=g.goods_id left join xgj_furnish_quote q on c.quote_id=q.quote_id where c.comment_id={$comment_id}";
        
        $result=M()->query($sql);
         
        return $result;
    }
    
    /**
     * 根据id删除产品材料列表
     * @return array
     */
    function del_comment_id($comment_id){
                 
        $result=M()->query("delete from xgj_furnish_comment where comment_id=$comment_id");
         
        return $result;
    }
}