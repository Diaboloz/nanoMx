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
 *
 * @package pragmaMx
 */

return true;

$todo_langarray = array(
    /* Dateien: language/lang-XXX.php */
//    "_SCROLLTOTHETOP" => "Zum Seitenanfang scrollen",
//    "_NOTIFYSUBJECT" => "Benachrichtigung",
//    "_NOTIFYMESSAGE" => "Hallo, es gibt neue Einsendungen auf Deiner Seite.",
//    "_NOTITLE" => "ohne Titel",
    // "_ADMINDASHBOARD" => "Dashboard",
    /* Dateien: admin/language/lang-XXX.php */
    // "_PANELTOOLS" => "Wartung",
    // "_PANELTOOLS_DESCR" => "Tools zur Wartung der Webseite",
    /* Dateien: modules/News/language/lang-XXX.php */
    // "_TIPATHPATH" => "Pfad der Themenbilder",
    // "_TIPATHPATHDESCR" => "der Ordnerpfad zu den Themenbildern",
    /* Dateien: admin/modules/seo/language/lang-XXX.core.php */
    // "_SEO" => "Suchmaschinen Optimierung",
    /* Dateien: admin/modules/seo/language/lang-XXX.php */
    // "_SEOGLOBALSET" => "allgemeine Einstellungen",
    // "_SEOSITEMAP" => "Sitemap Einstellungen",
    // "_SEOSITEMAPACTIVE" => "Sitemap aktivieren",
    // "_SEOSITEMAPKEYS" => "Schlüsselbegriffe",
    // "_SEOSITEMAPKEYSDESC" => "Schlüsselbegriffe, die die Wertigkeit der einzelnen Links erhöhen, wenn sie im Text oder Titel vorkommen.",
    // "_SEOSITEMAPLIMIT" => "Limit",
    // "_SEOSITEMAPLIMITDESC" => "Maximale Anzahl der Artikel/Links pro Modul.",
    // "_SEOSITEMAPEXMOD" => "Module ignorieren",
    // "_SEOSITEMAPEXMODDESC" => "Module, die in der Gesamtliste der Module nicht erscheinen sollen, das betrifft nur den Link zum Modul selbst.",
    /* Dateien: modules/rss/language/lang-XXX.php */
    // "_RSS_DESCRIBE" => "Beschreibung",
    // "_RSS_ASSEMBLING" => "RSS-Feed's zusammenstellen",
    // "_RSS_FORMAT" => "Ausgabeformat",
    // "_RSS_SECTION" => "Bereiche",
    // "_RSS_SECTIONALL" => "alle Bereiche",
    // "_RSS_SECTIONDESC1" => "Modulnamen mit Komma trennen, oder 'ALL' für eine Kombination aus allen verfügbaren Modulen.",
    // "_RSS_SECTIONDESC2" => "Bereiche auswählen oder 'ALL' für eine Kombination aus allen verfügbaren Bereichen.",
    // "_RSS_OTHERS" => "Sonstiges",
    // "_RSS_POINTLIMIT" => "wieviel einzelne Punkte in den jeweiligen feeds anzeigen (15 maximal !)",
    // "_RSS_LENDESC" => "max. Angezeigte Länge der RSS-Beschreibung",
    // "_RSS_LENIMGDESC" => "max. Angezeigte Länge der Image-Beschreibung",
    // "_RSS_LENITEMDESC" => "max. Angezeigte Länge der einzelnen Item-Beschreibung",
    // "_RSS_LINKCREATE" => "Link erstellen",
    // "_RSS_LINK2COPY" => "Der Link zum kopieren:",
    // "_RSS_LINK2CLICK" => "Der Link zum anklicken:",
    // "_RSS_PREVIEW" => "Ausgabequelltext-Vorschau:",
    // "_RSS_FEEDERROR1" => "Fehler im RSS-Feed",
    // "_RSS_FEEDERROR2" => "Der Bereich '%s' kann nicht angezeigt werden. Bitte wählen sie einen anderen RSS-feed",
    // "_RSS_NOTACTIVE" => "Sorry, RSS-Feeds sind zur Zeit nicht aktiviert.",
    // "_RSS_DEFAULTIMGDESC" => "Feed provided by %s. Click to visit.",
    // /* Dateien: admin/modules/logfiler/language/lang-XXX.core.php */
    // "_LOGFILER" => "Logfiles",
    // /* Dateien: admin/modules/logfiler/language/lang-XXX.php */
    // "_LOGF_FILENAME" => "Dateiname",
    // "_LOGF_FILETIME" => "Zeit",
    // "_LOGF_FILEWGHT" => "Größe",
    // "_LOGF_DELOK0" => "Es wurden keine Dateien gelöscht.",
    // "_LOGF_DELOK1" => "Die Datei '%s' wurde gelöscht.",
    // "_LOGF_DELOK2" => "Es wurden folgende Dateien gelöscht: %s",
    // "_LOGF_DELNOK1" => "Die Datei '%s' konnte nicht gelöscht werden.",
    // "_LOGF_DELNOK2" => "Folgende Dateien konnten nicht gelöscht werden: %s",
    // "_LOGF_SELECTALL" => "alle auswählen",
    /* Dateien: modules/Private_Messages/language/lang-XXX.core.php */
    /* Dateien: modules/pm/language/lang-XXX.core.php */
    // "_PMS_NEW_MESSAGE" => "eine neue Nachricht",
    // "_PMS_NEW_MESSAGES" => "%d neue Nachrichten",
    /* Dateien: modules/Private_Messages/language/lang-XXX.php */
    /* Dateien: modules/pm/language/lang-XXX.php */
    // "_PMS_GLOBAL" => "Allgemein",
    // "_PMS_BUDDY" => "Messenger",
    // "_PMS_PREFERENCES" => "Berechtigungen",
    // "_PMS_PAGESIZE" => "Anzahl der Zeilen in der Liste",
    // "_PMS_ALLOWDELUNREAD" => "auch ungelesene Nachrichten loeschen?",
    // "_PMS_ALLOWSMILIES" => "Smilies aktivieren?",
    // "_PMS_ALLOWBBCODE" => "bbCode erlauben",
    // "_PMS_ALLOWIMAGES" => "Bilder erlauben?",
    // "_PMS_ALLOWHTML" => "HTML erlauben?",
    // "_PMS_BLANKAVATAR" => "Standard Avatar",
    // "_PMS_USEICONS" => "Post-Icons verwenden?",
    // "_PMS_DEFAULTICON" => "Standard Post-Icon",
    // "_PMS_PATHICONS" => "Pfad zu Post-Icons",
    // "_PMS_PATHMODPICS" => "Pfad zu Modulbildern",
    // "_PMS_BUDDYSMILIES" => "Smilies im Messenger verwenden?",
    // "_PMS_BUDDYWIDTH" => "Breite des Messenger Popup in Px",
    // "_PMS_BUDDYMESWIDTH" => "Breite des Nachrichten-Popup in Px",
    // "_PMS_BUDDYMESHEIGHT" => "Höhe des Nachrichten-Popup in Px",
    // "_PMS_BUDDYREFRESHLIST" => "Refreshzeit der Userliste, wenn Buddy alleine laeuft",
    // "_PMS_BUDDYREFRESHPM" => "Abfrageintervall der PM, wenn Buddy alleine laeuft",
    // "_PMS_BUDDYADMINPREFIX" => "Admins in der Userliste kennzeichnen mit:",
    // "_PMS_BUDDYEXUSERS" => "User, die nicht angezeigt werden sollen:",
    // "_PMS_BUDDYBACKCOLOR" => "Hintergrundfarbe des Messengers",
    // "_PMS_BUDDYTEXTCOLOR" => "Textfarben des Messengers",
    // "_PMS_LIMITFORADMIN" => "PM Begrenzung gilt auch für Admins?",
    // "_PMS_LIMITFORADMINGOD" => "PM Begrenzung gilt auch für System-Administratoren?",
    // "_PMS_LIMITDEFAULT" => "Standardeinstellung, fuer alle ungenannten Gruppen",
    // !!! folgendes nicht, das ist nur vorübergehend hier gelagert !!!
    );

if (isset($TOOL)) {
    return true;
}

foreach ($todo_langarray as $constant => $value) {
    defined($constant) OR define($constant, $value);
}

if (!defined('mxMainFileLoaded')) {
    reset($todo_langarray);
    echo '<pre>';
    foreach ($todo_langarray as $key => $value) {
        echo 'define("' . $key . '", "' . addslashes($value) . '");' . "\n";
    }
    echo '</pre>';
}

?>