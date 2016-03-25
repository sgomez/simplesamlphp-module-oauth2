<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


$this->data['jquery'] = array('version' => '1.6', 'core' => TRUE, 'ui' => TRUE, 'css' => TRUE);
$this->data['head'] = '<link rel="stylesheed" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.1.8/semantic.min.css"/>' . "\n";
$this->data['header'] = 'OAuth2 Authorization';
$this->includeAtTemplateBase('includes/header.php');

?>

Cool...

<?php
$this->includeAtTemplateBase('includes/footer.php');
?>