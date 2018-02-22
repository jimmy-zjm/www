<?php
require_once(BASE_DIR ."/libs/logs.php");
class mysql_db
{
	var $dbname;
	var $database = null;
	var $database_r = null;
	var $result = null;
 	var $test = false;
 	//var $test = true;
	function mysql_db()
	{
		$this->dbname = null;
		$this->database = null;
		$this->database_r = null;
	}

	function connect($table_name)
	{
		global $_tableconf, $_dbconf, $_dbconf_r;
		// tablename is not existed.
		if (! isset($_tableconf[$table_name]))
			return false;

		// do nothing if dbname is same
		$dbname = $_tableconf[$table_name];
		if ($this->dbname == $dbname)
			return true;

		// select db if this tablename have same host and port with old table
		if (isset($this->dbname) && ((
			$_dbconf[$dbname]['0'] == $_dbconf[$this->dbname]['0'] && 
			$_dbconf[$dbname]['1'] == $_dbconf[$this->dbname]['1']) ||(
			$_dbconf_r[$dbname]['0'] == $_dbconf_r[$this->dbname]['0'] && 
			$_dbconf_r[$dbname]['1'] == $_dbconf_r[$this->dbname]['1']))
			)
			{
				$this->dbname = $dbname;
				mysql_select_db($_dbconf[$dbname][4], $this->database);
				mysql_select_db($_dbconf_r[$dbname][4], $this->database_r);
				return true;
			}

		if ($this->database != null) mysql_close($this->database);
		if ($this->database_r != null) mysql_close($this->database_r);

		$this->dbname = $dbname;
		$this->database = @mysql_connect($_dbconf[$dbname][0].":".$_dbconf[$dbname][1], 
			$_dbconf[$dbname][2], $_dbconf[$dbname][3],0);
		$this->database_r = @mysql_connect($_dbconf_r[$dbname][0].":".$_dbconf_r[$dbname][1], 
			$_dbconf_r[$dbname][2], $_dbconf_r[$dbname][3],1);
		mysql_select_db($_dbconf[$dbname][4], $this->database);
		mysql_select_db($_dbconf_r[$dbname][4], $this->database_r);
		mysql_query("set names 'utf8'",$this->database);
		mysql_query("set names 'utf8'",$this->database_r);
	}

	function disconnect()
	{
		if ($this->database != null){
		mysql_close($this->database);
		$this->database = null;
		$this->dbname = null;
		}
		if ($this->database_r != null){
		mysql_close($this->database_r);
		$this->database_r = null;
		$this->dbname = null;
		}
	}

	function insert($table, $data = array())
	{
		$fields = implode('`,`',array_keys($data));
		foreach ($data as $key => & $value) 
		{
			$value = mysql_escape_string($value);
		//	print $value."<br />";
		}
		$values = implode('\',\'', array_values($data));
		//print "value=".$values;
	//	echo 'insert into `'. $table. '` (`'. $fields 
	//		. '`) values (\''. $values .'\')';
		return $this->query('insert into `'. $table. '` (`'. $fields 
			. '`) values (\''. $values .'\')');
	}

	function insert_id()
	{
		return mysql_insert_id($this->database);
	}

	function delete($table, $primary_key, $primary_value)
	{
		if ($primary_value) {
			return $this->query('delete from `'. $table. '` where `'
				. $primary_key . '` =  \'' . mysql_escape_string($primary_value) . '\'');
		}
		return null;
	}

	function delete_more_primary($table, $primary)
	{
		$conditon = array();
		foreach($primary as $field => $value)
		{
			$conditon[] = '`' . $field . '` = \'' . mysql_escape_string($value) . '\'';
		}
		if ($primary) {
			return $this->query('delete from `'. $table. '` where ' . implode(' and ', $conditon));
		}
		return null;
	}	
	
	function update($table, $data = array(), $primary_key, $primary_value)
	{
		$sql = array();
		foreach ($data as $field => $value) 
			$sql[] = '`' . $field . '` = \''. mysql_escape_string($value). '\'';
		
		$sql_str = 'update `' . $table . '` set ' . implode(', ', $sql) 
			. ' where `' . $primary_key . '` = \''. mysql_escape_string($primary_value) . '\'';
//		echo $sql_str;
		return $this->query($sql_str);
	}
	
	function update_more_primary($table, $data = array(), $primary)
	{
		$sql = array();
		foreach ($data as $field => $value) 
			$sql[] = '`' . $field . '` = \''. mysql_escape_string($value). '\'';
		
		$conditon = array();
		foreach($primary as $field => $value)
			$conditon[] = '`' . $field . '` = \'' . mysql_escape_string($value) . '\'';
		
		$sql_str = 'update `' . $table . '` set ' . implode(', ', $sql) 
			. ' where ' . implode(' and ', $conditon);
		return $this->query($sql_str);
	}

	function getArray($sql, $params = array())
	{
		if(!isset($params['mode'])){
			$params['mode'] = MYSQL_ASSOC;
		}
		$res = array();
		$this->query($sql);
		while ($r = $this->fetch($params)) 
		{
			$res[] = $r;
		}
		if (count($res) == 0) 
			return null;
		$this->free();
		return $res;
	}
	
	function getRow($sql, $params = array())
	{
		if(!isset($params['mode'])){
			$params['mode'] = MYSQL_ASSOC;
		}
		$this->query($sql);
//		echo $sql;
		$res = $this->fetch($params);
		if ( !$res ) 
			return null;
		$this->free();
		return $res;
	}

	function getCell($sql, $params = array())
	{
		$this->query($sql);
		$res = $this->fetch(array('mode' => MYSQL_NUM));
		if (is_array($res))
		{
			$res = array_pop($res);
		}
		else 
			return null;
		$this->free();
		return $res;
	}

	//����Ĳ�������Դ�д��SELECT����ͷ����ʹ��ֻ����ݿ�l�ӡ�
	function query($sql)
	{
//		create_logs(L_INFO, "SQL", $sql);
		if($this->test){
			echo "<br />".$sql."<br />";
		}
		$dbcon = null;
		if(preg_match("/^SELECT/",$sql)){
			$dbcon = $this->database_r;
		}else{
			$dbcon = $this->database;
		}
		$this->result = @mysql_query($sql, $dbcon);
		if ( !$this->result ) {
			echo mysql_error();
			return null;
		}
		return mysql_affected_rows($dbcon);
	}

	function fetch($params = array())
	{
		if(!isset($params['mode'])){
			$params['mode'] = MYSQL_ASSOC;
		}
		return @mysql_fetch_array($this->result, $params['mode']);
	}

	function free()
	{
		mysql_free_result($this->result);
		return $this;
	}
	
	function err_no()
	{
		return mysql_errno($this->database);
	}
	
	function err_str()
	{
		return mysql_error($this->database);
	}
}
?>
