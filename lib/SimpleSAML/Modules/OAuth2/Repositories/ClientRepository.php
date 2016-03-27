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
use SimpleSAML\Modules\OAuth2\Model\Client;

class ClientRepository extends AbstractDBALRepository implements ClientRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null)
    {
        $entity = $this->find($clientIdentifier);

        if (!$entity) {
            return;
        }

        if ($clientSecret && $clientSecret !== $entity['secret']) {
            return;
        }

        $client = new Client();
        $client->setIdentifier($clientIdentifier);
        $client->setName($entity['name']);
        $client->setRedirectUri($entity['redirect_uri']);
        $client->setSecret($entity['secret']);

        return $client;
    }

    public function persistNewClient($id, $secret, $name, $description, $redirectUri)
    {
        $this->conn->insert($this->getTableName(), [
            'id' => $id,
            'secret' => $secret,
            'name' => $name,
            'description' => $description,
            'redirect_uri' => $redirectUri,
            'scopes' => serialize(['basic']),
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
        $sql = 'SELECT * FROM '.$this->getTableName().' WHERE id = :id';
        $conn = $this->store->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('id', $clientIdentifier);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function findAll()
    {
        $sql = 'SELECT * FROM '.$this->getTableName();
        $conn = $this->store->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTableName()
    {
        return $this->store->getPrefix().'_oauth2_client';
    }
}