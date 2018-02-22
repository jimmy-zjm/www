<?php
/**
 * model类
 * @author  Grass
 * @date 2015-12-14
 * 2016-3-14 增加save()
 * 2016-3-15 增加count()
 * 2016-3-18 增加where() 中的neq
 * 2016-3-22 增加_empty()
 */

class Model extends Mysql{

    private $tabName;   //表名,有前缀
    private $pk;        //自动获取出来的主键
    private $fields;    //自动获取出来的字段列表
    private $field;     //指定要查询的字段
    private $limit;     //保存limit
    private $where;     //保存where条件
    private $data;      //保存数据
    private $order;     //保存排序条件
    protected $error;     //保存错误信息

    public function __construct($tabName=''){

        $config = array(
            'host'     => C('DB_HOST'),
            'user'     => C('DB_USER'),
            'password' => C('DB_PWD'),
            'dbname'   => C('DB_NAME'),
            'prefix'   => C('DB_PREFIX'),
            'charset'  => C('DB_CHARSET'),
        );
        parent::__construct($config);
        if($tabName){
            $this->tabName = $config['prefix'] . $tabName;
            $this->fields = $this->getFields();
        }
    }

    public function getError(){
        return $this->error;
    }

    public function data(array $data){
        $this->data = $data;
        return $this;
    }

    public function add(){
        $keys   = array_keys($this->data);
        $values = array_values($this->data);
        $k = $v = '';
        for ($i=0,$len=count($keys); $i < $len; ++$i) {
            if(in_array($keys[$i], $this->fields)){
                $k .= "`{$keys[$i]}`,";
                $v .= "'".addslashes($values[$i])."',";
            }
        }
        $k = rtrim($k,',');
        $v = rtrim($v,',');
        $this->sql = $sql = "INSERT INTO `{$this->tabName}` ({$k}) VALUES({$v})";
        if($this->exec($sql)){
            return $this->pdo->lastInsertId();
        }else{
            return false;
        }
    }

    /**
     * 更新
     */
    public function save(){
        $data = $this->data;
        $data = array_intersect_key($data,array_flip($this->fields));//过滤掉不存在的字段
        if(empty($data)) return false;
        $str  = '';
        foreach ($data as $key => $value) {
            if($key != $this->pk){
                $str.= "`$key`='".addslashes($value)."',";
            }
        }
        $str = rtrim($str,',');
        $where = empty($this->where)?'':"WHERE {$this->where}";
        $this->sql = $sql = "UPDATE {$this->tabName} SET $str {$where}";
        return $this->exec($sql);
    }

    public function table($tabName){
        $this->tabName = C('DB_PREFIX') . $tabName;
        $this->fields = $this->getFields();
        return $this;
    }

    public function field($field){
        $this->field = $field;
        return $this;
    }

    public function getField($field){
        $where = empty($this->where)?'':"WHERE {$this->where}";
        $this->sql = $sql = "SELECT `{$field}` FROM {$this->tabName} $where ";
        $this->_empty();
        return $this->fetchColumn($sql, $field);
    }

    public function setField($field, $value){
        $where = empty($this->where)?'':"WHERE {$this->where}";
        $this->sql = $sql = "UPDATE {$this->tabName} SET `{$field}` = '".addslashes($value)."' $where ";
        $this->_empty();
        return $this->exec($sql);
    }

    public function setInc($field, $value=1){
        $value = (int) $value;
        $where = empty($this->where)?'':"WHERE {$this->where}";
        $this->sql = $sql = "UPDATE {$this->tabName} SET `{$field}` = {$field}+{$value} $where ";
        $this->_empty();
        return $this->exec($sql);
    }

    public function setDec($field, $value=1){
        $value = (int) $value;
        $where = empty($this->where)?'':"WHERE {$this->where}";
        $this->sql = $sql = "UPDATE {$this->tabName} SET `{$field}` = {$field}-{$value} $where ";
        $this->_empty();
        return $this->exec($sql);
    }

    public function find($id=''){
        $id    = (int) $id;
        $field = empty($this->field)?'*':$this->field;
        $order = empty($this->order)?'':"ORDER BY $this->order";
        if(empty($id)){
            $where = empty($this->where)?'':"WHERE {$this->where}";
            $this->sql = $sql = "SELECT {$field} FROM `{$this->tabName}` $where $order LIMIT 1";
        }else{
            $where = empty($this->where)?'':"AND {$this->where}";
            $this->sql = $sql = "SELECT {$field} FROM `{$this->tabName}` WHERE {$this->pk}='{$id}' $where $order LIMIT 1";
        }
        $this->_empty();
        return $this->fetch($sql);
    }

