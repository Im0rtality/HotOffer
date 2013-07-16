<?php

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id' => 'nfqhotoffer',
    'title' => 'Hot Offer New',
    'description' => 'Hot Offer test module',
    'thumbnail' => 'out/src/banner.png',
    'version' => '1.0.0',
    'author' => 'NFQ',
    'url' => 'http://www.nfq.lt',
    'email' => 'info@nfq.lt',
    'extend' => array(
    ),
    'blocks' => array(),
    'files' => array(
        'HotOfferInstaller' => 'Nfq/HotOffer/HotOfferInstaller.php',
        'hotoffer_tab' => 'Nfq/HotOffer/Controllers/Admin/HotOffer_Tab.php',
    ),
    'settings' => array(),
    'templates' => array(
        'hotoffer_tab.tpl' => 'Nfq/HotOffer/out/admin/tpl/hotoffer_tab.tpl',
    ),
    'events' => array(
        'onActivate' => 'HotOfferInstaller::onActivate',
        'onDeactivate' => 'HotOfferInstaller::onDeactivate',
    ),
);