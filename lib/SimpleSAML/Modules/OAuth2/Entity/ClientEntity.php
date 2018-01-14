<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Modules\OAuth2\Entity;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class ClientEntity implements ClientEntityInterface
{
    use EntityTrait;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $redirectUri;

    /**
     * @var string
     */
    private $authSource;

    /**
     * @var array
     */
    private $scopes;

    /**
     * ClientEntity constructor.
     * @param string $name
     * @param string $secret
     * @param string $redirectUri
     * @param string $authSource
     * @param array $scopes
     */
    public function __construct($identifier, $name, $secret, $redirectUri, $authSource, array $scopes)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->secret = $secret;
        $this->redirectUri = $redirectUri;
        $this->authSource = $authSource;
        $this->scopes = $scopes;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * @return string
     */
    public function getAuthSource()
    {
        return $this->authSource;
    }

    /**
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }
}
