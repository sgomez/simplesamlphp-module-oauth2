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


use Doctrine\DBAL\Connection;
use SimpleSAML\Modules\DBAL\Store\DBAL;

abstract class AbstractDBALRepository
{
    /**
     * @var DBAL
     */
    protected $store;

    /**
     * @var Connection
     */
    protected $conn;

    /**
     * ClientRepository constructor.
     */
    public function __construct()
    {
        $this->store = SimpleSAML_Store::getInstance();

        if (! $this->store instanceof DBAL) {
            throw new \SimpleSAML_Error_Exception('OAuth2 module: Only DBAL Store is supported');
        }

        $this->conn = $this->store->getConnection();
    }

    abstract public function getTableName();
}