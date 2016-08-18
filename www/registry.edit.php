<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use SimpleSAML\Modules\OAuth2\Form\ClientForm;
use SimpleSAML\Modules\OAuth2\Repositories\ClientRepository;
use SimpleSAML\Utils\Auth;
use SimpleSAML\Utils\HTTP;

Auth::requireAdmin();

/* Load simpleSAMLphp, configuration and metadata */
$client_id = $_REQUEST['id'];
$action = \SimpleSAML\Module::getModuleURL('oauth2/registry.edit.php', ['id' => $client_id]);
$config = SimpleSAML_Configuration::getInstance();

$clientRepository = new ClientRepository();
$client = $clientRepository->find($client_id);
if (!$client) {
    header('Content-type: text/plain; utf-8', TRUE, 500);

    print('Client not found');
    return;
}

$form = new ClientForm('client');
$form->setAction($action);
$form->setDefaults($client);

if ($form->isSubmitted() && $form->isSuccess()) {
    $client = $form->getValues();

    $clientRepository->updateClient(
        $client_id,
        $client['name'],
        $client['description'],
        $client['redirect_uri']
    );

    HTTP::redirectTrustedURL( 'registry.php' );
}

$template = new SimpleSAML_XHTML_Template( $config, 'oauth2:registry_edit' );
$template->data['form'] = $form;
$template->show();