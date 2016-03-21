<?php

/* php configuration*/
error_reporting(-1);
ini_set("display_errors", 1);

/*Load autoloader*/
require __dir__.'/autoloader.php';

$api = new code\api();
$api->run($_GET["_path"]);
$api->display();