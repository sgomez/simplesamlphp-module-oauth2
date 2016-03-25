<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use SimpleSAML\Modules\OAuth2\OAuth2Server;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$server = new OAuth2Server();

$request = Request::createFromGlobals();
$response = Response::create();

$response = $server->getInstance()->respondToRequest($request, $response);

var_dump($response);

$config = SimpleSAML_Configuration::getInstance();
$template = new SimpleSAML_XHTML_Template( $config, 'oauth2:consent.php' );
$template->show();