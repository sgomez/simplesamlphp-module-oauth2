<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use SimpleSAML\Modules\OAuth2\Repositories\AccessTokenRepository;
use SimpleSAML\Modules\OAuth2\Repositories\AuthCodeRepository;
use SimpleSAML\Modules\OAuth2\Repositories\RefreshTokenRepository;

function oauth2_hook_cron(&$croninfo)
{
    assert('is_array($croninfo)');
    assert('array_key_exists("summary", $croninfo)');
    assert('array_key_exists("tag", $croninfo)');

    $oauth2config = SimpleSAML_Configuration::getOptionalConfig('module_oauth2.php');

    if (is_null($oauth2config->getValue('cron_tag', 'hourly'))) {
        return;
    }
    if ($oauth2config->getValue('cron_tag', null) !== $croninfo['tag']) {
        return;
    }

    try {
        $store = \SimpleSAML_Store::getInstance();

        if (!$store instanceof \SimpleSAML\Modules\DBAL\Store\DBAL) {
            throw new \SimpleSAML_Error_Exception('OAuth2 module: Only DBAL Store is supported');
        }

        $accessTokenRepository = new AccessTokenRepository();
        $accessTokenRepository->removeExpiredAccessTokens();

        $authTokenRepository = new AuthCodeRepository();
        $authTokenRepository->removeExpiredAuthCodes();

        $refreshTokenRepository = new RefreshTokenRepository();
        $refreshTokenRepository->removeExpiredRefreshTokens();

        $croninfo['summary'][] = 'OAuth2 clean up. Removed expired entries from OAuth2 storage.';
    } catch (Exception $e) {
        $message = 'OAuth2 clean up cron script failed: '.$e->getMessage();
        \SimpleSAML_Logger::warning($message);
        $croninfo['summary'][] = $message;
    }
}
