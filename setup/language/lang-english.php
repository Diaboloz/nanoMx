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
 * english language file by:
 * pragmaMx Developer Team
 */

defined('mxMainFileLoaded') or die('access denied');

/* Datestrings und locale Einstellungen: */
$old_setlocale = setlocale(LC_TIME, 0);
$locale = array('en_GB.UTF-8', 'en_GB.UTF8', 'en_GB', 'en', 'eng', 'english-uk', 'uk', 'GB', 'GBR', '826', 'CTRY_UNITED_KINGDOM', 'en_GB.ISO-8859-1');
define('_SETLOCALE', setlocale(LC_TIME, $locale));
setlocale(LC_TIME, $old_setlocale);
define("_DOC_LANGUAGE", "en");
define("_DOC_DIRECTION", "ltr");
define('_DATESTRING', '%d-%b-%Y');

/**
 * Setup Optionen zur Auswahl, siehe setup-settings.php
 */
// Neuinstallation
define('_SETUPOPTION_NEW', 'New installation');
define('_SETUPOPTION_NEW_DESC', 'You will launch a new installation of pragmaMx, the data already present in your database will be preserved.');
// Update
define('_SETUPOPTION_UPDATE', 'Update of an existing installation');
define('_SETUPOPTION_UPDATE_DESC', 'The script of installation will update a preceding and functional version of pragmaMx. Script can also convert the data of phpNuke, vkpMx and clones of phpNuke.');
// Setupschritte
define('_STEP_SELECT', 'Selection, Setup or update');
define('_STEP_ISINCORRECT', 'Interrogation of safety');
define('_STEP_LICENSE', 'Licence of use');
define('_STEP_BACKUP', 'Backup of the database');
define('_STEP_UPDATE', 'Update of the database');
define('_STEP_DELFILES', 'Removal of the obsolete files');
define('_STEP_FINISHEDINSTALL', 'Finished installation');
define('_STEP_DBSETTINGS', 'Configuration of the access to the database');
define('_STEP_DBSETTINGSCREATE', 'Configuration of the database / Creation of the base');
define('_STEP_MORESETTINGS', 'Complementary adjustments');
define('_STEP_MORESETTINGSCHECK', 'Checking and validation of the adjustments');
define('_STEP_FINISHEDUPDATE', 'Finished update');
define('_STEP_CONFIGURATION', 'Update of the configuration');
define('_HELLOINSTALL', 'Installation of ' . MX_SETUP_VERSION);
define('_HELLOINSTALL2', 'Thank you for choosing our ' . preg_replace('#[[:space:]]#', '&nbsp;', MX_SETUP_VERSION) . '. <br /><br /> Please read the <a href="' . _MXDOKUSITE . '">online install-documentation</a> before you proceed. <br /> If you have done so, carry on.');
define('_WHATWILLYOUDO', 'You have the possibility of selecting between different setup-methods. The method recommended by the Setup Script is already activated. Select please only another method, if you are sure.<br /><br />What would you like to do?');
define('_OLDVERSION_ERR1', 'Your selected setup method does not correspond to the determined standard default,<br />this can to problems or even overrun lead.');
define('_OLDVERSION_ERR2', 'Are you sure that you want to execute the method &quot;<em>' . @constant($GLOBALS['opt'][@$_REQUEST['setupoption']]['name']) . '</em>&quot;?');
define('_CONFIGSAVEMESS', 'To finalize, the file of configuration <em>' . basename(FILE_CONFIG_ROOT) . '</em> will be updated.');
define('_CONFIG_OK_NEW', 'The configuration file <em>' . basename(FILE_CONFIG_ROOT) . '</em> was successfully created.');
define('_CONFIG_OK_OLD', 'The configuration file <em>' . basename(FILE_CONFIG_ROOT) . '</em> already was on the current status and correct.');
define('_CONFIG_ERR_1', 'The file <em>' . basename(FILE_CONFIG_ROOT) . '</em> is write-protected!');
define('_CONFIG_ERR_2', 'Writing into the file <em>' . basename(FILE_CONFIG_ROOT) . '</em> was not successful.');
define('_CONFIG_ERR_3', 'The folder <em>' . PMX_BASE_PATH . '</em> is write-protected! Adjust rights please!');
define('_CONFIG_ERR_4', 'The file <em>' . basename(FILE_CONFIG_ROOT) . '</em> could not be created for undefined reasons.');
define('_CONFIG_ERR_5', 'The file in <em>' . basename(FILE_CONFIG_ROOT) . '</em> is available, but cannot not be read.');
define('_CONFIG_ERR_6', 'The file in <em>' . basename(FILE_CONFIG_ROOT) . '</em> is available, but the data were not correctly written.');
define('_CONFIG_ERR_8', 'The configuration file <em>' . basename(FILE_CONFIG_ROOT) . '</em> was not correctly created, but the database connection is ok. You can absolutely check and if necessary again stop the system adjustments with the installation continue, should however thereafter in the administration menu.');
define('_CONFIG_BACK', 'A copy of the existing configuration file was successfully created, under the following name:');
define('_CONFIG_CREATE', 'Please create a new script file on the basis of the displayed php-code. Named this file <em>' . basename(FILE_CONFIG_ROOT) . '</em> and copy it in the following main-folder (' . dirname(basename(FILE_CONFIG_ROOT)) . ') of your pragmaMx installation.<br />Make sure that the complete source text 1:1 in this file is really stored.<br /><br />Afterwards, you can continue with the installation.');
define('_CONFIG_BUTTONMAN', 'Configuration file manually create');
define('_CURRENTSTATUS', 'Current installation status');
define('_THEREERROR', 'Errors occurred');
define('_WILL_CREATE_TABLES', 'In the next step the database tables will be created and updated, this could take a few moments');
define('_WILL_CREATE_BACKUP', 'If you uses the backup option, setup tries to create a complete backup of the selected database before creating the tables.');
define('_CONTINUE_WITHOUTDBBACKUP', 'Continue without backup');
define('_CONTINUE_WITHDBBACKUP', 'Continue with backup');
define('_DBFOLLOWERRORS', 'The following errors occurred');
define('_NODBERRORS', 'No errors occurred.');
define('_DBNOTEXIST', 'The selected database does not exist on the server.');
define('_DBNOTSELECT', 'You have no database selected.');
define('_DBNOACCESS', 'Connection with the database was refused.');
define('_DBOTHERERR', 'An error occurred at the time of connection with the database.');
define('_DBVERSIONFALSE', 'Sorry, but your mySQL version is too old. In order to install pragmaMx, version %s of the MySQL server is needed at least.');
define('_NOT_CONNECT', 'There is no connection to the database.');
define('_NOT_CONNECTMORE', 'Please make sure that the file config.php exists in it\'s prior version and that the information of connection to the database are correct.');
define('_DB_CONNECTSUCCESS', 'The connection to the database <em>%s</em> was successfully made.');
define('_CORRECTION', 'Correction');
define('_REMAKE', 'Repeat');
define('_IGNORE', 'Ignore and continue');
define('_DONOTHING', 'Ignore');
define('_DBARETABLES', '<li>Tables are already present in the choosen database.</li><li>A backup of the database is strongly recommended.</li>');
define('_DBARENOTABLES', '<li>The selected database being empty, there is no need to do a backup.</li>');
define('_SUBMIT', 'Continue');
define('_OR', 'Or');
define('_YES', 'Yes');
define('_NO', 'No');
define('_GOBACK', 'Back');
define('_CANCEL', 'Cancel');
define('_FILE_NOT_FOUND', 'File not found, or not readable.');
define('_ACCEPT', 'Do you except the licence agreement?');
define('_START', 'Starting page');
define('_INTRANETWARNING', 'Intranet should only be turned on if you cannot access pragmaMx with a fully-qualified host name (eg www.mysite.com). It is not recommended to run in this mode unless you are behind a firewall and users outside the firewall cannot gain access to your pragmaMx site');
define('_PRERR11', 'The two prefixes must start with a letter, they can contain numbers, letters and the underlined sign (_) but those should not have an overall length of more than ' . PREFIX_MAXLENGTH . ' characters.');
define('_PRERR12', 'The new prefix does not have a value.<br />Please indicate a prefix.');
define('_PRERR13', 'The new prefix does correspond to the standard standart pragmaMx or phpNuke. Please use another prefix for reasons of safety.');
define('_PRERR14', 'You use characters prohibited for your prefix. Use only letters, figures and the underlined character (_), whereby the prefix may not begin with a number.<br />Please correct and use another prefix.');
define('_PRERR15', 'The prefix cannot start with a number. <br />Please correct and use another prefix.');
define('_PRERR16', 'The new prefix of the tables is too long, the prefix should not have an overall length of more than ' . PREFIX_MAXLENGTH . ' characters.<br />Please shorten your prefix.');
define('_PRERR17', 'There are already %d tables with the new prefix.<br />Please use another prefix.');
define('_PRERR18', 'The new prefix of the users does not have a value.<br />Please indicate a prefix of the users.');
define('_PRERR19', 'You use characters prohibited for your prefix of the users. Use only letters, figures and the underlined character (_), whereby the prefix may not begin with a number.<br />Please correct and use another prefix of the users.');
define('_PRERR20', 'The prefix of the users cannot start with a number.<br />Please correct and use another prefix of the users.');
define('_PRERR21', 'The new prefix of the users is too long, the prefix should not have an overall length of more than ' . PREFIX_MAXLENGTH . ' characters.<br />Please shorten your prefix of the users.');
define('_PRERR22', 'There are already tables with the new prefix of the users.<br />Please use another prefix of the users.');
define('_SUPPORTINFO', 'Help and support for your system are available here: <a href="' . _MXSUPPORTSITE . '">' . _MXSUPPORTSITE . '</a>');
define('_DOKUINFO', 'Documentation on line is available here: <a href="' . _MXDOKUSITE . '">' . _MXDOKUSITE . '</a>');
define('_NOBACKUPCREATED', 'A backup of the database was not created.');
define('_HAVE_CREATE_DBBACKUP', 'Your database was backuped as file:');
define('_HAVE_CREATE_BACKUPERR_1', 'Database backup-error!');
define('_HAVE_CREATE_BACKUPERR_2', 'If this database contains data please make sure,<br />that you have a propper backup <strong>before</strong> continue!');
define('_SETUPHAPPY1', 'Congratulations');
define('_SETUPHAPPY2', 'Your system from now on is completely installed, with the next click you will be automatically redirected towards the panel of administration.');
define('_SETUPHAPPY3', 'Here you should first examine the basic adjustments and store them again.');
define('_DELETE_FILES', 'If your system runs fine, please delete the &quot;<em>' . basename(dirname(__DIR__)) . '</em>&quot;-directory.<br /><strong>This could be otherwise a security-problem!</strong>');
define('_GET_SQLHINTS', 'Below, the list of all the requests SQL which were imported during the process of conversion/integration');
define('_DATABASEISCURRENT', 'The structure of the database was already present, modifications were useless.');
define('_SEEALL', 'View all');
define('_DB_UPDATEREADY', 'Conversion/integration of the tables is finished.');
define('_DB_UPDATEFAIL', 'Conversion/integration of the tables could not be done completely.');
define('_DB_UPDATEFAIL2', 'The following important tables of the system are missing: ');
define('_BACKUPPLEASEDOIT', 'It is strongly recommended to carry out a complete backup of the database in front of the update.');
define('_ERRMSG1A', 'Error: One of the files necessary to the update is missing, want to make sure that the following file is quite present:');
define('_YEAHREADY2', 'Otherwise your pragmaMx now is in the newest condition.');
define('_SERVERMESSAGE', 'Server message');
define('_ERRDBSYSFILENOFILES', 'Tables of the system couldn\'t be integrated/checked, because no files of definitions were found in the directory <em>' . PATH_SYSTABLES . '</em>.');
define('_ERRDBSYSFILEMISSFILES_1', 'Unable to integrate/check all the tables of the system.');
define('_ERRDBSYSFILEMISSFILES_2', 'In the directory <em>' . PATH_SYSTABLES . '</em>,  the following files of definitions are missing');
define('_THESYSTABLES_1', 'Unable to integrate/check the table(s) of the system <strong>%s</strong>, because it\'s/their file(s) in the directory ' . PATH_SYSTABLES . 'could not be loaded.');
define('_THESYSTABLES_2', 'The table(s) of the system <strong>%s</strong> was/were not integrated/checked.');
define('_SYSTABLECREATED', '%d tables of the system were integrated/checked.');
define('_MODTABLESCREATED', 'Tables of %d modules were integrated/checked.');
define('_NOMODTABLES', 'No tables of modules were integrated/checked.');
define('_STAT_THEREWAS', '');
define('_STAT_TABLES_CREATED', 'tables were created.');
define('_STAT_TABLES_RENAMED', 'tables were renamed.');
define('_STAT_TABLES_CHANGED', 'tables were changed.');
define('_STAT_DATAROWS_CREATED', 'data records were inserted/changed.');
define('_STAT_DATAROWS_DELETED', 'data records were deleted.');
define('_MOREDEFFILEMISSING', 'The file (<em>' . @ADD_QUERIESFILE . '</em>) with additional SQL instructions is missing!');
define('_SETUPMODNOTFOUND1', 'Setup module <strong>%s</strong> not found!');
define('_ERROR', 'Error');
define('_ERROR_FATAL', 'Fatal error');
define('_SETUPCANCELED', 'Setup was aborted!');
define('_GOTOADMIN', 'Go to the administration menu');
define('_DBSETTINGS', 'Input here the database access settings. The Setup can be continued only, with a correctly created database connection. You normally receive the access data from your Webspace Provider.');
define('_DBNAME', 'Database name');
define('_DBPASS', 'Database password');
define('_DBSERVER', 'Database server');
define('_DBTYP', 'Database type');
define('_DBUSERNAME', 'Database username');
define('_DBCREATEQUEST', 'Do you want to try to create the database &quot;<em>' . @$_REQUEST['dbname'] . '</em>&quot;?');
define('_DBISCREATED', 'The database &quot;<em>' . @$_REQUEST['dbname'] . '</em>&quot; was successfully created.');
define('_DBNOTCREATED', 'An error occurred while creating the database &quot;<em>' . @$_REQUEST['dbname'] . '</em>&quot;.'); # settings
define('_PREFIXSETTING', 'The prefixes are used to make the distinction between the various tables, in particular if you wish to use several pragmaMx within the same database. The prefix of the table of the users allows the common use of the user data in several pragmaMx distincts. If you do not wish to use this functionality, leave the same prefix for the table of the users.');
define('_PREFIX', 'Database table prefix');
define('_USERPREFIX', 'User table prefix');
define('_DEFAULTLANG', 'Default language');
define('_INTRANETOPT', 'Run on intranet');
define('_ADMINEMAIL', 'Administrator email');
define('_SITENAME', 'Site name');
define('_STARTDATE', 'Page startdate');
define('_CHECKSETTINGS', 'Please check your settings!');
define('_PLEASECHECKSETTINGS', 'Please check the current settings.<br />Wenn all data are correct, you can continue the installation.<br />Otherwise you have still opportunity the specification to correct.');
define('_HAVE_CREATE_TABLES', 'Tables created.');
define('_HAVE_CREATE_TABLES_7', 'The tables necessary to the system were integrated without error. The routine of installation can continue, but various functions of the system may cause problems.');
define('_HAVECREATE_TABLES_ERR', 'The database could not be created completely. The installation failed.');
define('_CREATE_DB', 'Create database');
define('_DELETESETUPDIR', 'Click here to render this setup useless. For that purpose the file index.php will be renamed and access to the directory will be denied with .htaccess file. <em>(doesn\'t work on all servers.)</em>');
// add for fieldset
define('_PREFIXE', 'Prefix');
define('_SITE__MORESETTINGS', 'Site settings');
define('_SERVER', 'Server data');
define('_BACKUPBESHURE', 'Before following adjustment of database tables, please make sure that your current database backup is up to date.');
define('_BACKUPBESHUREYES', 'Yes, my current database backup is up to date.');
define('_BACKUPBESHUREOK', 'Please confirm that your current database backup is up to date.');
// Modulbezeichnungen
define('Your_Account', 'Account');
define('News', 'Articles');
define('blank_Home', 'Home');
define('Content', 'Content');
define('Downloads', 'Downloads');
define('eBoard', 'Forums');
define('FAQ', 'FAQ');
define('Feedback', 'Feedback');
define('Guestbook', 'Guestbook');
define('Impressum', 'Impressum');
define('Kalender', 'Events');
define('Statistics', 'Statistics');
define('Members_List', 'Members list');
define('My_eGallery', 'Media gallery');
define('Newsletter', 'Newsletter');
define('Private_Messages', 'Private messages');
define('Recommend_Us', 'Recommend us');
define('Reviews', 'Reviews');
define('Search', 'Search');
define('Sections', 'Sections');
define('Siteupdate', 'Site news');
define('Submit_News', 'Submit news');
define('Surveys', 'Surveys');
define('Top', 'Toplist');
define('Topics', 'Topics');
define('UserGuest', 'User guestbook');
define('Web_Links', 'Links');
define('Web_News', 'Internet news');
define('LinkMe', 'Link to us');
define('Userinfo', 'Userinfo');
define('User_Registration', 'User registration');
define('Gallery', 'Picturegallery');
define('Avatar', 'Avatar');
define('Banners', 'Banners');
define('Encyclopedia', 'Encyclopedia');
define('IcqList', 'Icqlist');
define('IrcChat', 'Chat');
define('Members_Web_Mail', 'Webmail');
define('Stories_Archive', 'Stories archive');
define('Themetest', 'Themes');
define('User_Blocks', 'Blocks');
define('User_Fotoalbum', 'Fotoalbum');
define('legal', 'Conditions of use');
// die Nachricht für den Begrüssungsblock
define('_NEWINSTALLMESSAGEBLOCKTITLE', 'Welcome on your pragmaMx ' . MX_SETUP_VERSION_NUM . '');
define('_NEWINSTALLMESSAGEBLOCK', trim(addslashes('
<p>Welcome,</p>
<p>if you can read this message then pragmaMx seems to function without error, congratulations still.</p>
<p>We want initially to cordially thank you for having decided to use our system pragmaMx in an obviousness to take slightly more precisely. We hope thus that our CMS will answer all your waitings.</p>
<p>Additional modules dedicated to your system are also available on our Internet site: <a href="http://www.pragmamx.org">http://pragmamx.org</a>.</p>
<p>If you did not create an account administrator for your pragmaMx during the installation, please <a href="' . adminUrl() . '"><strong>do so now</strong></a>.</p>
<p>We wish you much pleasure by exploring your system. So that you more easily get along, within the administrator area a short documentation of the system has been deposited, please look at in any case once you inside.</p>
<p>Your pragmaMx coding team</p>
')));
define('_DBUP_WAIT', 'Please Wait');
define('_DBUP_MESSAGE', '
<p>Setup will now configure your pragmaMx-System. </p>
<p>The adaptation of the database tables can take quite a while. Please wait until the process is complete. Quit or do not refresh the page and also not close the browser.</p>
');

// Blockbeschriftungen:
define('_BLOCK_CAPTION_MAINMENU', 'Main menu');
define('_BLOCK_CAPTION_INTERNAL', 'Internals');
define('_BLOCK_CAPTION_COMMUNITY', 'Community');
define('_BLOCK_CAPTION_OTHER', 'Other');
define('_BLOCK_CAPTION_1', 'Setup alarm');
define('_BLOCK_CAPTION_2', 'Admin menu');
define('_BLOCK_CAPTION_3', 'Language');
define('_BLOCK_CAPTION_4', 'Login');
define('_BLOCK_CAPTION_5', 'User menu');
define('_BLOCK_CAPTION_6', 'Who is online');
define('_BLOCK_CAPTION_7', 'FAQs');
define('_BLOCK_CAPTION_8', 'Surveys');
define('_BLOCK_CAPTION_9', 'pragmaMx news');
define('_BLOCK_CAPTION_5A', 'Your personal menu.');

/* Umgebungstest, äquivalent zu pmx_check.php */
define("_TITLE", " " . MX_SETUP_VERSION . "  environment test");
define("_ENVTEST", "Environment test");
define("_SELECTLANG", "Please select a language");
define("_TEST_ISOK", "OK, this system can run  " . MX_SETUP_VERSION . " ");
define("_TEST_ISNOTOK", "This system does not meet  " . MX_SETUP_VERSION . "  system requirements");
define("_LEGEND", "Legend");
define("_LEGEND_OK", "<span>ok</span> - All OK");
define("_LEGEND_WARN", "<span>warning</span> - Not a deal breaker, but it's recommended to have this installed for some features to work");
define("_LEGEND_ERR", "<span>error</span> -  " . MX_SETUP_VERSION . "  require this feature and can't work without it");
define("_ENVTEST_PHPFAIL", "Minimum PHP version required in order to run  " . MX_SETUP_VERSION . "  is PHP %s. Your PHP version: %s");
define("_ENVTEST_PHPOK", "Your PHP version is: %s");
define("_ENVTEST_MEMOK", "Your memory limit is: %s");
define("_ENVTEST_MEMFAIL", "Your memory is too low to complete the installation. Minimal value is %s, and you have it set to: %s");
define("_EXTTEST_REQFOUND", "Required extension '%s' found");
define("_EXTTEST_REQFAIL", "Extension '%s' is required in order to run  " . MX_SETUP_VERSION . " .");
define("_EXTTEST_GD", "GD is used for image manipulation. Without it, system is not able to create thumbnails for files or manage avatars, logos and project icons");
define("_EXTTEST_MB", "MultiByte String is used for work with Unicode. Without it, system may not split words and string properly and you can have weird question mark characters in Recent Activities for example");
// define("_EXTTEST_ICONV", "Iconv is used for character set conversion. Without it, system is a bit slower when converting different character set");
define("_EXTTEST_IMAP", "IMAP is used to connect to POP3 and IMAP servers. Without it, Incoming Mail module will not work");
define("_EXTTEST_CURL", "This functions optimizes the access to external data.");
define("_EXTTEST_TIDY", "When TIDY extension is active, the HTML output will be validated automatically. This can speed up the page layout in the browser and make the website W3C compliant.");
define("_EXTTEST_XML", "The XML extension is needed among others for the creation of RSS feeds.");
define("_EXTTEST_RECFOUND", "Recommended extension '%s' found");
define("_EXTTEST_RECNOTFOUND", "Extension '%s' was not found. <span class=\"details\">%s</span>");

define("_VERCHECK_DESCRIBE", "This list of files / folders are outdated and are no longer used by ". MX_SETUP_VERSION.". You can cause interference and safety problems. You should therefore be deleted necessarily.");
define("_VERCHECK_DEL", "delete files and folders");
define("_FILEDELNOTSURE", "This step can now proceed and catch up later in the version management of the pragmaMx-System.");
define("_ERRMSG2", "The following files / folders could not be deleted automatically. You can do later in the version management of the pragmaMx system.");
/// OLD !! define("_ERRMSG2", "The following files and/or directories could not be removed automatically.');

define("_PDOTEST_OK", "PDO database driver (% s) available");
define("_PDOTEST_FAIL", "It found no useful PDO database driver (for example,%s)");
define("_EXTTEST_PDO", "PDO extension will be the future standard database driver for pragmaMx. The extension should be available as soon as possible.");
define("_EXTTEST_ZIP", "The Zip functionality is used by some add-on modules and should be available.");

define("_DBCONNECT","Database connection");
define("_EXTTEST_FILE_FAIL", "can not write : %s");
define("_EXTTEST_FILE_OK", "All file access available.");
?>