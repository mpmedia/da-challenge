<?php

namespace DarwinAnalytics\Provider;

class ImapServiceProvider implements \Silex\ServiceProviderInterface {

    public function register(\Silex\Application $app)
    {
        $app['imap'] = $app->share(function () use ($app) {
            return new \DarwinAnalytics\Imap();
        });
    }

    public function boot(\Silex\Application $app)
    {
    }
}