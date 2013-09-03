<?php

require_once('reddit-lib.php');
require('blacklist.php');

function alert_bot($subreddit) { // watches reported links
  global $db;

  $list = reddit_reported($subreddit);

  foreach ($list as $entry) {
    $type = $entry->kind == 't1' ? 'comment' : 'post';
    $entry = $entry->data;
    $num_reports = $entry->num_reports;
    if ($num_reports > 1) {
      $thing = $entry->name;
      $query = $db->query("SELECT * FROM alerted WHERE thing='$thing'");
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
            $text = preg_replace('/<p>/', '', $text[0]);
            $text = preg_replace('/\n/', ' ', $text);
          }
          $message = "The following $type has been reported $num_reports times:\n\n> [$text]($permalink)";
          reddit_sendMessage('/r/'.$subreddit, $title, $message);
          $timestamp = time();
          $db->query("INSERT INTO alerted (thing,timestamp) VALUES ('$thing',$timestamp)");
          print("Alerting: $thing\n");
        }
      }
    }
  }
}

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

function link_bot($subreddit) { // watches the new queue
  global $db;

  #$query = $db->query("SELECT * FROM admin WHERE r='$subreddit'");
  #$row = $query->fetch();

  #$before = $row['last_link'];

  $list = reddit_new($subreddit, $before);

  if (!empty($list)) {
    $list = array_reverse($list);

    foreach ($list as $entry) {
      $entry = $entry->data;
      $link = $entry->name;
      if (!$entry->is_self) {
        $domain = $entry->domain;
        $reason = getBlacklistReason($domain);
        if ($reason) {
          if (!$entry->approved_by) {
            link_remove($subreddit, $entry, $reason);
          }
        } else if (!$entry->link_flair_css_class) {
          if ($domain == 'twitter.com') {
            $twitter = explode('/', substr($entry->url, 20));
            $twitter = $twitter[0];
            $query = $db->query("SELECT * FROM sources WHERE (type='Twitter' AND source LIKE '$twitter')");
          } else {
            $query = $db->query("SELECT * FROM sources WHERE (type='Web' AND source='$domain')");
          }
          if (is_object($query)) {
            $row = $query->fetch();
            $team = $row['team'];
            if ($team) {
              $css_class = $team;
              if (preg_match('/\-s\d$/', $team)) {
                $css_class = preg_replace('/^(.+)\-(s\d)$/', '$1-$2 $2', $css_class);
              }
              reddit_linkflair($subreddit, $link, 'Official', $css_class);
              print("Link flair ($css_class): '".$entry->title."'\n");
            }
          }
        }
      }
    }

    #$db->query("UPDATE admin SET last_link='".$link."' WHERE r='$subreddit'");
  }
}

function link_remove($subreddit, $link, $reason) {
  global $blacklist_reasons, $guidelines;

  $explanation = $blacklist_reasons[$reason].' '.$guidelines;

  $id = $link->id;
  $title = $link->title;

  reddit_remove($subreddit, $link->name, $reason == 'spam');
  print("Removed ($reason): '$title'\n");

  $prefix = "*Beep*.\n\nSorry, this post has been removed by a bot.\n\n";
  $suffix = "\n\nIf you feel that this post was removed by mistake then please [message the moderators]"
    ."(/message/compose/"
      ."?to=".rawurlencode("/r/$subreddit")
      ."&subject=".rawurlencode("Why was this post removed?")
      ."&message=".rawurlencode("[LINK](/$id)")
    .").";

  $comment = reddit_comment($subreddit, $link->name, $prefix.$explanation.$suffix);

  reddit_distinguish($subreddit, $comment->name);

  #$message = "Sorry. Your post was removed by a bot:\n\n> [$title](/$id)\n\n".$explanation.$suffix;
  #reddit_sendMessage($link->author, 'Link Removal', $message);
}

?>
