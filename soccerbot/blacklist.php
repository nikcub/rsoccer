<?php

$guidelines = "Read our [submission guidelines](/r/soccer/faq#SubmissionTipsandGuidelines) for further information.";

$blacklist = array(
  /* Memes */
  'quickmeme.com' => 'meme',
  'memegenerator.net' => 'meme',
  'qkme.me' => 'meme',
  'troll.me' => 'meme',

  /* Streams */
  'thefirstrow.eu' => 'stream',
  'wiziwig.tv' => 'stream',
  'livefootballol.tv' => 'stream',
  'livetv.ru' => 'stream',
  'nutjob.eu' => 'stream'
);

$url_shorteners = array(
  'bit.ly' => 'short',
  'goo.gl' => 'short',
  'su.pr' => 'short',
  'ow.ly' => 'short',
  'is.gd' => 'short',
  'tinyurl.com' => 'short',
  'cli.gs' => 'short',
  'ff.im' => 'short',
  'wp.me' => 'short',
  'ff.im' => 'short',
  'tiny.cc' => 'short'
);

$blacklist_reasons = array(
  'meme'   => "We do not allow memes on the front page of /r/soccer. ".
              "You are free to post memes in the comment threads however.",

  'stream' => "We do not allow links to streaming sites on the front page of /r/soccer. Instead create ".
              "a [match thread](/r/soccer/faq#MatchThreadCreationGuide) and link to streams in the comments.",

  'short'  => "Please don't use [URL shorteners](http://en.wikipedia.org/wiki/URL_shortening) in /r/soccer ".
              "as our users like to know what they are clicking on. Please use the full URL instead."
);

function getBlacklistExplanation($subdomain) {
  global $blacklist, $blacklist_reasons, $guidelines;

  $reason = $url_shorteners[$subdomain];

  if ($reason) {
    return $blacklist_reasons[$reason].' '.$guidelines;
  }

  foreach ($blacklist as $domain => $reason) {
    if (substr($subdomain, -strlen($domain)) === $domain) {
      return $blacklist_reasons[$reason].' '.$guidelines;
    }
  }

  return FALSE;
}

?>
