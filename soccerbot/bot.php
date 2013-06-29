<?php
header('Content-Type: text/plain');
header("User-Agent: soccerbot by /u/9jack9");

require('flair-lib.php');
require('link-lib.php');
require('banners-lib.php');

print(gmdate('c')."\n");

reddit_login();

link_bot($subreddit);
alert_bot($subreddit);
flair_bot($subreddit);
spam_bot($subreddit);

if ($banners_subreddit) {
  banners_bot($subreddit, $banners_subreddit);
}
?>
