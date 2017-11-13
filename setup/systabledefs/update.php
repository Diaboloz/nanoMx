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
 * $Revision: 321 $
 * $Author: pragmamx $
 * $Date: 2017-02-13 19:27:47 +0100 (Mo, 13. Feb 2017) $
 */

defined('mxMainFileLoaded') or die('access denied');

unset($sqlqry);

/* Einstellungen */
// das neue Charset
$newcharset = 'utf8';
// die neue Collation
$newcollate = 'utf8_unicode_ci';
// ALTER DATABASE dbname CHARACTER SET utf8 COLLATE utf8_unicode_ci;
//sql_query("ALTER DATABASE `{$dbname}` CHARACTER SET {$newcharset} COLLATE {$newcollate};");

/**
 * * SHOW TABLE STATUS [FROM datenbank] [LIKE platzhalter]
 */
$qry = 'SHOW TABLE STATUS FROM ' . $dbname . ' LIKE \'' . $prefix . '%\';';
$result = sql_query($qry);
while ($table = sql_fetch_assoc($result)) {
    if (isset($table['Collation']) && $table['Collation'] != $newcollate) {
        $sqlqry[] = 'ALTER TABLE `' . $table['Name'] . '` CONVERT TO CHARACTER SET ' . $newcharset . ' COLLATE ' . $newcollate;
    }
    if (isset($table['Engine']) && strtoupper($table['Engine']) != 'MYISAM' && strtoupper($table['Engine']) != 'MEMORY') {
        $sqlqry[] = 'ALTER TABLE `' . $table['Name'] . '` ENGINE = MYISAM;';
    }
}

if ($user_prefix != $prefix) {
    $qry = 'SHOW TABLE STATUS FROM ' . $dbname . ' LIKE \'' . $user_prefix . '%\';';
    $result = sql_query($qry);
    while ($table = sql_fetch_assoc($result)) {
        if (isset($table['Collation']) && $table['Collation'] != $newcollate) {
            $sqlqry[] = 'ALTER TABLE `' . $table['Name'] . '` CONVERT TO CHARACTER SET ' . $newcharset . ' COLLATE ' . $newcollate;
        }
        if (isset($table['Engine']) && strtoupper($table['Engine']) != 'MYISAM' && strtoupper($table['Engine']) != 'MEMORY') {
            $sqlqry[] = 'ALTER TABLE `' . $table['Name'] . '` ENGINE = MYISAM;';
        }
    }
}

if (isset($sqlqry)) {
    /* Probleme mit evtl. falschem Charset beheben */
    setup_set_sql_names('latin1');

    setupDoAllQueries($sqlqry);
    unset($sqlqry);

    /* Probleme mit evtl. falschem Charset beheben */
    setup_set_sql_names('utf8');
}

/* alte nuke Session Tabelle leeren */
if (isset($tables["${prefix}_session"])) {
    if (sql_num_rows(sql_query("SELECT guest FROM `${prefix}_session` LIMIT 1;"))) {
        $sqlqry[] = "TRUNCATE TABLE ${prefix}_session";
    }
}

/* unnötige Tabellen umbenennen */
$renametables = array("${prefix}_authors_users",
    "${prefix}_banned_ip",
    "${prefix}_banner_clients",
    "${prefix}_banner_plans",
    "${prefix}_banner_positions",
    "${prefix}_banner_terms",
    "${prefix}_bannerclient_neu",
    "${prefix}_catagories",
    "${prefix}_cities",
    "${prefix}_comments_moderated",
    "${prefix}_confirm",
    "${prefix}_groups_points",
    "${prefix}_groups",
    "${prefix}_intruder",
    "${prefix}_lastseen",
    "${prefix}_pollcomments_moderated",
    "${prefix}_public_messages",
    "${prefix}_quotes",
    "${prefix}_ranks",
    "${prefix}_reviews_comments_moderated",
    "${prefix}_session",
    "${prefix}_subscriptions",
    "${prefix}_users_temp",
    // cportal:
    "${prefix}_blockmessage",
    "${prefix}_blocks_manager",
    "${prefix}_metakeys",
    "${prefix}_metakeyscontent",
    "${prefix}_metakeysdownloads",
    "${prefix}_metakeyslinks",
    );
foreach($renametables as $table) {
    if (isset($tables[$table])) {
        $sqlqry[] = "RENAME TABLE `" . $table . "` TO `" . RENAME_PREFIX . $table . "`;";
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

/* Standardwerte, sowie alte Konfigurationsdateien von pragmaMx < 2.0
 * versuchen einzulesen und in neues Format zu konvertieren.
 */
if ($file = realpath(FILE_CONFIG_DEFAULTS)) {
    $defaults = include(FILE_CONFIG_DEFAULTS);
} else {
    $defaults = array();
}

/* alte Werte und Standardwerte vereinen */
foreach ($defaults as $section => $settings_array) {
    // prüfen ob Werte bereits vorhanden, wenn nicht anlegen
    $qry = setup_pushsettings($section, $settings_array, false);
    if ($qry) {
        $sqlqry[] = $qry;
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

unset($newcollate, $newcharset, $renametables, $table);

?>