<?php
/**
 * This file is part of
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * pragmaMx is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * $Revision: 119 $
 * $Author: PragmaMx $
 * $Date: 2016-03-30 15:35:05 +0200 (Mi, 30. Mrz 2016) $
 *

 */

defined('mxMainFileLoaded') or die('access denied');
// fetch random selected banner
function viewbanner($typ = 0)
{
    $bresult = sql_query("SELECT bid, imageurl, alttext, script, imptotal, impmade FROM " . $GLOBALS['prefix'] . "_banner WHERE typ=" . intval($typ) . " AND active=1 ORDER BY RAND() LIMIT 1");

    if ($bresult) {
        list($bid, $imageurl, $alttext, $script, $imptotal, $impmade) = sql_fetch_row($bresult);

        if (!empty($bid)) {
            // Banner wird nach check hier ausgegeben active=1 wird der Banner ausgegeben
            // active 2 wird geändert wenn Kaufrate erreicht ist und banner dann ausgeblendet
            if (($imptotal <= $impmade) AND (!empty($imptotal))) {
                sql_query("UPDATE " . $GLOBALS['prefix'] . "_banner SET active='2', dateend=now() WHERE bid=$bid");
                // falls nicht deaktivieren, counter hochsetzen wenn nicht Admin
            } elseif (!MX_IS_ADMIN && !empty($bid)) {
                sql_query("UPDATE " . $GLOBALS['prefix'] . "_banner SET impmade=impmade+1 WHERE bid=$bid");
            }
            // Ausgabe für Banner mit Bild und URL oder ob es ein Bannercode ist
            if (empty($script)) {
				list($width, $height, $type, $attr) = getimagesize($imageurl);
                return '<div class="align-center"><a href="banners.php?mxbop=click&amp;bid=' . $bid . '" target="_blank" style="max-width:100%">' . mxCreateImage($imageurl, $alttext, 0,"style='width:100%,max-width:".$width."px'") . '</a></div>';
            } else {
                return $script;
            }
        }
    }
    return '';
}

function clickbanner($bid)
{
    $result = sql_query("SELECT clickurl FROM " . $GLOBALS['prefix'] . "_banner WHERE bid=" . intval($bid));
    list($clickurl) = sql_fetch_row($result);
    sql_query("UPDATE " . $GLOBALS['prefix'] . "_banner SET clicks=clicks+1 WHERE bid=" . intval($bid));
    header('HTTP/1.1 301 Moved Permanently');
    mxRedirect($clickurl);
}

?>