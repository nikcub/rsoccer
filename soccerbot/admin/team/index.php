<?php
header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$ROOT = '../..';

require("$ROOT/config.php");
require("$ROOT/sprites.php");

$id = $_GET['id'];

$action = $_POST['action'];

$sprite = $_POST['sprite'];
if (!isset($sprite)) {
  $sprite = 4;
}

$originalName = $_POST['original-name'];
$originalSprite = $_POST['original-sprite'];

if (isset($action)) {
  if ($action == 'add-from-file') {
    $fileName = $_FILES['file']['name'];
    $name = preg_replace_callback('/(^|\s)(\w)/i', create_function(
      '$matches', 'return $matches[1].strtoupper($matches[2]);'
    ), preg_replace('/_/', ' ', substr($fileName, 0, -4)));
  } else {
    $name = $_POST['name'];
    $text = preg_replace('/\'/', "''", $name);
    $country = $_POST['country'];
    $wikipedia = $_POST['wikipedia'];
    $wikipedia_text = preg_replace('/\'/', "''", $wikipedia);
    $fileName = $_POST['fileName'];
    $css_class = $id;
    if ($sprite != 1) {
      $css_class .= " s$sprite";
    }
    if ($action == 'add') {
      $query = $db->query("SELECT id, COUNT(*) AS count FROM teams WHERE fileName='$fileName' AND sprite=$sprite");
      $row = $query->fetch();
      if ($row['count'] > 0) {
        $id = $row['id'];
        $action = 'modify';
      }
    }
    if ($action == 'delete-source') {
      foreach ($_POST as $key=>$value) {
        if (preg_match('/^source\-/', $key)) {
          list($dummy, $teamId) = explode('-', $key);
          $db->query("DELETE FROM sources WHERE id=$teamId");
        }
      }
      $action = 'modify';
    } else if ($action == 'add-source') {
      $source = $_POST['source'];
      $sourceType = $_POST['source-type'];
      $db->query("INSERT INTO sources (team,type,source) VALUES ($id, '$sourceType', '$source')");
      $action = 'modify';
    } else if ($action == 'add-user') {
      $user = $_POST['user'];
      $db->query("INSERT INTO uploads (user,text,css_class) VALUES ('$user', '$text', '$css_class')");
      $action = 'modify';
    } else {
      if ($action == 'add') {
        $db->query("INSERT INTO teams (name,country,wikipedia,fileName,sprite) VALUES ('$text', '$country', '$wikipedia_text', '$fileName', $sprite)");
        build_sprite($sprite);
        $action = 'modify';
        $query = $db->query("SELECT id, COUNT(*) AS count FROM teams WHERE fileName='$fileName' AND sprite=$sprite");
        $row = $query->fetch();
        if ($row['count'] > 0) {
          $id = $row['id'];
        }
      } else if ($action == 'modify') {
        $db->query("UPDATE teams SET name='$text', country='$country', wikipedia='$wikipedia_text', fileName='$fileName', sprite=$sprite WHERE id=$id");
        if ($name != $originalName || $sprite != $originalSprite) {
          $db->query("INSERT INTO renames (team,new_name,css_class) VALUES ($id, '$name', '$css_class')");
        }
      } else if ($action == 'delete') {
        $db->query("DELETE FROM teams WHERE id=$id");
        build_sprite($sprite);
      }
    }
  }
} else {
  if (isset($id)) {
    $query = $db->query("SELECT * FROM teams WHERE id=$id");
    if (is_object($query)) {
      $row = $query->fetch();
      $name = $row['name'];
      $country = $row['country'];
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
<script>
function sprite_onchange(sprite) {
  var id = document.querySelector("#team-id");
  id.value = id.value.replace(/\-s\d+$/, '');
  if (sprite.value != 1) {
    id.value += "-s" + sprite.value;
  }
}
</script>

<h1>Soccerbot</h1>

<form action="<?php if (isset($id)) echo('?id='.$id); ?>" method="post">

 <h2>Team: <?php echo($name); ?></h2>

 <fieldset>
  <input type="hidden" name="original-name" value="<?php echo($name); ?>">
  <input type="hidden" name="original-sprite" value="<?php echo($sprite); ?>">

  <p>
   <label for="team-name">Name:</label>
   <input type="text" id="team-name" name="name" value="<?php echo($name); ?>">
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
   <label for="team-wikipedia">Wikipedia:</label>
   <input type="text" id="team-wikipedia" name="wikipedia" value="<?php echo($wikipedia); ?>">
  </p>

  <p>
   <label for="team-fileName">File name:</label>
   <input type="text" id="team-fileName" name="fileName" value="<?php echo($fileName); ?>">
  </p>

  <p>
   <label for="team-sprite">Sprite:</label>
   <input type="number" id="team-sprite" name="sprite" value="<?php echo($sprite); ?>" onchange="sprite_onchange(this)">
  </p>
 </fieldset>

 <p>
<?php
if (isset($id) && $action != 'delete' && $action != 'add-from-file') {
?>
  <button type="submit" name="action" value="modify">Modify</button>
  <button type="submit" name="action" value="delete">Delete</button>
  <a href="../team">Add another</a>
<?php
} else {
?>
  <button type="submit" name="action" value="add">Add</button>
<?php
}
?>
 </p>
<?php
if (isset($id) && $action != 'add' && $action != 'add-from-file') {
?>
 <h3>Add user</h3>

 <fieldset>
  <p>
   <label for="user-name">User:</label>
   <input type="text" id="user-name" name="user">
  </p>
 </fieldset>

 <p><button type="submit" name="action" value="add-user">Add</button></p>

 <h3>Sources</h3>

 <fieldset>
 <ul class="sources">
<?php
  $query = $db->query("SELECT * FROM sources WHERE team=$id ORDER BY type");

  while ($row = $query->fetch()) {
    $sourceType = $row['type'];
    $source = $row['source'];
    $id = $row['id'];
    $sourceId = "source-$id";
?>
   <li><input type="checkbox" name="<?php print($sourceId); ?>" id="<?php print($sourceId); ?>">
       <label for="<?php print($sourceId); ?>"><?php print("$sourceType: $source"); ?></label></li>
<?php
  }
?>
 </ul>
 </fieldset>

 <p><button type="submit" name="action" value="delete-source">Delete</button></p>

 <h4>Add source</h4>

 <fieldset>
  <p>
   <label for="source">Source:</label>
   <input type="text" id="source" name="source">
  </p>
  <p>
   <label for="source-type">Type:</label>
   <select id="source-type" name="source-type">
    <option>Facebook
    <option>Twitter
    <option selected>Web
   </select>
  </p>
 </fieldset>

 <p><button type="submit" name="action" value="add-source">Add</button></p>
<?php
}
?>
</form>

<p><a href="../">Home</a></p>
