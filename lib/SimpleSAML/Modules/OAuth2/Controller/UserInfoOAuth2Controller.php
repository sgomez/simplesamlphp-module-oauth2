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

use SimpleSAML\Modules\OAuth2\ClaimTranslatorExtractor;
use SimpleSAML\Modules\OAuth2\OAuth2Controller;
use SimpleSAML\Modules\OAuth2\OAuth2ResourceServer;
use SimpleSAML\Modules\OAuth2\Repositories\AccessTokenRepository;
use SimpleSAML\Modules\OAuth2\Repositories\UserRepository;
use Zend\Diactoros\ServerRequest;

class UserInfoOAuth2Controller extends OAuth2Controller
{
    protected function handleRequest(ServerRequest $request, \League\OAuth2\Server\AuthorizationServer $server)
    {
        $server = OAuth2ResourceServer::getInstance();
        $authorization = $server->validateAuthenticatedRequest($request);

        $tokenId = $authorization->getAttribute('oauth_access_token_id');
        $scopes = $authorization->getAttribute('oauth_scopes');

        $accessTokenRepository = new AccessTokenRepository();
        $userId = $accessTokenRepository->getUserId($tokenId);

        $userRepository = new UserRepository();
        $attributes = $userRepository->getAttributes($userId);

        $translator = new ClaimTranslatorExtractor();
        $claims = $translator->extract($scopes, $attributes);

        return $claims;
    }
}
