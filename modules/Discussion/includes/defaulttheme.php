<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 * 
 * $Author: PragmaMx $
 * $Revision: 6 $
 * $Date: 2015-07-08 09:07:06 +0200 (mer. 08 juil. 2015) $
 * 
 * based on eBoard v1.1, rewrite and modified by
 * vkpMx-Developer-Team (http://www.maax-design.de)
 * Original source-code made by the XMB-team
 * (XMB-Forum, http://www.xmbforum.com), modified for nukestyle-systems
 * by Trollix (XForum, http://www.trollix.com).
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */
defined('mxMainFileLoaded') or die('access denied');
defined('MXB_INIT') or die('Not in mxBoard...');

// 
// Here you can change the values for the "default"-theme of the eBoard-Modul
// Normally color-settings are taken from your Nuke-theme
// 
// Hier können Sie die Farbwerte für das "default"-Theme des eBoard-Modules ändern
// Standardmässig werden die Farbwerte des Nukethemes übernommen
// 
global $textcolor1, $bgcolor1, $textcolor2, $bgcolor2, $textcolor3, $bgcolor3, $textcolor4, $bgcolor4;

$bgcolor = $bgcolor1;
$altbg1 = $bgcolor1;
$altbg2 = $bgcolor2;
$bgcolheader = $bgcolor1; 
// Color-Settings for the header-text, black or white depending on the bgc
if ($bgcolheader == "#000000") {
    $bgcolheadertext = "#FFFFFF";
} else {
    $bgcolheadertext = "#000000";
} 

$top = $bgcolor1; 
// Color-Setting for the table-border, black or grey depending on the backround-color
if ($bgcolor1 == "#000000" || $bgcolor1 == "#adadad") {
    $bordercolor = "#999999";
} else {
    $bordercolor = "#adadad";
} 

$catcolor = $bgcolor1;

$link = $textcolor1;
$text = $textcolor1;
// $color1 = "red";
// $color2 = "blue";
$tabletext = $textcolor1;

/**
 * Beispielwerte:
 * [bgcolor] => #FFFFFF
 * [bgcolor1] => #FF9900
 * [bgcolor2] => #FFCC33
 * [link] => #FF6633
 * [bordercolor] => #FFFFFF
 * [top] => #FF9900
 * [catcolor] => #FFCC33
 * [tabletext] => #000000
 * [text] => #000000
 * #[color1] => red
 * #[color2] => blue
 * [bgcolheader] => #FF6633
 * [bgcolheadertext] => #FFFFFF
 * [imageset] => gelb
 */

?>