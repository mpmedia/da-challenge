<?php

require_once(__DIR__.'/../vendor/autoload.php');

$app = new Silex\Application();

$app['debug'] = true;

$app->register(new Silex\Provider\MonologServiceProvider(), array(
	'monolog.logfile' => __DIR__.'/../log/development.log'
	));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__.'/../web/views',
	'twig.options' => array(
		'debug' => true
		)
	));
$app['twig']->addExtension(new Twig_Extension_Debug());

return $app;