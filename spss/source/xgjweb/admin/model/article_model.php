<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");
/**
 * @author L
 * 文章数据模型类
 */
class article_model
{
	/**
	 * 文章分类列表
	 * @param number $pid
	 * @param number $num
	 * @return string
	 */
	function article_cat_list($pid = 0, $num = 0){
		$db=new db();
		$sql="select * from xgj_article_cat where parent_id=$pid";
		$result=$db->getAll($sql);
		$str = str_repeat ( '&nbsp;&nbsp;', $num );
		$num += 2;
		$aa='';
		foreach ($result as $v){
			$aa.="<tr>
			<td>{$v['cat_id']}</td>
			<td>{$str}<a href='?cat_edit&cat_id={$v['cat_id']}'>{$v['cat_name']}</a></td>
			<td>{$v['sort_order']}</td>
			<td><a href='?cat_edit&cat_id={$v['cat_id']}'>修改</a>|<a href='?cat_del&cat_id={$v['cat_id']}' onclick='return del()'>删除</a></td>
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
		$db=new db();
		$sql="select * from xgj_article_cat where parent_id=$pid";
		$result=$db->getAll($sql);
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
		$db=new db();
		$sql="select * from xgj_article_cat where cat_id=$cat_id";
		$result=$db->getRow($sql);
		return $result;
	}
	
	/**
	 * 删除一条文章分类信息
	 * @param unknown $cat_id
	 * @return PDOStatement
	 */
	function article_cat_del($cat_id){
		$db=new db();
		$sql="select * from xgj_article_cat where parent_id=$cat_id";
		$result=$db->getAll($sql);
		if(!empty($result)){
			foreach($result as $v){
				$this->article_cat_del($v['cat_id']);
			}
		}
		//删除自己
		$re=$db->query("delete from xgj_article_cat where cat_id=$cat_id");
		return $re;
	}
	
	/**
	 * 分页
	 * @param unknown $page
	 * @return multitype:unknown
	 */
	function show_list($page){
		$db=new db();
		$start=($page-1)*10;
		$sql = "SELECT * FROM xgj_article a join xgj_article_cat c where a.cat_id=c.cat_id order by a.article_id desc limit ".$start.",10";
		$detail=$db->getAll($sql);
		return $detail;
	}
	
	/**
	 *总条数
	 * @return string
	 */
	function show_count(){
		$db=new db();
		$sql = "SELECT count(*) FROM xgj_article";
		$result=$db->getOne($sql);
		return $result;
	}

    /**
     * 根据id查询一条文章信息
     * @return array
     */
    function article_article_id($article_id){
    	$db=new db();
    	 
    	$sql="select * from xgj_article where article_id={$article_id}";
    	
    	$result=$db->getRow($sql);
    	 
    	return $result;
    }
    
    /**
     * 根据id删除文章列表中的一条数据
     * @return array
     */
    function del_article_article_id($article_id){
    	$db=new db();
    	     	 
    	$result=$db->query("delete from xgj_article where article_id=$article_id");
    	 
    	return $result;
    }
    
}