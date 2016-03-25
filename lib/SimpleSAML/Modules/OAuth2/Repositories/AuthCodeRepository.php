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


use League\OAuth2\Server\Entities\Interfaces\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

class AuthCodeRepository extends AbstractDBALRepository implements AuthCodeRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        $this->conn->insert($this->getTableName(), [
            'id' => $authCodeEntity->getIdentifier(),
            'scopes' => $authCodeEntity->getScopes(),
            'expires_at' => $authCodeEntity->getExpiryDateTime(),
            'user_id' => $authCodeEntity->getUserIdentifier(),
            'client_id' => $authCodeEntity->getClient()->getIdentifier(),
            'redirect_uri' => $authCodeEntity->getRedirectUri(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function revokeAuthCode($codeId)
    {
        $this->conn->update($this->getTableName(), ['is_revoked' => true], ['id' => $codeId]);
    }

    /**
     * @inheritDoc
     */
    public function isAuthCodeRevoked($codeId)
    {
        return $this->conn->fetchColumn('SELECT is_revoked FROM '.$this->getTableName().' WHERE id = ?', [$codeId]);
    }

    public function getTableName()
    {
        return $this->store->getPrefix().'_oauth2_authcode';
    }
}