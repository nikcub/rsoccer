<?php
header('Content-Type: text/plain');

include('reddit-lib.php');
include('flair-lib.php');
include('link-lib.php');
include('config.php');

reddit_login($username, $password);

link_bot('soccer');
flair_bot('soccer');
?>
