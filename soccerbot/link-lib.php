<?php

function alert_bot($subreddit) { // watches reported links
  $db = new PDO('sqlite:crests.db');

  $list = reddit_reported($subreddit);

  foreach ($list as $entry) {
    $type = $entry->kind == 't1' ? 'comment' : 'post';
    $entry = $entry->data;
    $num_reports = $entry->num_reports;
    if ($num_reports > 1) {
      $link = $entry->name;
      $query = $db->query("SELECT * FROM alerted WHERE thing='".$link."'");
      if (is_object($query)) {
        $row = $query->fetch();
        if (!$row['thing']) {
          $title = "Reported $type ($num_reports reports)";
          if ($type == 'post') {
            $permalink = $entry->permalink;
            $text = $entry->title;
          } else {
            $permalink = '/r/'.$subreddit.'/comments/'.substr($entry->link_id, 3).'/a/'.$entry->id.'?context=2';
            $text = strip_tags(html_entity_decode($entry->body_html), '<p>');
            $text = explode('</p>', $text);
            $text = $text[0];
            if (substr($text, 0, 3) == '<p>') {
              $text = substr($text, 3);
            }
          }
          $message = "The following $type has been reported $num_reports times:\n\n> [$text]($permalink)";
          reddit_sendMessage('/r/'.$subreddit, $title, $message);
          $db->query("INSERT INTO alerted (thing) VALUES ('$link')");
          print("Alerting: $link\n");
        }
      }
    }
  }
}

function spam_bot($subreddit) { // watches the spam queue
  $list = reddit_spam($subreddit);

  foreach ($list as $entry) {
    if ($entry->kind == 't3') {
      $entry = $entry->data;
      if ($entry->is_self && !$entry->banned_by) {
        if (preg_match('/^((post|pre)[\s-]?)?match[\s-]thread|^league\sround[\s-]?up/i', $entry->title)) {
          reddit_approve($entry->name);
          print("Approved: '".$entry->title."'\n");
        }
      }
    }
  }
}

function link_bot($subreddit) { // watches the new queue
  global $blacklist, $blacklist_reasons, $guidelines;

  $db = new PDO('sqlite:crests.db');

  $query = $db->query("SELECT * FROM admin WHERE r='$subreddit'");
  $row = $query->fetch();

  $before = $row['last_link'];

  $list = reddit_new($subreddit, $before);

  if (!empty($list)) {
    $list = array_reverse($list);

    foreach ($list as $entry) {
      $entry = $entry->data;
      $link = $entry->name;
      if (!$entry->is_self) {
        $domain = $entry->domain;
        if (isset($blacklist[$domain])) {
          if (!$entry->approved_by) {
            $reason = $blacklist[$domain];
            $explanation = $blacklist_reasons[$reason].' '.$guidelines;
            link_remove($subreddit, $entry, $explanation);
          }
        } else if ($domain == 'twitter.com') {
          $twitter = explode('/', substr($entry->url, 20));
          $twitter = $twitter[0];
          $query = $db->query("SELECT * FROM teams WHERE twitter='".$twitter."'");
        } else {
          $query = $db->query("SELECT * FROM teams WHERE site='".$domain."'");
        }
        if (is_object($query)) {
          $row = $query->fetch();
          $team = $row['name'];
          if ($team) {
            $css_class = $row['flair'];
            $sprite = $row['sprite'];
            if ($sprite != 1) {
              $css_class .= ' s'.$sprite;
            }
            reddit_linkflair($subreddit, $link, 'Official', $css_class);
            print("Link flair($css_class): '".$entry->title."'\n");
          }
        }
      }
    }

    $db->query("UPDATE admin SET last_link='".$link."' WHERE r='$subreddit'");
  }
}

function link_remove($subreddit, $link, $explanation) {
  $id = $link->id;
  $title = $link->title;
  $prefix = "Sorry, this post has been removed by a bot.\n\n";
  $suffix = "\n\nIf you feel that this post was removed by mistake then please [message the moderators]"
    ."(/message/compose/"
      ."?to=".rawurlencode("/r/$subreddit")
      ."&subject=".rawurlencode("Why was this post removed?")
      ."&message=".rawurlencode("[LINK](/$id)")
    .").";
  $comment = $prefix.$explanation.$suffix;
  $comment = reddit_comment($link->name, $comment);
  reddit_distinguish($comment->id);
  #$message = "Sorry. Your post was removed by a bot:\n\n> [$title](/$id)\n\n".$explanation.$suffix;
  #reddit_sendMessage($link->author, 'Link Removal', $message);
  reddit_remove($link->name);
  print("Removed: '$title'\n");
}

?>
