<?php

/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Modules\OAuth2\Form;

use Nette\Forms\Controls\CheckboxList;

class ScopeCheckList extends CheckboxList
{
    public function __construct($label = null)
    {
        $oauth2config = \SimpleSAML_Configuration::getOptionalConfig('module_oauth2.php');

        $items = array_map(function ($item) {
            return $item['description'];
        }, $oauth2config->getArray('scopes'));

        parent::__construct($label, $items);
        $this->setRequired(true);
    }
}
