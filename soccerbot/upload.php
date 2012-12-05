<?php
header('Content-Type: text/plain');

include('reddit-lib.php');
include('flair-lib.php');
include('config.php');

reddit_login($username, $password);

flair_upload('soccer', 'upload.csv');
?>
