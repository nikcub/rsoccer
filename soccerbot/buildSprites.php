<?php
header('Content-Type: text/plain');
header("User-Agent: soccerbot by /u/9jack9");

require('admin-lib.php');
require('sprites.php');

@ini_set('zlib.output_compression', 0);

@ini_set('implicit_flush', 1);

@ob_end_clean();

set_time_limit(0);

ob_implicit_flush(1);

build_sprite(1);
build_sprite(2);
build_sprite(3);
build_sprite(4);
?>
