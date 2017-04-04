<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use SimpleSAML\Modules\OAuth2\Entity\UserEntity;
use SimpleSAML\Modules\OAuth2\OAuth2AuthorizationServer;
use SimpleSAML\Modules\OAuth2\Repositories\UserRepository;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

try {
    $oauth2config = \SimpleSAML_Configuration::getOptionalConfig('module_oauth2.php');
    $useridattr = $oauth2config->getString('useridattr');

    $as = $oauth2config->getString('auth');
    $auth = new \SimpleSAML_Auth_Simple($as);
    $auth->requireAuth();

    $attributes = $auth->getAttributes();
    if (!isset($attributes[$useridattr])) {
        throw new \Exception('Oauth2 useridattr doesn\'t exists. Available attributes are: '.implode(', ', $attributes));
    }
    $userid = $attributes[$useridattr][0];

    // Persists the user attributes on the database
    $userRepository = new UserRepository();
    $userRepository->insertOrCreate($userid, $attributes);

    $server = OAuth2AuthorizationServer::getInstance();
    $request = ServerRequestFactory::fromGlobals();

    $authRequest = $server->validateAuthorizationRequest($request);
    $authRequest->setUser(new UserEntity($userid));
    $authRequest->setAuthorizationApproved(true);

    $response = $server->completeAuthorizationRequest($authRequest, new Response());

    $emiter = new Response\SapiEmitter();
    $emiter->emit($response);
} catch (Exception $e) {
    header('Content-type: text/plain; utf-8', true, 500);
    header('OAuth-Error: '.$e->getMessage());

    print_r($e);
}
