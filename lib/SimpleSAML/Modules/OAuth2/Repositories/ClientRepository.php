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


use Doctrine\DBAL\Types\JsonArrayType;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use SimpleSAML\Modules\OAuth2\Entity\ClientEntity;
use SimpleSAML\Utils\Random;

class ClientRepository extends AbstractDBALRepository implements ClientRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true)
    {
        /** @var \SimpleSAML\Modules\OAuth2\Entity\ClientEntity $entity */
        $entity = $this->find($clientIdentifier);

        if (!$entity) {
            return;
        }

        if ($clientSecret && $clientSecret !== $entity['secret']) {
            return;
        }

        $client = new ClientEntity();
        $client->setIdentifier($clientIdentifier);
        $client->setName($entity['name']);
        $client->setRedirectUri($entity['redirect_uri']);
        $client->setSecret($entity['secret']);

        return $client;
    }

    public function persistNewClient($id, $secret, $name, $description, $redirectUri)
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
            'redirect_uri' => $redirectUri,
            'scopes' => ['basic'],
        ], [
            'string',
            'string',
            'string',
            'string',
            'json_array',
            'json_array'
        ]);
    }

    public function updateClient($id, $name, $description, $redirectUri)
    {
        $this->conn->update($this->getTableName(), [
            'name' => $name,
            'description' => $description,
            'redirect_uri' => $redirectUri,
            'scopes' => ['basic'],
        ], [
            'id' => $id,
        ], [
            'string',
            'string',
            'json_array',
            'json_array'
        ]);
    }

    public function delete($clientIdentifier)
    {
        $conn = $this->store->getConnection();
        $conn->delete($this->getTableName(), [
            'id' => $clientIdentifier
        ]);
    }

    public function find($clientIdentifier)
    {
        $client = $this->conn->fetchAssoc(
            'SELECT * FROM '.$this->getTableName().' WHERE id = ?',
            [
                $clientIdentifier
            ], [
                'string'
            ]
        );

        $client['redirect_uri'] = $this->conn->convertToPHPValue($client['redirect_uri'], 'json_array' );
        $client['scopes'] = $this->conn->convertToPHPValue($client['scopes'], 'json_array' );

        return $client;
    }

    public function findAll()
    {
        $clients = $this->conn->fetchAll(
            'SELECT * FROM '.$this->getTableName()
        );

        foreach ($clients as &$client) {
            $client['redirect_uri'] = $this->conn->convertToPHPValue($client['redirect_uri'], 'json_array' );
            $client['scopes'] = $this->conn->convertToPHPValue($client['scopes'], 'json_array' );
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
            'string'
        ]);
    }
}