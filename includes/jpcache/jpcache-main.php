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
 *
 * this file based on:
 * jpcache
 * Copyright 2001 - 2003 Jean-Pierre Deckers <jp@jpcache.com>
 */

defined('mxMainFileLoaded') or die('access denied');

/* Take a wild guess... */
function jpcache_debug($s)
{
    static $jpcache_debugline;

    if ($GLOBALS["JPCACHE_DEBUG"]) {
        $jpcache_debugline++;
        header("X-CacheDebug-$jpcache_debugline: $s");
        error_log($s);
        // error_log ('debug: ' . $s . ' - ' . ob_get_level(), 0);
    }
}

/* jpcache_key()
 *
 * Returns a hashvalue for the current. Maybe md5 is too heavy,
 * so you can implement your own hashing-function.
 */
function jpcache_key()
{
    $key = md5(jpcache_scriptkey() . jpcache_varkey());
    jpcache_debug("Cachekey is set to $key");
    return $key;
}

/* jpcache_varkey()
 *
 * Returns a serialized version of GET vars
 * If you want to take cookies into account in the varkey too,
 * add them inhere.
 */
function jpcache_varkey()
{
    $varkey = 'GET=' . serialize($_GET);
    jpcache_debug("Cache varkey is set to $varkey");
    return $varkey;
}

/* jpcache_scriptkey()
 *
 * Returns the script-identifier for the request
 */
function jpcache_scriptkey()
{
    $name = $_SERVER['PHP_SELF'];
    // Commandline mode will also fail this one, I'm afraid, as there is no
    // way to determine the scriptname
    if ($name == "") {
        $name = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
    }

    jpcache_debug("Cache scriptkey is set to $name");
    return $name;
}

/* jpcache_check()
 *
 */
function jpcache_check()
{
    // error_log ('jpcache_check ' . ob_get_level(), 0);
    if (!$GLOBALS["JPCACHE_ON"]) {
        jpcache_debug('Cache has been disabled!');
        return false;
    }
    // We need to set this global, as ob_start only calls the given method
    // with no parameters.
    $GLOBALS["jpcache_key"] = jpcache_key();
    // Can we read the cached data for this key ?
    if (jpcache_restore()) {
        jpcache_debug('Cachedata for ' . $GLOBALS["jpcache_key"] . ' found, data restored');
        return true;
    } else {
        // No cache data (yet) or unable to read
        jpcache_debug('No (valid) cachedata for ' . $GLOBALS["jpcache_key"]);
        return false;
    }
}

/* jpcache_encoding()
 *
 * Are we capable of receiving gzipped data ?
 * Returns the encoding that is accepted. Maybe additional check for Mac ?
 */
function jpcache_encoding()
{
    if (headers_sent() || connection_aborted()) {
        return false;
    }
    if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
        return 'x-gzip';
    }
    if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
        return 'gzip';
    }
    return false;
}

/* jpcache_init()
 *
 * Checks some global variables and might decide to disable caching
 */
function jpcache_init()
{
    // error_log ('jpcache_init ' . ob_get_level(), 0);
    // Override default JPCACHE_TIME ?
    if (isset($GLOBALS["cachetimeout"])) {
        $GLOBALS["JPCACHE_TIME"] = $GLOBALS["cachetimeout"];
    }
    // Force cache off when POST occured
    if ((count($_POST) > 0)) {
        $GLOBALS["JPCACHE_ON"] = 0;
        $GLOBALS["JPCACHE_TIME"] = -1;
    }
    // A cachetimeout of -1 disables writing, only ETag and content encoding
    if ($GLOBALS["JPCACHE_TIME"] == -1) {
        $GLOBALS["JPCACHE_ON"] = 0;
    }
    // Sort GET params ? (so cache of file.php?a=1&b=2 == cache of file.php?b=2&a=1) collection is executed.
    if ($GLOBALS["JPCACHE_ON"]) {
        ksort($_GET);
    }
    // Output header to recognize version
    header('X-Cache: pmxcache v' . $GLOBALS["JPCACHE_VERSION"] . ' - ' . $GLOBALS["JPCACHE_TYPE"]);
}

