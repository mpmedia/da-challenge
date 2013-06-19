<?php

$app = require __DIR__.'/bootstrap.php';

$app->match('/', function (Symfony\Component\HttpFoundation\Request $request) use ($app) {
	$form = $app['form.factory']->createBuilder('form')
	->add('email', 'text', array(
		'constraints' => array(new Symfony\Component\Validator\Constraints\Email())
		))
	->add('password', 'password', array(
		'constraints' => array( new Symfony\Component\Validator\Constraints\NotBlank())
		))
	->getForm();

	if ('POST' == $request->getMethod()) {
		$form->bind($request);

		if ($form->isValid()) {

			$app['monolog']->addDebug('Fetching mails for '.$form->getData()['email'].'.');

			$fetch = new Fetch\Server('imap.gmail.com', 993);
			$fetch->setAuthentication($form->getData()['email'], $form->getData()['password']);

			$request = Symfony\Component\HttpFoundation\Request::create('/messages', 'GET', array(
				'messages' => $fetch->getMessages()
				));

           return $app->handle($request);
       }
   }
   return $app['twig']->render('index.twig', array('form' => $form->createView()));
});

$app->get('/messages', function(Symfony\Component\HttpFoundation\Request $request) use ($app) {
	return $app['twig']->render('messages.twig', array('messages' => $request->get('messages')));
});

$app->error(function (\Exception $e, $code) {
	switch ($code) {
		case 404:
		$message = 'The requested page could not be found.';
		break;
		default:
		$message = 'We are sorry, but something went terribly wrong.';
	}
	return new Symfony\Component\HttpFoundation\Response($message);
});

return $app;