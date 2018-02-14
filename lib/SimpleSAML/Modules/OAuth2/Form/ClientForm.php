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

class ClientForm extends Form
{
    /**
     * RFC3986. AppendixB. Parsing a URI Reference with a Regular Expression
     */
    const REGEX_URI = '/^[^:]+:\/\/?[^\s\/$.?#].[^\s]*$/';

    /**
     * {@inheritdoc}
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
        $this->addSelect('auth_source', 'Authorization source:')
            ->setItems(\SimpleSAML_Auth_Source::getSources(), false)
            ->setPrompt('Pick an AuthSource or blank for default')
            ->setRequired(false)
        ;
        $scopeCheckList = new ScopeCheckList('Scopes');
        $this->addComponent($scopeCheckList, 'scopes');

        $this->addSubmit('submit', 'Submit');
        $this->addButton('return', 'Return')
            ->setAttribute('onClick', 'parent.location = \''.\SimpleSAML_Module::getModuleURL('oauth2/registry.php').'\'')
        ;
    }

    /**
     * @param Form $form
     */
    public function validateRedirectUri($form)
    {
        $values = $form->getValues();
        $redirect_uris = $values['redirect_uri'];
        foreach ($redirect_uris as $redirect_uri) {
            if (!preg_match(self::REGEX_URI, $redirect_uri)) {
                $this->addError('Invalid URI: '.$redirect_uri);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getValues($asArray = false)
    {
        $values = parent::getValues(true);

        // Sanitize Redirect URIs
        $redirect_uris = preg_split("/[\t\r\n]+/", $values['redirect_uri']);
        $redirect_uris = array_filter($redirect_uris, function ($redirect_uri) {
            return !empty(trim($redirect_uri));
        });
        $values['redirect_uri'] = $redirect_uris;

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaults($values, $erase = false)
    {
        $values['redirect_uri'] = implode("\n", $values['redirect_uri']);

        return parent::setDefaults($values, $erase);
    }
}
