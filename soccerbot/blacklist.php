<?php

$blacklist = array(
  'quickmeme.com' => 'meme',
  'm.quickmeme.com' => 'meme',
  'memegenerator.net' => 'meme',
  'cdn.memegenerator.net' => 'meme',
  'qkme.me' => 'meme',
  'i.qkme.me' => 'meme',

  'thefirstrow.eu' => 'stream',
  'wiziwig.tv' => 'stream'
);

$guidlines = "Read our [submission guidelines](/r/soccer/faq#SubmissionTipsandGuidelines) for further information.";

$blacklist_reasons = array(
  'meme'   => "We do not allow memes as front page posts in /r/soccer. ".
              "You are free to post memes in the comment threads however. ".
              $guidlines,

  'stream' => "We do not allow links to streaming sites on the front page of /r/soccer. ".
              "Instead create a [match thread](/r/soccer/faq#MatchThreadCreationGuide) and link to streams in the comments. ".
              $guidlines
);

?>
