<?php
/*******************************************************************************
    sid.php by Bill Weinman <http://bw.org/contact/>

    SID: SQL Interactive Demonstrator
    a program for demonstrating SQL

    Copyright (c) 2009-2010 The BearHeart Group LLC

MAJOR UPDATES ONLY:
    1.1 bw -- 2009-04-22 - Updated to work with all PHP warnings enabled. 
    1.2 bw -- 2010-07-08 - Updated to support SQLite3
    2.0 bw -- 2010-07-17 - Significant rewrite to support mixed multiple queries
    2.1 bw -- 2010-08-04 - Support for CREATE TRIGGER ... BEGIN ... END;
    2.2 bw -- 2010-08-17 - Added example custom functions, scalar and aggregate

*******************************************************************************/

define("VERSION", "2.2.1");
define("DBENGINE", "sqlite3");
define("DBDIR", "/path/to/sqlite3_data");

define('SQLCOMMENT', '--');

/* ***** uncomment for MySQL
define("MYSQLUSER", "web");
define("MYSQLPASS", "foo.bar");
********************************* */

// list of databases to show
$db_list = array (
    ':memory:',
    'world.db',
    'album.db',
    'test.db'
);

_init();
main();
page();

function main()
{
    if(isset($_REQUEST['a'])) jump($_REQUEST["a"]);
}

function _init( )
{
    global $SID;
    global $db_list;
    $default_db = $db_list[0];

    // initialize display vars
    foreach ( array( 'MESSAGES', 'ERRORS', 'CONTENT', 'SQLfield' ) as $v )
        $SID[$v] = '';

    // connect to the database (persistent)
    $database = (isset($_REQUEST['select_database'])) ? $_REQUEST['select_database'] : $default_db;
    if($database == '--NONE--') $database = $default_db;
    $SID['utf8'] = FALSE;
    try {
        switch(DBENGINE) {
            case 'sqlite3':
                // don't add the DBDIR to :memory: you'll create a file
                if($database == ':memory:') $dbh = new PDO('sqlite::memory:', 'unused', 'unused');
                else $dbh = new PDO('sqlite:' . implode('/', array(DBDIR, $database)), 'unused', 'unused');
                $dbh->sqliteCreateFunction('SEC_TO_TIME', 'sec_to_time', 1);        // custom functions ...
                $dbh->sqliteCreateFunction('TIME_TO_SEC', 'time_to_sec', 1);
                $dbh->sqliteCreateAggregate('SUM_SEC_TO_TIME',
                    'sum_sec_to_time_step', 'sum_sec_to_time_finalize', 1);
                $dbh->sqliteCreateFunction('REPLACE_REGEX', 'replace_regex', 3);
                $dbh->sqliteCreateAggregate('AVG_LENGTH',
                    'avg_length_step', 'avg_length_finalize', 1);
                $SID['utf8'] = TRUE;                                                // GLOBALS
                $SID['sqlite_version'] = SQLite3::version();
                $SID['sqlite_version'] = $SID['sqlite_version']['versionString'];
                break;
            case 'mysql':
                if($database == '--NONE--') $database = '';
                $dbh = new PDO('mysql:host=localhost;dbname=' . $database, MYSQLUSER, MYSQLPASS,
                    array( PDO::ATTR_PERSISTENT => true ));
                break;
            default:
                error('unsupported DBENGINE: ' . DBENGINE);
        }
    } catch (PDOException $e) {
        error("Error while constructing PDO object: " . $e->getMessage());
    }

    if($dbh) {
        // set exception mode for errors (why is this not the default?)
        // this is far more portable for different DB engines than trying to 
        // parse error codes
        $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $SID['dbh'] = $dbh;
    } else {
        exit();
    }

    $SID['TITLE'] = "SID";
    $SID['SELF'] = $_SERVER["SCRIPT_NAME"];
    $SID['DATABASE_SELECT_LIST'] = database_select_list($database);

    // fixup missing common characters from the PHP entity translation table
    // (this is only used for latin1 conversions)
    $SID['xlat'] = get_html_translation_table(HTML_ENTITIES, ENT_NOQUOTES);
    $SID['xlat'][chr(130)] = '&sbquo;';     // Single Low-9 Quotation Mark
    $SID['xlat'][chr(131)] = '&fnof;';      // Latin Small Letter F With Hook
    $SID['xlat'][chr(132)] = '&bdquo;';     // Double Low-9 Quotation Mark
    $SID['xlat'][chr(133)] = '&hellip;';    // Horizontal Ellipsis
    $SID['xlat'][chr(136)] = '&circ;';      // Modifier Letter Circumflex Accent
    $SID['xlat'][chr(138)] = '&Scaron;';    // Latin Capital Letter S With Caron
    $SID['xlat'][chr(139)] = '&lsaquo;';    // Single Left-Pointing Angle Quotation Mark
    $SID['xlat'][chr(140)] = '&OElig;';     // Latin Capital Ligature OE
    $SID['xlat'][chr(145)] = '&lsquo;';     // Left Single Quotation Mark
    $SID['xlat'][chr(146)] = '&rsquo;';     // Right Single Quotation Mark
    $SID['xlat'][chr(147)] = '&ldquo;';     // Left Double Quotation Mark
    $SID['xlat'][chr(148)] = '&rdquo;';     // Right Double Quotation Mark
    $SID['xlat'][chr(149)] = '&bull;';      // Bullet
    $SID['xlat'][chr(150)] = '&ndash;';     // En Dash
    $SID['xlat'][chr(151)] = '&mdash;';     // Em Dash
    $SID['xlat'][chr(152)] = '&tilde;';     // Small Tilde
    $SID['xlat'][chr(154)] = '&scaron;';    // Latin Small Letter S With Caron
    $SID['xlat'][chr(155)] = '&rsaquo;';    // Single Right-Pointing Angle Quotation Mark
    $SID['xlat'][chr(156)] = '&oelig;';     // Latin Small Ligature OE
    $SID['xlat'][chr(159)] = '&Yuml;';      // Latin Capital Letter Y With Diaeresis

    // loose "index.php" if nec (regexes are fugly in php. Feh.)
    $SID["SELF"] = preg_replace('/([\\/\\\])index\\.php$/i', '$1', $SID["SELF"]); 
}

