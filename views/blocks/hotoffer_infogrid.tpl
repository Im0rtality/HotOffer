[{if $product->isHotOffer()}]
    [{assign var="hotoffer" value="hotoffer"}]
    <link rel="stylesheet" href="[{$oViewConf->getModuleUrl('nfqhotoffer','views/src/banner.css')}]"/>
[{else}]
    [{assign var="hotoffer" value=""}]
[{/if}]

<div class="pictureBox gridPicture [{$hotoffer}]">
    <a class="sliderHover" href="[{$_productLink}]"
       title="[{$product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]"></a>
    <a href="[{$_productLink}]" class="viewAllHover glowShadow corners"
       title="[{$product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]"><span>[{oxmultilang ident="WIDGET_PRODUCT_PRODUCT_DETAILS"}]</span></a>

    <img src="[{$product->getThumbnailUrl()}]" alt="[{$product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]">
</div>
