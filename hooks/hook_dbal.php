<?php

function oauth2_hook_dbal(&$dbinfo)
{
    $store = SimpleSAML_Store::getInstance();

    if (! $store instanceof \SimpleSAML\Modules\DBAL\Store\DBAL ) {
        throw new \SimpleSAML_Error_Exception('OAuth2 module: Only DBAL Store is supported');
    }

    $schema = new \Doctrine\DBAL\Schema\Schema();

    $clientTable = $store->getPrefix().'_oauth2_client';
    $client = $schema->createTable($clientTable);
    $client->addColumn('id', 'string', [ 'length' => 255 ]);
    $client->addColumn('secret', 'string', [ 'length' => 255 ]);
    $client->addColumn('name', 'string', [ 'length' => 255 ]);
    $client->addColumn('description', 'text', [ 'notnull' => false ]);
    $client->addColumn('redirect_uri', 'json_array');
    $client->addColumn('scopes', 'json_array');
    $client->setPrimaryKey(['id']);

    $accesstokenTable = $store->getPrefix().'_oauth2_accesstoken';
    $accesstoken = $schema->createTable($accesstokenTable);
    $accesstoken->addColumn('id', 'string', [ 'length' => 255 ]);
    $accesstoken->addColumn('scopes', 'simple_array', [ 'notnull' => false ]);
    $accesstoken->addColumn('expires_at', 'datetime');
    $accesstoken->addColumn('user_id', 'string', [ 'length' => 255 ]);
    $accesstoken->addColumn('client_id', 'string', [ 'length' => 255 ]);
    $accesstoken->addColumn('is_revoked', 'boolean', [ 'default' => false ]);
    $accesstoken->setPrimaryKey(['id']);
    $accesstoken->addForeignKeyConstraint($clientTable, ['client_id'], ['id']);

    $refreshtokenTable = $store->getPrefix().'_oauth2_refreshtoken';
    $refreshtoken = $schema->createTable($refreshtokenTable);
    $refreshtoken->addColumn('id', 'string', [ 'length' => 255 ]);
    $refreshtoken->addColumn('expires_at', 'datetime');
    $refreshtoken->addColumn('accesstoken_id', 'string', [ 'length' => 255 ]);
    $refreshtoken->addColumn('is_revoked', 'boolean', [ 'default' => false ]);
    $refreshtoken->setPrimaryKey(['id']);
    $refreshtoken->addForeignKeyConstraint($accesstokenTable, ['accesstoken_id'], ['id']);

    $authcodeTable = $store->getPrefix().'_oauth2_authcode';
    $authcode = $schema->createTable($authcodeTable);
    $authcode->addColumn('id', 'string', [ 'length' => 255 ]);
    $authcode->addColumn('scopes', 'simple_array');
    $authcode->addColumn('expires_at', 'datetime');
    $authcode->addColumn('user_id', 'string', [ 'length' => 255 ]);
    $authcode->addColumn('client_id', 'string', [ 'length' => 255 ]);
    $authcode->addColumn('is_revoked', 'boolean', [ 'default' => false ]);
    $authcode->addColumn('redirect_uri', 'text');
    $authcode->addForeignKeyConstraint($clientTable, ['client_id'], ['id']);

    $store->createOrUpdateSchema($schema, $store->getPrefix().'_oauth2');

    $dbinfo['summary'][] = 'Created OAuth2 Schema';
}