function page( )
{
    global $SID;
    set_vars();

    require_once "assets/header.php";
    require_once "assets/main.php";
    require_once "assets/footer.php";
}

function jump( $action )
{
    switch($action) {
        case "go":
            do_sql($_REQUEST['SQLfield']);  // don't need stripslashes here?
            break;
    }
    return;
}

//

function do_sql( $query )
{
    global $SID;
    $dbh = $SID['dbh'];
    
    // do some cleanup and input checking
    $query = trim($query);     // trim leading and trailing spaces
    $query_list = split_queries($query);    // 2.1 - instead of explode
    $qcount = count($query_list);

    $SID['query_start_time'] = microtime(TRUE);

    $stmt_count = 0;
    $select_row_count = 0;
    $non_select_row_count = 0;
    $select_qcount = 0;
    $non_select_qcount = 0;

    foreach ( $query_list as $k => $query ) {
        $query = strip_sql_comments($query);
        $qlen = strlen($query);

        if($qlen < 1) continue;     // skip empties
        else $stmt_count++;

        // debug -- display the query
        // message('%d: [%s]', $stmt_count, $query);

        if(is_select($query)) {
            // select statement
            try {
                $sth = $dbh->prepare($query);
                if($sth) {
                    $sth->execute();
                    $rc = select_results($sth, $qcount, $k + 1);
                    if($rc) {
                        $select_row_count += $rc;
                    }
                }
            } catch (PDOException $e) {
                error_message('query #%d: %s', $k + 1, $e->getMessage());
            }
            $select_qcount++;
        } else {
            // non-select statement
            try {
                $sth = $dbh->prepare($query);
                if($sth) {
                    $sth->execute();
                    $non_select_row_count += $sth->rowCount();
                }
            } catch (PDOException $e) {
                error_message('query #%d: %s', $k + 1, $e->getMessage());
            }
            $non_select_qcount++;
        }
    }

    // report statistics on results
    $elapsed_time = microtime(TRUE) - $SID['query_start_time'];
    $messages = array();
    if($stmt_count > 1) {
        $qstr = 'queries';
        array_push($messages, sprintf('%s %s performed', number_format($stmt_count), $qstr));
    } else {
        $qstr = 'query';
    }
    if($non_select_row_count) array_push($messages, sprintf('%d non-select %s affected %s rows', $non_select_qcount, $qstr, number_format($non_select_row_count)));
    if($select_row_count) array_push($messages, sprintf('%d select %s returned %s rows', $select_qcount, $qstr, number_format($select_row_count)));
    array_push($messages, sprintf('elapsed time: %s milliseconds', number_format($elapsed_time * 1000, 2)));
    message(join('; ', $messages) . '.');
}

