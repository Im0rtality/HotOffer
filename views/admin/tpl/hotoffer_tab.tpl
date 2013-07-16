[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
    [{else}]
    [{assign var="readonly" value=""}]
    [{/if}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="article_review">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>

<table cellspacing="0" cellpadding="0" border="0">
    <form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post"
        style="padding: 0px;margin: 0px;height:0px;">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="cl" value="hotoffer_tab">
        <input type="hidden" name="fnc" value="">
        <input type="hidden" name="oxid" value="[{ $oxid }]">
        <input type="hidden" name="voxid" value="[{ $oxid }]">
        <input type="hidden" name="oxparentid" value="[{ $oxparentid }]">
        <input type="hidden" name="editval[oxarticles__oxid]" value="[{ $oxid }]">
        <input type="hidden" name="editval[oxarticles__oxlongdesc]" value="">
        <tr>
            <td class="edittext" width="120">
                [{ oxmultilang ident="GENERAL_HOT_OFFER" }]
            </td>
            <td class="edittext">
                <input type="hidden" name="editval[oxarticles__nfq_hotoffer]" value="0">
                <input class="edittext" type="checkbox" name="editval[oxarticles__nfq_hotoffer]" value='1'
                [{if $edit->oxarticles__nfq_hotoffer->value == 1}]checked[{/if}] [{ $readonly }]>
            </td>
        </tr>
        <tr>
            <td class="edittext" colspan="2"><br><br>
                <input type="submit" class="edittext" id="oLockButton" name="saveArticle"
                       value="[{ oxmultilang ident='HOT_OFFER_SAVE' }]"
                       onClick="Javascript:document.myedit.fnc.value='save'"
                [{if !$edit->oxarticles__oxtitle->value && !$oxparentid }]disabled[{/if}]
                [{ $readonly }]>
            </td>
        </tr>
    </form>
</table>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
