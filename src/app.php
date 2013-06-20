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
            // setup the Mailr and share it for the rest of the application

            $app['mailr'] = $app->share( function() use($form) {
                return new DarwinAnalytics\Mailr('{imap.gmail.com:993/imap/ssl}','INBOX',$form->getData()['email'],$form->getData()['password']);
            });

            // if all went okay redirect to /messages
            $request = Symfony\Component\HttpFoundation\Request::create('/messages', 'GET');
            return print_r($app['mailr']->getMailHeaders());
            /*
            $app['monolog']->addDebug('Fetching mails for '.$form->getData()['email'].'.');
            
            if($inbox = imap_open('{imap.gmail.com:993/imap/ssl}INBOX',$form->getData()['email'],$form->getData()['password'])) {

                $all = imap_search($inbox, 'ALL');
                $messages = array();

                if($all) {
                    foreach($all as $messageNumber) {
                        $overview = imap_fetch_overview($all, $messageNumber, 0);
                        $messages[$messageNumber]->body = imap_fetchstructure($inbox, $messageNumber);
                        $messages[$messageNumber]->subject = $overview[0]->subject;
                    }
                }

                $request = Symfony\Component\HttpFoundation\Request::create('/messages', 'GET', array(
                    'messages' => $messages
                    ));

                return $app->handle($request); 
            } else {
                throw new Exception(imap_last_error().'.', 1);
            }*/
        }
    }
    return $app['twig']->render('index.html.twig', array('form' => $form->createView()));
});

$app->get('/messages', function(Symfony\Component\HttpFoundation\Request $request) use ($app) {
    return $app['twig']->render('messages.html.twig', array(
        //'messages' => $request->get('messages')
        ));
});

$app->error(function (\Exception $e, $code) use ($app) {
    $app['monolog']->addDebug('Error with code '.$code);
    switch ($code) {
        case 404:
        $message = 'The requested page could not be found.';
        break;
        default:
        $message = 'We are sorry, but something went terribly wrong.';
    }
    return new Symfony\Component\HttpFoundation\Response($app['twig']->render('404.html.twig', array( 'content' => $e->getMessage())), $code);
});

return $app;
