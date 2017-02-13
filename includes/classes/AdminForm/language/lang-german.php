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
 * $Revision: 214 $
 * $Author: PragmaMx $
 * $Date: 2016-09-15 15:51:34 +0200 (Do, 15. Sep 2016) $
 *
 * @package pragmaMx
 */

defined('mxMainFileLoaded') or die('access denied');

$toolbarlangarray = array('_NOACTION' => 'Bitte treffen Sie erst eine Auswahl !',
    '_EXPANDALL' => 'alle Öffnen',
    '_COLLAPSEALL' => 'alle Schließen',
    '_ADD' => 'Hinzufügen',
    '_ACCEPT' => 'Übernehmen',
    '_BACK' => 'zurück',
    '_CANCEL' => 'Abbrechen',
    '_CATEGORYS' => 'Kategorien',
    '_COLOR' => 'Farben',
    '_COMMENTS' => 'Kommentar',
    '_CONFIG' => 'Einstellungen',
    '_CONTENT' => 'Inhalt',
    '_COPY' => 'Kopieren',
    '_CPANEL' => 'Admin Menue',
    '_DELETE' => 'Löschen',
    '_DOWN' => 'unten',
    '_DOWNLOAD' => 'Download',
    '_EDIT' => 'Ändern',
    '_FOLDER' => 'Ordner',
    '_HELP' => 'Hilfe',
    '_HOME' => 'Home',
    '_IMAGE' => 'Bilder',
    '_LINK' => 'Link',
    '_MAIL' => 'Email',
    '_MOVE' => 'Verschieben',
    '_NEW' => 'Neu',
    '_NEWS' => 'Artikel',
    '_NEXT' => 'Weiter',
    '_PLUS' => 'Hinzufügen',
    '_PREVIEW' => 'Vorschau',
    '_PUBLISH' => 'Veröffentlichen',
    '_REDIRECT' => 'Weiterleiten',
    '_REFRESH' => 'Aktualisieren',
    '_SAVE' => 'Speichern',
    '_SETTINGS' => 'Einstellungen',
    '_TOOLS' => 'Optionen',
    '_TRASH' => 'Papierkorb',
    '_UNPUBLISH' => 'Sperren',
    '_UP' => 'oben',
    '_UPLOAD' => 'Hochladen',
    '_USER' => 'User',
    '_VOTE' => 'Bewerten',
    '_ZOOM' => 'Zoom',
    '_SELECTTIME' => 'Eingabe Zeit',
    '_DEFAULT' => 'Standard',
	'_HTML_EDIT' => 'HTML bearbeiten',
	'_CSS_EDIT' => 'CSS bearbeiten',
	'_WRITABLE' => 'beschreibbar',
	'_NOWRITABLE' =>'nicht beschreibbar',
	'_ARCHIVE'=>'Archiv',
	'_EXPORT'=>"Export",
	'_IMPORT'=>"Import",
	
    );

foreach ($toolbarlangarray as $constant => $value) {
    defined($constant) OR define($constant, $value);
}

?>