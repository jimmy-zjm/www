<?php
require_once(BASE_DIR ."/conf/databases.inc.php");
require_once(BASE_DIR ."/libs/logs.php");
/*
描述：
	这是一个通过memcached作为底层的缓存机制的封装类。
	主要提供三个接口：set设置缓存内容，get获取缓存内容，remove删除缓存内容。

	设计接口中，增加了tag的标签功能，根据不同的tag使用不同的缓存端口。以确保缓存机制是可以采取分布式策略的。
	与tag对应的配置变量，参看conf/databases.inc.php文件中的 $_cacheconf：
		$_cacheconf['tag:1'] = array('192.168.30.36', '3000', 3600);
	数组的三个值分别表示memcached运行的IP地址，端口号，以及其中CACHE的自动失效时间（以秒为单位）。
	前期一种类型的数据，只需要存储在一个memcached中即可，而在后期，由于数据量的膨胀，所以会采用数据分布式的策略，
	将相同类型的tag分散到多个服务器中，所以在配置表中，以:1，:2的形式呈现。

函数说明：
	function set($tag, $key, $val)
	设置一个单一主键的值。

	function get($tag, $key)
	如果$key是一个数组，则返回一个数组对应数组中每个主键的值。
	如果$key是一个字符串，则返回一个值，对应该主键的值。

	function remove($tag, $key)
	删除$key一个主键的值。
*/

class cache
{
	private $handler;
	
	function cache()
	{
		$this->handler = array();
	}

	function connect($tag)
	{
		global $_cacheconf;
		if (!isset($_cacheconf[$tag])) return false;
		if (isset($this->handler[$tag])) return true;
		$this->handler[$tag] = memcache_pconnect($_cacheconf[$tag][0], $_cacheconf[$tag][1]);
		return true;
	}

	function disconnect()
	{
		foreach ($this->handler as $key => & $value)
		{
			memcache_close($value);
			unset($this->handler[$key]);
		}
	}

	function set($tag, $key, $value)
	{
		global $_cacheconf;
		if (!$this->connect($tag)) return false; 
		return memcache_set($this->handler[$tag], $tag.":".$key, $value, 0, $_cacheconf[$tag][2]);
	}

	function get($tag, $key)
	{
		if (!$this->connect($tag)) return false; 

		if (is_array($key))
		{
			foreach ($key as $one_key => & $one_value)
				$key[$one_key] = $tag.":".$one_value;
			
//			$log = implode(",", $key);
//			create_logs(L_INFO, "CACHE_ARR", $log);
		}
		else
		{
			$key = $tag.":".$key;
//			create_logs(L_INFO, "CACHE", $tag." ".$key);
		}

		return memcache_get($this->handler[$tag], $key);
	}

	function remove($tag, $key)
	{
		if (!$this->connect($tag)) return false;
		return memcache_delete($this->handler[$tag], $tag.":".$key);
	}
}
?>