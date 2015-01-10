<?php

use OAuth2\IOAuth2GrantCode;
use OAuth2\IOAuth2RefreshTokens;
use OAuth2\Model\IOAuth2Client;
use OAuth2\Model\OAuth2AuthCode;
use OAuth2\Model\OAuth2Token;
use OAuth2\OAuth2;

class sspmod_oauth2_OAuth2Store implements IOAuth2GrantCode, IOAuth2RefreshTokens
{
    /** @var FALSE|SimpleSAML_Store_SQL  */
    private $store;

    private $allowedGrantTypes = array(OAuth2::GRANT_TYPE_AUTH_CODE);

    public function __construct()
    {
        $this->store = SimpleSAML_Store::getInstance();
        if (false === $this->store || false === $this->store instanceof SimpleSAML_Store_SQL) {
            throw new Exception("We don't have a SQL datastore", 1);
        }

        $this->createOAuth2ClientTables();
    }

    /**
     * Create OAUTH2 tableS in SQL, if it is missing.
     */
    private function createOAuth2ClientTables() {

        if ($this->store->getTableVersion('saml_OAuth2Store') === 1) {
            return;
        }

        $query = 'CREATE TABLE ' . $this->store->prefix . '_oauth2_AuthCodes (
            code varchar(200) NOT NULL,
            client_id varchar(200) NOT NULL,
            user_id varchar(200) NOT NULL,
            redirect_uri varchar(200) NOT NULL,
            expires int(11) NOT NULL,
            scope varchar(250) DEFAULT NULL,
            UNIQUE(code)
        )';
        $this->store->pdo->exec($query);

        $query = 'CREATE INDEX ' . $this->store->prefix . '_oauth2_AuthCodes_code ON '  . $this->store->prefix . '_oauth2_AuthCodes (code)';
        $this->store->pdo->exec($query);

