<?php
if(isset($_GET['link']) && $_GET['link'] != '') {
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

  $sql = "
    SELECT		url, linkID, clicks
    FROM		links
    WHERE       linkID = ".$_GET['link'];
  echo $sql;

  $sql_result = mysqli_query($connection, $sql) or die ("Couldn't execute query.<br />$sql");
  $row = mysqli_fetch_array($sql_result);

  $url = $row['url'];
  $clicks = $row['clicks'];
  $clicks++;

  $sql = "UPDATE links SET clicks = ".$clicks." WHERE linkID = ".$row['linkID']." LIMIT 1";
  $sql_result = mysqli_query($connection, $sql) or die ("Couldn't execute query.<br />$sql");
  header("Location: ".$url);
}

?>
