<?php
set_time_limit(10);
//error_reporting(-1);
//ini_set("display_errors", 1);

require_once __DIR__ . '/standalone_autoload.php';
require_once __DIR__ . '/helper.php';

$settings = \Linfo\Common::getVarFromFile(__DIR__.'/config.inc.php', 'settings');

$app = new app($settings);
if($app->check($_REQUEST["hash"])){
    $app->run();
}

$app->output();



