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

global $dbi, $vkpIntranet;

// function mxSessionInit($initiate = true)
// {
    // global $dbi, $prefix, $vkpIntranet;
    // nur, falls noch nicht bereits aufgerufen...
    // if (!defined("MX_SESSION_NAME")) {
        // define("MX_SESSION_NAME" , substr("mx" . strtoupper(md5(@MX_USER_AGENT . "s" . PMX_HOME_URL)), 0, 32));
        // define("MX_SAFECOOKIE_NAME_ADMIN" , substr("mx" . strtoupper(md5(MX_SESSION_NAME . "a")), 0, 32));
        // define("MX_SAFECOOKIE_NAME_USER" , substr("mx" . strtoupper(md5(MX_SESSION_NAME . "u")), 0, 32));
        // define("MX_SESSION_VARPREFIX" , 'mxSV');
        // define("MX_SESSION_DBTABLE" , "${prefix}_sys_session");
        // /* der Session Garbage Collection Wert in %, muss immer grösser als 0 sein!! */
        // define("MX_SESSION_GARBAGE" , 10);
        // if (!$initiate) {
            // return true;
        // }
        // /**
         // * Session-Konfiguration
         // * DIESE WERTE BITTE NUR VERAENDERN, WENN SIE WISSEN WAS SIE TUN !!
         // * weitere Informationen:
         // * - http://www.dclp-faq.de/ch/ch-version4_session.html
         // * - http://www.php.net/manual/de/ref.session.php
         // */
        // @ini_set("session.auto_start" , '1'); // Auto-start session
        // @ini_set("session.gc_probability" , MX_SESSION_GARBAGE); // Garbage collection in %
        // @ini_set("session.serialize_handler", 'php'); // How to store data
        // @ini_set("session.use_cookies" , '1'); // Use cookie to store the session ID
        // @ini_set("session.gc_maxlifetime" , MX_SESSION_LIFETIME); // Sekunden Inactivity timeout for user sessions
        // @ini_set("url_rewriter.tags" , ''); // verhindern, dass SID an URL gehaengt wird
    // }
	
	// @ini_set('session.save_path',realpath(PMX_SESSDATA_PATH));
	
    // session_name(MX_SESSION_NAME); // zur Sicherheit, falls ini_set() deaktiviert
    // if (empty($vkpIntranet)) {
        // session_set_cookie_params(MX_COOKIE_LIFETIME, PMX_BASE_PATH, preg_replace('#^www\.#i', '', $_SERVER['HTTP_HOST']), false, true);
    // } else {
        // session_set_cookie_params(MX_COOKIE_LIFETIME, PMX_BASE_PATH, false, false, true);
    // }
    // @ini_set('session.use_only_cookies', '1'); // Use only cookie to store the session ID
    // @ini_set('session.cookie_httponly' , '1'); // Whether or not to add the httpOnly flag to the cookie, which makes it inaccessible to browser scripting languages such as JavaScript.
    // /**
     // * ENDE Session-Konfiguration
     // */
    // session_module_name('user');
    // session_set_save_handler("mx_sys_session_open",
        // "mx_sys_session_close",
        // "mx_sys_session_read",
        // "mx_sys_session_write",
        // "mx_sys_session_destroy",
        // "mx_sys_session_gc");
// }

function mx_sys_session_open($spath, $sname, $pers = false)
{
    // $sess_speicherpfad = PMX_SESSDATA_PATH;
  return(true);
}

function mx_sys_session_read($sesskey)
{

    // bei neuer Session braucht nicht gelesen zu werden
    if (defined('MX_SESSION_NEWSESS')) {
        define('MX_SESSION_CRCSESS', '');
        return '';
    }
    // sessionkey auf Gueltigkeit testen: nur Buchstaben und Zahlen, genau 32 Zeichen lang
    if (!preg_match('#^[[:alnum:]]{32}$#', $sesskey)) die('unaccepted Sessionkey: ' . $sesskey);
	
	$sess_datei = PMX_SESSDATA_PATH . "sess_$sesskey";
	// Check, ob Sessionfile noch da?
	if (!file_exists($sess_datei)) return false;
	
	$data = @file_get_contents($sess_datei);
    $data = ($data===false) ? "" : rawurldecode($data);	
    if (!defined('MX_SESSION_CRCSESS')) {
        define('MX_SESSION_CRCSESS', (empty($sess)) ? "" : $sess);
    }
	return $data;	
   
}

function mx_sys_session_write($sesskey, $data)
{
    
    // sessionkey auf Gueltigkeit testen: nur Buchstaben und Zahlen, genau 32 Zeichen lang
    if (!preg_match('#^[[:alnum:]]{32}$#', $sesskey)) die(' -> write unaccepted Sessionkey: ' . $sesskey);
    
    $data = rawurlencode($data);
    
	$sess_datei = PMX_SESSDATA_PATH . "sess_$sesskey";

	$return = file_put_contents($sess_datei, $data);
	
	return $return;
	
}

function mx_sys_session_close()
{   
    return (true);
}

function mx_sys_session_destroy($sesskey)
{
	mx_sys_session_gc(MX_SESSION_LIFETIME);
	
	$sess_datei = PMX_SESSDATA_PATH . "sess_$sesskey";
	$return=@unlink($sess_datei);
	return($return);
}

function mx_sys_session_gc($maxlifetime)
{
  foreach (glob(PMX_SESSDATA_PATH ."sess_*") as $dateiname) {
    if (filemtime($dateiname) + $maxlifetime < time()) {
      @unlink($dateiname);
    }
  }
  return true;
}
/**
 * Initialise session
 */
function mxSessionStart()
{
    global $dbi;
    if (isset($GLOBALS['mxWithoutSession']) && !isset($_REQUEST['mxWithoutSession'])) {
        mxSessionInit(false);
        return true;
    }
    /**
     * Session initialisieren
     */
    mxSessionInit();
    // Testen ob bereits ein HTML-Header gesendet wurde
    if (headers_sent($filename, $linenum)) {
        echo "<br><br>Headers already sent in " . str_replace(MX_DOC_ROOT, '', $filename) . " on line $linenum<br><br>";
        return false;
    }
    // wenn kein Session-Cookie vorhanden, eine neue Session-ID generieren
    // dabei aber pruefen, ob die neue ID nicht schon in der db vorhanden ist
    if (!isset($_COOKIE[MX_SESSION_NAME])) {
        session_id(mxGetNewSessionId());
        define('MX_SESSION_NEWSESS', '1');
    }
    // wenn unerlaubte Zeichen in der uebermittelten Session-ID vorhanden sind,
    // ebenfalls, eine neue Session-ID generieren
    else if (!preg_match('#^[[:alnum:]]{32}$#', $_COOKIE[MX_SESSION_NAME])) {
        session_id(mxGetNewSessionId());
    }
    $sess = session_start();
	if ($sess==false) {
		session_id(mxGetNewSessionId());
		$sess = session_start();
	}

    return $sess;
}


/**
 * eine neue Session-ID generieren
 * dabei aber pruefen, ob die neue ID nicht schon in der db vorhanden ist
 */
function mxGetNewSessionId()
{
    global $dbi;
	mx_sys_session_gc(MX_SESSION_LIFETIME);
    mt_srand((double)microtime() * 1000000);
    $i = 0;
    do {
        $i++;
        $newsess = md5(uniqid(mt_rand(), true));
        $issess = file_exists(PMX_SESSDATA_PATH . "sess_$newsess");
        
    } while ($issess && $i < 6);
    return $newsess;
}

?>