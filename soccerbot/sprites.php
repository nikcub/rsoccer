<?php

function build_sprite($sprite) {
  global $db, $SPRITES_DIR;

  if (!class_exists("Imagick"))  {
    return;
  }

  $BGCOLOR = '#FDFEFD';
  $WIDTH  = $sprite == 3 ? 40 : 20;
  $HEIGHT = 20;

  $query = $db->query("SELECT COUNT(*) AS count FROM teams WHERE sprite=$sprite");
  $row = $query->fetch();
  $count = $row['count'] + 1;

  $spriteWidth  = $WIDTH;
  $spriteHeight = ($count * ($HEIGHT + 1)) - 1;

  $DIR = "$SPRITES_DIR/sprite$sprite/";

  $s = new Imagick();
  $s->newImage($spriteWidth, $spriteHeight, new ImagickPixel($BGCOLOR), 'gif');
  $s->paintTransparentImage(new ImagickPixel($BGCOLOR), 0.0, 0);

  // Create the default (empty) crest.
  addCrest($s, $DIR.'_unknown.gif', 0);

  $offset = $HEIGHT + 1;
  $query = $db->query("SELECT * FROM teams WHERE sprite=$sprite");

  while ($row = $query->fetch()) {
    addCrest($s, $DIR.$row['fileName'], $offset);
    $offset += $HEIGHT + 1;
  }

  $s->writeImage("$SPRITES_DIR/s$sprite.gif");
  $s->destroy();
}

function addCrest($s, $fileName, $offset) {
  print("$fileName\n");
  $slot = new Imagick();
  $slot->readImage($fileName);
  $s->compositeImage($slot, $slot->getImageCompose(), 0, $offset);
  $slot->destroy();
}
?>
