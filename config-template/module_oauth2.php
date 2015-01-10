<?php
/* 
 * Configuration for the OAuth2 module.
 * 
 */

$config = array (

    'accessTokenDuration'  => 3600, // 60 minutes
    'requestTokenDuration' => 1209600, // 14 days
    'authTokenDuration'    => 30, // 30 seconds


    // Tag to run storage cleanup script using the cron module...
    'cron_tag' => 'hourly',

    // auth is the idp to use for admin authentication,
    // useridattr is the attribute-name that contains the userid as returned from idp
    'auth' => 'default-sp',
    'useridattr' => 'user',

);
