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
 * $Revision: 1.8 $
 * $Author: tora60 $
 * $Date: 2013-01-08 23:04:20 $
 */

/* alle Meldungen unterdrücken */
error_reporting(0);
ob_start();

define('mxMainFileLoaded', true);

if (isset($_GET['t'])) {
    $theme = preg_replace('#[^A-Za-z0-9_-]#', '_', $_GET['t']);
} else {
    $theme = 'mx-default';
}

switch (true) {
    case $theme = realpath('../../themes/' . $theme . '/theme.php'):
        $theme = basename(dirname($theme));
        break;
    case $theme = (array)glob('../../themes/*/theme.php'):
        /* irgend ein Theme holen... */
        $theme = basename(dirname($theme[0]));
        break;
    default:
        die('/* no themes available */');
}

include(dirname(__FILE__) . '/../../config.php');

/* falls Themecache abgeschaltet, diese Info weitergeben */
if (empty($mxUseThemecache)) {
    $skipcache = '&skipcache';
} else {
    $skipcache = '';
}

ob_end_clean();

/* als CSS abschicken ;) */
header('Content-Type: text/css; charset=utf-8');
/* nicht cachen */
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Datum in der Vergangenheit
header('Etag: ' . uniqid());
header('X-Powered-By: pragmaMx-cms');

/* Ausgabe */
echo '@import url("default.css.php?t=', $theme, $skipcache, '");';

?>