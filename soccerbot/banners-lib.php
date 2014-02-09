<?php

require_once('reddit-lib.php');

function banners_bot($subreddit, $banners_subreddit) {
  global $db;

  $about = reddit_subreddit_about($subreddit);
  $title = $about->header_title;
  $url = $about->header_img;

  $query = $db->query("SELECT * FROM banners WHERE url='$url'");
  if (is_object($query)) {
    $row = $query->fetch();
    if (!$row['url']) {
      $timestamp = time();
      $escaped_title = preg_replace("/'/", "''", $title);
      $db->query("INSERT INTO banners (url,title,timestamp) VALUES ('$url','$escaped_title',$timestamp)");
      reddit_submit_link($banners_subreddit, $title, $url);
      print("Banner: $title\n");
    }
  }
}

?>
