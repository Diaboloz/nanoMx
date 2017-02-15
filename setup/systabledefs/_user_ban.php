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
 * $Revision: 171 $
 * $Author: PragmaMx $
 * $Date: 2016-06-29 13:59:03 +0200 (Mi, 29. Jun 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

unset($sqlqry);

if (!isset($tables["${prefix}_user_ban"])) {
    $sqlqry[] = "
CREATE TABLE `${prefix}_user_ban` (
  `ban_type` varchar(100) NOT NULL default '',
  `ban_val` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`ban_type`,`ban_val`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

/* Sicherstellen, dass Standardgruppe eingetragen ist */
$result = sql_query("select count(ban_type) from ${prefix}_user_ban WHERE ban_type='ban_auto'");
list($cbantype)=sql_fetch_row($result);

if ($cbantype==0) {
    $sqlqry[] = "INSERT INTO `${prefix}_user_ban` (`ban_type`, `ban_val`) VALUES ('ban_auto', '0')";
}
if ($cbantype>0) {
    $sqlqry[] = "DELETE FROM `${prefix}_user_ban` WHERE ban_type='ban_auto'";
	$sqlqry[] = "INSERT INTO `${prefix}_user_ban` (`ban_type`, `ban_val`) VALUES ('ban_auto', '1')";
}

if (!isset($tables["${prefix}_user_ban"])) {
    /* Werte aus altem Bannsystem uebernehmen, falls vorhanden */
    $cursettingsfile = PMX_REAL_BASE_DIR . '/includes/userip_ban.php';
    if (file_exists($cursettingsfile)) {
        unset($ip_ban);
        include($cursettingsfile);
        if (isset($ip_ban)) {
            $ip_net = explode('|', $ip_ban);
            foreach ($ip_net as $ban_ip) {
                $ban_ip = trim($ban_ip);
                if ($ban_ip) {
                    $bans[] = array('ban_ip', $ban_ip);
                }
            }
            @rename($cursettingsfile, $cursettingsfile . '.bak-' . SETUP_ID . '.php');
        }
    }

    /* alte Daten aus config.php importieren, falls vorhanden */
    if (isset($GLOBALS['CensorListUsers']) && is_array($GLOBALS['CensorListUsers'])) {
        foreach ($GLOBALS['CensorListUsers'] as $oldban_name) {
            $bans[] = array('ban_name', $oldban_name);
        }
    }

    $bans[] = array('ban_ip', '255.255.255.255');
    $bans[] = array('ban_mail', 'demo@url.tld');
    $bans[] = array('ban_name', 'admin');
    $bans[] = array('ban_name', 'administrador');
    $bans[] = array('ban_name', 'arsch');
    $bans[] = array('ban_name', 'god');
    $bans[] = array('ban_name', 'nobody');
    $bans[] = array('ban_name', 'operator');
    $bans[] = array('ban_name', 'penner');
    $bans[] = array('ban_name', 'root');
    $bans[] = array('ban_name', 'trottel');

    $part = array();
    foreach ($bans as $tmp) {
        $part[] = "('$tmp[0]', '$tmp[1]')";
    }
    $part = array_unique($part);
    sort($part);

    /* versch. Standardwerte in neue Tabelle eintragen */
    $sqlqry[] = "REPLACE INTO `${prefix}_user_ban` (`ban_type` ,`ban_val`)
    VALUES " . implode(',', $part) . "";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>
