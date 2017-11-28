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
 * $Revision: 206 $
 * $Author: PragmaMx $
 * $Date: 2016-09-12 13:33:26 +0200 (Mo, 12. Sep 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

/* Sprachdatei auswählen */
mxGetLangfile(dirname(__FILE__));

/* URL für pragmaMx Versionsinfo */
$show_pragmamx_news_url = 'http://www.pragmamx.org/infonews.php?v=' . urlencode(PMX_VERSION_NUM) . '&amp;lang=' . ($GLOBALS['currentlang']);
$show_pragmamx_doku_url = 'http://www.pragmamx.org/Documents.html';

if (!isset($show_pragmamx_news)) {
    // falls noch nicht gespeichert > einschalten
    $show_pragmamx_news = 1;
}

/**
 * pmx_get_admin_dashboard()
 *
 * @return
 */
function pmx_get_admin_dashboard()
{
    global $show_pragmamx_news, $show_pragmamx_news_url, $show_pragmamx_doku_url, $check_chmods;

    $err_dir = '';
    $err_file = '';
    $falsechmods = false;
    if ($check_chmods) {
        $err_dir = array();
        $err_file = array();
        admin_chmods::check($err_dir, $err_file);
        $falsechmods = array_merge($err_file, $err_dir);
    }

    $newentries = pmx_get_adminnews();
	

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->assign(compact('show_pragmamx_news', 'show_pragmamx_news_url', 'falsechmods', 'show_pragmamx_doku_url', 'newentries'));
    $content= $template->fetch('dashboard.html');
	
	return $content;
}

/**
 * pmx_admin_main()
 * das Hauptmenue des Adminbereiches
 *
 * @return
 */
function pmx_admin_main()
{
    include('header.php');
    echo pmx_get_admin_dashboard();
	include('footer.php');
}

/**
 * pmx_admin_main_intab()
 * das Hauptmenue des Adminbereiches
 *
 * @return
 */
function pmx_admin_main_intab()
{
    echo pmx_get_admin_dashboard();
}

/* Was ist zu tun ? */
switch ($op) {
    // case 'main/intabs';
    // pmx_admin_main_intab();
    // break;
    case 'main/infonews';
        /* Ajax Inhalt für pragmaMx Informationen */
        header('Content-type: text/html; charset=utf-8');
        pmx_admin_get_infonews();
        break;
    default:
        pmx_admin_main();
        break;
}

?>