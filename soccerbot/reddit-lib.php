<?php
  $r = null;
  $modhash = '';

  // --------------------------------------
  // Flair API
  // --------------------------------------

  function reddit_flair($subreddit, $user, $text, $css_class, $notifyUser = false) {
    global $r, $modhash;
    
    $r->setMethod(HttpRequest::METH_POST);
    $r->setUrl('http://www.reddit.com/api/flair');
    $r->setPostFields(array(
      'r' => $subreddit,
      'name' => $user,
      'text' => $text,
      'css_class' => $css_class,
      'uh' => $modhash
    ));

    $response = $r->send();
    $status = $response->getResponseCode();
    if ($status != 200) {
      die('reddit_flair failed, status='.$status);
    }
    
    if ($notifyUser) {
      $request = json_decode($response->getBody());
      reddit_sendMessage($user, 'Message from soccerbot', 'You have been assigned the crest for '.$text);
    }
  }

  function reddit_flaircsv($subreddit, $csv, $notifyUser = false) {
    global $r, $modhash;

    $r->setMethod(HttpRequest::METH_POST);
    $r->setUrl('http://www.reddit.com/r/'.$subreddit.'/api/flaircsv.json');
    $r->setPostFields(array(
      'flair_csv' => $csv,
      'uh' => $modhash
    ));

    $response = $r->send();
    
    $status = $response->getResponseCode();
    if ($status != 200) {
      die('reddit_flaircsv failed, status='.$status);
    }

    /*if ($notifyUser) {
      $csv = str_getcsv($csv);
      $requests = json_decode($response->getBody());
      if ($requests) {
        foreach ($requests as $request) {
          if ($request->ok) {
            //reddit_sendMessage($user, 'Message from soccerbot', 'You have been assigned the crest for '.$text);
          }
        }
      }
    }*/
  }

  function reddit_flairlist($subreddit) {
    global $r, $modhash;
    
    $url = 'http://www.reddit.com/r/'.$subreddit.'/api/flairlist.json?limit=1000';

    $r->setMethod(HttpRequest::METH_GET);
    $r->setUrl($url);
    
    $result = array();
    $next = true;

    while ($next) {
      $count = 0;
      $status = 0;
      while ($status != 200 && $count < 3) {
        $response = $r->send();
        $status = $response->getResponseCode();
        $count++;
      }
      if ($status != 200) {
        die('reddit_flairlist failed, status='.$status);
      }

      $json = json_decode($response->getBody());
      
      foreach ($json->users as $entry) {
        array_push($result, $entry);
      }
      
      $next = $json->next;
      if ($next) {
        $r->setUrl($url.'&after='.$next);
      }
    }
    
    return $result;
  }

  // --------------------------------------
  // Mail API
  // --------------------------------------

  function reddit_getUnreadMail() {
    global $r, $modhash;

    $r->setMethod(HttpRequest::METH_GET);
    $r->setUrl('http://www.reddit.com/message/unread/.json');

    $response = $r->send();
    $status = $response->getResponseCode();
    if ($status != 200) {
      die('Failed to fetch mail, status='.$status);
    }
    
    $json = json_decode($response->getBody());

    return $json->data->children;
  }

  function reddit_clearUnreadMail() {
    global $r;

    $r->setMethod(HttpRequest::METH_GET);
    $r->setUrl('http://www.reddit.com/message/inbox');
    $r->send();
  }

  function reddit_sendMessage($to, $subject, $message) {
    global $r, $modhash;

    $r->setMethod(HttpRequest::METH_POST);
    $r->setUrl('http://www.reddit.com/api/compose');
    $r->addPostFields(array(
      'to' => $to,
      'subject' => $subject,
      'text' => $message,
      'uh' => $modhash
    ));
    $r->send();
  }

  // --------------------------------------
  // Login
  // --------------------------------------
  
  function reddit_login($mod_user, $mod_password) {
    global $r, $modhash;
    
    $r = new HttpRequest('http://www.reddit.com/api/login/'.$mod_user, HttpRequest::METH_POST);
    
    $r->addPostFields(array(
      'api_type' => 'json',
      'user' => $mod_user,
      'passwd' => $mod_password
    ));

    $response = $r->send();
    $status = $response->getResponseCode();
    if ($status != 200) {
      die('Failed to login, status='.$status);
    }

    $userInfo = json_decode($response->getBody());
    $r->addCookies(array(
      'reddit_session' => $userInfo->json->data->cookie
    ));
    $modhash = $userInfo->json->data->modhash;
  }
?>
