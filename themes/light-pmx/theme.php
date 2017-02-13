<?php
/**
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Revision: 1.3 $
 * $Author: tora60 $
 * $Date: 2014-01-18 11:09:13 $
 */

defined('mxMainFileLoaded') or die('access denied');

define('_THISTHEME_', __DIR__);

/* Sprachdatei auswaehlen */
mxGetLangfile(__DIR__);

global $themesetting; // global muss drin bleiben, weil das in einer Funktion includet wird !!
$themesetting = array();
require(_THISTHEME_ . '/theme.settings.php');
require(_THISTHEME_ . '/theme.colors.php');
require_once(_THISTHEME_ . '/theme.functions.php');

theme_preview_settings($themesetting);

/* nur wenn theme.php normal verwendet wird, nicht fuer CSS */
if (!defined('DONT_INIT_THEME') || DONT_INIT_THEME == false) {
    /* zu alte pragmaMx-Version */
    if (version_compare(PMX_VERSION, '1.12.1', '<')) {
        die('<br />Sorry, theme "' . basename(_THISTHEME_) . '" requires pragmaMx version >= 1.12.1');
    }

    /* die themeEngine laden */
    include_once(PMX_SYSTEM_DIR . '/mx_themes.php');

    /* bei Bedarf den Dokumenttyp anpassen :) */
    if (!theme_check_xhtmldoctype()) {
        $GLOBALS['DOCTYPE'] = 3;
    }
}

?>