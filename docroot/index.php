<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');
$config = "config.php";
$configDefault = "config_default.php";

if (file_exists($config)) {
	require $config;
} elseif (file_exists($configDefault)) {
	require $configDefault;
} else {
	die('Both main and fallback files are missing.');
}

$portalID = $_GET["portalID"];
$sql = "SELECT * FROM links, link_cat ";
$sql .= "FROM links, link_cat ";
$sql .= "WHERE active = 1 AND catID = cat ";
$sql .= "ORDER BY	rank, linkName";

// $sql = "
// 	SELECT		*
// 	FROM		links, link_cat
// 	WHERE		active = 1 AND catID = cat
// 	ORDER BY	rank, linkName";

	// echo $sql;
$connection = mysqli_connect($server, $user, $password) or die ("Couldn't connect to server.");
$db = mysqli_select_db($connection,$database) or die ("Couldn't select database");

$sql_result = mysqli_query($connection, $sql) or die ("Couldn't execute query.");
$results = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);
$results[] = $portalID;
echo json_encode($results);