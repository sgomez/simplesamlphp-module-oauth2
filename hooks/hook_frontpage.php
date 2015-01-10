<?php
/**
 * Hook to add link to the frontpage.
 *
 * @param array &$links  The links on the frontpage, split into sections.
 */
function oauth2_hook_frontpage(&$links) {
    assert('is_array($links)');
    assert('array_key_exists("links", $links)');

    $links['federation']['oauthregistry'] = array(
        'href' => SimpleSAML_Module::getModuleURL('oauth2/registry.php'),
        'text' => array('en' => 'OAuth2 Client Registry'),
        'shorttext' => array('en' => 'OAuth2 Registry'),
    );

}
