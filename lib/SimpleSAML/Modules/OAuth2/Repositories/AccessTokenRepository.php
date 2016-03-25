<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Modules\OAuth2\Repositories;


use League\OAuth2\Server\Entities\Interfaces\AccessTokenEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class AccessTokenRepository extends AbstractDBALRepository implements AccessTokenRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $this->conn->insert($this->getTableName(), [
            'id' => $accessTokenEntity->getIdentifier(),
            'scopes' => $accessTokenEntity->getScopes(),
            'expires_at' => $accessTokenEntity->getExpiryDateTime(),
            'user_id' => $accessTokenEntity->getUserIdentifier(),
            'client_id' => $accessTokenEntity->getClient()->getIdentifier(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function revokeAccessToken($tokenId)
    {
        $this->conn->update($this->getTableName(), ['is_revoked' => true], ['id' => $tokenId]);
    }

    /**
     * @inheritDoc
     */
    public function isAccessTokenRevoked($tokenId)
    {
        return $this->conn->fetchColumn('SELECT is_revoked FROM '.$this->getTableName().' WHERE id = ?', [$tokenId]);
    }

    public function getTableName()
    {
        return $this->store->getPrefix().'_oauth2_accesstoken';
    }
}