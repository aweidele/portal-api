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

$method = $_SERVER["REQUEST_METHOD"];
require_once("functions/functions.php");

switch ($method) {
	case "GET":
		$portalID = isset($_GET["portalID"]) ? $_GET["portalID"] : false;
		get_bookmarks($conn,$portalID);
		break;
	case "POST":
		$data = json_decode(file_get_contents("php://input"), true);
		add_bookmark($conn, $data);
		break;
}