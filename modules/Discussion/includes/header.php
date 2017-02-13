<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: PragmaMx $
 * $Revision: 171 $
 * $Date: 2016-06-29 13:59:03 +0200 (mer. 29 juin 2016) $
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

/* versch. Grundeinstellungen, werden durch die custom.ini überschrieben.. */
$index = 1;
$imageset_default = 'phpbb';
$maxposticons = 16;
$max_w = 512;
$max_h = 384;
$max_ppp = 50;
$max_searchreults = 70;
$showeditedby = 1;
$mxbshowonlineentiresite = true;
extract(parse_ini_file(dirname(__FILE__) . DS . 'custom.ini', true), EXTR_OVERWRITE);
// Scriptlaufzeitanzeige initialisieren
$mxbStartTime = microtime(true);
// Anzahl der bereits abgearbeiteten Queries zwischenspeichern.
$mxbQueryDiff = $mxQueryCount;
$time = time();

define('MXB_INIT', true);
$MXB_INIT = true;

ob_start();
// der HTML-Seitencache (JP-Cache) macht im mxBoard Probleme und
// sollte deaktiviert sein
$JPCACHE_ON = false;

$ModName = basename(dirname(dirname(__FILE__)));
include_once ("modules/" . $ModName . "/includes/initvar.php");
include_once ("modules/" . $ModName . "/includes/functions.php");
include_once ("modules/" . $ModName . "/includes/functions2.php");
include_once (MXB_SETTINGSFILE);

sql_query("SET sql_mode = 'MYSQL40'");
// versch. Grundeinstellungen checken u. korrigieren
if (empty($postperpage) || $postperpage < 1) {
    $postperpage = 25;
}
if (empty($topicperpage) || $topicperpage < 1) {
    $topicperpage = 25;
}
if (!isset($linktype)) {
    $linktype = '';
}
// if (!isset($multipage)) {
// $multipage = '';
// }
$tid = (isset($tid)) ? intval($tid) : 0;
$fid = (isset($fid)) ? intval($fid) : 0;
$pid = (isset($pid)) ? intval($pid) : 0;
$lid = (isset($lid)) ? intval($lid) : 0;
$jumplink = (empty($jumplink)) ? 0 : intval($jumplink);
// Foren-ID ermitteln für tracking
if (!empty($tid) && empty($fid)) {
    $res = sql_query("SELECT fid, subject FROM $table_threads WHERE tid=" . intval($tid));
    $locate2 = sql_fetch_object($res);
    $fid = (empty($locate2->fid)) ? 0 : intval($locate2->fid);
    // $locate2->subject  wird weiter unten nochmal verwendet!!
}

setcookie("lastvisita", $time, $time + (86400 * 365));
if (isset($_COOKIE['lastvisitb'])) {
    $thetime = $_COOKIE['lastvisitb'];
} elseif (isset($_COOKIE['lastvisita'])) {
    $thetime = $_COOKIE['lastvisita'];
} else {
    $thetime = $time - (86400 * 365);
}
setcookie("lastvisitb", $thetime, $time + 600);
$lastvisit = $thetime;

$status = '';
$validadmin = false;
$superuser = false;
$mxb_user_data = array();
$userdata = array();
$synchro = '';
$thisuser = '';

if (MX_IS_USER) {
    $userdata = mxGetUserData();
}

