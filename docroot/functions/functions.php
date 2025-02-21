<?php

function get_bookmarks($conn,$portalID) {
  $sql = "SELECT * FROM links WHERE active = 1" . ($portalID ? " AND portal = '".$portalID."'" : "") . " ORDER BY linkName";
  $result = $conn->query($sql);
  if (!$result) {
    die("Error: " . $conn->error);
  }
  $bookmarks = [];
  while ($row = $result->fetch_assoc()) {
      $bookmarks[] = $row;
  }

  $sql = "SELECT * FROM link_cat" . ($portalID ? " WHERE portal = '".$portalID."'" : "") . " ORDER BY rank";
  $result = $conn->query($sql);
  if (!$result) {
    die("Error: " . $conn->error);
  }
  $categories = [];
  while ($row = $result->fetch_assoc()) {
      $categories[] = $row;
  }

  echo json_encode([
    "categories" => $categories,
    "bookmarks" => $bookmarks
  ]);
}

function add_bookmark($conn, $bookmark) {
  if (!isset($bookmark["linkName"], $bookmark["url"], $bookmark["cat"], $bookmark["portal"])) {
    echo json_encode(["error" => "Missing required fields"]);
    exit;
  }

  

  if(!$stmt = $conn->prepare("INSERT INTO links (linkName, url, cat, portal) VALUES (?, ?, ?, ?)")) {
    echo json_encode(["error" => "There was an error"]);
    exit;
  }
  if(!$stmt->bind_param("ssss", $bookmark["linkName"], $bookmark["url"], $bookmark["cat"], $bookmark["portal"])){
    echo json_encode(["error" => "There was an error2"]);
    exit;
  }

  $response = [];
  if($stmt->execute()) {
    $last_id = $conn->insert_id;
    $result = $conn->query("SELECT * FROM links WHERE linkID = $last_id");

    if ($row = $result->fetch_assoc()) {
      $response = ["success" => true, "data" => $row];
    } else {
      $response = ["success" => false, "error" => "Could not retrieve inserted record"];
    }
  } else {
    $response = ["success" => false, "error" => $stmt->error];
  }

  echo json_encode($response);
}

function edit_bookmark($conn, $bookmark) {
  if (!isset($bookmark["linkName"], $bookmark["url"], $bookmark["cat"], $bookmark["linkID"])) {
    echo json_encode(["error" => "Missing required fields"]);
    exit;
  }

  if(!$stmt = $conn->prepare("UPDATE links SET linkName=?, url=?, cat=? WHERE linkID=?")) {
    echo json_encode(["error" => "There was an error"]);
    exit;
  }
  if(!$stmt->bind_param("sssi", $bookmark["linkName"], $bookmark["url"], $bookmark["cat"], $bookmark["linkID"])){
    echo json_encode(["error" => "There was an error2"]);
    exit;
  }

  echo json_encode(["success" => $stmt->execute()]);
}

//echo json_encode(["msg"=>"Put successful!", "action"=>$data["action"], "data"=>$data]);