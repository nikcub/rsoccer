<?php
require('config.php');
require('flair-lib.php');

header('Content-Type: text/plain');
header("User-Agent: soccerbot by /u/9jack9");

set_time_limit(60);

$FLAIR = "puerto-rican-islanders-s2";

$NEW_FLAIR = "puerto-rico-islanders-s2";
$NEW_NAME  = "Puerto Rico Islanders";

$data = array();

$query = $db->query("SELECT * FROM users WHERE team='".$FLAIR."'");

while (($row = $query->fetch())) {
  array_push($data, $row['name'].',"'.$NEW_NAME.'",'.$NEW_FLAIR);
}

reddit_login();

flair_batch($data);
?>
