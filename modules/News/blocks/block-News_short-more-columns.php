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

/* Anzahl der Spalten */
$showcolumns = 2;

/* Anzahl der angezeigten Artikel
 * - eine beliebige Zahl, oder
 * - die globale Variable $GLOBALS['storyhome']
 */
$storynum = $GLOBALS['storyhome'];

/* Die ersten Teile des Artikeltextes anzeigen,
 * - eine beliebige Zahl, die die Menge der angezeigten Zeichen definiert
 * HTML-Tags, ausser <br /> werden entfernt und nicht mitgerechnet
 */
$textlen = 120;

/* Bilder im Text anzeigen
 * false = nein
 * true = ja
 */
$showpics = true;

/* --------- Ende der Konfiguration ----------------------------------------- */

extract($block['settings'], EXTR_OVERWRITE);

global $prefix;

/* Block kann gecached werden? */
$mxblockcache = true;

/* der passende Modulname */
$module_name = basename(dirname(dirname(__FILE__)));

if (!mxModuleAllowed($module_name)) {
    /* Block darf nicht gecached werden und weg... */
    $mxblockcache = false;
    return;
}

settype($showcolumns, 'int');
settype($storynum, 'int');
settype($textlen, 'int');
$qry = "SELECT s.sid, aid, s.title, time, DATE_ADD(time, INTERVAL 2 DAY) AS expire, now() AS nowi, hometext, bodytext, comments, s.counter, topic, informant, notes, acomm
    FROM ${prefix}_stories AS s
    WHERE (ihome='0' OR s.catid='0') AND time <= now() " . pmx_multilang_query('alanguage', 'AND') . "
    ORDER BY s.time DESC, s.sid DESC
    LIMIT " . intval($storynum);

$result = sql_query($qry);
if (!$result) {
    return;
}

$list = array();
while ($story = sql_fetch_assoc($result)) {
    $story['title'] = "<a href=\"modules.php?name=$module_name&amp;file=article&amp;sid=" . $story['sid'] . "\">" . strip_tags($story['title']) . "</a>";
    $introtext = '';
    if ($textlen) {
        // array zuruecksetzen
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
            // die gewuenschte Textlaenge um die Textlaenge des alternativen String erweitern
            $textlentemp = $textlen + strlen($alternate);
            // den gefundenen Tag in das array stellen
            $replaces[$alternate] = $img;
            // den gefundenen Tag aus dem Text entfernen und dafuer den alternativen String einsetzen
            $introtext = trim(str_replace($img, $alternate, $introtext));
        }
        // Text auf die gewuenschte Laenge kuerzen
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
    $image = '';
    if ($story['nowi'] < $story['expire']) {
        $datetime = mx_strftime(_SHORTDATESTRING, mxSqlDate2UnixTime($story['time']));
        $image = mxCreateImage("images/menu/new.gif", 'new: ' . $datetime) . '&nbsp;';
    }
    $list[] = '<h4>' . $image . $story['title'] . '</h4>' . $introtext;
}

if ($list && $showcolumns > 1) {
    $browser = load_class('Browser');
    $width = ($browser->msie) ? 99 : 100;
    $width = floor($width / $showcolumns);
    pmxHeader::add_style_code('
.nb-o {float: left; width: ' . $width . '%; overflow: hidden; margin: 0; padding: 0;}
.nb-i {margin-left: .3em; margin-right: .5em; margin-bottom: .5em; margin-top: .3em; padding: 0;}
    ');
    $i = 0;
    $content = '<div>';
    foreach ($list as $key => $value) {
        $i++;
        $content .= '
<div class="nb-o">
    <div class="nb-i">
    ' . $value . '
    </div>
</div>
';
        if ($i === $showcolumns) {
            $content .= '<div style="clear: both;"></div>';
            $i = 0;
        }
    }
    if ($i !== 0) {
        $content .= '<div style="clear: both;"></div>';
    }
    $content .= '</div>';
} else if ($list) {
    $content = implode("\n", $list);
}

?>
