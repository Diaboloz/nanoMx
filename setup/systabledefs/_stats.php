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
// Tabellenstruktur fuer Tabelle `mx_stats`
if (!isset($tables["${prefix}_stats"])) {
    $sqlqry[] = "
CREATE TABLE `${prefix}_stats` (
  `year` smallint(6) NOT NULL default '0',
  `month` tinyint(4) NOT NULL default '0',
  `date` tinyint(4) NOT NULL default '0',
  `hour` tinyint(4) NOT NULL default '0',
  `hits` int(11) NOT NULL default '0',
  PRIMARY KEY  (`year`,`month`,`date`,`hour`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
";
}
// // fuer geaenderte Statistiken
if (isset($tables["${prefix}_stats_hour"])) {
    $sqlqry[] = "REPLACE INTO `${prefix}_stats` SELECT `year`, `month`, `date`, `hour`, Sum(`hits`) FROM `${prefix}_stats_hour` GROUP BY `year`, `month`, `date`, `hour` ORDER BY `year`, `month`, `date`, `hour`;";
    if (isset($tables["${prefix}_stats_year"])) $sqlqry[] = "RENAME TABLE `${prefix}_stats_year` TO `" . RENAME_PREFIX . "${prefix}_stats_year`";
    if (isset($tables["${prefix}_stats_month"])) $sqlqry[] = "RENAME TABLE `${prefix}_stats_month` TO `" . RENAME_PREFIX . "${prefix}_stats_month`";
    if (isset($tables["${prefix}_stats_date"])) $sqlqry[] = "RENAME TABLE `${prefix}_stats_date` TO `" . RENAME_PREFIX . "${prefix}_stats_date`";
    if (isset($tables["${prefix}_stats_hour"])) $sqlqry[] = "RENAME TABLE `${prefix}_stats_hour` TO `" . RENAME_PREFIX . "${prefix}_stats_hour`";
}
// // ende  fuer geaenderte Statistiken
if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}
// Indexe
$indexes = setupGetTableIndexes("${prefix}_stats");
if (isset($indexes['PRIMARY'])) {
    if (!isset($indexes['PRIMARY']['all_fields']['year']) || !isset($indexes['PRIMARY']['all_fields']['month']) || !isset($indexes['PRIMARY']['all_fields']['date']) || !isset($indexes['PRIMARY']['all_fields']['hour'])) {
        $sqlqry[] = "ALTER TABLE `${prefix}_stats` DROP PRIMARY KEY, ADD PRIMARY KEY ( `year` , `month` , `date` , `hour` )";
    }
} else {
    $sqlqry[] = "ALTER TABLE `${prefix}_stats` ADD PRIMARY KEY ( `year` , `month` , `date` , `hour` )";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>