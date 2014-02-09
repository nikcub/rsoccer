<?php
require('../config.php');
?>
<!doctype html>
<title>soccerbot</title>
<meta charset="utf-8">
<meta name="author" content="9jack9">
<link rel="stylesheet" href="style.css">

<h1>Soccerbot</h1>

<form action="" method="post" enctype="multipart/form-data">
 <p>
  <input type="search" name="q" id="q">
  <button type="submit" name="action" value="search">Search</button>
 </p>
</form>

<?php
  $action = $_POST['action'];
  $q = $_POST['q'];

  if ($action == 'search') {
    $query = $db->query("SELECT * FROM teams WHERE (name LIKE '%$q%' OR country='$q') ORDER BY name");
    while ($row = $query->fetch()) {
      print('<div><a href="./team?id='.$row['id'].'">'.htmlspecialchars($row['name'])."</a></div>\n");
    }
  } else {
?>
<form action="./team/" method="post" enctype="multipart/form-data">
 <h2>Add a new team</h2>

 <fieldset>
  <p>
   <label for="team-name">Name:</label>
   <input type="text" id="team-name" name="name">
  </p>

  <p>
   <label for="team-country">Country:</label>
   <select id="team-country" name="country">
<?php
  $query = $db->query('SELECT * FROM countries ORDER BY name');

  while ($row = $query->fetch()) {
    print('    <option value="'.$row['code'].'">'.htmlspecialchars($row['name'])."</option>\n");
  }

?>
   </select>
  </p>
 </fieldset>

 <p><button type="submit" name="action" value="add">Add</button></p>

 <p>
  <input type="file" name="file" onchange="this.nextElementSibling.disabled=false">
  <button type="submit" name="action" value="add-from-file" disabled>Add</button>
 </p>
</form>

<form action="./country/" method="post" enctype="multipart/form-data">
 <h2>Add a new country</h2>

 <fieldset>
  <p>
   <label for="country-code">Code:</label>
   <input type="text" id="country-code" name="code" size="3">
  </p>

  <p>
   <label for="country-name">Name:</label>
   <input type="text" id="country-name" name="name">
  </p>

  <p>
   <label for="country-region">Region:</label>
   <select id="country-region" name="region">
<?php
  $query = $db->query('SELECT * FROM regions ORDER BY name');

  while ($row = $query->fetch()) {
    print('    <option value="'.$row['code'].'">'.htmlspecialchars($row['name'])."</option>\n");
  }
?>
   </select>
  </p>
 </fieldset>

 <p><button type="submit" name="action" value="add">Add</button></p>
</form>
<?php } ?>
