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
 *
 *
 */

defined('mxMainFileLoaded') or die('access denied');

if (!mxGetAdminPref('radminsuper')) {
    mxErrorScreen("Access Denied");
    die();
}

/* Sprachdatei auswählen */
mxGetLangfile(dirname(__FILE__));

$adminrunning = 1;
global $optr, $sort, $datef, $datet, $timef, $timet, $text, $url, $ip, $truid, $trid;
// Menu Definition / you can change it here
// change text and/or date from (datef), date to (datet, time from (timef),
// time to (timet)
if ($sort == "") {
    $sort = "visit"; # Default by: visit / page / time
}

$menu = array(1 => array("text" => _TRACK_LAST . " 1 " . _TRACK_HOURS,
        "timef" => date("Y-m-d H:i:s", time()-3600 * 1), // now - 1 hours
        "timet" => ''
        ),
    2 => array("text" => _TRACK_LAST . " 4 " . _TRACK_HOURS,
        "timef" => date("Y-m-d H:i:s", time()-3600 * 4), // now - 4 hours
        "timet" => ''
        ),
    3 => array("text" => _TRACK_TODAY,
        "timef" => date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d"), date("Y"))), // now
        "timet" => date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d"), date("Y"))) // today
        ),
    4 => array("text" => _TRACK_YESTERDAY,
        "timef" => date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d")-1, date("Y"))), // today - 1 day
        "timet" => date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d")-1, date("Y"))) // today - 1 day
        ),
    5 => array("text" => _TRACK_LAST . " 3",
        "timef" => date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d")-3, date("Y"))), // today - 3 days
        "timet" => date("Y-m-d H:i:s", time()) // now
        ),
    6 => array("text" => _TRACK_LAST . " 7",
        "timef" => date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d")-7, date("Y"))), // today - 7 days
        "timet" => date("Y-m-d H:i:s", time()) // now
        ),
    7 => array("text" => _TRACK_LAST . " 14",
        "timef" => date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d")-14, date("Y"))), // today - 14 days
        "timet" => date("Y-m-d H:i:s", time()) // now
        ),
    8 => array("text" => _TRACK_LAST . " 30",
        "timef" => date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d")-30, date("Y"))), // today - 30 days
        "timet" => date("Y-m-d H:i:s", time()) // now
        ),
    9 => array("text" => _TRACK_ALL,
        "timef" => "1990-01-01 00:00:01",
        "timet" => date("Y-m-d H:i:s", time()) // now
        )
    ); # End of Menu Definition

// only default if first start without parameters
// Default Menu: 1 / 2 / 3 / ...
// $default_timef = $menu[1]["timef"];
// $default_timet = $menu[1]["timet"];
// $default_text  = $menu[1]["text"];
$menu_optr = "start"; # Default mode for period menu

