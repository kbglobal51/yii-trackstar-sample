<?php
error_reporting(7);
define('IN_ET', TRUE);

$version='EasyTalk V5.0.1';
$addtime=time();
$action=$_POST['action'];
$act=$_GET['act'];
$step=$_GET['step']?$_GET['step']:1;
$step=$step>4?1:$step;

$sqlfile=dirname(__FILE__)."/easytalk.sql";
$lockfile = '../include/lockstall';

if (file_exists($lockfile)) {
    header("Location: ../index.php");
    exit;
}

include("../include/db_mysql.class.php");
include("../include/template.class.php");
include('install.func.php');

$options = array('template_dir' => './','cache_dir' => './','auto_update' => true,'cache_lifetime' => 0);
$template = Template::getInstance();
$template->setOptions($options);

$s0=file_writeable("../config.inc.php");
$s1=dir_writeable("../include/cache");
$s2=dir_writeable("../attachments/head");
$s3=dir_writeable("../attachments/usertemplates");
$s4=dir_writeable("../attachments/photo");
$s5=dir_writeable("../templates/cache");
$s6=dir_writeable("../admin/backup");
$s7=dir_writeable("../admin/templates/cache");
$s8=dir_writeable("../admin/themetemp");

if (($s0!=1 || $s1!=1 || $s2!=1 || $s3!=1 || $s4!=1 || $s5!=1 || $s6!=1 || $s7!=1 || $s8!=1) && $step!=1) {
    header("location: install.php?step=1");
    exit;
}

if ($action=="checkinstall") {
    $server=$_POST['server'];
    $datauname=$_POST['datauname'];
    $dataupass=$_POST['dataupass'];
    $database=$_POST['database'];
    $webadd=$_POST['webadd'];

    $link = @mysql_connect($server, $datauname, $dataupass, 1);
	$connnect = $link && @mysql_select_db($database, $link) ? 'yes' : 'no';
    if ($connnect=="yes") {
        $fp=fopen("../config.inc.php","w");
        fputs($fp,"<?php\n");
        fputs($fp,"if(!defined('IN_ET')) {\n");
        fputs($fp,"   exit('Access Denied');\n");
        fputs($fp,"}\n\n");
        fputs($fp,"\$server = '".$server."';        // 数据库服务器，一般为“localhost”\n");
        fputs($fp,"\$db_username = '".$datauname."';        // MySQL 用户名\n");
        fputs($fp,"\$db_password = '".$dataupass."';        // MySQL 用户密码\n");
        fputs($fp,"\$db_name = '".$database."';     // 数据库名称\n");
        fputs($fp,"\$pconnect = 0;      // 数据库持久连接 0=关闭, 1=打开\n");
        fputs($fp,"\$webaddr = '".$webadd."';       // 安装地址,末尾不加“/”\n\n");
        fputs($fp,"\$index_num = 20;        //主页每次显示的条数\n");
        fputs($fp,"\$home_num = 20;     //我的博客每次显示的条数\n");
        fputs($fp,"\$autoUpdate = 1;        //是否开始自动更新\n\n");
        fputs($fp,"\$cookiedomain = '';     // cookie 作用域\n");
        fputs($fp,"\$cookiepath = '/';      // cookie 作用路径\n\n");
        fputs($fp,"\$version = '".$version."';      // 程序版本\n");
        fputs($fp,"?>");
        fclose($fp);
        header("location: install.php?step=3");
        exit;
    } else {
        echo "<script>alert('数据库检测未通过，请重新填写数据库信息！');history.go(-1);</script>";
        exit;
    }
}

if ($action=="install") {
    $username=daddslashes(trim($_POST['username']));
    $nickname=daddslashes(trim($_POST['nickname']));
    $mailadres=daddslashes(trim($_POST['mailadres']));
    $password1=md5(md5($_POST['password1']));
    $password2=md5(md5($_POST['password2']));
    $deniedname=array("admin","attachments","badge","images","include"."install","source","templates");
    if(in_array($username, $deniedname)) {
        echo "<script>alert('该管理员名不能使用！');history.go(-1);</script>";
        exit;
    }
    if(StrLenW($username)>20 || StrLenW($username)<4) {
        echo "<script>alert('管理员名长度应该大于4小于20个字符！');history.go(-1);</script>";
        exit;
    }
    if(StrLenW($nickname)>20 || StrLenW($nickname)<4) {
        echo "<script>alert('管理员昵称长度应该大于4小于20个字符！');history.go(-1);</script>";
        exit;
    }
    $t=explode("@",$mailadres);
    if(!$t[1]) {
        echo "<script>alert('电子邮件格式不正确！');history.go(-1);</script>";
        exit;
    }
    if ($password1!=$password2) {
        echo "<script>alert('两次输入的密码不正确！');history.go(-1);</script>";
        exit;
    }
    if ($password1==$password2 && $_POST['password1']) {
        $web_name3="EasyTalk 安装";
        include($template->getfile('install.htm'));
        include('../config.inc.php');
        $sql = file_get_contents($sqlfile);
        $db = new dbstuff;
        $db->connect($server,$db_username,$db_password,$db_name, $pconnect,true);
        @mysql_query("set names utf8");
        runquery($sql);
        $db->query("INSERT INTO et_users (user_name,nickname,password,mailadres,signupdate,isadmin) VALUES ('$username','$nickname','$password2','$mailadres','$addtime','1')");
        @touch($lockfile);
        exit;
    } else {
        echo "<script>alert('密码输入不正确，请重新输入！');history.go(-1);</script>";
        exit;
    }
}

//模板和Foot
$web_name3="EasyTalk 安装";
include($template->getfile('install.htm'));
?>