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

/* tiefe der anzuzeigenden Verschachtelungen angeben */

$menuwidth = 3;

$menuclass="sidemenu";

/* --------- Ende der Konfiguration ----------------------------------------- */

extract($block['settings'], EXTR_OVERWRITE);

global $doc, $doc_cfg;

/* der passende Modulname */
$module_name = basename(dirname(dirname(__FILE__)));
// mxGetLangfile($module_name,"core.lang-*.php");
if (!mxModuleAllowed($module_name)) {
    /* Block darf nicht gecached werden und weg... */
    $mxblockcache = false;
    return;
}
$mxblockcache = false;

$content = "";

include_once(PMX_MODULES_DIR . DS . $module_name . DS . "includes/functions.php");
$cat = $doc;
$rootid = strval($doc->getModulRootID());
$filter = $cat->setFilter("docmenu", "parent_id", "=", "'" . $rootid . "'");
$filter = $cat->setFilter("docmenu", "publish", "=", "1");
$baselist = $cat->getRecordList(0, "", "docmenu");

if (count($baselist) == 0) return;
// $docmenu=md5($module_name.microtime());
$config = $doc->getConfig();
$menuwidth = $config['blockmenuwidth'];

$docmenu = substr(md5(microtime()), 0, 5);
//pmxHeader::add_jquery();

/* pmxHeader::add_style(PMX_MODULES_DIR . DS . $module_name . DS . "style/style.css");
// pmxHeader::add_jquery("jquery.treeview.js");
pmxHeader::add_style(PMX_MODULES_DIR . DS . $module_name . DS . "style/navigation.css");

pmxHeader::add_script(PMX_MODULES_DIR . DS . $module_name . DS . "includes/indexslide.js");
pmxHeader::add_jquery("jquery.treeview.js");
pmxHeader::add_script_code("$(document).ready(function(){
	$('#docmenu_" . $docmenu . "').treeview({
		persist: \"location\",
		collapsed: true,
		unique: true
	});
});");
// $docmenu=time();//md5($base['id']); */

$content .= "<div  id=\"docmenu_" . $docmenu . "\" style=\"margin-right: 0px;\" ><ul class=\"vmenu\">";//".$menuclass."
foreach($baselist as $base) {
    if ($config['blockmenucontent'] && array_key_exists($base['id'],$config['blockmenucontent']) ) {
	  if ($config['blockmenucontent'][$base['id']] == 1) {
        $content .= "<li>";
        $markclass = (true)?"collapsable":"";
        $content .= "<a class=\"" . $markclass . "\" href=\"modules.php?name=" . $module_name . "&amp;act=page&amp;id=" . $base['id'] . "\" title=\"" . $base['title'] . "\">" . $base['title'] . "</a>";
        $content .= $cat->content_get_html($base['id'], 0, "", false, ($menuwidth));
        // $content .= "";
        $content .= "</li>";
	   }
    }
}
$content .= "</ul></div>";

$cat->delFilter("docmenu");

?>