function track_fc_format_date($f1_date, $f1_datestring)
{
    return ucfirst(strftime($f1_datestring, mxSqlDate2UnixTime($f1_date)));
}
// ************************************************************************
// Line break for words longer then $f1_lenght. It will ab a space after
// $f1_lenghtletters. So in a table can be an automatic line break
// for long words.
// ************************************************************************
function track_umbruch($f1_string, $f1_lenght)
{
    $hf_neu = "";
    $hf_words = explode (" ", $f1_string);

    while (list($key, $val) = each($hf_words)) {
        $hf_neu[] = chunk_split ($val, $f1_lenght, " ");
    }

    $f1_string = trim(implode(" ", $hf_neu));

    return $f1_string;
}
// ************************************************************************
// print an array
// ************************************************************************
function track_print_array_table ($f1_array, $f1_double, $hf_printkey)
{
    $hf_count = 0;
    while (list($key1, $subarray) = each($f1_array)) {
        $hf_count++;

        if (($hf_count != 2) and ($f1_double == '2')) {
            continue;
        }
        $hf_count = 0;
        echo "<tr>";
        if ($hf_printkey) {
            echo "<td>$key1:</td>";
        }
        if (!is_array($subarray)) {
            echo "<td>" . $subarray . "</td>";
        } else {
            echo "<ul>";
            while (list($key2, $val) = each($subarray)) {
                echo "<li>$key2 : $val\n";
            }
            echo "</ul>";
        }
        echo "</tr>";
    }
}
// ******************************************************************************
// Display group of visits for selected range
// ******************************************************************************
function start ($sort, $timef, $timet, $text)
{
    global $user_prefix, $prefix, $menu;

    global $hf_text1, $hf_textend, $f1_text, $uid, $tracktime, $ip;

    if ($timef == '') {
        $timef = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
    }
    if ($timet == '') {
        $timet = date("Y-m-d H:i:s", time());
    }

    $result = sql_query("SELECT count(*) FROM ${prefix}_tracking WHERE tracktime BETWEEN '$timef' and '$timet' ");
    list($hf_pageviews) = sql_fetch_row($result);

    switch ($sort) {
        case "visit":
            $result = sql_query("SELECT substring(tracktime,1,10) as hf_date, ip, uid, count(*) as hf_count FROM ${prefix}_tracking
                           WHERE tracktime BETWEEN '$timef' and '$timet'
                           GROUP BY hf_date DESC, ip, uid ");
            $hf_headcount = sql_num_rows($result);
            $hf_sorttext = _TRACK_VISITS;
            $hf_textend = " " . _TRACK_AND . " " . $hf_pageviews . " " . _TRACK_PAGES;
            break;

        case "page":
            $result = sql_query("SELECT uid, requrl, count(*) as hf_count FROM ${prefix}_tracking
                           WHERE tracktime BETWEEN '$timef' and '$timet'
                           GROUP BY requrl DESC
                           ORDER BY hf_count DESC ");
            $hf_headcount = sql_num_rows($result);
            $hf_sorttext = _TRACK_DIFFPAGES;
            $hf_textend = " " . _TRACK_AND . " " . $hf_pageviews . " " . _TRACK_PAGES;
            break;

        case "time":
            $result = sql_query("SELECT tracktime, ip, uid, requrl FROM ${prefix}_tracking
                           WHERE tracktime BETWEEN '$timef' and '$timet'
                           ORDER BY tracktime DESC ");
            $hf_headcount = sql_num_rows($result);
            $hf_sorttext = _TRACK_PAGES;
            break;

        case "date":
            $result = sql_query("SELECT substring(tracktime,1,10) as hf_date, count(*) as hf_count FROM ${prefix}_tracking
                           WHERE tracktime BETWEEN '$timef' and '$timet'
                           GROUP BY hf_date DESC ");
            $hf_headcount = sql_num_rows($result);
            $hf_sorttext = _TRACK_DAYS;
            $hf_textend = " " . _TRACK_AND . " " . $hf_pageviews . " " . _TRACK_PAGES;
            break;

        case "user":
            $result = sql_query("SELECT uid, count(*) as hf_count FROM ${prefix}_tracking
                           WHERE tracktime BETWEEN '$timef' and '$timet'
                           GROUP BY uid
                           ORDER BY hf_count DESC
                           ");
            $hf_headcount = sql_num_rows($result);
            $hf_sorttext = _TRACK_USERS;
            $hf_textend = " " . _TRACK_AND . " " . $hf_pageviews . " " . _TRACK_PAGES;
            break;

        case "trackid":
            $result = sql_query("SELECT trackid, uid, count(*) as hf_count FROM ${prefix}_tracking
                           WHERE tracktime BETWEEN '$timef' and '$timet'
                           GROUP BY trackid
                           ORDER BY hf_count DESC
                           ");
            $hf_headcount = sql_num_rows($result);
            $hf_sorttext = _TRACK_TRACKID;
            $hf_textend = " " . _TRACK_AND . " " . $hf_pageviews . " " . _TRACK_PAGES;
            break;
    }

    OpenTable();
    if ($text != '') {
        $hf_text = " " . _TRACK_AT . " '" . $text . "'";
    } else {
        $hf_text = '';
    }
    echo "<b>" . $hf_text1 . $hf_headcount . " " . $hf_sorttext . $hf_text . $hf_textend . "</b>" . "(" . _TRACK_FROM . " " . $timef . " " . _TRACK_TO . " " . $timet . ")";
    CloseTable();

    OpenTable();
    echo "<table width='100%' border='1' cellspacing='0' cellpadding='1'>";

    switch ($sort) {
        case "visit":
            echo "<tr>";
            echo "<th>" . _TRACK_DATE . "</th>";
            echo "<th>" . _TRACK_IP . "</th>";
            echo "<th>" . _TRACK_UID . "</th>";
            echo "<th>" . _TRACK_COUNT . "</th>";
            echo "</tr>";
            break;
        case "page":
            echo "<tr>";
            echo "<th>" . _TRACK_COUNT . "</th>";
            echo "<th>" . _TRACK_URL . "</th>";
            echo "</tr>";
            break;
        case "time":
            echo "<tr>";
            echo "<th>" . _TRACK_DATE . "</th>";
            echo "<th>" . _TRACK_IP . "</th>";
            echo "<th>" . _TRACK_UID . "</th>";
            echo "<th>" . _TRACK_URL . "</th>";
            echo "</tr>";
            break;
        case "date":
            echo "<tr>";
            echo "<th>" . _TRACK_DATE . "</th>";
            echo "<th>" . _TRACK_COUNT . "</th>";
            echo "</tr>";
            break;
        case "user":
            echo "<tr>";
            echo "<th>" . _TRACK_USER . "</th>";
            echo "<th>" . _TRACK_COUNT . "</th>";
            echo "</tr>";
            break;
        case "trackid":
            echo "<tr>";
            echo "<th>" . _TRACK_TRACKID . "</th>";
            echo "<th>" . _TRACK_COUNT . "</th>";
            echo "</tr>";
            break;
    }

    /* Schleife durch alle Datensaetze */
    while ($array = sql_fetch_assoc($result)) {
        if (@$array["uid"] != @$uid) {
            $uname = '';
            $uid = '';
        }

        $array['requrl'] = trim($array['requrl'], ' /?&');

        $href = "<a href=\"" . adminUrl(PMX_MODULE, '', "optr=show_line&amp;timef=" . @($array['tracktime']) . "&amp;ip=" . @$array['ip'] . "&amp;text=" . $f1_text) . "\">";

        $hf_date = @track_fc_format_date($array['tracktime'], _TRACK_DATESTRING);

        switch ($sort) {
            case "visit":
                if (($array["uid"] > 0) and ($array["uid"] != $uid)) {
                    $nukeuser = sql_query("SELECT uid, uname FROM {$user_prefix}_users WHERE uid='" . $array["uid"] . "'");
                    list($uid, $uname) = sql_fetch_row($nukeuser);
                }

                echo "<tr>";
                echo "<td>" . $array['hf_date'] . "</td>" . "<td>" . $array['ip'] . "</td>";
                if ($array["uid"] > 0) {
                    echo '<td>' . mxCreateUserprofileLink($uname) . '</td>';
                } else {
                    echo "<td>" . $uname . "</td>";
                }
                echo "<td><a href=\"" . adminUrl(PMX_MODULE, '', "optr=show_visit&amp;sort=" . $sort . "&amp;timef=" . ($timef) . "&amp;timet=" . ($timet) . "&amp;ip=" . $array['ip'] . "&amp;truid=" . $array["uid"]) . "\">" . $array['hf_count'] . "</a></td>";
                echo "</tr>\n";
                break;

            case "page":
                echo "<tr>";
                echo "<td><a href=\"" . adminUrl(PMX_MODULE, '', "optr=show_visit&amp;sort=" . $sort . "&amp;timef=" . ($timef) . "&amp;timet=" . ($timet) . "&amp;text=" . $text . "&amp;url=" . (str_replace("&", "@@", $array['requrl']))) . "\">" . $array['hf_count'] . "</a></td>" . "<td><a href=\"" . $array['requrl'] . "\" target=\"_blank\">" . track_umbruch($array['requrl'], 51) . "</a></td>";
                echo "</tr>\n";
                break;

            case "time":
                if (($array["uid"] > 0) and ($array["uid"] != $uid)) {
                    $nukeuser = sql_query("SELECT uid, uname FROM {$user_prefix}_users WHERE uid='" . $array["uid"] . "'");
                    list($uid, $uname) = sql_fetch_row($nukeuser);
                }

                echo "<tr>";
                echo "<td>" . $href . $hf_date . "</a></td>" . "<td>" . $array['ip'] . "</td>";
                if ($array["uid"] > 0) {
                    echo '<td>' . mxCreateUserprofileLink($uname) . '</td>';
                } else {
                    echo "<td>" . $uname . "</td>";
                }
                echo "<td><a href=\"" . $array['requrl'] . "\" target=\"_blank\">" . track_umbruch($array['requrl'], 51) . "</a></td>";
                echo "</tr>\n";
                break;

            case "date":

                echo "<tr>";
                echo "<td>" . $array['hf_date'] . "</td>";
                echo "<td><a href=\"" . adminUrl(PMX_MODULE, '', "optr=show_visit&amp;sort=" . $sort . "&amp;timef=" . ($array['hf_date'] . " 00:00:00") . "&amp;timet=" . ($array['hf_date'] . " 23:59:59")) . "\">";
                echo $array['hf_count'] . "</a></td>";
                echo "</tr>\n";
                break;

            case "user":

                echo "<tr>";
                if ($array["uid"] > 0) {
                    $nukeuser = sql_query("SELECT uid, uname FROM {$user_prefix}_users WHERE uid='" . $array["uid"] . "'");
                    list($uid, $uname) = sql_fetch_row($nukeuser);
                    echo '<td>' . mxCreateUserprofileLink($uname) . '</td>';
                } else {
                    echo "<td>" . $GLOBALS['anonymous'] . "</td>";
                }
                echo "<td><a href=\"" . adminUrl(PMX_MODULE, '', "optr=show_visit&amp;sort=" . $sort . "&amp;timef=" . ($timef) . "&amp;timet=" . ($timet) . "&amp;truid=" . $array["uid"]) . "\">";
                echo $array['hf_count'] . "</a></td>";
                echo "</tr>\n";
                break;

            case "trackid":

                echo "<tr>";
                echo "<td>" . $array['trackid'] . "</td>";
                if (($array["uid"] > 0) and ($array["uid"] != $uid)) {
                    $nukeuser = sql_query("SELECT uid, uname FROM {$user_prefix}_users WHERE uid='" . $array["uid"] . "'");
                    list($uid, $uname) = sql_fetch_row($nukeuser);
                }
                if ($array["uid"] > 0) {
                    echo '<td>' . mxCreateUserprofileLink($uname) . '</td>';
                } else {
                    echo "<td>Anonymous</td>";
                }
                echo "<td><a href=\"" . adminUrl(PMX_MODULE, '', "optr=show_visit&amp;timef=" . ($timef) . "&amp;timet=" . ($timet) . "&amp;trid=" . $array['trackid'] . "&amp;text=" . $text) . "\">" . $array['hf_count'] . "</a></td>";
                echo "</tr>\n";
                break;
        }
    }

    echo "</table>";
    CloseTable();
    if (isset($result)) {
        sql_free_result($result);
    }
    if (isset($nukeuser)) {
        sql_free_result($nukeuser);
    }
}
// ******************************************************************************
// Display pagesviews for one page
// ******************************************************************************
function show_page ($sort, $timef, $timet, $text, $url)
{
    global $user_prefix, $prefix;

    $url = urldecode(str_replace("@@", "&", $url));
    $result = sql_query("SELECT * FROM ${prefix}_tracking
                       WHERE tracktime   BETWEEN '$timef' and '$timet'
                         AND requrl =       '$url'
                       ORDER BY tracktime DESC ");

    OpenTable();
    echo "<table width='100%' border='1' cellspacing='0' cellpadding='1'>";
    echo "<tr>";
    echo "<th>" . _TRACK_DATE . "</th>";
    echo "<th>" . _TRACK_IP . "</th>";
    echo "<th>" . _TRACK_UID . "</th>";
    echo "<th>" . _TRACK_URL . "</th>";
    echo "</tr>";

    /* Schleife durch alle Datensaetze */
    while ($array = sql_fetch_assoc($result)) {
        $array['requrl'] = trim($array['requrl'], ' /?&');

        $uname = '';
        if ($array["uid"] > 0) {
            $nukeuser = sql_query("SELECT uname FROM {$user_prefix}_users WHERE uid='" . $array["uid"] . "'");
            list($uname) = sql_fetch_row($nukeuser);
        }

        $hf_date = track_fc_format_date($array['tracktime'], _TRACK_DATESTRING);

        echo '<tr>';
        echo '<td width="140">' . $hf_date . '</td>'
         . '<td width="120">' . $array['ip'] . '</td>';
        if ($array["uid"] > 0) {
            echo '<td width="40">' . mxCreateUserprofileLink($uname) . '</td>';
        } else {
            echo '<td width="40">' . $uname . '</td>';
        }
        echo '<td><a href="' . $array['requrl'] . '" target="_blank">' . track_umbruch($array['requrl'], 51) . '</a></td>'
         . '</tr>' . "\n";
    }
    echo '</table>';
    CloseTable();

    sql_free_result($result);
    sql_free_result($nukeuser);
}
// ******************************************************************************
// Display pagesviews for one page
// ******************************************************************************
function show_line ($timef, $f1_ip)
{
    global $user_prefix, $prefix;

    $result = sql_query("SELECT * FROM ${prefix}_tracking WHERE tracktime   = '$timef' " . "AND         ip   = '$f1_ip'"
        );

    OpenTable();
    echo "<table width='100%' border='1' cellspacing='0' cellpadding='1'>";

    echo "<colgroup>" . "<col width='1'>" . "<col width='99%'" . "</colgroup>";

    echo "<tr>";
    echo "<td></td>";
    echo "<td></td>";
    echo "</tr>";

    /* Schleife durch alle Datensaetze */
    while ($array = sql_fetch_assoc($result)) {
        track_print_array_table ($array, "2", "x");
    }
    echo "</table>";
    CloseTable();
}
// ******************************************************************************
// Display pagesviews for one visit
// ******************************************************************************
function show_visit ($sort, $timef, $timet, $f1_ip, $f1_uid, $f1_trid, $f1_url, $f1_text)
{
    global $user_prefix, $prefix;

    $f1_url = urldecode(str_replace("@@", "&", $f1_url));

    if ($f1_uid != '') {
        $sel_uid = " AND      uid = '$f1_uid'";
    }
    if ($f1_ip != '') {
        $sel_ip = " AND       ip = '$f1_ip'";
    }
    if ($f1_trid != '') {
        $sel_trid = " AND  trackid = '$f1_trid'";
    }
    if ($f1_url != '') {
        $sel_url = " AND   requrl = '$f1_url'";
    }

    switch ($sort) {
        case "tracktime": $hf_order = " ORDER BY tracktime";
            break;
        case "requrl": $hf_order = " ORDER BY requrl";
            break;
        case "ip": $hf_order = " ORDER BY ip";
            break;
        case "uid": $hf_order = " ORDER BY uid";
            break;
        case "trackid": $hf_order = " ORDER BY trackid";
            break;
        default: $hf_order = " ORDER BY tracktime DESC";
    }

    $result = sql_query("SELECT * FROM ${prefix}_tracking
                       WHERE tracktime   BETWEEN '$timef' and '$timet'" . $sel_uid . $sel_ip . $sel_trid . $sel_url . $hf_order
        );

    $hf_headcount = sql_num_rows($result);
    $hf_sorttext = _TRACK_PAGES;
    $hf_text = _TRACK_WITH;
    if ($f1_trid != '') {
        $hf_textend = " " . _TRACK_TRACKID . " " . $f1_trid;
    }
    if ($f1_uid != '') {
        $hf_textend = " " . _TRACK_UID . " " . $f1_uid;
    }
    if ($f1_ip != '') {
        $hf_textend = " " . _TRACK_IP . " " . $f1_ip;
    }
    if ($f1_url != '') {
        $hf_textend = " " . _TRACK_URL . " " . $f1_url;
    }

    OpenTable();
    echo "<b>" . $hf_headcount . " " . $hf_sorttext . " " . $hf_text . $hf_textend . "</b>" . "(" . _TRACK_FROM . " " . $timef . " " . _TRACK_TO . " " . $timet . ")";
    CloseTable();

    $href = adminUrl(PMX_MODULE, '', "optr=show_visit&amp;timef=" . urlencode($timef) . "&amp;timet=" . urlencode($timet) . "&amp;trid=" . $f1_trid . "&amp;uid=" . $f1_uid . "&amp;url=" . urlencode(str_replace("&", "@@", $f1_url)) . "&amp;ip=" . $f1_ipl . "&amp;text=" . $f1_text);

    OpenTable();
    echo "<table width='100%' border='1' cellspacing='0' cellpadding='1'>";
    echo "<tr>";
    echo "<th><a href=\"" . $href . "&amp;sort=tracktime\">" . _TRACK_DATE . "</a></th>";
    echo "<th><a href=\"" . $href . "&amp;sort=ip\">" . _TRACK_IP . "</a></th>";
    echo "<th><a href=\"" . $href . "&amp;sort=trackid\">" . _TRACK_TRACKID . "</a></th>";
    echo "<th><a href=\"" . $href . "&amp;sort=uid\">" . _TRACK_UID . "</a></th>";
    echo "<th><a href=\"" . $href . "&amp;sort=requrl\">" . _TRACK_URL . "</a></th>";
    echo "</tr>";

    /* Schleife durch alle Datensaetze */
    while ($array = sql_fetch_assoc($result)) {
        $array['requrl'] = trim($array['requrl'], ' /?&');

        $href = "<a href=\"" . adminUrl(PMX_MODULE, '', "optr=show_line&amp;timef=" . ($array["tracktime"]) . "&amp;ip=" . $array['ip'] . "&amp;text=" . $f1_text) . "\">";
        $hf_date = track_fc_format_date($array['tracktime'], _TRACK_DATESTRING);
        if ($array["uid"] != $uid) {
            $uname = '';
        }
        if (($array["uid"] > 0) and ($array["uid"] != $uid)) {
            $nukeuser = sql_query("SELECT uid, uname FROM {$user_prefix}_users WHERE uid='" . $array["uid"] . "'");
            list($uid, $uname) = sql_fetch_row($nukeuser);
        }

        echo '<tr>';
        echo '<td width="140">' . $href . $hf_date . '</a></td>'
         . '<td width="120">' . $array["ip"] . '</td>'
         . '<td width="120">' . $array["trackid"] . '</td>';
        if ($array["uid"] > 0) {
            echo '<td width="100">' . mxCreateUserprofileLink($uname) . '</td>';
        } else {
            echo '<td width="100">' . $uname . '</td>';
        }

        echo '<td width="300"><a href="' . $array['requrl'] . '" target="_blank">' . track_umbruch($array['requrl'], 51) . '</a></td>'
         . '</tr>' . "\n";
    }
    echo '</table>';
    CloseTable();

    if (isset($result)) {
        sql_free_result($result);
    }
    if (isset($nukeuser)) {
        sql_free_result($nukeuser);
    }
}
// ******************************************************************************
// Pls select the period for deleting
// ******************************************************************************
function go_to_delete_mode()
{
    global $user_prefix, $prefix;

    OpenTable();
    echo _TRACK_GODELSELECT1 . '<br /><br />'
     . _TRACK_GODELSELECT2 . '<br /><br />'
     . '<a href="' . adminUrl(PMX_MODULE, '', 'optr=start') . '">' . _TRACK_LEAVEDELMODE . '</a>';
    CloseTable();
}
// ******************************************************************************
// There will be x lines to deletion
// ******************************************************************************
function go_to_delete_select($timef, $timet)
{
    global $user_prefix, $prefix;

    if ($timet == '') {
        $timet = date("Y-m-d H:i:s", time());
    }

    $result = sql_query("SELECT tracktime FROM ${prefix}_tracking WHERE tracktime BETWEEN '$timef' and '$timet' ");
    $hf_count = sql_num_rows($result);
    if ($hf_count == "-1") {
        $hf_count = 0;
    }
    OpenTable();
    echo _TRACK_GODELWARN1 . ' ' . $hf_count . ' '
     . _TRACK_GODELWARN2 . '<br /><br />'
     . _TRACK_TIMEFROM . ': ' . $timef . '<br />'
     . _TRACK_TIMETO . ': ' . $timet . '<br /><br />'
     . _TRACK_GODELWARN3 . '<br /><br />'
     . '<a href="' . adminUrl(PMX_MODULE, '', 'optr=delnow&amp;timef=' . ($timef) . '&amp;timet=' . ($timet), '') . '">' . _TRACK_YESDELETE . '</a>'
     . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
     . '<a href="' . adminUrl(PMX_MODULE, '', 'optr=start') . '">' . _TRACK_NODONTDELETE . '</a>';
    CloseTable();
}
// ******************************************************************************
// Delete tracking lines
// ******************************************************************************
function delete_tracking($timef, $timet)
{
    global $user_prefix, $prefix;

    $result = sql_query("DELETE FROM ${prefix}_tracking WHERE tracktime BETWEEN '$timef' and '$timet' ");
    $hf_count = sql_affected_rows($result);
    if ($hf_count == "-1") {
        $hf_count = 0;
    }

    OpenTable();
    echo _TRACK_ITHAVEBEEN . ' ' . $hf_count . ' '
     . _TRACK_TRACKDELETED . '<br /><br />'
     . '<a href="' . adminUrl(PMX_MODULE, '', 'optr=start') . '">' . _TRACK_BACKTOSTART . '</a>';
    CloseTable();
}
// ******************************************************************************
// Start of program
// ******************************************************************************
function TrackingStart($f1_optr, $f1_sort, $f1_datef, $f1_datet, $f1_timef, $f1_timet, $f1_text,
    $f1_url, $f1_ip, $f1_uid, $f1_trackid)
{
    include("header.php");
    GraphicAdmin();
    global $menu;
    pmxDebug::pause();

    switch ($f1_optr) {
        case "start":
            $menu_optr = "start";
            break;
        case "godel":
            $menu_optr = "delsel";
            break;
        case "delsel":
            $menu_optr = "delsel";
            break;
        case "fdelsel":
            $menu_optr = "delsel";
            break;
        default:
            $menu_optr = "start";
    }

    if (($f1_optr == 'fstart') or ($f1_optr == 'fdelsel')) {
        $f1_timef = $f1_datef . " " . $f1_timef;
        $f1_timet = $f1_datet . " " . $f1_timet;
    }

    $menu[1]["timef"] = (isset($menu[1]["timef"])) ? $menu[1]["timef"] : '';
    $menu[1]["timet"] = (isset($menu[1]["timet"])) ? $menu[1]["timet"] : '';
    $menu[1]["text"] = (isset($menu[1]["text"])) ? $menu[1]["text"] : '';

    if (($f1_timef == '') and ($f1_timet == '')) {
        $f1_timef = $menu[1]["timef"];
        $f1_timet = $menu[1]["timet"];
        $f1_text = $menu[1]["text"];
    }

    OpenTable();
    echo "<br />\n";
    $hf_count = 0;
    while (list($index, $sub) = each($menu)) {
        if ($hf_count++ > 0) {
            echo " - ";
        }
        if ($f1_text != $sub["text"]) {
            $sub["timet"] = (isset($sub["timet"])) ? $sub["timet"] : '';
            $sub["timef"] = (isset($sub["timef"])) ? $sub["timef"] : '';
            echo "<a href=\"" . adminUrl(PMX_MODULE, '', "optr=" . $menu_optr . "&amp;sort=" . $f1_sort . "&amp;timef=" . ($sub["timef"]) . "&amp;timet=" . ($sub["timet"]) . "&amp;text=" . ($sub["text"])) . "\">";
        }
        echo $sub["text"];
        if ($f1_text != $sub["text"]) {
            echo "</a>\n";
        }
    }

    echo "&nbsp;&nbsp;&nbsp;";
    echo "(<a href=\"" . adminUrl(PMX_MODULE, '', 'optr=godel') . "\">" .
    _TRACK_GODEL . "</a>)";

    echo "<br /><br />\n";

    if ($f1_optr != "godel") {
        echo "<p><a href=\"";
        if ($f1_sort != "page") {
            echo "" . adminUrl(PMX_MODULE, '', "optr=" . $menu_optr . "&amp;sort=page&amp;timef=" . ($f1_timef) . "&amp;timet=" . ($f1_timet) . "&amp;text=" . ($f1_text));
        }
        echo "\">" . _TRACK_BYPAGES . "</a> - <a href=\"";

        if ($f1_sort != "visit") {
            echo "" . adminUrl(PMX_MODULE, '', "optr=" . $menu_optr . "&amp;sort=visit&amp;timef=" . ($f1_timef) . "&amp;timet=" . ($f1_timet) . "&amp;text=" . ($f1_text));
        }
        echo "\">" . _TRACK_BYVISITS . "</a> - <a href=\"";

        if ($f1_sort != "time") {
            echo "" . adminUrl(PMX_MODULE, '', "optr=" . $menu_optr . "&amp;sort=time&amp;timef=" . ($f1_timef) . "&amp;timet=" . ($f1_timet) . "&amp;text=" . ($f1_text));
        }
        echo "\">" . _TRACK_BYTIME . "</a> - <a href=\"";

        if ($f1_sort != "date") {
            echo "" . adminUrl(PMX_MODULE, '', "optr=" . $menu_optr . "&amp;sort=date&amp;timef=" . ($f1_timef) . "&amp;timet=" . ($f1_timet) . "&amp;text=" . ($f1_text));
        }
        echo "\">" . _TRACK_BYDATE . "</a> - <a href=\"";

        if ($f1_sort != "user") {
            echo "" . adminUrl(PMX_MODULE, '', "optr=" . $menu_optr . "&amp;sort=user&amp;timef=" . ($f1_timef) . "&amp;timet=" . ($f1_timet) . "&amp;text=" . ($f1_text));
        }
        echo "\">" . _TRACK_BYUSER . "</a> - <a href=\"";

        if ($f1_sort != "trackid") {
            echo "" . adminUrl(PMX_MODULE, '', "optr=" . $menu_optr . "&amp;sort=trackid&amp;timef=" . ($f1_timef) . "&amp;timet=" . ($f1_timet) . "&amp;text=" . ($f1_text));
        }
        echo "\">" . _TRACK_BYTRACKID . "</a>";
    }
    echo "</p>\n";
    // display selection form for period
    ?>
  <form method="post" action="<?php echo adminUrl(PMX_MODULE, '', 'optr=f' . $menu_optr) ?>" name="range">
    <?php echo _TRACK_DATEFROM ?> <input type="text" name="datef" size="10" maxlength="10" />
    <?php echo _TRACK_DATETO ?> <input type="text" name="datet" size="10" maxlength="10" /><br />
    <?php echo _TRACK_TIMEFROM ?> <input type="text" name="timef" maxlength="8" size="10" />
    <?php echo _TRACK_TIMETO ?> <input type="text" name="timet" maxlength="8" size="10" />
    <input type="submit" name="Submit" value="<?php echo _FORMSUBMIT ?>" />
  </form>
  <?php
    CloseTable();

    switch ($f1_optr) {
        case "delnow":
            delete_tracking($f1_timef, $f1_timet);
            break;
        case "delsel":
            go_to_delete_select($f1_timef, $f1_timet);
            break;
        case "fdelsel":
            go_to_delete_select($f1_timef, $f1_timet);
            break;
        case "godel":
            go_to_delete_mode();
            break;
        case "fstart":
            start ($f1_sort, $f1_timef, $f1_timet, $f1_text);
            break;
        case "start":
            start ($f1_sort, $f1_timef, $f1_timet, $f1_text);
            break;
        case "show_visit":
            show_visit ($f1_sort, $f1_timef, $f1_timet, $f1_ip, $f1_uid, $f1_trackid, $f1_url, $f1_text);
            break;
        case "show_page":
            show_page ($f1_sort, $f1_timef, $f1_timet, $f1_text, $f1_url);
            break;
        case "show_line":
            show_line ($f1_timef, $f1_ip);
            break;
        default:
            start ($f1_sort, $menu[1]["timef"], $menu[1]["timet"], $menu[1]["text"]);
    }

    pmxDebug::restore();

    include("footer.php");
}

TrackingStart($optr, $sort, $datef, $datet, $timef, $timet, $text, $url, $ip, $truid, $trid);

?>