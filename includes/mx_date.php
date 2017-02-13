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
 * $Revision: 144 $
 * $Author: PragmaMx $
 * $Date: 2016-05-03 18:36:14 +0200 (Di, 03. Mai 2016) $
 *
 * german language file by:
 * pragmaMx Developer Team
 * corrections by Joerg Fiedler, http://www.vatersein.de/
 */

defined('mxMainFileLoaded') or die('access denied');


/**
 * Beschreibung
 *
 * @param string $dateformat
 * @param mixed $unixtimestamp optional, default value 0
 * @return string
 */
function mx_strftime($dateformat, $unixtimestamp = 0)
{
    if (!is_numeric($unixtimestamp)) {
        $unixtimestamp = strtotime($unixtimestamp);
    }
    $unixtimestamp = intval($unixtimestamp);
    if (!$unixtimestamp) {
        $unixtimestamp = time();
    }

    $out = strftime($dateformat, $unixtimestamp);

    switch (true) {
        case !defined('_SETLOCALE'):
        case !_SETLOCALE:
        case (stripos(_SETLOCALE, 'UTF8') === false) && (stripos(_SETLOCALE, 'UTF-8') === false):
            $out = utf8_encode($out);
    }

    $search = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December", "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
    $replace = array(_JANUARY, _FEBRUARY, _MARCH, _APRIL, _MAY, _JUNE, _JULY, _AUGUST, _SEPTEMBER, _OCTOBER, _NOVEMBER, _DECEMBER, _WEEKFIRSTDAY, _WEEKSECONDDAY, _WEEKTHIRDDAY, _WEEKFOURTHDAY, _WEEKFIFTHDAY, _WEEKSIXTHDAY, _WEEKSEVENTHDAY);
    $out = str_replace($search, $replace, $out);
    return $out;
}

function mxTime2DateTime($dtime=0)
{
	return mx_strftime(_DATETIME_FORMAT ,strtotime($dtime));
}

function mxTime2Date($dtime=0)
{
	return mx_strftime(_SHORTDATESTRING ,strtotime($dtime));
}

/*  datestring in unix-Time umwandeln */

function mxDate2Time($datestring="now")
{
	return strtotime($datestring);
}

/**
 * Gibt eine formatierte Datumsangabe zu einer übergebene mySQL-Zeitangabe zurück
 *
 * Folgende Formatangaben stehen als Konstanten in den Hauptsprachdateien zur
 * Verfügung: _DATESTRING (Standardvorgabe), _DATESTRING2, _XDATESTRING,
 * _SHORTDATESTRING, _XDATESTRING2
 * Die Formatparameter entsprechen denen der PHP-Funktion strftime(),
 * siehe http://de3.php.net/manual/de/function.strftime.php
 *
 * @since pragmaMx 0.1.6
 * @staticvar string $ger
 * @param int $time Zeitangabe im UNIX-Timestamp Format
 * @param string $dateformat Formatangabe, bestimmt das Ausgabeformat.
 * @return string Gibt je nach Spracheinstellung eine deutsche oder internationale Zeitangabe
 * zurück
 */
 
 
function formatTimestamp($time, $dateformat = _DATESTRING)
{
    $timestamp = strtotime($time);
    if ($timestamp) {
        return mx_strftime($dateformat, $timestamp);
    }
    return '';
}

/**
 * Nur zur Kompatibilität mit phpNuke-Modulen
 *
 * @param int $timestamp Unix-Zeitstempel
 * @return Liefert phpNuke-konformes Registrierungsdatum zurück
 *
 * deprecated !!
 */

 function mxGetNukeUserregdate($timestamp = 0)
{
   trigger_error('Use of deprecated phpnuke-function ' . __FUNCTION__ . '()', E_USER_WARNING);
    $timestamp = (empty($timestamp)) ? time() : $timestamp;
    $old = setlocale(LC_TIME, 0);
    setlocale(LC_TIME, 'en_EN');
    $user_regdate = date('M d, Y', $timestamp);
    setlocale(LC_TIME, $old);
    return $user_regdate;
}


/**
 * Wandelt SQL-Konforme Zeitangabe in einen Unix-Zeitstempel um
 *
 * @param string $sqldate Zeitangabe aus Datenbankquerie
 * @return integer Liefert Unix-Zeitstempel zurück
 */
function mxSqlDate2UnixTime($sqldate)
{
    switch (true) {
        case !$sqldate:
            return 0;
        case $out = strtotime($sqldate):
            return $out;
        default:
            return 0;
    }
}


/**
 * SQL-Konformes Datum schreiben
 *
 * @param int $year Jahr
 * @param int $month Monat
 * @param int $day Tag
 * @return string Liefert SQL-Konformes Datum zurück
 */
function mxGetSqlDate($year, $month, $day)
{
    $isdate = checkdate((int)$month, (int)$day, (int)$year);
    $out = ($isdate) ? sprintf("%04d-%02d-%02d", $year, $month, $day) : "";
    return $out;
}


/**
 * Datumselect-Gruppe
 *
 * erstellt eine Gruppe von <select> Feldern zur Eingabe eines Datums.
 * pragmamx.org
 *
 * @param string $username
 * @param string $password
 * @tables users, permissions
 * @author Andi
 */
function mxDateSelect($timestamp, $yearname = "year", $monthname = "month", $dayname = "day")
{
    // der Monat
    $month = date("n", $timestamp);
    $monthopt = "";
    for ($i = 1; $i <= 12; $i++) {
        $monthopt .= "<option value=\"$i\"" . (($i == $month) ? 'selected="selected" class="current"' : '') . ">$i\n</option>\n";
    }
    $monthopt = "<select name=\"$monthname\">\n$monthopt</select> \n";
    // der Tag
    $day = date("j", $timestamp);
    $dayopt = "";
    for ($i = 1; $i <= 31; $i++) {
        $dayopt .= "<option value=\"$i\"" . (($i == $day) ? 'selected="selected" class="current"' : '') . ">$i\n</option>\n";
    }
    $dayopt = "<select name=\"$dayname\">\n$dayopt</select> \n";

    if (_SYS_INTERNATIONALDATES) {
        $out = $dayopt . $monthopt;
    } else {
        $out = $monthopt . $dayopt;
    }
    // das Jahr
    $out .= '<input type="text" name="' . $yearname . '" value="' . date("Y", $timestamp) . '" size="5" maxlength="4" />';
    return $out;
}

?>