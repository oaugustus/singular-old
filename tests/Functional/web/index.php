<?php
require_once(__DIR__."/../../bootstrap.php");

$app = new Singular\Application(array(
    'base_dir' => __DIR__."/..",
    'env' => 'dev'
));

$app->get('/', function() use ($app){
    echo "<pre>";
    print_r($app['routes']);
    echo "</pre>";
    die();
});

$app->run();