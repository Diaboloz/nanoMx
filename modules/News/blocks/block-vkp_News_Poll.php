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

/* --------- Konfiguration fuer den Block ----------------------------------- */

/* Name des Umfrage Moduls */
$surveys_mod = 'Surveys';

/* --------- Ende der Konfiguration ----------------------------------------- */
extract($block['settings'], EXTR_OVERWRITE);

global $prefix;

/* die passenden Modulnamen */
$module_name = basename(dirname(__DIR__));

/* Block kann nicht gecached werden? */
$mxblockcache = false;

switch (true) {
    case MX_MODULE != $module_name:
    case empty($GLOBALS['story_blocks']):
    case !mxModuleAllowed($module_name):
    case !$surveys_mod:
    case !mxModuleAllowed($surveys_mod):
    case !($functionfile = realpath(PMX_MODULES_DIR . DS . $surveys_mod . '/includes/functions.php')):
    case !include_once($functionfile):
        return true;
}

/* Artikelzuweisung an Umfrageblock */
$artid = $GLOBALS['story_blocks']['sid'];

$result = sql_query("SELECT pollID
    FROM  {$prefix}_poll_desc 
    WHERE  artid  = " . intval($artid) . "
      AND  pollactive  > 0
    ORDER BY  pollID  DESC
    LIMIT 1");
list($pollID) = sql_fetch_row($result);

$content = poll_block($pollID, $block);

/* Blocktitel aus Sprachdatei auslesen */
$blockfiletitle = _SURVEY;

?>