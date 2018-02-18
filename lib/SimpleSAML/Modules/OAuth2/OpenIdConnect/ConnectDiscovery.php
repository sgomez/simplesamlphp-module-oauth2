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

use SimpleSAML\Module;
use SimpleSAML\Utils\HTTP;

class ConnectDiscovery
{
    /**
     * @var self
     */
    private static $self;
    /**
     * @var array
     */
    private $scopes;
    /**
     * @var bool
     */
    private $pkceIsEnabled;
    /**
     * @var array
     */
    private $metadata;

    public function __construct(string $issuerUrlHost, string $moduleBasePath, \SimpleSAML_Configuration $oauth2config)
    {
        $this->scopes = $oauth2config->getArray('scopes');
        $this->pkceIsEnabled = $oauth2config->getBoolean('pkce');

        $this->metadata['issuer'] = $issuerUrlHost;
        $this->metadata['authorization_endpoint'] = "{$moduleBasePath}/authorize.php";
        $this->metadata['token_endpoint'] = "{$moduleBasePath}/access_token.php";
        $this->metadata['userinfo_endpoint'] = "{$moduleBasePath}/userinfo.php";
        $this->metadata['jwks_uri'] = "{$moduleBasePath}/jwks.php";
        $this->metadata['scopes_supported'] = array_keys($this->scopes);
        $this->metadata['response_types_supported'] = ['code', 'token', 'id_token token'];
        $this->metadata['subject_types_supported'] = ['public'];
        $this->metadata['id_token_signing_alg_values_supported'] = ['RS256'];
        if ($this->pkceIsEnabled) {
            $this->metadata['code_challenge_methods_supported'] = ['plain', 'S256'];
        }
    }

    public function metadata()
    {
        return $this->metadata;
    }

    public static function getInstance()
    {
        if (empty(static::$self)) {
            $oauth2config = \SimpleSAML_Configuration::getConfig('module_oauth2.php');
            $selfURLHost = HTTP::getSelfURLHost();
            $moduleBasePath = Module::getModuleURL('oauth2');
            static::$self = new self($selfURLHost, $moduleBasePath, $oauth2config);
        }

        return static::$self;
    }
}
