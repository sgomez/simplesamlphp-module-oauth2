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

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use OpenIDConnectServer\Repositories\IdentityProviderInterface;
use SimpleSAML\Modules\OAuth2\Entity\UserEntity;

class UserRepository extends AbstractDBALRepository implements UserRepositoryInterface, IdentityProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        throw new \Exception('Not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function getUserEntityByIdentifier($identifier)
    {
        $entity = $this->find($identifier);

        if (!$entity) {
            return null;
        }

        $user = new UserEntity($identifier);
        $user->setAttributes($entity['attributes']);
        $user->setCreatedAt($entity['created_at']);
        $user->setUpdatedAt($entity['updated_at']);

        return $user;
    }

    /**
     * @param $id
     * @param $attributes
     */
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

    /**
     * @param $id
     * @param $attributes
     *
     * @return int
     */
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

    /**
     * @param $userIdentifier
     *
     * @return array|bool
     */
    public function find($userIdentifier)
    {
        $user = $this->conn->fetchAssoc(
            "SELECT * FROM {$this->getTableName()} WHERE id = ?", [
                $userIdentifier,
            ], [
                'string',
            ]
        );

        if ($user) {
            $user['attributes'] = $this->conn->convertToPHPValue($user['attributes'], 'json_array');
        }

        return $user;
    }

    /**
     * @param $userIdentifier
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function delete($userIdentifier)
    {
        $this->conn->delete($this->getTableName(), [
            'id' => $userIdentifier,
        ]);
    }

    /**
     * @param $userId
     * @param $attributes
     */
    public function insertOrCreate($userId, $attributes)
    {
        if (0 === $this->updateUser($userId, $attributes)) {
            $this->persistNewUser($userId, $attributes);
        }
    }

    /**
     * @param $userId
     *
     * @return mixed
     */
    public function getAttributes($userId)
    {
        $attributes = $this->conn->fetchColumn(
            "SELECT attributes FROM {$this->getTableName()} WHERE id = ?",
            [$userId]
        );

        return $this->conn->convertToPHPValue($attributes, 'json_array');
    }

    /**
     * {@inheritdoc}
     */
    public function getTableName()
    {
        return $this->store->getPrefix().'_oauth2_user';
    }
}
