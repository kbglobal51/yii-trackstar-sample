<?php
/*******************************************************************************
    start.php by Bill Weinman <http://bw.org/contact/>
    based upon BW's test sandbox for PHP
    Copyright (c) 2009-2010 The BearHeart Group LLC
*******************************************************************************/

define('TITLE', 'SQLite 3 Quick Start');
define('VERSION', '1.0.2');
define('HEADER', '../assets/header.php');
define('BODY', '../assets/body.php');
define('FOOTER', '../assets/footer.php');
define('DB_FILENAME', ':memory:');
define('TABLE_NAME', 'test');

require_once('../lib/bwSQLite3.php');

_init();
main();
page();

function main()
{
    global $G;
    $db = $G['db'];
    $tn = TABLE_NAME;

    message('%s version %s', TITLE, VERSION);
    message('bwSQLite3 version %s', bwSQLite3::VERSION);
    message('file name: %s, table name: %s', DB_FILENAME, TABLE_NAME);

    $query_start_time = microtime(TRUE);
    message('There are %d rows in the table.', $db->count_recs());

    // database operations go here

    $elapsed_time = microtime(TRUE) - $query_start_time;
    message('elapsed time: %s ms', number_format($elapsed_time * 1000, 2));
}

function _init( )
{
    global $G;
    $G['TITLE'] = TITLE;
    $G['ME'] = basename($_SERVER['SCRIPT_FILENAME']);

    // initialize display vars
    foreach ( array( 'MESSAGES', 'ERRORS', 'CONTENT' ) as $v )
        $G[$v] = "";

    // create the table
    try {
        $db = new bwSQLite3(DB_FILENAME, TABLE_NAME);
        $tn = TABLE_NAME;
        $db->sql_do("drop table if exists $tn");
        $db->sql_do("create table $tn ( id integer primary key, animal text, sound text )");
        // insert some rows
        $db->sql_do("insert into $tn (animal, sound) values (?, ?)", 'cat', 'Purr');
        $db->sql_do("insert into $tn (animal, sound) values (?, ?)", 'dog', 'Woof');
        $db->sql_do("insert into $tn (animal, sound) values (?, ?)", 'duck', 'Quack');
        $db->sql_do("insert into $tn (animal, sound) values (?, ?)", 'bear', 'Grrr');
    } catch (PDOException $e) {
        error($e->getMessage());
    }
    $G['db'] = $db;
}

function page( )
{
    global $G;
    set_vars();

    require_once(HEADER);
    require_once(BODY);
    require_once(FOOTER);
    exit();
}

//

function set_vars( )
{
    global $G;
    if(isset($G["_MSG_ARRAY"])) foreach ( $G["_MSG_ARRAY"] as $m ) $G["MESSAGES"] .= $m;
    if(isset($G["_ERR_ARRAY"])) foreach ( $G["_ERR_ARRAY"] as $m ) $G["ERRORS"] .= $m;
    if(isset($G["_CON_ARRAY"])) foreach ( $G["_CON_ARRAY"] as $m ) $G["CONTENT"] .= $m;
}

function content( $s )
{
    global $G;
    $G["_CON_ARRAY"][] = "\n<div class=\"content\">$s</div>\n";
}

function message()
{
    global $G;
    $args = func_get_args();
    if(count($args) < 1) return;
    $s = vsprintf(array_shift($args), $args);
    $G["_MSG_ARRAY"][] = "<p class=\"message\">$s</p>\n";
}

function error_message()
{
    global $G;
    $args = func_get_args();
    if(count($args) < 1) return;
    $s = vsprintf(array_shift($args), $args);
    $G["_ERR_ARRAY"][] = "<p class=\"error_message\">$s</p>\n";
}

function error( $s )
{
    error_message($s);
    page();
}

?>
