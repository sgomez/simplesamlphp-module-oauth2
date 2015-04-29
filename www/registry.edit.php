<?php

use SimpleSAML\Utils\Auth;
use SimpleSAML\Utils\HTTP;

/* Load simpleSAMLphp, configuration and metadata */
$config = SimpleSAML_Configuration::getInstance();
$session = SimpleSAML_Session::getSessionFromRequest();
$oauthconfig = SimpleSAML_Configuration::getOptionalConfig( 'module_oauth2.php' );

Auth::requireAdmin();

$store = new sspmod_oauth2_OAuth2Store();

if (array_key_exists( 'editkey', $_REQUEST) ) {
    $entry = $store->getClient( $_REQUEST['editkey'] )->toArray();
} else {
    $entry = array(
        'client_id' => SimpleSAML_Utilities::generateID(),
        'client_secret' => SimpleSAML_Utilities::generateID(),
    );
}

$editor = new sspmod_oauth2_Registry();

if ( isset( $_POST['submit'] ) ) {
    $editor->checkForm( $_POST );

    $new = $editor->formToMeta( $_POST, array(), array() );
    $entry = array_merge( $entry, $new );
    $client = sspmod_oauth2_Model_OAuth2Client::newFromArray( $entry );
    $store->addClient( $client );
    HTTP::redirectTrustedURL( 'registry.php' );
}

$form = $editor->metaToForm($entry);

$template = new SimpleSAML_XHTML_Template( $config, 'oauth2:registry.edit.php' );
$template->data['form'] = $form;
$template->show();