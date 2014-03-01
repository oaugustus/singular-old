<?php
require_once __DIR__."/vendor/autoload.php";

$app = new Singular\Application(array(
    'base_dir' => __DIR__
));

$app->get('/', function(){
    return 'OK';
});

$app->run();