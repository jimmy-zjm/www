<?php
namespace Admin\Model\Index;
use \Think\Model;

class ArticleCategoryModel extends Model{
	protected $trueTableName = 'xgj_article_cat';
	/**
	 * 文章分类列表
	 * @param number $pid
	 * @param number $num
	 * @return string
	 */
	function article_cat_list($pid = 0, $num = 0){
		//$sql="select * from xgj_article_cat where parent_id=$pid";
		$result=M('xgj_article_cat')->where("parent_id=$pid")->select();
		$str = str_repeat ( '&nbsp;&nbsp;', $num );
		$num += 2;
		$aa='';
		foreach ($result as $v){
			$aa.="<tr>
			<td>{$v['cat_id']}</td>
			<td>$str<a href=".U('edit',array('cat_id'=>$v['cat_id'])).">{$v['cat_name']}</a></td>
			<td>{$v['sort_order']}</td>
			<td><a href=".U('edit',array('cat_id'=>$v['cat_id'])).">修改</a>|<a href=".U('del',array('cat_id'=>$v['cat_id']))." onclick='return delete()'>删除</a></td>
			</tr><tr>
			{$this->article_cat_list($v['cat_id'],$num)}
			</tr>";
		}
		return $aa;
	}
	
	/**
	 * 获取文章分类上级分类
	 * @param number $pid
	 * @param number $num
	 * @param number $curid
	 * @return string
	 */
	function article_cat_option($pid = 0, $num = 0, $curid = 0){
		//$sql="select * from xgj_article_cat where parent_id=$pid";
		$result=M('xgj_article_cat')->where("parent_id=$pid")->select();
		$str = str_repeat ( '+', $num );
		$num += 2;
		$string='';
		foreach ($result as $v){
			if ($v ['cat_id'] == $curid) {
				$string.="<option selected value='{$v['cat_id']}'>|+{$str}{$v['cat_name']}</option>{$this->article_cat_option($v ['cat_id'], $num ,$curid)}";
			} else {
				$string.="<option value='{$v['cat_id']}'>|+{$str}{$v['cat_name']}</option>{$this->article_cat_option($v ['cat_id'], $num ,$curid )}";
			}
		}
		return $string;
	}
	
	/**
	 * 获取一条文章分类信息
	 * @param unknown $cat_id
	 * @return mixed
	 */
	function article_cat_id($cat_id){
		//$sql="select * from xgj_article_cat where cat_id=$cat_id";
		$result=M('xgj_article_cat')->where("cat_id=$cat_id")->find();
		return $result;
	}
	
	/**
	 * 删除一条文章分类信息
	 * @param unknown $cat_id
	 * @return PDOStatement
	 */
	function article_cat_del($cat_id){
		//$sql="select * from xgj_article_cat where parent_id=$cat_id";
		$result=M('xgj_article_cat')->where("parent_id=$cat_id")->select();
		if(!empty($result)){
			foreach($result as $v){
				$this->article_cat_del($v['cat_id']);
			}
		}
		//删除自己
		$re=M('xgj_article_cat')->where("cat_id=$cat_id")->delete();
		return $re;
	}

}