if (MX_IS_ADMIN) {
    $admindata = mxGetAdminData();
    if (!empty($admindata['user_uid']) && (empty($userdata['uid']) || $admindata['user_uid'] != $userdata['uid'])) {
        $userdata = mxGetUserDataFromUid($admindata['user_uid']);
    }
    $superuser = mxGetAdminPref('radminsuper');
    $validadmin = MX_IS_USER && (mxGetAdminPref('radminforum') || $superuser);
}
// wenn Admin oder User
if ($validadmin || !empty($userdata)) {
    if ($validadmin) {
        $new_status = 'Administrator';
    } else {
        $new_status = 'Member';
    }
    $userdata['user_sig'] = (empty($userdata['user_sig'])) ? '' : $userdata['user_sig'];
    $userdata['uid'] = (empty($userdata['uid'])) ? '' : $userdata['uid'];
    $userdata['user_regtime'] = (empty($userdata['user_regtime'])) ? $time : $userdata['user_regtime'];

    $thisuser = substr($userdata['uname'], 0, 25);
    $lastvisitdate = $time;
    $lastvisitstore = $time;
    $userquery = "SELECT username, timeoffset, status, theme, tpp, ppp, timeformat, dateformat, langfile, lastvisit, lastvisitstore, lastvisitdate, trackingfid, trackingtime, keeplastvisit
                  FROM $table_members
                  WHERE username='" . mxAddSlashesForSQL($thisuser) . "'";
    $res = sql_query($userquery);
    // falls Datenbankprobleme, wegen falschem präfix, ist hier die erste Gelegenheit, als Admin...
    if (!$res) {
        return mxbExitMessage('<h1>' . $bbname . '</h1><hr/><p>Sorry we have some database problems.</p>' . (($validadmin) ? '<br /><br />' . sql_error() : ''));
    }
    $mxb_user_data = sql_fetch_assoc($res);

    if (is_array($mxb_user_data)) {
        // wenn Datensatz bereits vorhanden >> aktualisieren, mit tracking
        // Tracking aktualisieren
        if ($mxb_user_data['lastvisitstore'] < ($time - (3600 * $mxb_user_data['keeplastvisit']))) {
            // für den Fall das wir in das Forum kommen und "date" und "store" ungleich sind
            // wollen wir natürlich nicht, das das "date" auf time gesetzt wird. Daher Bed. if
            if ($mxb_user_data['lastvisit'] < ($time - 900)) {
                $lastvisitdate = $mxb_user_data['lastvisit'];
                // wenn wir im Forum rumkrauchen sollen die Umschläge ja auch
                // irgendwann verschwinden, daher also diese Bedingung
            }
        } else {
            $lastvisitdate = $mxb_user_data['lastvisitdate'];
            $lastvisitstore = $mxb_user_data['lastvisitstore'];
        }
        extract(mxbUserTracking($mxb_user_data['trackingfid'], $mxb_user_data['trackingtime'], $fid), EXTR_OVERWRITE);
        // aktuell gültigen Status ermitteln
        $mxb_user_data['status'] = mxbGetRepairedStatus ($mxb_user_data);
        switch (true) {
            case !$validadmin && $mxb_user_data['status'] == 'Administrator':
                // wenn eigentlich Administrator, aber nicht als Administrator eingeloggt
                $synchro = ' - administrator synchro <a href="' . adminUrl('main') . '" title="login?"><strong>failed</strong></a>';
                $status = 'Super Moderator';
                $sync_additions = '';
                break;
            case $validadmin && $mxb_user_data['status'] != 'Administrator':
                $status = 'Administrator';
                $sync_additions = "`status` = '" . $status . "', ";
                break;
            default:
                $status = $mxb_user_data['status'];
                $sync_additions = '';
        }
        // User-Daten aktualisieren
        // das passiert immer, weil gleichzeitig auch das tracking eingetragen wird ;-))
        sql_query("UPDATE $table_members SET
                          " . $sync_additions . "
                          `lastvisit` = '$time',
                          `lastvisitstore` = '$lastvisitstore',
                          `lastvisitdate` = '$lastvisitdate',
                          `trackingfid` = '$trackingfid',
                          `trackingtime` = '$trackingtime',
                          `totaltime`=totaltime+'$timeinforum'
                          WHERE username='" . mxAddSlashesForSQL($thisuser) . "'");
    } else {
        // wenn Datensatz noch nicht vorhanden >> einfügen
        if (mxb_insert_user($thisuser, $new_status)) {
            // und die neuen Userdaten auslesen
            $res = sql_query($userquery);
            $mxb_user_data = sql_fetch_assoc($res);
            $status = $mxb_user_data['status'];
        }
    }
}
$mxb_user_data['user_sig'] = (empty($userdata['user_sig'])) ? '' : $userdata['user_sig'];

unset($userdata, $admindata, $userquery, $sync_additions);

/* Spracheinstellung */
if (empty($mxb_user_data['langfile'])) {
    $langs = array($GLOBALS['currentlang'], 'german');
} else {
    $langs = array($mxb_user_data['langfile'], $GLOBALS['currentlang'], 'german');
}
$found = false;
$langs = array_unique(array_merge($langs, array_values(mxbGetAvailableLanguages('lang'))));

foreach ($langs as $value) {
    if ($value) {
        $file = MXB_ROOTMOD . 'language' . DS . 'lang-' . $value . '.php';
        if (file_exists($file)) {
            include_once($file);
            $langfile = $value;
            $found = true;
            break;
        }
    }
}

if (file_exists(MXB_ROOTMOD . 'language' . DS . 'lang-' . $currentlang . '.php')) {
    // fuer Alle, auch Anonyme Spracheinstellung ueber CMS-Einstellung
    $langfile = $currentlang;
}
if (!empty($mxb_user_data['langfile']) && $langfile != $mxb_user_data['langfile']) {
    // Wenn User im Forum eine andere Sprache gewählt hat und diese vorhanden ist..
    if (file_exists(MXB_ROOTMOD . 'language' . DS . 'lang-' . $langfile . '.php')) {
        $langfile = $mxb_user_data['langfile'];
    }
}

$mxbnavigator = new mxb_navigation();

if ($status && isset($mxb_user_data) && is_array($mxb_user_data)) {
    if ($globaltimestatus != 'on') {
        $timeoffset = $mxb_user_data['timeoffset'];
    } else {
        $timeoffset = $globaltimeoffset;
    }
    $XFthemeuser = $mxb_user_data['theme'];
    $tpp = ($mxb_user_data['tpp'] > $max_ppp || $mxb_user_data['tpp'] < 0) ? $topicperpage : $mxb_user_data['tpp'];
    $ppp = ($mxb_user_data['ppp'] > $max_ppp || $mxb_user_data['ppp'] < 0) ? $postperpage : $mxb_user_data['ppp'];
    $memtime = $mxb_user_data['timeformat'];
    $memdate = $mxb_user_data['dateformat'];
    $notify = _LOGGEDIN . " <strong>$thisuser</strong> " . $synchro;
} else {
    $thisuser = MXB_ANONYMOUS;
    $timeoffset = $globaltimeoffset;
    $XFthemeuser = '';
    $tpp = $topicperpage;
    $ppp = $postperpage;
    $memtime = $timeformat;
    $memdate = $dateformat;
    $lastvisitdate = $lastvisit;
    $notify = _NOTLOGGEDIN;
}
// Zeit und Datumsformate
if (empty($memtime)) {
    if ($timeformat == "24") {
        $timecode = "H:i";
    } else {
        $timecode = "h:i A";
    }
} else {
    if ($memtime == "24") {
        $timecode = "H:i";
    } else {
        $timecode = "h:i A";
    }
}
$dformatorigconf = $dateformat;
if (empty($memdate)) {
    $dateformat = $dateformat;
} else {
    $dateformat = $memdate;
}
$dformatorig = $dateformat;
$dateformat = preg_replace("/m{2,}/i", "n", $dateformat);
$dateformat = preg_replace("/d{2,}/i", "j", $dateformat);
$dateformat = preg_replace("/y{4,}/i", "Y", $dateformat);
$dateformat = preg_replace("/y{2,3}/i", "y", $dateformat);

$lastvisittext = '';
if (!empty($_COOKIE['lastvisita'])) {
    $lastdate = gmdate($dateformat, (int)$_COOKIE['lastvisita'] + ($timeoffset * 3600));
    $lasttime = gmdate($timecode, (int)$_COOKIE['lastvisita'] + ($timeoffset * 3600));
    $lastvisittext = '<p class="right">	' . _LASTACTIVE . '&nbsp;' . $lastdate . '&nbsp;' . _TEXTAT . '&nbsp;' . $lasttime . '</p>';
}
// who-Online aktualisieren
$onlineip = MX_REMOTE_ADDR;

if (!preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $onlineip)) {
    $onlineip = '0.0.0.0';
}
// Board ist z.Zt. deaktiviert
if ($bbstatus != 'on' && !$validadmin) {
    return mxbExitMessage('<p>' . $bbname . '</p><hr/><br/><br/>' . _TEXTBBOFFNOTE . '<br/><br/>' . stripslashes($bboffreason));
}
// user oder ip ist gebannt, nicht bei Admins
if (!MX_IS_ADMIN) {
    $ips = explode(".", $onlineip);
    $query = sql_query("SELECT id FROM $table_banned WHERE (ip1='$ips[0]' OR ip1='-1') AND (ip2='$ips[1]' OR ip2='-1') AND (ip3='$ips[2]' OR ip3='-1') AND (ip4='$ips[3]' OR ip4='-1')");
    // falls Datenbankprobleme, wegen falschem präfix, ist hier die erste Gelegenheit, für Anonyme...
    if (!$query) {
        return mxbExitMessage('<p>' . $bbname . '</p><hr/><br/><br/>' . 'Sorry we have some database problems.');
    }
    $result = sql_fetch_object($query);
    if ($status == 'Banned' || ($result && (!$status || $status == 'Member'))) {
        return mxbExitMessage('<p>' . $bbname . '</p><hr/><br/><br/>' . _BANNEDMESSAGE);
    }
    unset($ips);
}
// board nicht für Gäste zu sehen
if ($regviewonly == 'on' && !$status) {
    return mxbExitMessage('<p>' . $bbname . '</p><hr/><br/><br/>' . _REGGEDONLY . '<br/><br/><a href="' . MXB_URLREGISTER . '">' . _TEXTREGISTER . '</a>');
}
// who-Online aktualisieren
$locate = '';
if (!empty($fid)) {
    $query = sql_query("SELECT name, private, theme, userlist FROM $table_forums WHERE fid=" . intval($fid));
    $locate = sql_fetch_object($query);
}

