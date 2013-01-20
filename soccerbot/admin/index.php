<?php
  require('../config.php');
?>
<!doctype html>
<title>soccerbot</title>
<meta charset="utf-8">
<meta name="author" content="9jack9">
<link rel="stylesheet" href="style.css">

<h1>Soccerbot</h1>

<form action="./country/" method="post">
 <h2>Add a new country</h2>

 <fieldset>
  <p>
   <label for="country-code">Code:</label>
   <input id="country-code" name="code" size="3">
  </p>

  <p>
   <label for="country-name">Name:</label>
   <input id="country-name" name="name">
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

<form action="./team/" method="post">
 <h2>Add a new team</h2>

 <fieldset>
  <p>
   <label for="team-flair">Flair:</label>
   <input id="team-flair" name="flair">
  </p>

  <p>
   <label for="team-name">Name:</label>
   <input id="team-name" name="name">
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
</form>

<form action="./special/" method="post">
 <h2>Special tasks</h2>

 <p>
  <input type="checkbox" id="admin-reset-new" name="reset-new">
  <label for="admin-reset-new">Reset the <em>new</em> queue</label>
 </p>

 <p>
  <input type="checkbox" id="admin-remove-alerts" name="remove-alerts">
  <label for="admin-remove-alerts">Remove alerts</label>

  <label for="admin-alerts-age">older than</label>
  <select id="admin-alerts-age" name="alerts-age">
   <option value="7">7 days</option>
   <option value="14">14 days</option>
   <option value="30">30 days</option>
   <option value="all">all</option>
  </select>
 </p>

 <p><button type="submit">Submit</button></p>
</form>
