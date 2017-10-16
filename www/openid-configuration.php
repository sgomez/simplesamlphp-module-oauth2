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
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

try {
    $config = \SimpleSAML_Configuration::getInstance();
    $oauth2config = \SimpleSAML_Configuration::getOptionalConfig('module_oauth2.php');
    $request = ServerRequestFactory::fromGlobals();


    $metadata = [
        'issuer' => \SimpleSAML\Utils\HTTP::getSelfURLHost() . '/',
        'authorization_endpoint' => SimpleSAML_Module::getModuleURL('oauth2/authorize.php'),
        'token_endpoint' => SimpleSAML_Module::getModuleURL('oauth2/access_token.php'),
        'userinfo_endpoint' => SimpleSAML_Module::getModuleURL('oauth2/userinfo.php'),
        'jwks_uri' => SimpleSAML_Module::getModuleURL('oauth2/userinfo.php'),
        'scopes_supported' => null,
        'response_types_supported' => null,
        'subject_types_supported' => null,
        'id_token_signing_alg_values_supported' => null,
    ];

    $response = new JsonResponse($metadata);
    $emiter = new Response\SapiEmitter();
    $emiter->emit($response);
} catch (OAuthServerException $e) {
    header('Content-type: text/plain; utf-8', true, 500);
    header('OAuth-Error: '.$e->getHint());

    echo json_encode([
        'error' => [
            'code' => $e->getHttpStatusCode(),
            'message' => $e->getHint(),
        ],
    ]);
} catch (Exception $e) {
    header('Content-type: text/plain; utf-8', true, 500);
    header('OAuth-Error: '.$e->getMessage());

    echo json_encode([
        'error' => [
            'code' => 500,
            'message' => $e->getMessage(),
        ],
    ]);
}
