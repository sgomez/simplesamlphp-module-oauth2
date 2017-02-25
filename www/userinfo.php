<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


use SimpleSAML\Modules\OAuth2\OAuth2ResourceServer;
use SimpleSAML\Modules\OAuth2\Repositories\AccessTokenRepository;
use SimpleSAML\Modules\OAuth2\Repositories\UserRepository;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

try {
    $server = OAuth2ResourceServer::getInstance();
    $request = ServerRequestFactory::fromGlobals();

    $authorization = $server->validateAuthenticatedRequest($request);

    $oauth2Attributes = $authorization->getAttributes();
    $tokenId = $oauth2Attributes['oauth_access_token_id'];

    $accessTokenRepository = new AccessTokenRepository();
    $userId = $accessTokenRepository->getUserId($tokenId);

    $userRepository = new UserRepository();
    $attributes['attributes'] = $userRepository->getAttributes($userId);
    $attributes['username'] = $userId;

    $response = new Response\JsonResponse($attributes);

    $emiter = new Response\SapiEmitter();
    $emiter->emit($response);
} catch (Exception $e) {
    header('Content-type: text/plain; utf-8', TRUE, 500);
    header('OAuth-Error: ' . $e->getMessage());

    print_r($e);
}