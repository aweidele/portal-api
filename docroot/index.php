<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');
$config = "config.php";
$configDefault = "config_default.php";
$output = [];

if (file_exists($config)) {
	require $config;
} elseif (file_exists($configDefault)) {
	require $configDefault;
} else {
	die('Both main and fallback files are missing.');
}

$connection = mysqli_connect($server, $user, $password) or die ("Couldn't connect to server.");
$db = mysqli_select_db($connection,$database) or die ("Couldn't select database");

$portalID = $_GET["portalID"];

/* GET BOOKMARKS */
$sql = "SELECT * FROM links ";
$sql .= "WHERE active = 1 ";
if($portalID) { 
	$sql .= "AND portal = '".$portalID."' "; 
}
$sql .= "ORDER BY linkName";

$sql_result = mysqli_query($connection, $sql) or die ("Couldn't execute query.");
$results = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

$output['bookmarks'] = [
	'results' => $results,
	'query'=>$sql
];

/* GET LINK CATEGORIES */
$sql = "SELECT * FROM link_cat ";
if($portalID) {
	$sql .= "WHERE portal = '".$portalID."' ";
}
$sql .= "ORDER BY rank";
$sql_result = mysqli_query($connection, $sql) or die ("Couldn't execute query.");
$results = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

$output['categories'] = [
	'results' => $results,
	'query'=>$sql
];
echo json_encode($output);