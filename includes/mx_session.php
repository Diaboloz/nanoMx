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
 * $Revision: 296 $
 * $Author: PragmaMx $
 * $Date: 2016-12-13 12:53:33 +0100 (Di, 13. Dez 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

//if (isset($GLOBALS['mxSessionLoc']) && $GLOBALS['mxSessionLoc']==0) {
if (pmxBase::mxSessionLoc()==0) {	
	require_once(PMX_SYSTEM_DIR . DS . 'mx_sess_file.php');

	} else {
		
	require_once(PMX_SYSTEM_DIR . DS . 'mx_sess_mysqli.php');

}

function mxSessionInit($initiate = true)
{
    global $dbi, $prefix, $vkpIntranet;
    // nur, falls noch nicht bereits aufgerufen...
	
		$home_url=PMX_HOME_URL;
	
    if (!defined("MX_SESSION_NAME")) {
        define("MX_SESSION_NAME" , substr("mx" . strtoupper(md5(@MX_USER_AGENT . "s" . $home_url)), 0, 32));
        define("MX_SAFECOOKIE_NAME_ADMIN" , substr("mx" . strtoupper(md5(MX_SESSION_NAME . "a")), 0, 32));
        define("MX_SAFECOOKIE_NAME_USER" , substr("mx" . strtoupper(md5(MX_SESSION_NAME . "u")), 0, 32));
        define("MX_SESSION_VARPREFIX" , 'mxSV');
        define("MX_SESSION_DBTABLE" , $prefix ."_sys_session");
        /* der Session Garbage Collection Wert in %, muss immer grösser als 0 sein!! */
        define("MX_SESSION_GARBAGE" , 10);
        if (!$initiate) {
            return true;
        }
        /**
         * Session-Konfiguration
         * DIESE WERTE BITTE NUR VERAENDERN, WENN SIE WISSEN WAS SIE TUN !!
         * weitere Informationen:
         * - http://www.dclp-faq.de/ch/ch-version4_session.html
         * - http://www.php.net/manual/de/ref.session.php
         */
        @ini_set("session.auto_start" , '0'); // Auto-start session
        @ini_set("session.gc_probability" , MX_SESSION_GARBAGE); // Garbage collection in %
        @ini_set("session.serialize_handler", 'php_serialize'); // How to store data
        @ini_set("session.use_cookies" , '1'); // Use cookie to store the session ID
        @ini_set("session.gc_maxlifetime" , MX_SESSION_LIFETIME); // Sekunden Inactivity timeout for user sessions
        @ini_set("url_rewriter.tags" , ''); // verhindern, dass SID an URL gehaengt wird
    }
	
	session_save_path(realpath(PMX_SESSDATA_PATH));
    
	session_name(MX_SESSION_NAME); // zur Sicherheit, falls ini_set() deaktiviert
    if (empty($vkpIntranet)) {
        session_set_cookie_params(MX_COOKIE_LIFETIME, PMX_BASE_PATH, preg_replace('#^www\.#i', '', $_SERVER['HTTP_HOST']), false, true);
    } else {
        session_set_cookie_params(MX_COOKIE_LIFETIME, PMX_BASE_PATH, false, false, true);
    }
    @ini_set('session.use_only_cookies', '1'); // Use only cookie to store the session ID
    @ini_set('session.cookie_httponly' , '1'); // Whether or not to add the httpOnly flag to the cookie, which makes it inaccessible to browser scripting languages such as JavaScript.
    /**
     * ENDE Session-Konfiguration
     */
    session_module_name('user');
    session_set_save_handler("mx_sys_session_open",
        "mx_sys_session_close",
        "mx_sys_session_read",
        "mx_sys_session_write",
        "mx_sys_session_destroy",
        "mx_sys_session_gc");
	
}

/**
 * Setzen einer Session Variablen
 *
 * @since pragmaMx 0.1.0
 * @param string $varname Name der Session-Variablen, die gesetzt werden soll.
 * @param string $value Wert der Session-Variablen, der gesetzt werden soll.
 * @return bool Gibt true zurück
 */
function mxSessionSetVar($varname, $value)
{
    $_SESSION[MX_SESSION_VARPREFIX . $varname] = $value;
    return true;
}

/**
 * Auslesen einer Session-Variablen
 *
 * @since pragmaMx 0.1.0
 * @param string $varname Name der Session-Variablen, die ausgelesen werden soll.
 * @param mixed $default : optionaler Standardwert, falls die Session-Variable nicht existiert
 * @return mixed Gibt den Wert der Session-Variablen zurück.
 */
function mxSessionGetVar($varname, $default = false)
{
    $varname = MX_SESSION_VARPREFIX . $varname;
    if (!isset($_SESSION[$varname])) {
        return $default;
    }
    return $_SESSION[$varname];
}

/**
 * Löschen einser Session-Variablen
 *
 * @since pragmaMx 0.1.0
 * @param string $varname Name der Session-Variablen, die gelöscht werden soll.
 * @return bool Gibt true zurück
 */
function mxSessionDelVar($varname)
{
    unset($_SESSION[MX_SESSION_VARPREFIX . $varname]);
    return true;
}

/**
 * Zerstören der aktuellen Session
 *
 * @since pragmaMx 0.1.0
 * @return bool Gibt true zurück
 */
function mxSessionDestroy()
{
    mxSetCookie(MX_SESSION_NAME, "", -1);
    mxSetCookie(MX_SAFECOOKIE_NAME_USER, "", -1);
    mxSetCookie(MX_SAFECOOKIE_NAME_ADMIN, "", -1);
    //mxSetNukeCookie('user');
    //mxSetNukeCookie('admin');
    $_SESSION = array();
    session_destroy();
    return true;
}
/**
 * Prüfen, ob Sessionvariable vorhanden
 *
 * @since pragmaMx 2.4.
 * @return bool 
 */
function mxSessionCheckVar($varname){

    if (isset($_SESSION[MX_SESSION_VARPREFIX . $varname])) {
        return true;
    }else{
		return false;
	}
}

?>