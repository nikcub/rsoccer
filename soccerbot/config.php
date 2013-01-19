<?php
$REDDIT = 'http://www.reddit.com';

$subreddit = 'soccer';
$username  = 'soccerbot';
$password  = '';
$dbname    = 'crests.db';

$db = new PDO('sqlite:'.dirname(__FILE__).'\\'.$dbname);
?>
