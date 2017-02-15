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
 */

error_reporting(E_ERROR);

/* nur wenn auch index.php vorhanden ist, also setup noch funktionsfähig.. */
if (!file_exists(__dir__ . '/index.php') || basename(__dir__) != 'setup') {
    die('access denied');
}

$file = (isset($_GET['file'])) ? $_GET['file'] : '';
if (!$file) {
    die('no file selected');
}

define('mxMainFileLoaded', true);

if (!function_exists('file_get_contents')) {
    include(__DIR__ . '/includes/functions.php');
}

include(__DIR__ . '/setup-settings.php');

if (preg_match('#^' . preg_quote(PATH_LOGFILES) . '.+\.txt$#', $file, $matches)) {
    if (file_exists($file)) {
        echo '<pre>' . file_get_contents($file) . '</pre>';
    } else {
        die('file not found');
    }
} else {
    die('fileview not allowed');
}

