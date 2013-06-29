<?php
require('config.php');

$modhash = '';
$cookies = '';

// --------------------------------------
// Submit
// --------------------------------------

function reddit_submit_link($subreddit, $title, $url) {
  return reddit_POST($subreddit, 'api/submit', array(
    'kind' => 'link',
    'sr' => $subreddit,
    'title' => $title,
    'url' => $url
  ));
}

// --------------------------------------
// Listings
// --------------------------------------

function reddit_new($subreddit) {
  return reddit_listing($subreddit, 'new');
}

function reddit_reported($subreddit) {
  return reddit_listing($subreddit, 'about/reports');
}

function reddit_spam($subreddit) {
  return reddit_listing($subreddit, 'about/spam');
}

function reddit_listing($subreddit, $list, $limit=25, $sort='new', $after='', $before='') {
  $url = "r/$subreddit/$list.json?limit=$limit&sort=$sort&after=$after&before=$before";
  $data = reddit_GET($url);

  return $data->children;
}

function reddit_editusertext($subreddit, $id, $text) {
  return reddit_POST($subreddit, 'api/editusertext', array(
    'thing_id' => $id,
    'text' => $text
  ));
}

// --------------------------------------
// Moderation
// --------------------------------------

function reddit_remove($subreddit, $id, $spam = false) {
  return reddit_POST($subreddit, 'api/remove', array(
    'id' => $id,
    'spam' => $spam ? 'on' : 'off'
  ));
}

function reddit_approve($subreddit, $id) {
  return reddit_POST($subreddit, 'api/approve', array(
    'id' => $id
  ));
}

function reddit_distinguish($subreddit, $id) {
  return reddit_POST($subreddit, 'api/distinguish', array(
    'how' => 'yes',
    'id' => $id
  ));
}

// --------------------------------------
// Subreddit
// --------------------------------------

function reddit_subreddit_stylesheet($subreddit, $css) {
  return reddit_POST($subreddit, 'api/subreddit_stylesheet', array(
    'op' => 'save',
    'stylesheet_contents' => $css
  ));
}

function reddit_subreddit_about($subreddit) {
  return reddit_GET("r/$subreddit/about.json");
}

function reddit_upload_sr_img($subreddit, $fileName, $name = '') {
  global $REDDIT, $modhash, $cookies;

  $type = preg_match('/\.(gif|png)$/i', $fileName) ? 'png' : 'jpg';

  if (!$name) {
    $parts = preg_split('/[.\/\\\]/', $fileName);
    array_pop($parts);
    $name = array_pop($parts);
  }

  $url = "$REDDIT/api/upload_sr_img";

  $request = new HttpRequest($url, HttpRequest::METH_POST);
  $request->addCookies($cookies);
  $request->addPostFields(array(
    'r' => $subreddit,
    'uh' => $modhash,
    'formid' => 'image-upload',
    'img_type' => $type,
    'name' => $name
  ));

  $fileName = realpath("./$fileName");
  $request->addPostFile('file', $fileName, 'image/png');

  $response = $request->send();

  $status = $response->getResponseCode();
  if ($status != 200) {
    die("/api/upload_sr_img failed, status=$status\n");
  }

  return $response;
}

// --------------------------------------
// Comments
// --------------------------------------

function reddit_comment($subreddit, $thing_id, $text) {
  $response = reddit_POST($subreddit, 'api/comment', array(
    'thing_id' => $thing_id,
    'text' => $text
  ));

  $json = json_decode($response->getBody());

  return $json->json->data->things[0]->data;
}

// --------------------------------------
// Wiki
// --------------------------------------

function reddit_wiki_edit($subreddit, $page, $markdown) {
  return reddit_POST($subreddit, 'api/wiki/edit', array(
    'page' => $page,
    'content' => $markdown
  ));
}

// --------------------------------------
// Flair
// --------------------------------------

function reddit_linkflair($subreddit, $link, $text, $css_class) {
  return reddit_POST($subreddit, 'api/flair', array(
    'link' => $link,
    'text' => $text,
    'css_class' => $css_class
  ));
}

