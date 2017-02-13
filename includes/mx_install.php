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

/**
 * alle erstellten Queries ausfuehren
 */
function setupDoAllQueries($allqueries)
{
    foreach($allqueries as $query) {
        $query = trim($query);
        if (stripos($query, 'type')) {
            // TYPE=MyISAM nach ENGINE=MyISAM umschreiben
            $query = preg_replace('#(^.+(?:\)|\s))(type\s*=)([^)(]+$)#is', '$1ENGINE=$3', $query);
        }
        echo '<li>' . $query . '</li>';
        sql_query($query);
    }
}
// die bestehende Datenbankstruktur einlesen
// Array mit allen bereits vorhandenen Tabellen erstellen
function setupGetTables()
{
    $tables = array();
    $result = sql_query("SHOW TABLES;");
    while (list($tablename) = sql_fetch_row($result)) {
        $tables[$tablename] = $tablename;
    }
    return $tables;
}

/**
 * liest alle Felder einer Tabelle in ein assoziatives Array
 * der Feldname dient als Index
 */
function setupGetTableFields($tablename)
{
    global $tables;
    // print "<h5>$tablename</h5>";
    $fields = array();
    $result = sql_query("SHOW COLUMNS FROM `${tablename}`");
    if (!$result || sql_error()) {
        // irgenwie kommt das ab und an zu dem Fehler:
        // - Can't create/write to file '/tmp/#sql_8c4_0.MYD' (Errcode: 13)
        // http://bugs.mysql.com/bug.php?id=25872
        // dann einfach etwas warten und nochmal probieren ;-))
        sleep(1);
        $result = sql_query("SHOW COLUMNS FROM `${tablename}`");
    }
    while ($row = sql_fetch_assoc($result)) {
        $fields[$row['Field']] = $row;
    }
    sql_free_result($result);
    return $fields;
}

/**
 * liest alle indexe einer Tabelle in ein Array
 */
function setupGetTableIndexes($tablename)
{
    $indexes = array();
    $result = sql_query("SHOW INDEXES FROM `${tablename}`");
    if (!$result || sql_error()) {
        // siehe dazu: setupGetTableFields()
        sleep(1);
        $result = sql_query("SHOW INDEXES FROM `${tablename}`");
    }
    while ($row = sql_fetch_assoc($result)) {
        if (!isset($indexes[$row['Key_name']])) {
            $indexes[$row['Key_name']] = $row;
        }
        $indexes[$row['Key_name']]['all_fields'][$row['Column_name']] = $row['Seq_in_index'];
    }
    sql_free_result($result);
    return $indexes;
}

?>