function select_results( &$sth, $qcount, $qnum = NULL )
{
    global $SID;
    // $a is an accumulator for the output string
    $a = "\n";
    $a .= "<table class=\"results\">\n";
    $sth->setFetchMode(PDO::FETCH_ASSOC);

    // get the first row separately so we know if there are any results to display
    $row = $sth->fetch();
    if( ! $row ) {
        return 0;
    }

    $col_names = array_keys($row);

    // table heading
    $a .= "<tr>\n";
    foreach( $col_names as $name ) {
        $a .= "<td class=\"column_head\">$name</td>\n";
    }
    $a .= "</tr>\n";

    // the first row
    $a .= result_row($row);
    $row_count = 1;

    // the rest of the rows
    foreach( $sth as $row ) {
        $a .= result_row($row);
        $row_count ++;
    }

    $a .= "</table>\n"; 

    if($qcount > 1) {
        content(sprintf('<p class="message">Query %d:</p> %s', $qnum, $a));
    } else {
        content($a);
    }
    return $row_count;
}

function result_row( &$row )
{
    global $SID;
    $a = "<tr>\n";
    foreach( $row as $v ) {
        // show NULL values in red
        if( !isset($v) ) $v = "<span class=\"red\">NULL</span>\n";
        else $v = make_entities($v);

        $a .= "<td class=\"cell_value\">" . $v . "</td>\n";
    }
    $a .= "</tr>\n";
    return $a;
}

function database_select_list( $database )
{
    global $SID;
    global $db_list;
    $a = '';

    if(isset($SID['dbh'])) $dbh = $SID['dbh'];
    else return;

    if(DBENGINE == 'mysql') {
        try {
            $sth = $dbh->query("SHOW DATABASES");
        } catch (PDOException $e) {
            error_message($e->getMessage());
            return;
        }

        $a = "<option value=\"--NONE--\">-- Select Database --</option>\n";
    
        while( $row = $sth->fetch() ) {
            $v = $row['Database'];
            foreach( $db_list as $s ) {
                if($v == $s) {
                    $selected = ($v == $database) ? ' selected' : '';
                    $a .= "<option$selected>$v</option>\n";
                }
            }
        }
    } elseif(DBENGINE == 'sqlite3') {
        foreach($db_list as $s) {
            $selected = ($s == $database) ? ' selected' : '';
            $a .= "<option$selected>$s</option>\n";
        }
    }
    return $a;
}

// custom functions for SQLite 3

// SEC_TO_TIME( seconds INTEGER )
function sec_to_time( $sec )
{
    if(is_null($sec)) return NULL;
    $sec = intval($sec);    // make sure it's an integer
    $s = $sec % 60;
    $m = $sec / 60;
    return sprintf('%d:%02d', $m, $s);
}

// TIME_TO_SEC( time TEXT )  -- 'mm:ss'
function time_to_sec( $time )
{
    if(is_null($time)) return NULL;
    $t = explode(':', $time, 2);
    $m = intval($t[0]);
    $s = intval($t[1]);
    return ( $m * 60 ) + $s;
}

// SUM_SEC_TO_TIME
function sum_sec_to_time_step ($context, $rownumber, $value)
{
    if(is_null($value)) return $context;
    if(is_null($context)) $context = 0;
    $context += intval($value);     // accumulate the sum of the values
    return $context;
}

function sum_sec_to_time_finalize ( $context, $rownumber )
{
    $sec = $context;
    $s = $sec % 60;
    $m = $sec / 60;
    $h = 0;
    if($m > 60) {
        $h = $m / 60;
        $m = $m % 60;
    }
    return sprintf('%d:%02d:%02d', $h, $m, $s);
}

