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
use OAuth2ServerExamples\Entities\ClientEntity;
use SimpleSAML\Modules\DBAL\Store\DBAL;

class ClientRepository implements ClientRepositoryInterface
{
    /**
     * @var DBAL
     */
    private $store;

    /**
     * ClientRepository constructor.
     */
    public function __construct()
    {
        $this->store = SimpleSAML_Store::getInstance();

        if (! $this->store instanceof DBAL) {
            throw new \SimpleSAML_Error_Exception('OAuth2 module: Only DBAL Store is supported');
        }
    }

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

        $client = new ClientEntity();
        $client->setIdentifier($clientIdentifier);
        $client->setName($entity['name']);
        $client->setRedirectUri($entity['redirect_uri']);
        $client->setSecret($entity['secret']);

        return $client;
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

    public function getTableName()
    {
        return $this->store->getPrefix().'_oauth2_client';
    }
}