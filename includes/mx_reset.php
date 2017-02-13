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

defined('mxMainFileLoaded') or die('access denied');

function resetPmxCache()
{
    /* Blockcache zuruecksetzen */
    resetBlockCache();

    /* Themecache, jpCache, etc. zuruecksetzen */
    $cachedirs = array(
        PMX_DYNADATA_DIR . '/cache',
        PMX_MODULES_DIR . '/Forum/dynastyles',
        PMX_MODULES_DIR . '/Gallery/themes_styles',
        PMX_REAL_BASE_DIR . '/themecache',
        );
    $excludes = array('index.html', 'index.htm', '.htaccess', 'readme.txt', 'readme');
    foreach ($cachedirs as $dir) {
        $dir = str_replace('/', DS, $dir);
        if (file_exists($dir)) {
            $handle = opendir($dir);
            while (false !== ($file = readdir($handle))) {
                $filename = $dir . DS . $file;
                if (is_file($filename) && !in_array(strtolower($file), $excludes)) {
                    mx_chmod($filename, PMX_CHMOD_FULLUNOCK);
                    if (!unlink($filename)) {
                        trigger_error(__function__ . ': can\'t delete file ' . $filename, E_USER_NOTICE);
                    }
                }
            }
            closedir($handle);
        }
    }

    /* Dateiname mit header.php abgleichen !! */
    foreach ((array)glob(PMX_LAYOUT_DIR . DS . 'style' . DS . '_theme.*.css', GLOB_NOSORT) as $filename) {
        if ($filename && is_file($filename) && !unlink($filename)) {
            trigger_error(__function__ . ': can\'t delete file ' . $filename, E_USER_NOTICE);
        }
    }
}

/**
 * cache nur fuer die Bloecke, bzw. einen Block, zuruecksetzen
 */
function resetBlockCache($bid = false)
{
    global $prefix;

    $qrybid = ($bid !== false && intval($bid)) ? ' AND bid=' . intval($bid) . ' ' : '';

    $ids = array();
    $qry = "SELECT bid FROM `${prefix}_blocks` WHERE blockfile<>'' AND refresh > 0 AND url='' " . $qrybid . "";
    $result = sql_system_query($qry);
    while (list($bid) = sql_fetch_row($result)) {
        $ids[] = $bid;
    }

    if ($ids) {
        $qry = "UPDATE `${prefix}_blocks` SET content='', time=0 WHERE bid in(" . implode(',', $ids) . ")";
        sql_system_query($qry);
    }
}

?>