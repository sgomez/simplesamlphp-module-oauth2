<?php

use SimpleSAML\Modules\OAuth2\Repositories\ClientRepository;
use SimpleSAML\Utils\Auth;
use SimpleSAML\Utils\HTTP;

$config = SimpleSAML_Configuration::getInstance();
$session = SimpleSAML_Session::getSessionFromRequest();
$oauthconfig = SimpleSAML_Configuration::getOptionalConfig( 'module_oauth2.php' );

Auth::requireAdmin();

$clientRepository = new ClientRepository();

if ( isset( $_REQUEST['delete'] ) ) {
    $client = $clientRepository->delete($_REQUEST['delete']);
    HTTP::redirectTrustedURL( 'registry.php' );
}

if (isset($_REQUEST['restore'])) {
    $client = $store->getClient($_REQUEST['restore']);
    if ( $client ) {
        $store->restoreClient( $client );
    }
    HTTP::redirectTrustedURL( 'registry.php' );
}

$clients = $clientRepository->findAll();

$template = new SimpleSAML_XHTML_Template( $config, 'oauth2:registry.list.php' );
$template->data['clients'] = $clients;
$template->show();