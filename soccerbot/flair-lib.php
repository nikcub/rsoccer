<?php
function flair_bot($subreddit) {
  $db = new PDO('sqlite:crests.db');
  $messages = reddit_getUnreadMail();
  $data = array();
  foreach ($messages as $message) {
    $message = $message->data;
    if ($message->subject == 'crest') {
      if ($message->body == 'none') {
        array_push($data, $message->author.',,');
      } else {
        $query = $db->query("SELECT * FROM teams WHERE flair='".$message->body."'");
        if (is_object($query)) {
          $row = $query->fetch();
          $team = $row['name'];
          $css_class = $message->body;
          $sprite = $row['sprite'];
          if ($sprite != 1) {
            $css_class .= ' s'.$sprite;
          }
          if ($team) {
            array_push($data, $message->author.',"'.$team.'","'.$css_class.'"');
          }
        }
      }
    }
  }
  flair_batch($subreddit, $data);
  reddit_clearUnreadMail();
}

function flair_list($subreddit) {
  $list = reddit_flairlist($subreddit);

  foreach ($list as $entry) {
    echo($entry->user.','.$entry->flair_text.','.$entry->flair_css_class."\n");
  }
}

function flair_upload($subreddit, $fileName) {
  $handle = fopen($fileName, "r");
  if ($handle) {
    $data = array();
    while (($line = fgets($handle)) !== false) {
      array_push($data, trim($line));
    }
    fclose($handle);
    flair_batch($subreddit, $data);
  } else {
    die('Could not open file '.$fileName);
  }
}

function flair_batch($subreddit, $data) {
  $count = 0;
  $batch = array();
  foreach ($data as $line) {
    $batch[$count++] = $line;
    if ($count == 100) {
      $csv = implode("\n", $batch);
      reddit_flaircsv($subreddit, $csv);
      echo($csv);
      $batch = array();
      $count = 0;
    }
  }
  if ($count > 0) {
    $csv = implode("\n", $batch);
    reddit_flaircsv($subreddit, $csv);
    echo($csv);
  }
}
?>
