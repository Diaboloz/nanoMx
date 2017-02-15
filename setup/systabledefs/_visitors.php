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
// Tabellenstruktur für Tabelle `mx_visitors`
if (!isset($tables["${prefix}_visitors"])) {
    $sqlqry[] = "
CREATE TABLE `${prefix}_visitors` (
  `time` int(11) NOT NULL default '0',
  `ip` varchar(60) NOT NULL default '',
  `module` varchar(40) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `uid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ip`),
  KEY `time_id` (`time`,`uid`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>