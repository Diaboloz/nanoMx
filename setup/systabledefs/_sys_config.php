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
 * $Revision: 304 $
 * $Author: PragmaMx $
 * $Date: 2016-12-19 12:48:55 +0100 (Mo, 19. Dez 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');
unset($sqlqry);

/* ab pragmaMx 2.0 */
// --------------------------------------------------------
// Tabellenstruktur fuer Tabelle `mx_sys_config`
if (!isset($tables["{$prefix}_sys_config"])) {
    $sqlqry[] = "
CREATE TABLE `{$prefix}_sys_config` (
  `section` varchar(50) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` longtext,
  `serialized` tinyint(1) NOT NULL default '0',
  `change` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`section`,`key`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
";

	$sqlqry[] = "INSERT IGNORE INTO `{$prefix}_sys_config` (`section` ,`key` ,`value` ,`serialized` ,`change`) VALUES ('pmx.hooks', 'deactivated', 'a:0:{}', '1', '2014-03-31 18:28:02');";

	
} else {
    $tf = setupGetTableFields("{$prefix}_sys_config");
    if (!isset($tf['change'])) {
        $sqlqry[] = "ALTER TABLE `{$prefix}_sys_config` ADD `change` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP AFTER `serialized`";
    }
    if ($tf['value']['Default'] !== null || $tf['value']['Type'] != 'longtext') {
        $sqlqry[] = "ALTER TABLE `{$prefix}_sys_config` CHANGE `value` `value` longtext NULL";
    }

    $indexes = setupGetTableIndexes("{$prefix}_sys_config");
    if (!isset($indexes['PRIMARY'])) {
        $sqlqry[] = "ALTER TABLE `{$prefix}_sys_config` ADD PRIMARY KEY ( `section` , `key` )";
    }
	$sqlqry[] = "ALTER TABLE `{$prefix}_sys_config` CHANGE `key` `key` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;";
}
	

	
if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>
