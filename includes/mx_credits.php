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
 * $Revision: 296 $
 * $Author: PragmaMx $
 * $Date: 2016-12-13 12:53:33 +0100 (Di, 13. Dez 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

$worker['developer'][] = 'Olaf Herfurth'; // seit 1.12
$worker['developer'][] = 'Andreas Eichler'; // since 2.2
$worker['developer'][] = 'Sven Hedström-Lang'; // since 2.4

$worker['former_developer'][] = 'Thierry Roussel';
$worker['former_developer'][] = 'Sıtkı Özkurt';

$worker['former_developer'][] = 'Andreas Ellsel';
$worker['former_developer'][] = 'Hajo Grunert';
$worker['former_developer'][] = 'René Henschke';
$worker['former_developer'][] = 'David Heidrich';
$worker['former_developer'][] = 'Olaf Lücke';
$worker['former_developer'][] = 'Murat Yıldız';
$worker['former_developer'][] = 'Markus von Waechter';
$worker['former_developer'][] = 'Marco Andreas';
$worker['former_developer'][] = 'Frank Möhring';
$worker['former_developer'][] = 'Siggi Braunert';
$worker['former_developer'][] = 'Zeljko Ljepojevic';
$worker['former_developer'][] = 'Nilay Deniz';
$worker['former_developer'][] = 'Jörg Küsters';

$worker['translator'][] = 'Inge Galepp';
$worker['translator'][] = 'Thierry Roussel (' . _LANGFRENCH . ')';
$worker['translator'][] = 'Murat Yıldız (' . _LANGTURKISH . ')';
$worker['translator'][] = 'Sıtkı Özkurt (' . _LANGTURKISH . ')';
$worker['translator'][] = 'Klaus Mattern';
$worker['translator'][] = 'Christopher Peterwerth';
$worker['translator'][] = 'Katrin Wolf';
$worker['translator'][] = 'Kris';
$worker['translator'][] = 'Jörg Küsters';
#$worker['translator'][] = 'Siggi Braunert';
#$worker['translator'][] = 'Miguel Sirna (' . _LANGSPANISH . ')';
$worker['translator'][] = 'Frank Möhring';
$worker['translator'][] = 'Hajo Sackmann (' . _LANGGERMAN . ')';
$worker['translator'][] = 'Wilhelm Möllering (' . _LANGDANISH . ')';
$worker['translator'][] = 'Mustafa Navruz (' . _LANGTURKISH . ')';
$worker['translator'][] = 'Jörg Fiedler (' . _LANGGERMAN . ')';
$worker['translator'][] = 'Prakash Shetty (' . _LANGENGLISH . ')';
$worker['translator'][] = 'Jochem Schausten (' . _LANGGERMAN . ')';
$worker['translator'][] = 'Markus Kahle (' . _LANGGERMAN . ')';

/**
 */
function mxcredit_gethtml()
{
    global $worker;

    /* Sprachdatei auswaehlen */
    mxGetLangfile(PMX_LANGUAGE_DIR . DS . 'credits');

    #sort($worker['leader'], SORT_STRING);
    #sort($worker['developer'], SORT_STRING);
    sort($worker['former_developer'], SORT_STRING);
    sort($worker['translator'], SORT_STRING);
    #$c_leader = implode(', ', $worker['leader']);
    $c_developer = implode(', ', $worker['developer']);
    $c_formerdeveloper = implode(', ', $worker['former_developer']);
    $c_translator = implode(', ', $worker['translator']);

    $content = '
    	<div class="credits" style="margin-bottom: 10px;">
     		<h3>' . _CREDITS_TITLE . '</h3>
     		<ul>
     			<li><strong>' . _CREDITS_TITLE_DEVELOPER . ':</strong><br />' . $c_developer . '</li>
     			<li><strong>' . _CREDITS_TITLE_FORMERDEVELOPER . ':</strong><br />' . $c_formerdeveloper . '</li>
     			<li><strong>' . _CREDITS_TITLE_TRANSLATOR . ':</strong><br />' . $c_translator . ' ' . _CREDITS_TRANSLATOR_MORE . '</li>
     		</ul>
     </div>
     <div class="credits" style="margin-bottom: 10px;">
     		<h3>pragmaMx</h3>
     		<ul>
     			<li>' . _CREDITS_INSPIRED . '</li>
     			<li>' . _CREDITS_LICENSE1 . '</li>
     			<li>' . _CREDITS_LICENSE2 . '</li>
     			<li>' . _CREDITS_MORE . '</li>
     		</ul>
     </div>
     <div class="credits" style="margin-bottom: 10px;">
     		<h3>' . _CREDITS_TITLE_GRAFICS . '</h3>
     		<ul>
     			<li><strong>' . _CREDITS_TITLE_MOSTICONS . '</strong><br />' . _CREDITS_MOSTICONS . '</li>
     			<!--<li><strong>' . _CREDITS_TITLE_ADMINICONS . '</strong><br />' . _CREDITS_ADMINICONS . '</li>-->
     			<li><strong>' . _CREDITS_TITLE_SMALLICONS . '</strong><br />' . _CREDITS_SMALLICONS . '</li>
     			<li><strong>' . _CREDITS_TITLE_FLAGS . '</strong><br />' . _CREDITS_FLAGS . '</li>
     			<!--<li><strong>' . _CREDITS_TITLE_GREENSMILIES . '</strong><br />' . _CREDITS_GREENSMILIES . '</li>-->
				<li><strong>' . _CREDITS_TITLE_AVATARS . '</strong><br />' . _CREDITS_AVATARS . '</li>
     		</ul>
     </div>
     ';
    return $content;
}

/**
 * Gibt Copyright-Informationen aus
 */
function mxcredit_getinfo()
{
    $intro = mxcredit_gethtml();
    if (!defined('DIR_mod_root')) {
        define('DIR_mod_root', 'modules'); /// fuer pragma pmx's
    }

    $module_info = '';
    foreach ((array)glob('modules/*/setup.pmx.php', GLOB_NOSORT) as $filename) {
        $credits = null;
        $file = basename(dirname($filename));
        if ($filename && (MX_IS_ADMIN || mxModuleAllowed($file))) {
            include($filename);
            $module_info .= '<h4>' . $file . '</h4>';
            if ($credits) {
                $module_info .= '<p>' . mxcredit_prepare($credits) . '</p>';
            }
        }
    }
    if ($module_info) {
        $module_info = '<h2 class="title">' . _SYSINFOMODULES . '</h2>' . $module_info;
    }
    $design_info = '';
    foreach ((array)glob('themes/*/setup.pmx.php', GLOB_NOSORT) as $filename) {
        if ($filename) {
            $credits = null;
            $file = basename(dirname($filename));
            include($filename);
            $design_info .= '<h4>' . $file . '</h4>';
            if ($credits) {
                $design_info .= '<p>' . mxcredit_prepare($credits) . '</p>';
            }
        }
    }
    if ($design_info) {
        $design_info = '<h2 class="title">' . _SYSINFOTHEMES . '</h2>' . $design_info;
    }

    $out['intro'] = $intro;
    $out['module_info'] = $module_info;
    $out['design_info'] = $design_info;
    return $out;
}

/* die Ausgabe der setup.pmx.php aufbereiten */
function mxcredit_prepare($credits)
{
    if ($credits) {
        $credits = " " . strip_tags($credits);
        $credits = preg_replace('#\s+[-_=.:~*!%]{10,}\s+#', "\n", $credits);
        $credits = preg_replace('/&(?![a-zA-Z]{2,6};|#[0-9]{2,3};)/', '&amp;', $credits);
        $credits = preg_replace("#(\s)([a-z]+?)://([^, \n\r<]+)#i", "\\1 <a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a> ", $credits);
        $credits = preg_replace("#(\s)www\.([a-z0-9\-]+)\.([a-z0-9\-.\~]+)((?:/[^, \n\r<]*)?)#i", "\\1 <a href=\"http://www.\\2.\\3\\4\" target=\"_blank\">www.\\2.\\3\\4</a> ", $credits);
        $credits = preg_replace("#(\s)([a-z0-9\-_.]+?)@([^, \n\r<]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $credits);
        $credits = nl2br(trim($credits));
    }
    return $credits;
}

/**
 * Ausgabe der Informationen des pragmaMx-Developer-Team
 * Das Entfernen der Zeilen verhindert nicht die Ausgabe,
 * zerstoert aber das Layout der Seite
 */
function mxcredit()
{
    @define('MX_MODULE', 'mxcredit');
    include('header.php');
    OpenTable();
    echo implode('<br />', mxcredit_getinfo());
    CloseTable();
    include('footer.php');
    die();
}

?>