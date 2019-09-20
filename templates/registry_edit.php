<?php
$form = (string) $this->data['form'];

$this->data['jquery'] = array('core' => TRUE, 'ui' => TRUE, 'css' => TRUE);
$this->data['head']  = '<link rel="stylesheet" type="text/css" href="/' . $this->data['baseurlpath'] . 'module.php/oauth2/resources/style.css" />' . "\n";
$this->includeAtTemplateBase('includes/header.php');

$moduleurlpath = '/' . $this->data['baseurlpath'].'module.php/oauth2/';

$page = <<< EOD
    <h1>Oauth2 Client Registry</h1>
    <p>Here you can edit an OAuth2 client.</p>

    {$form}
EOD;

echo($page);

$this->includeAtTemplateBase('includes/footer.php');
