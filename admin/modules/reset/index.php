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
 * $Revision: 101 $
 * $Author: PragmaMx $
 * $Date: 2015-12-30 21:08:19 +0100 (Mi, 30. Dez 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

/* Sprachdatei auswählen */
mxGetLangfile(dirname(__FILE__));

/* Was ist zu tun ? */
switch ($op) {
    case 'reset/cache/blocks':
        include_once(PMX_SYSTEM_DIR . DS . 'mx_reset.php');
        resetBlockCache();
        return mxRedirect(PMX_REFERER, _BLOCKDELCACHEOK, 1);
        break;

    case 'reset/cache/all':		
        include_once(PMX_SYSTEM_DIR . DS . 'mx_reset.php');		
        resetPmxCache();
		resetBlockCache();
        return mxRedirect(PMX_REFERER, _RESETPMXCACHEALLOK, 1);
        break;
    case 'reset/cache':
    default:		
        include_once(PMX_SYSTEM_DIR . DS . 'mx_reset.php');		
        resetPmxCache();
		resetBlockCache();
        return mxRedirect(PMX_REFERER, _RESETPMXCACHEOK, 1);
        break;		
}

?>