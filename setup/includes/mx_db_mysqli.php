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
 * $Revision: 180 $
 * $Author: PragmaMx $
 * $Date: 2016-07-08 10:13:09 +0200 (Fr, 08. Jul 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');


/**
 * sql_connect()
 * Datenbankverbindung herstellen
 * Datenbank auswaehlen
 *
 * @return
 */
function sql_connect($host, $user, $password, $dbname)
{
    global $dbi;
    // wenn bereits Datenbankverbindung besteht....
    if (!is_object($dbi)) {
        $dbi = @mysqli_connect($host, $user, $password) ;
    }
    sql_select_db($dbname,$dbi);
    return $dbi;
}

/**
 * sql_select_db()
 * Datenbank auswaehlen
 *
 * @param mixed $dbi
 * @return
 */
function sql_select_db($dbname,$dbi=NULL)
{

    if (!is_object($dbi)) {
        if (@mysqli_errno($dbi) == 1045) {
            $msg = ("Die Benutzerdaten zur Datenbank sind falsch angegeben!<br />The user data to the database are given wrong!");
        } else if (@mysqli_errno($dbi) == 2005) {
            $msg = ("Der Datenbank-Host ist falsch angegeben!<br />The database host is set incorrectly!");
        } else {
            $msg = @mysqli_error($dbi);
        }
    }
    if (!isset($msg)) {
        $isdb = @mysqli_select_db($dbi,$dbname);
        if (!$isdb) {
            if (@mysqli_errno($dbi) == 1049) {
                $msg = ("Der Datenbank-Name ist falsch angegeben!<br />The database name is set incorrectly!");
            } else {
                $msg = @mysqli_error($dbi);
            }
        }
    }
    if (isset($msg)) {
        if (!defined('MX_SQL_VERSION')) {
            define('MX_SQL_VERSION', '0');
        }

        /* Fehlerbehandlung */
        if (@mysqli_error($dbi)) {
			return false;
            
        }
        $msg = "Sorry, wir haben zur Zeit Probleme mit der Datenbank.<br/>Sorry, we have some database-problems.<br/><br/>(" . mysqli_errno($dbi) . ")";
        //die(mxSqlPrepareMessage('<html><head><title>' . $GLOBALS['sitename'] . '</title></head><body text="#000080"><h1>' . $GLOBALS['sitename'] . '</h1>' . $msg . '<br/><br/><br/>pragmaMx ' . PMX_VERSION . '</body></html>'));
		return false;
    }
	
    if (!defined('MX_SQL_VERSION')) {
        $mysqlversion = @mysqli_get_server_info($dbi);		
        define('MX_SQL_VERSION', $mysqlversion);
    }

    /* Probleme mit evtl. falschem Charset beheben */
    @mysqli_query($dbi,"SET
      names 'utf8',
      character set 'utf8',
      character_set_results = 'utf8',
      character_set_client = 'utf8',
      character_set_connection = 'utf8',
      character_set_database = 'utf8',
      character_set_server = 'utf8'
    ");

    /* Zeitzone einstellen */
    @mysqli_query($dbi,"SET time_zone = '" . date('P') . "';");

    /* Datenbank-Handle zurueckgeben */
    return $dbi;
}

/**
 * sql_close()
 * Schließt die Verbindung zu MySQL
 *
 * @return
 */
function sql_close($dbid=NULL)
{
	//if (is_null($dbid)) $dbid = $GLOBALS['dbi'];
    return true; //mysqli_close($dbid);
}

/**
 * sql_logout()
 * Schließt die Verbindung zu MySQL (alias von close)
 * nicht benoetigt, die Session beendet die Verbindung
 *
 * @return
 */
function sql_logout($dbid = null)
{
    return true;
}

/**
 * sql_insert_id()
 * Liefert die ID einer vorherigen INSERT-Operation
 *
 * @return
 */
function sql_insert_id($dbid = null)
{
    if (is_null($dbid)) $dbid = $GLOBALS['dbi'];
    return mysqli_insert_id($dbid);
}

/**
 * sql_affected_rows()
 * liefert die Anzahl betroffener Datensaetze durch die letzte INSERT, UPDATE oder DELETE Anfrage
 * an den Server, die mit der angegebenen Verbindungs-Kennung assoziiert wird.
 * Wird die Verbindungskennung nicht angegeben, wird die letzte durch mysql_connect() geoeffnete Verbindung angenommen
 *
 * @return
 */
function sql_affected_rows($dbid = null)
{
    if (!is_object($dbid)) return false;
    return intval(mysqli_affected_rows($dbid));
}

/**
 * sql_query()
 * executes an SQL statement, returns a result identifier
 *
 * @param mixed $query
 * @return
 */
function sql_query($query, $dbid = null)
{
    if (!is_object($dbid)) {
        $dbid = $GLOBALS['dbi'];
    }
	
	if (is_null($dbid)) return false;
	
    if (!is_object($dbid)) {
        trigger_error("Keine gueltige Datenbankverbindung ( \$dbid)<br />Not a valid database connection (\$dbid) - ". E_USER_ERROR);
		
    }
	
    /* wenn sql_inject Überpruefung eingeschaltet */
    //if (!$GLOBALS['mxSkipSqlDetect'] && $GLOBALS['vkpSafeSqlinject']) {
    //    $query = mxDetectCheckQuery($query);
    //}
    $GLOBALS['mxSkipSqlDetect'] = false;

    /* Query Ausgabe fuellen */
    //pmxDebug::querystack($query);

    /* die eigentliche DB-Anfrage ausfuehren */
    $result = mysqli_query($dbid,$query);

    /* Fehlerbehandlung */
    if (mysqli_error($dbid)) {
        sql_trigger_error($query);
        return false;
    }

    return $result;
}

/**
 * sql_system_query()
 * executes an SQL statement, returns a result identifier
 *
 * @param mixed $query
 * @return
 */
function sql_system_query($query, $dbid = null)
{
    $GLOBALS['mxSkipSqlDetect'] = true;
    return sql_query($query, $dbid);
}

/**
 * sql_num_rows()
 * given a result identifier, returns the number of affected rows
 *
 * @param mixed $result
 * @return
 */
function sql_num_rows($result)
{
    if (!is_object($result)) {
        return 0;
    }
    return intval(mysqli_num_rows($result));
}

/**
 * sql_fetch_row()
 * given a result identifier, returns an array with the resulting row
 *
 * @param mixed $result
 * @return
 */
function sql_fetch_row($result)
{
    if (!is_object($result)) {
        return false;
    }
    return mysqli_fetch_row($result);
}

/**
 * sql_fetch_array()
 * given a result identifier, returns an associative and numeric array
 * with the resulting row using field names as keys.
 *
 * @param mixed $result
 * @return
 */
function sql_fetch_array($result)
{
    if (!is_object($result)) {
        return false;
    }
    return mysqli_fetch_array($result);
}

/**
 * sql_fetch_assoc()
 * given a result identifier, returns an associative array
 * with the resulting row using field names as keys.
 *
 * @param mixed $result
 * @return
 */
function sql_fetch_assoc($result)
{
    if (!is_object($result)) {
        return false;
    }
    return mysqli_fetch_array($result, MYSQLI_ASSOC);
}

/**
 * sql_fetch_object()
 * given a result identifier, returns an object
 *
 * @param mixed $result
 * @return
 */
function sql_fetch_object($result)
{
    if (!is_object($result)) {
        return false;
    }
    return mysqli_fetch_object($result);
}

/**
 * sql_fetch_rowset()
 * Fetch all rows in an array
 *
 * @param mixed $result
 * @return
 */
function sql_fetch_rowset($result)
{
    if (!is_object($result)) {
        return false;
    }
    $rowset = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $rowset[] = $row;
    }
    return $rowset;
}

/**
 * sql_free_result()
 * for function free the memory
 *
 * @param mixed $result
 * @return
 */
function sql_free_result($result)
{
    if (!is_object($result)) {
        return false;
    }
    return mysqli_free_result($result);
}
/**
 * sql_escape()
 * ersetzt mysql_real_escape_string()
 * @param $value string
 * @return string
 */
function sql_real_escape_string($value, $dbid=NULL) {
    
	if (!is_object($dbid)) {
        $dbid = $GLOBALS['dbi'];
    }   
    $fix_str=stripslashes($value);
    $fix_str=str_replace("'","''",$fix_str);
    $fix_str=str_replace("\0","[NULL]",$fix_str);
    return mysqli_real_escape_string($dbid, $fix_str);
   
}  

/**
 * sql_escape()
 * ersetzt mysql_escape_string()
 * @param $value string
 * @return string
 */
function sql_escape_string($value, $dbid=NULL) {
   
   return sql_real_escape_string($value, $dbid);
}  

/**
 * sql_error()
 *
 * @return
 */
function sql_error()
{
	global $dbi;
    return mysqli_connect_errno();
}

/**
 * sql_error()
 *
 * @return
 */
function sql_errno()
{
    global $dbi;
	return mysqli_connect_errno();
}
/**
 * mxSqlPrepareMessage()
 * u.A. die Datenbankverbindungsdaten aus den Fehlermeldungen entfernen
 *
 * @param mixed $msg
 * @param string $class
 * @return
 */
function mxSqlPrepareMessage($msg, $class = '')
{
    //$msg = pmxDebug::sql_clean_message($msg);
    if ($class) {
        $msg = '<div class="' . $class . ' align-left">' . $msg . '</div>';
    }
    return $msg;
}

/**
 * sql_trigger_error()
 *
 * @param mixed $query
 * @return
 */
function sql_trigger_error($query = false)
{
	global $dbi;

    die($GLOBALS['dbtype']." ". __FILE__ ." ". __LINE__ ." ". mysqli_error($dbi) ." ". mysqli_errno($dbi) ." ". $query);
}

?>
