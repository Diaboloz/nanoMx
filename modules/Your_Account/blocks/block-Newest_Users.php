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


echo '<p class="warning">', basename(__DIR__), DS, basename(__FILE__), '<br>BLOCK IST NICHT FERTIG!!!</p>';
// TODO:
// - einfach alles



// ################ Einstell Variablen ##############
// // Anzahl der anzuzeigenden User
$ucount = 5;
// // Blockcache erlauben
$mxblockcache = true;
// ##################################################
global $user_prefix;
$ucount = intval($ucount);
$qry = "SELECT uname, user_regtime FROM ${user_prefix}_users WHERE user_stat=1 ORDER BY user_regtime DESC LIMIT " . $ucount . ";";
$result = sql_query($qry); # x neuste User ermitteln
while (list($uname, $user_regtime) = sql_fetch_row($result)) {
    $lasts[] = '<img src="images/menu/rarrow.gif" width="14" height="9" alt="" border="0">&nbsp;<a href="modules.php?name=Userinfo&amp;uname=' . $uname . '">' . $uname . '</a>';
}

if (isset($lasts)) {
    $content = implode("<br>\n", $lasts);
}

?>