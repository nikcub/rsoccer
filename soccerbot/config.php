<?php
$REDDIT = 'http://www.reddit.com';
$ROOT = dirname(__FILE__);

$SPRITES = 4; // Number of sprites
$SPRITES_DIR = "$ROOT\\sprites";

$username      = 'soccerbot';
$password      = '';

$subreddit     = 'soccer';
$bot_subreddit = 'soccerbot';

$dbname        = 'crests.db';

$bot_index = array(
  't3_170gqp' => "region='afr'",
  't3_170gpm' => "region='aus'",
  't3_170god' => "region='asia' AND REGEXP('/^[A-M]/', countries.name)",
  't3_170gnb' => "region='asia' AND REGEXP('/^[N-Z]/', countries.name)",
  't3_170glm' => "region='eng'",
  't3_170gkl' => "region='eur'  AND REGEXP('/^[A-F]/', countries.name)",
  't3_170gje' => "region='eur'  AND REGEXP('/^[G-H]/', countries.name)",
  't3_170gh8' => "region='eur'  AND REGEXP('/^[I-M]/', countries.name)",
  't3_170geu' => "region='eur'  AND REGEXP('/^[N-R]/', countries.name)",
  't3_170gdq' => "region='eur'  AND REGEXP('/^[S-Z]/', countries.name)",
  't3_170gac' => "region='nam'",
  't3_170g8u' => "region='sam'"
);

$stats_id = 't3_17de4z'; // thing_id of stats post (optional)

$db = new PDO("sqlite:$ROOT\\$dbname");
?>
