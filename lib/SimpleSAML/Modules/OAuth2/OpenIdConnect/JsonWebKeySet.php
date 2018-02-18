<?php

/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Modules\OAuth2\OpenIdConnect;

use Jose\Factory\JWKFactory;
use Jose\Object\JWKSet;
use SimpleSAML\Utils\Config;

class JsonWebKeySet
{
    /**
     * @var self
     */
    private static $self;
    /**
     * @var JWKSet
     */
    private $jwkSet;

    public function __construct(string $publicKeyPath)
    {
        if (!file_exists($publicKeyPath)) {
            throw new \SimpleSAML_Error_Error("OAuth2 Cert File does not exists: {$publicKeyPath}.");
        }

        $jwk = JWKFactory::createFromKeyFile($publicKeyPath, null, [
            'use' => 'sig',
            'alg' => 'RS256',
        ]);

        $this->jwkSet = new JWKSet();
        $this->jwkSet->addKey($jwk);
    }

    public function keys()
    {
        return $this->jwkSet->getKeys();
    }

    public static function getInstance()
    {
        if (empty(static::$self)) {
            $oauth2CertPath = Config::getCertPath('oauth2_module.crt');

            static::$self = new self($oauth2CertPath);
        }

        return static::$self;
    }
}
