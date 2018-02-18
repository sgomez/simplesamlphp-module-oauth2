<?php

/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$form = (string) $this->data['form'];

$this->data['jquery'] = ['core' => true, 'ui' => true, 'css' => true];
$this->data['head'] = '<link rel="stylesheet" type="text/css" href="/'.$this->data['baseurlpath'].'module.php/oauth2/resources/style.css" />'."\n";
$this->includeAtTemplateBase('includes/header.php');

$moduleurlpath = '/'.$this->data['baseurlpath'].'/module.php/oauth2/';

$page = <<< EOD
    <h1>Oauth2 Client Registry</h1>
    <p>Here you can register a new OAuth2 Clients.</p>

    {$form}
EOD;

echo $page;

$this->includeAtTemplateBase('includes/footer.php');