$file = (isset($_REQUEST['file'])) ? $_REQUEST['file'] : 'index';
$action = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : $file;

if ($whosonlinestatus == 'on') {
    if (is_object($locate) && !empty($fid) && empty($tid) && $locate->private != "staff" && $locate->userlist == "") {
        $location = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=$fid\">" . $locate->name . "</a>";
    } elseif (is_object($locate) && !empty($fid) && !empty($tid) && $locate->private != "staff" && $locate->userlist == "" && !empty($locate2->subject)) {
        $location = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=$fid\">" . $locate->name . "</a>: <a href=\"" . MXB_BM_VIEWTHREAD1 . "tid=$tid\">" . strip_tags($locate2->subject) . "</a>";
    } elseif (is_object($locate) && ($locate->private == "staff" || $locate->userlist)) {
        $location = _TEXTPRIV;
    } elseif ($action == "memberslist" && $memliststatus == 'on' && ($status || $memlistanonymousstatus == 'on')) {
        $location = "<a href=\"" . MXB_BM_MEMBERSLIST0 . "\">" . _TEXTMEMBERLIST . "</a>";
    } elseif ($action == "search" && $searchstatus == 'on') {
        $location = "<a href=\"" . MXB_BM_MISC1 . "action=search\">" . _TEXTSEARCH . "</a>";
    } elseif ($action == "faq" && $faqstatus == 'on') {
        $location = "<a href=\"" . MXB_BM_MISC1 . "action=faq\">" . _TEXTFAQ . "</a>";
    } elseif ($action == "online" && $whosonlinestatus == 'on') {
        $location = "<a href=\"" . MXB_BM_MISC1 . "action=online\">" . _WHOSONLINE . "</a>";
    } elseif ($action == "messslv" && $status) {
        $location = "<a href=\"" . MXB_BM_MESSLV0 . "\">" . _TEXTMESSSLV . "</a>";
    } elseif ($action == "messotd") {
        $location = "<a href=\"" . MXB_BM_MESSOTD0 . "\">" . _TEXTMESSOTD . "</a>";
    } elseif ($action == "stats" && $statspage == 'on') {
        $location = "<a href=\"" . MXB_BM_STATS0 . "\">" . _TEXTSTATS . "</a>";
    } elseif ($action == "editpro" || $action == "viewpro") {
        $location = "<a href=\"" . MXB_BM_MEMBER1 . "action=viewpro\">" . _TEXTPROFILE . "</a>";
    } else {
        $location = "<a href=\"" . MXB_BM_INDEX0 . "\">" . _TEXTINDEX . "</a>";
    }

    $delete_add = '';
    if ($status) {
        $query = sql_query("SELECT username, ip, `time` FROM $table_whosonline WHERE username='" . mxAddSlashesForSQL($thisuser) . "'");
        if ($row = sql_fetch_object($query)) {
            // angemeldeter User gefunden
            sql_query("UPDATE $table_whosonline SET ip='" . $onlineip . "', `time`=" . $time . ", location='" . mxAddSlashesForSQL($location) . "' WHERE username='" . mxAddSlashesForSQL($thisuser) . "'");
        } else {
            // angemeldeter User NICHT gefunden
            sql_query("INSERT INTO $table_whosonline VALUES('" . mxAddSlashesForSQL($thisuser) . "', '" . $onlineip . "', '" . $time . "', '" . mxAddSlashesForSQL($location) . "')");
        }
        $delete_add = " AND username <> '" . mxAddSlashesForSQL($thisuser) . "'";
    } else {
        $query = sql_query("SELECT username, ip, `time` FROM $table_whosonline WHERE username='xguest123' AND ip='" . $onlineip . "'");
        if ($row = sql_fetch_object($query)) {
            // Gast und IP vorhanden
            sql_query("UPDATE $table_whosonline SET `time`=" . $time . ", location='" . mxAddSlashesForSQL($location) . "' WHERE username='xguest123' AND ip='" . $onlineip . "'");
        } else {
            // Gast und IP NICHT vorhanden
            sql_query("INSERT INTO $table_whosonline VALUES('xguest123', '" . $onlineip . "', '" . $time . "', '" . mxAddSlashesForSQL($location) . "')");
        }
        $delete_add = " AND username <> 'xguest123' AND ip <> '" . $onlineip . "'";
    }

    $past = intval($time - (MX_SETINACTIVE_MINS / 2));
    if (!mxSessionGetVar('mxbPast') || mxSessionGetVar('mxbPast') < $past) {
        sql_query("DELETE FROM $table_whosonline WHERE time<'$past'" . $delete_add);
        mxSessionSetVar('mxbPast', time());
    }
}
// ------------------------------------------------------------------------------
// Theme Stuff
// ------------------------------------------------------------------------------
$mxboard_copyright = 'mxBoard, &copy; 2011-2017 by <a href="http://www.pragmamx.org/" target="_blank">pragmaMx.org</a>';
$page_down = '<a href="#bas">' . mxbGetImage('page_down.gif', _MXBPAGEDOWN, false) . '</a>';
$page_up = '<a href="#haut">' . mxbGetImage('page_up.gif', _MXBPAGEUP, false) . '</a>';

