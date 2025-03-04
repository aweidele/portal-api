<pre>
<?php
// 9e56f67e
// b8aee059

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

$sql = "SELECT * FROM portals";
$result = $connection->query($sql);
if (!$result) {
  die("Error: " . $connection->error);
}
?>
</pre>