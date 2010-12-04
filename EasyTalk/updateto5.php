<?PHP
header('Content-Type: text/html; charset=utf-8');
define('ET_ROOT', dirname(__FILE__));
define('IN_ET', TRUE);
error_reporting(7);

include(ET_ROOT."/include/db_mysql.class.php");
include(ET_ROOT.'/config.inc.php');

$db = new dbstuff;
$db->connect($server,$db_username,$db_password,$db_name, $pconnect,true);
@mysql_query("set names utf8");

$query1=$db->query("ALTER TABLE `et_settings` CHANGE `replace_word` `replace_word` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
$query2=$db->query("ALTER TABLE `et_content` CHANGE `content_body` `content_body` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");

$query3 = $db->query("Describe `et_content` `replyshow`");
$fetch1 = $db->fetch_array($query3);
if (!$fetch1['Field']) {
    $query4=$db->query("ALTER TABLE `et_content` ADD COLUMN `replyshow` tinyint(1) NOT NULL DEFAULT '1';");
}
echo $query1*$query2*$query3?"升级完成，请删除此文件":"升级出现问题，请重新运行";
?>