function reddit_flair($subreddit, $user, $text, $css_class, $notifyUser = false) {
  $response = reddit_POST($subreddit, 'api/flair', array(
    'name' => $user,
    'text' => $text,
    'css_class' => $css_class
  ));

  #if ($notifyUser) {
  #  reddit_sendMessage($user, 'Message from soccerbot', "You have been assigned the crest for $text");
  #}

  return $response;
}

function reddit_flaircsv($subreddit, $csv, $notifyUser = false) {
  $response = reddit_POST($subreddit, "r/$subreddit/api/flaircsv", array(
    'flair_csv' => $csv
  ));

  #if ($notifyUser) {
  #  $csv = str_getcsv($csv);
  #  $requests = json_decode($response->getBody());
  #  if ($requests) {
  #    foreach ($requests as $request) {
  #      if ($request->ok) {
  #        reddit_sendMessage($user, 'Message from soccerbot', "You have been assigned the crest for $text");
  #      }
  #    }
  #  }
  #}

  return $response;
}

function reddit_flairlist($subreddit) {
  global $REDDIT, $modhash, $cookies;
  
  $url = "$REDDIT/r/$subreddit/api/flairlist.json?limit=1000";

  $request = new HttpRequest($url, HttpRequest::METH_GET);
  $request->addCookies($cookies);
  
  $result = array();
  $next = true;

  while ($next) {
    $count = 0;
    $status = 0;
    while ($status != 200 && $count < 3) {
      $response = $request->send();
      $status = $response->getResponseCode();
      $count++;
    }
    if ($status != 200) {
      die("reddit_flairlist failed, status=$status\n");
    }

    $json = json_decode($response->getBody());
    
    foreach ($json->users as $entry) {
      array_push($result, $entry);
    }
    
    $next = $json->next;
    if ($next) {
      $request->setUrl("$url&after=$next");
    }
  }
  
  return $result;
}

// --------------------------------------
// Mail
// --------------------------------------

function reddit_getUnreadMail() {
  $data = reddit_GET('message/unread/.json');

  return $data->children;
}

function reddit_clearUnreadMail() {
  reddit_GET('message/inbox');
}

function reddit_sendMessage($to, $subject, $message) {
  return reddit_POST('', 'api/compose', array(
    'to' => $to,
    'subject' => $subject,
    'text' => $message
  ));
}

// --------------------------------------
// Login
// --------------------------------------

function reddit_login() {
  global $REDDIT, $username, $password, $modhash, $cookies;

  $url = "$REDDIT/api/login/$username";

  $request = new HttpRequest($url, HttpRequest::METH_POST);
  $request->addPostFields(array(
    'api_type' => 'json',
    'user' => $username,
    'passwd' => $password
  ));
  $response = $request->send();

  $status = $response->getResponseCode();
  if ($status != 200) {
    die("Failed to login, status=$status\n");
  }

  $raw = json_decode($response->getBody());
  $data = $raw->json->data;

  $modhash = $data->modhash;
  $cookies = array('reddit_session' => $data->cookie);
}

// --------------------------------------
// HTTP
// --------------------------------------

function reddit_GET($path) {
  global $REDDIT, $cookies;

  $url = "$REDDIT/$path";

  $request  = new HttpRequest($url, HttpRequest::METH_GET);
  $request->addCookies($cookies);
  $response = $request->send();

  $status = $response->getResponseCode();
  if ($status != 200) {
    die("$path failed, status=$status\n");
  }

  $json = json_decode($response->getBody());

  return $json->data;
}

function reddit_POST($subreddit, $to, $data) {
  global $REDDIT, $modhash, $cookies;

  $data['uh'] = $modhash;
  $data['api_type'] = 'json';
  if ($subreddit) {
    $data['r'] = $subreddit;
  }

  $request = new HttpRequest("$REDDIT/$to.json", HttpRequest::METH_POST);
  $request->addCookies($cookies);
  $request->addPostFields($data);

  $response = $request->send();

  $status = $response->getResponseCode();
  if ($status != 200) {
    die("/$to failed, status=$status\n");
  }

  return $response;
}

?>
