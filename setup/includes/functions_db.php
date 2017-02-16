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
$GLOBALS['mxSkipSqlDetect']=0;


/* Datenbanktyp  auf MySql stellen */

$GLOBALS['dbtype'] = "mysql";

$GLOBALS["mxSqlErrorDebug"]=0;

$GLOBALS['dbi']=NULL;

/* since PHP7 only prmitted MYSQLI */
require_once( 'includes/mx_db_mysqli.php');



/**
 * sobald die config.php geschrieben ist, wird diese Verbindung verwendet
 */
function setupConnectDb($dbhost = '', $dbuname = '', $dbpass = '', $dbname = '')
{
    $out['msg'] = '';
    if (empty($dbhost) && empty($dbuname) && empty($dbpass) && empty($dbname) ) {
        @ini_set('track_errors', '1');
        if (!@include(FILE_CONFIG_ROOT)) {
            $out['msg'] = '
              <div class="alert alert-error alert-block">
                <p>' . _THEREERROR . ':</p>
                <p>' . _SERVERMESSAGE . ': ' . @$php_errormsg . '</p>
              </div>';
            return $out;
        }
    }
    $dbi = sql_connect($dbhost, $dbuname, $dbpass,$dbname);
    $GLOBALS['dbi'] = $dbi;
    if (!$dbi) {
        $out['msg'] .= '
          <p><strong>' . _NOT_CONNECT . '</strong></p><p>' . _SERVERMESSAGE . ': ' . sql_error() . ' (' . sql_errno() . ')</p>';
        defined('MX_SQL_VERSION') or define('MX_SQL_VERSION', 0);
    } else {
        /* Probleme mit evtl. falschem Charset beheben */
        //setup_set_sql_names('utf8');

        if (!defined('MX_SQL_VERSION')) {
            list($mysqlversion) = sql_fetch_row(sql_query("SELECT VERSION() as version"));
            define('MX_SQL_VERSION', $mysqlversion);
        }
        if (version_compare(MX_SQL_VERSION, MX_SETUP_MIN_MYSQLVERSION, '<')) {
            $out['msg'] .= '<div class="alert alert-error alert-block">' . sprintf(_DBVERSIONFALSE, MX_SETUP_MIN_MYSQLVERSION) . '</div>' . str_repeat('<br />', 50);
        } else if (!sql_select_db($dbname , $dbi)) {
            $out['msg'] .= '<p>' . _DBNOTEXIST . '</p>
              <p>' . _SERVERMESSAGE . ': ' . sql_error() . ' (' . sql_errno() . ')</p>';
            // $out['dbnotexist'] = TRUE;
        } else {
            $out['dbi'] = $dbi;
            $out['msg'] .= sprintf(_DB_CONNECTSUCCESS, $dbname);
        }
    }

    return $out;
}

/**
 * checkt ob eine Tabelle vorhanden ist,
 * mit dem 2ten Parameter kann das Tabellenarray aktualisiert werden
 */
function setupTableExist($tablename, $refr_array = 0)
{
    global $tables;
    if (empty($tablename)) {
        return false;
    }
    $result = sql_query("SHOW TABLES LIKE '${tablename}';");
    $is = sql_num_rows($result);
    if ($is) {
        if ($refr_array) {
            $tables[$tablename] = $tablename;
        }
        return true;
    } else {
        if ($refr_array) {
            unset($tables[$tablename]);
        }
        return false;
    }
}

/**
 * liest alle Felder einer Tabelle in ein assoziatives Array
 * der Feldname dient als Index
 */
function setupGetTableFields($tablename)
{
    global $tables;
    // print "<h5>$tablename</h5>";
    $fields = array();
    $result = sql_query("DESCRIBE `${tablename}`");
    if (!$result || sql_error()) {
        // irgenwie kommt das ab und an zu dem Fehler:
        // - Can't create/write to file '/tmp/#sql_8c4_0.MYD' (Errcode: 13)
        // http://bugs.mysql.com/bug.php?id=25872
        // dann einfach etwas warten und nochmal probieren ;-))
        sleep(1);
        $result = sql_query("DESCRIBE `${tablename}`");
    } while ($row = sql_fetch_array($result, MYSQLI_ASSOC)) {
        $fields[$row['Field']] = $row;
    }
    sql_free_result($result);
    return $fields;
}

/**
 * liest alle indexe einer Tabelle in ein Array
 */
function setupGetTableIndexes($tablename)
{
    $indexes = array();
    $result = sql_query("SHOW INDEXES FROM `${tablename}`");
    if (!$result || sql_error()) {
        // siehe dazu: setupGetTableFields()
        sleep(1);
        $result = sql_query("SHOW INDEXES FROM `${tablename}`");
    } while ($row = sql_fetch_array($result, MYSQLI_ASSOC)) {
        if (!isset($indexes[$row['Key_name']])) {
            $indexes[$row['Key_name']] = $row;
        }
        $indexes[$row['Key_name']]['all_fields'][$row['Column_name']] = $row['Seq_in_index'];
    }
    sql_free_result($result);
    return $indexes;
}


?>