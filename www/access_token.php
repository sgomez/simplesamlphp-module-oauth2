<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


use SimpleSAML\Modules\OAuth2\OAuth2AuthorizationServer;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

try {
    $server = OAuth2AuthorizationServer::getInstance();
    $request = ServerRequestFactory::fromGlobals();

    $response = $server->respondToAccessTokenRequest($request, new Response());

    $emiter = new Response\SapiEmitter();
    $emiter->emit($response);
} catch (Exception $e) {
    header('Content-type: text/plain; utf-8', TRUE, 500);
    header('OAuth-Error: ' . $e->getMessage());

    print_r($e);
}