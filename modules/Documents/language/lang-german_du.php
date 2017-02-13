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
 * Author: Olaf Herfurth / TerraProject  http://www.tecmu.de
 *
 * $Revision: 6 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (Mi, 08. Jul 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

define("_DOCUMENTS_TITLE", "Dokumente");

/* books */

define("_DOCU", "Dokument");
// define("_DOCS_CREATED","erstellt");
define("_DOCS_CHANGED", "geändert");
define("_DOCS_PAGECOUNT", "Anzahl/Seite");
define("_DOCS_VIEWCONTENT", "zeige Inhalte");
define("_DOCS_MOVEUP", "nach vorn");
define("_DOCS_MOVEDN", "nach hinten");
define("_DOCS_POSITION", "Position");
define("_DOCS_PUBLISH", "Freigabe");
define("_DOCS_ACCESS", "Zugriff");
define("_DOCS_OWNER", "Eigentümer");
define("_DOCS_PUBLISHED", "freigeben");
define("_DOCS_UNPUBLISHED", "sperren");
define("_DOCS_EDIT", "Dokument bearbeiten");
define("_DOCS_NEW", "Dokument neu");

define("_DOCS_PAGE_NEW", "Neu");
define("_DOCS_PAGE_EDIT", "Bearbeiten");

define("_DOCS_SECTION", "Bereich");
define("_DOCS_EDIT_TEXT", "Hier kannst du die Angaben zum Dokument bearbeiten");
define("_DOCS_INFO", "Informationen zum Dokument angeben. Diese Informationen werden nicht angezeigt.");
define("_DOCS_ALIAS", "alias");
define("_DOCS_ALIAS_TEXT", "Bitte einen 'alias'-Namen eingeben (optional)");
define("_DOCS_KEYWORDS", "Keywords");
define("_DOCS_KEYWORDS_TEXT", "Hier kommagetrennt Keywords zum Text eingeben. Damit wird die Suche erleichtert.");
define("_DOCS_NEW_BOOK", "Neues Dokument");
define("_DOCS_NEW_CONTENT", "Neuer Artikel");
define("_DOCS_MOVECONTENT", "Markierte Artikel werden in den gewählten Artikel verschoben. Sie können auch wieder einzelne Artikel abwählen. <br /> ACHTUNG: evtl. untergeordnete Artikel werde mit verschoben !!");

define("_DOCS_CHILDS", "enthaltene Artikel");
define("_DOCS_CONTENTDELETEINFO", "Markierte Artikel werden gelöscht inkl. ihrer eventuellen Anlagen !! Eventuelle Unterartikel werde in den übergeordneten Artikel verschoben");
define("_DOCS_DELETEINFO", "Markierte Dokumente werden gelöscht, ebenso alle darin enthaltenen Artikel mit Ihren Anlagen !! Sie können auch einzelne Dokumente wieder abwählen. ");
define("_DOCS_INDEX", "Inhalt");
define("_DOCS_TITLE", "Titel");
define("_DOCS_TITLE_TEXT", "Hier bitte den Titel des Dokumentes eintragen.");
define("_DOCS_LANGUAGE", "Sprache auswählen");
define("_DOCS_LANGUAGE_TEXT", "hier bitte die Sprache auswählen, unter welcher das Dokument angezeigt werden soll. Bei Auswahl ALLE wird es immer angezeigt.");
define("_DOCS_PREAMBLE", "Einleitung zum Dokument");
define("_DOCS_PREAMBLE_TEXT", "Die Einleitung zum Dokument wird auf der Startseite angezeigt. Bleibt dieses Feld leer wird die kurzbeschreibung des Dokumentes verwendet.");

define("_DOCS_COPYRIGHT", "Copyright");
define("_DOCS_COPYRIGHT_TEXT", "Hier bitte die evtl. Urheberrechte eintragen");
define("_DOCS_SHORTDESC", "Kurzbeschreibung");
define("_DOCS_SHORTDESC_TEXT", "Kurzbeschreibung des Dokumentes erscheit auf der Startseite des Dokumentes , nicht auf der Modul-Startseite.");

define("_DOCS_USERGROUP", "Zugriff");
define("_DOCS_USERGROUP_TEXT", "Auswählen, welche Usergruppe Zugriff auf das Dokument hat");

/* content*/
define("_DOCS_CONTENT_EDIT", "Inhalt bearbeiten");
define("_DOCS_CONTENT_TITLE", "Inhalts Titel");
define("_DOCS_CONTENT_TITLE_TEXT", "hier den Titel des Inhaltes angeben");

/* Search */
define("_DOCS_SEARCH", "Inhaltssuche");
define("_DOCS_SEARCH_RESULTS_TEXT", "Folgende Seiten wurden mit ihrem Suchmuster gefunden");
define("_DOCS_SEARCH_NORESULTS_TEXT", "Es gibt kein Suchergebnis, welches mit der Suchmaske übereinstimmt.");
define("_DOCS_SEARCHMASK", "Suchmuster");
define("_DOCS_SEARCHINFO", "Hier bitte die Suchwörter durch Komma getrennt eingeben ");

/*config*/
define("_DOCS_CONFIG", "Konfiguration");
define("_DOCS_CONF_RIGHTBLOCKS", "rechte Blöcke anzeigen");

define("_DOCS_CONF_TITLE", "Modultitel");
define("_DOCS_CONF_TITLE_TEXT", "Hier einen Modultitel angeben. ");

define("_DOCS_CONF_STARTPAGE", "Konfiguration Modul");
define("_DOCS_CONF_STARTPAGE_TEXT", "");

define("_DOCS_CONF_LOGGING", "Änderungen loggen");
define("_DOCS_CONF_LOGGING_TEXT", "Wenn aktiviert, werden Datum und User bei Änderungen an den Dokumenten in eine Logtabelle geschrieben.");

define("_DOCS_CONF_BLOGPAGE", "Konfiguration Blog");
define("_DOCS_CONF_BLOGPAGE_TEXT", "");
define("_DOCS_CONF_INDEXPAGE", "Konfiguration Kategorieansicht");
define("_DOCS_CONF_INDEXPAGE_TEXT", "");

define("_DOCS_CONF_RIGHTS", "Konfiguration Rechte");
define("_DOCS_CONF_RIGHTS_TEXT", "");
// define("_DOCS_CONF_INDEXVIEW","Inhaltsübersicht anzeigen");
// define("_DOCS_CONF_INDEXVIEW_TEXT","Wenn aktiviert wird eine Übersicht aller Dokumente angezeigt.");
define("_DOCS_CONF_BLOGVIEW", "Ansicht");
define("_DOCS_CONF_BLOGVIEW_TEXT", "Kategorieansicht - zeigt eine Übersicht der Dokumente an. Blogansicht - zeigt die neuesten Artikel in einer Blogansicht an. ");

define("_DOCS_CONF_BREADCRUMP", "Breadcrump anzeigen");
define("_DOCS_CONF_BREADCRUMP_TEXT", "");
define("_DOCS_CONF_PREAMBLE", "Volle Einleitung anzeigen");
define("_DOCS_CONF_PREAMBLE_TEXT", "Wenn aktiviert, wird auf der Startseite des Moduls die volle Einleitung der Dokumente angezeigt. Wenn deaktiviert werden nur die Anzahl zeichen angezeigt, wie in nachstehender Eingabe angegeben.");
define("_DOCS_CONF_CHARCOUNT", "Länge Einleitung");
define("_DOCS_CONF_CHARCOUNT_TEXT", "Anzahl der Zeichen für die Länge der Einleitung angeben. Nur aktiv, wenn vorstehende Einstellung deaktiviert");
define("_DOCS_CONF_INDEXCOUNT", "Inhaltsverzeichnis");
define("_DOCS_CONF_INDEXCOUNT_TEXT", "0=kein Inhaltsverzeichnis für die einzelnen Dokumente auf der Startseite, Wert >0 gibt die Tiefe des Inhaltsverzeichnisses für alle Dokumente in der Übersicht an, welches angezeigt wird.");
define("_DOCS_CONF_SEARCHCOUNT", "Anzahl Suchergebnisse");
define("_DOCS_CONF_SEARCHCOUNT_TEXT", "Angeben, wieviel Suchergebnisse maximal angezeigt werden sollen.");
define("_DOCS_CONF_LANGUAGE", "Sprachauswahl");
define("_DOCS_CONF_LANGUAGE_TEXT", "Hier auswählen, in welcher Sprache standardmäßig neue Artikel angelegt werden sollen.");
define("_DOCS_CONF_TABCOUNT", "Anzahl Spalten");
define("_DOCS_CONF_TABCOUNT_TEXT", "Anzahl der Spalten in denen die Dokumente/Artikel auf der Startseite angezeigt werden.");

define("_DOCS_PAGE_NEWS", "Neue Artikel anzeigen");
define("_DOCS_PAGE_NEWS_TEXT", "Wenn aktiviert werden neue Artikel auf der Startseite des Moduls angezeigt");

define("_DOCS_PAGE_NEWSCOUNT", "Zeitspanne für neue/geänderte Artikel");
define("_DOCS_PAGE_NEWSCOUNT_TEXT", "(in Tagen) wie lange Artikel als 'Neu' oder 'geändert' gekennzeichnet werden");

define("_DOCS_PAGE_CHANGES", "Geänderte Artikel anzeigen");
define("_DOCS_PAGE_CHANGES_TEXT", "Wenn aktiviert werden geänderte Artikel auf der Startseite des Moduls angezeigt. ");
define("_DOCS_PAGE_CHANGESCOUNT", "Anzahl Artikel");
define("_DOCS_PAGE_CHANGESCOUNT_TEXT", "maximale Anzahl, wieviel Artikel als 'NEU' oder 'Geändert' oder als Blog auf der Startseite angezeigt werden.");

/* attachments */
define("_DOCS_ATTACHMENTS", "Anhänge");
define("_DOCS_ATTACH_DELETE", "diesen Anhang zum Löschen markieren.");
define("_DOCS_ATTACH_MAX", "max. Anhänge");
define("_DOCS_ATTACH_MAX_TEXT", "max.Anzahl Datei-Anhänge zum Dokument");

define("_DOCS_CONF_ATTACH", "Konfiguration Dokumentenanhänge");
define("_DOCS_CONF_ATTACH_TEXT", "Hier wird das Verhalten für die Dateianhänge für Dokumente eingestellt. Anhänge werden unterhalb des Dokumentes als Downloadliste angezeigt und können vom User heruntegeladen werden.");
define("_DOCS_CONF_ATTACH_ON", "Anhänge erlauben");
define("_DOCS_CONF_ATTACH_ON_TEXT", "Wenn aktiviert, können Dateinanhänge an die Dokumente angefügt werden. ");
define("_DOCS_CONF_ATTACH_MAXSIZE", "max. Dateigröße");
define("_DOCS_CONF_ATTACH_MAXSIZE_TEXT", "in kByte pro Datei");
define("_DOCS_CONF_ATTACH_PATH", "Verzeichnis");
define("_DOCS_CONF_ATTACH_PATH_TEXT", "hier Verzeichnis angeben, wo die Dateianhänge gespeichert werden sollen.");
define("_DOCS_CONF_ATTACH_MEDIA", "Mediadaten anzeigen");
define("_DOCS_CONF_ATTACH_MEDIA_TEXT", "Wenn aktiviert, werden Mediadaten (mp3,mp4,Bilder) unterhalb des Content angezeigt und nicht in der Downloadliste.");
define("_DOCS_CONF_ATTACH_MAXWIDTH", "max. Breite von Mediadateien");
define("_DOCS_CONF_ATTACH_MAXWIDTH_TEXT", "hier die Breite in Pixeln angeben, wie breit Mediadateien im Content angezeigt werden dürfen. Die Angabe bitte mit dem verwendeten Theme abstimmen. ");
define("_DOCS_CONF_ATTACH_MAXWIDTHTHUMB", "max. Ausdehnung von Thumbnails");
define("_DOCS_CONF_ATTACH_MAXWIDTHTHUMB_TEXT", "max. Ausdehnung von Thumbnails angeben für Bilder. Diese werden dann in einer Lightbox angezeigt.");
define("_DOCS_CONF_ATTACH_MAXHEIGHT", "max. Höhe von Mediadateien");
define("_DOCS_CONF_ATTACH_MAXHEIGHT_TEXT", "hier die Höhe in Pixeln angeben, wie hoch Mediadateien im Content angezeigt werden dürfen. Die Angabe bitte mit dem verwendeten Theme abstimmen. ");

define("_DOCS_CONF_PAGE", "Konfiguration Dokumentenseite");
define("_DOCS_CONF_PAGE_TEXT", "");
define("_DOCS_PAGE_INDEX", "Index anzeigen");
define("_DOCS_PAGE_INDEX_TEXT", "Wenn aktiviert wird ein Inhaltsverzeichnis auf jeder Seite angezeigt.");
define("_DOCS_PAGE_INDEXFULL", "Index komplett anzeigen");
define("_DOCS_PAGE_INDEXFULL_TEXT", "Wenn aktiviert wird immer das komplette Inhaltsverzeichnis auf jeder Seite angezeigt.");
define("_DOCS_PAGE_LASTEDITOR", "letzten Autor anzeigen ");
define("_DOCS_PAGE_LASTEDITOR_TEXT", "Wenn aktiviert wird unterhalb des Beitrages das letzte Änderungsdatum und der Autor angezeigt.");
define("_DOCS_PAGE_VIEWKEYWORDS", "Keywords anzeigen ");
define("_DOCS_PAGE_VIEWKEYWORDS_TEXT", "Wenn aktiviert werden die Keywords unter dem Artikel angezeigt.");
define("_DOCS_PAGE_VIEWNAVIGATION", "Navigation anzeigen");
define("_DOCS_PAGE_VIEWNAVIGATION_TEXT", "Wenn aktiviert werden unterhalb des Artikels Button zum Blättern durch die Artikel angezeigt.");
define("_DOCS_PAGE_CREATOR", "Ersteller anzeigen");
define("_DOCS_PAGE_CREATOR_TEXT", "Wenn aktiviert wird unterhalb des Beitrages der Ersteller angezeigt.");
define("_DOCS_PAGE_VIEWRATING", "Bewertung anzeigen");
define("_DOCS_PAGE_VIEWRATING_TEXT", "Wenn aktiviert werden über dem Artikel die Bewertung angezeigt.");

define("_DOCS_PAGE_VIEWSOCIAL", "Social Links anzeigen ");
define("_DOCS_PAGE_VIEWSOCIAL_TEXT", "Wenn aktiviert werden unter dem Artikel Links zu verschiedenen Social-Netzwerken angezeigt.");

define("_DOCS_PAGE_EDITORS", "Wer darf Artikel ändern ");
define("_DOCS_PAGE_EDITORS_TEXT", "Angeben, welche Usergruppe Artikel anlegen und ändern darf. Wird hier 'user' angegeben, können ALLE Usergruppen editieren. Administratoren können immer alles. Löschen kann nur ein Administrator.");
define("_DOCS_PAGE_EDITOR_RIGHTS", "Darf Editor Freigeben");
define("_DOCS_PAGE_EDITOR_RIGHTS_TEXT", "Bitte Angeben, ob Editoren auch neue Artikel freischalten dürfen.");

define("_DOCS_PAGE_VIEWSIMILAR", "ähnliche Artikel anzeigen");
define("_DOCS_PAGE_VIEWSIMILAR_TEXT", "Wenn aktiviert werden unterhalb des Beitrages eine Liste ähnlicher Artikel angezeigt.");
define("_DOCS_PAGE_SIMILARCOUNT", "Anzahl ähnlicher Artikel");
define("_DOCS_PAGE_SIMILARCOUNT_TEXT", "Hier angeben, wieviel ähnliche Artikel unter dem Text angezeigt werden sollen. Ähnliche Artikel werden nur angezeigt, wenn in dem Artikel Keywords hinterlegt wurden.");

define("_DOCS_PAGE_PRINT", "Drucken");
define("_DOCS_PAGE_PRINT_TEXT", "Wenn aktiviert wird dem User eine Drucksymbol angezeigt, über welches er eine druckoptimierte Darstellung des Dokumentes ansehen kann.");

define("_DOCS_CONF_INTRO", "Einleitung zum Modul");
define("_DOCS_CONF_INTRO_TEXT", "Hier kann eine Einleitung zum Modul eingetragen werden. Diese erscheint auf der Modulstartseite.");

define("_DOCS_CONF_LINK", "Konfiguration Verlinkung");
define("_DOCS_CONF_LINK_TEXT", "");
define("_DOCS_PAGE_VIEWBOOKLINK", "Artikel verlinken");
define("_DOCS_PAGE_VIEWBOOKLINK_TEXT", "Wenn aktiviert, alle Wörter, die anderen Artikeltiteln entsprechen, mit diesen Artikeln verlinkt.");
define("_DOCS_PAGE_VIEWBOOKBASE", "Aus allen Büchern verlinken");
define("_DOCS_PAGE_VIEWBOOKBASE_TEXT", "Wenn aktiviert werden für die o.g. Verlinkung alle Dokumente herangezogen. Wenn deaktiviert, nur das Dokument, in welchem sich der aktuelle Artikel befindet.");
define("_DOCS_PAGE_VIEWENCYLINKS", "Links zur Enzyklopädie erstellen");
define("_DOCS_PAGE_VIEWENCYLINKS_TEXT", "Wenn aktiviert, werden Begriffe aus der Enzyklopädie aus den Texten verlinkt.");
define("_DOCS_PAGE_INDEX_NEW", "Änderungen kennzeichnen");
define("_DOCS_PAGE_INDEX_NEW_TEXT", "Wenn aktiviert, werden im Inhaltsverzeichnis Neue und geänderte Artikel gekennzeichnet");
// define("_DOCS_STATUS","Zeitangaben verwenden");
// define("_DOCS_STATUS_TEXT","Wenn aktiviert, werden nachstehende Zeitangaben für die Veröffentlichung verwendet.");
// define("_DOCS_STARTTIME","Starttermin");
// define("_DOCS_STARTTIME_TEXT","Du kannst hier eine Startzeit für den Artikel angeben. Erst ab diesem Zeitpunkt wird der Artikel im Frontend angezeigt.");
// define("_DOCS_ENDTIME","Endtermin");
// define("_DOCS_ENDTIME_TEXT","Du kannst hier eine Endzeit für den Artikel angeben. Ab diesem Zeitpunkt wird der Artikel im Frontend nicht mehr angezeigt. Liegt der Endtermin vor dem Starttermin, wird der Artikel unbegrenz ab dem Starttermin angezeigt.");
define("_DOCS_VIEWTITLE", "Titel anzeigen");
define("_DOCS_VIEWTITLE_TEXT", "Soll der Modultitel auf der Artikelseite angezeigt werden");
define("_DOCS_VIEWVIEWS", "Zugriffe anzeigen");
define("_DOCS_VIEWVIEWS_TEXT", "");
define("_DOCS_LINKTITLE", "Titel verlinken");
define("_DOCS_LINKTITLE_TEXT", "Soll der Modultitel verlinkt werden ?");
define("_DOCS_VIEWSEARCH", "Suchfeld anzeigen");
define("_DOCS_VIEWSEARCH_TEXT", "Soll das Suchformular auf der Artikelseite angezeigt werden");

define("_DOCS_META_PAGE", "Metadatenoptionen");
define("_DOCS_META_PAGE_TEXT", "Hier können die Metadaten angepasst werden. Werden keine Einträge vorgenommen, so werden automatisch die Standardeinträge verwendet.");
define("_DOCS_META_CANONICAL", "Ursprungs URL");
define("_DOCS_META_CANONICAL_TEXT", "Hier die Ursprungs URL des Artikels angeben, um doppelten Inhalt zu vermeiden. Angabe inklusive 'http://'");
define("_DOCS_META_ROBOTS", "Robots");
define("_DOCS_META_ROBOTS_TEXT", "Robotsanweisung");
define("_DOCS_META_ALTERNATE", "zusätzliche Meta-Tags");
define("_DOCS_META_ALTERNATE_TEXT", "Hier können zusätzliche META-Tags für den Header angegeben werden. Die Angaben bitte komplett und kommagetrennt angeben OHNE die an- und abschließenden Klammern (z.Bsp. link rel=\"robots\" content=\"all\", meta name=\"author\".... )  beide Besipiele sind allerdings schon integriert.");
define("_DOCS_META_REVISIT", "Revisit");
define("_DOCS_META_REVISIT_TEXT", "anzahl Tage angeben.");
define("_DOCS_META_AUTHOR", "Author");
define("_DOCS_META_AUTHOR_TEXT", "gegebenenfalls abweichenden Autor angeben.");

/* tools */
define("_DOCS_TOOLS", "Erweiterte Funktionen");
define("_DOCS_TOOLS_TEXT", "");
define("_DOCS_TOOLS_IMPORT", "Import Funktionen");
define("_DOCS_TOOLS_IMPORT_TEXT", "Hier kann aus verschiedenen anderen Modulen der Content in Documents importiert werden. Die Daten werden aus den Tabellen kopiert, nicht gelöscht");
define("_DOCS_TOOLS_IMPORT_SELECT", "Modul auswählen");
define("_DOCS_TOOLS_IMPORT_SELECT_TEXT", "Hier auswählen, aus welchem Modul Daten importiert werden sollen. Es werden keine Inhalte doppelt importiert. Die Inhalte des Moduls werden in ein Document mit dem nachfolgenden Namen importiert.");
define("_DOCS_TOOLS_DB", "Datenbank Funktionen");
define("_DOCS_TOOLS_DB_TEXT", "Tools zur Pflege der Datenbank des Moduls");
define("_DOCS_TOOLS_IMPORT_DOC", "Import in Document");
define("_DOCS_TOOLS_IMPORT_DOC_TEXT", "Hier einen Namen für das Basisdokument eingeben, in welche die Inhalte importiert werden. Bleibt das Feld frei, wird der Modulnamen verwendet");

/* extendet settings */
define("_DOCS_EXTENDET_SETTINGS", "erweiterte Einstellungen");
/* sociol */
define("_DOCS_SOCIAL_INFO_FACEBOOK", "2 Klicks für mehr Datenschutz: Erst wenn Du hier klickst, wird der Button aktiv und du kannst deine Empfehlung an Facebook senden. Schon beim Aktivieren werden Daten an Dritte übertragen – siehe i.");
define("_DOCS_SOCIAL_INFO_TWITTER", "2 Klicks für mehr Datenschutz: Erst wenn Du hier klickst, wird der Button aktiv und du kannst deine Empfehlung an Twitter senden. Schon beim Aktivieren werden Daten an Dritte übertragen – siehe i.");
define("_DOCS_SOCIAL_INFO_GPLUS", "2 Klicks für mehr Datenschutz: Erst wenn Du hier klickst, wird der Button aktiv und du kannst deine Empfehlung an Google+ senden. Schon beim Aktivieren werden Daten an Dritte übertragen – siehe 'i'.");
define("_DOCS_SOCIAL_INFO_HELP", "Wenn Du diese Felder durch einen Klick aktivierst, werden Informationen an Facebook, Twitter oder Google in die USA übertragen und unter Umständen auch dort gespeichert.");
define("_DOCS_SOCIAL_INFO_TOOLS", "Dauerhaft aktivieren und Datenüber­tragung zustimmen:");

/* diverse */
define("_DOCS_START", "Start");
define("_DOCS_ADMIN_PANEEL", "Administration");
define("_DOCS_PREVIOUS", "zurück");
define("_DOCS_NEXT", "weiter");
define("_DOCS_MOVE", "verschieben");
define("_DOCS_FROM", "von");
define("_DOCS_ACTION", "Aktion");
define("_DOCS_HISTORY", "Historie");
define("_DOCS_LASTCHANGE", "letzte Änderung");
define("_DOCS_NEWCONTENT", "neue Artikel");
define("_DOCS_LASTCHANGES", "letzte Änderungen");
define("_DOCS_LINKS", "Verlinkung");
// define("_ATTACHMENTS","Anhänge");
// define("_READMORE","weiterlesen");
define("_DOCS_VIEW_INDEX", "Kategorieansicht");
define("_DOCS_VIEW_BLOG", "Blogansicht");
define("_DOCS_VIEW_LOG", "Änderungsprotokoll");

define("_DOCS_DOWNLOAD", "Download");
define("_DOCS_ATTACHMENT", "Dateianhänge");
define("_DOCS_PAGE_SIMILAR", "Diese Artikel könnte Dich auch interessieren:");

define("_DOCS_FILENAME", "Dateiname");
define("_DOCS_FILESIZE", "Größe");
define("_DOCS_FILETYPE", "MIME-Type");
define("_DOCS_FILETITLE", "Beschreibung");

define("_DOCS_URL", "Link zum Original-Artikel");
define("_DOCS_URL_TEXT", "Dieser Artikel kommt von:");
/* error */

define("_DOCS_NESTEDSET_ERROR", "Es wurden Fehler in der Datenbankstruktur des Moduls gefunden. Klicke auf 'Optionen' um die Datenbankwartung.");
define("_DOCS_NESTEDSET_IO", "Datenbankstruktur ohne Fehler.");
define("_DOCS_REPAIR", "Reparieren");
define("_DOCS_DB_REPAIR", "Datenstruktur reparieren ");
define("_DOCS_DB_REPAIR_TEXT", "Es wird die Datenstruktur für das Modul überprüft und bei Fehlern ein Fehler-Dokument angelegt.");
define("_DOCS_DB_DELLOG", "Logtabelle löschen ");
define("_DOCS_DB_DELLOG_TEXT", "Es werden ALLE Einträge für dieses Modul aus der Log-Tabelle gelöscht !!");
define("_DOCS_DB_DELLOG_ACTION", "Logtabelle wurde gelöscht!! ");
define("_DOCS_IMPORT_ACTION", " Datensätze wurden importiert. ");

/* --- */
define("_DOCS_CONF_LINKOTHER", "in anderen Modulen verlinken");
define("_DOCS_CONF_LINKOTHER_TEXT", "Sollen die Artikel in anderen Modulen verlinkt werden ?");
define("_DOCS_LINK_ALL", "jedes Vorkommen");
define("_DOCS_LINK_FIRST", "nur das erste Vorkommen");
define("_DOCS_CONF_LINKCOUNT", "Anzahl der Verlinkungen");
define("_DOCS_CONF_LINKCOUNT_TEXT", "Wie oft soll im Content der Artikel verlinkt werden?");
define("_DOCS_PAGE_VIEWMODULELINK", "Dürfen andere Modulen verlinken");
define("_DOCS_PAGE_VIEWMODULELINK_TEXT", "Dürfen andere Module in diesem Modul Begriffe verlinken ?");

define("_DOCS_NEW2", "Neu");
define("_DOCS_DEFAULT", "Standard");
define("_DOCS_UPDATE", "Update");
define("_DOCS_VIEW_LIST", "Liste");
define("_DOCS_CONF_TABCOUNT_LIST_TEXT", "Anzahl der Spalten - gilt nur für Listenansicht");
// define("_DOCS_NEW_DOCUMENTS","Neue(s) Dokument(e)");
define("_DOCS_CONF_INSERTFIRST", "Einfügereihenfolge");
define("_DOCS_CONF_INSERTFIRST_TEXT", "auswählen an welcher Stelle neue Dokumente in das übergeordnete Dokument eingefügt werden");
define("_DOCS_INSERTFIRST", "am Anfang");
define("_DOCS_INSERTLAST", "am Ende");

define("_DOCS_RATE_BAD", "miserabel");
define("_DOCS_RATE_POOR", "schlecht");
define("_DOCS_RATE_REGULAR", "ok");
define("_DOCS_RATE_GOOD", "gut");
define("_DOCS_RATE_GORGEOUS", "Sehr Gut");
define("_DOCS_RATE_CANCEL", "meine Eingabe löschen");
define("_DOCS_SELECT_ICON", "Hier kannst du ein Icon auswählen, welches im Titel angezeigt wird auf der Modulstartseite");

define("_DOCS_ERR_FILESIZE", "Datei zu groß");
define("_DOCS_INFO_FILESIZE", "Maximale Dateigröße [kByte] :");

/* sendfriend */
define("_DOCS_RECYOURNAME", "Dein Name:");
define("_DOCS_RECYOUREMAIL", "Deine E-mail:");
define("_DOCS_RECFRIENDNAME", "Name des Freundes:");
define("_DOCS_RECFRIENDEMAIL", "E-mail-Adresse des Freundes:");
define("_DOCS_RECREMARKS", "Persönliche Ergänzungen dazu:");
define("_DOCS_RECYOURFRIEND", "Dein Freund");
define("_DOCS_RECINTSITE", "Interessanter Artikel:");
define("_DOCS_RECOURSITE", "fand den Artikel");
define("_DOCS_RECINTSENT", "interessant und wollte ihn Dir empfehlen.");
define("_DOCS_RECSITENAME", "Artikel Vorschau:");
define("_DOCS_RECSITEURL", "URL der Website:");
define("_DOCS_RECTHANKS", "Vielen Dank für Deine Weiterempfehlung!");
define("_DOCS_RECERRORTITLE", "Email nicht verschickt, folgender Fehler ist aufgetreten:");
define("_DOCS_RECERRORNAME", "Bitte gib deinen Namen ein.");
define("_DOCS_RECERRORRECEIVER", "Die Empfänger Email-Adresse ist ungültig.");
define("_DOCS_RECERRORSENDER", "Ihre Absender Email-Adresse ist ungültig.");

define("_DOCS_PAGE_SENDFRIEND", "Senden anzeigen");
define("_DOCS_PAGE_SENDFRIEND_TEXT", "Button 'Artikel an Freund senden' anzeigen");

define("_DOCS_STARTPAGE", "Startseite");
define("_DOCS_STARTPAGE_TEXT", "Wenn aktiviert wird dieser Artikel auf im Block 'Startseite' angezeigt");
define("_DOCS_STARTPAGE_OFF", "von Startseite entfernen");
define("_DOCS_STARTPAGE_ON", "auf Startseite anzeigen");

define("_DOCS_NONE", "keine");
define("_DOCS_PAGE_ALPHA","Alphabetischen Index anzeigen");
define("_DOCS_PAGE_ALPHA_TEXT","wenn aktiviert, wird ein Alphabetischer index über dem Artikel angezeigt");
define("_DOCS_ALPHA_INDEX","Alphabetischer Index");

define("_DOCS_FILTER","Filter");
define("_DOCS_CONF_BLOCKS","Blockmenus");
define("_DOCS_CONF_BLOCKS_TEXT","Hier kann der moduleigene Menublock eingestellt werden.");
define("_DOCS_CONF_MENUWIDTH","Tiefe Menus");
define("_DOCS_CONF_MENUWIDTH_TEXT","Hier angeben, mit welcher Tiefe das Blockmenu angegeben werden soll.");
define("_DOCS_CONF_MENUCONTENT","Inhalte auswählen");
define("_DOCS_CONF_MENUCONTENT_TEXT","Hier auswählen, welche Dokumente im Menublock angezeigt werden sollen.");

define("_DOCS_PAGE_TITLE","Seitentitel");
define("_DOCS_PAGE_TITLE_TEXT","Abweichenden Seitentitel angeben");
define("_DOCS_UPDATE_DB","Update Datenbank wurde durchgeführt");
define("_DOCS_UPDATE_DB_TXT","Datensätze wurden geprüft/angepasst");

?>