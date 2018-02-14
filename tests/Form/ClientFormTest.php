<?php
/**
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 14/02/18
 * Time: 18:04
 */

namespace Tests\SimpleSAML\Modules\OAuth2\Form;


use PHPUnit\Framework\TestCase;
use SimpleSAML\Modules\OAuth2\Form\ClientForm;

class ClientFormTest extends TestCase
{
    public function redirectUriProvider()
    {
        return [
            [['http://example.com'], true],
            [['https://example.com'], true],
            [['http://app.example.com'], true],
            [['https://app.example.com'], true],
            [['com.example.app:/redirect'], true],
            [['com.example.app.my-app:/oauth2-redirect'], true],
            [[':/:/app.example.com'], false],
            [['app.example.com'], false],
        ];
    }

    /**
     * @dataProvider redirectUriProvider
     */
    public function testValidateRedirectUri($redirectUri, $result)
    {
        $form = $this->createPartialMock(ClientForm::class, ['getValues']);
        $form->method('getValues')
            ->willReturn([
                'redirect_uri' => $redirectUri,
            ]);

        $form->validateRedirectUri($form);
        $this->assertSame($result,empty($form->getErrors()));
    }
}
