<?php
require('flair-lib.php');

header('Content-Type: text/plain');
header("User-Agent: soccerbot by /u/9jack9");

set_time_limit(60);

$FLAIR = "ca-penarol";

$NEW_FLAIR = "penarol";
$NEW_NAME  = "Peñarol";

$data = array();

$query = $db->query("SELECT * FROM users WHERE flair='".$FLAIR."'");

while (($row = $query->fetch())) {
  $user = $row['user'];
  array_push($data, $user.',"'.$NEW_NAME.'",'.$NEW_FLAIR);
  $db->query("UPDATE users SET flair='$NEW_FLAIR' WHERE user='$user'");
}

reddit_login();

flair_batch($subreddit, $data);
?>
