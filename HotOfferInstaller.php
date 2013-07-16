<?php
/**
 * @copyright C UAB “NFQ Solutions” 2013
 *
 * This Software is the property of “NFQ Solutions”
 * and is protected by copyright law – it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * Contact UAB “NFQ Solutions”:
 * E-mail: info@nfq.lt
 * http://www.nfq.lt
 *
 */

class HotOfferInstaller
{

    /**
     * On module activation, executes sql's, update views or smth.
     *
     * @return null
     */
    public static function onActivate() {
        $sModuleDir = dirname(__FILE__);

        if (file_exists($sModuleDir) . '/Sql/Install.sql') {
            $sSqlFileContent = file_get_contents($sModuleDir . '/Sql/Install.sql');
            self::_executeSqlFile($sSqlFileContent);
            oxUtils::getInstance()->oxResetFileCache();
            self::_updateViews();
        }
    }

    /**
     * What should be done on module deactivation
     *
     * @return null
     */
    public static function onDeactivate()
    {
        $sModuleDir = dirname(__FILE__);

        if (file_exists($sModuleDir) . '/Sql/Uninstall.sql') {
            $sSqlFileContent = file_get_contents($sModuleDir . '/Sql/Uninstall.sql');
            self::_executeSqlFile($sSqlFileContent);
        }

        self::_cleanCache();
        self::_updateViews();
    }

    /**
     * Executes SQL string with more than one query.
     *
     * @return null
     */
    protected static function _executeSqlFile($sUpdateSQL)
    {
        $oStr = getStr();
        $iLen = $oStr->strlen( $sUpdateSQL );
        $aQueries = self::_prepareSQL( $sUpdateSQL, $iLen );

        if ( !empty($aQueries) ) {

            if ( count( $aQueries ) > 0 ) {
                $blStop = false;
                $oDB = oxDb::getDb();
                $iQueriesCounter = 0;
                for ( $i = 0; $i < count( $aQueries ); $i++ ) {
                    $sUpdateSQL = $aQueries[$i];
                    $sUpdateSQL = trim( $sUpdateSQL );

                    if ( $oStr->strlen( $sUpdateSQL ) > 0 ) {
                        $aPassedQueries[$iQueriesCounter] = nl2br( htmlentities( $sUpdateSQL ) );
                        if ( $oStr->strlen( $aPassedQueries[$iQueriesCounter] ) > 200 )
                            $aPassedQueries[$iQueriesCounter] = $oStr->substr( $aPassedQueries[$iQueriesCounter], 0, 200 )."...";

                        while ( $sUpdateSQL[ $oStr->strlen( $sUpdateSQL)-1] == ";") {
                            $sUpdateSQL = $oStr->substr( $sUpdateSQL, 0, ( $oStr->strlen( $sUpdateSQL)-1));
                        }

                        try {
                            $oDB->execute( $sUpdateSQL );
                        } catch ( Exception $oExcp ) {
                            // catching exception ...
                            break;
                        }
                    }
                }
            }
        }
    }


    /**
     * Prepares sql array from long sql string (with more than one query)
     *
     * @return array
     */
    protected static function _prepareSQL( $sSQL, $iSQLlen )
    {
        $sChar = "";
        $sStrStart = "";
        $blString  = false;
        $oStr = getStr();
        $aSQLs = array();

        //removing "mysqldump" application comments
        while ( $oStr->preg_match( "/^\-\-.*\n/", $sSQL ) )
            $sSQL = trim( $oStr->preg_replace( "/^\-\-.*\n/", "", $sSQL ) );
        while ( $oStr->preg_match( "/\n\-\-.*\n/", $sSQL ) )
            $sSQL = trim( $oStr->preg_replace( "/\n\-\-.*\n/", "\n", $sSQL ) );

        for ( $iPos = 0; $iPos < $iSQLlen; ++$iPos ) {
            $sChar = $sSQL[$iPos];
            if ( $blString ) {
                while ( true ) {
                    $iPos = $oStr->strpos( $sSQL, $sStrStart, $iPos );
                    //we are at the end of string ?
                    if ( !$iPos ) {
                        $aSQLs[] = $sSQL;
                        return $aSQLs;
                    } elseif ( $sStrStart == '`' || $sSQL[$iPos-1] != '\\' ) {
                        //found some query separators
                        $blString  = false;
                        $sStrStart = "";
                        break;
                    } else {
                        $iNext = 2;
                        $blBackslash = false;
                        while ( $iPos-$iNext > 0 && $sSQL[$iPos-$iNext] == '\\' ) {
                            $blBackslash = !$blBackslash;
                            $iNext++;
                        }
                        if ( $blBackslash ) {
                            $blString  = false;
                            $sStrStart = "";
                            break;
                        } else
                            $iPos++;
                    }
                }
            } elseif ( $sChar == ";" ) {
                // delimiter found, appending query array
                $aSQLs[] = $oStr->substr( $sSQL, 0, $iPos );
                $sSQL = ltrim( $oStr->substr( $sSQL, min( $iPos + 1, $iSQLlen ) ) );
                $iSQLlen = $oStr->strlen( $sSQL );
                if ( $iSQLlen )
                    $iPos      = -1;
                else
                    return $aSQLs;
            } elseif ( ( $sChar == '"') || ( $sChar == '\'') || ( $sChar == '`')) {
                $blString  = true;
                $sStrStart = $sChar;
            } elseif ( $sChar == "#" || ( $sChar == ' ' && $iPos > 1 && $sSQL[$iPos-2] . $sSQL[$iPos-1] == '--')) {
                // removing # commented query code
                $iCommStart = (( $sSQL[$iPos] == "#") ? $iPos : $iPos-2);
                $iCommEnd = ($oStr->strpos(' ' . $sSQL, "\012", $iPos+2))
                    ? $oStr->strpos(' ' . $sSQL, "\012", $iPos+2)
                    : $oStr->strpos(' ' . $sSQL, "\015", $iPos+2);
                if ( !$iCommEnd ) {
                    if ( $iCommStart > 0 )
                        $aSQLs[] = trim( $oStr->substr( $sSQL, 0, $iCommStart ) );
                    return $aSQLs;
                } else {
                    $sSQL = $oStr->substr( $sSQL, 0, $iCommStart ).ltrim( $oStr->substr( $sSQL, $iCommEnd ) );
                    $iSQLlen = $oStr->strlen( $sSQL );
                    $iPos--;
                }
            } elseif ( 32358 < 32270 && ($sChar == '!' && $iPos > 1  && $sSQL[$iPos-2] . $sSQL[$iPos-1] == '/*'))  // removing comments like /**/
                $sSQL[$iPos] = ' ';
        }

        if ( !empty( $sSQL ) && $oStr->preg_match( "/[^[:space:]]+/", $sSQL ) ) {
            $aSQLs[] = $sSQL;
        }
        return $aSQLs;
    }

    /**
     * Clean cache in /tmp dir
     *
     * @return null
     */
    private static function _cleanCache()
    {
        oxRegistry::getUtils()->oxResetFileCache();
        oxRegistry::getUtils()->resetLanguageCache();
    }

    /**
     * Updating DB views
     *
     * @return null
     */
    private static function _updateViews()
    {
//        $oDbHandler = oxNew("oxDbMetaDataHandler");
//        $oDbHandler->updateViews(array('oxdeliverytime', 'oxorder'));
    }
}
