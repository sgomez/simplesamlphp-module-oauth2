<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use League\OAuth2\Server\Exception\OAuthServerException;
use SimpleSAML\Modules\OAuth2\Entity\UserEntity;
use SimpleSAML\Modules\OAuth2\OAuth2AuthorizationServer;
use SimpleSAML\Modules\OAuth2\Repositories\ClientRepository;
use SimpleSAML\Modules\OAuth2\Repositories\UserRepository;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

try {
    $request = ServerRequestFactory::fromGlobals();
    $parameters = $request->getQueryParams();
    $clientId = array_key_exists('client_id', $parameters) ? $parameters['client_id'] : null;

    // The AS could be configured by client
    $clientRepository = new ClientRepository();
    $client = $clientRepository->find($clientId);

    $oauth2config = \SimpleSAML_Configuration::getOptionalConfig('module_oauth2.php');

    if (!$client || !$client['auth_source']) {
        $as = $oauth2config->getString('auth');
    } else {
        $as = $client['auth_source'];
    }

    $auth = new \SimpleSAML_Auth_Simple($as);
    $auth->requireAuth();

    $attributes = $auth->getAttributes();
    $useridattr = $oauth2config->getString('useridattr');

    if (!isset($attributes[$useridattr])) {
        throw new \Exception('Oauth2 useridattr doesn\'t exists. Available attributes are: '.implode(', ', $attributes));
    }
    $userid = $attributes[$useridattr][0];

    // Persists the user attributes on the database
    $userRepository = new UserRepository();
    $userRepository->insertOrCreate($userid, $attributes);

    $server = OAuth2AuthorizationServer::getInstance();
    $authRequest = $server->validateAuthorizationRequest($request);
    $authRequest->setUser(new UserEntity($userid));
    $authRequest->setAuthorizationApproved(true);

    $response = $server->completeAuthorizationRequest($authRequest, new Response());

    $emiter = new Response\SapiEmitter();
    $emiter->emit($response);
} catch (OAuthServerException $e) {
    throw new \SimpleSAML_Error_Exception($e->getHint());
} catch (Exception $e) {
    throw new \SimpleSAML_Error_Exception($e->getMessage());
}
