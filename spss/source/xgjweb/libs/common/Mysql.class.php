<?php
/**
 * PDO 的mysql数据库操作类
 * @author  Grass
 * @date 2015-12-14
 */

class Mysql{
    protected $pdo;
    const FETCH_ASSOC = PDO::FETCH_ASSOC;
    const FETCH_BOTH  = PDO::FETCH_BOTH;
    const FETCH_NUM   = PDO::FETCH_NUM;

    public function __construct(array $config=array()){
        if(!empty($config)){
            $this->connect($config);
        }
    }

    public function __destruct(){
        $this->pdo = null;
    }

    /**
     * 连接数据库
     * @access public
     * @param  array $config 数据库配置信息
     */
    public function connect(array $config){
        $pdo = new PDO("mysql:dbname={$config['dbname']};host={$config['host']}", $config['user'], $config['password']);
        $charset = empty($config['charset'])?'UTF8':$config['charset'];
        $pdo->exec('SET NAMES '.$charset);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo = $pdo;
    }

    /**
     * 执行SELELCT SHOW DESC 等语句 获取一行数据
     * @param  string   $sql       sql
     * @param  integer  $fetchMode 返回的数据的风格
     * @return array               返回一维数组
     */
    public function fetch($sql, $fetchMode = PDO::FETCH_ASSOC){
        // die($sql);
        return $this->pdo->query($sql)->fetch($fetchMode);
    }

    /**
     * 执行SELELCT SHOW DESC 等语句 获取所有数据,
     * @param  string   $sql        sql
     * @param  integer  $fetchMode  返回的数据风格
     * @return array                返回二维数组
     */
    public function fetchAll($sql, $fetchMode = PDO::FETCH_ASSOC){
        // echo $sql,'<br/>';
        return $this->pdo->query($sql)->fetchAll($fetchMode);
    }

    /**
     * 执行SELELCT SHOW DESC 等语句 获取一行中的指定列数据
     * @param  [type] $sql       [description]
     * @param  [type] $key       [description]
     * @param  [type] $fetchMode [description]
     * @return [type]            [description]
     */
    public function fetchColumn($sql, $key, $fetchMode = PDO::FETCH_ASSOC){
        // die($sql);
        $row = $this->pdo->query($sql)->fetch($fetchMode);
        return $row[$key];
    }

    /**
     * 执行 UPDATE DELETE INSERT 等,没有结果集的sql
     * @param  string $sql sql
     * @return mixed       返回影响的记录行数
     */
    public function exec($sql){
        return $this->pdo->exec($sql);
    }

}