<?php
require('flair-lib.php');
require('link-lib.php');

header('Content-Type: text/plain');
header("User-Agent: soccerbot by /u/9jack9");

print(gmdate('c')."\n");

reddit_login();

link_bot();
alert_bot();
flair_bot();
spam_bot();
?>
