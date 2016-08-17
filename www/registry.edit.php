<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use SimpleSAML\Modules\OAuth2\Form\Client;
use SimpleSAML\Modules\OAuth2\Repositories\ClientRepository;
use SimpleSAML\Utils\Auth;
use SimpleSAML\Utils\HTTP;

/* Load simpleSAMLphp, configuration and metadata */
$config = SimpleSAML_Configuration::getInstance();
$session = SimpleSAML_Session::getSessionFromRequest();
$oauthconfig = SimpleSAML_Configuration::getOptionalConfig( 'module_oauth2.php' );

Auth::requireAdmin();

$clientRepository = new ClientRepository();
$editor = new Client();

if ( isset( $_POST['submit'] ) ) {
    $editor->checkForm( $_POST );

    $entry = $editor->formToMeta( $_POST, [], [] );

    $clientRepository->updateClient(
        $entry['id'],
        $entry['name'],
        $entry['description'],
        $entry['redirect_uri']
    );

    HTTP::redirectTrustedURL( 'registry.php' );
}

$entry = $clientRepository->find($_REQUEST['editkey']);

$form = $editor->metaToForm($entry);

$template = new SimpleSAML_XHTML_Template( $config, 'oauth2:registry.edit.php' );
$template->data['form'] = $form;
$template->show();