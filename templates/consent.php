<?php

$this->data['jquery'] = array('version' => '1.6', 'core' => TRUE, 'ui' => TRUE, 'css' => TRUE);
$this->data['head'] = '<link rel="stylesheet" type="text/css" href="/' . $this->data['baseurlpath'] . 'module.php/oauth2/resources/style.css" />' . "\n";
$this->data['header'] = 'OAuth2 Authorization';
$this->includeAtTemplateBase('includes/header.php');

?>

    <p style="margin-top: 2em">
        Do you agree to let the application at <b><?php echo htmlspecialchars($this->data['client']->getName())?></b> use Foodle on your behalf?
    </p>

    <form method="GET" action="">
        <?php foreach ($this->data['auth_params'] as $key => $value) : ?>
            <input type="hidden" name="<?php echo htmlspecialchars($key, ENT_QUOTES); ?>" value="<?php echo htmlspecialchars($value, ENT_QUOTES); ?>" />
        <?php endforeach; ?>
        <hr/>
        <p>
            <input class="btn" type="submit" name="accept" value="Accept"  />
            <input class="btn" type="submit" name="cancel" value="Cancel" />
        </p>
    </form>

<?php
$this->includeAtTemplateBase('includes/footer.php');
?>