<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: dia_bolo $
 * $Revision: 1.1.2.3 $
 * $Date: 2012-01-22 16:18:13 $
 *
 * based on eBoard v1.1, rewrite and modified by
 * vkpMx-Developer-Team (http://www.maax-design.de)
 * Original source-code made by the XMB-team
 * (XMB-Forum, http://www.xmbforum.com), modified for nukestyle-systems
 * by Trollix (XForum, http://www.trollix.com).
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */
defined('mxMainFileLoaded') or die('access denied');

// /////////////////////////////////////////////////////////////////////////////
// / ACHTUNG!!!!
// / alle bestehenden VAriablen, die hier nochmal geändert werden,
// / müssen explizit mit "global" importiert werden!!!
// / Dies ist nötig, weil diese Datei manchmal auch in Funktionen includet wird.
// /////////////////////////////////////////////////////////////////////////////
$mxb_temp_design = file_get_contents(dirname(__FILE__) . '/theme.htm');

global $printlink;
$printlink = (!empty($printlink)) ? '' . $printlink . '' : '';

global $ruleslink;
$ruleslink = '';
if ($bbrules == 'on') {
    $ruleslink = "<p class=\"align-center stronger\"><a href=\"" . MXB_BASEMOD . "bbrules\" onclick=\"UnbPopup('" . MXB_BASEMOD . "bbrules&amp;theme=" . MX_THEME . "', 'bbrules', 500, 400); return false;\">" . _TEXTBBRULESWINDOW . "</a></p>";
} else {
    mxb_theme_extract_optional_part($mxb_temp_design, 'ruleslink', '');
}

global $faqlink;
$faqlink = '';
if ($faqstatus == 'on') {
    $faqlink = '<li class="icon-faq"><a href="' . MXB_BM_MISC1 . 'action=faq">' . _TEXTFAQ . '</a></li>';
} else {
    mxb_theme_extract_optional_part($mxb_temp_design, 'faqlink', '');
}

global $searchlink;
if ($searchstatus == 'on') {
    $part[] = array("{SEARCHFORM}", mxbThemeSearchForm());
} else {
    mxb_theme_extract_optional_part($mxb_temp_design, 'searchform', '');
    mxb_theme_extract_optional_part($mxb_temp_design, 'searchlink', '');
}

global $cplink;
if ($eBoardUser['isadmin']) {
    $cplink = '<li class="rightside"><a href="' . MXB_BM_CP0 . '"><i class="fa fa-cog"></i> ' . _TEXTCP . '</a></li>';
} else {
    mxb_theme_extract_optional_part($mxb_temp_design, 'cplink', '');
}

global $jump;
if ($affjumper != 'on') {
    $jump = '';
    mxb_theme_extract_optional_part($mxb_temp_design, 'quickjumper', '');
}
// bestimmte Texte, vor allem Image-Pfade, die ersetzt werden sollen, definieren (suche/ersetze)
$part[] = array("'images/", "'" . MX_BASE_URL . MXB_BASEMODTEMPLATE . '/' . basename(dirname(__FILE__)) . '/images/');
$part[] = array('"images/', '"' . MX_BASE_URL . MXB_BASEMODTEMPLATE . '/' . basename(dirname(__FILE__)) . '/images/');
$mxb_temp_design = mxb_theme_replace_parts($mxb_temp_design, $part);
// komplette Navigationsleiste, Leerzeichen und | drumrum entfernen
// diese Version überschreibt die, in der header.php ;-)
global $completenavbar;
$completenavbar = '
	<ul class="linklist leftside">
		<li><i class="fa fa-user"></i> ' . preg_replace('#(\s*\|\s*){2,}#', ' | ', trim("$proreg | $messslvlink | $messotdlink", '| ')). '</li>
	</ul>';
$completenavbar = (!empty($completenavbar)) ? '' . $completenavbar . '' : '';	
// das Template mit den globalen Variablen belegen
$mxb_temp_design = mxb_theme_replace_vars($mxb_temp_design);
// ///////////////////////////////////////////////////////////////////////////
// / die folgenden Teile muessen in jedem Theme vorhanden sein
// / und die folgenden 3 Arrayschlüssel zurückgeben:
// / - $mxb_template['head']    = die script und stylesheet Tags aus dem Headerbereich
// / - $mxb_template['top']     = vor der eigentlichen Ausgabe
// / - $mxb_template['bottom']  = nach der eigentlichen Ausgabe
$mxb_template['head'] = '';
if (preg_match('#(.*)</head>#si', $mxb_temp_design, $tmp)) {
    if (preg_match_all('#<(script|style)[^>]*>(.*)</\1>#siU', $tmp[1], $tmp)) {
        // die script und stylesheet Tags aus dem Headerbereich
        foreach ($tmp[0] as $key => $value) {
            if ($tmp[1][$key] == 'style') {
                pmxHeader::add_style_code(mxbThemeCompressCss($tmp[2][$key]));
            } else {
                pmxHeader::add($value);
            }
        }
    }
}

/* das Theme-Stylesheet */
pmxHeader::add_style(MXB_BASEMODTEMPLATE . '/' . basename(dirname(__file__)) . '/style.css');

if (preg_match('#<body[^>]*>(.*)</body>#siU', $mxb_temp_design, $tmp)) {
    $tmp = preg_split('#<!--\s*{MX_BOARD_OUTPUT}\s*-->#', $tmp[1]);
    if (isset($tmp[1])) {
        // vor der eigentlichen Ausgabe
        $mxb_template['top'] = $tmp[0];
        // nach der eigentlichen Ausgabe
        $mxb_template['bottom'] = $tmp[1];
    }
}
// Speicher aufräumen
unset($mxb_temp_design, $tmp, $part);

function mxbThemeSearchForm()
{
	global $searchlink;
    $out = '
      <form name="mxbsearch" method="post" action="' . MXB_BM_SEARCH0 . '" id="mxbsearch">
				<fieldset>
					<input type="hidden" name="searchname" value="" />
					<input type="hidden" name="srchfid" value="all" />
					<input type="hidden" name="searchfrom" value="0" />
					<input type="hidden" name="searchin" value="both" />
					<input type="hidden" name="searchsubmit" value="' . _SEARCH . '" />									
					<input id="keywords" type="text" name="keywords" size="20" maxlength="40" class="inputbox search" />
					<input class="button2" value="' . _SEARCH . '" type="submit" />
					' . $searchlink . '
				</fieldset>
			</form>';
    return $out;
}

?>
