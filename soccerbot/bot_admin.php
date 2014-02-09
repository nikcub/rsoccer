<?php
header('Content-Type: text/plain');
header("User-Agent: soccerbot by /u/9jack9");

require('admin-lib.php');

@ini_set('zlib.output_compression', 0);

@ini_set('implicit_flush', 1);

@ob_end_clean();

set_time_limit(0);

ob_implicit_flush(1);

if (isset($argv)) {
  $password = $argv[1];
} else {
  $password = $_GET['password'];
}

if ($username && $password) {
  reddit_login();

  if ($bot_subreddit) {
    upload_sprites($bot_subreddit);
    upload_bot_css($bot_subreddit);
  }

  upload_sprites($subreddit, true);
  upload_css($subreddit);

  flair_bot($subreddit);
  upload_users($subreddit);
  download_users($subreddit);
  rename_teams($subreddit);

  if ($bot_subreddit) {
    upload_bot_sidebar($bot_subreddit);
    upload_bot_index($bot_subreddit);
    if ($stats_id) {
      upload_stats($bot_subreddit, $stats_id);
    }
  }
} else {
  print("Username and password required.\n");
}
?>