$XFthemedef = $XFtheme;
if (isset($_GET['settheme']) && $validadmin) {
    sql_query("UPDATE $table_members SET `theme` = '" . mxAddSlashesForSQL($_GET['settheme']) . "' WHERE username='" . mxAddSlashesForSQL($thisuser) . "'");
    $XFtheme = $_GET['settheme'];
} else if (is_object($locate) && ($locate->theme && $XFthemeuser == $XFtheme)) {
    $XFtheme = $locate->theme;
} elseif (!empty($XFthemeuser)) {
    $XFtheme = $XFthemeuser;
}

$imageset = '';
$query = sql_query("SELECT * FROM $table_themes WHERE name='" . substr($XFtheme, 0, 30) . "'");
if ($themevars = sql_fetch_assoc($query)) {
    $themevars['bgcolheader'] = $themevars['header'];
    $themevars['bgcolheadertext'] = $themevars['headertext'];
    $themevars['imageset'] = $themevars['replyimg'];
    unset($themevars['name'], $themevars['header'], $themevars['headertext'], $themevars['replyimg']);
    extract(mxbThemeFallback($themevars), EXTR_OVERWRITE);
    if (file_exists(MXB_BASEMODTEMPLATE . DS . $XFtheme . DS . 'theme.php')) {
        define('MXBOARD_HTML_THEME', $XFtheme);
    }
} else {
    // wenn das theme nicht in der db existiert
    // ggf. bestehende Userdaten ändern
    sql_query("UPDATE $table_members SET theme='' WHERE theme='" . mxAddSlashesForSQL($XFtheme) . "'");
    sql_query("UPDATE $table_forums SET theme='' WHERE theme='" . mxAddSlashesForSQL($XFtheme) . "'");
    // fallback anwenden
    extract(mxbThemeFallback(), EXTR_OVERWRITE);
    // und Standardtheme verwenden
    $XFtheme = 'default';
}
// wenn default eingestellt, mit der defaulttheme.php die Farbwerte überschreiben
if ($XFtheme == "default") {
    $themevars = includetheme();
    foreach ($themevars as $key => $value) {
        global $$key;
        $$key = $value;
    }
    include_once(MXB_BASEMODINCLUDE . 'defaulttheme.php');
}
unset($themevars);
// falls kein gültiges Imageset eingestellt, das Standardset verwenden
if (!empty($imageset) && (!file_exists(MXB_ROOTMOD . 'imagesets/' . $imageset) || is_file(MXB_ROOTMOD . 'imagesets/' . $imageset))) {
    $imageset = $imageset_default;
}
// Nur Farbverlauf laden, wenn File existiert
if (file_exists(MXB_ROOTMOD . 'imagesets/' . $imageset . '/bkg.png')) {
    $mxbcoloredback = ' style="background-image: url(' . MXB_ROOTMOD . 'imagesets/' . $imageset . '/bkg.png' . '); background-repeat: repeat;"';
} else {
    $mxbcoloredback = '';
}

