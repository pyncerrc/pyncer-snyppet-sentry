<?php
use Pyncer\Snyppet\Sentry\SentrySnyppet;
use Pyncer\Snyppet\SnyppetManager;

SnyppetManager::register(new SentrySnyppet(
    'sentry',
    dirname(__DIR__),
    [
        'debug' => ['Initialize'],
        'access' => ['IdentifyUser'],
    ],
));
