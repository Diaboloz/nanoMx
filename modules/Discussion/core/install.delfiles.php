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
 * $Date: 2015-07-08 09:07:06 +0200 (mer. 08 juil. 2015) $
 */

$modpath = 'modules/' . basename(dirname(__DIR__)) . '/';

$delfiles = array(/* Dateien zum löschen */
    'blocks/block-XForum_Center.php',
    'blocks/block-eBoard_Center.php',
    'blocks/block-mxBoard_Center.php',
    'blocks/block-mxBoard_Center_oldstyle.php',
    'blocks/block-mxBoard_mods_online.php',
    'admin/links/links.eBoard.php',
    $modpath . 'core/prepare.header.php',
    $modpath . 'js/jpicker/css/index.html',
    $modpath . 'js/jpicker/css/jpicker.css',
    $modpath . 'js/jpicker/css/jpicker.min.css',
    $modpath . 'js/jpicker/images/AlphaBar.png',
    $modpath . 'js/jpicker/images/bar-opacity.png',
    $modpath . 'js/jpicker/images/Bars.png',
    $modpath . 'js/jpicker/images/index.html',
    $modpath . 'js/jpicker/images/map-opacity.png',
    $modpath . 'js/jpicker/images/mappoint.gif',
    $modpath . 'js/jpicker/images/Maps.png',
    $modpath . 'js/jpicker/images/NoColor.png',
    $modpath . 'js/jpicker/images/picker.gif',
    $modpath . 'js/jpicker/images/preview-opacity.png',
    $modpath . 'js/jpicker/images/rangearrows.gif',
    $modpath . 'js/jpicker/ChangeLog.txt',
    $modpath . 'js/jpicker/index.html',
    $modpath . 'js/jpicker/jpicker.js',
    $modpath . 'js/jpicker/jpicker.min.js',
    $modpath . 'js/jpicker/ReadMe.txt',
    );

$deldirs = array(/* Ordner zum löschen */
    $modpath . 'js/jpicker',
);

?>