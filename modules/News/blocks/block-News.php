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

/* --------- Konfiguration für den Block ----------------------------------- */

/* Anzahl der Spalten */
$showcolumns = 2;

/* Anzahl der angezeigten Artikel
 * - eine beliebige Zahl, oder
 * - die globale Variable $GLOBALS['storyhome']
 */
$storynum = $GLOBALS['storyhome'];

/* Die ersten Teile des Artikeltextes anzeigen,
 * - 0 für nein, oder
 * - eine beliebige Zahl, die die Menge der angezeigten Zeichen definiert
 * HTML-Tags, ausser <br /> werden entfernt und nicht mitgerechnet
 */
$textlen = 280;

/* Bilder im Text anzeigen, wenn $textlen angeschaltet ist
 * false = nein
 * true = ja
 */
$showpics = true;

/* Buttonleiste anzeigen
 * false = nein
 * true = ja */
$showbuttons = true;

/* Kommentarbutton anzeigen in Buttonleiste
 * Einstellungen:
 * 0 = Immer anzeigen
 * 1 = Nur anzeigen, wenn Kommentare vorhanden
 */
$showcombutton = 0;

/* Veröffentlichungsdatum anzeigen
 * false = nein
 * true = ja */
$showdate = true;

/* Kategorie zusammen mit der Artikelüberschrift anzeigen
 * false = nein
 * true = ja */
$showcattitle = false;

/* Links zu Suche etc. anzeigen
 * false = nein
 * true = ja */
$showextendedlinks = false;

/* Links oberhalb, unterhalb oder an beiden Stellen anzeigen
 * 0 = nur unterhalb
 * 1 = nur oberhalb
 * 2 = ober- und unterhalb
 */
$positionextendedlinks = 0;

/* Bild für neü Artikel anzeigen
 * false = nein
 * true = ja */
$shownewpic = true;

/* --------- Ende der Konfiguration ----------------------------------------- */

extract($block['settings'], EXTR_OVERWRITE);

global $prefix;

/* der passende Modulname */
$module_name = basename(dirname(__DIR__));

if (!mxModuleAllowed($module_name)) {
    /* Block darf nicht gecached werden und weg... */
    $mxblockcache = false;
    return;
}

/* Block kann gecached werden? */
$mxblockcache = true;

$qry = "SELECT s.sid, cat.catid, aid, s.title, time, DATE_ADD(time, INTERVAL 2 DAY) AS expire, now() AS jetzt, hometext, bodytext, comments, s.counter, topic, informant, notes, acomm, cat.title AS title1
        FROM ${prefix}_stories AS s LEFT JOIN ${prefix}_stories_cat AS cat ON s.catid = cat.catid
        WHERE (ihome='0' OR s.catid='0') AND time <= now() " . pmx_multilang_query('alanguage', 'AND') . "
        ORDER BY s.time DESC, s.sid DESC
        LIMIT " . intval($storynum);

$result = sql_query($qry);
if (!$result) {
    return;
}

