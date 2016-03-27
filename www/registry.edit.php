<?php

use SimpleSAML\Modules\OAuth2\Form\Client;
use SimpleSAML\Modules\OAuth2\Repositories\ClientRepository;
use SimpleSAML\Utils\Auth;
use SimpleSAML\Utils\HTTP;
use SimpleSAML\Utils\Random;

/* Load simpleSAMLphp, configuration and metadata */
$config = SimpleSAML_Configuration::getInstance();
$session = SimpleSAML_Session::getSessionFromRequest();
$oauthconfig = SimpleSAML_Configuration::getOptionalConfig( 'module_oauth2.php' );

Auth::requireAdmin();

$clientRepository = new ClientRepository();

if (array_key_exists( 'editkey', $_REQUEST) ) {
    $entry = $clientRepository->find($_REQUEST['editkey']);
} else {
    $entry = [
        'id' => Random::generateID(),
        'secret' => Random::generateID(),
    ];
}

$editor = new Client();

if ( isset( $_POST['submit'] ) ) {
    $editor->checkForm( $_POST );

    $new = $editor->formToMeta( $_POST, [], [] );
    $entry = array_merge( $entry, $new );
    $clientRepository->persistNewClient(
        $entry['id'],
        $entry['secret'],
        $entry['name'],
        $entry['description'],
        $entry['redirect_uri']
    );

    HTTP::redirectTrustedURL( 'registry.php' );
}

$form = $editor->metaToForm($entry);

$template = new SimpleSAML_XHTML_Template( $config, 'oauth2:registry.edit.php' );
$template->data['form'] = $form;
$template->show();