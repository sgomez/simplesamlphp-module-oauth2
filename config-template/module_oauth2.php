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
    'accessTokenDuration'  => 3600, // 60 minutes
    'requestTokenDuration' => 1209600, // 14 days
    'authTokenDuration'    => 30, // 30 seconds

    'title' => 'SimpleSAMLphp',
    'logo' => 'data:image/png;base64,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',

    // Tag to run storage cleanup script using the cron module...
    'cron_tag' => 'hourly',

    // auth is the idp to use for admin authentication,
    'auth' => 'default-sp',

    // useridattr is the attribute-name that contains the userid as returned from idp
    'useridattr' => 'uid',

    'clients' => [
        ''
    ],

    // You can create as many scopes as you want and assign attributes to them
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