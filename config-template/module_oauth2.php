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
    // The private key passphrase (optional)
    // 'pass_phrase' => 'secret',

    // Tokens TTL
    'authCodeDuration' => 'PT10M', // 10 minutes
    'refreshTokenDuration' => 'P1M', // 1 month
    'accessTokenDuration' => 'PT1H', // 1 hour,

    // Tag to run storage cleanup script using the cron module...
    'cron_tag' => 'hourly',

    // this is the auth source used for authentication,
    'auth' => 'default-sp',
    // useridattr is the attribute-name that contains the userid as returned from idp
    'useridattr' => 'uid',

    // You can create as many scopes as you want and assign attributes to them
    // WIP: Actually only basic scope is supported with all the attributes
    'scopes' => [
        'basic' => [
            'icon' => 'user',
            'description' => [
                'en' => 'Your username.',
                'es' => 'Su nombre de usuario.'
            ],
            'attributes' => ['uid'],
        ],
    ],
];