<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Jose\Factory\JWKFactory;
use Jose\Object\JWKSet;
use League\OAuth2\Server\Exception\OAuthServerException;
use SimpleSAML\Utils\Config;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

try {
    $config = \SimpleSAML_Configuration::getInstance();
    $oauth2config = \SimpleSAML_Configuration::getOptionalConfig('module_oauth2.php');
    $request = ServerRequestFactory::fromGlobals();

    $publicKeyPath = Config::getCertPath('oauth2_module.crt');
    $publicKey = file_get_contents($publicKeyPath);

    $jwk = JWKFactory::createFromKeyFile($publicKeyPath, null, [
        'use' => 'sig',
        'alg' => 'RS256',
    ]);

    $jwkset = new JWKSet();
    $jwkset->addKey($jwk);


    $response = new JsonResponse([ 'keys' => $jwkset->getKeys()]);
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
