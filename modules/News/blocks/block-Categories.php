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
/* --------- Ende der Konfiguration ----------------------------------------- */
// extract($block['settings'], EXTR_OVERWRITE);
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

$cat = (empty($_REQUEST['cat'])) ? 0 : intval($_REQUEST['cat']);

$a = 0;

$qry = "SELECT c.catid, c.title, COUNT(s.sid) AS anzahl
        FROM ${prefix}_stories AS s
        INNER JOIN ${prefix}_stories_cat AS c
          ON s.catid = c.catid
        WHERE  time  <= now() " . pmx_multilang_query('alanguage', 'AND') . "
        GROUP BY c.catid
        ORDER BY c.title";
$result = sql_query($qry);

$items = array();

if ($cat != 0) {
    $items[] = array('title' => _ALLCATEGORIES, 'link' => 'modules.php?name=' . $module_name);
}
/* Schleifchen ;-) */
while (list($catid, $title, $counter) = sql_fetch_row($result)) {
    if ($cat == $catid) {
        $items[] = array('title' => $title, 'link' => '');
    } else {
        $items[] = array('title' => $title, 'link' => 'modules.php?name=' . $module_name . '&amp;file=categories&amp;catid=' . $catid);
    }
}

if (!$items) {
    return;
}
// $blockfiletitle = _CATEGORIES1;
/* Templateausgabe erstellen */
$tpl = load_class('Template');
$tpl->init_path(__FILE__);
$tpl->init_template(__FILE__);
$tpl->items = $items;
$content = $tpl->fetch();

?>
