<?php
/**
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Revision: 1.7 $
 * $Author: tora60 $
 * $Date: 2014-01-19 10:43:40 $
 */

/* diese Zeile nicht ändern!! */
defined('mxMainFileLoaded') or die('access denied');

/**
 * der Titel im Kopfbereich der Seite,
 * z.B.: 'pragmaMx'
 */
$themesetting['title'] = $GLOBALS['sitename'];

/**
 * Layout-Columns: default, left, right
 * .                 |X|     ||X    X||  
 * .                                    
 */
$themesetting['layoutcols'] = 'default';

/**
 * Layout-Breiten
 */
$themesetting['layoutwidth'] = array(
        'page' => 100,/*  Seitenbreite, in Prozent */
        'left' => 21,/*   linke Blockspalte, in Prozent */
        'right' => 32,/*  rechte Blockspalte, in Prozent */
        'min' => 320,/*   minimale Seitenbreite, immer in Pixel! */
        'max' => 1140,/*  maximale Seitenbreite, immer in Pixel! */
    );

/**
 * das Menü im Kopfbereich
 * - den Menünamen aus dem Menümanager angeben, oder
 * - leer lassen (false) um kein Menü anzuzeigen
 */
$themesetting['head_css_menu'] = 'header-nav'; // z.B.: {CSS-MENU}

/**
 * Links und Inhalte in der oberen Linkleiste anzeigen
 * true = ja, false = nein
 */
$themesetting['topmenu'] = array(
    // User relevante Links (z.B. Login)
    'user' => true,
    // Link zu RSS-Feeds
    'backend' => true,
    // Link zum Impressum
    'impres' => true,
    );


/**
 * Links und Inhalte in der oberen Linkleiste anzeigen
 * true = ja, false = nein
 */
$themesetting['footmenu'] = array(
    // User relevante Links (z.B. Login)
    // 'user' => true,
    // Link zu RSS-Feeds
    'backend' => true,
    // Link zum Impressum
    'impres' => true,
    );

/* folgendes nur, wenn Module geladen */
if (defined('MX_MODULE')) {
    /* Hilfsvariablen für das SMF-Forum */
    global $context; // vom SMF
    $current_action = isset($context['current_action']) ? $context['current_action'] : false;

    /**
     * Seiten, bei denen, die linken Blöcke nicht gezeigt werden sollen
     */
    $themesetting['hide-left'] = array(/* Seiten/Module */
        MX_MODULE == 'Web_Links',
        MX_MODULE == 'News' && !defined('MX_HOME_FILE'),
        // MX_MODULE == 'FAQ',
        // MX_MODULE == 'Downloads' && $_REQUEST['cid'] == 61, // Downloads, Kategorie 61
        // $_REQUEST['name'] == 'Content' && $_REQUEST['pid'] == 28, // Contentmodul Id 28
        );

    /**
     * Seiten, bei denen beide Blockspalten nicht angezeigt werden sollen
     */
    $themesetting['hide-both'] = array(/* Seiten/Module */
        MX_MODULE == 'Downloads', 
        MX_MODULE == 'Web_Links', 
        MX_MODULE == 'Documents', 
        MX_MODULE == 'Private_Messages',     
        MX_MODULE == 'Userinfo',
        MX_MODULE == 'User_Registration',    
        MX_MODULE == 'Discussion',
        MX_MODULE == 'Your_Account',
        MX_MODULE == 'Forum' && $current_action != 'profile' && $current_action != 'pm' ,
        // weitere Beispiele:
        // MX_MODULE === 'Gallery',
        // MX_MODULE == 'eBoard',
        // MX_MODULE == 'Downloads' && $_REQUEST['cid'] == 61, // Downloads, Kategorie 61
        // $_REQUEST['name'] == 'Content' && $_REQUEST['pid'] == 28, // Contentmodul Id 28
        );
}
/* Ende nur Module */

/**
 * /////////////////////////////////////////////////////////////////////////////
 * folgende Einstellungen sollten nicht verändert werden
 */

/* Modul-Blöcke im passenden Design anzeigen */
$themesetting['blocknav']['style'] = 'menu';
$themesetting['blocknav']['current'] = 'current';

/* definieren ob die Templates des Themes gecached werden können */
define('MX_THEME_CACHABLE', false);

/* Dateiname des templates */
define('MX_THIS_THEMEFILE', 'theme.html');

?>
