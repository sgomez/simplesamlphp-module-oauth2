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
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use SimpleSAML\Modules\OAuth2\Entity\ScopeEntity;

class ScopeRepository implements ScopeRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        $oauth2config = \SimpleSAML_Configuration::getConfig('module_oauth2.php');

        $scopes = $oauth2config->getArray('scopes');

        if (array_key_exists($identifier, $scopes) === false) {
            return null;
        }

        $scope = $scopes[$identifier];
        $icon = isset($scope['icon']) ? $scope['icon'] : null;
        $description = isset($scope['description']) ? $scope['description'] : null;
        $attributes = isset($scope['attributes']) ? $scope['attributes'] : null;

        $scope = new ScopeEntity();
        $scope->setIdentifier($identifier);
        $scope->setIcon($icon);
        $scope->setDescription($description);
        $scope->setAttributes($attributes);

        return $scope;
    }

    /**
     * {@inheritdoc}
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        return array_filter($scopes, function (ScopeEntityInterface $scope) use ($clientEntity) {
            return in_array($scope->getIdentifier(), $clientEntity->getScopes());
        });
    }
}
