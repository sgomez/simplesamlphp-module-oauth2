<?php
/*
 * This file is part of the simplesamlphp.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Modules\OAuth2\Form;


use Nette\Forms\Form;
use SimpleSAML\Module;

class ClientForm extends Form
{
    /**
     * @inheritDoc
     */
    public function __construct($name)
    {
        parent::__construct($name);

        $this->onValidate[] = [$this, 'validateRedirectUri'];

        $this->setMethod('POST');
        $this->addProtection('Security token has expired, please submit the form again');

        $this->addText('name', 'Name of client:')
            ->setMaxLength(255)
            ->setRequired('Set a name')
        ;
        $this->addTextArea('description', 'Description of client:', null, 5);
        $this->addTextArea('redirect_uri', 'Static/enforcing callback-url (one per line)', null, 5)
            ->setRequired('Write one redirect URI at least')
        ;

        $this->addSubmit('submit', 'Submit');
        $this->addButton('return', 'Return')
            ->setAttribute('onClick', 'parent.location = \''. Module::getModuleURL('oauth2/registry.php') .'\'')
        ;
    }

    public function validateRedirectUri($form)
    {
        $values = $this->getValues();
        $redirect_uris = $values['redirect_uri'];
        foreach ($redirect_uris as $redirect_uri) {
            if (false === filter_var($redirect_uri, FILTER_VALIDATE_URL)) {
                $this->addError('Invalid URI: '.$redirect_uri);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getValues($asArray = false)
    {
        $values = parent::getValues(true);

        // Sanitize Redirect URIs
        $redirect_uris = preg_split("/[\t\r\n]+/", $values['redirect_uri']);
        $redirect_uris = array_filter($redirect_uris, function($redirect_uri) {
            return !empty(trim($redirect_uri));
        });
        $values['redirect_uri'] = $redirect_uris;

        return $values;
    }

    /**
     * @inheritDoc
     */
    public function setDefaults($values, $erase = false)
    {
        $values['redirect_uri'] = implode("\n", $values['redirect_uri']);

        return parent::setDefaults($values, $erase);
    }
}