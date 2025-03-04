<?php
function jsonResponse($data, $status = 200) {
  http_response_code($status);
  echo json_encode($data);
  exit;
}

function authenticate() {
  $headers = getallheaders();

  if (!isset($headers['Authorization'])) {
    jsonResponse(["error" => "Missing authorization token"], 401);
  }
  $token = str_replace("Bearer ", "", $headers['Authorization']);
  $decoded = validateToken($token);

  if (!$decoded) {
    jsonResponse(["error" => "Invalid or expired token"], 401);
  } 

  return true;
}

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
  if(authenticate()) {
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
}

function edit_bookmark($conn, $bookmark) {
  if (!isset($bookmark["linkName"], $bookmark["url"], $bookmark["cat"], $bookmark["linkID"])) {
    echo json_encode(["error" => "Missing required fields", "function" => "edit_bookmark"]);
    exit;
  }

  if(isset($bookmark["active"])) {
    $active = $bookmark["active"];
  } else {
    $active = 1;
  }

  if(!$stmt = $conn->prepare("UPDATE links SET linkName=?, url=?, cat=?, active=? WHERE linkID=?")) {
    echo json_encode(["error" => "There was an error"]);
    exit;
  }
  if(!$stmt->bind_param("ssssi", $bookmark["linkName"], $bookmark["url"], $bookmark["cat"], $active, $bookmark["linkID"])){
    echo json_encode(["error" => "There was an error2"]);
    exit;
  }

  echo json_encode(["success" => $stmt->execute()]);
}

function delete_bookmark($conn, $bookmark) {
  if (!isset($bookmark["linkID"])) {
    echo json_encode(["error" => "Missing link ID"]);
    exit;
  }

  if(!$stmt = $conn->prepare("UPDATE links SET active=0 WHERE linkID=?")) {
    echo json_encode(["error" => "There was an error"]);
    exit;
  }
  if(!$stmt->bind_param("i", $bookmark["linkID"])){
    echo json_encode(["error" => "There was an error2"]);
    exit;
  }

  echo json_encode(["success" => $stmt->execute()]);
}

function reorder_categories($conn, $data) {
  $caseStatements = [];
  $catIDs = [];

  foreach ($data["categories"] as $item) {
    $caseStatements[] = "WHEN catID = '{$conn->real_escape_string($item['catID'])}' THEN {$conn->real_escape_string($item['rank'])}";
    $catIDs[] = "'{$conn->real_escape_string($item['catID'])}'";
  }

  if (!empty($caseStatements)) {
    $sql = "UPDATE link_cat SET rank = CASE " . implode(" ", $caseStatements) . " END WHERE catID IN (" . implode(",", $catIDs) . ")";
    $conn->query($sql);
  }

  echo json_encode([
    "data"=>$data,
    "caseStatements"=>$caseStatements,
    "catIDs"=>$catIDs,
    "sql" => $sql
  ]);
}

function add_category($conn, $data) {
  if (!isset($data["catName"], $data["portal"])) {
    echo json_encode(["error" => "Missing required fields"]);
    exit;
  }

  $rank = isset($data["rank"]) ? $data["rank"] : 1000;

  if(!$stmt = $conn->prepare("INSERT INTO link_cat (catName, portal, rank) VALUES (?, ?, ?)")) {
    echo json_encode(["error" => "There was an error"]);
    exit;
  }
  if(!$stmt->bind_param("sss", $data["catName"], $data["portal"], $rank)){
    echo json_encode(["error" => "There was an error2"]);
    exit;
  }

  $response = [];
  if($stmt->execute()) {
    $last_id = $conn->insert_id;
    $result = $conn->query("SELECT * FROM link_cat WHERE catID = $last_id");

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

function login($conn, $data) {
  $portal = $data['portal'] ?? '';
  $password = $data['password'] ?? '';

  $stmt = $conn->prepare("SELECT * FROM portals WHERE portal = ?");
  $stmt->bind_param("s", $portal);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();

  if ($result && password_verify($password, $result['password'])) {
    $token = generateToken($result['id']);
    echo json_encode(["token" => $token]);
  } else {
      echo json_encode(["error" => "Invalid credentials"], 401);
  }
}

//echo json_encode(["msg"=>"Put successful!", "action"=>$data["action"], "data"=>$data]);