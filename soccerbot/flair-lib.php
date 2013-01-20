<?php

require_once('reddit-lib.php');

function flair_bot() {
  global $db;

  $messages = reddit_getUnreadMail();

  $data = array();
  foreach ($messages as $message) {
    $message = $message->data;
    if ($message->subject == 'crest') {
      if ($message->body == 'none') {
        array_push($data, $message->author.',,');
      } else {
        $flair = $message->body;
        $query = $db->query("SELECT * FROM teams WHERE flair='$flair'");
        if (is_object($query)) {
          $row = $query->fetch();
          $team = $row['name'];
          if ($team) {
            $author = $message->author;
            $css_class = $flair;
            $sprite = $row['sprite'];
            if ($sprite != 1) {
              $css_class .= ' s'.$sprite;
            }
            array_push($data, "$author,$team,$css_class");
          }
        }
      }
    }
  }
  flair_batch($data);
  reddit_clearUnreadMail();
}

function flair_list() {
  $list = reddit_flairlist();

  foreach ($list as $entry) {
    echo($entry->user.','.$entry->flair_text.','.$entry->flair_css_class."\n");
  }
}

function flair_upload($fileName) {
  $handle = fopen($fileName, "r");
  if ($handle) {
    $data = array();
    while (($line = fgets($handle)) !== false) {
      array_push($data, trim($line));
    }
    fclose($handle);
    flair_batch($data);
  } else {
    die("Could not open file $fileName\n");
  }
}

function flair_batch($data) {
  $count = 0;
  $batch = array();
  foreach ($data as $line) {
    $batch[$count++] = $line;
    if ($count == 100) {
      $csv = implode("\n", $batch);
      reddit_flaircsv($csv);
      echo($csv);
      $batch = array();
      $count = 0;
    }
  }
  if ($count > 0) {
    $csv = implode("\n", $batch);
    reddit_flaircsv($csv);
    echo($csv);
  }
  if (!empty($data)) {
    print("\n");
  }
}

?>
