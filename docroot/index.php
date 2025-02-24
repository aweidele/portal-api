<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

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

$conn = new mysqli($server, $user, $password, $database);
if ($conn->connect_error) {
	die(json_encode(["error" => "Database connection failed"]));
} 


$request_uri = isset($_GET['request']) ? trim($_GET['request']) : '';
$method = $_SERVER["REQUEST_METHOD"];


// echo json_encode([$request_uri, $method]);

require_once("functions/functions.php");

switch ($method) {
	case "GET":
		$portalID = isset($_GET["portalID"]) ? $_GET["portalID"] : false;
		get_bookmarks($conn,$portalID);
		break;
	case "POST":
		$data = json_decode(file_get_contents("php://input"), true);
		
		if($request_uri === 'bookmarks/add') {
			add_bookmark($conn, $data);
		}

		if($request_uri === 'categories/add') {
			add_category($conn, $data);
		}
		break;
	case "PUT":
		$data = json_decode(file_get_contents("php://input"), true);

		if($request_uri === 'bookmarks/edit') {
			edit_bookmark($conn, $data);
		}

		elseif($request_uri === 'bookmarks/set-inactive') {
			delete_bookmark($conn, $data);
		}

		elseif($request_uri === 'categories/reorder') {
			reorder_categories($conn, $data);
		}
		break;
}