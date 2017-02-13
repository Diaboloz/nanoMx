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

$result = sql_query("SELECT sid, title
          FROM ${prefix}_stories
          WHERE (DATE( time ) = CURRENT_DATE)
            AND ( time  <= now())
            AND (title <> '') " . pmx_multilang_query('alanguage', 'AND') . "
          ORDER BY counter DESC limit 0,1");
list($sid, $title) = sql_fetch_row($result);

if (!$sid) {
    return;
}

$link = '<a href="modules.php?name=' . $module_name . '&amp;file=article&amp;sid=' . $sid . '">' . $title . '</a>';

/* Templateausgabe erstellen */
$tpl = load_class('Template');
$tpl->init_path(__FILE__);
$tpl->init_template(__FILE__);
$tpl->link = $link;
$content = $tpl->fetch();

?>