<?php

/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Modules\OAuth2;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\ServerRequestFactory;

abstract class OAuth2Controller
{
    /**
     * @var \SimpleSAML_Configuration
     */
    protected $oauth2config;

    protected function __construct(\SimpleSAML_Configuration $oauth2config)
    {
        $this->oauth2config = $oauth2config;

        set_exception_handler(function (\Throwable $t) {
            if ($t instanceof OAuthServerException) {
                $error['error'] = [
                    'code' => $t->getHttpStatusCode(),
                    'message' => $t->getMessage(),
                    'hint' => $t->getHint(),
                ];
            } else {
                $error['error'] = [
                    'code' => 500,
                    'message' => $t->getMessage(),
                ];
            }

            $response = new JsonResponse($error, 500);
            $emitter = new SapiEmitter();
            $emitter->emit($response);
        });
    }

    abstract protected function handleRequest(ServerRequest $request, AuthorizationServer $server);

    public static function invoke()
    {
        $oauth2config = \SimpleSAML_Configuration::getConfig('module_oauth2.php');
        $class = new static( $oauth2config);

        $request = ServerRequestFactory::fromGlobals();
        $server = OAuth2AuthorizationServer::getInstance();
        $response = $class->handleRequest($request, $server);

        if (is_array($response)) {
            $response = new JsonResponse($response);
        }

        $emitter = new SapiEmitter();
        $emitter->emit($response);
    }
}
