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

/* --------- Konfiguration fuer den Block ----------------------------------- */
// Anzahl SPalten
$xtabs = 2;
// Anzahl Artikel
$randcount = 6;
// max. ausdehnung Thumbnails
$thumbwidth = 100;

/* --------- Ende der Konfiguration ----------------------------------------- */

extract($block['settings'], EXTR_OVERWRITE);
// global $prefix, $user_prefix, $themesetting;
global $doc, $doc_cfg;
// mxGetLangfile($module_name,"core.lang-*.php");
include_once(PMX_MODULES_DIR . DS . $module_name . DS . "includes/functions.php");
$cat = $doc;

$content = "";

$rootid = $cat->getBookRootID();
$cat->setFilter("randomfilter", "parent_id", ">", 0);
$cat->setFilter("randomfilter", "publish", "=", 1);
$news = $cat->getRecords("randomfilter", "Order by rand()", $randcount);

if ($news === false) {
    // add by Andi: Fehlermeldung vermeiden, falls keine Datensätze vorhanden
    $mxblockcache = false;
    return;
}

$newscount = count($news);
// $lastchange=$cat->getRecords_LastChange($doc_cfg['newscount'],$doc_cfg['changescount'],$rootid);
// $lastchangecount=count($lastchange);
$doc_cfg['cutlen'] = ($doc_cfg['cutlen'] == 0)?100:$doc_cfg['cutlen'];
$width = ($newscount > 1)?intval((100 - $xtabs * 3) / $xtabs):100;

if ($newscount > 0) {
    $content .= "<div class=\"postcontent\">";
    $content .= "<ul class=\"mainpage\">";
    foreach ($news as $cats) {
        $book = $cat->getBookRoot($cats['id']);
        if ($book['parent_id'] == $rootid) continue;

        $bookstitle = $book['title'];
        $node['attachments'] = unserialize($cats['attachment']);
        if (!is_array($node['attachments']))$node['attachments'] = array();

        $node['attachment'] = array();
        $nodemedia = "";
        $fnr = 0;

        $attachments = (is_array($node['attachments']))?$node['attachments']:array();
        $attcount = count($attachments);
        if ($attcount) {
            foreach ($attachments as $file) {
                switch (strtolower($file['type'])) {
                    case "image/png":
                    case "image/jpg":
                    case "image/jpeg":
                    case "image/gif":
                        // $nodemedia=content_getAttachmentLink("temp",$file);
                        $nodemedia = "<a href=\"modules.php?name=$module_name&amp;act=page&amp;id=" . $cats['id'] . "\"
                                        title=\"" . $file['name'] . "\" ><img src=\"" . $file['filename'] . "\" alt=\"" . $file['name'] . "\"
                                        style=\"max-width:" . $thumbwidth . "px;max-height:" . $thumbwidth . "px;\" />" . "</a>";
                        break 2;
                        break;
                }
            }
        }

        if ($nodemedia) $nodemedia = "<span style=\"float:left;padding:5px;border:none;\">" . $nodemedia . "</span>";
        $content .= "<li class=\"mainpage\" style=\"display:inline-block;width: " . $width . "%;list-style-type:none;margin:0;padding:5px;vertical-align:top;\"><div class=\"blogstory\">";
        $content .= "<p class=\"tiny\"><a href=\"modules.php?name=$module_name&amp;act=page&amp;id=" . $book['id'] . "\">" . $bookstitle . "</a>";
        $content .= "<span class=\"blogstory-info\" style=\"float:right;\">" . _DOCS_CREATED . " " . _FROM . " : " . $cats['owner_name'] . " "
         . mx_strftime(_XDATESTRING, $cats['date_created']) . "</span></p>";
        $content .= "<h4><a href=\"modules.php?name=$module_name&amp;act=page&amp;id=" . $cats['id'] . "\">" . $cats['title'] . "</a></h4>";
        $content .= "<p class=\"postcontent\" style=\"text-align:justify;\">" . $nodemedia;
        $content .= pmx_cutString($cats['text1'], $doc_cfg['cutlen'], false);
        $content .= "</p></div></li>";
    }

    $content .= "</ul></div>";
    $content .= "<div class=\"block align-center\"><a class=\"button\" title=\"" . _DOCS_READMORE . "\" href=\"modules.php?name=" . $module_name . "\" >" . _DOCS_READMORE . "</a></div>";
}

?>