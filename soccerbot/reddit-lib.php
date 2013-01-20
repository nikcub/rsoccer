<?php
  require('config.php');

  $request = null;
  $modhash = '';

  // --------------------------------------
  // Listings
  // --------------------------------------

  function reddit_new() {
    return reddit_listing('new');
  }

  function reddit_reported() {
    return reddit_listing('about/reports');
  }

  function reddit_spam() {
    return reddit_listing('about/spam');
  }

  function reddit_listing($list, $limit = 25, $sort = 'new', $after = '', $before = '') {
    global $REDDIT, $subreddit, $request, $modhash;

    $url = "$REDDIT/r/$subreddit/$list.json?limit=$limit&sort=$sort&after=$after&before=$before";

    $request->setMethod(HttpRequest::METH_GET);
    $request->setUrl($url);

    $response = $request->send();

    $status = $response->getResponseCode();
    if ($status != 200) {
      die("/r/$subreddit/$list failed, status=$status\n");
    }

    $json = json_decode($response->getBody());

    return $json->data->children;
  }

  // --------------------------------------
  // Moderation
  // --------------------------------------

  function reddit_remove($id, $spam = false) {
    reddit_POST('api/remove', array(
      'id' => $id,
      'spam' => $spam ? 'on' : 'off'
    ));
  }

  function reddit_approve($id) {
    reddit_POST('api/approve', array(
      'id' => $id
    ));
  }

  function reddit_distinguish($id) {
    reddit_POST('api/distinguish', array(
      'how' => 'yes',
      'id' => $id
    ));
  }

  // --------------------------------------
  // Comments
  // --------------------------------------

  function reddit_comment($thing_id, $text) {
    $response = reddit_POST('api/comment', array(
      'thing_id' => $thing_id,
      'text' => $text
    ));

    $json = json_decode($response->getBody());

    return $json->json->data->things[0]->data;
  }

  // --------------------------------------
  // Flair
  // --------------------------------------

  function reddit_linkflair($link, $text, $css_class) {
    global $subreddit;

    reddit_POST('api/flair', array(
      'r' => $subreddit,
      'link' => $link,
      'text' => $text,
      'css_class' => $css_class
    ));
  }

  function reddit_flair($user, $text, $css_class, $notifyUser = false) {
    global $subreddit;

    reddit_POST('api/flair', array(
      'r' => $subreddit,
      'name' => $user,
      'text' => $text,
      'css_class' => $css_class
    ));

    #if ($notifyUser) {
    #  reddit_sendMessage($user, 'Message from soccerbot', "You have been assigned the crest for $text");
    #}
  }

  function reddit_flaircsv($csv, $notifyUser = false) {
    global $subreddit;

    $response = reddit_POST("r/$subreddit/api/flaircsv", array(
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
  }

  function reddit_flairlist() {
    global $REDDIT, $subreddit, $request, $modhash;
    
    $url = "$REDDIT/r/$subreddit/api/flairlist.json?limit=1000";

    $request->setMethod(HttpRequest::METH_GET);
    $request->setUrl($url);
    
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
    global $REDDIT, $request;

    $request->setMethod(HttpRequest::METH_GET);
    $request->setUrl("$REDDIT/message/unread/.json");

    $response = $request->send();

    $status = $response->getResponseCode();
    if ($status != 200) {
      die("Failed to fetch mail, status=$status\n");
    }
    
    $json = json_decode($response->getBody());

    return $json->data->children;
  }

  function reddit_clearUnreadMail() {
    global $REDDIT, $request;

    $request->setMethod(HttpRequest::METH_GET);
    $request->setUrl("$REDDIT/message/inbox");
    $request->send();
  }

  function reddit_sendMessage($to, $subject, $message) {
    reddit_POST('api/compose', array(
      'to' => $to,
      'subject' => $subject,
      'text' => $message
    ));
  }

  // --------------------------------------
  // Login
  // --------------------------------------

  function reddit_login() {
    global $REDDIT, $username, $password, $request, $modhash;

    $request = new HttpRequest("$REDDIT/api/login/$username", HttpRequest::METH_POST);

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

    $userInfo = json_decode($response->getBody());
    $request->addCookies(array(
      'reddit_session' => $userInfo->json->data->cookie
    ));

    $modhash = $userInfo->json->data->modhash;
  }

  // --------------------------------------
  // POST
  // --------------------------------------

  function reddit_POST($to, $data) {
    global $REDDIT, $request, $modhash;

    $data['uh'] = $modhash;
    $data['api_type'] = 'json';

    $request->setMethod(HttpRequest::METH_POST);
    $request->setUrl("$REDDIT/$to.json");
    $request->addPostFields($data);

    $response = $request->send();

    $status = $response->getResponseCode();
    if ($status != 200) {
      die("/$to failed, status=$status\n");
    }

    return $response;
  }

?>