// REPLACE_REGEX( string TEXT, pattern TEXT, replace TEXT )
function replace_regex( $string, $pattern, $replace )
{
    if($pattern[0] != '/') $pattern = '/' . $pattern . '/';
    return @preg_replace($pattern, $replace, $string);
}

// AVG_LENGTH
function avg_length_step ($context, $rownumber, $value)
{
    if(is_null($value)) return $context;
    if(is_null($context)) {
        $context = array();
        $context['count'] = 1;
        $context['sum'] = strlen($value);
    } else {
        $context['sum'] += strlen($value);
        $context['count'] ++;
    }
    return $context;
}

// AVG_LENGTH
function avg_length_finalize ( $context, $rownumber )
{
    return $context['sum'] / $context['count'];
}

// utility functions

function is_select($query)
{
    switch(DBENGINE) {
        case 'mysql':
            $select_list = array( 'SELECT', 'DESCRIBE', 'SHOW' );
            break;
        case 'sqlite3':
            $select_list = array( 'SELECT', 'EXPLAIN', 'PRAGMA' );
            break;
        default:
            $select_list = array( 'SELECT' );
            break;
    }
    foreach ($select_list as $s) {
        if(strncmp(strtoupper($query), $s, strlen($s)) == 0) return TRUE;
    }
}

// split queries into array
// handles CREATE TRIGGER correctly
function split_queries($q_string)
{
    $q_array = array();
    $q_parts = explode(';', $q_string);

    while($q_parts) {
        $qp = array_shift($q_parts);
        if( ($i = strpos($qp, 'TRIGGER')) > 0
            and ($j = strpos($qp, 'CREATE')) > 0
            and $j < $i
            and strpos($qp, 'BEGIN')
        ) {
            // we have a CREATE TRIGGER statement
            // keep its parts together until we see the END
            while($q_parts) {
                if(strpos($qp, 'END')) break;
                else $qp .= ";\n" . array_shift($q_parts);
            }
        }
        $qp = trim($qp);
        if(strlen($qp) > 0) $q_array[] = $qp;
    }

    return $q_array;
}

// strip comments from query
function strip_sql_comments( $q )
{
    $lines = explode("\n", $q);     # break it into lines
    foreach($lines as $i => $l) {
        if(($index = strpos($l, SQLCOMMENT)) !== FALSE) {   # has comment?
            if($index == 0) unset($lines[$i]);
            else $lines[$i] = substr($l, 0, $index);
        }
    }
    return implode("\n", $lines);
}

function make_entities( $s )
{
    global $SID;
    if($SID['utf8']) {
        $s = htmlentities($s, ENT_QUOTES, 'UTF-8');
    } else {
        $s = strtr( $s, $SID['xlat'] );
    }
    return $s;
}

function set_vars( )
{
    global $SID;
    if(isset($SID["_MSG_ARRAY"])) foreach ( $SID["_MSG_ARRAY"] as $m ) $SID["MESSAGES"] .= $m;
    if(isset($SID["_ERR_ARRAY"])) foreach ( $SID["_ERR_ARRAY"] as $m ) $SID["ERRORS"] .= $m;
    if(isset($SID["_CON_ARRAY"])) foreach ( $SID["_CON_ARRAY"] as $m ) $SID["CONTENT"] .= $m;
    if(isset($_REQUEST['SQLfield'])) $SID['SQLfield'] = htmlspecialchars($_REQUEST['SQLfield']);  // stripslashes?
}

function content( $s )
{
    global $SID;
    $SID["_CON_ARRAY"][] = "\n<div class=\"content\">$s</div>\n";
}

function message()
{
    global $SID;
    $args = func_get_args();
    if(count($args) < 1) return;
    $s = vsprintf(array_shift($args), $args);
    $SID["_MSG_ARRAY"][] = "<p class=\"message\">$s</p>\n";
}

function error_message()
{
    global $SID;
    $args = func_get_args();
    if(count($args) < 1) return;
    $s = vsprintf(array_shift($args), $args);
    $SID["_ERR_ARRAY"][] = "<p class=\"error_message\">$s</p>\n";
}

function error( $s )
{
    error_message($s);
    page();
}

?>
