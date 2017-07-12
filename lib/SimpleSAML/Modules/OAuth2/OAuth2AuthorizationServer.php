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
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use SimpleSAML\Modules\OAuth2\Repositories\AccessTokenRepository;
use SimpleSAML\Modules\OAuth2\Repositories\AuthCodeRepository;
use SimpleSAML\Modules\OAuth2\Repositories\ClientRepository;
use SimpleSAML\Modules\OAuth2\Repositories\RefreshTokenRepository;
use SimpleSAML\Modules\OAuth2\Repositories\ScopeRepository;
use SimpleSAML\Utils\Config;

class OAuth2AuthorizationServer
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $oauth2config = \SimpleSAML_Configuration::getConfig('module_oauth2.php');
        $accessTokenDuration = $oauth2config->getString('accessTokenDuration');
        $authCodeDuration = $oauth2config->getString('authCodeDuration');
        $passPhrase = $oauth2config->getString('pass_phrase', null);
        $refreshTokenDuration = $oauth2config->getString('refreshTokenDuration');

        $privateKeyPath = Config::getCertPath('oauth2_module.pem');
        $publicKeyPath = Config::getCertPath('oauth2_module.crt');
        $privateKey = new CryptKey($privateKeyPath, $passPhrase);
        $publicKey = new CryptKey($publicKeyPath);

        self::$instance = new AuthorizationServer(
            new ClientRepository(),
            new AccessTokenRepository(),
            new ScopeRepository(),
            $privateKey,
            $publicKey
        );

        $refreshTokenRepository = new RefreshTokenRepository();

        $authCodeGrant = new AuthCodeGrant(
            new AuthCodeRepository(),
            $refreshTokenRepository,
            new \DateInterval($authCodeDuration)
        );
        $authCodeGrant->setRefreshTokenTTL(new \DateInterval($refreshTokenDuration)); // refresh tokens will expire after 1 month

        self::$instance->enableGrantType(
            $authCodeGrant,
            new \DateInterval($accessTokenDuration)
        );

        $implicitGrant = new ImplicitGrant(new \DateInterval($accessTokenDuration));

        self::$instance->enableGrantType(
            $implicitGrant,
            new \DateInterval($accessTokenDuration)
        );

        $refreshTokenGrant = new RefreshTokenGrant($refreshTokenRepository);
        $refreshTokenGrant->setRefreshTokenTTL(new \DateInterval($refreshTokenDuration));

        self::$instance->enableGrantType(
            $refreshTokenGrant,
            new \DateInterval($authCodeDuration)
        );

        return self::$instance;
    }
}
