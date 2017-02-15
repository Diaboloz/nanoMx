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
$modname = basename(dirname(__DIR__));

unset($sqlqry);

if (isset($tables["${prefix}_referer"])) {
    // wenn ungueltiger oder kein primaer-Index vorhanden, die Tabelle zur
    // Sicherheit einfach loeschen und neu erstellen lassen
    $indexes = setupGetTableIndexes("${prefix}_referer");
    $falseindex = false;
    if (!isset($indexes['PRIMARY'])) {
        $falseindex = true;
    } else if ($indexes['PRIMARY']['Column_name'] != 'rid') {
        $falseindex = true;
    } else if (isset($indexes['rid'])) {
        $falseindex = true;
    }
    if ($falseindex) {
        unset($tables["${prefix}_referer"]);
        $sqlqry[] = "DROP TABLE ${prefix}_referer";
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

if (!isset($tables["${prefix}_referer"])) {
    $sqlqry[] = "
CREATE TABLE `${prefix}_referer` (
  `rid` int(11) NOT NULL auto_increment,
  `url` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`rid`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
";
} else {
    // Felder aktualisieren
    $tf = setupGetTableFields("${prefix}_referer");
    // if (!isset($tf['reftime'])) $sqlqry[] = "ALTER TABLE `${prefix}_referer` ADD `reftime` DATETIME NOT NULL;";
    if ($tf['url']['Default'] === null || $tf['url']['Type'] != 'varchar(255)' || $tf['rid']['Type'] != 'int(11)') {
        $sqlqry[] = "ALTER TABLE `${prefix}_referer`
                CHANGE `url` `url` varchar(255) NOT NULL default '',
                CHANGE `rid` `rid` INT( 11 ) NOT NULL AUTO_INCREMENT";
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>