<?php
header('Content-Type: text/plain');

$db = new PDO('sqlite:crests.db');

$query = $db->query('SELECT teams.flair AS flair,teams.name AS name,teams.users AS count,countries.name AS country,countries.region AS region FROM teams LEFT JOIN countries WHERE teams.country=countries.code ORDER BY countries.region,countries.name,teams.sort');

$country = '';
$country = '';
while ($row = $query->fetch()) {
  if ($country != $row['country']) {
    print("\n");
    $country = $row['country'];
    print('#'.$country."\n");
  }
  print('* ['.$row['name'].'](/message/compose/?to=soccerbot&subject=crest&message='.$row['flair'].') *\\('.$row['count']."\\)*\n");
}

?>
