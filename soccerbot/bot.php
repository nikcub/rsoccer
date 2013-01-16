<?php
header('Content-Type: text/plain');

print(gmdate('c')."\n");

require('flair-lib.php');
require('link-lib.php');
require('config.php');

reddit_login($username, $password);

link_bot($subreddit);
alert_bot($subreddit);
flair_bot($subreddit);
spam_bot($subreddit);
?>
