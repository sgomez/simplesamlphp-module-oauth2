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


use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use SimpleSAML\Modules\OAuth2\Entity\RefreshTokenEntity;

class RefreshTokenRepository extends AbstractDBALRepository implements RefreshTokenRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getNewRefreshToken()
    {
        return new RefreshTokenEntity();
    }

    /**
     * @inheritDoc
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        $this->conn->insert($this->getTableName(), [
            'id' => $refreshTokenEntity->getIdentifier(),
            'expires_at' => $refreshTokenEntity->getExpiryDateTime(),
            'accesstoken_id' => $refreshTokenEntity->getAccessToken()->getIdentifier(),
        ], [
            'string',
            'datetime',
            'string',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function revokeRefreshToken($tokenId)
    {
        $this->conn->update($this->getTableName(), ['is_revoked' => true], ['id' => $tokenId]);
    }

    /**
     * @inheritDoc
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        return $this->conn->fetchColumn('SELECT is_revoked FROM '.$this->getTableName().' WHERE id = ?', [$tokenId]);
    }

    public function removeExpiredRefreshTokens()
    {
        $this->conn->executeUpdate('
                DELETE FROM '.$this->getTableName().'
                WHERE expires_at < ?
            ',
            [
                new \DateTime(),
            ],
            [
                'datetime',
            ]
        );
    }

    public function getTableName()
    {
        return $this->store->getPrefix().'_oauth2_refreshtoken';
    }
}