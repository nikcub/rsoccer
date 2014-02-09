<?php
header('Content-Type: text/plain');
header("User-Agent: soccerbot by /u/9jack9");

require('flair-lib.php');
require('link-lib.php');
require('banners-lib.php');

if (isset($argv)) {
  $password = $argv[1];
} else {
  $password = $_GET['password'];
}

if ($username && $password) {
  print(gmdate('c')."\n");

  reddit_login();

  flair_bot($subreddit);
  linkflair_bot($subreddit);
  spam_bot($subreddit);

  if ($banners_subreddit) {
    banners_bot($subreddit, $banners_subreddit);
  }
} else {
  print("Username and password required.\n");
}
?>
