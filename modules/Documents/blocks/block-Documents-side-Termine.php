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
/* der passende Modulname */
$module_name = basename(dirname(dirname(__FILE__)));

if (!mxModuleAllowed($module_name)) {
    /* Block darf nicht gecached werden und weg... */
    $mxblockcache = false;
    return;
}
global $doc, $doc_cfg;
extract($block['settings'], EXTR_OVERWRITE);

include_once(PMX_MODULES_DIR . DS . $module_name . DS . "includes/functions.php");

/* --------- Konfiguration fuer den Block ----------------------------------- */
// Anzahl Artikel
$newscount = $doc_cfg['newscount'];

/* --------- Ende der Konfiguration ----------------------------------------- */



// mxGetLangfile($module_name,"core.lang-*.php");
$cat = $doc;
$content = "";
$rootid = $cat->getBookRootID();
$basisid=3;
$news = $cat->getRecords_New(99, $newscount, $basisid);

if ($news === false) {
    // add by Andi: Fehlermeldung vermeiden, falls keine Datensätze vorhanden
    $mxblockcache = false;
    return;
}
$newscount = count($news);
$doc_cfg['cutlen'] = ($doc_cfg['cutlen'] == 0)?100:$doc_cfg['cutlen'];

if ($newscount > 0) {
	$blockfiletitle = "Termine";
    $content .= "<ul class=\"menu\">";
    $i = 0;
    $width = 100;
    foreach ($news as $cats) {
		if ($basisid <> $cats['id']) {
			$book = $cat->getBookRoot($cats['id']);
			$bookstitle = $book['title'];
			$content .= "<li><a href=\"modules.php?name=$module_name&amp;act=page&amp;id=" . $cats['id'] . "\">" . $cats['title'] . "</a>";
			$content .= "</li>";
		}
    }

    $content .= "</ul>";
}

?>