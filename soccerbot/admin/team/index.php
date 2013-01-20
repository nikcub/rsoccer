<?php
  require('../../config.php');

  header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
  header('Cache-Control: no-cache, must-revalidate, max-age=0');
  header('Pragma: no-cache');

  $action = $_POST['action'];
  $flair = $_POST['flair'];

  if (isset($action)) {
    $name = $_POST['name'];
    $country = $_POST['country'];
    if ($action == 'add') {
      $db->query("INSERT INTO teams (flair,name,country) VALUES ('$flair','$name','$country')");
    } else if ($action == 'modify') {
      $site = $_POST['site'];
      $twitter = $_POST['twitter'];
      $wikipedia = $_POST['wikipedia'];
      $db->query("UPDATE teams SET name='$name', country='$country', site='$site', twitter='$twitter', wikipedia='$wikipedia' WHERE flair='$flair'");
    } else if ($action == 'delete') {
      $db->query("DELETE FROM teams WHERE flair='$flair'");
      $flair = '';
      $name = '';
      $country = '';
    }
  } else if (isset($flair)) {
    $query = $db->query("SELECT * FROM teams WHERE flair='$flair'");
    if (is_object($query)) {
      $row = $query->fetch();
      $name = $row['name'];
      $country = $row['country'];
      $site = $row['site'];
      $twitter = $row['twitter'];
      $wikipedia = $row['wikipedia'];
    }
  }
?>
<!doctype html>
<title>soccerbot</title>
<meta charset="utf-8">
<meta name="author" content="9jack9">
<link rel="stylesheet" href="../style.css">

<h1>Soccerbot</h1>

<form action="" method="post">

 <h2>Team: <?php echo($name); ?></h2>

 <fieldset>
  <p>
   <label for="team-flair">Flair:</label>
   <input id="team-flair" name="flair" value="<?php echo($flair); ?>">
  </p>

  <p>
   <label for="team-name">Name:</label>
   <input id="team-name" name="name" value="<?php echo($name); ?>">
  </p>

  <p>
   <label for="team-country">Country:</label>
   <select id="team-country" name="country">
<?php
  $query = $db->query('SELECT * FROM countries ORDER BY name');

  while ($row = $query->fetch()) {
    $selected = $country == $row['code'] ? ' selected' : '';
    print('    <option value="'.$row['code'].'"'.$selected.'>'.htmlspecialchars($row['name'])."</option>\n");
  }

?>
   </select>
  </p>

  <p>
   <label for="team-site">Website:</label>
   <input id="team-site" name="site" value="<?php echo($site); ?>">
  </p>

  <p>
   <label for="team-twitter">Twitter:</label>
   <input id="team-twitter" name="twitter" value="<?php echo($twitter); ?>">
  </p>

  <p>
   <label for="team-wikipedia">Wikipedia:</label>
   <input id="team-wikipedia" name="wikipedia" value="<?php echo($wikipedia); ?>">
  </p>
 </fieldset>

 <p>
<?php
if (isset($flair) && $action != 'delete') {
?>
  <button type="submit" name="action" value="modify">Modify</button>
  <button type="submit" name="action" value="delete">Delete</button>
  <a href="">Add another</a>
<?php
} else {
?>
  <button type="submit" name="action" value="add">Add</button>
<?php
}
?>
 </p>
</form>

<p><a href="../">Home</a></p>
