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
    'blocks' => array(
        array(
            'template' => 'page/details/inc/productmain.tpl',
            'block' => 'details_productmain_zoom',
            'file' => '/out/blocks/hotoffer_productmain.tpl',
        ),
    ),
    'files' => array(
        'HotOfferInstaller' => 'Nfq/HotOffer/HotOfferInstaller.php',
        'hotoffer_tab' => 'Nfq/HotOffer/Controllers/Admin/HotOffer_Tab.php',
        'banner.css' => 'Nfq/HotOffer/out/src/banner.css',
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