<?php

$this->data['jquery'] = array('core' => TRUE, 'ui' => TRUE, 'css' => TRUE);
$this->data['head']  = '<link rel="stylesheet" type="text/css" href="/' . $this->data['baseurlpath'] . 'module.php/oauth2/resources/style.css" />' . "\n";
$this->includeAtTemplateBase('includes/header.php');

$moduleurlpath = '/' . $this->data['baseurlpath'].'/module.php/oauth2/';

if (!empty($this->data['clients'])) {
    $clients = [];

    foreach ($this->data['clients'] as $client) {
        $urls = array_reduce($client['redirect_uri'], function($initial, $item) {
            return sprintf('%s <li>%s</li>', $initial, $item);
        }, '');

        $clients[] = <<< EOD
        <table class="metalist">
            <tr>
                <th class="desc">Name</td>
                <td class="data">{$client['name']}</td>
            </tr>
            <tr>
                <th class="desc">Description</td>
                <td class="data">{$client['description']}</td>
            </tr>
            <tr>
                <th class="desc">Client ID</td>
                <td class="data">{$client['id']}</td>
            </tr>
            <tr>
                <th class="desc">Client Secret</td>
                <td class="data">{$client['secret']}</td>
            </tr>
            <tr>
                <th class="desc">Redirect URIs</td>
                <td class="data">
                    <ul>
                        {$urls}
                    </ul>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <a href="{$moduleurlpath}registry.edit.php?id={$client['id']}" class="button">Edit config</a>
                    <a href="{$moduleurlpath}registry.php?restore={$client['id']}" class="button">Restore secret</a>
                    <a href="{$moduleurlpath}registry.php?delete={$client['id']}" class="button">Delete</a>
                </td>
            </tr>
        </table>
        <hr/>
        
EOD;
    }

    $clients = implode(' ', $clients);
} else {

    $clients = <<< EOD
    <table class="metalist">
        <tr>
            <td>No clients registered</td>
        </tr>
    </table>
EOD;
}

$page = <<< EOD
    <h1>Oauth2 Client Registry</h1>
    <p>Here you can register new OAuth2 Clients.</p>
    
    <h2>Your clients</h2>
    
    {$clients}

    <a href="{$moduleurlpath}registry.new.php" class="button">Add new client</a>
EOD;

echo($page);

$this->includeAtTemplateBase('includes/footer.php');
