<?php

$db = new PDO('sqlite:crests.db');

$query = $db->query("SELECT * FROM teams WHERE ((sort IS NULL) OR (sort=''))");

while ($row = $query->fetch()) {
  $sort = preg_replace('/\-s\d$/', '', $row['flair']);
  $sort = preg_replace('/\-/', '_', $sort);
  $db->query("UPDATE teams SET sort='".$sort."' WHERE flair='".$row['flair']."'");
}
?>
