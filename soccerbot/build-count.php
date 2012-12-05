<?php
header('Content-Type: text/plain');

$db = new PDO('sqlite:crests.db');

$query = $db->query('SELECT flair FROM teams');

while ($row = $query->fetch()) {
  $users = $db->query("SELECT COUNT(*) AS count FROM users WHERE team='".$row['flair']."'");
  if ($team = $users->fetch()) {
    $db->query("UPDATE teams SET users=".$team['count']." WHERE flair='".$row['flair']."'");
  }
}
?>
