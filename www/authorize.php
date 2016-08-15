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
use SimpleSAML\Modules\OAuth2\OAuth2Server;
use SimpleSAML\Modules\OAuth2\Utils\Crypt;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

try {
    $oauthconfig = \SimpleSAML_Configuration::getOptionalConfig( 'module_oauth2.php' );
    $as = $oauthconfig->getString('auth');
    $useridattr = $oauthconfig->getString('useridattr');

    $auth = new \SimpleSAML_Auth_Simple($as);
    $auth->requireAuth();

    $attributes = $auth->getAttributes();
    $userid = Crypt::getInstance()->cryptAttributes($attributes);

    $server = OAuth2Server::getInstance();
    $request = ServerRequestFactory::fromGlobals();

    $authRequest = $server->validateAuthorizationRequest($request);
    $authRequest->setUser(new UserEntity($userid));
    $authRequest->setAuthorizationApproved(true);
    $response = $server->completeAuthorizationRequest($authRequest, new Response());

    $emiter = new Response\SapiEmitter();
    $emiter->emit($response);

} catch (Exception $e) {

    header('Content-type: text/plain; utf-8', TRUE, 500);
    header('OAuth-Error: ' . $e->getMessage());

    print_r($e);

}