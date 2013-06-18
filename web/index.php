<?php

require_once(__DIR__.'/../vendor/autoload.php');

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__.'/views',
	'twig.options' => array(
		'debug' => true
	)
));
$app['twig']->addExtension(new Twig_Extension_Debug());

$app->register(new Silex\Provider\MonologServiceProvider(), array(
	'monolog.logfile' => __DIR__.'/../log/development.log'
));

$app->run();