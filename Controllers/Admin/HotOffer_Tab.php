<?php

class HotOffer_Tab extends oxAdminView
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'hotoffer_tab.tpl';

    /**
     * Executes parent method parent::render(), passes configuration data to
     * Smarty engine.
     *
     * @return string current view template file name
     */
    public function render()
    {
        parent::render();

        $this->_aViewData["edit"] = $oArticle = oxNew("oxarticle");

        $soxId = $this->getEditObjectId();
        if ($soxId != "-1" && isset($soxId)) {
            // load object
            $oArticle->loadInLang($this->_iEditLang, $soxId);

            // load object in other languages
            $oOtherLang = $oArticle->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oArticle->loadInLang(key($oOtherLang), $soxId);
            }

            //set access field properties to prevent derived articles for editing
            if ($oArticle->isDerived()) {
                $this->_aViewData["readonly"] = true;
            }

            // variant handling
            if ($oArticle->oxarticles__oxparentid->value) {
                $oParentArticle = oxNew("oxarticle");
                $oParentArticle->load($oArticle->oxarticles__oxparentid->value);
                $this->_aViewData["parentarticle"] = $oParentArticle;
                $this->_aViewData["oxparentid"] = $oArticle->oxarticles__oxparentid->value;
            }

            $aLang = array_diff(oxLang::getInstance()->getLanguageNames(), $oOtherLang);
            if (count($aLang)) {
                $this->_aViewData["posslang"] = $aLang;
            }

            foreach ($oOtherLang as $id => $language) {
                $oLang = new oxStdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        }

        return $this->_sThisTemplate;
    }

    /**
     * Stores new article state
     *
     * @return string
     */
    public function save()
    {
        parent::save();
        $soxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter("editval");

        $oArticle = oxNew("oxarticle");
        $oArticle->load($soxId);

        $oArticle->setLanguage(0);

        $oArticle->assign($aParams);
        if ($oArticle->isHotOffer()) {
            $this->assignToCategory(array($oArticle->getId()));
        } else {
            $this->removeFromCategory(array($oArticle->getId()));
        }
        $oArticle->setLanguage($this->_iEditLang);

        $oArticle->save();

        $this->setEditObjectId($oArticle->getId());
    }

    public function assignToCategory($aArticles = null)
    {
        if ((!isset($aArticles)) || (empty($aArticles)) || (!is_array($aArticles))) {
            return;
        }
        $sCategoryID = oxRegistry::get("oxConfig")->getConfigParam("sCategory");
        $oDb = oxDb::getDb();
        $sO2CView = getViewName('oxobject2category', $this->_iEditLang);

        $oNew = oxNew('oxbase');
        $oNew->init('oxobject2category');
        $myUtilsObject = oxUtilsObject::getInstance();

        $sProdIds = "";
        foreach ($aArticles as $sAdd) {

            // check, if it's already in, then don't add it again
            $sSelect = "select 1 from {$sO2CView} as oxobject2category where oxobject2category.oxcatnid= " . $oDb->quote(
                    $sCategoryID
                ) . " and oxobject2category.oxobjectid = " . $oDb->quote($sAdd) . "";
            if ($oDb->getOne($sSelect, false, false)) {
                continue;
            }

            $oNew->oxobject2category__oxid = new oxField($oNew->setId($myUtilsObject->generateUID()));
            $oNew->oxobject2category__oxobjectid = new oxField($sAdd);
            $oNew->oxobject2category__oxcatnid = new oxField($sCategoryID);
            $oNew->oxobject2category__oxtime = new oxField(time());

            $oNew->save();

            if ($sProdIds) {
                $sProdIds .= ",";
            }
            $sProdIds .= $oDb->quote($sAdd);
        }

        // updating oxtime values
        $this->_updateOxTime($sProdIds);

        $this->resetArtSeoUrl($aArticles);
        $this->resetCounter("catArticle", $sCategoryID);
    }

    /**
     * Updates oxtime value for products
     *
     * @param string $sProdIds product ids: "id1", "id2", "id3"
     *
     * @return null
     */
    protected function _updateOxTime($sProdIds)
    {
        if ($sProdIds) {
            $sO2CView = getViewName('oxobject2category', $this->_iEditLang);
            $sQ = "update oxobject2category set oxtime = 0 where oxid in (
                      select _tmp.oxid from (
                          select oxobject2category.oxid from (
                              select min(oxtime) as oxtime, oxobjectid from {$sO2CView} where oxobjectid in ( {$sProdIds} ) group by oxobjectid
                          ) as _subtmp
                          left join oxobject2category on oxobject2category.oxtime = _subtmp.oxtime and oxobject2category.oxobjectid = _subtmp.oxobjectid
                      ) as _tmp
                   )";

            oxDb::getDb()->execute($sQ);
        }
    }

    public function removeFromCategory($aArticles = null)
    {
        if ((!isset($aArticles)) || (empty($aArticles)) || (!is_array($aArticles))) {
            return;
        }
        $sCategoryID = oxRegistry::get("oxConfig")->getConfigParam("sCategory");
        $sShopID = $this->getConfig()->getShopId();
        $oDb = oxDb::getDb();

        // adding
        if (oxConfig::getParameter('all')) {
            $sArticleTable = getViewName('oxobject2category', $this->_iEditLang);
            $aArticles = $this->_getAll($this->_addFilter("select $sArticleTable.oxid " . $this->_getQuery()));
        }

        // adding
        if (is_array($aArticles) && count($aArticles)) {
            $sProdIds = implode(", ", oxDb::getInstance()->quoteArray($aArticles));

            $sDelete = "delete from oxobject2category where";
            $sWhere = " oxcatnid=" . $oDb->quote($sCategoryID);
            if (!$this->getConfig()->getConfigParam('blVariantsSelection')) {
                $sQ = $sDelete . $sWhere . " and oxobjectid in ( select oxid from oxarticles where oxparentid in ( {$sProdIds} ) )";
                $oDb->execute($sQ);
            }
            $sQ = $sDelete . $sWhere . " and oxobjectid in ( {$sProdIds} )";
            $oDb->execute($sQ);


            // updating oxtime values
            $this->_updateOxTime($sProdIds);
        }

        $this->resetArtSeoUrl($aArticles, $sCategoryID);
        $this->resetCounter("catArticle", $sCategoryID);
    }

    /**
     * Marks article seo url as expired
     *
     * @param array $aArtIds article id's
     * @param array $aCatIds ids if categories, which must be removed from oxseo
     *
     * @return null
     */
    public function resetArtSeoUrl( $aArtIds, $aCatIds = null )
    {
        if ( empty( $aArtIds ) ) {
            return;
        }

        if ( !is_array( $aArtIds ) ) {
            $aArtIds = array( $aArtIds );
        }

        $blCleanCats = false;
        if ( $aCatIds ) {
            if ( !is_array( $aCatIds ) ) {
                $aCatIds = array( $aCatIds );
            }
            $sShopId = $this->getConfig()->getShopId();
            $sQ = "delete from oxseo where oxtype='oxarticle' and oxobjectid='%s' and
                   oxshopid='{$sShopId}' and oxparams in (" . implode( ",", oxDb::getInstance()->quoteArray( $aCatIds ) ) . ")";
            $oDb = oxDb::getDb();
            $blCleanCats = true;
        }

        $sShopId = $this->getConfig()->getShopId();
        foreach ( $aArtIds as $sArtId ) {
            oxRegistry::get("oxSeoEncoder")->markAsExpired( $sArtId, $sShopId, 1, null, "oxtype='oxarticle'" );
            if ( $blCleanCats ) {
                $oDb->execute( sprintf( $sQ, $sArtId ) );
            }
        }
    }

    /**
     * Resets counters values from cache. Resets price category articles, category articles,
     * vendor articles, manufacturer articles count.
     *
     * @param string $sCounterType counter type
     * @param string $sValue       reset value
     *
     * @return null
     */
    public function resetCounter( $sCounterType, $sValue = null )
    {
        $blDeleteCacheOnLogout = $this->getConfig()->getConfigParam( 'blClearCacheOnLogout' );

        if ( !$blDeleteCacheOnLogout ) {
            $myUtilsCount = oxRegistry::get("oxUtilsCount");
            switch ( $sCounterType ) {
                case 'priceCatArticle':
                    $myUtilsCount->resetPriceCatArticleCount( $sValue );
                    break;
                case 'catArticle':
                    $myUtilsCount->resetCatArticleCount( $sValue );
                    break;
                case 'vendorArticle':
                    $myUtilsCount->resetVendorArticleCount( $sValue );
                    break;
                case 'manufacturerArticle':
                    $myUtilsCount->resetManufacturerArticleCount( $sValue );
                    break;
            }
        }
    }
}
