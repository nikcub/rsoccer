<?php
header('Content-Type: text/plain');

set_time_limit(120);

$db = new PDO('sqlite:crests.db');

$users = sqlCount("SELECT COUNT(*) AS count FROM users");
$teams = sqlCount("SELECT COUNT(DISTINCT teams.flair) AS count FROM users LEFT JOIN teams WHERE users.team=teams.flair");
$countries = sqlCount("SELECT COUNT(DISTINCT teams.country) AS count FROM users LEFT JOIN teams WHERE users.team=teams.flair");

print('LAST UPDATED: '.date('Y-m-d')."\n\n");
print("Stats from [soccerbot](/r/soccerbot).\n");
print("\n*There are currently $users users supporting $teams teams from $countries countries.*\n");
printTable('#Top Teams', 20);
print("\n#Biggest Leagues\n");
printTable('##England', 10, array('eng', 'wal'));
printTable('##USA', 8, array('usa', 'can'));
printTable('##Spain', 1, array('esp'));
printTable('##Italy', 1, array('ita'));
printTable('##Germany', 1, array('deu'));
printTable('##Mexico', 1, array('mex'));
printTable('##Holland', 1, array('ned'));
printTable('##France', 1, array('fra'));

/*
$query = $db->query('SELECT country, COUNT(user) AS count FROM (SELECT countries.name AS country, teams.flair AS flair1 FROM teams LEFT JOIN countries ON teams.country=countries.code) LEFT JOIN (SELECT users.name AS user, teams.name AS team, teams.flair AS flair2 FROM teams LEFT JOIN users ON teams.flair=users.team) ON flair1=flair2 GROUP BY country ORDER BY count DESC');
print("\n#By Country\n");
print("\nCountry||\n");
print(":---|---:\n");
while (($row = $query->fetch()) && $row['count'] >= 1) {
  print($row['country'].'|'.$row['count']."\n");
}
*/

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

function sqlCount($sql) {
  global $db;
  
  $query = $db->query($sql);
  $row = $query->fetch();
  return $row['count'];
}

?>
