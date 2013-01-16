<?php
header('Content-Type: text/plain');

set_time_limit(60);

include('flair-lib.php');
include('config.php');

$FLAIR = "puerto-rican-islanders-s2";

$NEW_FLAIR = "puerto-rico-islanders-s2";
$NEW_NAME  = "Puerto Rico Islanders";

$data = array();

$db = new PDO('sqlite:crests.db');

$query = $db->query("SELECT * FROM users WHERE team='".$FLAIR."'");

while (($row = $query->fetch())) {
  array_push($data, $row['name'].',"'.$NEW_NAME.'",'.$NEW_FLAIR);
}

reddit_login($username, $password);

flair_batch('soccer', $data);
?>
