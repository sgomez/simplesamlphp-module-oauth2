<?php

use OAuth2\Model\IOAuth2Client;

class sspmod_oauth2_Model_OAuth2Client implements IOAuth2Client
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var array
     */
    private $redirectUris;

    /**
     * @var array
     */
    private $allowedOrigins;

    /**
     * @var null|string
     */
    private $secret;


    function __construct( $id, $name, $description, array $redirectUris, array $allowedOrigins, $secret=null )
    {
//        assert('is_string($clientId)');
//        assert('is_string($clientSecret)');
//        assert('is_string($name)');
//        assert('is_string($description)');
//        assert('is_string($redirectUri)');
//        assert('is_string($allowedOrigins)');

        $this->setPublicId( $id );
        $this->setName( $name );
        $this->setDescription( $description );
        $this->setRedirectUris( $redirectUris );
        $this->setAllowedOrigins( $allowedOrigins );
        $this->setSecret( $secret );
    }

    public static function newFromArray($data)
    {
        return new sspmod_oauth2_Model_OAuth2Client(
            $data['client_id'],
            $data['name'],
            $data['description'],
            $data['redirect_uri'],
            $data['allowed_origins'],
            $data['client_secret']
        );
    }

    /**
     * @return string
     */
    public function getPublicId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setPublicId( $id )
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName( $name )
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription( $description )
    {
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function getRedirectUris()
    {
        return $this->redirectUris;
    }

    /**
     * @param array $redirectUris
     */
    public function setRedirectUris( array $redirectUris )
    {
        $this->redirectUris = $redirectUris;
    }

    /**
     * @return array
     */
    public function getAllowedOrigins()
    {
        return $this->allowedOrigins;
    }

    /**
     * @param array $allowedOrigins
     */
    public function setAllowedOrigins( array $allowedOrigins )
    {
        $this->allowedOrigins = $allowedOrigins;
    }

    /**
     * @return null|string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param null|string $secret
     */
    public function setSecret( $secret )
    {
        $this->secret = $secret;
    }

    /**
     * @param mixed $secret
     *
     * @return boolean
     */
    public function checkSecret($secret)
    {
        return $this->secret === null || $secret === $this->secret;
    }

    public function toArray()
    {
        return array(
            'client_id' => $this->id,
            'client_secret' => $this->secret,
            'name' => $this->name,
            'description' => $this->description,
            'redirect_uri' => $this->redirectUris,
            'allowed_origins' => $this->allowedOrigins,
        );
    }
}