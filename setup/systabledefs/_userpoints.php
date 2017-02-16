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
unset($sqlqry);

/* Tabellenstruktur fuer Tabelle `mx_userpoints` */
if (!isset($tables["${prefix}_userpoints"])) {
    $sqlqry[] = "
CREATE TABLE `${prefix}_userpoints` (
  `uid` int(11) NOT NULL DEFAULT '0',
  `punkte` int(11) NOT NULL DEFAULT '0',
  `updated` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
";
} else {
    // Felder aktualisieren
    $tf = setupGetTableFields("${prefix}_userpoints");
    // Felder ergaenzen/loeschen
    if (!isset($tf['updated'])) {
        $sqlqry[] = "ALTER TABLE `${prefix}_userpoints` ADD `updated` INT( 11 ) NOT NULL DEFAULT '0';";
    }

    $indexes = setupGetTableIndexes("${prefix}_userpoints");
    if (!isset($indexes['PRIMARY'])) {
        $sqlqry[] = "ALTER TABLE `${prefix}_userpoints` ADD PRIMARY KEY ( `uid` );";
    } else if ($indexes['PRIMARY']['Column_name'] != 'uid') {
        $sqlqry[] = "ALTER TABLE `${prefix}_userpoints`  DROP PRIMARY KEY, ADD PRIMARY KEY ( `uid` );";
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>