    public function limit($limit){
        $this->limit = $limit;
        return $this;
    }

    public function delete($id=''){
        $id    = (int) $id;
        $where = empty($this->where)?'':"AND {$this->where}";
        if(!empty($id)){
            $sql = "DELETE FROM `{$this->tabName}` WHERE {$this->pk}='{$id}' {$where}";
        }else{
            $where = empty($this->where)?'':"WHERE {$this->where}";
            if(empty($where)){
                die('危险:sql error 没有条件');
            }
            $sql = "DELETE FROM `{$this->tabName}` {$where}";
        }
        $this->_empty();
        return $this->exec($sql);
    }

    public function select(){
        $field = empty($this->field)?'*':$this->field;
        $order = empty($this->order)?'':"ORDER BY $this->order";
        $where = empty($this->where)?'':"WHERE {$this->where}";
        $limit = empty($this->limit)?'':"LIMIT {$this->limit}";
        $this->sql = $sql = "SELECT {$field} FROM `{$this->tabName}` $where $order $limit";
        $this->_empty();
        // die($sql);
        return $this->fetchAll($sql);
    }

    public function count(){
        $where = empty($this->where)?'':"WHERE {$this->where}";
        $limit = empty($this->limit)?'':"LIMIT {$this->limit}";
        $this->sql = $sql = "SELECT COUNT(*) AS total FROM `{$this->tabName}` $where $limit ";
        $this->_empty();
        return $this->fetchColumn($sql,'total');
    }

    public function order($order=''){
        $this->order = $order;
        return $this;
    }

    public function _sql(){
        return $this->sql;
    }

    private function _empty(){
        $this->limit = $this->order = $this->where = $this->field = $this->validate = '';
    }

    public function create($data=array(), $type=0){
        if(empty($data)){
            $data = $_POST;
        }
        $validate = $this->validate;
        foreach ($data as $key => $value) {
            foreach ($validate as $k => $v) {
                if($key == $v[0]){
                    if($v[1]=='require'){
                        if(trim($value) === ''){
                            $this->error = $v[2];
                            return false;
                        }
                    }elseif($v[1]=='phone'){
                        if(!preg_match('/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\d{8}$/', $value)){
                            $this->error = $v[2];
                            return false;
                        }
                    }elseif($v[1]=='email'){
                        if(!preg_match('/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/', $value)){
                            $this->error = $v[2];
                            return false;
                        }
                    }elseif($v[1]=='url'){
                        if(!preg_match('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/', $value)){
                            $this->error = $v[2];
                            return false;
                        }
                    }else{
                        if(!preg_match($v[1], $value)){
                            $this->error = $v[2];
                            return false;
                        }
                    }
                }
            }
        }
        return $data;
    }

    public function validate($validate){
        $this->validate = $validate;
        return $this;
    }

    public function where($map){
        if(!is_array($map)){
            $this->where = $map;
            return $this;
        }

        $map_str = '';
        foreach ($map as $key => $value) {
            if(is_array($value)){
                if(!is_array($value[1])){
                    $v = addslashes($value[1]);
                }
                switch($value[0]){
                    case 'eq':
                        $map_str .= "`{$key}`='{$v}' AND ";
                        break;
                    case 'neq':
                        $map_str .= "`{$key}`!='{$v}' AND ";
                        break;
                    case 'lt':
                        $map_str .= "`{$key}`<'{$v}' AND ";
                        break;
                    case 'gt':
                        $map_str .= "`{$key}`>'{$v}' AND ";
                        break;
                    case 'elt':
                        $map_str .= "`{$key}`<='{$v}' AND ";
                        break;
                    case 'egt':
                        $map_str .= "`{$key}`>='{$v}' AND ";
                        break;
                    case 'in':
                        $map_str .= "`{$key}` IN (".join(',',$value[1]).") AND ";
                        break;
                }
            }else{
                $map_str .= "`{$key}`='".addslashes($value)."' AND ";
            }
        }
        $this->where = substr($map_str,0,-4);
        return $this;
    }


    private function getFields(){
        $sql = "DESCRIBE {$this->tabName}";
        $fields = $this->fetchAll($sql);
        $arr = array();
        foreach ($fields as $field) {
            $arr[] = $field['Field'];
            if($field['Key'] === 'PRI'){
                $this->pk = $field['Field'];
            }
        }
        return $arr;
    }
}