<?php

$config = SimpleSAML_Configuration::getInstance();
$session = SimpleSAML_Session::getSessionFromRequest();
$oauthconfig = SimpleSAML_Configuration::getOptionalConfig( 'module_oauth2.php' );

SimpleSAML_Utilities::requireAdmin();

$store = new sspmod_oauth2_OAuth2Store();

if ( isset( $_REQUEST['delete'] ) ) {
    $client = $store->getClient( $_REQUEST['delete'] );
    if ( $client ) {
      $store->deleteClient( $client );
    }
  SimpleSAML_Utilities::redirectTrustedURL( 'registry.php' );
}

if (isset($_REQUEST['restore'])) {
  $client = $store->getClient($_REQUEST['restore']);
  if ( $client ) {
    $store->restoreClient( $client );
  }
  SimpleSAML_Utilities::redirectTrustedURL( 'registry.php' );
}

$clients = $store->getClients();

$template = new SimpleSAML_XHTML_Template( $config, 'oauth2:registry.list.php' );
$template->data['clients'] = $clients;
$template->show();