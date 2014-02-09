<?php

require_once('reddit-lib.php');

function flair_bot($subreddit) {
  global $db;

  $messages = reddit_getUnreadMail();

  $data = array();
  foreach ($messages as $message) {
    $message = $message->data;
    if ($message->subject == 'crest') {
      $author = $message->author;
      if ($message->body == 'none') {
        array_push($data, "$author,,");
      } else {
        $id = preg_replace('/\-s\d+$/', '', $message->body);
        $query = $db->query("SELECT name FROM teams WHERE id=$id");
        if (is_object($query)) {
          $row = $query->fetch();
          $text = $row['name'];
          $css_class = preg_replace('/\-/', ' ', $message->body);
          if ($text) {
            array_push($data, "$author,$text,$css_class");
          }
        }
      }
    }
  }
  flair_batch($subreddit, $data);
  reddit_clearUnreadMail();
}

function linkflair_bot($subreddit) { // watches the new queue
  global $db;

  $list = reddit_new($subreddit, $before);

  if (!empty($list)) {
    $list = array_reverse($list);

    foreach ($list as $entry) {
      $entry = $entry->data;
      $link = $entry->name;
      if (!$entry->is_self) {
        if (!$entry->link_flair_css_class) {
          $domain = $entry->domain;
          if ($domain == 'twitter.com') {
            $twitter = explode('/', $entry->url);
            $twitter = $twitter[3];
            $query = $db->query("SELECT * FROM sources WHERE (type='Twitter' AND source LIKE '$twitter')");
          } else {
            $query = $db->query("SELECT * FROM sources WHERE (type='Web' AND source='$domain')");
          }
          if (is_object($query)) {
            $row = $query->fetch();
            $team = $row['team'];
            if ($team) {
              $css_class = $team;
              $spriteQuery = $db->query("SELECT sprite FROM teams WHERE id=$team");
              $row = $spriteQuery->fetch();
              $sprite = $row['sprite'];
              if ($sprite != 1) {
                $css_class .= " s$sprite";
              }
              reddit_linkflair($subreddit, $link, 'Official', $css_class);
              print("Link flair ($css_class): '".$entry->title."'\n");
            }
          }
        }
      }
    }
  }
}

function flair_list($subreddit) {
  $list = reddit_flairlist($subreddit);

  foreach ($list as $entry) {
    echo($entry->user.','.$entry->flair_text.','.$entry->flair_css_class."\n");
  }
}

function flair_batch($subreddit, $data, $limit=100) {
  $count = 0;
  $batch = array();

  foreach ($data as $line) {
    $batch[$count++] = $line;
    if ($count == $limit) {
      $csv = implode("\n", $batch);
      reddit_flaircsv($subreddit, $csv);
      echo($csv."\n");
      $batch = array();
      $count = 0;
    }
  }
  if ($count > 0) {
    $csv = implode("\n", $batch);
    reddit_flaircsv($subreddit, $csv);
    echo($csv);
  }
  if (!empty($data)) {
    print("\n");
  }
}

?>
