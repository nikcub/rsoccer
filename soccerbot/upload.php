<?php
require('flair-lib.php');
require('config.php');

header('Content-Type: text/plain');
header("User-Agent: soccerbot by /u/9jack9");

reddit_login();

flair_upload('upload.csv');
?>
