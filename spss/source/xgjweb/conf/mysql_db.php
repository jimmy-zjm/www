<?php

// $dbhost = DB_HOST; 
// $dbuser = DB_USER;
// $dbpass = DB_PASSWORD; 
// $connect = mysql_connect($dbhost,$dbuser,$dbpass) or die('Could not connect: ' . mysql_error());
// mysql_query("set names utf8");
// mysql_select_db(DB_NAME);

$servername = DB_HOST;
$username = DB_USER;
$password = DB_PASSWORD;
$dbname=DB_NAME;
// 创建连接
//$conn = new mysqli($servername, $username, $password);
// $con=mysqli_connect($servername, $username, $password,$dbname);
// mysqli_set_charset($con,"utf8");
// // 检测连接
// if (mysqli_connect_errno($con))
// {
// echo "Failed to connect to MySQL: " . mysqli_connect_error();
// }
/**
 * 面向对象
 */
// 创建连接
$conn = new mysqli($servername, $username, $password);
$conn->set_charset("utf8");
// $conn->("utf8");
// 检测连接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
//echo "Connected successfully";
?>