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

            // use the service provider for imap injection
            $app->register(new DarwinAnalytics\Provider\ImapServiceProvider());

            // open up the imap stream
            $app['imap']->open('{imap.gmail.com:993/imap/ssl}', $form->getData()['email'], $form->getData()['password']);

            $request = Symfony\Component\HttpFoundation\Request::create('/mails', 'GET');

            return $app->handle($request);
        }
    }
    return $app['twig']->render('index.html.twig', array('form' => $form->createView()));
});

$app->get('/mails', function(Symfony\Component\HttpFoundation\Request $request) use ($app) {
    return $app['twig']->render('mails.html.twig', array(
        'mails' => $app['imap']->getMailHeaders()
        ));
});

/*$app->get('/mail/{uid}', function($uid) use ($app) {
    return $app['twig']->render('mail.html.twig', array(
        'mail' => 'TEST_OBJECT'
        ));
});*/

$app->error(function (\Exception $e, $code) use ($app) {
    return new Symfony\Component\HttpFoundation\Response($app['twig']->render('404.html.twig', array( 'content' => $e->getMessage())), $code);
});

return $app;
