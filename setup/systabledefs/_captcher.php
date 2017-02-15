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
// ab pragmaMx 0.1.9
// --------------------------------------------------------
// Tabellenstruktur für Tabelle `mx_captcher`
if (!isset($tables["${prefix}_captcher"])) {
    $sqlqry[] = "
CREATE TABLE `${prefix}_captcher` (
  `ckey` varchar(32) NOT NULL default '',
  `ctime` varchar(20) NOT NULL default '',
  `timestamp` int(11) NOT NULL default '0',
  `cip` varchar(25) NOT NULL default '',
  PRIMARY KEY  (`ckey`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>
