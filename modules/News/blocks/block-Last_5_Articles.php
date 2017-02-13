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
 * $Revision: 101 $
 * $Author: PragmaMx $
 * $Date: 2015-12-30 21:08:19 +0100 (Mi, 30. Dez 2015) $
 *
 *
 */

defined('mxMainFileLoaded') or die('access denied');

/* --------- Konfiguration fuer den Block ----------------------------------- */
// Anzahl der angezeigten Links
$limit = 5;

/* --------- Ende der Konfiguration ----------------------------------------- */

extract($block['settings'], EXTR_OVERWRITE);

global $prefix;

/* der passende Modulname */
$module_name = basename(dirname(dirname(__FILE__)));

if (!mxModuleAllowed($module_name)) {
    /* Block darf nicht gecached werden und weg... */
    $mxblockcache = false;
    return;
}

/* Block kann gecached werden? */
$mxblockcache = true;

$limit = ($limit) ? ' LIMIT ' . intval($limit) : '';
$storieslist = array();
$storie = array();
$result = sql_query("SELECT sid, title, comments, counter FROM " . $GLOBALS['prefix'] . "_stories " . pmx_multilang_query('alanguage', 'WHERE') . " ORDER BY time DESC, sid DESC " . $limit . "");
while (list($sid, $title, $comments, $counter) = sql_fetch_row($result)) {
    $storie['id'] = $sid;
    $storie['title'] = $title;
    $storie['comments'] = ($GLOBALS['articlecomm'] && $comments >= 1) ? mxValueToString($comments, 0) . ' ' . _COMMENTS : '';
    $storie['counter'] = mxValueToString($counter, 0) . ' ' . _READS;
    $storieslist[] = $storie;
}

if (!$storieslist) {
    return;
}

/* Templateausgabe erstellen */
$tpl = load_class('Template');
$tpl->init_path(__FILE__);
$tpl->init_template(__FILE__);
$tpl->storieslist = $storieslist;
$tpl->module_name = $module_name;
$content = $tpl->fetch();

?>