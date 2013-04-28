<?php
header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$ROOT = '../..';

require("$ROOT/config.php");
require("$ROOT/sprites.php");

$action = $_POST['action'];

$sprite = $_POST['sprite'];
if (!isset($sprite)) {
  $sprite = 4;
}

if (isset($action)) {
  if ($action == 'add-from-file') {
    $fileName = $_FILES['file']['name'];
    $flair = preg_replace('/_/', '-', substr($fileName, 0, -4));
    if ($sprite != 1) {
      $flair .= "-s$sprite";
    }
    $name = preg_replace_callback('/(^|\s)(\w)/i', create_function(
      '$matches', 'return $matches[1].strtoupper($matches[2]);'
    ), preg_replace('/_/', ' ', substr($fileName, 0, -4)));
  } else {
    $flair = $_POST['flair'];
    $name = $_POST['name'];
    $text = preg_replace('/\'/', "''", $name);
    $country = $_POST['country'];
    $site = $_POST['site'];
    $twitter = $_POST['twitter'];
    $wikipedia = $_POST['wikipedia'];
    $wikipedia_text = preg_replace('/\'/', "''", $wikipedia);
    $fileName = $_POST['fileName'];
    if (!isset($fileName)) {
      $fileName = preg_replace('/\-/', '_', preg_replace('/\-s\d$/', '', $flair));
    }
    if ($action == 'add') {
      $query = $db->query("SELECT COUNT(*) as count FROM teams WHERE flair='$flair'");
      $row = $query->fetch();
      if ($row['count'] > 0) {
        $action = 'modify';
      }
    }
    if ($action == 'add-user') {
      $user = $_POST['user'];
      $css_class = $flair;
      if ($sprite != 1) {
        $css_class .= " s$sprite";
      }
      $db->query("INSERT INTO uploads (user,text,css_class) VALUES ('$user', '$text', '$css_class')");
      $action = 'modify';
    } else {
      if ($action == 'add') {
        $db->query("INSERT INTO teams (flair,name,country,site,twitter,wikipedia,fileName) VALUES ('$flair', '$text', '$country', '$site', '$twitter', '$wikipedia_text', '$fileName')");
        build_sprite($sprite);
        $action = 'modify';
      } else if ($action == 'modify') {
        $db->query("UPDATE teams SET name='$text', country='$country', site='$site', twitter='$twitter', wikipedia='$wikipedia_text', fileName='$fileName' WHERE flair='$flair'");
      } else if ($action == 'delete') {
        $db->query("DELETE FROM teams WHERE flair='$flair'");
        build_sprite($sprite);
      }
    }
  }
} else {
  $flair = $_GET['flair'];
  if (isset($flair)) {
    $query = $db->query("SELECT * FROM teams WHERE flair='$flair'");
    if (is_object($query)) {
      $row = $query->fetch();
      $name = $row['name'];
      $country = $row['country'];
      $site = $row['site'];
      $twitter = $row['twitter'];
      $wikipedia = $row['wikipedia'];
      $fileName = $row['fileName'];
      $sprite = $row['sprite'];
    }
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

  <p>
   <label for="team-fileName">File name:</label>
   <input id="team-fileName" name="fileName" value="<?php echo($fileName); ?>">
  </p>

  <p>
   <label for="team-sprite">Sprite:</label>
   <input type="number" id="team-sprite" name="sprite" value="<?php echo($sprite); ?>">
  </p>
 </fieldset>

 <p>
<?php
if (isset($flair) && $action != 'delete' && $action != 'add-from-file') {
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
<?php
if ($action == 'modify') {
?>
 <h3>Add user</h3>

 <fieldset>
  <p>
   <label for="user-name">User:</label>
   <input id="user-name" name="user">
  </p>
 </fieldset>

 <p><button type="submit" name="action" value="add-user">Add</button></p>
<?php
}
?>
</form>

<p><a href="../">Home</a></p>
