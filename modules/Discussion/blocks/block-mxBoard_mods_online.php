<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * $Author: PragmaMx $
 * $Revision: 31 $
 * $Date: 2015-07-23 14:22:19 +0200 (jeu. 23 juil. 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

/* --------- Konfiguration fuer den Block ----------------------------------- */

$ModName = basename(dirname(__DIR__)); // here you have to set you Module-Name
$onlinelistsize = 4; // Hoehe des Selectfeld der Onlinuser (Online-Liste)
$showuserlist = true; // Wenn Online-Liste immer erscheinen soll, 1=Ja, 0=nein
$excludedusers = 'Texsterdsgf, Knaxllerfgfd'; // User, die nicht angezeigt werden sollen, Namen mit Komma trennen!
$imagedir = 'images/maaxon';
$pm_module_name = 'Private_Messages';
$mxblockcache = false;

/* --------- Ende der Konfiguration ----------------------------------------- */

/* check ob die verwendete pragmaMx Version korrekt ist */
if (!isset($block['settings'])) {
    return $content = 'Sorry, pragmaMx-Version >= 2.0 is required for this mxBoard-Block.';
}

extract($block['settings'], EXTR_OVERWRITE);

global $prefix, $user_prefix;

if (!defined('MXB_INIT')) define('MXB_INIT', true);

$path = PMX_MODULES_DIR . DS . $ModName . DS;

/* Load the $ModName-settings */
if (!file_exists($path . 'settings.php')) {
    if (MX_IS_ADMIN) {
        $content = 'ERROR: <br />mxBoard-settings not found in ' . basename(__file__);
    }
    return;
}
include($path . 'settings.php');

/* Sprache einbinden */
// mxGetLangfile(PMX_LANGUAGE_DIR . DS . 'whoonline');
if (substr(strtolower($GLOBALS['currentlang']), 0, 6) == "german") {
    // Bilder fuer Buttons definieren (noch keine Bilder vorhanden!!)
    $pmimagepath = "modules/$pm_module_name/images/buttons/german";
} else {
    $pmimagepath = "modules/$pm_module_name/images/buttons/english";
}

$exusers = explode (",", $excludedusers);
if (!MX_IS_ADMIN) {
    while (list($key, $val) = each($exusers)) {
        $xexusers[] = trim($val);
    }
}
$xexusers[] = $GLOBALS['anonymous'];
$excludedusers = "'" . implode("','", $xexusers) . "'";

$result = sql_query("SELECT username, status FROM {$table_members} WHERE ((username NOT IN(" . $excludedusers . "))AND((status='Moderator')OR(status='Super Moderator')OR(status='Administrator')))");
while (list ($name, $status) = sql_fetch_row($result)) {
    $member['name'][] = $name;
    $member['status'][] = $status;
    $member['adminname'][] = '';
}

$result = sql_query("SELECT aid, user_uid FROM " . $prefix . "_authors WHERE (aid NOT IN (" . $excludedusers . "))");
while (list ($aname, $auid) = sql_fetch_row ($result)) {
    $getuname = sql_fetch_row(sql_query("SELECT uname FROM " . $user_prefix . "_users WHERE uid='" . $auid . "'"));
    if (!in_array($getuname[0], $member['name'])) {
        $member['name'][] = $getuname[0];
        $member['status'][] = "Administrator";
        $member['adminname'][] = $aname;
    }
}

/* Ausgabe */
if (MX_IS_USER || MX_IS_ADMIN || $showuserlist) {
    if (MX_IS_USER) {
        $userdata = mxGetUserData();
        $smallunmae = strtolower($userdata['uname']);
    } else {
        $smallunmae = '';
    }

    $member_online_num = (count($member['name']));
    $pmactiv = (mxModuleAllowed($pm_module_name)) ? 1 : 0; # feststellen ob pm-modul aktiv ist
    $i = 0;
    $whoonlineselect = '';
    foreach ($member['name'] as $membername) {
        $online = mxGetUserDataFromUsername($membername);
        if ($online['user_online']) {
            $stat = $member['status'][$i];
            if ($stat == "Administrator") {
                $stat = "Admin";
            }
            if ($stat == "Super Moderator") {
                $stat = "SuperMod";
            }
            if ($stat == "Moderator") {
                $stat = "Mod";
            }
            $sel = (strtolower($membername) == $smallunmae) ? ' selected="selected" ' : '';
            if ((isset($member['adminname'][$i])) && ($member['adminname'][$i] != "")) {
                $uname3 = mxCutString($member['adminname'][$i], 18, "..", ""); # Kurzen Usernamen erstellen
            } else {
                $uname3 = mxCutString($membername, 18, "..", ""); # Kurzen Usernamen erstellen
            }
            $whoonlineselect .= "<option value=\"" . $membername . "\" " . $sel . " title=\"" . $membername . "\">" . $uname3 . " -- " . $stat . "</OPTION>\n"; # options fuer auswahlselect erstellen
        }
        $i++;
    }
    if ($member_online_num > 0) {
        if ($onlinelistsize >= $member_online_num) {
            $onlinelistsize = $member_online_num + 1;
        }
        if (MX_IS_USER && $pmactiv) {
            $script = " ondblclick=\"whob_clickit2()\"";
            $content .= "<script type=\"text/javascript\">\n<!--\n";
            $content .= "function whob_clickit2(){\n";
            $content .= "	var y=document.onlineuserinfosec.uname.selectedIndex; \n";
            $content .= "	var x=document.onlineuserinfosec.uname.options[y].value; \n";
            $content .= "	window.open('modules.php?name=$pm_module_name&file=buddy&op=compose&to='+x, '" . md5(time()) . "','left=370,top=150,width=360,height=200,toolbar=no,location=no,menubar=no,scrollbars=yes,resizable=yes,status=no');\n";
            $content .= "	return false;\n";
            $content .= "	}\n//-->\n";
            $content .= "</script>\n";
        } else {
            $script = '';
        }

        $content .= "<div align=\"center\">";
        $content .= "<form action=\"modules.php?test\" name=\"onlineuserinfosec\" method=\"get\">";
        $content .= "<select name=\"uname\" size=\"$onlinelistsize\" style=\"width: 90%; cursor: pointer;\" $script>" . $whoonlineselect . "</select>";
        if (mxModuleAllowed('Userinfo') || MX_IS_ADMIN) {
            $content .= "<input type=\"hidden\" name=\"name\" value=\"Your_Account\">";
            $content .= "<input type=\"hidden\" name=\"op\" value=\"userinfo\">";
            $content .= "<br /><button title=\"" . _MXB_PMXPROFILE . "\" type=\"submit\">" . _MXB_PMXPROFILE . "</button>";
        }
        $content .= "</form></div>";
    }
}

?>
