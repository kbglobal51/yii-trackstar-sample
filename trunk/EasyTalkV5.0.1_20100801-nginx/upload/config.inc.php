<?php
if(!defined('IN_ET')) {
   exit('Access Denied');
}

$server = 'localhost';        // 数据库服务器，一般为“localhost”
$db_username = 'root';        // MySQL 用户名
$db_password = '123456';        // MySQL 用户密码
$db_name = 'easytalk';     // 数据库名称
$pconnect = 0;      // 数据库持久连接 0=关闭, 1=打开
$webaddr = 'http://';       // 安装地址,末尾不加“/”

$index_num = 20;        //主页每次显示的条数
$home_num = 20;     //我的博客每次显示的条数
$autoUpdate = 0;        //是否开始自动更新

$cookiedomain = '';     // cookie 作用域
$cookiepath = '/';      // cookie 作用路径

$version = 'EasyTalk V5.0.1';      // 程序版本
?>