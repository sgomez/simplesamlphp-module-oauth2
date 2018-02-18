<?php

/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\SimpleSAML\Modules\OAuth2;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Modules\OAuth2\ClaimTranslatorExtractor;

class ClaimTranslatorExtractorTest extends TestCase
{
    public function invokeMethod(&$object, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function samlAttributesProvider()
    {
        return [
            // Translate single value
            [
                [
                    'eduPersonPrincipalName' => ['johndoe'],
                ],
                [
                    'sub' => 'johndoe',
                ],
            ],
            // Translate multiple value
            [
                [
                    'mail' => ['johndoe@example.com', 'john@example.com'],
                ],
                [
                    'email' => 'johndoe@example.com',
                ],
            ],
            // Translate by order preference
            [
                [
                    'eduPersonPrincipalName' => ['johndoe'],
                    'eduPersonTargetedID' => ['sha1sum'],
                ],
                [
                    'sub' => 'johndoe',
                ],
            ],
            // Translate multiple
            [
                [
                    'eduPersonPrincipalName' => ['johndoe'],
                    'mail' => ['johndoe@example.com'],
                ],
                [
                    'sub' => 'johndoe',
                    'email' => 'johndoe@example.com',
                ],
            ],
            // Remove unknown
            [
                [
                    'uid' => ['johndoe'],
                ],
                [
                    // Empty.
                ],
            ],
        ];
    }

    /**
     * @dataProvider samlAttributesProvider
     */
    public function testTranslateSamlAttributes($from, $to)
    {
        $claimTranslatorExtractor = new ClaimTranslatorExtractor();
        $translatedClaims = $this->invokeMethod($claimTranslatorExtractor, 'translateSamlAttributesToClaims', [$from]);

        $this->assertArraySubset($to, $translatedClaims);
    }

    public function testExtractEmailScope()
    {
        $claimTranslatorExtractor = new ClaimTranslatorExtractor();
        $extractedClaims = $claimTranslatorExtractor->extract(['email'], [
                'eduPersonPrincipalName' => ['johndoe'],
                'mail' => ['johndoe@example.com'],
            ]
        );

        $this->assertArraySubset(['email' => 'johndoe@example.com'], $extractedClaims);
    }
}
