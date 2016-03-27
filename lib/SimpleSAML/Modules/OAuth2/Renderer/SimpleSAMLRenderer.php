<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Modules\OAuth2\Renderer;


use League\OAuth2\Server\TemplateRenderer\TwigRenderer;

class SimpleSAMLRenderer extends TwigRenderer
{
    /**
     * @inheritDoc
     */
    protected function render($template, array $data = [])
    {
        $oauth2config = \SimpleSAML_Configuration::getConfig('module_oauth2.php');
        $data['title'] = $oauth2config->getString('title');
        $data['logo'] = $oauth2config->getString('logo');

        return parent::render($template, $data);
    }
}