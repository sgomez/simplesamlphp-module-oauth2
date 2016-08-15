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

    // Tag to run storage cleanup script using the cron module...
    'cron_tag' => 'hourly',

    // auth is the idp to use for admin authentication,
    'auth' => 'default-sp',

    // useridattr is the attribute-name that contains the userid as returned from idp
    'useridattr' => 'uid',

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