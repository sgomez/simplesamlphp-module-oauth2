<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$config = [
    'authCodeDuration' => 'PT10M', // 10 minutes
    'refreshTokenDuration' => 'P1M', // 1 month
    'accessTokenDuration' => 'PT1H', // 1 hour,

    // Tag to run storage cleanup script using the cron module...
    'cron_tag' => 'hourly',

    // auth is the idp to use for admin authentication,
    'auth' => 'default-sp',

    // You can create as many scopes as you want and assign attributes to them
    // TODO
    'scopes' => [
        'basic' => [
            'icon' => 'user',
            'description' => [
                'en' => 'Your username.',
                'es' => 'Su nombre de usuario.'
            ],
            'attributes' => ['uid'],
        ],
        'email' => [
            'icon' => 'mail',
            'description' => [
                'en' => 'Your email.',
                'es' => 'Su dirección de correo.'
            ],
            'attributes' => ['email'],
        ],
    ],
];