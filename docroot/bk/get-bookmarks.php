<?php
$portalID = isset($_GET["portalID"]) ? $_GET["portalID"] : false;

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