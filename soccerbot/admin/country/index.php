<?php
require('../../config.php');

header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$action = $_POST['action'];
$code = $_POST['code'];

if (isset($action)) {
  $name = $_POST['name'];
  $region = $_POST['region'];
  if ($action == 'add') {
    $db->query("INSERT INTO countries (code,name,region) VALUES ('$code','$name','$region')");
  } else if ($action == 'modify') {
    $db->query("UPDATE countries SET name='$name', region='$region' WHERE code='$code'");
  } else if ($action == 'delete') {
    $db->query("DELETE FROM countries WHERE code='$code'");
    $code = '';
    $name = '';
    $region = '';
  }
} else if (isset($code)) {
  $query = $db->query("SELECT * FROM countries WHERE code='$code'");
  if (is_object($query)) {
    $row = $query->fetch();
    $name = $row['name'];
    $region = $row['region'];
  }
}
?>
<!doctype html>
<title>soccerbot</title>
<meta charset="utf-8">
<meta name="author" content="9jack9">
<link rel="stylesheet" href="../style.css">

<h1>Soccerbot</h1>

<form action="" method="post" enctype="multipart/form-data">

 <h2>Country: <?php echo($name); ?></h2>

 <fieldset>
  <p>
   <label for="country-code">Code:</label>
   <input id="country-code" name="code" size="3" value="<?php echo($code); ?>">
  </p>

  <p>
   <label for="country-name">Name:</label>
   <input id="country-name" name="name" value="<?php echo($name); ?>">
  </p>

  <p>
   <label for="country-region">Region:</label>
   <select id="country-region" name="region">
<?php
  $query = $db->query('SELECT * FROM regions ORDER BY name');

  while ($row = $query->fetch()) {
    $selected = $region == $row['code'] ? ' selected' : '';
    print('    <option value="'.$row['code'].'"'.$selected.'>'.htmlspecialchars($row['name'])."</option>\n");
  }

?>
   </select>
  </p>
 </fieldset>

 <p>
<?php
if (isset($code) && $action != 'delete') {
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
