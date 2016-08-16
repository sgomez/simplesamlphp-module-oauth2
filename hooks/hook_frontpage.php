<?php
/**
 * Hook to add link to the frontpage.
 *
 * @param array &$links  The links on the frontpage, split into sections.
 */
function oauth2_hook_frontpage(&$links) {
    assert('is_array($links)');
    assert('array_key_exists("links", $links)');

    $links['federation']['oauthregistry'] = [
        'href' => SimpleSAML\Module::getModuleURL('oauth2/registry.php'),
        'text' => [
            'en' => 'OAuth2 Client Registry',
            'es' => 'Registro de clientes OAuth2',
        ],
        'shorttext' => [
            'en' => 'OAuth2 Registry',
            'es' => 'Registro OAuth2',
        ],
    ];
}
