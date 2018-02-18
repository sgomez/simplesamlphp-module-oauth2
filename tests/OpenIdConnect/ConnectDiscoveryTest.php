<?php

/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\SimpleSAML\Modules\OAuth2\OpenIdConnect;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Modules\OAuth2\OpenIdConnect\ConnectDiscovery;

class ConnectDiscoveryTest extends TestCase
{
    const ISSUER_URL_HOST = 'http://www.example.com';
    const MODULE_BASE_PATH = 'https://www.example.com/simplesaml/module.php/oauth2';

    public function testPkceCanBeEnabled()
    {
        $oauth2config = $this->createOAuth2Config([], true);
        $class = $this->createConnectDiscoveryClass($oauth2config);
        $metadata = $class->metadata();
        $this->assertArrayHasKey('code_challenge_methods_supported', $metadata);
    }

    public function testPkceCanBeDisabled()
    {
        $oauth2config = $this->createOAuth2Config([], false);
        $class = $this->createConnectDiscoveryClass($oauth2config);
        $metadata = $class->metadata();
        $this->assertArrayNotHasKey('code_challenge_methods_supported', $metadata);
    }

    public function testShowScopes()
    {
        $oauth2config = $this->createOAuth2Config([
            'basic' => ['attribute'],
            'openid' => ['attribute'],
        ], false);
        $class = $this->createConnectDiscoveryClass($oauth2config);
        $metadata = $class->metadata();
        $this->assertArraySubset(['scopes_supported' => ['basic', 'openid']], $metadata);
    }

    private function createOAuth2Config(array $scopes, bool $pkce)
    {
        $oauth2config = $this->createMock(\SimpleSAML_Configuration::class);
        $oauth2config
            ->method('getArray')
            ->willReturn($scopes);
        $oauth2config
            ->method('getBoolean')
            ->willReturn($pkce);

        return $oauth2config;
    }

    private function createConnectDiscoveryClass(\SimpleSAML_Configuration $oauth2config)
    {
        return new ConnectDiscovery(
            self::ISSUER_URL_HOST,
            self::MODULE_BASE_PATH,
            $oauth2config
        );
    }
}
