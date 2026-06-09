<?php

$config = "../config.php";
$configDefault = "../config_default.php";

if (file_exists($config)) {
	require $config;
} elseif (file_exists($configDefault)) {
	require $configDefault;
} else {
	die('Both main and fallback files are missing.');
}

$db = "mysql:host={$server};dbname={$database};charset=utf8mb4";

try {
    $pdo = new PDO($db, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
}
catch (PDOException $e) {
    // var_dump($e->getMessage());
    echo 'A problem occured with the database connection...';
    die();
}

$stmt = $pdo->prepare('SELECT * FROM `todo`');
$stmt->execute();
$todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<ul>
  <?php foreach( $todos as $todo ) : ?>
    <li><?php echo $todo["todo_title"]; ?></li>
  <?php endforeach; ?>
</ul>
<pre>
<?php
print_r($todos);
?>
</pre>