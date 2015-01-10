<?php

use OAuth2\OAuth2ServerException;

$store = new sspmod_oauth2_OAuth2Store();
$oauth = new sspmod_oauth2_OAuth2($store);

try {
    $response = $oauth->grantAccessToken();
    $response->send();
} catch (OAuth2ServerException $oauthError) {
    $oauthError->getHttpResponse()->send();
}
