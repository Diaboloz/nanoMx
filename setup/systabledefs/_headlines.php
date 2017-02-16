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
// Tabellenstruktur für Tabelle `mx_headlines`
if (!isset($tables["${prefix}_headlines"])) {
    $sqlqry[] = "
CREATE TABLE `${prefix}_headlines` (
  `hid` int(11) NOT NULL auto_increment,
  `sitename` varchar(30) NOT NULL default '',
  `headlinesurl` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`hid`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}
// pragmaMx.org hier einfuegen, falls leer...
$result = sql_query("SELECT COUNT(hid) FROM `${prefix}_headlines`");
list($counthead) = sql_fetch_row($result);
if (empty($counthead)) {
    $sqlqry[] = "INSERT INTO `${prefix}_headlines` (`sitename`, `headlinesurl`) VALUES ('pragmaMx.org', 'http://www.pragmamx.org/modules.php?name=rss&feed=News');";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>