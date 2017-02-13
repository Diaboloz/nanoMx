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

/* --------- Konfiguration fuer den Block ----------------------------------- */
$days = 3; // Tage die im Voraus angezeigt werden sollen
$showage = false; // Alter des Users mit anzeigen
/* --------- Ende der Konfiguration ----------------------------------------- */

extract($block['settings'], EXTR_OVERWRITE);

global $user_prefix;

/* der passende Modulname */
$module_name = basename(dirname(dirname(__FILE__)));

if (!mxModuleAllowed($module_name)) {
    /* Block darf nicht gecached werden und weg... */
    $mxblockcache = false;
    return;
}

/* Block kann gecached werden? */
$mxblockcache = true;

/* Sprache einbinden */
if (!defined('_BIRTHDAY_TODAY')) {
    mxGetLangfile($module_name, 'birthday/lang-*.php');
}

$sql = "
SELECT
  uname,
  user_bday,
  (YEAR(CURRENT_DATE) - YEAR(user_bday)) - (RIGHT(CURRENT_DATE, 5) < RIGHT(user_bday, 5)) AS user_age,
  DATEDIFF(DATE(DATE_FORMAT(user_bday, CONCAT(IF((RIGHT(CURRENT_DATE, 5) <= RIGHT(user_bday, 5)), YEAR(CURRENT_DATE), YEAR(CURRENT_DATE)+1), '-%m-%d'))), CURRENT_DATE) as days_to
FROM `{$user_prefix}_users`
WHERE user_bday IS NOT NULL AND user_bday <> '0001-01-01' AND user_bday <> '0000-00-00' AND user_stat=1
HAVING days_to <= " . intval($days) . "
ORDER BY days_to ASC, user_age DESC
";

$items = array();
$result = sql_system_query($sql);
while ($row = sql_fetch_assoc($result)) {
    switch ($row['days_to']) {
        case 0:
            $msg = _BIRTHDAY_TODAY;
            break;
        case 1:
            $msg = _BIRTHDAY_NEXTDAY;
            break;
        case 2:
            $msg = _BIRTHDAY_IN2DAYS;
            break;
        default:
            $msg = _BIRTHDAY_INXDAYS;
    }

    if ($showage) {
        $msg .= sprintf(' (%d ' . _BIRTHDAYYEARS . ')', $row['user_age']);
    }
    $items[] = sprintf($msg, mxCreateUserprofileLink($row['uname']), $row['days_to']);
}

/* Templateausgabe erstellen */
$tpl = load_class('Template');
$tpl->init_path(__FILE__);
$tpl->init_template(__FILE__);
$tpl->items = $items;
$tpl->module_name = $module_name;
$content = $tpl->fetch();

?>