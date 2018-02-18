<?php

/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Modules\OAuth2\Controller;

use League\OAuth2\Server\AuthorizationServer;
use SimpleSAML\Modules\OAuth2\OAuth2Controller;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class AccessTokenOAuth2Controller extends OAuth2Controller
{
    protected function handleRequest(ServerRequest $request, AuthorizationServer $server)
    {
        return $server->respondToAccessTokenRequest($request, new Response());
    }
}
