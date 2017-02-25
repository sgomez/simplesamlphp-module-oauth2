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


use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use SimpleSAML\Modules\OAuth2\Entity\AccessTokenEntity;

class AccessTokenRepository extends AbstractDBALRepository implements AccessTokenRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new AccessTokenEntity();
        $accessToken->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }

        return $accessToken;
    }

    /**
     * @inheritDoc
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $scopes = [];
        foreach ($accessTokenEntity->getScopes() as $scope) {
            $scopes[] = $scope->getIdentifier();
        }

        $this->conn->insert(
            $this->getTableName(),
            [
                'id' => $accessTokenEntity->getIdentifier(),
                'scopes' => $scopes,
                'expires_at' => $accessTokenEntity->getExpiryDateTime(),
                'user_id' => $accessTokenEntity->getUserIdentifier(),
                'client_id' => $accessTokenEntity->getClient()->getIdentifier()
            ], [
                'string',
                'json_array',
                'datetime',
                'string',
                'string',
            ]
        );
    }

    public function getUserId($tokenId)
    {
        $userId = $this->conn->fetchColumn(
            'SELECT user_id FROM ' . $this->getTableName() . ' WHERE id = ?',
            [$tokenId]
        );

        return $this->conn->convertToPHPValue($userId, 'string');
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
        return $this->conn->fetchColumn(
            'SELECT is_revoked FROM '.$this->getTableName().' WHERE id = ?',
            [$tokenId]
        );
    }

    public function removeExpiredAccessTokens()
    {
        $this->conn->executeUpdate(
            'DELETE FROM '.$this->getTableName().' WHERE expires_at < ?',
            [new \DateTime()],
            ['datetime']
        );
    }

    public function getTableName()
    {
        return $this->store->getPrefix().'_oauth2_accesstoken';
    }
}