$count = 0;
$out = '';
$out2 = '';
$width = floor(100 / $showcolumns);
while ($story = sql_fetch_assoc($result)) {
    $totalcount = strlen(strip_tags($story['hometext'])) + strlen(strip_tags($story['bodytext']));

    $introtext = '';
    if ($textlen) {
        // array zurücksetzen
        $replaces = array();
        $prependtags = array();
        $textlentemp = $textlen;

        if ($showpics) {
            // Alle Tags ausser <br /> & <img> entfernen
            $introtext = trim(strip_tags($story['hometext'], '<br><img>'));
            // <br /> am Textbeginn entfernen
            $introtext = preg_replace('#^(?:<br\s*/*>\s*)*#is', '', $introtext);
            // alle img & br tags suchen und zwischenspeichern
            preg_match_all('#<(br|img)[^>]*>#si', $introtext, $prependtags);
        } else {
            // Alle Tags ausser <br /> entfernen
            $introtext = trim(strip_tags($story['hometext'], '<br>'));
            // <br /> am Textbeginn entfernen
            $introtext = preg_replace('#^(?:<br\s*/*>\s*)*#is', '', $introtext);
            // alle br tags suchen und zwischenspeichern
            preg_match_all('#<br[^>]*>#si', $introtext, $prependtags);
        }

        foreach($prependtags[0] as $i => $img) {
            // einen alternativen String zum Ersetzen erstellen
            $alternate = md5($img);
            // die gewünschte Textlänge um die Textlänge des alternativen String erweitern
            $textlentemp = $textlen + strlen($alternate);
            // den gefundenen Tag in das array stellen
            $replaces[$alternate] = $img;
            // den gefundenen Tag aus dem Text entfernen und dafür den alternativen String einsetzen
            $introtext = trim(str_replace($img, $alternate, $introtext));
        }
        // Text auf die gewünschte Länge kürzen
        $introtext = mxCutString($introtext, $textlentemp, "&nbsp;...", " ");
        // wenn Tags gefunden wurden, die alternativen Textteile wieder
        // durch die im Array zwischengespeicherten Tags ersetzen
        if (count($replaces)) {
            $introtext = str_replace(array_keys($replaces), array_values($replaces), $introtext);
        }

        if ($introtext) {
            $introtext = '<div class="postcontent">' . $introtext . '</div>';
        }
    }
    // das formatierte Datum
    $datetime = '';
    if ($showdate) {
        $datetime = '<td width="96%">' . mx_strftime(_SHORTDATESTRING, mxSqlDate2UnixTime($story['time'])) . '</td>';
    }
    // die Buttons
    $buttons = array();
    if ($showbuttons) {
        $buttons[] = '<a href="modules.php?name=' . $module_name . '&amp;file=print&amp;sid=' . $story['sid'] . '" rel="nofollow" target="_blank">' . mxCreateImage('images/menu/print.gif', _BMXPRINTER, array('title' => _BMXPRINTER)) . '</a>';
        if (mxModuleAllowed('Recommend_Us')) {
            $buttons[] = '<a href="modules.php?name=' . $module_name . '&amp;file=friend&amp;sid=' . $story['sid'] . '" rel="nofollow">' . mxCreateImage('images/menu/friend.gif', _BMXFRIEND, array('title' => _BMXFRIEND)) . '</a>';
        }
        $buttons[] = '<a href="modules.php?name=' . $module_name . '&amp;file=article&amp;sid=' . $story['sid'] . '">' . mxCreateImage('images/menu/bytesmore.gif', _BMXBYTESMORE . ' ' . $totalcount, array('title' => _BMXBYTESMORE . ': ' . $totalcount)) . '</a>';
        if ($GLOBALS['articlecomm'] && ($showcombutton == 0 || ($showcombutton == 1 && $story['comments'] > 0))) {
            $buttons[] = '<a href="modules.php?name=' . $module_name . '&amp;file=article&amp;sid=' . $story['sid'] . '#comments">' . mxCreateImage('images/menu/comments2.gif', _COMMENTS, array('title' => _COMMENTS . ' (' . $story['comments'] . ')')) . '</a>';
        }
        $buttons[] = mxCreateUserprofileLink($story['informant'], mxCreateImage('images/menu/informant.gif', _UNICKNAME . ': ' . $story['informant'], array('title' => _UNICKNAME . ': ' . $story['informant'])));
        $buttons[] = mxCreateImage('images/menu/counter.gif', $story['counter'] . ' ' . _READS, array('title' => $story['counter'] . ' ' . _READS));
    }
    $colspan = count($buttons) + 1;
    // $buttons = '<table cellspacing="1" cellpadding="1" align="right"><tr><td>'.implode('</td><td>', $button).'</td></tr></table>';
    $buttons = '<td>' . implode('</td><td>', $buttons) . '</td>';
    // der Titel des Artikels
    $story['title'] = '<a href="modules.php?name=' . $module_name . '&amp;file=article&amp;sid=' . $story['sid'] . '">' . $story['title'] . '</a>';
    // den Kategoriename mit anzeigen
    if ($showcattitle && $story['catid'] && $story['title1']) {
        $story['title'] = $story['title'] . '<br /><span class="smaller">[<a href="modules.php?name=' . $module_name . '&amp;file=categories&amp;catid=' . $story['catid'] . '">' . $story['title1'] . '</a>]</span>';
    }
    if ($shownewpic && $story['jetzt'] < $story['expire']) {
        $story['title'] = mxCreateImage('images/menu/new.gif', '', 0, 'align="bottom"') . '&nbsp;' . $story['title'];
    }
    if ($count == 0) {
        $out .= '<tr valign="top">';
    }
    $out .= '
    <td width="' . $width . '%" valign="top" class="border">
        <table cellspacing="0" cellpadding="2" width="100%">';
    if ($datetime || $buttons) {
        $out .= '
            <tr valign="top" style="height: 10px;">
                ' . $datetime . $buttons . '
            </tr>
            ';
    }
    $out .= '
            <tr valign="top" style="height: 35px;">
                <td colspan="' . $colspan . '"><h4>' . $story['title'] . '</h4>' . $introtext . '</td>
            </tr>
        </table>
    </td>
    ';

    $count++;
    if ($count * $width == 100) {
        $out .= '</tr>';
        $count = 0;
    }
}
// falls nicht alle Tabellenspalten am Ende gefüllt sind, eine zus. anfügen
if ($count && $out) {
    $out .= '<td colspan="' . ($showcolumns - $count) . '">&nbsp;</td></tr>';
}
// die Links zu anderen Modulen
if ($showextendedlinks) {
    if (mxModuleAllowed('Stories_Archive')) {
        $links[] = '<a href="modules.php?name=Stories_Archive">' . _STORIEARCHIVE . '</a>';
    }
    if (mxModuleAllowed('Submit_News')) {
        $links[] = '<a href="modules.php?name=Submit_News">' . _SUBMITNEWS . '</a>';
    }
    if (mxModuleAllowed('Search')) {
        $links[] = '<a href="modules.php?name=Search&amp;m=' . $module_name . '">' . _NEWS_SEARCH . '</a>';
    }
    if (isset($links)) {
        $out2 = '<tr><td align="center" colspan="' . $showcolumns . '" class="border">';
        $out2 .= '[&nbsp;' . implode('&nbsp;|&nbsp;', $links) . '&nbsp;]';
        $out2 .= '</td></tr>';
        switch ($positionextendedlinks) {
            case 2:
                $out = $out2 . $out . $out2;
                break;
            case 1:
                $out = $out2 . $out;
                break;
            case 0:
            default:
                $out .= $out2;
                break;
        }
    }
}
// content-tabelle, nur wenn überhaupt was zum anzeigen da ist...
if ($out) {
    $content = '<table width="100%" border="0" cellspacing="2" cellpadding="5">' . $out . '</table>';
}

?>