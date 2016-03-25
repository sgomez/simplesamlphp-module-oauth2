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


use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Server;
use SimpleSAML\Modules\OAuth2\Repositories\AccessTokenRepository;
use SimpleSAML\Modules\OAuth2\Repositories\AuthCodeRepository;
use SimpleSAML\Modules\OAuth2\Repositories\ClientRepository;
use SimpleSAML\Modules\OAuth2\Repositories\RefreshTokenRepository;
use SimpleSAML\Modules\OAuth2\Repositories\ScopeRepository;
use SimpleSAML\Modules\OAuth2\Repositories\UserRepository;
use SimpleSAML\Utils\Config;

class OAuth2Server
{
    private static $instance;

    private static $prefix;

    public static function getInstance()
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $oauth2config = \SimpleSAML_Configuration::getConfig('module_oauth2.php');

        $clientRepository =  new ClientRepository();
        $scopeRepository = new ScopeRepository();
        $accessTokenRepository = new AccessTokenRepository();
        $authTokenRepository = new AuthCodeRepository();
        $refreshTokenRepository = new RefreshTokenRepository();
        $userRepository = new UserRepository();

        $privateKey = Config::getCertPath('oauth2_module.pem');
        $publicKey = Config::getCertPath('oauth2_module.crt');

        self::$instance = new Server(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $privateKey,
            $publicKey
        );

        self::$instance->enableGrantType(
            new AuthCodeGrant(
                $authTokenRepository,
                $refreshTokenRepository,
                $userRepository,
                new \DateInterval('PT10M')
            ),
            new \DateInterval('PT1H')
        );

        return self::$instance;
    }
}