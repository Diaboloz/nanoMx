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
 */

defined('mxMainFileLoaded') or die('access denied');



function mx_sys_session_open($spath, $sname, $pers = false)
{
    sql_connect(0,0,0);
	return true;
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
    //$isdb = sql_select_db($dbi,$GLOBALS['dbname'] );
    $qry = "SELECT sesskey, data FROM " . MX_SESSION_DBTABLE . " WHERE sesskey = '" . $sesskey . "' AND expiry >= " . time();
    $result = sql_system_query($qry);
    if (is_object($result)) {
        list($sess, $data) = sql_fetch_row($result);
        sql_free_result($result);
        $data = (empty($data)) ? "" : rawurldecode($data);
        if (!defined('MX_SESSION_CRCSESS')) {
            define('MX_SESSION_CRCSESS', (empty($sess)) ? "" : $sess);
        }
        return $data;
    } else {
        $msg = str_replace($GLOBALS['prefix'], '{prefix}', strtolower("<div style=\"background-color: Red; color: Yellow; border: thin solid Black;\"><h1>Error:</h1><p>" . sql_error() . "</p></div>"));
        $msg = str_replace(strtolower($GLOBALS['dbname']), '{dbname}', $msg);
        echo $msg;
        sql_system_query("REPAIR TABLE `" . MX_SESSION_DBTABLE . "`  ");
        return false;
    }
}

function mx_sys_session_write($sesskey, $data)
{

    // sessionkey auf Gueltigkeit testen: nur Buchstaben und Zahlen, genau 32 Zeichen lang
    if (!preg_match('#^[[:alnum:]]{32}$#', $sesskey)) die('unaccepted Sessionkey: ' . $sesskey);

    $timeadd = (mxSessionGetVar('user') || mxSessionGetVar('admin')) ? MX_SESSION_LIFETIME : MX_SESSION_LIFETIME_NOUSER;
    $expiry = time() + $timeadd;
    $is_sess = (defined('MX_SESSION_CRCSESS')) ? (MX_SESSION_CRCSESS == $sesskey) : false;
    $data = rawurlencode($data);
    if ($is_sess) {
        $qry = "UPDATE `" . MX_SESSION_DBTABLE . "` SET expiry = " . $expiry . ", data='" . $data . "' WHERE sesskey='" . $sesskey . "' AND expiry >= " . time();
    } else {
        $qry = "REPLACE INTO `" . MX_SESSION_DBTABLE . "` ( `sesskey`, `expiry`, `data` ) VALUES ('" . $sesskey . "', " . $expiry . ", '" . $data . "')";
    }
    $result = sql_system_query($qry);
    return !empty($result);
}

function mx_sys_session_close()
{
    return true;
}

function mx_sys_session_destroy($sesskey)
{
    // sessionkey auf Gueltigkeit testen: nur Buchstaben und Zahlen, genau 32 Zeichen lang
    if (!preg_match('#^[[:alnum:]]{32}$#', $sesskey)) die('unaccepted Sessionkey: ' . $sesskey);
    
    $qry = "DELETE FROM " . MX_SESSION_DBTABLE . " WHERE sesskey = '" . $sesskey . "' OR expiry < " . time();
    $result = sql_system_query($qry);
    return $result ? true : false;
}

function mx_sys_session_gc($maxlifetime)
{
    $qry = "DELETE FROM " . MX_SESSION_DBTABLE . " WHERE expiry < " . time();
    sql_query($qry);
    $opt_qry = 'OPTIMIZE TABLE ' . MX_SESSION_DBTABLE;
    sql_system_query($opt_qry);
    return true;
}

/**
 * Initialise session
 */
function mxSessionStart()
{

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
	
    $sess1 = session_start();
	
	if ($sess1==false) {
		session_id(mxGetNewSessionId());
		$sess1 = session_start();
	}
	
    return $sess1;
}

/**
 * eine neue Session-ID generieren
 * dabei aber pruefen, ob die neue ID nicht schon in der db vorhanden ist
 */
function mxGetNewSessionId()
{
	sql_connect(0,0,0);
    mt_srand((double)microtime() * 1000000);
	
    $i = 0;
    do {
        $i++;
        $newsess = md5(uniqid(mt_rand(), true));
        $qry = "SELECT sesskey, expiry FROM " . MX_SESSION_DBTABLE . " WHERE sesskey = '" . $newsess . "'";
        $result = sql_system_query($qry);
        list($sesskey, $expiry) = sql_fetch_row($result);
        if (empty($sesskey) || $expiry < time()) {
            if ($expiry < time()) {
                $qry = "DELETE FROM " . MX_SESSION_DBTABLE . " WHERE expiry < " . time();
                sql_system_query($qry);
            }
        }
    } while (!empty($sesskey) && $i < 6);
    return $newsess;
}

?>