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
// --------------------------------------------------------
// Tabellenstruktur für Tabelle `mx_groups_access`
if (!isset($tables["${prefix}_groups_access"])) {
    $sqlqry[] = "
CREATE TABLE `${prefix}_groups_access` (
  `access_id` int(10) NOT NULL auto_increment,
  `access_title` varchar(20) default NULL,
  PRIMARY KEY  (`access_id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}
// Daten für Tabelle `mx_groups_access`
// Sicherstellen, dass Standardgruppe eingetragen ist
$result = sql_query("select access_id from ${prefix}_groups_access WHERE access_id='1' AND access_title='" . MX_FIRSTGROUPNAME . "'");
if ($result && !@sql_num_rows($result)) {
    $sqlqry[] = "REPLACE INTO ${prefix}_groups_access VALUES (1, '" . ((defined('MX_FIRSTGROUPNAME')) ? MX_FIRSTGROUPNAME : 'User') . "');";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>