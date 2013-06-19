<?php

$app = require __DIR__.'/bootstrap.php';

$app->get('/', function() use ($app){
	return 'Hello world!';
});

return $app;