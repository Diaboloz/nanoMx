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
 * corrections by Joerg Fiedler, http://www.vatersein.de/
 */

defined('mxMainFileLoaded') or die('access denied');

define("_NOMOREINFORMATION", "Keine weiteren Informationen über diesen Benutzer verfügbar");
define("_OPTIONAL", "(optional)");
define("_ERRORINVEMAIL", "Fehler: Ungültige E-Mail-Adresse.");
define("_ERRORNOEMAIL", "Fehler: keine E-Mail-Adresse angegeben.");
define("_ERRORINVNICK", "Fehler: Ungültiger Benutzername.");
define("_NICK2LONG", "Fehler: der Benutzername ist zu lang. Maximal %s Zeichen sind erlaubt.");
define("_NICK2SHORT", "Fehler, der Benutzername ist zu kurz. Minimal %s Zeichen sind erforderlich.");
define("_NAMERESERVED", "Fehler: dieser Benutzername ist reserviert oder gesperrt.");
define("_NICKNOSPACES", "Fehler: der Benutzername darf keine Leerzeichen enthalten.");
define("_NICKNOSPECIALCHARACTERS", "Fehler: der Benutzername darf keine Sonderzeichen wie <i>%s</i> enthalten.");
define("_NICKNOTNUMERIC", "Fehler: Rein numerische Benutzernamen sind nicht erlaubt.");
define("_NICKTAKEN", "Fehler: Dieser Benutzername existiert bereits.");
define("_EMAILREGISTERED", "Fehler: Diese E-Mail-Adresse existiert bereits.");
define("_UUSERNAME", "Benutzername");
define("_FINISH", "Fertigstellen");
define("_YOUUSEDEMAIL", "Sie, oder jemand anderes, hat Ihre E-Mail-Adresse verwendet, um bei");
define("_TOREGISTER", " einen Account anzumelden");
define("_YOUAREREGISTERED", "Sie sind jetzt angemeldet. Das Passwort wird Ihnen an die angegebene E-Mail-Adresse gesendet.");
define("_THISISYOURPAGE", "Ihr persönliches Kontrollzentrum");
define("_AVATAR", "Avatar");
define("_WEBSITE", "Webseite");
define("_ICQ", "ICQ-Nummer");
define("_AIM", "AIM-Nummer");
define("_YIM", "YIM-Nummer");
define("_MSNM", "MSNM-Nummer");
define("_LOCATION", "Ort");
define("_OCCUPATION", "Tätigkeit");
define("_INTERESTS", "Interessen");
define("_SIGNATURE", "Signatur");
define("_EXTRAINFO", "Weitere Infos");
define("_LAST10COMMENTS", "die letzten 10 Kommentare von");
define("_LAST10SUBMISSIONS", "die letzten 10 Beiträge von");
define("_LOGININCOR", "Login fehlerhaft! Bitte versuchen Sie es noch einmal...");
define("_USERLOGIN", "Benutzer- Login");
define("_USERREGLOGIN", "Benutzer anmelden/einloggen");
define("_REGNEWUSER", "Neuen Benutzer anmelden");
define("_LIST", "Auflisten");
define("_PASSWILLSEND", "Das Passwort wird Ihnen (zusätzlich) per E-Mail geschickt.");
define("_COOKIEWARNING", "Um Benutzerfunktionen nutzen zu können, müssen Cookies in Ihrem Browser aktiviert werden.");
// define("_ASREGUSER", "Als angemeldeter Benutzer können Sie bspw.:");
// define("_ASREG1", "Kommentare mit Ihrem Namen schreiben");
// define("_ASREG2", "Nachrichten mit Ihrem Namen schreiben");
// define("_ASREG3", "Die Anzeige der Startseite verändern");
// define("_ASREG4", "Die Zahl der angezeigten Nachrichten für die Newsseite einstellen");
// define("_ASREG5", "Kommentardarstellung anpassen");
// define("_ASREG6", "Ihr persönliches Benutzerbild auswählen oder hochladen");
// define("_ASREG7", "sowie auf weitere interessante Funktionen und Inhalte zugreifen...");
define("_REGISTERNOW", "Melden Sie sich jetzt an! Es ist kostenlos und unverbindlich.");
define("_WEDONTGIVE", "Wir versichern Ihnen, daß wir Ihre persönlichen Daten nicht weitergeben.");
define("_ALLOWEMAILVIEW", "Anderen Benutzern meine E-Mail-Adresse sichtbar machen ?");
define("_OPTION", "Optionen");
define("_PASSWORDLOST", "Passwort vergessen?");
define("_SENDPASSWORD", "Bestätigungscode / Passwort zusenden");
define("_NOPROBLEM", "Kein Problem. Benutzername eingeben und auf \"" . _SENDPASSWORD . "\" klicken.<br />Sie werden den Bestätigungscode per E-Mail erhalten.<br />Danach gehen Sie wieder auf diese Seite und geben unter dem Benutzernamen den Bestätigungscode ein.<br />Daraufhin wird Ihnen automatisch ein neues Passwort per E-Mail zugeschickt.");
define("_CONFIRMATIONCODE", "Bestätigungscode");
define("_SORRYNOUSERINFO", "Keine passende Benutzerinfo gefunden");
define("_CODEREQUESTED", "Sie, oder ein anderer Benutzer, haben gerade einen Bestätigungscode beantragt um Ihr Passwort zu ändern. ");
define("_YOURCODEIS", "Ihr Bestätigungscode lautet:");
define("_WITHTHISCODE", "Mit diesem Code können Sie ein neues Passwort beantragen.");
define("_HASTHISEMAIL", "Um Ihr neues Passwort zu bekommen, folgen Sie bitte diesem Link und geben dort den obenstehenden Bestätigungscode ein:");
define("_IFYOUDIDNOTASK2", "Sollten Sie diese E-Mail nicht beantragt haben, so ignorieren sie diese. Sollte dies aber nocheinmal vorkommen, setzen Sie sich bitte mit einem Administrator in Verbindung und nennen diesem die oben genannte IP.");
define("_HASREQUESTED", "Sie, oder ein anderer Benutzer, haben gerade ein neues Passwort angefordert.");
define("_YOURNEWPASSWORD", "Ihr neues Passwort lautet:");
define("_YOUCANCHANGE", "Sie können es ändern, nachdem Sie sich eingeloggt haben.");
define("_IFYOUDIDNOTASK", "Falls Sie nicht nach dem Passwort gefragt haben, ist dies auch kein Problem. NUR Sie können diese Nachricht sehen. Melden Sie sich einfach mit dem neuen Passwort an.");
define("_UPDATEFAILED", "Konnte Passwort nicht ändern. Bitte den Administrator kontaktieren.");
define("_PASSWORD4", "Passwort für");
define("_MAILED", "gesendet.");
define("_CODEFOR", "Bestätigungscode für");
define("_USERPASSWORD4", "Benutzer Passwort für");
define("_UREALNAME", "Richtiger Name");
define("_UREALEMAIL", "E-Mail-Adresse");
define("_EMAILNOTPUBLIC", "(Diese E-Mail-Adresse wird nicht veröffentlicht. Sie wird gebraucht, um Ihnen z.B. ein neues Passwort zu senden.)");
define("_UFAKEMAIL", "angezeigte E-Mail-Adresse");
define("_EMAILPUBLIC", "(Diese E-Mail-Adresse wird veröffentlicht.)");
define("_YOURHOMEPAGE", "Ihre Homepage");
define("_YOURAVATAR", "Ihr Benutzerbild");
define("_YICQ", "Ihr ICQ");
define("_YAIM", "Ihr AIM");
define("_YYIM", "Ihr YIM");
define("_YMSNM", "Ihr MSNM");
define("_YLOCATION", "Ihr Wohnort");
define("_YOCCUPATION", "Ihre Tätigkeit");
define("_YINTERESTS", "Ihre Interessen");
define("_MAXICHARS", "(maximal 400 Zeichen. Fügen Sie hier die Signatur ein (HTML code möglich)");
define("_CANKNOWABOUT", "(maximal 255 Zeichen. Nähere Angaben über Sie, für andere Benutzer)");
define("_TYPENEWPASSWORD", "(Zum Ändern bitte das neue Passwort zwei Mal eingeben)");
define("_SOMETHINGWRONG", "SQL-Fehler!<br />Die Daten konnten nicht gespeichert werden.");
define("_PASSDIFFERENT", "Die beiden Passwörter sind unterschiedlich. Sie müssen identisch sein.");
define("_YOUPASSMUSTBE", "Das Passwort muss mindestens");
define("_CHARLONG", "Zeichen lang sein");
define("_AVAILABLEAVATARS", "Verfügbare Avatare");
define("_NEWSINHOME", "Anzahl der Artikel auf der News-Seite");
define("_MAX127", "(max. 127):");
define("_ACTIVATEPERSONAL", "persönlichen Block aktivieren");
define("_CHECKTHISOPTION", "(Aktivieren sie diese Option und der folgende Text wird auf der Startseite erscheinen.)");
define("_YOUCANUSEHTML", "(Sie können auch HTML Code einfügen, wie z.B. Weblinks.)");
define("_SELECTTHEME", "Seiten-Design wählen");
define("_DISPLAYMODE", "Darstellungsmodus");
define("_SORTORDER", "Sortieren nach");
define("_COMMENTSWILLIGNORED", "Kommentare, welche diese Einstellung unterschreiten, werden nicht angezeigt.");
define("_UNCUT", "Ursprünglich und ungekürzt");
define("_EVERYTHING", "Fast alles");
define("_FILTERMOSTANON", "die meisten anonymen Nutzer filtern");
// define("_USCORE", "Wertung");
define("_SCORENOTE", "Anonyme Beiträge beginnen bei 0, von Benutzern erstellte Beiträge bei 1. Moderatoren können Punkte hinzufügen oder abziehen.");
define("_NOSCORES", "Wertungen nicht anzeigen");
define("_HIDDESCORES", "(Versteckte Wertung: Die Punkte sind wirksam, aber nicht sichtbar.)");
define("_MAXCOMMENT", "Maximale Kommentarlänge");
define("_TRUNCATES", "(Kürzt lange Kommentare und fügt einen Link für \"weiterlesen...\" ein. Schaltet ganz gross aus)");
define("_BYTESNOTE", "bytes (1024 bytes = 1K)");
define("_USENDPRIVATEMSG", "Private Nachricht senden an");
define("_THEMESELECTION", "Seitendarstellung konfigurieren");
define("_COMMENTSCONFIG", "Kommentardesign konfigurieren");
define("_HOMECONFIG", "Startseitenkonfiguration");
define("_PERSONALINFO", "persönliche Daten");
define("_USERSTATUS", "jetzt Online");
define("_ONLINE", "Online");
define("_OFFLINE", "Offline");
define("_CHANGEYOURINFO", "Daten");
define("_CHANGEYOURPHOTO", "Ihr Photo");
define("_CONFIGCOMMENTS", "Kommentar-Ansicht");
define("_CHANGEHOME", "Einstellungen");
define("_LOGOUTEXIT", "ausloggen");
define("_SELECTTHETHEME", "Seiten-Design");
define("_YA_EDITUSER", "Benutzerdaten editieren");
define("_USERCHECKDATA", "bitte überprüfen Sie alle eingegebenen Daten. Wenn alles korrekt ist, können Sie mit dem \"Fertigstellen\" Button die Anmeldung abschliessen. Klicken sie auf \"Zurück\" und Sie können ändern, was nötig ist.");
define("_USERFINALSTEP", "Neue Benutzer Anmeldung: Abschliessender Schritt");
define("_ACCOUNTCREATED", "Ein neuer UserAccount wurde erstellt!");
define("_THANKSUSER", "Vielen Dank für Ihre Registrierung bei");
define("_YOUCANLOGIN", "In kurzer Zeit erhalten Sie eine E-Mail mit dem vom System erstellten Kennwort.<br />Sie können das Kennwort über den Link \"Ihre Benutzerdaten\" jederzeit ändern.");
define("_BROWSEUSERS", "Benutzer auflisten");
define("_SEARCHUSERS", "Benutzer suchen");
define("_DIRECTLOGIN", "Sofort einloggen, ");
define("_TOCHANGEINFO", "um \"Ihre Benutzerdaten\" zu ändern.");
define("_DESIREDPASS", "Kennwort");
define("_CONFIRMPASS", "Bitte wiederholen");
define("_OPTIONAL1", "(Optional, minimum %d Zeichen.)");
define("_OPTIONAL2", "Bitte das Passwort zwei Mal eingeben.");
define("_OPTIONAL3", "(Bitte mit \" http:// \" eingeben.)");
define("_NOTCONFIRMED", "Sie haben Ihr Kennwort nicht korrekt wiederholt.");
define("_NOTPROVIDE", "Sie haben kein Wunschkennwort eingeben, so dass vom System ein zufälliges generiert wurde.");
define("_OR", "oder");
define("_YOUBAD", "Sie haben versucht, eine ungültige Operation durchzuführen!");
define("_SUREDELETE", "Sind Sie sicher, dass Sie Ihren Account löschen möchten?");
define("_ACCTDELETED", "Ihr Account wurde aus unserem System entfernt!");
define("_DELETEACCT", "Account löschen");
define("_CHANGEACCT", "Ihre Infos verändern");
define("_ACCTHOME", "Hauptseite verändern");
define("_ACCTCOMMENTS", "Kommentareinstellungen");
define("_ACCTTHEME", "Ein anderes Design auswählen");
define("_ACCTEXIT", "Logout/Exit");
define("_DATABASEERROR", "Datenbankfehler: Benutzer konnte nicht zur Datenbank hinzugefügt werden.");
define("_YA_YESPOINTS", "einen Userpunkt");
define("_YA_NOPOINTS", "keine Userpunkte");
define("_YA_HASPOINTS", "Punkte insgesamt");
define("_YA_BWOPMSG", "Private Nachrichten");
define("_YA_BWOPMSGALL", "insgesamt");
define("_YA_BWOPMSGUNREAD", "ungelesen");
define("_YA_CLICKTOSHOW", "hier klicken");
define("_YA_PWVORSCHLAG", "Vorschlag");
define("_YA_ACCOUNTDATA", "Zugangsdaten");
define("_YA_EDITDATAOK", "Ihre Einstellungen wurden gespeichert.");
define("_YA_EDITUSEROK", "Ihre persönlichen Daten wurden gespeichert.");
define("_YA_SITEDEFAULT", "Seitenstandard");
define("_YOUAREREGISTERED_0", "Sie sind jetzt angemeldet. In Kürze erhalten Sie eine E-Mail mit dem vom System erstellten Kennwort. Sie können das Kennwort über den Link \"Ihre Benutzerdaten\" jederzeit ändern.");
define("_YOUAREREGISTERED_1", "Sie sind jetzt angemeldet. Das Passwort wird Ihnen zusätzlich an die angegebene E-Mail-Adresse gesandt.");
define("_YOUAREREGISTERED_2", "Sie sind jetzt angemeldet. Nach Freischaltung Ihres Accounts, durch einen Administrator, wird Ihnen das Passwort an die angegebene E-Mail-Adresse gesandt. Damit können Sie sich im System einloggen.");
define("_YOUAREREGISTERED_3", "Sie erhalten eine E-Mail mit dem Aktivierungslink an die angegebene E-Mail-Adresse. Sie müssen damit Ihren Account, innerhalb der nächsten 24 Stunden, aktivieren. Ansonsten werden Ihre Daten wieder aus dem System gelöscht.");
define("_YA_LOGINERR_1", "Fehler!<br />Die Sessioninitialisierung ist fehlgeschlagen.<br />Vermutlich verhindern Ihre Sicherheitseinstellungen das Setzen von Cookies. (1)");
define("_YA_LOGINERR_2", "Fehler!<br />Die Sessioninitialisierung ist fehlgeschlagen. (2)");
define("_YA_LOGINERR_3", "Fehler!<br />Der Benutzername oder das Kennwort ist ungültig, oder wurde nicht angegeben. (3)");
define("_YA_LOGINERR_4", "Fehler!<br />Dieser Benutzeraccount existiert nicht. (4)");
define("_YA_LOGINERR_5", "Fehler!<br />Das Passwort ist falsch. (5)");
define("_YA_LOGINERR_6", "Fehler!<br />Dieser Benutzeraccount wurde noch nicht aktiviert. (6)");
define("_YA_LOGINERR_7", "Fehler!<br />Dieser Benutzeraccount wurde gelöscht. (7)");
define("_YA_LOGINERR_8", "Fehler!<br />Diese Webseite zeigt das Copyright der CMS-Entwickler nicht an. Das Einloggen ist nicht möglich. (8).");
define("_YA_LOGINERR_9", "Fehler!<br />Dieser Benutzeraccount wurde deaktiviert. (9)");
define("_YA_LOGINERR_10", "Fehler!<br />Die aktuelle Session ist nicht mehr gültig. (10)");
define("_YA_LOGINERR_11", "Fehler!<br />Dieser Benutzername ist nicht erlaubt. (11)");
define("_YA_PMPOPTIME1", "Popupfenster");
define("_YA_PMPOPTIME2", "neue private Nachrichten abfragen und diese im Popupfenster anzeigen. (Javascript muss aktiviert sein)");
define("_YA_PMPOPTIME3", "alle ");
define("_YA_PMPOPTIME4", "Sekunden");
define("_YA_PMPOPTIME5", "Minuten");
define("_YA_ONLYYOUSEE", "Diese Informationen können nur Sie sehen");
define("_YA_REG_MAILMSG2", "Nach Freischaltung Ihres Accounts, durch einen Administrator, wird Ihnen das Passwort an diese E-Mail-Adresse gesandt. Sollten Sie innerhalb der nächsten 3 Tage keine Nachricht von uns erhalten, fragen Sie bitte per E-Mail bei uns nach. Oder verwenden Sie unsere Feedback-Funktion."); # unter: " . PMX_HOME_URL . "/modules.php?name=Feedback");
define("_YA_REG_MAILSUB2", "Anmeldebestätigung für");
define("_NOTAGREE", "Fehler: Sie müssen die Nutzungsbedingungen akzeptieren!");
define("_SHOWIT", "anzeigen");
// define("_EULA", "Nutzungsbedingungen");
define("_IHAVE", "Ich habe die");
define("_READDONE", "gelesen");
define("_BLOCKEDMAIL", "Fehler: diese E-Mail-Adresse (oder Teile davon) ist nicht zugelassen, wählen Sie bitte eine andere.");
define("_YA_REG_MAILSUB5", "Aktivierungslink für");
define("_YA_REG_MAILMSG5", "Bitte klicken Sie den unten stehenden Link an, um Ihren Account zu aktivieren.\nFalls Sie keinen Account bei uns registriert haben,\ndann wurde Ihre E-Mail-Adresse missbraucht. In diesem Fall ignorieren Sie bitte diese E-Mail.\nAnsonsten haben Sie 24 Stunden Zeit um über den Aktivierungslink Ihren Account zu aktivieren.");
define("_ACTLINKSENDED", "Der Aktivierungslink wurde an die angegebene E-Mail-Adresse gesendet!");
define("_USERREGACT", "Benutzeraccount anmelden/aktivieren");
define("_ACTSUCCESS", "Sie haben erfolgreich Ihren Account freigeschaltet.");
define("_LOGINNOW", "Wenn Sie möchten, können Sie sich sofort einloggen");
define("_WAITFORADMINACTION", "Sobald der Administrator Ihre Daten überprüft und freigeschaltet hat,\n bekommen Sie eine Bestätigung-E-Mail. Danach können Sie sich einloggen");
define("_ALREADY_EXIST", "Sorry, aber Username oder E-Mail-Adresse sind bereits registriert !");
define("_ALREADYACTIVE", "Dieser Account wurde bereits aktiviert !");
define("_ACTIVEORDELETED", "Dieser Account wurde entweder bereits aktiviert, oder die Aktivierungs-\nwartezeit (24Stunden) wurde überschritten und der Aktivierungswunsch\nwurde gelöscht!");
define("_ERROREMPYBDATE", "Es wurde kein Geburtsdatum eingetragen");
define("_ERRFALSEDATE", "Es wurde ein ungültiges Geburtsdatum angegeben.");
define("_ERRAPPROVEDATE", "Das Mindestalter für die Anmeldung beträgt <strong>%d</strong>&nbsp;Jahre.");
define("_YAUSERINFO", "Benutzerinfo");
define("_USERGUESTBOOK", "Usergästebuch");
define("_ACTIVATEUSERGUESTBOOK", "aktivieren");
define("_SENDPASSWORD_2", "Passwort zusenden");
define("_NOPROBLEM_2", "Kein Problem. Benutzername eingeben und auf \"" . _SENDPASSWORD_2 . "\" klicken.<br />Sie werden dann Ihr Passwort per E-Mail erhalten.<br />Danach können Sie sich wie gewohnt mit dem gesendeten Passwort einloggen.");
define("_ERROR_NO_USERNAME_EMAIL", "Fehler: Bitte füllen Sie beide Felder aus um die Password - Recover Funktion zu benutzen!");
define("_ERROR_NO_USERNAME", "Fehler: Kein Username angegeben!");
define("_YAOVERVIEW", "Übersicht");
define("_PASSLOSTOPTION", "Passwort vergessen Funktion");
define("_PASSLOSTOPTION_A", "Bestätigungscode zusenden");
define("_PASSLOSTOPTION_B", "E-Mail abfragen und direkt zusenden");
define("_ERROR_CANNOTSENDMAIL", "Fehler beim Mailversand");
define("_ERROR_USERNAMENOTEXIST", "Der Benutzername '%s' wurde nicht gefunden.");
define("_ERROR_USERHASNOEMAIL", "Zu diesem Benutzernamen existiert keine gültige E-Mail-Adresse. Ein neues Passwort kann nicht zugesendet werden. Bitte wenden Sie sich an den Administrator der Webseite.");
define("_ERROR_USEREMAILNOTMATCH", "Diese E-Mail-Adresse gehört nicht zum Benutzernamen '%s'.");
define("_ERROR_FALSECODE", "Ungültiger Bestätigungscode.");
define("_UPIC_PIC", "Benutzerbild");
define("_UPIC_UPLOADED", "hochgeladenes Benutzerbild");
define("_UPIC_UPLOAD1", "Es wurde kein Bild hochgeladen.");
define("_UPIC_UPLOAD2", "Es wurde kein Bild hochgeladen, es sind folgende Fehler aufgetreten:");
define("_UPIC_UPLOAD3", "Das Bild wurde erfolgreich hochgeladen.");
define("_UPIC_UPLOAD3A", "Das Bild wurde erfolgreich hochgeladen.");
define("_UPIC_UPLOAD4", "Das Bild wurde nicht korrekt hochgeladen, es sind folgende Fehler aufgetreten:");
define("_UPIC_UPLOAD5", "Der Administrator konnte von diesem Upload nicht benachrichtigt werden!");
define("_UPIC_MAILSUBJECT", "Ein neues Benutzerbild wurde hochgeladen!");
define("_UPIC_MAILMESSAGE", "%s hat sich ein neues Benutzerbild hochgeladen!\n%s\n\n\nHinweis: Diese E-Mail wurde automatisch generiert, bitte nicht darauf antworten!");
define("_UPIC_MAXSIZE", "Maximale Größe der Bild-Datei:");
define("_UPIC_MAXPROPERTIES", "Die Proportionen von <span class=\"nowrap\">%d * %d Pixel</span> sollten nicht überschritten werden.");
define("_UPIC_AUTORESIZESIZING", "Werden die Proportionen (Breite * Höhe) überschritten, wird das Bild automatisch verkleinert.");
define("_UPIC_PIXEL", "Pixel");
define("_UPIC_MAX_KB_UPLOAD", "Die maximale Größe eines Bildes beträgt <span class=\"nowrap\">%d KB</span>.");
define("_UPIC_UPLOADING", "Hochladen");
define("_UPIC_JS_INVALIDEXTENSION", "Fehler: ungültige Dateiendung");
define("_UPIC_JS_UPLOADING", "Hochladen");
define("_UPIC_JS_UPLOAD", "Auswählen und Hochladen");
define("_UPIC_OWN", "Eigenes Bild");
define("_UPIC_OWN_DESC", "Geben Sie die URL zu Ihrem Benutzerbild an. <span class=\"nowrap tiny\">(z.B.: http://www.meineseite.de/bild.gif )</span>");
define("_UPIC_UPLOADPIC", "Eigenes Bild hochladen");
define("_UPIC_UPLOADPIC_DESC", "Laden Sie Ihr Benutzerbild auf unseren Server.");
define("_UPIC_UPLOADPIC_NOTES", "Beachten Sie dabei bitte die folgenden Hinweise:");
define("_UPIC_HAVENO", "kein Bild");
define("_UPIC_WRONGAVATARFILE", "Ungültiger Dateiname in der Avatarauswahl.");
define("_UPIC_WRONGOWNPICENDING", "Das eigene externe Bild hat eine ungültige Dateiendung. Erlaubt ist nur: %s");
define("_UPIC_WRONGOWNPIC", "Ungültige Angabe des eigenen externen Bildes.");
define("_UPIC_NOUPLOADEDPIC", "Das hochgeladene Bild existiert nicht oder ist ungültig.");
define("_UPIC_DIRECTORY", "Benutzerbild-Verzeichnis");
define("_UPIC_URL", "Benutzerbild URL");
define("_UPIC_DIMENSIONS_NOTE", "(0 = kein Limit)");
define("_UPIC_RESIZE_UPLOAD", "Größenänderung bei übergroßen Benutzerbildern");
define("_UPIC_RESIZE_UPLOAD_note", "(benötigt GD Modul)");
define("_UPIC_DOWNLOAD_PNG", "PNG für geänderte Benutzerbilder benutzen");
define("_UPIC_GD_WARNING", "Das GD Modul ist nicht installiert, daher sind manche Funktionen zu den Benutzerbildern deaktiviert.");
define("_UPIC_UPLOADS", "Hochzuladende Benutzerbilder");
define("_UPIC_SERVER_STORED", "Server-gespeicherte Benutzerbilder");
define("_UPIC_SERVER_STORED_GROUPS", "Mitgliedergruppen, die ein Benutzerbild wählen dürfen");
define("_UPIC_UPLOAD_GROUPS", "Mitgliedergruppen, die ein Benutzerbild hochladen dürfen");
define("_UPIC_CUSTOM_DIR", "Upload-Verzeichnis");
define("_UPIC_CUSTOM_DIR_DESC", "Es sollte nicht das gleiche Verzeichnis wie für die Server-gespeicherten Benutzerbilder sein.");
define("_UPIC_CUSTOM_URL", "Upload-URL");
define("_UPIC_MAILNOTICE", "E-Mail Benachrichtigung:");
define("_UPIC_YOURMAIL", "Ihre E-Mail Adresse:");
define("_UPIC_MAXFILESIZE", "Maximale Größe der hochzuladenden Datei:");
define("_UPIC_IMAGEPROPS_UPLOADED", "Max. Dimensionen der hochzuladenden Bilder (Breite * Höhe)");
define("_UPIC_IMAGEPROPS_PROFILE", "Bilddimensionen im Benutzerprofil (Breite * Höhe):");
define("_UPIC_IMAGEPROPS_AVATAR", "Bilddimensionen als Avatar (Breite * Höhe):");
/* Neu: */
define("_UPIC_IMAGEPROPS_THEME", "Bilddimensionen in der Themenavigation (Breite * Höhe):");
/* Ende Neu */
define("_UPIC_SUREDELETE", "Wollen Sie das Bild wirklich löschen?");
define("_UPIC_DELETEIMG", "Bild löschen");
define("_UPIC_DELETED", "Benutzerbild wurde gelöscht");
define("_UPIC_DELETEERR", "Fehler: Benutzerbild konnte nicht gelöscht werden. Möglicherweise haben sie nicht die nötigen Rechte.");
define("_UTAB_OPTIONS", "Optionen");
define("_UTAB_USERPIC", _UPIC_PIC);
define("_UTAB_WELCOMEMSG", "Willkommensnachricht");
define("_UTAB_USERPOINTS", "Benutzerpunkte");
define("_UTAB_OTHERS", "Sonstiges");
define("_YA_REAC_DELETED", "gelöscht");
define("_YA_REAC_DELETEDUSER", "gelöschter Benutzeraccount");
define("_YA_REAC_SENDMESSAGE", "Wollen Sie den Benutzer <i>%s</i> per eMail benachrichtigen?");
define("_YA_REAC_EDITMSGTEXT", "Den folgenden Mailtext, bei Bedarf bitte anpassen");
define("_YA_REAC_MSGSUBJECT", "Benutzername bei - %s - wurde reaktiviert");
define("_YA_REAC_MESSAGETEXT", "Hallo %s,\n\nIhr Benutzername bei - %s - wurde soeben reaktiviert.\nBei Ihrem nächsten Besuch klicken Sie bitte den Link \"" . _PASSWORDLOST . "\", um sich ein neues Passwort zusenden zu lassen.\n\nMit freundlichen Grüssen\nder Webmaster");
define("_YA_REAC_RESULTERR", "Der Benutzer %s konnte nicht reaktiviert werden");
define("_YA_REAC_RESULTOK", "Der Benutzer %s wurde erfolgreich reaktiviert.");
define("_YA_REAC_SENDMSGERR", "Der Benutzer %s konnte nicht benachrichtigt werden.");
define("_YA_REAC_SENDMSGOK", "Der Benutzer %s wurde benachrichtigt.");
define("_YA_DELETED_MAILSUBJ", "%s - Benutzeraccount gelöscht"); // sprintf(sitename)
define("_YA_DELETED_MAILTEXT", "Der Benutzeraccount '%s' wurde gelöscht von %s."); // sprintf(username, sitename)

