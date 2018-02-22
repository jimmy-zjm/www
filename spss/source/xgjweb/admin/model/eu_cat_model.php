<?php
header('content-type:text/html;charset=utf-8');
require_once(WWW_DIR."/conf/mysql_db.php");
/**
 * @author L
 * 团代购商品分类数据模型类
 */
class eu_cat_model
{
	/**
	 * 团代购商品分类列表
	 * @param number $pid
	 * @param number $num
	 * @return string
	 */
	function eu_cat_list($pid = 0, $num = 0){
		$db=new db();
		$sql="select * from xgj_eu_cat where parent_id=$pid";
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
			{$this->eu_cat_list($v['cat_id'],$num)}
			</tr>";
		}
		return $aa;
	}
	
	/**
	 * 获取团代购商品分类上级分类
	 * @param number $pid
	 * @param number $num
	 * @param number $curid
	 * @return string
	 */
	function eu_cat_option($pid = 0, $num = 0, $curid = 0){
		$db=new db();
		$sql="select * from xgj_eu_cat where parent_id=$pid";
		$result=$db->getAll($sql);
		$str = str_repeat ( '-', $num );
		$num += 2;
		$string='';
		foreach ($result as $v){
			if ($v ['cat_id'] == $curid) {
				$string.="<option selected value='{$v['cat_id']}'>|-{$str}{$v['cat_name']}</option>{$this->eu_cat_option($v ['cat_id'], $num ,$curid)}";
			} else {
				$string.="<option value='{$v['cat_id']}'>|-{$str}{$v['cat_name']}</option>{$this->eu_cat_option($v ['cat_id'], $num ,$curid )}";
			}
		}
		return $string;
	}
	
	/**
	 * 获取一条团代购商品分类信息
	 * @param unknown $cat_id
	 * @return mixed
	 */
	function eu_cat_id($cat_id){
		$db=new db();
		$sql="select * from xgj_eu_cat where cat_id=$cat_id";
		$result=$db->getRow($sql);
		return $result;
	}
	
	/**
	 * 删除一条团代购商品分类信息
	 * @param unknown $cat_id
	 * @return PDOStatement
	 */
	function eu_cat_del($cat_id){
		$db=new db();
		$sql="select * from xgj_eu_cat where parent_id=$cat_id";
		$result=$db->getAll($sql);
		if(!empty($result)){
			foreach($result as $v){
				$this->eu_cat_del($v['cat_id']);
			}
		}
		//删除自己
		$re=$db->query("delete from xgj_eu_cat where cat_id=$cat_id");
		return $re;
	}
    
}