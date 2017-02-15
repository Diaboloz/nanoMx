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
 * $Revision: 227 $
 * $Author: PragmaMx $
 * $Date: 2016-09-28 15:44:45 +0200 (Mi, 28. Sep 2016) $
 *
 * danish language file, translated by:
 * Wilhelm Moellering
 */

defined('mxMainFileLoaded') or die('access denied');

/* Datestrings und locale Einstellungen: */
$old_setlocale = setlocale(LC_TIME, 0);
$locale = array('dk_DK.UTF-8', 'da_DK.UTF-8', 'dk_DK.UTF8', 'da_DK.UTF8', 'da_DK', 'da_DA', 'dk_DK', 'dan', 'danish', 'DK', 'DNK', '208', 'CTRY_DENMARK', 'da_DK.ISO-8859-15', 'dk_DK.ISO-8859-15');
define('_SETLOCALE', setlocale(LC_TIME, $locale));
setlocale(LC_TIME, $old_setlocale);
define("_DOC_LANGUAGE", "da");
define("_DOC_DIRECTION", "ltr");
define('_DATESTRING', '%d.%m.%Y');

/**
 * Setup Optionen zur Auswahl, siehe setup-settings.php
 */
// Nyinstallation
define('_SETUPOPTION_NEW', 'Nyinstallation');
define('_SETUPOPTION_NEW_DESC', 'pragmaMx vil blive nyinstalleret. Hvis der allerede er data i databasen, bliver disse uforandret stående.');
// Update
define('_SETUPOPTION_UPDATE', 'Update af en bestående installation');
define('_SETUPOPTION_UPDATE_DESC', 'Setupscriptet forsøger at aktualisere en allerede bestående installation af pragmaMx. Det er også egnet til konvertering af data fra phpNuke, vkpMx og forskellige andre phpNuke-Clones. Yderligere informationer til konverteringen findes her: &quot;<a href="' . _MXCONVERTINFOSITE . '">pragmaMx skift fra andre CMS</a>&quot;');
// Setupschritte
define('_STEP_SELECT', 'Udvalg, Setup eller Update');
define('_STEP_ISINCORRECT', 'Sikkerhedsdialog');
define('_STEP_LICENSE', 'Licensaftale');
define('_STEP_BACKUP', 'Backup af databasen');
define('_STEP_UPDATE', 'Update af databasen');
define('_STEP_DELFILES', 'fjern unødvendige filer');
define('_STEP_FINISHEDINSTALL', 'Installation afslutted');
define('_STEP_DBSETTINGS', 'konfigurer database forespørgelser');
define('_STEP_DBSETTINGSCREATE', 'konfigurere database / udarbejde databasen');
define('_STEP_MORESETTINGS', 'Yderligere indstillinger');
define('_STEP_MORESETTINGSCHECK', 'kontrollere indstillinger');
define('_STEP_FINISHEDUPDATE', 'Update afsluttet');
define('_STEP_CONFIGURATION', 'aktualisere konfiguration');
define('_HELLOINSTALL', 'Installation <span class="label">' . MX_SETUP_VERSION .'</span>');
define('_HELLOINSTALL2', 'Vi glæder os over, at du har besluttet dig til at bruge ' . preg_replace('#[[:space:]]#', '&nbsp;', MX_SETUP_VERSION) . '.<br /> Før du starter installationen, er det tilrådeligt at læse vores <a href="' . _MXDOKUSITE . '">Online-dokumentation</a>.<br /> Hvis du allerede har gjort det, kan du fortsætte med installationen.');
define('_WHATWILLYOUDO', 'Du har nu mulighed at vælge mellem forskellige setup-metoder. Setup-Scriptet har allerede aktiveret den anbefalede metode. Vælg kun en anden metode, når du er sikker på, at du ved hvad du gør.<br /><br />Hvad vil du gøre?');
define('_OLDVERSION_ERR1', 'Din valgte Setup-Option svarer ikke til standarten,<br />det kan give problemer eller tab af data.');
define('_OLDVERSION_ERR2', 'Er du sikker du vil gennemføre optionen &quot;<em>' . @constant($GLOBALS['opt'][@$_REQUEST['setupoption']]['name']) . '</em>&quot; ?');
define('_CONFIGSAVEMESS', 'Afsluttende aktualiseres konfigurationsfilen <em>' . basename(FILE_CONFIG_ROOT) . '</em> .');
define('_CONFIG_OK_NEW', 'Konfigurationsfilen <em>' . basename(FILE_CONFIG_ROOT) . '</em> blev succesrigt oprettet.');
define('_CONFIG_OK_OLD', 'Konfigurationsfilen <em>' . basename(FILE_CONFIG_ROOT) . '</em> var allerede på aktuelt stand og er i orden.');
define('_CONFIG_ERR_1', 'Filen <em>' . basename(FILE_CONFIG_ROOT) . '</em> er skrivebeskyttet!');
define('_CONFIG_ERR_2', 'Der kun ikke skrives i filen <em>' . basename(FILE_CONFIG_ROOT) . '</em>.');
define('_CONFIG_ERR_4', 'Filen <em>' . basename(FILE_CONFIG_ROOT) . '</em> kun af udefineret grund ikke fremstilles.');
define('_CONFIG_ERR_3', 'Mappen <em>' . PMX_BASE_PATH . '</em> er skrivebeskyttet! Juster rettigheder venligst!');
define('_CONFIG_ERR_5', 'Filen i <em>' . basename(FILE_CONFIG_ROOT) . '</em> eksisterer, men kan ikke læses af systemet.');
define('_CONFIG_ERR_6', 'Filen i <em>' . basename(FILE_CONFIG_ROOT) . '</em> eksisterer, men data kun ikke korrekt bliver skrevet.');
define('_CONFIG_ERR_8', 'Konfigurationsfilen <em>' . basename(FILE_CONFIG_ROOT) . '</em> er ikke korrekt skrevet, men forbindelse til databasen er i orden. Du kan fortsætte med installationen, men skulle der efter dine systemindstillinger i administrationsmenuen kontrollere og evtl. ny indstille.');
define('_CONFIG_BACK', 'En kopie af den bestående konfigurationsfil blev under følgende navn succesrigt oprettet:');
define('_CONFIG_CREATE', 'Lav venligst med hjælp af den viste PHP-kode en ny scriptfil. Døb filen <em>' . basename(FILE_CONFIG_ROOT) . '</em> og kopier den ind i hovedfortegnelse (' . dirname(basename(FILE_CONFIG_ROOT)) . ') af din pragmaMx installation.<br />Læg mærk til, at den hele tekst, 1:1 bliver overføret.<br /><br />Efter du har gjort det, kan du forsætte med installationen.');
define('_CONFIG_BUTTONMAN', 'Opret konfigurationsfilen selv');
define('_CURRENTSTATUS', 'Hitiliger installationsstatus');
define('_THEREERROR', 'Der optrådte fejl');
define('_WILL_CREATE_TABLES', 'Ved næste trin bliver tabbeller oprettet og aktualiseret, dette kan tage et stykke tid!');
define('_WILL_CREATE_BACKUP', 'Hvis du benytter dig af Backup-optionen før oprettelsen af tabellerne, bliver der forsøgt at oprette en komplet Backup af databasen.');
define('_CONTINUE_WITHOUTDBBACKUP', 'Fortsæt uden Backup');
define('_CONTINUE_WITHDBBACKUP', 'Fortsæt med Backup');
define('_DBFOLLOWERRORS', 'Der optrådte følgende fejl');
define('_NODBERRORS', 'Der optrådte ingen fejl.');
define('_DBNOTEXIST', 'Den nævnte database ekstisterer ikke på serveren.');
define('_DBNOTSELECT', 'Du har ikke nævnt en database.');
define('_DBNOACCESS', 'Adgang til databesen blev nægtet.');
define('_DBOTHERERR', 'Ved tilgang til databasen optrådte en fejl.');
define('_DBVERSIONFALSE', 'Desværre, men din mySQL version er for gammel.  For at installere PragmaMx er mindst version %s af MySQL-serveren påkrævet.');
define('_NOT_CONNECT', 'Der er ingen forbindelse til database-serveren eller dine adgangsdata er ikke korrekt.');
define('_NOT_CONNECTMORE', 'Vær sikker at filen config.php fra din tidligere version er til stede og at de der angivede adgangsdata til databasen er korrekte.');
define('_DB_CONNECTSUCCESS', 'Forbindelse til databasen <em>%s</em> blev succcesrigt oprettet.');
define('_CORRECTION', 'Korrektur');
define('_REMAKE', 'Gentag');
define('_IGNORE', 'Ignorere og videre');
define('_DONOTHING', 'Fortsæt');
define('_DBARETABLES', '<li>I den valgte database er der allerede tabeller.</li><li>Der anbefales en Backup af databasen.</li>');
define('_DBARENOTABLES', '<li>Den valgte database er tom, og en Backup ikke nødvendig.</li>');
define('_SUBMIT', 'Videre');
define('_OR', 'Eller');
define('_YES', 'Ja');
define('_NO', 'Nej');
define('_GOBACK', 'Tilbage');
define('_CANCEL', 'Afbrydde');
define('_FILE_NOT_FOUND', 'Filen ikke fundet eller kan ikke læses.');
define('_ACCEPT', 'Aksepterer du licensaftalen?');
define('_START', 'Startside');
define('_INTRANETWARNING', 'Intranet optionen skal kun vælges, når websitet ikke kan kaldes op med et domænenavn. Optionen kan kun anbefales, når sitet ligger bag en Firewall eller computeren er ikke tilsluttet internettet.');
define('_PRERR11', 'Begge præfikse skal begynde med et lille bogstav og må kun indholde tal, små bogstaver eller understregninger (_) og må ikke overskride en længde af ' . PREFIX_MAXLENGTH . ' tegn.');
define('_PRERR12', 'Den nye præfiks har ingen værdi.<br />Indtast venligst en præfiks.');
define('_PRERR13', 'Den nye præfiks svarer ikke standarden af pragmaMx eller phpNuke. Vælg for sikkerheds skyld en anden præfiks.');
define('_PRERR14', 'Præfiksen indholder ikke tilladte tegn. Tilladt er kun lille bogstaver, tal og understregninger (_). Præfiksen må ikke begynder med et.<br />Vælg venligst en anden præfiks.');
define('_PRERR15', 'Præfiksen må ikke begynde med et tal.<br />Vælg venligst en anden præfiks.');
define('_PRERR16', 'Præfiksen er for lang, en præfiks må højest bestå af ' . PREFIX_MAXLENGTH . ' tegn.<br />Vælg venligst en korter præfiks.');
define('_PRERR17', 'Der eksisterer allerede %d tabeller med den nye præfiks.<br />Vælg venligst en anden præfiks.');
define('_PRERR18', 'Den nye bruger-præfiks har ingen værdi.<br />Vælg venligst en bruger-præfiks.');
define('_PRERR19', 'Bruger-præfiksen indholder ikke tilladte tegn. Tilladt er kun lille bogstaver, tal og understregninger (_). Bruger-præfiksen må ikke begynde med et tal.<br />Vælg venligst en anden bruger-præfiks.');
define('_PRERR20', 'Bruger-præfiksen må ikke begynde med et tal.<br />Vælg venligst en anden bruger-præfiks.');
define('_PRERR21', 'Den nye bruger-præfiks er for lang. Præfikse må højest indholde ' . PREFIX_MAXLENGTH . ' tegn.<br />Vælg venligst en korter bruger-præfiks.');
define('_PRERR22', 'Der eksisterer allerede en brugertabelle med den nye brugerpræfiks.<br />Vælg venligst en anden brugerpræfiks.');
define('_SUPPORTINFO', 'Support for dit system kan du få her: <a href="' . _MXSUPPORTSITE . '">' . _MXSUPPORTSITE . '</a>');
define('_DOKUINFO', 'En online dokumentation finder du her: <a href="' . _MXDOKUSITE . '">' . _MXDOKUSITE . '</a>');
define('_NOBACKUPCREATED', 'Der er ikke oprettet en backup af databasen.');
define('_HAVE_CREATE_DBBACKUP', 'Databasen blev sikret som fil:');
define('_HAVE_CREATE_BACKUPERR_1', 'Backup af databasen var ikke muglig.');
define('_HAVE_CREATE_BACKUPERR_2', 'Hvis denne database indholder data vær sikker, at du har en aktuel backup før du fortsætter!');
define('_SETUPHAPPY1', 'Tillykke');
define('_SETUPHAPPY2', 'Dit system er nu komplet installeret. Ved næste klik kommer du direkte ind i administrationsmenuen.');
define('_SETUPHAPPY3', 'Her skal du først kontrollere grundindstillingerne og sikre dem.');
define('_DELETE_FILES', 'Når dit system kører tilfredsstillende, slet venligst mappen &quot;<em>' . basename(dirname(__DIR__)) . '</em>&quot; i din pragmaMx mappe.<br /><strong>Ellers opstår der en sikkerhedsrisiko!</strong>');
define('_GET_SQLHINTS', 'Her kan du se SQL-kommandoer hvilke gennem konverteringen/oprettelsen blev udført');
define('_DATABASEISCURRENT', 'Databasestrukturen var allerede up-to-date. Ændringer var ikke nødvendig.');
define('_SEEALL', 'se det hele');
define('_DB_UPDATEREADY', 'Tabelkonverteringen/oprettelsen er afsluttet.');
define('_DB_UPDATEFAIL', 'Tabelkonverteringen/oprettelsen kun ikke komplet gennemføres.');
define('_DB_UPDATEFAIL2', 'Der mangler følgende vigtige systemtabeller: ');
define('_BACKUPPLEASEDOIT', 'Der anbefales før aktualiseringen, at oprette en komplet database backup.');
define('_ERRMSG1A', 'Fejl: en update fil mangler, vær sikker, at følgende fil er til sted:');
define('_YEAHREADY2', 'Ellers er din pragmaMx nu på nyeste stand.');
define('_SERVERMESSAGE', 'Servermeddelelse');
define('_ERRDBSYSFILENOFILES', 'Ingen af systemtabellerne kunne blive kontrolleret/oprettet, i mappen <em>' . PATH_SYSTABLES . '</em> er ingen definitionsfil.');
define('_ERRDBSYSFILEMISSFILES_1', 'Ikke alle systemtabeller kunne blive kontrolleret/oprettet.');
define('_ERRDBSYSFILEMISSFILES_2', 'I mappen <em>' . PATH_SYSTABLES . '</em> mangler følgende definitionsfiler');
define('_THESYSTABLES_1', 'Systemtabellen <strong>%s</strong> kunne ikke blive kontrolleret/oprettet, fordi filerne af mappen ' . PATH_SYSTABLES . ' kun ikke findes.');
define('_THESYSTABLES_2', 'Systemtabellen <strong>%s</strong> kunne ikke blive kontrolleret/oprettet.');
define('_SYSTABLECREATED', 'Der blev kontrolleret/oprettet %d systemtabeller.');
define('_MODTABLESCREATED', 'Tabellerne af %d moduler blev kontrolleret/oprettet.');
define('_NOMODTABLES', 'Modultabeller blev ikke kontrolleret/oprettet.');
define('_STAT_THEREWAS', 'Der blev');
define('_STAT_TABLES_CREATED', 'tabeller oprettet.');
define('_STAT_TABLES_RENAMED', 'tabeller omdøbt.');
define('_STAT_TABLES_CHANGED', 'tabeller ændret.');
define('_STAT_DATAROWS_CREATED', 'Datapakke tilføjet/ændret.');
define('_STAT_DATAROWS_DELETED', 'Datapakke slettet.');
define('_MOREDEFFILEMISSING', 'Filen (<em>' . @ADD_QUERIESFILE . '</em>) med yderligere SQL-kommandoer mangler!');
define('_SETUPMODNOTFOUND1', 'Det valgte setup modul <strong>%s</strong> er ikke til rådighed!');
define('_ERROR', 'Fejl');
define('_ERROR_FATAL', 'stor fejl');
define('_SETUPCANCELED', 'Setup blev afbrudt!');
define('_GOTOADMIN', 'videre til administrationsmenuen');
define('_DBSETTINGS', 'Indtast her dine adgangsdata til din database. Setup-rutinen kan kun fortsætte med en korrekt oprettet database forbindelse. Dine adgangsdata før du fra din Webspace-udbyder.');
define('_DBNAME', 'Database navn');
define('_DBPASS', 'Database kodeord');
define('_DBSERVER', 'Database server');
define('_DBTYP', 'Database typ');
define('_DBUSERNAME', 'Database bruger');
define('_DBCREATEQUEST', 'Vil du prøve at oprette databasen &quot;<em>' . @$_REQUEST['dbname'] . '</em>&quot; ?');
define('_DBISCREATED', 'Databasen &quot;<em>' . @$_REQUEST['dbname'] . '</em>&quot; blev succesrigt oprettet.');
define('_DBNOTCREATED', 'Der optrådte en fejl ved oprettelse af databasen &quot;<em>' . @$_REQUEST['dbname'] . '</em>&quot; .');
define('_PREFIXSETTING', 'Præfikserne skal gøre en forskel på tabeller, hvis du vil installere flere pragmaMx systemer i en database. Bruger-præfiksen mugliggør en samtidig benyttelse af brugerdata indenfor flere pragmaMx. Ellers skulle bruger-præfiksen svare til den normal præfiks.');
define('_PREFIX', 'Præfiks af databasetabeller');
define('_USERPREFIX', 'Præfiks af brugertabel');
define('_DEFAULTLANG', 'Standardsprog');
define('_INTRANETOPT', 'Intranet område');
define('_ADMINEMAIL', 'administrator email');
define('_SITENAME', 'Sitenavn');
define('_STARTDATE', 'Startdato for sitet');
define('_CHECKSETTINGS', 'Kontroller venligst dine indstillinger!');
define('_PLEASECHECKSETTINGS', 'Kontroller venligst dine nuværende indstillinger.<br />Er alle data korrekt, kan du fortsætte med installations rutinen.<br />Ellers har du lejlighed til at korrigere dine data.');
define('_HAVE_CREATE_TABLES', 'Tabellerne blev oprettet.');
define('_HAVE_CREATE_TABLES_7', 'Tabellerne brugt til systemet, blev fejlfrit oprettet. Fortsæt med setup rutinen, men ved forskellige systemfunktioner  kan  der forkomme fejl.');
define('_HAVECREATE_TABLES_ERR', 'Databasen kun ikke oprettes fuldstændig. Installationen er slået fejl.');
define('_CREATE_DB', 'oprette databasen');
define('_DELETESETUPDIR', 'Klik her for at gøre denne setuprutine ubrugelig. Filen index.php bliver omdøbt og mappen pr. .htaccess beskyttet. <em>(funktionerer ikke på alle servere)</em>');
// add for fieldset //
define('_PREFIXE', 'Præfiks');
define('_SITE__MORESETTINGS', 'Website indstillinger');
define('_SERVER', 'Server data');
define('_BACKUPBESHURE', 'Før du tilpasser databasetabeller vær sikker på at du har lavet en aktuel sikring af din database.');
define('_BACKUPBESHUREYES', 'Ja, jeg har en aktuel database backup.');
define('_BACKUPBESHUREOK', 'Bekræft venligst, at du har en backup af din aktuel database.');
// Modulbezeichnungen
define('Your_Account', 'Min konto');
define('News', 'Artikel');
define('blank_Home', 'Home');
define('Content', 'Indhold');
define('Downloads', 'Downloads');
define('eBoard', 'Forum');
define('FAQ', 'FAQ');
define('Feedback', 'Feedback');
define('Guestbook', 'Gæstebog');
define('Impressum', 'Impressum');
define('Kalender', 'Terminer');
define('Statistics', 'Statistik');
define('Members_List', 'Brugerliste');
define('My_eGallery', 'Mediagalleri');
define('Newsletter', 'Newsletter');
define('Private_Messages', 'mine meddelelser');
define('Recommend_Us', 'Tip en ven');
define('Reviews', 'Testartikler');
define('Search', 'Søge');
define('Sections', 'Tema område');
define('Siteupdate', 'Site News');
define('Submit_News', 'Skriv artikel');
define('Surveys', 'Rundspørgelser');
define('Top', 'Topliste');
define('Topics', 'Temaer');
define('UserGuest', 'min Gæstebog');
define('Web_Links', 'Links');
define('Web_News', 'Internet News');
define('LinkMe', 'Link til os');
define('Userinfo', 'Brugerinfo');
define('User_Registration', 'Bruger tilmelding');
define('Gallery', 'Billedgalleri');
define('Avatar', 'min Avatar');
define('Banners', 'mine Banner');
define('Encyclopedia', 'Encyklopædi');
define('IcqList', 'Icq-Liste');
define('IrcChat', 'Chat');
define('Members_Web_Mail', 'min Web-Mail');
define('Stories_Archive', 'Artikel Archiv');
define('Themetest', 'Temaer');
define('User_Blocks', 'mine Blokke');
define('User_Fotoalbum', 'min Fotoalbum');
define('legal', 'betingelser');
// die Nachricht für den Begrüssungsblock
define('_NEWINSTALLMESSAGEBLOCKTITLE', 'Velkommen på din pragmaMx ' . MX_SETUP_VERSION_NUM . '');
define('_NEWINSTALLMESSAGEBLOCK', trim(addslashes('
<p>Hej</p>
<p>Når du kan læse denne meddelelse, synes det at din pragmaMx installation arbejder uden fejl. Tillykke med det!</p>
<p>Vi vil gerne sige tak, at du har besluttet dig til at teste vores pragmaMx. Vi håber på, at systemet kan opleve til all dine forventninger.</p>
<p>Yderligere udvidelser for dit pragmaMx system kan du finde på vores hjemmeside: <a href="http://www.pragmamx.org">http://pragmamx.org</a>.</p>
<p>Hvis du ikke direkte efter setup-rutinen har oprettet en administratorkonto, kan du gøre det <a href="' . adminUrl() . '"><strong>nu her</strong></a>.</p>
<p>Vi ønsker dig succes og glæde med vores pragmaMx system. Du kan bestemt nemt finde rundt og vi har i administrator område en dokumentation af systemet, kig venligst ind i det hvis du støtter på problemer.</p>
<p>Med venlig hilsen <br>pragmaMx Coding-Team</p>
')));

define('_DBUP_WAIT', 'Vent venligst');
define('_DBUP_MESSAGE', '
<p>Opsætning nu konfigurerer dit pragmaMx-System. </p>
<p>Tilpasningen af ​​de databasetabeller kan tage et godt stykke tid. Vent, indtil processen er færdig. Afslut eller ikke opdatere siden og heller ikke lukker browseren.</p>
');

// Blockbeschriftungen:
define('_BLOCK_CAPTION_MAINMENU', 'Hovedmenu;');
define('_BLOCK_CAPTION_INTERNAL', 'Internt');
define('_BLOCK_CAPTION_COMMUNITY', 'Community');
define('_BLOCK_CAPTION_OTHER', 'Yderligere');
define('_BLOCK_CAPTION_1', 'Setup-Alarm');
define('_BLOCK_CAPTION_2', 'Admin Menu;');
define('_BLOCK_CAPTION_3', 'Sprog');
define('_BLOCK_CAPTION_4', 'Login');
define('_BLOCK_CAPTION_5', 'Bruger Manual;');
define('_BLOCK_CAPTION_6', 'Hvem er online');
define('_BLOCK_CAPTION_7', 'FAQs/OSS');
define('_BLOCK_CAPTION_8', 'Rundspørgelse');
define('_BLOCK_CAPTION_9', 'pragmaMx-News');
define('_BLOCK_CAPTION_5A', 'Din personlig menu;');

/* Umgebungstest, äquivalent zu pmx_check.php */
define("_TITLE", " " . MX_SETUP_VERSION . "  environment test");
define("_ENVTEST", "Installations Tjek");
define("_SELECTLANG", "Vælg venligst et sprog");
define("_TEST_ISOK", "Okay, på dette system bør  " . MX_SETUP_VERSION . "  køre korrekt.");
define("_TEST_ISNOTOK", "Dette system opfylder ikke minimumsystemkravene for driften af  " . MX_SETUP_VERSION . " .");
define("_LEGEND", "Legende");
define("_LEGEND_OK", "<span>OK</span> - Alt i orden");
define("_LEGEND_WARN", "<span>Advarsel</span> - Uden denne funktion kan nogle funktioner af  " . MX_SETUP_VERSION . "  ikke bruges.");
define("_LEGEND_ERR", "<span>Fejl</span> - Denne funktion kræves af " . MX_SETUP_VERSION . " .");
define("_ENVTEST_PHPFAIL", "Den af  " . MX_SETUP_VERSION . "  påkrævede, minimum PHP version er %s. Din PHP version er: %s");
define("_ENVTEST_PHPOK", "Din PHP version er: %s");
define("_ENVTEST_MEMOK", "Din PHP hukommelse grænse er: %s");
define("_ENVTEST_MEMFAIL", "Din PHP hukommelse grænse er for lav til  " . MX_SETUP_VERSION . "  installationen. Minimumværdien er %s.");
define("_EXTTEST_REQFOUND", "Den påkrævede udvidelse '%s' eksisterer");
define("_EXTTEST_REQFAIL", "Udvidelsen '%s' er nødvendig til drift af  " . MX_SETUP_VERSION . " .");
define("_EXTTEST_GD", "GD bruges til billedredigering. Uden denne udvidelse, kan systemet ikke oprette f.eks. miniaturer af billedfiler.");
define("_EXTTEST_MB", "Multi byte streng bruges til håndtering af Unicode-tegn. Uden denne udvidelse, kan der muligvis være visningsfejl på visse specialtegn.");
// define("_EXTTEST_ICONV", "Iconv wird teilweise zur Zeichensatz-Konvertierung verwendet.  Ohne diese Erweiterung kann es evtl. zu Anzeigefehlern bei bestimmten Sonderzeichen kommen.");
define("_EXTTEST_IMAP", "IMAP bruges til at forbinde til POP3 og IMAP-servere.");
define("_EXTTEST_CURL", "CURL-funktionen forbedrer adgang til eksterne data.");
define("_EXTTEST_TIDY", "Når TIDY udvidelsen er aktiveret, kan HTML-output automatisk valideres. Dette kan fremskynde sidelayoutet i browseren og gør hjemmesiden W3C-kompatibel.");
define("_EXTTEST_XML", "XML-udvidelse er blandt andet nødvendig til at generere RSS-feed.");
define("_EXTTEST_RECFOUND", "Den anbefalede udvidelse '%s' eksisterer");
define("_EXTTEST_RECNOTFOUND", "Den anbefalede udvidelse '%s' eksisterer ikke. <span class=\"details\">%s</span>");

define("_VERCHECK_DESCRIBE", "Denne liste over filer / mapper er forældede og ikke længere bruges af " . MX_SETUP_VERSION . ". De kan forårsage under visse omstændigheder interferens og sikkerhedsspørgsmål. Du bør derfor udgå nødvendigvis.");
define("_VERCHECK_DEL", "Sletning af filer og mapper");
define("_FILEDELNOTSURE", "Dette trin kan nu fortsætte og indhente senere i versionsstyring af pragmaMx-system.");
define("_ERRMSG2", "De følgende filer / mapper kan ikke slettes automatisk. Du kan gøre det senere bruger version forvaltning af pragmaMx-System.");
/// old !! define("_ERRMSG2", "Følgende filer/mapper kunne ikke slettes af systemet.');

define("_PDOTEST_OK", "PDO-database driver (%s) er tilgængelig");
define("_PDOTEST_FAIL", "Det blev fundet nogen brugbar PDO-database-driver (f.eks %s)");
define("_EXTTEST_PDO", "PDO forlængelse vil være den fremtidige standard database driver til pragmaMx. Forlængelsen bør være til rådighed så hurtigt som muligt.");
define("_EXTTEST_ZIP", "Zip-funktionalitet bruges af nogle add-on moduler og skal være tilgængelige.");

define("_DBCONNECT","databaseforbindelse");
define("_EXTTEST_FILE_FAIL", "kan ikke skrive : %s");
define("_EXTTEST_FILE_OK", "alle filer til rådighed.");
?>