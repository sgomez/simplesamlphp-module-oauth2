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

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use SimpleSAML\Modules\OAuth2\Entity\ClientEntity;
use SimpleSAML\Utils\Random;

class ClientRepository extends AbstractDBALRepository implements ClientRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true)
    {
        /** @var \SimpleSAML\Modules\OAuth2\Entity\ClientEntity $entity */
        $entity = $this->find($clientIdentifier);

        if (!$entity) {
            return null;
        }

        if ($clientSecret && $clientSecret !== $entity['secret']) {
            return null;
        }

        $client = new ClientEntity(
            $clientIdentifier,
            $entity['name'],
            $entity['secret'],
            $entity['redirect_uri'],
            $entity['auth_source'],
            $entity['scopes']
        );

        return $client;
    }

    public function persistNewClient($id, $secret, $name, $description, $authSource, $redirectUri, $scopes)
    {
        if (false === is_array($redirectUri)) {
            if (is_string($redirectUri)) {
                $redirectUri = [$redirectUri];
            } else {
                throw new \InvalidArgumentException('Client redirect URI must be a string or an array.');
            }
        }

        $this->conn->insert($this->getTableName(), [
            'id' => $id,
            'secret' => $secret,
            'name' => $name,
            'description' => $description,
            'auth_source' => $authSource,
            'redirect_uri' => $redirectUri,
            'scopes' => $scopes,
        ], [
            'string',
            'string',
            'string',
            'string',
            'string',
            'json_array',
            'json_array',
        ]);
    }

    public function updateClient($id, $name, $description, $authSource, $redirectUri, $scopes)
    {
        $this->conn->update($this->getTableName(), [
            'name' => $name,
            'description' => $description,
            'auth_source' => $authSource,
            'redirect_uri' => $redirectUri,
            'scopes' => $scopes,
        ], [
            'id' => $id,
        ], [
            'string',
            'string',
            'string',
            'json_array',
            'json_array',
        ]);
    }

    public function delete($clientIdentifier)
    {
        $conn = $this->store->getConnection();
        $conn->delete($this->getTableName(), [
            'id' => $clientIdentifier,
        ]);
    }

    /**
     * @param $clientIdentifier
     *
     * @return array
     */
    public function find($clientIdentifier)
    {
        $client = $this->conn->fetchAssoc(
            'SELECT * FROM '.$this->getTableName().' WHERE id = ?',
            [
                $clientIdentifier,
            ], [
                'string',
            ]
        );

        if ($client) {
            $client['redirect_uri'] = $this->conn->convertToPHPValue($client['redirect_uri'], 'json_array');
            $client['scopes'] = $this->conn->convertToPHPValue($client['scopes'], 'json_array');
        }

        return $client;
    }

    /**
     * @return ClientEntity[]
     */
    public function findAll()
    {
        $clients = $this->conn->fetchAll(
            'SELECT * FROM '.$this->getTableName()
        );

        foreach ($clients as &$client) {
            $client['redirect_uri'] = $this->conn->convertToPHPValue($client['redirect_uri'], 'json_array');
            $client['scopes'] = $this->conn->convertToPHPValue($client['scopes'], 'json_array');
        }

        return $clients;
    }

    public function getTableName()
    {
        return $this->store->getPrefix().'_oauth2_client';
    }

    public function restoreSecret($clientIdentifier)
    {
        $secret = Random::generateID();
        $this->conn->update($this->getTableName(), [
            'secret' => $secret,
        ], [
            'id' => $clientIdentifier,
        ], [
            'string',
        ]);
    }
}
