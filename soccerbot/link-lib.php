<?php

function link_bot($subreddit) {
  $db = new PDO('sqlite:crests.db');

  $query = $db->query("SELECT * FROM admin");
  $row = $query->fetch();

  $before = $row['last_link'];

  $list = reddit_new($subreddit, $before);

  if (!empty($list)) {
    $list = array_reverse($list);

    foreach ($list as $entry) {
      $link = $entry->data->name;
      if (!$entry->data->is_self) {
        $domain = $entry->data->domain;
        if ($domain == 'twitter.com') {
          $twitter = explode('/', substr($entry->data->url, 20));
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
            print($link.',Official,'.$css_class."\n");
          }
        }
      }
    }

    $db->query("UPDATE admin SET last_link='".$link."'");
  }
}

?>
