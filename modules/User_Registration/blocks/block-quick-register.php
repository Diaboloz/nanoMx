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

echo '<p class="warning">', basename(__DIR__), DS, basename(__FILE__), '<br>BLOCK IST NICHT FERTIG!!!</p>';
/* TODO: Block genau überprüfen !!!! */
// TODO:
// - Templates
// - config import
defined('mxMainFileLoaded') or die('access denied');

/* der passende Modulname */
$module_name = basename(dirname(dirname(__FILE__)));

if (MX_IS_USER || (!mxModuleAllowed($module_name))) {
    return;
}

$mxblockcache = false;

mxGetLangfile('Your_Account');

$blockfiletitle = _QUICKREGISTER ;

include_once(PMX_SYSTEM_DIR . DS . "mx_userfunctions.php");

$userconfig = load_class('Userconfig');
$pvs['uid'] = (empty($pvs['uid'])) ? 0 : $pvs['uid'];
$pvs['uname'] = (empty($pvs['uname'])) ? '' : $pvs['uname'];
$pvs['user_bday'] = (empty($pvs['user_bday'])) ? '0000-00-00' : $pvs['user_bday'];

if (function_exists("vkpUserform_option")) {
    return vkpUserform_option($pvs);
}

$cbday = vkpBdaySelect($pvs['user_bday']);
if ($userconfig->yaproofdate) {
    // $cbday .= ' ' . _REQUIRED . '<br />' . _APPROVEDATE1 . "&nbsp;<strong>" . $userconfig->yaproofdate . "</strong>&nbsp;" . _APPROVEDATE2;
    $cbday .= ' ' . _REQUIRED . '<br />' . sprintf(_ERRAPPROVEDATE, $userconfig->yaproofdate);
}

$content = "<form name=\"Register\" action=\"modules.php?name=" . $module_name . "\"  method=\"post\">\n"

 . "<table cellspacing=\"1\" cellpadding=\"3\" class=\"bgcolor1\">"
 . "<tr valign=\"top\"><th colspan=\"4\" class=\"bgcolor2 bigger\">" . _YA_ACCOUNTDATA . "</th></tr>"
 . "<tr valign=\"top\"><td width=\"20%\" class=\"bgcolor2\"><b>" . _NICKNAME . ":</b></td><td colspan=\"3\" class=\"bgcolor3\">\n"
 . "<input type=\"text\" name=\"uname\" size=\"50\" maxlength=\"25\" value=\"" . ((isset($oldvals['uname'])) ? mxentityquotes($oldvals['uname']) : "") . "\" />&nbsp;&nbsp;<font class=\"tiny\">" . _REQUIRED . "</font></td></tr>\n"
 . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _UREALEMAIL . ":</b></td><td colspan=\"3\" class=\"bgcolor3\">\n"
 . "<input type=\"text\" name=\"email\" size=\"50\" maxlength=\"100\" value=\"" . ((isset($oldvals['email'])) ? mxentityquotes($oldvals['email']) : "") . "\" />&nbsp;&nbsp;<font class=\"tiny\">" . _REQUIRED . "</font><br /><span class=\"tiny\">" . _EMAILNOTPUBLIC . "</span></td></tr>\n"
 . "<tr valign=\"top\"><td width=\"20%\" class=\"bgcolor2\"><b>" . _YA_UBDAY . ":</b></td><td colspan=\"3\" width=\"80%\" class=\"bgcolor3\">\n" . $cbday . "</td></tr>\n";

switch ($userconfig->register_option) {
    case 1:
    case 3:
    case 4:
        $xpass = pmx_password_create();
        pmx_html_passwordchecker();
        $msg = _PASSWILLSEND;
        $content .= "<tr valign=\"top\"><td class=\"bgcolor2\">
        <b>" . _DESIREDPASS . ":</b>
        <input type=\"hidden\" name=\"xpass\" value=\"" . $xpass . "\" />
        </td><td colspan=\"3\" class=\"bgcolor3\">\n
        <input type=\"password\" name=\"pass\" size=\"22\" value=\"\" class=\"password-checker-input\" />&nbsp;
        <input type=\"password\" name=\"vpass\" size=\"22\" value=\"\" class=\"password-checker-input\" />&nbsp; " . sprintf(_OPTIONAL1, $userconfig->minpass) . "\n<br />
        <span class=\"tiny\">" . _OPTIONAL2 . " " . _YA_PWVORSCHLAG . ":&nbsp;" . $xpass . "</span>
        </td></tr>\n";
        break;

    case 2:
        $msg = _YA_REG_MAILMSG2;
        break;
}
// echo vkpUserform($oldvals);
/**
 * START Benutzer muessen den AGB zustimmen - 2005-18-05  (RtR)
 */
if ($userconfig->agb_content) {
    $content .= "<tr valign=\"top\"><td width=\"20%\" class=\"bgcolor2\"></td><td colspan=\"3\" class=\"bgcolor3\">\n";
    switch ($userconfig->agb_content) {
        case "1":
            $agb_out = $userconfig->agb_content_sub1;
            $moduleslist = sql_query("SELECT DISTINCT artid FROM " . $prefix . "_seccont WHERE artid='$agb_out'");
            list($linkartid) = sql_fetch_row($moduleslist);
            if (!empty($linkartid)) {
                $linkout = "modules.php?name=Sections&amp;op=viewarticle&amp;artid=$linkartid";
            } else {
                $linkout = "#>";
            }
            break;
        case "2":
            $agb_out = $userconfig->agb_content_sub2;
            $moduleslist = sql_query("SELECT DISTINCT pid FROM " . $prefix . "_pages WHERE pid='$agb_out'");
            list($linkpid) = sql_fetch_row($moduleslist);
            if (!empty($linkpid)) {
                $linkout = "modules.php?name=Content&amp;pid=$linkpid";
            } else {
                $linkout = "#>";
            }
            break;
        case "3":
            $agb_out = $userconfig->agb_content_sub3;
            $moduleslist = sql_query("SELECT DISTINCT title FROM " . $prefix . "_modules WHERE mid='$agb_out'");
            list($linktitle) = sql_fetch_row($moduleslist);
            if (!empty($linktitle)) {
                $linkout = "modules.php?name=$linktitle";
            } else {
                $linkout = "#>";
            }
            break;
        default:
            $linkout = "#>";
    }
    $content .= "<input type=\"checkbox\" name=\"readrules\" value=\"1\" /> " . _IHAVE . " <a href=\"{$linkout}\" target=\"_blank\" title=\"" . _LEGAL . " " . _SHOWIT . "\"><strong>" . _LEGAL . "</strong></a> " . _READDONE . " " . _REQUIRED . "</font></td></tr>\n";
}
/**
 * ENDE Benutzer muessen den AGB zustimmen
 */
$content .= "<tr class=\"bgcolor2\"><td>\n<input type=\"submit\" value=\"" . _NEWUSER . "\" />&nbsp;&nbsp;</td>"
 . "<td colspan=\"3\">" . $msg . "\n"
 . "<input type=\"hidden\" name=\"op\" value=\"confirm\" />\n"
 . "<input type=\"hidden\" name=\"name\" value=\"" . $module_name . "\" />\n"
 . "<input type=\"hidden\" name=\"check\" value=\"" . md5(mxsessiongetvar("newusercheck")) . "\" />\n"
 . "</td></tr></table>\n"
 . "</form>";

?>