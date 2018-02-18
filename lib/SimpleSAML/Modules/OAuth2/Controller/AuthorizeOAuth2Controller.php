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
use League\OAuth2\Server\Exception\OAuthServerException;
use SimpleSAML\Auth as Auth;
use SimpleSAML\Modules\OAuth2\Entity\UserEntity;
use SimpleSAML\Modules\OAuth2\OAuth2Controller;
use SimpleSAML\Modules\OAuth2\Repositories\ClientRepository;
use SimpleSAML\Modules\OAuth2\Repositories\UserRepository;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class AuthorizeOAuth2Controller extends OAuth2Controller
{
    protected function handleRequest(ServerRequest $request, AuthorizationServer $server)
    {
        $parameters = $request->getQueryParams();
        $clientId = $parameters['client_id'] ?? null;
        if (!$clientId) {
            throw OAuthServerException::invalidRequest('client_id', "Parameter 'client_id' is required.");
        }

        $client = $this->getClient($clientId);
        $userid = $this->authenticateClient($client);

        $authRequest = $server->validateAuthorizationRequest($request);
        $authRequest->setUser(new UserEntity($userid));
        $authRequest->setAuthorizationApproved(true);

        return $server->completeAuthorizationRequest($authRequest, new Response());
    }

    protected function getClient($clientId): array
    {
        $clientRepository = new ClientRepository();
        $client = $clientRepository->find($clientId);

        if (!$client) {
            throw OAuthServerException::invalidRequest('client_id', 'Client not found');
        }

        return $client;
    }

    protected function authenticateClient($client)
    {
        $as = $client['auth_source'] ?? $this->oauth2config->getString('auth');
        $auth = new Auth\Simple($as);
        $auth->requireAuth();

        $attributes = $auth->getAttributes();
        $userIdAttr = $this->oauth2config->getString('useridattr');

        if (!isset($attributes[$userIdAttr])) {
            throw new \SimpleSAML_Error_Exception('OAuth2 useridattr doesn\'t exists. Available attributes are: '.implode(', ', $attributes));
        }
        $userid = $attributes[$userIdAttr][0];

        // Persists the user attributes on the database
        $userRepository = new UserRepository();
        $userRepository->insertOrCreate($userid, $attributes);

        return $userid;
    }
}
