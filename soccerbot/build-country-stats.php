<?php
header('Content-Type: text/plain');

set_time_limit(60);

$db = new PDO('sqlite:crests.db');

printTable('##South Korea', 1, array('kor'));

function printTable($heading, $limit, $countries = null) {
  global $db;
  
  $sql = "SELECT teams.name AS team,COUNT(users.team) AS count FROM users LEFT JOIN teams WHERE users.team=teams.flair ";
  if ($countries) {
    $clause = array();
    foreach ($countries as $country) {
      array_push($clause, "teams.country='".$country."'");
    }
    $sql .= 'AND ('.implode(' OR ', $clause).')';
  }
  $sql .= " GROUP BY team ORDER BY count DESC";

  $query = $db->query($sql);
  print("\n".$heading."\n");
  print("\nTeam||\n");
  print(":---|---:\n");
  while (($row = $query->fetch()) && $row['count'] >= $limit) {
    print($row['team'].'|'.$row['count']."\n");
  }
}

?>
