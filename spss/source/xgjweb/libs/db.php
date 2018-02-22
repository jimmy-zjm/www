<?php 
/**
 * This file contains the CDb db class.
 *
 */
class db{
	var $db;
	
	
	function __construct(){
		$this->db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASSWORD);
		$this->db->exec("set names utf8");
	}
	
	
	function query($sql,$ptn='query'){
		
		if($ptn=='query') $ret=$this->db->query($sql);
		else $ret=$this->db->exec($sql);
		$err=$this->db->errorInfo();
		if($err[0]!='00000'){
			var_dump($err,$sql);	
		}
		return $ret;
	}
	
	function insert_id(){
		return $this->db->lastInsertId();
	}
	
	
	function getRow($sql,$ptn='assoc'){
		$res=$this->query($sql);
		if(!$res || !$res->columnCount()) return array();
		if($ptn=='assoc')
			$ret= $res->fetch(PDO::FETCH_ASSOC);
		else 
			$ret= $res->fetch(PDO::FETCH_BOTH);
		return $ret;	
	}
	
	
	function getAll($sql,$ptn='assoc'){
		$res=$this->query($sql);
		if(!$res || !$res->columnCount()) return array();
		if($ptn=='assoc'){
			$ret= $res->fetchAll(PDO::FETCH_ASSOC);
		}else{
			$ret= $res->fetchAll(PDO::FETCH_BOTH);
		}
		return $ret;
	}
	
	function getCol($sql){
		$res=$this->getAll($sql,'both');
		$ret=array();
		foreach($res as $v){
			$ret[]=$v[0];
		}
		return $ret;
	}
	
	
	function getOne($sql,$x=0){
		$res=$this->query($sql);
		if(!$res || !$res->columnCount()) return '';
		$ret= $res->fetch(PDO::FETCH_NUM);
		return $ret[$x];
	}
	
	function update($table,$data,$where){
		if(!is_array($data)) return false;
		foreach($data as $k=>$v){
			$fields[]=$k;
			$values[]=$v;
		}
		
		foreach($fields as $k=>$field){
			if(is_array($values[$k])){
				//field=field+1                   +-             changed value
				$sql_part[]="{$field}={$field}{$values[$k][0]}'{$values[$k][1]}'";
			}else{
				//field=1
				$sql_part[]="{$field}='{$values[$k]}'";
			}
		}
		$sql_part=implode(',',$sql_part);
		
		$sql="update $table set $sql_part where $where";
// 		echo $sql;exit;
		return $this->query($sql,'exec');
	}
	
	function add($table,$data,$compatible = false)
	{
		
		$insert_info = $this->getInsertInfo($data);
		$mode = $compatible ? 'REPLACE' : 'INSERT';
		$sql="{$mode} INTO {$table}{$insert_info['fields']} VALUES{$insert_info['values']}";
		//echo $sql;exit;
		$this->query($sql);
		$insert_id = $this->insert_id();
		if ($insert_id)
		{
			if ($insert_info['length'] > 1)
			{
				for ($i = $insert_id; $i < $insert_id + $insert_info['length']; $i++)
				{
					$id[] = $i;
				}
			}
			else
			{
				
				$id = $insert_id;
			}
		}

		return $id;
	}
 
	protected function getInsertInfo($data)
	{
		reset($data);
		$fields = array();
		$values = array();
		$length = 1;
		if (key($data) === 0 && is_array($data[0]))
		{
			$length = count($data);
			foreach ($data as $_k => $_v)
			{
				foreach ($_v as $_f => $_fv)
				{
					$is_array = is_array($_fv);
					($_k == 0 && !$is_array) && $fields[] = $_f;
					!$is_array && $values[$_k][] = "'{$_fv}'";
				}
				$values[$_k] = '(' . implode(',', $values[$_k]) . ')';
			}
		}
		else
		{
			foreach ($data as $_k => $_v)
			{
				$is_array = is_array($_v);
				!$is_array && $fields[] = $_k;
				!$is_array && $values[] = "'{$_v}'";
			}
			$values = '(' . implode(',', $values) . ')';
		}
		$fields = '(' . implode(',', $fields) . ')';
		is_array($values) && $values = implode(',', $values);

		return compact('fields', 'values', 'length');
	}	
}
?>