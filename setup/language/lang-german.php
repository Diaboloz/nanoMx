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
 * $Revision: 229 $
 * $Author: PragmaMx $
 * $Date: 2016-09-29 07:44:51 +0200 (Do, 29. Sep 2016) $
 *
 * german language file by:
 * pragmaMx Developer Team
 */

defined('mxMainFileLoaded') or die('access denied');

/* Datestrings und locale Einstellungen: */
$old_setlocale = setlocale(LC_TIME, 0);
$locale = array("de_DE.UTF-8", "de_DE.UTF8", "de_DE", "ge", "deu", "german", "DE", "DEU", "276", "CTRY_GERMANY", "de_DE.ISO-8859-15");
define("_SETLOCALE", setlocale(LC_TIME, $locale));
setlocale(LC_TIME, $old_setlocale);
define("_DOC_LANGUAGE", "de");
define("_DOC_DIRECTION", "ltr");
define('_DATESTRING', '%d.%m.%Y');

/**
 * Setup Optionen zur Auswahl, siehe setup-settings.php
 */
// Neuinstallation
define('_SETUPOPTION_NEW', 'Neuinstallation');
define('_SETUPOPTION_NEW_DESC', 'pragmaMx wird neu installiert. Eventuell bestehende Daten in der Datenbank bleiben unverändert erhalten.');
// Update
define('_SETUPOPTION_UPDATE', 'Update einer bestehenden Installation');
define('_SETUPOPTION_UPDATE_DESC', 'Das Setupscript versucht eine bereits bestehende Installation von pragmaMx zu aktualisieren. Es eignet sich auch zur Konvertierung von Daten aus phpNuke, vkpMx und verschiedenen anderen phpNuke-Clones.');
// Setupschritte
define('_STEP_SELECT', 'Auswahl, Setup oder Update');
define('_STEP_ISINCORRECT', 'Sicherheitsabfrage');
define('_STEP_LICENSE', 'Lizenzbestimmungen');
define('_STEP_BACKUP', 'Backup der Datenbank');
define('_STEP_UPDATE', 'Update der Datenbank');
define('_STEP_DELFILES', 'unnötige Dateien entfernen');
define('_STEP_FINISHEDINSTALL', 'Installation beendet');
define('_STEP_DBSETTINGS', 'Datenbankzugriff konfigurieren');
define('_STEP_DBSETTINGSCREATE', 'Datenbankzugriff konfigurieren / Datenbank erstellen');
define('_STEP_MORESETTINGS', 'Weitere Einstellungen');
define('_STEP_MORESETTINGSCHECK', 'Einstellungen überprüfen');
define('_STEP_FINISHEDUPDATE', 'Update beendet');
define('_STEP_CONFIGURATION', 'Konfiguration aktualisieren');
define('_HELLOINSTALL', 'Installation <span class="label">' . MX_SETUP_VERSION .'</span>');
define('_HELLOINSTALL2', 'Wir freuen uns, daß Sie sich für unser ' . preg_replace('#[[:space:]]#', '&nbsp;', MX_SETUP_VERSION) . ' entschieden haben.<br /> Bevor Sie mit der Installation beginnen, sollten Sie unbedingt die <a href="' . _MXDOKUSITE . '">Online-Dokumentation</a> lesen.<br /> Sollten Sie dieses bereits getan haben, dann können Sie nun mit der Installation fortfahren.');
define('_WHATWILLYOUDO', 'Sie haben die Möglichkeit, zwischen verschiedenen Setup-Methoden zu wählen. Die vom Setup-Script empfohlene Methode ist bereits aktiviert. Wählen Sie bitte nur eine andere Methode, wenn Sie sich sicher sind.<br /><br />Was möchten Sie tun?');
define('_OLDVERSION_ERR1', 'Ihre gewählte Setup-Option entspricht nicht der ermittelten Standardvorgabe,<br />dies kann zu Problemen oder sogar Datenverlust führen.');
define('_OLDVERSION_ERR2', 'Sind Sie sicher, daß Sie die Option &quot;<em>' . @constant($GLOBALS['opt'][@$_REQUEST['setupoption']]['name']) . '</em>&quot; durchführen wollen?');
define('_CONFIGSAVEMESS', 'Zum Abschluss wird noch die Konfigurationsdatei <em>' . basename(FILE_CONFIG_ROOT) . '</em> aktualisiert.');
define('_CONFIG_OK_NEW', 'Die Konfigurationsdatei <em>' . basename(FILE_CONFIG_ROOT) . '</em> wurde erfolgreich erstellt.');
define('_CONFIG_OK_OLD', 'Die Konfigurationsdatei <em>' . basename(FILE_CONFIG_ROOT) . '</em> war bereits auf dem aktuellen Stand und in Ordnung.');
define('_CONFIG_ERR_1', 'Die Datei <em>' . basename(FILE_CONFIG_ROOT) . '</em> ist schreibgeschützt, bitte ändern sie die Dateiberechtigungen (chmod).');
define('_CONFIG_ERR_2', 'Das Schreiben in die Datei <em>' . basename(FILE_CONFIG_ROOT) . '</em> war nicht erfolgreich.');
define('_CONFIG_ERR_3', 'Das Verzeichnis <em>' . PMX_BASE_PATH . '</em> ist schreibgeschützt, bitte ändern sie die Dateiberechtigungen.');
define('_CONFIG_ERR_4', 'Die Datei <em>' . basename(FILE_CONFIG_ROOT) . '</em> konnte aus undefinierten Gründen nicht erstellt werden.');
define('_CONFIG_ERR_5', 'Die Datei in <em>' . basename(FILE_CONFIG_ROOT) . '</em> ist vorhanden, aber kann nicht gelesen werden.');
define('_CONFIG_ERR_6', 'Die Datei in <em>' . basename(FILE_CONFIG_ROOT) . '</em> ist vorhanden, aber die Daten wurden nicht korrekt geschrieben.');
define('_CONFIG_ERR_8', 'Die Konfigurationsdatei <em>' . basename(FILE_CONFIG_ROOT) . '</em> wurde zwar nicht korrekt erstellt, aber die Datenbankverbindung ist ok. Sie können mit der Installation fortfahren, sollten aber danach unbedingt die Systemeinstellungen im Administrationsmenü überprüfen und ggf. neu einstellen.');
define('_CONFIG_BACK', 'Eine Kopie der bestehenden Konfigurationsdatei wurde, unter folgendem Namen, erfolgreich erstellt:');
define('_CONFIG_CREATE', 'Bitte erstellen Sie anhand des angezeigten PHP-Codes eine neue script-Datei. Diese Datei nennen Sie <em>' . basename(FILE_CONFIG_ROOT) . '</em> und kopieren sie in das Hauptverzeichnis (' . dirname(basename(FILE_CONFIG_ROOT)) . ') Ihrer pragmaMx Installation.<br />Bitte achten sie darauf, daß wirklich der komplette Quelltext 1:1 in dieser Datei gespeichert wird.<br /><br />Nachdem Sie dies getan haben, können Sie mit der Installation fortfahren.');
define('_CONFIG_BUTTONMAN', 'Konfigurationsdatei manuell erstellen');
define('_CURRENTSTATUS', 'Bisheriger Installationsstatus');
define('_THEREERROR', 'Es sind Fehler aufgetreten');
define('_WILL_CREATE_TABLES', 'Im nächsten Schritt werden die Tabellen erstellt und aktualisiert, dies kann eine ganze Weile dauern!');
define('_WILL_CREATE_BACKUP', 'Wenn Sie die Backup-Option benutzen, wird vor dem Erstellen der Tabellen versucht, ein komplettes Backup der gewählten Datenbank anzulegen.');
define('_CONTINUE_WITHOUTDBBACKUP', 'Weiter ohne Backup');
define('_CONTINUE_WITHDBBACKUP', 'Weiter mit Backup');
define('_DBFOLLOWERRORS', 'Es sind folgende Fehler aufgetreten');
define('_NODBERRORS', 'Es sind keine Fehler aufgetreten.');
define('_DBNOTEXIST', 'Die angegebene Datenbank existiert nicht auf dem Server.');
define('_DBNOTSELECT', 'Sie haben keine Datenbank angegeben.');
define('_DBNOACCESS', 'Der Zugriff auf die Datenbank wurde verweigert.');
define('_DBOTHERERR', 'Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten.');
define('_DBVERSIONFALSE', 'Sorry, aber ihre mySQL-Version ist zu alt. Um pragmaMx zu installieren, wird mindestens Version %s des MySQL-Servers benötigt.');
define('_NOT_CONNECT', 'Es besteht keine Verbindung zum Datenbank-Server, oder die Zugangsdaten sind nicht korrekt.');
define('_NOT_CONNECTMORE', 'Bitte stellen sie sicher, daß die config.php Ihrer Vorversion vorhanden ist und daß die dort angegebenen Datenbankzugangsdaten korrekt sind.');
define('_DB_CONNECTSUCCESS', 'Die Verbindung zur Datenbank <em>%s</em> wurde erfolgreich hergestellt.');
define('_CORRECTION', 'Korrektur');
define('_REMAKE', 'Wiederholen');
define('_IGNORE', 'Ignorieren und Weiter');
define('_DONOTHING', 'Übergehen');
define('_DBARETABLES', '<li>In der gewählten Datenbank befinden sich bereits Tabellen.</li><li>Ein Backup der Datenbank wird empfohlen.</li>');
define('_DBARENOTABLES', '<li>Da die gewählte Datenbank leer ist, braucht kein Backup durchgeführt werden.</li>');
define('_SUBMIT', 'Weiter');
define('_OR', 'Oder');
define('_YES', 'Ja');
define('_NO', 'Nein');
define('_GOBACK', 'Zurück');
define('_CANCEL', 'Abbrechen');
define('_FILE_NOT_FOUND', 'Datei nicht gefunden, oder nicht lesbar.');
define('_ACCEPT', 'Akzeptieren Sie die Lizenzvereinbarungen?');
define('_START', 'Startseite');
define('_INTRANETWARNING', 'Die Intranet Option sollte nur dann gewählt werden, wenn die Website nicht durch einen vollquallifizierten Internetnamen aufgerufen werden kann. Die Option ist nur dann zu empfehlen, wenn die Website sich hinter einer Firewall befindet oder der Computer nicht mit dem Internet verbunden ist. <br> z.B. eine lokale Testumgebung via Xampp (localhost)');
define('_PRERR11', 'Die beiden Präfixe müssen mit einem Kleinbuchstaben beginnen, dürfen nur Zahlen, Kleinbuchstaben und den Unterstrich (_) enthalten und sollten eine Gesamtlänge von ' . PREFIX_MAXLENGTH . ' Zeichen nicht überschreiten.');
define('_PRERR12', 'Der neue Präfix hat keinen Wert.<br />Bitte geben sie einen Präfix an.');
define('_PRERR13', 'Der neue Präfix entspricht der Standardvorgabe von pragmaMx oder von phpNuke. Bitte verwenden sie aus Sicherheitsgründen einen anderen Präfix.');
define('_PRERR14', 'Im Präfix sind nicht erlaubte Zeichen enthalten. Erlaubt sind nur Kleinbuchstaben, Zahlen und der Unterstrich (_), wobei der Präfix nicht mit einer Zahl beginnen darf.<br />Bitte verwenden sie einen anderen Präfix.');
define('_PRERR15', 'Der Präfix darf nicht mit einer Zahl beginnen.<br />Bitte verwenden sie einen anderen Präfix.');
define('_PRERR16', 'Der neue Präfix ist zu lang, die Präfixe dürfen höchstens ' . PREFIX_MAXLENGTH . ' Zeichen lang sein.<br />Bitte verwenden sie kürzere Präfixe.');
define('_PRERR17', 'Es sind bereits %d Tabellen mit dem neuen Präfix vorhanden.<br />Bitte verwenden sie einen anderen Präfix.');
define('_PRERR18', 'Der neue User-Präfix hat keinen Wert.<br />Bitte geben sie einen User-Präfix an.');
define('_PRERR19', 'Im User-Präfix sind nicht erlaubte Zeichen enthalten. Erlaubt sind nur Kleinbuchstaben, Zahlen und der Unterstrich (_), wobei der User-Präfix nicht mit einer Zahl beginnen darf.<br />Bitte verwenden sie einen anderen User-Präfix.');
define('_PRERR20', 'Der User-Präfix darf nicht mit einer Zahl beginnen.<br />Bitte verwenden sie einen anderen User-Präfix.');
define('_PRERR21', 'Der neue User-Präfix ist zu lang, die Präfixe dürfen höchstens ' . PREFIX_MAXLENGTH . ' Zeichen lang sein.<br />Bitte verwenden sie kürzere Präfixe.');
define('_PRERR22', 'Es ist bereits eine User-Tabelle mit dem neuen User-Präfix vorhanden.<br />Bitte verwenden sie einen anderen User-Präfix');
define('_SUPPORTINFO', 'Support für Ihr System erhalten sie hier: <a href="' . _MXSUPPORTSITE . '">' . _MXSUPPORTSITE . '</a>');
define('_DOKUINFO', 'Die Online-Dokumentation finden sie hier: <a href="' . _MXDOKUSITE . '">' . _MXDOKUSITE . '</a>');
define('_NOBACKUPCREATED', 'Ein Datenbank-Backup wurde nicht angelegt.');
define('_HAVE_CREATE_DBBACKUP', 'Die Datenbank wurde gesichert als Datei:');
define('_HAVE_CREATE_BACKUPERR_1', 'Das Backup der Datenbank ist fehlgeschlagen.');
define('_HAVE_CREATE_BACKUPERR_2', 'Falls diese Datenbank Daten enthält stellen sie bitte sicher, daß Sie eine aktuelle Sicherung haben BEVOR Sie weiter machen!');
define('_SETUPHAPPY1', 'Herzlichen Glückwunsch');
define('_SETUPHAPPY2', 'Ihr System ist jetzt komplett installiert. Mit dem nächsten Klick kommen Sie direkt in das Administrationsmenü.');
define('_SETUPHAPPY3', 'Hier sollten sie zunächst die Grundeinstellungen überprüfen und neu abspeichern.');
define('_DELETE_FILES', 'Wenn Ihr System ordnungsgemäß läuft, löschen Sie bitte den Ordner &quot;<em>' . basename(dirname(__DIR__)) . '</em>&quot; in Ihrem Webroot.<br /><strong>Das verbleiben kann ein Sicherheitsrisiko darstellen!</strong>');
define('_GET_SQLHINTS', 'Hier sehen Sie die SQL-Befehle die während der Konvertierung/Erstellung ausgeführt wurden');
define('_DATABASEISCURRENT', 'Die Datenbankstruktur war bereits auf dem aktuellen Stand. Änderungen waren nicht nötig.');
define('_SEEALL', 'komplett ansehen');
define('_DB_UPDATEREADY', 'Die Tabellen-Konvertierung/Erstellung ist abgeschlossen.');
define('_DB_UPDATEFAIL', 'Die Tabellen-Konvertierung/Erstellung konnte nicht komplett ausgeführt werden.');
define('_DB_UPDATEFAIL2', 'Es fehlen folgende wichtige Systemtabellen: ');
define('_BACKUPPLEASEDOIT', 'Es wird empfohlen, vor der Aktualisierung ein Komplett-Backup der Datenbank zu erstellen.');
define('_ERRMSG1A', 'Fehler: eine Update-Datei fehlt, bitte stellen sie sicher, daß die folgende Datei vorhanden ist:');
define('_YEAHREADY2', 'Ansonsten ist Ihr pragmaMx jetzt auf dem neusten Stand.');
define('_SERVERMESSAGE', 'Servermeldung');
define('_ERRDBSYSFILENOFILES', 'Keine der Systemtabellen konnte überprüft/erstellt werden, im Ordner <em>' . PATH_SYSTABLES . '</em> befinden sich keine Definitionsdateien.');
define('_ERRDBSYSFILEMISSFILES_1', 'Es können nicht alle Systemtabellen überprüft/erstellt werden.');
define('_ERRDBSYSFILEMISSFILES_2', 'Im Ordner <em>' . PATH_SYSTABLES . '</em> fehlen folgende Definitionsdateien');
define('_THESYSTABLES_1', 'Die Systemtabelle(n) <strong>%s</strong> konnte nicht überprüft/erstellt werden, weil die entsprechende(n) Dateien aus dem Ordner ' . PATH_SYSTABLES . ' nicht geladen werden konnte.');
define('_THESYSTABLES_2', 'Die Systemtabelle(n) <strong>%s</strong> wurde(n) nicht überprüft/erstellt.');
define('_SYSTABLECREATED', 'Es wurden %d Systemtabellen überprüft/erstellt.');
define('_MODTABLESCREATED', 'Es wurden die Tabellen von %d Modulen überprüft/erstellt.');
define('_NOMODTABLES', 'Es wurden keine Modultabellen überprüft/erstellt.');
define('_STAT_THEREWAS', 'Es wurden');
define('_STAT_TABLES_CREATED', 'Tabellen erstellt.');
define('_STAT_TABLES_RENAMED', 'Tabellen umbenannt.');
define('_STAT_TABLES_CHANGED', 'Tabellen geändert.');
define('_STAT_DATAROWS_CREATED', 'Datensätze eingefügt/geändert.');
define('_STAT_DATAROWS_DELETED', 'Datensätze gelöscht.');
define('_MOREDEFFILEMISSING', 'Die Datei (<em>' . @ADD_QUERIESFILE . '</em>) mit zusätzlichen SQL-Befehlen fehlt!');
define('_SETUPMODNOTFOUND1', 'Das gewählte Setup-Modul <strong>%s</strong> ist nicht verfügbar!');
define('_ERROR', 'Fehler');
define('_ERROR_FATAL', 'schwerer Fehler');
define('_SETUPCANCELED', 'Setup abgebrochen!');
define('_GOTOADMIN', 'weiter zum Administrationsmenü');
define('_DBSETTINGS', 'Geben sie hier die Daten für den Datenbankzugriff ein. Das Setup kann nur mit einer korrekt eingerichteten Datenbankverbindung fortgesetzt werden. Die Zugangsdaten erhalten Sie normalerweise von Ihrem Webspace-Provider.');
define('_DBNAME', 'Name der Datenbank');
define('_DBPASS', 'Password der Datenbank');
define('_DBSERVER', 'Datenbank Server');
define('_DBTYP', 'Datenbanktyp');
define('_DBUSERNAME', 'User der Datenbank');
define('_DBCREATEQUEST', 'Wollen Sie versuchen die Datenbank &quot;<em>' . @$_REQUEST['dbname'] . '</em>&quot; zu erstellen?');
define('_DBISCREATED', 'Die Datenbank &quot;<em>' . @$_REQUEST['dbname'] . '</em>&quot; wurde erfolgreich angelegt.');
define('_DBNOTCREATED', 'Es ist ein Fehler beim Anlegen der Datenbank &quot;<em>' . @$_REQUEST['dbname'] . '</em>&quot; aufgetreten.');
define('_PREFIXSETTING', 'Die Präfixe dienen zur Unterscheidung der jeweiligen Tabellen, wenn Sie mehrere pragmaMx in einer Datenbank betreiben wollen. Der User-Präfix ermöglicht die gemeinsame Nutzung von Benutzerdaten innerhalb mehrerer pragmaMx. Ansonsten sollte der User-Präfix dem normalen Präfix entsprechen.');
define('_PREFIX', 'Präfix der Datenbanktabellen');
define('_USERPREFIX', 'Präfix der Usertabelle');
define('_DEFAULTLANG', 'Standardsprache');
define('_INTRANETOPT', 'Intranet Umgebung (z.B.&nbsp;Localhost)');
define('_ADMINEMAIL', 'Administrator- Email');
define('_SITENAME', 'Name der Site');
define('_STARTDATE', 'Startdatum der Seite');
define('_CHECKSETTINGS', 'Überprüfen Sie bitte Ihre Einstellungen!');
define('_PLEASECHECKSETTINGS', 'Bitte überprüfen Sie die bisherigen Einstellungen.<br />Wenn alle Daten korrekt sind, können Sie die Installation fortsetzen.<br />Ansonsten haben Sie noch Gelegenheit die Angaben zu korrigieren.');
define('_HAVE_CREATE_TABLES', 'Tabellen wurden erstellt.');
define('_HAVE_CREATE_TABLES_7', 'Die Tabellen, die für das reine System benötigt werden, wurden fehlerfrei erstellt. Das Setup kann fortgesetzt werden, aber bei verschiedenen Funktionen des Systems kann es zu Fehlern kommen.');
define('_HAVECREATE_TABLES_ERR', 'Die Datenbank konnte nicht vollständig angelegt werden. Die Installation ist fehlgeschlagen.');
define('_CREATE_DB', 'Datenbank erstellen');
define('_DELETESETUPDIR', 'Hier klicken, um diese Setuproutine unbrauchbar zu machen. Dazu wird die index.php umbenannt und der Ordnerzugriff per .htaccess Datei verhindert. <em>(funktioniert nicht auf allen Servern)</em>');
// add for fieldset
define('_PREFIXE', 'Präfix');
define('_SITE__MORESETTINGS', 'Website Einstellungen');
define('_SERVER', 'Server Daten');
define('_BACKUPBESHURE', 'Stellen Sie vor der nun folgenden Anpassung der Datenbanktabellen bitte sicher, daß Sie eine aktuelle Sicherung der Datenbank haben.');
define('_BACKUPBESHUREYES', 'Ja, ich habe eine aktuelle Datenbanksicherung.');
define('_BACKUPBESHUREOK', 'Bestätigen Sie bitte, daß Sie eine aktuelle Datenbanksicherung haben.');
// Modulbezeichnungen
define('Your_Account', 'Account');
define('News', 'Artikel');
define('blank_Home', 'Home');
define('Content', 'Inhalte');
define('Downloads', 'Downloads');
define('eBoard', 'Forum');
define('FAQ', 'FAQ');
define('Feedback', 'Feedback');
define('Guestbook', 'Gästebuch');
define('Impressum', 'Impressum');
define('Kalender', 'Termine');
define('Statistics', 'Statistik');
define('Members_List', 'Benutzerliste');
define('My_eGallery', 'Mediengalerie');
define('Newsletter', 'Newsletter');
define('Private_Messages', 'Private Nachrichten');
define('Recommend_Us', 'Uns empfehlen');
define('Reviews', 'Testberichte');
define('Search', 'Suche');
define('Sections', 'Themen Bereiche');
define('Siteupdate', 'Site News');
define('Submit_News', 'Artikel schreiben');
define('Surveys', 'Umfragen');
define('Top', 'Topliste');
define('Topics', 'Themen');
define('UserGuest', 'Benutzer Gästebuch');
define('Web_Links', 'Links');
define('Web_News', 'Internet News');
define('LinkMe', 'Link zu uns');
define('Userinfo', 'Benutzerinfo');
define('User_Registration', 'Benutzer Anmeldung');
define('Gallery', 'Bildergalerie');
define('Avatar', 'Avatar');
define('Banners', 'Banner');
define('Encyclopedia', 'Enzyklopädie');
define('IcqList', 'Icq-Liste');
define('IrcChat', 'Chat');
define('Members_Web_Mail', 'Web-Mail');
define('Stories_Archive', 'Artikel Archiv');
define('Themetest', 'Themes');
define('User_Blocks', 'Blöcke');
define('User_Fotoalbum', 'Fotoalbum');
define('legal', 'Nutzungsbedingungen');
// die Nachricht für den Begrüssungsblock
define('_NEWINSTALLMESSAGEBLOCKTITLE', 'Willkommen bei Ihrem pragmaMx ' . MX_SETUP_VERSION_NUM . '');
define('_NEWINSTALLMESSAGEBLOCK', trim(addslashes('
<p>Guten Tag,</p>
<p>wenn Sie diese Mitteilung lesen können, scheint Ihr pragmaMx ohne Fehler zu arbeiten. Herzlichen Glückwunsch.</p>
<p>Wir möchten uns an dieser Stelle recht herzlich dafür bedanken, daß Sie sich dazu entschieden haben unser pragmaMx etwas genauer in Augenschein zu nehmen. Wir hoffen, daß es all Ihren Erwartungen gerecht wird.</p>
<p>Zusätzliche Erweiterungen für Ihr System erhalten Sie selbstverständlich auch auf unserer Homepage: <a href="http://www.pragmamx.org">http://pragmamx.org</a>.</p>
<p>Sollten Sie nicht direkt nach dem Setup einen Administrator-Account für Ihr pragmaMx angelegt haben, so holen Sie dies bitte <a href="' . adminUrl() . '"><strong>jetzt hier nach</strong></a>.</p>
<p>Wir wünschen Ihnen viel Freude bei der Erkundung Ihres Systems. Damit Sie sich etwas leichter zurecht finden haben wir im Administrator-Bereich eine kurze Dokumentation des Systems hinterlegt, bitte schauen Sie auf jeden Fall einmal hinein.</p>
<p>Ihr pragmaMx Coding-Team</p>
')));
define('_DBUP_WAIT', 'Bitte Warten');
define('_DBUP_MESSAGE', '
<p>Setup konfiguriert nun Ihr pragmaMx-System. </p>
<p>Die Anpassung der Datenbanktabellen kann eine ganze Weile dauern, bitte warten Sie, bis der Vorgang abgeschlossen ist. Beenden oder aktualisieren Sie die Seite nicht und schließen Sie auch nicht den Browser.</p>
');

// Blockbeschriftungen:
define('_BLOCK_CAPTION_MAINMENU', 'Hauptmenü');
define('_BLOCK_CAPTION_INTERNAL', 'Internes');
define('_BLOCK_CAPTION_COMMUNITY', 'Community');
define('_BLOCK_CAPTION_OTHER', 'Sonstiges');
define('_BLOCK_CAPTION_1', 'Setup-Alarm');
define('_BLOCK_CAPTION_2', 'Admin Menü');
define('_BLOCK_CAPTION_3', 'Sprache');
define('_BLOCK_CAPTION_4', 'Login');
define('_BLOCK_CAPTION_5', 'Benutzer Menü');
define('_BLOCK_CAPTION_6', 'Wer ist Online');
define('_BLOCK_CAPTION_7', 'FAQs');
define('_BLOCK_CAPTION_8', 'Umfrage');
define('_BLOCK_CAPTION_9', 'pragmaMx-News');
define('_BLOCK_CAPTION_5A', 'Das ist Ihr persönliches Menü');

/* Umgebungstest, äquivalent zu pmx_check.php */
define("_TITLE", " " . MX_SETUP_VERSION . "  environment test");
define("_ENVTEST", "Installations Umgebungs Test");
define("_SELECTLANG", "Bitte wählen Sie eine Sprache");
define("_TEST_ISOK", "OK, auf diesem System sollte  " . MX_SETUP_VERSION . "  korrekt laufen.");
define("_TEST_ISNOTOK", "Dieses System erfüllt nicht die Mindestvoraussetzungen zum Betrieb von  " . MX_SETUP_VERSION . " .");
define("_LEGEND", "Legende");
define("_LEGEND_OK", "<span>OK</span> - Alles OK");
define("_LEGEND_WARN", "<span>Warnung</span> - Ohne dieses Feature können einige Funktionen von  " . MX_SETUP_VERSION . "  nicht genutzt werden.");
define("_LEGEND_ERR", "<span>error</span> - Dieses Feature wird von " . MX_SETUP_VERSION . "  unbedingt benötigt.");
define("_ENVTEST_PHPFAIL", "Die von  " . MX_SETUP_VERSION . "  benötigte, minimale PHP Version ist %s. Ihre PHP Version ist: %s");
define("_ENVTEST_PHPOK", "Ihre PHP Version ist: %s");
define("_ENVTEST_MEMOK", "Ihr PHP Speicher Limit ist: %s");
define("_ENVTEST_MEMFAIL", "Ihr PHP Speicher Limit ist zu gering um  " . MX_SETUP_VERSION . "  zu installieren. Der minimale Wert ist %s.");
define("_EXTTEST_REQFOUND", "Die benötige Erweiterung '%s' ist vorhanden");
define("_EXTTEST_REQFAIL", "Die Erweiterung '%s' wird benötigt um  " . MX_SETUP_VERSION . "  zu betreiben.");
define("_EXTTEST_GD", "GD wird für die Bildbearbeitung verwendet. Ohne diese Erweiterung, ist das System nicht in der Lage, um z.B. Miniaturansichten von Bild-Dateien zu erstellen.");
define("_EXTTEST_MB", "Multibyte-String wird für den Umgang mit Unicode Zeichen verwendet. Ohne diese Erweiterung kann es evtl. zu Anzeigefehlern bei bestimmten Sonderzeichen kommen.");
// define("_EXTTEST_ICONV", "Iconv wird teilweise zur Zeichensatz-Konvertierung verwendet.  Ohne diese Erweiterung kann es evtl. zu Anzeigefehlern bei bestimmten Sonderzeichen kommen.");
define("_EXTTEST_IMAP", "IMAP wird verwendet, um eine Verbindung zu POP3- und IMAP-Servern herzustellen.");
define("_EXTTEST_CURL", "Die CURL-Funktionen verbessern den Zugriff auf externe Daten.");
define("_EXTTEST_TIDY", "Wenn die TIDY Erweiterung aktiv ist, kann die HTML-Ausgabe automatisch validiert werden. Dies kann den Seitenaufbau im Browser beschleunigen und macht die Webseite W3C konform.");
define("_EXTTEST_XML", "Die XML-Erweiterung wird unter anderem für die Generierung der RSS-Feeds benötigt.");
define("_EXTTEST_RECFOUND", "Die empfohlene Erweiterung '%s' ist vorhanden");
define("_EXTTEST_RECNOTFOUND", "Die empfohlene Erweiterung '%s' ist nicht vorhanden. <span class=\"details\">%s</span>");
define("_VERCHECK_DESCRIBE", "Die hier aufgelisteten Dateien/Ordner sind veraltet und werden von " . MX_SETUP_VERSION . " nicht mehr verwendet. Sie können unter Umständen, Störungen und Sicherheitsprobleme verursachen. Sie sollten deshalb unbedingt gelöscht werden.");
define("_VERCHECK_DEL", "Dateien und Ordner löschen");
define("_FILEDELNOTSURE", "Sie können diesen Schritt jetzt übergehen und später in der Versionsverwaltung des pragmaMx-Systems nachholen.");
define("_ERRMSG2", "Nachfolgende Dateien/Ordner konnten nicht automatisch gelöscht werden. Sie können dies später über die Versionsverwaltung des pragmaMx-Systems nachholen.");
define("_PDOTEST_OK", "PDO-Datenbanktreiber (%s) ist verfügbar");
define("_PDOTEST_FAIL", "Es wurde kein verwendbarer PDO-Datenbanktreiber (z.B. %s) gefunden");
define("_EXTTEST_PDO", "Die PDO Erweiterung wird künftig der Standarddatenbanktreiber für pragmaMx sein. Die Erweiterung sollte baldmöglichst verfügbar sein.");
define("_EXTTEST_ZIP", "Die Zip-Funktionalität wird von einigen Zusatzmodulen verwendet und sollte verfügbar sein.");

define("_DBCONNECT","Datenanbindung");

define("_EXTTEST_FILE_FAIL", "fehlende Schreibberechtigung für : %s");
define("_EXTTEST_FILE_OK", "Schreibberechtigungen für das System sind vorhanden.");
?>