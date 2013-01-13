<?php

$guidelines = "Read our [submission guidelines](/r/soccer/faq#SubmissionTipsandGuidelines) for further information.";

$blacklist = array(
  'quickmeme.com' => 'meme',
  'memegenerator.net' => 'meme',
  'qkme.me' => 'meme',
  'troll.me' => 'meme',

  'thefirstrow.eu' => 'stream',
  'wiziwig.tv' => 'stream',
  'livefootballol.tv' => 'stream',
  'livetv.ru' => 'stream',
  'nutjob.eu' => 'stream'
);

$blacklist_reasons = array(
  'meme'   => "We do not allow memes on the front page of /r/soccer. ".
              "You are free to post memes in the comment threads however.",

  'stream' => "We do not allow links to streaming sites on the front page of /r/soccer. ".
              "Instead create a [match thread](/r/soccer/faq#MatchThreadCreationGuide) and link to streams in the comments."
);

function getBlacklistExplanation($subdomain) {
  global $blacklist, $blacklist_reasons, $guidelines;

  foreach ($blacklist as $domain => $reason) {
    if (substr($subdomain, -strlen($domain)) === $domain) {
      return $blacklist_reasons[$reason].' '.$guidelines;
    }
  }
  return FALSE;
}

?>
