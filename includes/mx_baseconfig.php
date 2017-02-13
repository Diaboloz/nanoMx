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
 * $Revision: 206 $
 * $Author: PragmaMx $
 * $Date: 2016-09-12 13:33:26 +0200 (Mo, 12. Sep 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * Session-Konfiguration
 * DIESE WERTE BITTE NUR VERÄNDERN, WENN SIE WISSEN WAS SIE TUN !!
 * Halten Sie vorher unbedingt Rücksprache mit dem Support-Team.
 * http://www.pragmamx.de/
 * weitere Informationen auch:
 * http://www.dclp-faq.de/ch/ch-version4_session.html
 * http://www.php.net/manual/de/ref.session.php
 */

/**
 * Folgendes wird vom System generiert, kann aber bei Bedarf frei geändert
 * werden verschiedene Sicherheitsfunktionen werden dadurch aber abgeschwächt !
 * diese Optionen sind nützlich, wenn mehrere mx-Portale auf einer Domain laufen
 */
// define('PMX_BASE_PATH'           , '/');
// define('MX_SESSION_NAME'          , 'mxSessid');
// define('MX_SAFECOOKIE_NAME_ADMIN' , 'mxAdmin');
// define('MX_SAFECOOKIE_NAME_USER'  , 'mxUser');
// define('MX_SESSION_DBTABLE'       , $prefix.'_sys_session');
// define('MX_SESSION_VARPREFIX'     , 'mxSV');
$vkpInactiveMins = (empty($vkpInactiveMins)) ? (int)$vkpInactiveMins : 5; # Standard 5 Minuten
define("MX_SETINACTIVE_MINS", $vkpInactiveMins * 60);
unset($vkpInactiveMins);

$vkpSessLifetime = (isset($vkpSessLifetime)) ? $vkpSessLifetime : 30;
define("MX_COOKIE_LIFETIME", (empty($vkpSessLifetime)) ? 0 : $vkpSessLifetime * 24 * 60 * 60);

$vkpSessLifetime = (empty($vkpSessLifetime)) ? 0.25 : $vkpSessLifetime;
define("MX_SESSION_LIFETIME", $vkpSessLifetime * 24 * 60 * 60); # Standard 6 Stunden
unset($vkpSessLifetime);

/* die Sessionlifetime für nicht-User / nicht-Admins in Sekunden */
define('MX_SESSION_LIFETIME_NOUSER', '1440');

/**
 * ENDE Session-Konfiguration
 */

/* Standard chmods */
// -- ACHTUNG, das muss für die Funktion chmod() mit octdec() noch umgewandelt werden
define('PMX_CHMOD_LOCK', octdec('0444'));
define('PMX_CHMOD_NORMAL', octdec('0644'));
define('PMX_CHMOD_UNLOCK', octdec('0666'));
define('PMX_CHMOD_FULLOCK', octdec('0400'));
define('PMX_CHMOD_FULLUNOCK', octdec('0777'));

define('PMX_GROUP_ID_ANONYMOUS', -1); //  Anonyme       , nicht angemeldete Benutzer
define('PMX_GROUP_ID_USER', 0); //  Standarduser  , Standardlevel für alle angemeldeten User
define('PMX_GROUP_ID_ADMIN', -2); //  Administrator , Administratoren mit beschränktem Zugriff
define('PMX_GROUP_ID_SYSADMIN', -3); //  Administrator , System-Administratoren mit Vollzugriff

/**
 * change this to receive email on each config.php change,
 * 1 = send email an hide checkbox, 0 = send if checkbox is checked
 */
$mxSendConfig = 0;

/* prefix um die Blöcke vom News-Modul zu identifizieren */
define('MX_NEWSBLOCK_PREFIX', 'block-vkp_News_');

/* der Fallback-Name für die erste Usergruppe */
if (!defined('MX_FIRSTGROUPNAME')) define('MX_FIRSTGROUPNAME', 'User');

/* PHP-Version ermitteln, nicht verändern !!!!! */
define('MX_PHP_VERSION', intval(str_replace('.', '', PHP_VERSION)));

/* Platzhalter um mehrseitige Inhalte zu splitten */
define('PMX_PAGE_DELIMITER', '<!-- pagebreak -->');

/* HTML-Tags, die immer im Request ausgefiltert werden (mxSecureValue) */
$mxBadHtmlTags = array('script', 'frameset', 'frame', 'object', 'meta', 'applet', 'link', 'embed');

/**
 * folgende Funktion ausführen :)
 */
mxdefinepath();

//$GLOBALS['nukeurl'] = PMX_HOME_URL;

/**
 * die Variable mxSkipSqlDetect deklarieren, sie kann verwendet, um bei
 * bestimmten Datenbankanfragen den sql_injection Schutz abzuschalten,
 * z.B. bei allen Abfragen, wo keine Benutzereingaben ausgewertet werden
 * Sie wird in der Funktion sql_query immer wieder auf FALSE gesetzt
 */
global $mxSkipSqlDetect;
$mxSkipSqlDetect = false;

$GLOBALS['mxSecureKey'] = (isset ($GLOBALS['mxSecureKey'])) ? $GLOBALS['mxSecureKey'] : md5($GLOBALS['dbuname'] . '%#f4S' . __file__);

/**
 * Pfade ermitteln etc.
 * in Funktion gekapselt, um die Variablen nicht in den globalen scope zu kopieren ;)
 */
function mxdefinepath()
{
    /* Pfade ermitteln etc. */
	
    $info_script_name = pathinfo($_SERVER['SCRIPT_NAME']);
    $scriptpath = str_replace(DS, '/', realpath(dirname($_SERVER['SCRIPT_FILENAME'])));
    $basepath = str_replace(DS, '/', PMX_REAL_BASE_DIR);

    /* just the subfolder part between <installation_path> and the page */
    $scriptpath = substr($scriptpath, strlen($basepath));
	/* neu, da subst unter PHP7 anders funktioniert */
	$scriptpath =($scriptpath=="")?FALSE:$scriptpath;
	
    $rootpath = str_replace(DS, '/', $info_script_name['dirname']);
    /*we subtract the subfolder part from the end of <installation_path>, leaving us with just <installation_path> :)*/
    if ($scriptpath !== false) {
        $rootpath = str_replace('//', '/', substr($rootpath, 0, - strlen($scriptpath)) . '/');
        $scriptpath = trim($scriptpath, '/') . '/';
    } else {
        $rootpath = str_replace('//', '/', $rootpath . '/');
        $scriptpath = '';
    }
	
	
    /* der wichtigste Pfad: zum mx-Root */
    define('PMX_BASE_PATH', $rootpath);
	
    /**
     * verschiedene absolute Pfade zu Systemordnern definieren. Alle hier
     * generierten Pfade müssen ohne Slash am Ende sein!
     */

    /* zur Abwärtskompatibilität */
    //define('MX_DOC_ROOT', PMX_REAL_BASE_DIR); // deprecated!
    //define('MX_CRYPTEDDIR', 'includes'); // deprecated!

    /**
     * verschiedene relative Pfade zu Systemordnern definieren. Alle hier
     * generierten Pfade müssen MIT Slash am Ende sein!
     */

    /* Ordner mit den Systemdateien */
    define('PMX_SYSTEM_PATH', 'includes/');

    /* Ordner mit den Administrationsdateien */
    define('PMX_ADMIN_PATH', 'admin/');

    /* Ordner mit den Administrations Modulen */
    define('PMX_ADMINMODULES_PATH', PMX_ADMIN_PATH . 'modules/');

    /* Ordner mit dynamischen Inhalten (Logdatein, Cache, etc.) */
    define('PMX_DYNADATA_PATH', 'dynadata/');

    /* Ordner mit Session Inhalten  */
    define('PMX_SESSDATA_PATH', PMX_DYNADATA_DIR . '/session/');

    /* Ordner mit dynamischen Medien (Bilder, Dokumente, etc.) */
    define('PMX_MEDIA_PATH', 'media/');

    /* Ordner mit den Systemdateien für die HTML-Ausgabe (view) */
    define('PMX_LAYOUT_PATH', 'layout/');

    /* Ordner mit den Modulen */
    define('PMX_MODULES_PATH', 'modules/');

    /* Ordner mit den Themes */
    define('PMX_THEMES_PATH', 'themes/');

    /* Ordner mit den Bildchen */
    define('PMX_IMAGE_PATH', 'images/');

    /* Ordner mit Standard Javascripten */
    define('PMX_JAVASCRIPT_PATH', PMX_SYSTEM_PATH . 'javascript/');

    /* das aktuelle Verzeichnis !ohne slash am Ende! */
    define('PMX_CURRENTSCRIPT_PATH', $scriptpath);

    /* REQUEST_URI kann auf manchen Servern nicht verfügbar sein, z.B. IIS */
    /* bzw. durch ungültige Parameter oder mod_rewrite verfälscht sein */
    switch (true) {
        case !isset($_SERVER['REQUEST_URI']):
            $bad_request_uri = true;
            $error_request = false;
            break;
        case !($currequest = @parse_url($_SERVER['REQUEST_URI'])):
            $bad_request_uri = true;
            $error_request = true;
            break;
        default:
            $info_request_uri = pathinfo($_SERVER['REQUEST_URI']);
            /* x. verhindert "empty delimiter" Meldung, falls leere Werte */
            $bad_request_uri = @strpos('x' . $info_request_uri['basename'], 'x' . $info_script_name['basename']) !== 0;
            $error_request = false;
            break;
    }

    if ($bad_request_uri || !isset($_SERVER['QUERY_STRING'])) {
        if (isset($_POST['name'])) {
            $_GET['name'] = $_POST['name'];
        }
        $querystring = '';
        if (count($_GET)) {
            $parts = array();
            foreach ($_GET as $key => $value) {
                if (is_scalar($value)) {
                    if ($error_request) {
                        $value = rawurlencode($value);
                        $_GET[$key] = $value;
                    }
                    $parts[$key] = "{$key}={$value}";
                }
            }
            $querystring = implode('&', $parts);
        }
        $_SERVER['REQUEST_URI'] = str_replace('//', '/', '/' . trim($info_script_name['dirname'] . '/' . $info_script_name['basename'] . '?' . $querystring, ' /?&'));
        $_SERVER['QUERY_STRING'] = $querystring;
    }

    $server = $_SERVER['HTTP_HOST'];
    $proto = (!empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off' || strtolower($_SERVER['HTTPS']) == 'on'))? 'https://' : 'http://';

    if (!defined('PMX_HOME_URL')) {
        // ohne slash, entspricht $nukeurl
        define('PMX_HOME_URL' , trim($proto . $server . PMX_BASE_PATH, ' .;/:\\'));
    }

    /* folgende nur noch zur Abwärtskompatibilität mit alten Modulen */
    define('MX_COOKIE_PATH', PMX_BASE_PATH); // Pfad, ausgehend vom document-root, ohne slashes
    define('MX_BASE_URL', PMX_HOME_URL . '/'); // z.B. für URL in mails
    define('MX_HOME_URL', PMX_HOME_URL); // ohne slash, entspricht $nukeurl
    define('MX_ROOT_DIR', '/' . trim(PMX_BASE_PATH, ' .;/:\\')); // Pfad, ausgehend vom document-root
    define('MX_BASE_URI', MX_ROOT_DIR); // Pfad, ausgehend vom document-root

    /* versch. Servervariablen prüfen und neu initialisieren */

    /* remote Adresse "cleanen" */
    switch (true) {
        case function_exists('filter_var'):
            $_SERVER['REMOTE_ADDR'] = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
            break;
        case preg_match('#^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}(:\d{1,5})?$#', $_SERVER['REMOTE_ADDR']):
        // case preg_match('#(^|\s|(\[))(::)?([a-f\d]{1,4}::?){0,7}(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}(?=(?(2)\]|($|\s|(?(3)($|\s)|(?(4)($|\s)|:\d)))))|((?(3)[a-f\d]{1,4})|(?(4)[a-f\d]{1,4}))(?=(?(2)\]|($|\s))))(?(2)\])(:\d{1,5})?#', $_SERVER['REMOTE_ADDR']):
        case preg_match('#^(((?=(?>.*?::)(?!.*::)))(::)?|([\dA-F]{1,4}(\2::|:(?!$)|$)|\2))(?4){5}((?4){2}|(25[0-5]|(2[0-4]|1\d|[1-9])?\d)(\.(?7)){3})\z#i', $_SERVER['REMOTE_ADDR']):
            break;
        default:
            $_SERVER['REMOTE_ADDR'] = '';
    }
    if (!$_SERVER['REMOTE_ADDR'] || $_SERVER['REMOTE_ADDR'] == '::') {
        $_SERVER['REMOTE_ADDR'] = '0.0.0.0';
    }
    // wenn leer, einfach die IP verwenden
    $_SERVER['REMOTE_HOST'] = strip_tags((empty($_SERVER['REMOTE_HOST'])) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['REMOTE_HOST']);
    // da kann ja sonst was kommen...
    $_SERVER['HTTP_USER_AGENT'] = strip_tags($_SERVER['HTTP_USER_AGENT']);
    // referer gibts nicht auf allen Servern
    $_SERVER['HTTP_REFERER'] = strip_tags((!isset($_SERVER['HTTP_REFERER'])) ? getenv('HTTP_REFERER') : $_SERVER['HTTP_REFERER']);
    // k.A. warum, aber wir hatten da schon Hosts mit slashes drumrum...
    $_SERVER['HTTP_HOST'] = strip_tags(strtolower(trim($_SERVER['HTTP_HOST'], ' /:;.')));

    switch (true) {
        // so sollte es sein
        case isset($_SERVER['SERVER_ADDR']):
        // da hat ma mal nen Strato Server
        case $_SERVER['SERVER_ADDR'] = getenv('SERVER_ADDR'):
            break;
        // IIS Spezial...
        case isset($_SERVER['LOCAL_ADDR']):
            $_SERVER['SERVER_ADDR'] = $_SERVER['LOCAL_ADDR'];
            break;
        // weiss Gott was...
        default:
            $_SERVER['SERVER_ADDR'] = '0.0.0.0';
    }

    define('MX_REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);
    define('MX_REMOTE_HOST', $_SERVER['REMOTE_HOST']);
    define('MX_USER_AGENT', $_SERVER['HTTP_USER_AGENT']);
	define("MX_JQUERY_VERSION",pmxGetFileVersion(PMX_JAVASCRIPT_PATH."jquery/jquery.js"));
	
}

?>
