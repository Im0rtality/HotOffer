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
    'thumbnail' => 'views/src/banner.png',
    'version' => '1.0.0',
    'author' => 'NFQ',
    'url' => 'http://www.nfq.lt',
    'email' => 'info@nfq.lt',
    'extend' => array(
        'oxarticle' => 'HotOfferOxArticle',
    ),
    'blocks' => array(
        array(
            'template' => 'page/details/inc/productmain.tpl',
            'block' => 'details_productmain_zoom',
            'file' => '/views/blocks/hotoffer_productmain.tpl',
        ),
        array(
            'template' => 'widget/product/listitem_infogrid.tpl',
            'block' => 'widget_product_listitem_infogrid_gridpicture',
            'file' => '/views/blocks/hotoffer_infogrid.tpl',
        ),
        array(
            'template' => 'widget/product/listitem_grid.tpl',
            'block' => 'widget_product_listitem_grid',
            'file' => '/views/blocks/hotoffer_grid.tpl',
        ),
        array(
            'template' => 'widget/product/listitem_line.tpl',
            'block' => 'widget_product_listitem_line_picturebox',
            'file' => '/views/blocks/hotoffer_list.tpl',
        ),
    ),
    'files' => array(
        'HotOfferInstaller' => 'Nfq/HotOffer/HotOfferInstaller.php',
        'hotoffer_tab' => 'Nfq/HotOffer/Controllers/Admin/HotOffer_Tab.php',
        'HotOfferOxArticle' => 'Nfq/HotOffer/Models/HotOfferOxArticle.php',
    ),
    'settings' => array(
        array('group' => 'main', 'name' => 'sCategory', 'type' => 'str', 'value' => '32'),
    ),
    'templates' => array(
        'hotoffer_tab.tpl' => 'Nfq/HotOffer/out/admin/tpl/hotoffer_tab.tpl',
    ),
    'events' => array(
        'onActivate' => 'HotOfferInstaller::onActivate',
        'onDeactivate' => 'HotOfferInstaller::onDeactivate',
    ),
);