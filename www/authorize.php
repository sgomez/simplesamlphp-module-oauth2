<?php

use OAuth2\OAuth2ServerException;
use Symfony\Component\HttpFoundation\Request;

$store = new sspmod_oauth2_OAuth2Store();
$oauth = new sspmod_oauth2_OAuth2($store);
$request = Request::createFromGlobals();

try {

    $config = SimpleSAML_Configuration::getInstance();
    $session = SimpleSAML_Session::getSessionFromRequest();
    $oauthconfig = SimpleSAML_Configuration::getOptionalConfig( 'module_oauth2.php' );

    try {
        $auth_params = $oauth->getAuthorizeParams();
    } catch (OAuth2ServerException $oauthError) {
        $oauthError->sendHttpResponse();
    }

    $as = $oauthconfig->getString('auth');
    if (!$session->isValid($as)) {
        SimpleSAML_Auth_Default::initLogin($as, SimpleSAML_Utilities::selfURL());
    }

    $userId = json_encode( $session->getAuthData( $as, 'Attributes' ) );

    try {
        if ( $request->get( 'accept', false ) ) {
        $response = $oauth->finishClientAuthorization(true, $userId, $request, $request->get('scope', null));
        $response->send();
        } elseif ( $request->get ( 'cancel', false ) ) {
            $response = $oauth->finishClientAuthorization(false, null);
            $response->send();
        }
    } catch (OAuth2ServerException $oauthError) {
        $oauthError->sendHttpResponse();
    }

    $template = new SimpleSAML_XHTML_Template( $config, 'oauth2:consent.php' );
    $template->data['client'] = $auth_params['client'];
    $template->data['auth_params'] = $auth_params;
    unset($template->data['auth_params']['client']);
    $template->data['url'] = SimpleSAML_Module::getModuleURL( 'oauth2/authorize.php' );
    $template->show();

} catch (Exception $e) {

    header('Content-type: text/plain; utf-8', TRUE, 500);
    header('OAuth-Error: ' . $e->getMessage());

    print_r($e);

}