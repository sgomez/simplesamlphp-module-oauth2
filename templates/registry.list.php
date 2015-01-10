<?php

$this->data['jquery'] = array('version' => '1.6', 'core' => TRUE, 'ui' => TRUE, 'css' => TRUE);
$this->data['head'] = '<link rel="stylesheet" type="text/css" href="/' . $this->data['baseurlpath'] . 'module.php/oauth2/resources/style.css" />' . "\n";

$echorow = function($desc, $data) {
	 echo "<tr><td class='desc'>$desc</td><td class='data'>$data</td></tr>";
};

$this->includeAtTemplateBase('includes/header.php');

echo '<h1>OAuth2 Client Registry</h1>';
echo('<p>Here you can register new OAuth2 Clients.</p>');

echo('<h2>Your clients</h2>');
if (!empty($this->data['clients'])) {
	/** @var sspmod_oauth2_Model_OAuth2Client $client */
	foreach ($this->data['clients'] as $client) {

		echo ('<table class="metalist">');
		$echorow('Name', htmlspecialchars($client->getName()));
		$echorow('Description', htmlspecialchars($client->getDescription()));
		$echorow('Client ID', htmlspecialchars($client->getPublicId()));
		$echorow('Client Secret', htmlspecialchars($client->getSecret()));
		$echorow('Redirect URIs', nl2br(htmlspecialchars( implode( "\n" , $client->getRedirectUris() ) ) ) );
		$echorow('Javascript Origins', nl2br(htmlspecialchars( implode( "\n", $client->getAllowedOrigins() ) ) ) );
		echo ('<tr><td colspan="2">
					 <a class="btn" href="registry.edit.php?editkey=' . urlencode($client->getPublicId()) . '">Edit Config</a>
					 <a class="btn confirm" href="registry.php?restore=' . urlencode($client->getPublicId()) . '">Restore Secret</a>
					 <a class="btn confirm" href="registry.php?delete=' . urlencode($client->getPublicId()) . '">Delete</a>
					 </td></tr>');
		echo ('</table>');
		echo ('<hr/>');
	}
} else {
  echo('<table class="metalist" style="width: 100%">');
	echo('<tr><td colspan="4">No entries registered</td></tr>');
  echo('</table>');
}

echo('<p><a class="btn" href="registry.edit.php">Add new client</a></p>');

$this->includeAtTemplateBase('includes/footer.php');
