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
 * $Revision: 119 $
 * $Author: PragmaMx $
 * $Date: 2016-03-30 15:35:05 +0200 (Mi, 30. Mrz 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

/* --------- Konfiguration fuer den Block ----------------------------------- */
$onlinelistsize = 8; // Hoehe des Selectfeld der Onlineuser (Online-Liste)
$showuserlistall = 0; // Wenn Online-Liste immer erscheinen soll, 1=Ja, 0=nein
$showbuddy = 1; // Buddylink (Messenger) anzeigen , 1=Ja, 0=nein
$showavatar = 0; // Avatar anzeigen, 1=Ja, 0=nein
$avatar_width = 120; // maximale Breite des Avatars in Pixel
$showstatistic = 1; // Mitgliederstatistik, 1=Ja, 0=nein

/* User, die nicht angezeigt werden sollen, Namen mit Komma trennen! */
$excludedusers = 'Texsterdsgf, Knaxllerfgfd';

$imagedir = "images/maaxon";
$pm_module_name = 'Private_Messages';
$ug_module_name = 'UserGuest';
/* --------- Ende der Konfiguration ----------------------------------------- */

extract($block['settings'], EXTR_OVERWRITE);

/* der passende Modulname */
$module_name = basename(dirname(dirname(__FILE__)));

/* Dieser Block sollte nicht gecached werden */
$mxblockcache = false;

/* Sprache einbinden */
if (!defined('_BWOTITLE')) {
    mxGetLangfile($module_name, 'whoonline/lang-*.php');
}

/* Variablen initialisieren */
global $prefix, $user_prefix;
$username = '';
if (MX_IS_USER) {
    $usersession = mxGetUserSession();
    $username = $usersession[1];
    $uid = $usersession[0];
}

/* SQL-bedingung für excluded Users erstellen */
$exusers = explode (',', $excludedusers);
if (!MX_IS_ADMIN) {
    while (list($key, $val) = each($exusers)) {
        $xexusers[] = trim($val);
    }
}
$xexusers[] = $GLOBALS['anonymous'];
$excludedusers = "'" . implode("','", mxAddSlashesForSQL($xexusers)) . "'";

/* Anzahl aller User und hoechste uid ermitteln */
$totalmembers = 0;
$qry = "SELECT COUNT(uid), MAX(uid) FROM {$user_prefix}_users WHERE user_stat=1;";
list($totalmembers, $lastuid) = sql_fetch_row(sql_query($qry));

/* neusten User ermitteln */
$tmp = mxGetUserDataFromUid($lastuid);
if ($tmp) {
    $lastuser = $tmp['uname'];
} else {
    $lastuser = '';
}

$past = time() - MX_SETINACTIVE_MINS ;

/* Alle Gaeste ermitteln */
$guest_online_num = 0;
$result = sql_query("SELECT Count(ip) FROM ${prefix}_visitors WHERE time>" . $past . " AND uid=0;");
list($guest_online_num) = sql_fetch_row($result);

/* Alle Online-User ermitteln, evtl auflisten */
$member_online_num = 0;
$whoonlineselect = "";
$smallunmae = strtolower($username);
$result = sql_query("SELECT uname FROM {$user_prefix}_users
            WHERE ((uname Not In (" . $excludedusers . ")) AND (user_lastvisit >= " . $past . ") AND (user_stat=1) AND (user_lastmod<>'logout'))
            ORDER BY uname");
while (list($uname2) = sql_fetch_row($result)) {
    $sel = (strtolower($uname2) == $smallunmae) ? ' selected="selected" ' : '';
    $uname3 = mxCutString($uname2, 18, "..", ""); # Kurzen Usernamen erstellen
    $whoonlineselect .= "<option value=\"" . $uname2 . "\" " . $sel . " title=\"" . $uname2 . "\">" . $uname3 . "</option>\n"; # options fuer auswahlselect erstellen
    $member_online_num++; # Anzahl User hochzaehlen
}

$avatar = '';
$countpm = 0;
$countpmunread = 0;
$content_ug = "";
if (MX_IS_USER) { // Wenn aktueller User registriert ist (kein Gast)
    /* Private Nachrichten */
    $pmactiv = (mxModuleAllowed($pm_module_name)) ? 1 : 0; # feststellen ob pm-modul aktiv ist
    if ($pmactiv) { // falls pm-modul aktiv ist
        $qry = "SELECT read_msg, Count(msg_id) FROM ${prefix}_priv_msgs WHERE to_userid=" . $uid . " GROUP BY read_msg;";
        $result = sql_query($qry);
        while (list($read_msg, $nums) = sql_fetch_row($result)) {
            if ($read_msg == 0) { // wenn angemeldeter User
                $countpmunread = $nums; # Anzahl ungelesene ermitteln
            } else {
                $countpm = $nums; #++;	# Anzahl aller pm's ermitteln
            }
        }
    }

    /* Usergaestebuch */
    if (mxModuleAllowed($ug_module_name)) { // falls Gaestebuch vorhanden
        $qry = "SELECT Count(gid) FROM ${prefix}_userguest WHERE touserid='" . $uid . "' AND touser = '" . $username . "' AND dummy=0;";
        $result = sql_query($qry);
        list($gbnewentries) = sql_fetch_row($result);
        // Usergaestebuch
        if (!empty($gbnewentries)) {
            $content_ug .= '<div align="left"><a href="modules.php?name=' . $ug_module_name . '&amp;owner=' . urlencode($username) . '">' . mxCreateImage($imagedir . '/tcake.gif', _BWOGUESTBOOK) . '</a> ' . "\n"
             . '<b>' . mxValueToString($gbnewentries, 0) . ' </b>'
             . '<a href="modules.php?name=' . $ug_module_name . '&amp;owner=' . urlencode($username) . '">';
            if ($gbnewentries == 1) {
                $content_ug .= _BWOGUESTBOOK1;
            } else {
                $content_ug .= _BWOGUESTBOOK2;
            }
            $content_ug .= '</a></div>';
        }
    }

    /* Avatar */
    if ($showavatar && intval($avatar_width)) {
        $userinfo = mxGetUserData(); // gesamte Userdaten in Array lesen
        $pici = load_class('Userpic', $userinfo);
        if ('nopic' != $pici->gettype()) {
            $avatar = $pici->getHtml('normal', array('shrink-width' => intval($avatar_width)));
        }
    }
}
// weitere Variablen initialisieren zur Zeitberechnung :(
$gestern = mktime(0, 0, 0, date ("m") , date ("d")-1, date("Y"));
$heute = mktime(0, 0, 0, date ("m") , date ("d"), date("Y"));
// Heute neu angemeldete User ermitteln
$qry = "SELECT COUNT(uid) FROM {$user_prefix}_users WHERE user_regtime >= $heute AND user_stat=1 AND uname NOT IN($excludedusers);";
list($userCount) = sql_fetch_row(sql_query($qry));
// Gestern neu angemeldete User ermitteln
$qry = "SELECT COUNT(uid) FROM {$user_prefix}_users WHERE user_regtime >= $gestern AND user_regtime < $heute AND user_stat=1 AND uname NOT IN($excludedusers);";
list($userCount2) = sql_fetch_row(sql_query($qry));
// Ausgabe
$content = '';
if (MX_IS_USER && $avatar) {
    $content .= '<p class="align-center">' . _HELLO . ' ' . $username . '<br />' . $avatar . '</p>';
}
$content .= "\n<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"border: 0px; margin-bottom: 5px;\">\n"
 . "\n<tr>\n<td>" . _BWOTOTALMEMBERS . "</td>\n<td align=\"right\">\n<b>" . mxValueToString($totalmembers, 0) . "</b></td>\n</tr>\n"
 . "\n<tr>\n<td>" . _BWOTODAYMEMBERS . "</td>\n<td align=\"right\">\n<b>" . mxValueToString($userCount, 0) . "</b></td>\n</tr>\n"
 . "\n<tr>\n<td>" . _BWOYESTERDAYMEMBERS . "</td>\n<td align=\"right\">\n<b>" . mxValueToString($userCount2, 0) . "</b></td>\n</tr>\n"
 . "\n<tr>\n<td>" . _BWOMEMBERS1 . "</td>\n<td align=\"right\">\n<b>" . mxValueToString($member_online_num, 0) . "</b></td>\n</tr>\n"
 . "\n<tr>\n<td>" . _BWOGUESTES . "</td>\n<td align=\"right\">\n<b>" . mxValueToString($guest_online_num, 0) . "</b></td>\n</tr>\n"
 . "</table>\n<div class=\"align-center\">\n";
if (MX_IS_USER) {
    // zuletzt angemeldeter User
    $content .= '<div style="border: 0px; margin-bottom: 5px;">' . _BWOLATEMEMBER . '<br />' . mxCreateUserprofileLink($lastuser) . '</div>';
}
if (MX_IS_USER || MX_IS_ADMIN || $showuserlistall) { // Wenn angemeldeter User oder Admin oder Liste immer erscheinen soll
    // Onlineliste anzeigen wenn User online sind
    if ($member_online_num > 0) {
        if ($onlinelistsize >= $member_online_num) {
            $onlinelistsize = $member_online_num + 1;
        }

        $script_id = 'ouinfo' . rand(0, 1000);

        if (!MX_IS_USER || !$pmactiv) {
            $script = "";
        } else {
            $script = ' ondblclick="func_' . $script_id . '()"';

            ?>
<script type="text/javascript">
/* <![CDATA[ */
function func_<?php echo $script_id ?>(){
  var y=document.<?php echo $script_id ?>.uname.selectedIndex;
  var x=document.<?php echo $script_id ?>.uname.options[y].value;
  window.open('modules.php?name=<?php echo $pm_module_name ?>&file=buddy&op=compose&to='+x, '<?php echo md5(time()) ?>','left=370,top=150,width=360,height=200,toolbar=no,location=no,menubar=no,scrollbars=yes,resizable=yes,status=no');
  return false;
}
/* ]]> */
</script>
<?php
        }
        $content .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"border: 0px; margin-bottom: 5px;\">\n";
        $content .= "\n<tr>\n<td colspan=\"2\" align=\"center\">\n";
        $content .= '<b>' . _BWOONLINELIST . '</b><br />';
        $content .= "</td>\n</tr>\n";
        $content .= "\n<tr>\n<td colspan=\"2\" align=\"center\">"; # select-options verwenden
        $content .= "<form action=\"modules.php?test\" id=\"" . $script_id . "\" name=\"" . $script_id . "\" method=\"get\" style=\"border: 0px; margin-bottom: 5px; margin-top: 0px;\">";
        $content .= "<input type=\"hidden\" name=\"name\" value=\"Userinfo\" />";
        $content .= "<select name=\"uname\" size=\"$onlinelistsize\" style=\"font-size:.9em;width:90%;\" $script>" . $whoonlineselect . "</select>";
        if (mxModuleAllowed('Userinfo') || MX_IS_ADMIN) {
            $content .= "<br />\n<button title=\"" . _USERINFO . "\" type=\"submit\">" . _BWOUSERINFO . "</button>";
        }
        $content .= "</form>\n";
        $content .= "</td>\n</tr>\n";
        $content .= "</table>\n";
    }
    // private messages
    if (MX_IS_USER && $pmactiv) {
        // pm Liste
        $content .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"border: 0px; margin-bottom: 5px;\">\n";
        if ($countpm) {
            $content .= "\n<tr>\n<td colspan=\"3\" align=\"center\"><a href=\"modules.php?name=" . $pm_module_name . "\">" . _BWOPMSG . "</a></td>\n</tr>\n";
            $content .= "\n" . '<tr>' . "\n" . '<td align="center"><a href="modules.php?name=' . $pm_module_name . '">' . mxCreateImage($imagedir . '/pmessage.gif', _BWOPMSG) . '</a></td>' . "\n";
            $content .= "<td valign=\"top\">" . _BWOPMSGALL . ":<br />\n" . _BWOPMSGUNREAD . ":</td>\n";
            $content .= "<td valign=\"top\" align=\"right\"><b>" . mxValueToString($countpm, 0) . "</b><br />\n";
            $content .= ($countpmunread) ? mxCreateImage($imagedir . '/arrow-ani.gif') . '&nbsp;<b>' . mxValueToString($countpmunread, 0) . '</b>' : '<b>' . mxValueToString($countpmunread, 0) . '</b>';
            $content .= "</td>\n</tr>\n";
        } else {
            $content .= '<tr><td><a href="modules.php?name=' . $pm_module_name . '">' . mxCreateImage($imagedir . '/pmessage.gif', _BWOPMSG, 0, 'hspace="0"') . '</a></td><td><a href="modules.php?name=' . $pm_module_name . '">' . _BWOPMSG . '</a></td></tr>' . "\n";
        }
        $content .= "</table>\n";
        // Buddy
        if ($showbuddy) {
            $content .= "<button onclick=\"window.open('modules.php?name=" . $pm_module_name . "&amp;file=buddy','" . md5($GLOBALS['sitename']) . "','left=180, top=150, width=540,height=450,toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0,resizable=1,copyhistory=0'); return false;\" title=\"" . _BWODISCRIBE . "\">" . _BWOBUDDIE . "</button><br />\n";
        }
    }
    // Usergaestebuch
    $content .= $content_ug;
}

if (!MX_IS_USER) { // Gast no user
    $content .= '<p class="align-left"><br />' . _BWOASREGISTERED . '</p>';
}

$content .= "</div>\n";
// Blocktitel aus Sprachdatei auslesen
$blockfiletitle = _BWOMAAXONLINE;

?>