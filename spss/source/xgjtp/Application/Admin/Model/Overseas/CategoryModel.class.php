<?php
namespace Admin\Model\Overseas;
use \Think\Model;

class CategoryModel extends Model{
	
	/* public function test(){
		echo '我是实例化的外部Model类';
	} */
	
	//获取分类数据
	public function getCatTree($arr,$id = 0,$lev=0) {
		$tree = array();
	
		foreach($arr as $v) {
			if($v['pid'] == $id) {
				$v['lev'] = $lev;
				$tree[] = $v;
	
				$tree = array_merge($tree,$this->getCatTree($arr,$v['id'],$lev+1));
			}
		}
	
		return $tree;
	}
	
	
}