/* Das bitte auch: (Your Account) */
/**
 * Derzeitiges Passwort     Bitte geben Sie ihr derzeitiges Passwort an! (Passwort vergessen?)
 * Neue Email-Adresse     Ihre neue Email Adresse
 * Hinweis: Wenn Sie Ihre Email-Adresse ändern, wird ein neues Passwort generiert und an Ihre neue Adresse geschickt. Stellen Sie sicher das diese Email-Adresse einwandfrei funktioniert, sonst wird ihr Account unbrauchbar.
 */

// define("_ASREGISTERED", "<a href=\"modules.php?name=User_Registration\">Kostenlos registrieren!</a>. Gestalten Sie diese Seite mit und passen Sie sich das Seitenlayout Ihren Wünschen an");
// define("_YOUARELOGGED", "Sie sind eingeloggt als");
// define("_CURRENTLY", "Zur Zeit sind");
// define("_GUEST", "Gast und");
// define("_GUESTS", "Gäste und");
// define("_MEMBER", "Mitglied online.");
// define("_MEMBERS", "Mitglieder online.");

define("_GRANKS", "Benutzer-Punkte");
define("_USERPUNKTE", "FAQ zu den Benutzer-Punkten");
define("_YA_MALE", "männlich");
define("_YA_FEMALE", "weiblich");
define("_YA_NOSEX", "verrat ich nicht");
define("_YA_USEXUS", "Geschlecht");
define("_YA_UBDAY", "Geburtstag");
define("_YA_BYEAR", "Jahr");
define("_YA_BMONTH", "Monat");
define("_YA_BDAY", "Tag");
define("_YA_BDAYERR1", "ungültiges Datum");
define("_YA_BDAYERR2", "keine Angabe");
define("_YA_LASTONLINE", "letzter Besuch");
define("_YA_INGROUP", "Benutzergruppe");
define("_YA_FOLLOWINGMEM", "Folgende Informationen liegen von Ihnen vor:");
define("_YA_REG_MAILMSG4", "Benutzer Passwort fuer");
define("_YA_REG_MAILMSG3", "Ihr Account wurde freigeschaltet. Sie können sich ab sofort mit den untenstehenden Daten einloggen. Ihre Daten können Sie jederzeit über Ihr Benutzermenü ändern.");
define("_ALLOWUSERS", "Andere Benutzer die E-Mail-Adresse einsehen lassen");
define("_MENUFOR", "Menü für");

// define("_LEGALPP", "Erklärung zum Datenschutz (Privacy Policy)");

?>