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
global $sitename;
// Betreffzeile
define("_HELLOSUBJECT1", "Hallo"); # + username
define("_HELLOSUBJECT2", "willkommen bei"); # + sitename

// Nachrichtentext
define("_HELLOTEXT", "

Allgemeines zur Seite:
<br /><i>$sitename ist ein online Magazin für Webmaster und Nuker. Beachten Sie bitte das Forum, hier werden Webmaster und Nuker fündig, wenn es um Tips und Hilfe rund um Webdesign, phpNuke etc. geht.</i>
<br /><br />Datenschutz:
<br /><i>Die von Ihnen hier <a href=\"modules.php?name=Your_Account\" target=\"_blank\">angegebenen Daten</a> werden unter keinen Umständen an Dritte weitergegeben oder verkauft. Wir belästigen unsere Mitglieder nicht mit unfreiwilligen Newslettern oder Emails; unaufgefordert werden Sie somit keine Emails von uns an den von Ihnen angegebenen Email Account erhalten! Sie können jederzeit Ihren Account löschen! Dazu finden Sie in <a href=\"modules.php?name=Your_Account\" target=\"_blank\">Ihrem Account</a> oben einen Link. Es steht Ihnen somit frei, jederzeit alle eingegebenen Daten wieder zu entfernen. Mehr Informationen dazu finden Sie auch in der <a href=\"modules.php?name=FAQ\" target=\"_blank\">FAQ</a>, Bereich Registrierung.</i>
");

?>