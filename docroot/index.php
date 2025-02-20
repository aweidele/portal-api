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

$conn = new mysqli("localhost", "your_user", "your_password", "your_database");