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
 * $Revision: 214 $
 * $Author: PragmaMx $
 * $Date: 2016-09-15 15:51:34 +0200 (Do, 15. Sep 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

/* --------- Konfiguration fuer den Block ----------------------------------- */
// Anzahl der angezeigten Links
$limit = 5;
// Bloc title, comment and/or change if you have change $limit and need custom title
$blockfiletitle = ucfirst(_FIVELASTARTICLES);

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

if (MX_MODULE != $module_name || empty($GLOBALS['story_blocks'])) {
    /* Block kann gecached werden? */
    $mxblockcache = false;
    return;
}
/* Block kann gecached werden? */
$mxblockcache = true;

$where[] = "time < ".time()."";
$where[] = (empty($_REQUEST['sid'])) ? "" : " (sid <> " . intval($_REQUEST['sid']) . ")";
$where[] = pmx_multilang_query('alanguage');
$where = implode(' AND ', $where);

$limit = ($limit) ? ' LIMIT ' . intval($limit) : '';
$storieslist = array();
$storie = array();
$where .= "true";//(trim($where)=="")?" true ":$where;
$result = sql_query("SELECT sid, title FROM " . $GLOBALS['prefix'] . "_stories WHERE " . $where . " ORDER BY time DESC, sid DESC " . $limit);
while (list($sid, $title) = sql_fetch_row($result)) {
    $storie['id'] = $sid;
    $storie['title'] = $title;
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