        $query = 'CREATE TABLE ' . $this->store->prefix . '_oauth2_Clients (
            client_id varchar(200) NOT NULL,
            client_secret varchar(200) NOT NULL,
            name varchar(200) NOT NULL,
            description text NOT NULL,
            allowed_origins text NOT NULL,
            redirect_uri text NOT NULL,
            UNIQUE(client_id)
        )';
        $this->store->pdo->exec($query);

        $query = 'CREATE INDEX ' . $this->store->prefix . '_oauth2_Clients_clientId ON '  . $this->store->prefix . '_oauth2_Clients (client_id)';
        $this->store->pdo->exec($query);

        $query = 'CREATE TABLE ' . $this->store->prefix . '_oauth2_AccessTokens (
            oauth_token varchar(200) NOT NULL,
            client_id varchar(200) NOT NULL,
            user_id varchar(200) NOT NULL,
            expires int(11) NOT NULL,
            scope varchar(200) DEFAULT NULL,
            UNIQUE (oauth_token)
        )';
        $this->store->pdo->exec($query);

        $query = 'CREATE INDEX ' . $this->store->prefix . '_oauth2_AccessTokens_oauthToken ON '  . $this->store->prefix . '_oauth2_AccessTokens (oauth_token)';
        $this->store->pdo->exec($query);


        $query = 'CREATE TABLE ' . $this->store->prefix . '_oauth2_RefreshTokens (
            oauth_token varchar(200) NOT NULL,
            refresh_token varchar(200) NOT NULL,
            client_id varchar(200) NOT NULL,
            user_id varchar(200) NOT NULL,
            expires int(11) NOT NULL,
            scope varchar(200) DEFAULT NULL,
            UNIQUE (oauth_token)
        )';
        $this->store->pdo->exec($query);

        $query = 'CREATE INDEX ' . $this->store->prefix . '_oauth2_RefreshTokens_oauthToken ON '  . $this->store->prefix . '_oauth2_RefreshTokens (oauth_token)';
        $this->store->pdo->exec($query);


        $this->store->setTableVersion('saml_OAuth2Store', 1);
    }

    /**
     * Clean the logout table of expired entries.
     *
     * @return int Num of affected rows.
     */
    public function cleanOAuth2ClientStore() {

        SimpleSAML_Logger::debug('saml.OAuth2Store: Cleaning logout store.');

        $count = 0;

        $tables = array(
            'AuthCodes',
            'Clients',
            'AccessTokens',
            'RefreshTokens',
        );

        foreach ($tables as $table) {
            $sql = 'DELETE FROM ' . $this->store->prefix . '_oauth2_' . $table . ' WHERE _expire < :now';
            $params = array('now' => time());
            $stmt = $this->store->pdo->prepare($sql);
            $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
            $stmt->execute();

            $count += $stmt->rowCount();
        }

        return $count;
    }

    public function addClient(sspmod_oauth2_Model_OAuth2Client $client)
    {
        $data = $client->toArray();
        $data['redirect_uri'] = serialize($data['redirect_uri']);
        $data['allowed_origins'] = serialize($data['allowed_origins']);

        $this->store->insertOrUpdate($this->store->prefix . '_oauth2_Clients', array_keys($data), $data);
    }

    public function restoreClient(sspmod_oauth2_Model_OAuth2Client $client)
    {
        $tableName = $this->store->prefix . '_oauth2_Clients' ;
        $client->setSecret(SimpleSAML_Utilities::generateID());

        $this->addClient($client);
    }

    public function deleteClient(sspmod_oauth2_Model_OAuth2Client $client)
    {
        $sql = 'DELETE FROM ' . $this->store->prefix . '_oauth2_Clients WHERE client_id=:client_id';
        $stmt = $this->store->pdo->prepare($sql);
        $stmt->bindParam(':client_id', $client->getPublicId(), PDO::PARAM_STR);
        $stmt->execute();
    }

    public function getClients()
    {
        $sql = 'SELECT * FROM ' . $this->store->prefix . '_oauth2_Clients';
        $stmt = $this->store->pdo->prepare($sql);
        $stmt->execute();

        $results = array();
        while ( ($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== FALSE) {
            $row['redirect_uri'] = unserialize($row['redirect_uri']);
            $row['allowed_origins'] = unserialize($row['allowed_origins']);
            $results[] = sspmod_oauth2_Model_OAuth2Client::newFromArray($row);
        }

        return $results;
    }

    /**
     * Implements IOAuth2Storage::getClient()
     * {@inheritdoc}
     */
    public function getClient($clientId)
    {
        assert('is_string($clientId)');

        $sql = 'SELECT * FROM ' . $this->store->prefix . '_oauth2_Clients WHERE client_id=:client_id';
        $stmt = $this->store->pdo->prepare($sql);
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_STR);
        $stmt->execute();


        if ( $result = $stmt->fetch(PDO::FETCH_ASSOC) ) {
            $result['redirect_uri'] = unserialize($result['redirect_uri']);
            $result['allowed_origins'] = unserialize($result['allowed_origins']);
        }

        return $result !== false ? sspmod_oauth2_Model_OAuth2Client::newFromArray($result) : null;
    }

    /**
     * Implements IOAuth2Storage::checkClientCredentials().
     * {@inheritdoc}
     */
    public function checkClientCredentials(IOAuth2Client $client, $clientSecret = null)
    {
        $client = $this->getClient($client->getPublicId());

        return $client->checkSecret($clientSecret);
    }

    /**
     * Implements IOAuth2Storage::getAccessToken().
     * {@inheritdoc}
     */
    public function getAccessToken($oauthToken)
    {
        return $this->getToken($oauthToken, false);
    }

    /**
     * Implements IOAuth2Storage::createAccessToken().
     * {@inheritdoc}
     */
    public function createAccessToken($oauthToken, IOAuth2Client $client, $data, $expires, $scope = null)
    {
        $this->setToken($oauthToken, $client->getPublicId(), $data, $expires, $scope, false);
    }

    /**
     * Implements IOAuth2Storage::checkRestrictedGrantType().
     * {@inheritdoc}
     */
    public function checkRestrictedGrantType(IOAuth2Client $client, $grantType)
    {
        return in_array($grantType, $this->allowedGrantTypes);
    }

    /**
     * Implements IOAuth2GrantCode::getAuthCode().
     * {@inheritdoc}
     */
    public function getAuthCode($code)
    {

        $tableName = $this->store->prefix . '_oauth2_AuthCodes' ;
        $sql = 'SELECT code, client_id, user_id, redirect_uri, expires, scope FROM '. $tableName .' auth_codes WHERE code = :code';
        $stmt = $this->store->pdo->prepare($sql);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new OAuth2AuthCode(
                $result['client_id'],
                $result['code'],
                $result['expires'],
                $result['scope'],
                $result['user_id'],
                $result['redirect_uri']
            );
        }

        return null;
    }

    /**
     * Implements IOAuth2GrantCode::getAuthCode().
     * {@inheritdoc}
     */
    public function createAuthCode($code, IOAuth2Client $client, $userId, $redirectUri, $expires, $scope = null)
    {
        $id = $client->getPublicId();

        $tableName = $this->store->prefix . '_oauth2_AuthCodes' ;
        $sql = 'INSERT INTO '.$tableName.' (code, client_id, user_id, redirect_uri, expires, scope) VALUES (:code, :client_id, :user_id, :redirect_uri, :expires, :scope)';
        $stmt = $this->store->pdo->prepare($sql);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':client_id', $id, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
        $stmt->bindParam(':redirect_uri', $redirectUri, PDO::PARAM_STR);
        $stmt->bindParam(':expires', $expires, PDO::PARAM_INT);
        $stmt->bindParam(':scope', $scope, PDO::PARAM_STR);

        $stmt->execute();
    }

    /**
     * Implements IOAuth2GrantCode::getAuthCode().
     * {@inheritdoc}
     */
    public function markAuthCodeAsUsed($code)
    {
        $tableName = $this->store->prefix . '_oauth2_AuthCodes' ;
        $sql = 'DELETE FROM '.$tableName.' WHERE code = :code';
        $stmt = $this->store->pdo->prepare($sql);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);

        $stmt->execute();
    }

    /**
     * Implements IOAuth2RefreshTokens::getRefreshToken().
     * {@inheritdoc}
     */
    public function getRefreshToken($refreshToken)
    {
        return $this->getToken($refreshToken);
    }

    /**
     * Implements IOAuth2RefreshTokens::createRefreshToken().
     * {@inheritdoc}
     */
    public function createRefreshToken($refreshToken, IOAuth2Client $client, $data, $expires, $scope = null)
    {
        $this->setToken($refreshToken, $client->getPublicId(), $data, $expires, $scope);
    }

    public function unsetRefreshToken($refreshToken)
    {
        $tableName = $this->store->prefix . '_oauth2_RefreshTokens' ;
        $sql = 'DELETE FROM '.$tableName.' WHERE refresh_token = :refresh_token';
        $stmt = $this->store->pdo->prepare($sql);
        $stmt->bindParam(':refresh_token', $refreshToken, PDO::PARAM_STR);

        $stmt->execute();
    }

    /**
     * Implements IOAuth2Storage::createAccessToken().
     * {@inheritdoc}
     */
    protected function setToken($token, $clientId, $userId, $expires, $scope, $isRefresh = true)
    {
        $tableName = $isRefresh ? $this->store->prefix . '_oauth2_RefreshTokens' :  $this->store->prefix . '_oauth2_AccessTokens';

        $sql = "INSERT INTO $tableName (oauth_token, client_id, user_id, expires, scope) VALUES (:token, :client_id, :user_id, :expires, :scope)";
        $stmt = $this->store->pdo->prepare($sql);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
        $stmt->bindParam(':expires', $expires, PDO::PARAM_INT);
        $stmt->bindParam(':scope', $scope, PDO::PARAM_STR);

        $stmt->execute();
    }

    /**
     * Retrieves an access or refresh token.
     *
     * @param string $token
     * @param bool   $isRefresh
     *
     * @return array|null
     */
    protected function getToken($token, $isRefresh = true)
    {
        $tableName = $isRefresh ? $this->store->prefix . '_oauth2_RefreshTokens' :  $this->store->prefix . '_oauth2_AccessTokens';
        $tokenName = $isRefresh ? 'refresh_token' : 'oauth_token';

        $sql = "SELECT oauth_token AS $tokenName, client_id, expires, scope, user_id FROM $tableName WHERE oauth_token = :token";
        $stmt = $this->store->pdo->prepare($sql);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
           return new OAuth2Token(
               $result['client_id'],
               $result[$tokenName],
               $result['expires'],
               $result['scope'],
               $result['user_id']
           );
        }

        return null;
    }

}