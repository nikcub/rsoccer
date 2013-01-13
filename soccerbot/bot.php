<?php
header('Content-Type: text/plain');

include('reddit-lib.php');
include('flair-lib.php');
require('blacklist.php');
include('link-lib.php');
include('config.php');

reddit_login($username, $password);

link_bot($subreddit);
alert_bot($subreddit);
flair_bot($subreddit);
spam_bot($subreddit);
?>
