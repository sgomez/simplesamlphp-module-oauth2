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


use League\OAuth2\Server\ResourceServer;
use SimpleSAML\Modules\OAuth2\Repositories\AccessTokenRepository;
use SimpleSAML\Utils\Config;

class OAuth2ResourceServer
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $oauth2config = \SimpleSAML_Configuration::getConfig('module_oauth2.php');

        $accessTokenRepository = new AccessTokenRepository();
        $publicKey = Config::getCertPath('oauth2_module.crt');

        self::$instance = new ResourceServer(
            $accessTokenRepository,
            $publicKey
        );

        return self::$instance;
    }
}