$font0 = strval(intval($fontsize)) . 'px';
$font1 = strval(intval($altfontsize)) . 'px';
$font2 = strval(intval($fontsize) + 1) . 'px';
$font3 = strval(intval($fontsize) + 3) . 'px';
$font4 = strval(intval($fontsize) + 5) . 'px';

$rulesbutton = '';
if ($bbrules == 'on') {
    $rulesbutton = "<p class=\"align-center\"><a href=\"" . MXB_BASEMOD . "bbrules\" onclick=\"UnbPopup('" . MXB_BASEMOD . "bbrules&amp;theme=" . MX_THEME . "', 'bbrules', 500, 400); return false;\" class=\"button\">" . _TEXTBBRULESWINDOW . "</a></p>";
}

$cplink = '';
if ($validadmin) {
    $cplink = "<a href=\"" . MXB_BM_CP0 . "\">" . _TEXTCP . "</a>";
}

$proreg = '';
$messslvlink = '';
if ($status) {
    $proreg = "<a href=\"" . MXB_BM_MEMBER1 . "action=editpro\">" . _TEXTPROFILE . "</a>";
    $messslvlink = "<a href=\"" . MXB_BM_MESSLV0 . "\">" . _TEXTMESSSLV . "</a>";
}

$searchlink = '';
if ($searchstatus == 'on') {
    $searchlink = "<a href=\"" . MXB_BM_SEARCH0 . "\">" . _TEXTSEARCH . "</a>";
}

