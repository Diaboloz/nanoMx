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
// //////////////////////////////////////////////////////
// Modulueberschrift anzeigen
$showcaption = 0;
// //////////////////////////////////////////////////////
// Anzahl der Artikel in der Liste pro Topic
// Wenn 0, dann wird das Modul wie im alten
// Original-phpNuke Modul (bis 6.0) dargestellt
$headlinecount = 6;
// //////////////////////////////////////////////////////
// Anzahl der Artikel in der Liste pro Topic,
// wenn per &tid=xx nur ein bestimmtes Topic gelistet wird
// Wenn 0, dann wird das Modul wie im alten
// Original-phpNuke Modul (bis 6.0) dargestellt
$headlinecount_topic = 100;
// //////////////////////////////////////////////////////
// Anzahl der Spalten, (Topics nebeneinander)
// Nur bei Anzeige aller Topics
// Minimum = 1
$columnscount = 1;
// //////////////////////////////////////////////////////
// Topicsbild anzeigen
// 1 = Ja, 0 = Nein
$showimage = 1;
// //////////////////////////////////////////////////////
// Topicsbild RECHTS anzeigen, ansonsten LINKS
// 1 = Rechts, 0 = Links
$topicimageRight = 1;
// //////////////////////////////////////////////////////
// Funktion "thememiddlebox" zur Anzeige verwenden
// (nur bei Themes fuer VKP-Mxxx oder pragmaMx)
// 1 = Ja, 0 = Nein
$useMxMiddlebox = 1;
// //////////////////////////////////////////////////////
// rechte Bloecke anzeigen
// 1 = Ja, 0 = Nein
$GLOBALS['index'] = 0;
// //////////////////////////////////////////////////////
// Optik des <ul><li> Tags in der Artikelliste
// leer lassen, fuer Standard-HTML-Ausgabe
$liststyle = " style=\"list-style: circle url(modules/$module_name/images/rarrow.gif);\"";
// //////////////////////////////////////////////////////

?>