/* jpcache_gc()
 *
 * Checks if garbagecollection is needed.
 */
function jpcache_gc()
{
    // Should we garbage collect ?
    if ($GLOBALS["JPCACHE_GC"] > 0) {
        mt_srand(time(null));
        $precision = 100000;
        // Garbagecollection probability
        if (((mt_rand() % $precision) / $precision) <=
                ($GLOBALS["JPCACHE_GC"] / 100)) {
            jpcache_debug('GarbageCollection hit!');
            jpcache_do_gc();
        }
    }
}

/* jpcache_start()
 *
 * Sets the handler for callback
 */
function jpcache_start()
{
    // error_log ('jpcache_start ' . ob_get_level(), 0);
    // Initialize cache
    jpcache_init();
    // Handle type-specific additional code if required
    jpcache_do_start();
    // Check cache
    if (jpcache_check()) {
        // Cache is valid and restored: flush it!
        print jpcache_flush($GLOBALS["jpcachedata_gzdata"],
            $GLOBALS["jpcachedata_datasize"],
            $GLOBALS["jpcachedata_datacrc"]);
        // Handle type-specific additional code if required
        jpcache_do_end();
        exit;
    } else {
        // if we came here, cache is invalid: go generate page
        // and wait for jpCacheEnd() which will be called automagically
        // Check garbagecollection
        jpcache_gc();
        // Go generate page and wait for callback
        ob_start('jpcache_end');
        ob_implicit_flush(0);
    }
}

/* jpcache_end()
 *
 * This one is called by the callback-funtion of the ob_start.
 */
function jpcache_end($contents)
{
    if (!defined('PMX_HEADER')) {
        // Cache nur schreiben, wenn header.php eingebunden wurde,
        // also nicht bei .css Dateien etc.
        $GLOBALS["JPCACHE_ON"] = false;
    }
    // error_log ('jpcache_end ' . ob_get_level(), 0);
    jpcache_debug('Callback happened');

    $datasize = strlen($contents);
    $datacrc = crc32($contents);
    // If the connection was aborted, do not write the cache.
    // We don't know if the data we have is valid, as the user
    // has interupted the generation of the page.
    // Also check if jpcache is not disabled
    if ($contents && (!connection_aborted()) && $GLOBALS["JPCACHE_ON"] && ($GLOBALS["JPCACHE_TIME"] >= 0)) {
        jpcache_debug("Writing cached data to storage");
        // write the cache with the current data
        jpcache_write($contents, $datasize, $datacrc);
    }
    // Handle type-specific additional code if required
    jpcache_do_end();
    // Return flushed data
    return jpcache_flush($contents, $datasize, $datacrc);
}

/* jpcache_flush()
 *
 * Responsible for final flushing everything.
 * Sets ETag-headers and returns "Not modified" when possible
 *
 * When ETag doesn't match (or is invalid), it is tried to send
 * the gzipped data. If that is also not possible, we sadly have to
 * uncompress (assuming JPCACHE_USE_GZIP is on)
 */
function jpcache_flush($gzdata, $datasize, $datacrc)
{
    // First check if we can send last-modified
    $myETag = "\"jpd-$datacrc.$datasize\"";
    header("ETag: $myETag");
    $foundETag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) : '';

    if (strstr($foundETag, $myETag)) {
        // Not modified!
        if (stristr($_SERVER['SERVER_SOFTWARE'], 'microsoft')) {
            // IIS has already sent a HTTP/1.1 200 by this stage for
            // some strange reason
            header('Status: 304 Not Modified');
        } else {
            header('HTTP/1.0 304');
        }
        return null;
    }

    $check = '<!-- mxgetuserlogincheckfield_' . md5($GLOBALS['mxSecureKey']) . ' -->';
    $gzdata = str_replace($check, mxGetUserLoginCheckField(true), $gzdata);

    return $gzdata;
}

?>