$advancedonlinelink = '';
if ($whosonlinestatus == 'on') {
    $advancedonlinelink = "<a href=\"" . MXB_BM_MISC1 . "action=online\">" . _WHOSONLINE . "</a>";
}

$messotdlink = '';
if ($messotdstatus == 'on') {
    $messotdlink = "<a href=\"" . MXB_BM_MESSOTD0 . "\">" . _TEXTMESSOTD . "</a>";
}

$faqlink = '';
if ($faqstatus == 'on') {
    $faqlink = "<a href=\"" . MXB_BM_MISC1 . "action=faq\">" . _TEXTFAQ . "</a>";
}

$memlistlink = '';
if ($memliststatus == 'on' && ($status || $memlistanonymousstatus == 'on')) {
    $memlistlink = '<a href="' . MXB_BM_MEMBERSLIST0 . '">' . _TEXTMEMBERLIST . '</a>';
}

$statslink = '';
if ($statspage == 'on') {
    $statslink = "<a href=\"" . MXB_BM_STATS0 . "\">" . _TEXTSTATS . "</a>";
}
// komplette Navigationsleiste, Leerzeichen und | drumrum entfernen
$completenavbar = '<span class="navtd">' . preg_replace('#(\s*\|\s*){2,}#', ' | ', trim("$proreg | $memlistlink | $advancedonlinelink | $searchlink | $messslvlink | $messotdlink | $cplink | $faqlink | $statslink", '| ')) . '</span>';

$eBoardUser = $mxb_user_data;
$eBoardUser['username'] = (empty($thisuser)) ? '' : $thisuser;
$eBoardUser['status'] = (empty($status)) ? '' : $status;
$eBoardUser['isadmin'] = (empty($validadmin)) ? 0 : $validadmin;
$eBoardUser['superuser'] = (empty($superuser)) ? 0 : $superuser;

unset($isEboardAdmin, $thisuser, $validadmin, $superuser, $mxb_user_data);

?>