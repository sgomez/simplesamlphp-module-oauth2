<?php

use OAuth2\OAuth2ServerException;

$store = new sspmod_oauth2_OAuth2Store();
$oauth = new sspmod_oauth2_OAuth2($store);

try {
    $bearer = $oauth->getBearerToken();
    $token = $oauth->verifyAccessToken($bearer);
} catch (OAuth2ServerException $oauthError) {
    $oauthError->sendHttpResponse();
}

echo $token->getData();