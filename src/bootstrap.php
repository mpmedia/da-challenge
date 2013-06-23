<?php

require_once(__DIR__.'/../vendor/autoload.php');

$app = new Silex\Application();

$app['debug'] = true;

$app->register(new Silex\Provider\FormServiceProvider());

$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.messages' => array(),
    ));

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

echo "Why is this echoed after every single request? Should this be like that?!?!";
$app->register(new DarwinAnalytics\Provider\ImapServiceProvider());

return $app;