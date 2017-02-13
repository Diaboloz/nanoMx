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
 * $Revision: 6 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (Mi, 08. Jul 2015) $
 *
 *
 */
/**
 */
/* mxBanner Modifikation 1.0                                            */
/* © 2003 by www.komplett-umsonst.de                                    */
/* modified by Christian Kleinschroth                                   */
/**
 */

if (!defined("mxMainFileLoaded")) require_once("mainfile.php");

$mxbop = (empty($_REQUEST['mxbop'])) ? "" : $_REQUEST['mxbop'];

if (empty($mxbop)) { // / falls noch irgendein alter aufruf op=click rumschwirrt
    if (isset($_REQUEST['op'])) {
        $mxbop = ($_REQUEST['op'] == 'click') ? 'click' : '';
    }
}

switch ($mxbop) {
    case "click":
        if (!function_exists('clickbanner')) {
            include_once(PMX_SYSTEM_DIR . DS . "mx_bannerfunctions.php");
        }
        $bid = (empty($_REQUEST['bid'])) ? 0 : intval($_REQUEST['bid']);
        clickbanner($bid);
        break;

    default:
        if ($GLOBALS['banners']) {
            if (!function_exists('viewbanner')) {
                include_once(PMX_SYSTEM_DIR . DS . 'mx_bannerfunctions.php');
            }
            $xx_banners = viewbanner(1);
            if ($xx_banners) {
                echo "<center>$xx_banners</center>";
            }
            unset ($xx_banners);
        }
        break;
}

?>