<?php

$db = new PDO('sqlite:crests.db');

$query = $db->query("SELECT * FROM teams WHERE ((sort IS NULL) OR (sort=''))");

while ($row = $query->fetch()) {
  $sort = $row['flair'];
  if ($row['sprite'] != 1) {
    $sort = substr($sort, 0, -3);
  }
  $sort = preg_replace('/\-/', '_', $sort);
  $db->query("UPDATE teams SET sort='".$sort."' WHERE flair='".$row['flair']."'");
}
?>
