<?php 
//连接本地的 Redis 服务
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
//设置 redis 字符串数据
$redis->set("aaa", "bbb");
// 获取存储的数据并输出
echo $redis->get("aaa");