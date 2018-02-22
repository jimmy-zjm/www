<?php
/**
 *	MySQL增删改查
 *  2016-9-27
 *  find 查询一条 select 查询多条
 **/

function M($table){
	$new = new ModelController();
   	return $new->table($table);
}

class ModelController{
	/*************************/
	//连接sql属性
	private $host     = '192.168.100.238';
	private $dbname   = 'nyy';  
	private $username = 'xgj'; 
	private $password = 'xgj'; 
	private $db 	  = null;
	/*************************/

	private $tableName = null;
	private $where 	   = null;
	private $limit 	   = null;
	private $order 	   = null;
	private $like 	   = null;
	private $field 	   = null;
	private $join 	   = null;


	function __construct(){
		try {
			header("Content-Type:text/html;Charset=utf-8");
		    $this->db = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
		    $this->db->query("set character set 'utf8'");
		}catch(PDOException $e){
		    echo $e->getMessage();exit;
		}
	}

	public function table($tableName){
		$this->tableName = $tableName;
		return $this;
	}

	public function where($where='1=1'){
		if (!empty($where)) $this->where = ' where '.$where;
		return $this;
	}

	public function limit($start=null,$end=null){
		if (!empty($start) && empty($end)) $this->limit = ' limit '.$start;
		else if (empty($start) && !empty($end)) $this->limit = ' limit '.$end;
		else if (!empty($start) && !empty($end)) $this->limit = ' limit '.$start.','.$end;
		return $this;
	}

	public function order($key,$order='asc'){
		$this->order = ' order by '.$key.' '.$order;
		return $this;
	}

	public function like($key,$like){
		$this->like = $key.' like '."'%$like%'";
		return $this;
	}

	public function field($field){
		$this->field = $field;
		return $this;
	}

	public function join($join){
		$this->join = $join;
		return $this;
	}

	public function select(){
		$list = null;
		$tableName = $this->tableName;
		$where 	   = $this->where;
		$limit 	   = $this->limit;
		$order 	   = $this->order;
		$like 	   = $this->like;
		$field 	   = $this->field;
		$join 	   = $this->join;
		if (empty($field)) $field = '*';
		if (!empty($join)) $join = ' join '.$join;
		

		if (!empty($where) && !empty($like)) $like=' and '.$like;
		else if(empty($where) && !empty($like)) $like=' where '.$like;

		$sql = "SELECT $field FROM $tableName $join $where $like $order $limit ";

		$return = $this->db->query($sql);
		if ($return) {
			while($row = $return->fetch(PDO::FETCH_ASSOC)) {
	            $list[] = $row;
	        }
		}
		
		$this->db = null;
		return $list;
	}


	public function find(){
		$row = null;
		$tableName = $this->tableName;
		$where 	   = $this->where;
		$order 	   = $this->order;
		$like 	   = $this->like;
		$field 	   = $this->field;
		$join 	   = $this->join;
		if (empty($field)) $field = '*';
		if (!empty($join)) $join = ' join '.$join;

		if (!empty($where) && !empty($like)) $like=' and '.$like;
		else if(empty($where) && !empty($like)) $like=' where '.$like;

		$sql = "SELECT * FROM $tableName $join $where $like $order limit 1";

		$return = $this->db->query($sql);
		if ($return) $row = $return->fetch(PDO::FETCH_ASSOC);
		$this->db = null;
		return $row;
	}


	public function query($sql){
		$return = $this->db->query($sql);
		if ($return) {
			while($row = $return->fetch(PDO::FETCH_ASSOC)) {
	            $list[] = $row;
	        }
		}
		return $list;
	}


	public function getField($field){
		$this->field = $field;
		$data = $this->find();
		return $data[$field];
	}


	public function add($data){
		$tableName = $this->tableName;
		foreach ($data as $k => $v) {
			$key = empty($key)?$k:$key.','.$k;
			$val = empty($val)?$v:$val.','.$v;
		}
		$sql = "INSERT INTO $tableName($key) VALUES($val)";
		$return = $this->db->exec($sql);
		if ($return) return $this->db->lastInsertId();
		else return $return;
	}


	public function create($data){
		$tableName = $this->tableName;
		$return = $this->db->query("SHOW COLUMNS FROM ".$tableName."");
		if ($return) {
			while($row = $return->fetch(PDO::FETCH_ASSOC)) {
	            $Field[] = $row['Field'];
	        }
	        foreach ($data as $k => $v) {
	        	if (in_array($k, $Field)) {
	        		$list[$k]=$v;
	        	}
	        }
		}
		return $list;
	}


	public function save($data){
		$tableName = $this->tableName;
		$where 	   = $this->where;
		foreach ($data as $k => $v) {
			$key = empty($key)?$k.'='."'$v'":$key.','.$k.'='."'$v'";
		}
		$sql = "UPDATE $tableName SET $key $where";
		$return = $this->db->exec($sql);
		return $return;
	}


	public function delete(){
		$tableName = $this->tableName;
		$where 	   = $this->where;
		if ($where) {
			$sql = "DELETE FROM $tableName $where";
			$return = $this->db->exec($sql);
			return $return;
		}else return false;
	}


}

$aaa = array(
	'sn'=>'22',
	'name'=>'222',
	'model'=>'22',
	);

$a =  M('sheet s')->join('xgj_furnish_goods')->getField('goods_name');


echo '<pre>';
var_dump($a);