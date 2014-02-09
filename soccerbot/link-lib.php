<?php

require_once('reddit-lib.php');

function spam_bot($subreddit) { // watches the spam queue
  $list = reddit_spam($subreddit);

  $match_thread = '/^((post|pre)[\s-]?)?match[\s-]thread|^league\sround[\s-]?up/i';

  foreach ($list as $entry) {
    if ($entry->kind == 't3') {
      $entry = $entry->data;
      if ($entry->banned_by === TRUE) { // auto-removed
        if ($entry->approved_by) { // re-approve
          reddit_approve($subreddit, $entry->name);
        } else if ($entry->is_self && preg_match($match_thread, $entry->title)) {
          reddit_approve($subreddit, $entry->name);
          print("Approved: '".$entry->title."'\n");
        }
      }
    }
  }
}

?>
