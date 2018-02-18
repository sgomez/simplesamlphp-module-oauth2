<?php

/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function oauth2_hook_frontpage(&$links)
{
    assert('is_array($links)');
    assert('array_key_exists("links", $links)');

    $links['federation']['oauthregistry'] = [
        'href' => \SimpleSAML_Module::getModuleURL('oauth2/registry.php'),
        'text' => [
            'en' => 'OAuth2 Client Registry',
            'es' => 'Registro de clientes OAuth2',
        ],
        'shorttext' => [
            'en' => 'OAuth2 Registry',
            'es' => 'Registro OAuth2',
        ],
    ];
}
