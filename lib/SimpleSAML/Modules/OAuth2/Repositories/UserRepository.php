<?php
/*
 * This file is part of the jt2016-uco-spa.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Modules\OAuth2\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository extends AbstractDBALRepository implements UserRepositoryInterface
{
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        throw new \Exception('Not supported');
    }

    public function persistNewUser($id, $attributes)
    {
        $now = new \DateTime();

        $this->conn->insert($this->getTableName(),
            [
                'id' => $id,
                'attributes' => $attributes,
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'string',
                'json_array',
                'datetime',
                'datetime',
            ]
        );
    }

    public function updateUser($id, $attributes)
    {
        $now = new \DateTime();

        return $this->conn->update($this->getTableName(),
            [
                'attributes' => $attributes,
                'updated_at' => $now,
            ], [
               'id' => $id,
            ], [
                'json_array',
                'datetime',
            ]
        );
    }

    public function delete($userIdentifier)
    {
        $this->conn->delete($this->getTableName(), [
            'id' => $userIdentifier,
        ]);
    }

    public function insertOrCreate($userId, $attributes)
    {
        if (0 === $this->updateUser($userId, $attributes)) {
            $this->persistNewUser($userId, $attributes);
        }
    }

    public function getAttributes($userId)
    {
        $attributes = $this->conn->fetchColumn(
            'SELECT attributes FROM '.$this->getTableName().' WHERE id = ?',
            [$userId]
        );

        return $this->conn->convertToPHPValue($attributes, 'json_array');
    }

    public function getTableName()
    {
        return $this->store->getPrefix().'_oauth2_user';
    }
}
