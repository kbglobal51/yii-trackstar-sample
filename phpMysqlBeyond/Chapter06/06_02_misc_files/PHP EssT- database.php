<?php
require_once("config.php");

// 1. Create a database connection
$connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS);
if (!$connection) {
	die("Database connection failed: " . mysql_error());
}

// 2. Select a database to use 
$db_select = mysql_select_db(DB_NAME, $connection);
if (!$db_select) {
	die("Database selection failed: " . mysql_error());
}

// 3. Perform database query
$sql = "SELECT * FROM subjects";
$result = mysql_query($sql, $connection);
if (!$result) {
	die("Database query failed: " . mysql_error());
}

// 4. Use returned data
while ($row = mysql_fetch_array($result)) {
	// output data
}

// 5. Close connection
if(isset($connection)) {
	mysql_close($connection);
	unset($connection);
}
?>