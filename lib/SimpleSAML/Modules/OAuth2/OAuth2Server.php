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
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\ImplicitGrant;
use SimpleSAML\Modules\OAuth2\Repositories\AccessTokenRepository;
use SimpleSAML\Modules\OAuth2\Repositories\AuthCodeRepository;
use SimpleSAML\Modules\OAuth2\Repositories\ClientRepository;
use SimpleSAML\Modules\OAuth2\Repositories\RefreshTokenRepository;
use SimpleSAML\Modules\OAuth2\Repositories\ScopeRepository;
use SimpleSAML\Utils\Config;

class OAuth2Server
{
    private static $instance;

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

        $privateKey = Config::getCertPath('oauth2_module.pem');
        $publicKey = Config::getCertPath('oauth2_module.crt');

        self::$instance = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $privateKey,
            $publicKey
        );

        $authCodeGrant = new AuthCodeGrant(
            $authTokenRepository,
            $refreshTokenRepository,
            new \DateInterval('PT10M')
        );
        $authCodeGrant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month

        self::$instance->enableGrantType(
            $authCodeGrant,
            new \DateInterval('PT1H')
        );

        $implicitGrant = new ImplicitGrant(new \DateInterval('PT1H'));

        self::$instance->enableGrantType(
            $implicitGrant,
            new \DateInterval('PT1H')
        );

        return self::$instance;
    }
}