<?php
function dir_writeable($dir) {
    if($fp = @fopen("$dir/test.test", 'w')) {
        @fclose($fp);
        @unlink("$dir/test.test");
        return 1;
    } else {
        return 0;
    }
}

function file_writeable($file) {
    if (is_writable($file)) {
        return 1;
    } else {
        return 0;
    }
}

function runquery($sql, $showmessage = TRUE) {
	global $db;
    $dbcharset="utf8";

	$sql = str_replace("\r", "\n",$sql);
	$ret = array();
	$num = 0;
	foreach(explode(";\n", trim($sql)) as $query) {
		$queries = explode("\n", trim($query));
		foreach($queries as $query) {
			@$ret[$num] .= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
		}
		$num++;
	}
	unset($sql);

	foreach($ret as $query) {
		$query = trim($query);
		if($query) {

			if(substr($query, 0, 12) == 'CREATE TABLE') {
				$name = preg_replace("/CREATE TABLE IF NOT EXISTS ([a-z0-9_]+) .*/is", "\\1", $query);
				$showmessage && showjsmessage('建立数据表 '.$name.' ... 成功！');
				$db->query(createtable($query, $dbcharset));

			} else {
				$db->query($query);
			}

		}
	}
}

function showjsmessage($message) {
	echo '<script type="text/javascript">showmessage(\''.addslashes($message).' \');</script>'."\r\n";
	flush();
	ob_flush();
}

function createtable($sql, $dbcharset) {
	$type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
	$type = in_array($type, array('MYISAM', 'HEAP')) ? $type : 'MYISAM';
	return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).
	(mysql_get_server_info() > '4.1' ? " ENGINE=$type DEFAULT CHARSET=$dbcharset" : " TYPE=$type");
}

function daddslashes($string) {
    $string=str_replace("'",'"',$string);
    !defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
    if(!MAGIC_QUOTES_GPC) {
        if(is_array($string)) {
            foreach($string as $key => $val) {
                $string[$key] = daddslashes($val);
            }
        } else {
            $string = addslashes($string);
        }
    }
	return $string;
}

function StrLenW($str){
    $len=strlen($str);
    $i=0;
    while($i<$len){
        if(preg_match("/^[".chr(0xa1)."-".chr(0xff)."]+$/",$str[$i])){
            $i+=2;
        }else{
            $i+=1;
        }
    }
    return $i;
}
?>