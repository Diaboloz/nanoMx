<?php
/**
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Revision: 1.4 $
 * $Author: tora60 $
 * $Date: 2014-01-18 11:09:13 $
 */

defined('mxMainFileLoaded') or die('access denied');

define('_THISTHEME_', __DIR__);

/* Sprachdatei auswählen */
mxGetLangfile(__DIR__);

/* Theme stellt eigenes Adminmenü bereit */
define('GRAPHICADMIN', true);

global $themesetting; // global muss drin bleiben, weil das in einer Funktion includet wird !!
$themesetting = array();
require(_THISTHEME_ . '/theme.settings.php');
require_once(_THISTHEME_ . '/theme.functions.php');

if (MX_IS_ADMIN && !defined('mxAdminFileLoaded')) {
    echo '<h1 class="error">Error: admintheme "' . basename(_THISTHEME_) . '" selected...</h1>';
}

/* nur wenn theme.php normal verwendet wird, nicht für CSS */
if (!defined('DONT_INIT_THEME') || DONT_INIT_THEME == false) {
    /* zu alte pragmaMx-Version */
    if (version_compare(PMX_VERSION, '2', '<')) {
        die('<h1>Sorry, theme "' . basename(_THISTHEME_) . '" requires pragmaMx version >= 2.0</h1>');
    }

    /* die themeEngine laden */
    include_once(PMX_SYSTEM_DIR . '/mx_themes.php');

    /* bei Bedarf den Dokumenttyp anpassen :) */
    if (!theme_check_xhtmldoctype()) {
        $GLOBALS['DOCTYPE'] = 5;
    }
}

?>
