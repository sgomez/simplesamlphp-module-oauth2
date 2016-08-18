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
use SimpleSAML\Utils\Random;

/* Load simpleSAMLphp, configuration and metadata */
$action = \SimpleSAML\Module::getModuleURL('oauth2/registry.new.php');
$config = SimpleSAML_Configuration::getInstance();

Auth::requireAdmin();

$form = new ClientForm('client');
$form->setAction($action);

if ( $form->isSubmitted() && $form->isSuccess() ) {
    $client = $form->getValues();
    $client['id'] = Random::generateID();
    $client['secret'] = Random::generateID();

    $clientRepository = new ClientRepository();
    $clientRepository->persistNewClient(
        $client['id'],
        $client['secret'],
        $client['name'],
        $client['description'],
        $client['redirect_uri']
    );

    HTTP::redirectTrustedURL( 'registry.php' );
}

$template = new SimpleSAML_XHTML_Template( $config, 'oauth2:registry_new' );
$template->data['form'] = $form;
$template->show();