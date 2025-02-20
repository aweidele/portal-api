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