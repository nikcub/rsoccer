<?php
header('Content-Type: text/plain');

set_time_limit(3000);

include('reddit-lib.php');
include('flair-lib.php');
include('config.php');

reddit_login($username, $password);

print("name,text,team\n");
flair_list('soccer');

?>
