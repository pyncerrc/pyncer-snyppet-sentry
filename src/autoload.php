<?php
use Pyncer\Snyppet\Sentry\Snyppet;
use Pyncer\Snyppet\SnyppetManager;

SnyppetManager::register(new Snyppet(
    'sentry',
    dirname(__DIR__),
    [
        'debug' => ['Initialize'],
        'access' => ['IdentifyUser'],
    ],
));
