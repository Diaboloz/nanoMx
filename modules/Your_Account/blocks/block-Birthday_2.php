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
$blockheight = 100;
/* --------- Ende der Konfiguration ----------------------------------------- */

extract($block['settings'], EXTR_OVERWRITE);

/* der passende Modulname */
$module_name = basename(dirname(dirname(__FILE__)));

if (!mxModuleAllowed($module_name)) {
    /* Block darf nicht gecached werden und weg... */
    $mxblockcache = false;
    return;
}

global $user_prefix;

/* Block kann gecached werden? */
$mxblockcache = true;

/* Sprache einbinden */
if (!defined('_BIRTHDAY_TODAY')) {
    mxGetLangfile($module_name, 'birthday/lang-*.php');
}

$sql = "SELECT uname, (YEAR( CURRENT_DATE ) - YEAR( user_bday )) - ( RIGHT( CURRENT_DATE, 5 ) < RIGHT( user_bday, 5 ) ) AS user_age
        FROM {$user_prefix}_users
        WHERE MONTH(user_bday)=MONTH(CURRENT_DATE) AND DAYOFMONTH(user_bday)=DAYOFMONTH(CURRENT_DATE) AND YEAR(user_bday)<YEAR(CURRENT_DATE) AND user_stat=1
        ORDER BY user_age DESC;";
$result = sql_query($sql);

$items = array();
$list = '';
while (list($uname, $user_age) = sql_fetch_row($result)) {
    $items[] = array('uname' => mxCreateUserprofileLink($uname), 'userage' => $user_age);
}

if (!$items) {
    return;
}

/* Variablen uebergeben */
$tplvars['blockheight'] = $blockheight;
$tplvars['items'] = $items;

/* Templateausgabe erstellen */
$tpl = load_class('Template');
$tpl->init_path(__FILE__);
$tpl->init_template(__FILE__);
$tpl->assign($tplvars);
$content = $tpl->fetch();

?>