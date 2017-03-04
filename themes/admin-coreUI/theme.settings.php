<?php
/**
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Revision: 1.2 $
 * $Author: tora60 $
 * $Date: 2014-01-19 10:43:39 $
 */

/* diese Zeile nicht ändern!! */
defined('mxMainFileLoaded') or die('access denied');

/**
 * Layout-Type:
<!-- BODY options, add following classes to body to change options // keep app first

// Header options
1. '.header-fixed'					- Fixed Header

// Sidebar options
1. '.sidebar-fixed'					- Fixed Sidebar
2. '.sidebar-hidden'				- Hidden Sidebar
3. '.sidebar-off-canvas'		- Off Canvas Sidebar
4. '.sidebar-compact'				- Compact Sidebar Navigation (Only icons)

// Aside options
1. '.aside-menu-fixed'			- Fixed Aside Menu
2. '.aside-menu-hidden'			- Hidden Aside Menu
3. '.aside-menu-off-canvas'	- Off Canvas Aside Menu

// Footer options
1. '.footer-fixed'						- Fixed footer
*/

$themesetting['layouttype'] = 'app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden';

/* definieren ob die Templates des Themes gecached werden können */
define('MX_THEME_CACHABLE', false);

/* Dateiname des templates */
define('MX_THIS_THEMEFILE', 'theme.html');

?>
