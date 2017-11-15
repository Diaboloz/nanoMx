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
 * $Revision: 259 $
 * $Author: PragmaMx $
 * $Date: 2016-11-27 18:20:19 +0100 (So, 27. Nov 2016) $
 *
 * Credits:
 * this file based on:
 * jpcache
 * Copyright 2001 - 2003 Jean-Pierre Deckers <jp@jpcache.com>
 * Based upon and inspired by:
 * - phpCache        <nathan@0x00.org> (http://www.0x00.org/phpCache)
 * - gzdoc.php       <catoc@163.net> and <jlim@natsoft.com.my>
 * - jr-cache.php    <jr-jrcache@quo.to>
 *
 * More info on http://www.jpcache.com/
 */

defined('mxMainFileLoaded') or die('access denied');

$JPCACHE_VERSION = 2;

if (empty($GLOBALS['mxJpCacheUse']) or PMX_CACHE_AKTIVE==false) {
    // einfach beenden
    return;
}

/**
 * File based caching setting.
 */
$JPCACHE_TYPE = 'file';
$JPCACHE_DIR = PMX_DYNADATA_DIR . '/cache'; // Directory where jpcache must store generated files. Please use a dedicated directory, and make it writable
$JPCACHE_FILEPREFIX = "jpc-"; // Prefix used in the filename. This enables us to (more accuratly) recognize jpcache-files.

/**
 * General configuration options.
 */
$JPCACHE_ON = 1;

if (empty($GLOBALS['mxJpCacheTimeout'])) {
    if ($JPCACHE_ON) {
        $JPCACHE_TIME = MX_SETINACTIVE_MINS;
    } else {
        $JPCACHE_TIME = -1;
    }
} else {
    $JPCACHE_TIME = $GLOBALS['mxJpCacheTimeout'];
}
$JPCACHE_DEBUG = 0; // Turn debugging on/off
$JPCACHE_GC = 1; // Probability % of garbage collection

/* Standard functions*/
require "includes/jpcache/jpcache-main.php";

/* Start caching */
jpcache_start();

/* zusaetzlichen Ausgabepuffer starten, damit nach dem Cache die Tidy und mod_rewrite Funktionen funktionieren */
ob_start();

/* jpcache_restore()
 *
 * Will (try to) restore the cachedata.
 */
function jpcache_restore()
{
    // Construct filename
    $filename = $GLOBALS["JPCACHE_DIR"] . "/" . $GLOBALS["JPCACHE_FILEPREFIX"] . $GLOBALS["jpcache_key"];
    // read file and unserialize the data
    $cachedata = unserialize(jpcache_fileread($filename));
    if (is_array($cachedata)) {
        // Only read cachefiles of my version
        if ($cachedata["jpcache_version"] == $GLOBALS["JPCACHE_VERSION"]) {
            if (($cachedata["jpcache_expire"] == "0") ||
                    ($cachedata["jpcache_expire"] >= time())) {
                // Restore data
                $GLOBALS["jpcachedata_gzdata"] = $cachedata["jpcachedata_gzdata"];
                $GLOBALS["jpcachedata_datasize"] = $cachedata["jpcachedata_datasize"];
                $GLOBALS["jpcachedata_datacrc"] = $cachedata["jpcachedata_datacrc"];
                return true;
            } else {
                jpcache_debug("Data in cachefile $filename has expired");
            }
        } else {
            // Invalid version of cache-file
            jpcache_debug("Invalid version of cache-file $filename");
        }
    } else {
        // Invalid cache-file
        jpcache_debug("Invalid content of cache-file $filename");
    }

    return false;
}

/* jpcache_write()
 *
 * Will (try to) write out the cachedata to the db
 */
function jpcache_write($gzdata, $datasize, $datacrc)
{
    // Construct filename
    $filename = $GLOBALS["JPCACHE_DIR"] . "/" . $GLOBALS["JPCACHE_FILEPREFIX"] . $GLOBALS["jpcache_key"];
    // Create and fill cachedata-array
    $cachedata = array();
    $cachedata["jpcache_version"] = intval($GLOBALS["JPCACHE_VERSION"]);
    $cachedata["jpcache_expire"] = ($GLOBALS["JPCACHE_TIME"] > 0) ?
    time() + $GLOBALS["JPCACHE_TIME"] :
    0;
    $cachedata["jpcachedata_gzdata"] = $gzdata;
    $cachedata["jpcachedata_datasize"] = $datasize;
    $cachedata["jpcachedata_datacrc"] = $datacrc;
    // And write the data
    if (jpcache_filewrite($filename, serialize($cachedata))) {
        jpcache_debug("Successfully wrote cachefile $filename");
    } else {
        jpcache_debug("Unable to write cachefile $filename");
    }
}

/* jpcache_do_gc()
 *
 * Performs the actual garbagecollection
 */
function jpcache_do_gc()
{
    $dp = opendir($GLOBALS["JPCACHE_DIR"]);
    // Can we access directory ?
    if (!$dp) {
        jpcache_debug("Error opening " . $GLOBALS["JPCACHE_DIR"] . " for garbage-collection");
    } while (!(($de = readdir($dp)) === false)) {
        // To get around strange php-strpos, add additional char
        // Only read jpcache-files.
        if (strpos("x$de", $GLOBALS["JPCACHE_FILEPREFIX"]) == 1) {
            $filename = $GLOBALS["JPCACHE_DIR"] . "/" . $de;
            // read file and unserializes the data
            $cachedata = unserialize(jpcache_fileread($filename));
            // Check data in array.
            if (is_array($cachedata)) {
                if ($cachedata["jpcache_expire"] != "0" && $cachedata["jpcache_expire"] <= time() && file_exists($filename)) {
                    // Unlink file, we do not need to get a lock
                    $deleted = unlink($filename);
                    if ($deleted) {
                        jpcache_debug("Successfully unlinked $filename");
                    } else {
                        jpcache_debug("Failed to unlink $filename");
                    }
                }
            }
        }
    }
}

/* jpcache_do_start()
 *
 * Additional code that is executed before real jpcache-code kicks in
 */
function jpcache_do_start()
{
    // error_log ('jpcache_do_start ' . ob_get_level(), 0);
    // Add additional code you might require
    pmxDebug::pause();
}

/* jpcache_do_end()
 *
 * Additional code that is executed after caching has been performed,
 * but just before output is returned. No new output can be added!
 */
function jpcache_do_end()
{
    // error_log ('jpcache_do_end ' . ob_get_level(), 0);
    // Add additional code you might require
    pmxDebug::restore();
}

/* This internal function reads in the cache-file */
function jpcache_fileread($filename)
{
    if (is_file($filename)) {
        return file_get_contents($filename);
    }
    jpcache_debug("Failed to open for read of $filename");
    return '';
}

/* This internal function writes the cache-file */
function jpcache_filewrite($filename, $data)
{
    $ok = mx_write_file($filename, $data);
    if ($ok) {
        return true;
    }
    // Strange! We are not able to write the file!
    jpcache_debug("Failed to open for write of $filename");
    return false;
}
// Make sure no additional lines/characters are after the closing-tag!
?>