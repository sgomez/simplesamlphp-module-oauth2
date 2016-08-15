<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Modules\OAuth2\Repositories;


use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use SimpleSAML\Modules\OAuth2\Entity\ScopeEntity;

class ScopeRepository implements ScopeRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        $oauth2config = \SimpleSAML_Configuration::getConfig('module_oauth2.php');

        $scopes = $oauth2config->getArray('scopes');

        if (array_key_exists($identifier, $scopes) === false) {
            return;
        }

        $scope = new ScopeEntity();
        $scope->setIdentifier($identifier);
        $scope->setIcon($scopes[$identifier]['icon']);
        $scope->setDescription($scopes[$identifier]['description']);
        $scope->setAttributes($scopes[$identifier]['attributes']);

        return $scope;
    }

    /**
     * @inheritDoc
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        return $scopes;
    }

}