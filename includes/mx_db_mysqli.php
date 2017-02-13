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
 * $Revision: 216 $
 * $Author: PragmaMx $
 * $Date: 2016-09-20 15:29:30 +0200 (Di, 20. Sep 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');


/**
 * sql_escape()
 * ersetzt mysql_real_escape_string()
 * @param $value string
 * @return string
 */
function sql_real_escape_string($value, $dbid=NULL) {
   
   $fix_str=stripslashes($value);
   $fix_str=str_replace("'","''",$fix_str);
   $fix_str=str_replace("\0","[NULL]",$fix_str);
   
   return $fix_str;
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
 * sql_ifTableExists()
 * prüft, ob Tabelle vorhanden ist
 *
 * @param string $table
 * 
 * @return boolean
 */
function sql_ifTableExists($table)
{
		global $prefix;
		return sql_query("SHOW TABLES LIKE '" . $prefix . $table . "'");
		
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
    $msg = pmxDebug::sql_clean_message($msg);
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
    pmxDebug::sql_trigger_error(pmxBase::dbtype(), __FILE__, pmxDatabase::error(),pmxDatabase::errno(), $query);
}

/**
 * sql_connect()
 * Datenbankverbindung herstellen
 * Datenbank auswaehlen
 *
 * @return
 */
function sql_system_connect()
{
   return pmxDatabase::connect();
}

/**
 * sql_connect()
 * Datenbankverbindung herstellen
 * Datenbank auswaehlen
 *
 * @return
 */
function sql_connect($host, $user, $password, $database="", $dbtype="mysql")
{
   return pmxDatabase::connect();
}
/**
 * sql_connect()
 * Datenbankverbindung herstellen
 * Datenbank auswaehlen
 *
 * @return
 */
function sql_pconnect($host, $user, $password)
{
  return pmxDatabase::connect();
}

/**
 * sql_select_db()
 * Datenbank auswaehlen
 *
 * @param mixed $dbi
 * @return
 */
function sql_select_db($dbname)
{
   return pmxDatabase::select_db($dbname);
}

/**
 * sql_close()
 * Schließt die Verbindung zu MySQL
 *
 * @return
 */
function sql_close($dbid=NULL)
{
	return pmxDatabase::close();
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
    return pmxDatabase::insert_id();
}

/**
 * sql_affected_rows()
 * liefert die Anzahl betroffener Datensaetze durch die letzte INSERT, UPDATE oder DELETE Anfrage
 * an den Server, die mit der angegebenen Verbindungs-Kennung assoziiert wird.
 * Wird die Verbindungskennung nicht angegeben, wird die letzte durch mysqli_connect() geoeffnete Verbindung angenommen
 *
 * @return
 */
function sql_affected_rows($dbid = NULL)
{
	return pmxDatabase::affected_rows();
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
    return pmxDatabase::query($query);
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
	return pmxDatabase::system_query($query);
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
    return pmxDatabase::num_rows($result);
}

/**
 * sql_num_rows()
 * given a result identifier, returns the number of affected rows
 *
 * @param mixed $result
 * @return
 */
function sql_num_fields($result)
{
    return pmxDatabase::field_count($result);
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
    return pmxDatabase::fetch_row($result);
}

/**
 * sql_fetch_field()
 * given a result identifier, returns an array with the resulting row
 *
 * @param mixed $result
 * @return
 */
function sql_fetch_field($result,$fieldno=NULL)
{
    return pmxDatabase::fetch_fields($result,$fieldno);
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
    return pmxDatabase::fetch_array($result);
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
    return pmxDatabase::fetch_assoc($result);
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
    return pmxDatabase::fetch_object($result);
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
    return pmxDatabase::fetch_rowset($result);
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
    return pmxDatabase::free_result($result);
}

/**
 * sql_result()
 *
 * @return
 */
function sql_result($result,$row,$col=0)
{
	return pmxDatabase::data_seek_field($result,$row,$col);
}

/**
 * sql_data_seek()
 *
 * @return
 */
function sql_data_seek($result,$row)
{
	return pmxDatabase::data_seek($result,$row);
}
/**
 * sql_error()
 *
 * @return
 */
function sql_errno()
{
	return pmxDatabase::errno();
}

/**
 * sql_error()
 *
 * @return
 */
function sql_error()
{
	return pmxDatabase::error();
}


function sql_get_server_info()
{
	return pmxDatabase::server_info();
}
/**
 * sql_get_client_info()
 *
 * @return
 */
function sql_get_client_info()
{
	return pmxDatabase::client_info();
}


function sql_client_encoding($dbi=NULL) 
{
	return pmxDatabase::client_encoding();
}

function sql_list_tables($database,$dbi=NULL)
{
	return sql_query("SHOW TABLES FROM $database" );
}


 class pmxDatabase{

    private static $instance;
	private static $affect_rows=NULL;
	private static $num_rows=NULL;
	private static $insertid=NULL;
	private static $result=NULL;

    private function __construct() {}
    private function __clone(){}
	
    private static function call(){
		
        if(!isset(self::$instance)){  
			$msg="";
			$old_error=error_reporting();
			error_reporting(0);
			self::$instance = new mysqli(pmxBase::dbhost(), pmxBase::dbuname(), pmxBase::dbpass(),pmxBase::dbname());  
            switch (self::$instance->connect_errno){  
				case 1045:
					$msg = ("Die Benutzerdaten zur Datenbank sind falsch angegeben!<br />The user data to the database are given wrong!");
					break;
				case 2005:
					$msg = ("Der Datenbank-Host ist falsch angegeben!<br />The database host is set incorrectly!");
					break;
				case 1049:
					$msg = ("Der Datenbank-Name ist falsch angegeben!<br />The database name is set incorrectly!");
					break;					
				case 2003:
					$msg = ("Keine Verbindung zum Datenbank-Server!<br />Can't connect to MySQL server!");
					break;				
				default:
					$msg = self::$instance->connect_error;  
            }  
			error_reporting($old_error);
			if (isset($msg)) {
				if (!defined('MX_SQL_VERSION')) {
					define('MX_SQL_VERSION', '0');
				}

				/* Fehlerbehandlung */
				//if (self::$instance->connect_error) {
									
					$msg = "Sorry, wir haben zur Zeit Probleme mit der Datenbank.<br/>Sorry, we have some database-problems.<br/><br/>(" . self::$instance->connect_errno . ")";
					die(mxSqlPrepareMessage('<html><head><title>' . pmxBase::sitename() . '</title></head><body text="#000080"><h1>' . pmxBase::sitename() . '</h1>' . $msg . '<br/><br/><br/>pragmaMx ' . PMX_VERSION . '</body></html>'));
				//}
			}	
			if (!defined('MX_SQL_VERSION')) {
				$mysqlversion = self::$instance->server_info;		
				define('MX_SQL_VERSION', $mysqlversion);
			}
			
			self::$instance->set_charset("utf8");
			/* Probleme mit evtl. falschem Charset beheben */
			self::$instance->query("SET
			  names 'utf8',
			  character set 'utf8',
			  character_set_results = 'utf8',
			  character_set_client = 'utf8',
			  character_set_connection = 'utf8',
			  character_set_database = 'utf8',
			  character_set_server = 'utf8'
			");

			/* Zeitzone einstellen */
			self::$instance->query("SET time_zone = '" . date('P') . "';");

    	
        } 
		/* Datenbank-Handle zurueckgeben */		
        return self::$instance;
    }
	
	/* globale Funktionen*/
	public static function select_db($dbname)
	{
		return self::call()->select_db($dbname);
	}	
	
	public static function insert_id($result=NULL)
	{
		return self::call()->insert_id;
	}	
	public static function affected_rows($result=NULL)
	{
		return self::call()->affected_rows;
	}	
	
	/* statement functionen */

	public static function field_count($result=NULL)
	{
		$result=($result)?$result:self::$result;
		if ($result==NULL) return false;
		return $result->field_count;
	}
	
	public static function current_field($result=NULL)
	{
		$result=($result)?$result:self::$result;
		if ($result==NULL) return false;
		return $result->current_field;
	}	
	
	public static function param_count($result=NULL)
	{
		$result=($result)?$result:self::$result;
		if ($result==NULL) return false;
		return $result->param_count;
	}	

	/* result functions */

	public static function fetch_field($result=NULL,$fieldno=NULL)
	{
		static $myresult,$myfno;
		if ($myresult <> $result){
			$myresult=$result;
			$filedno=0;
		} else {
			$fieldno++;
		}
		$result=($result)?$result:self::$result;
		if ($result==NULL) return false;
		$temp=$result->fetch_fields();
		return (object)$temp[$fieldno];
	}
	
	public static function fetch_assoc($result=NULL)
	{
		$result=($result)?$result:self::$result;
		if ($result==NULL) return false;
		return $result->fetch_assoc();
	}
	
	public static function fetch_row($result=NULL)
	{
		$result=($result)?$result:self::$result;
		if ($result==NULL) return false;
		return $result->fetch_row();
	}

	public static function fetch_rowset($result=NULL)
	{
		$result=($result)?$result:self::$result;
		if ($result==NULL) return false;
		$rowset = array();
		while ($row = self::fetch_array($result)) {
        $rowset[] = $row;
		}
		return $rowset;	
	}	

	public static function fetch_array($result=NULL)
	{
		$result=($result)?$result:self::$result;
		if ($result==NULL) return false;
		return $result->fetch_array();
	}
	
	public static function fetch_object($result=NULL)
	{
		$result=($result)?$result:self::$result;
		if ($result==NULL) return false;
		return $result->fetch_object();
	}

	public static function free_result($result=NULL)
	{
		$result=($result)?$result:self::$result;
		if ($result==NULL) return false;
		return $result->free_result();
	}	
	
	public static function num_rows($result=NULL)
	{
		$result=($result)?$result:self::$result;
		if ($result==NULL) return false;
		return $result->num_rows;
	}	

	public static function data_seek_field($result,$row,$field=0)
	{
		$result=($result)?$result:self::$result;
		if ($result==NULL) return false;		
		$row = (intval($row))?intval($row):0;
		$field = (intval($field))?intval($field):0;
		$result->data_seek($row);
		$temp=$result->fetch_row();
		return $temp[$field];
	}	
	
	public static function data_seek($result,$row)
	{
		$result=($result)?$result:self::$result;
		if ($result==NULL) return false;		
		$row = (intval($row))?intval($row):0;
		$result->data_seek($row);
		return $result->fetch_row();
	}	
	
	/* mysqli fuctions */
	
	public static function connect() {
		return self::call();
	}
	
	public static function close()
	{
		return self::call()->close();
	}
	
	public static function error()
	{
		return self::call()->error;
	}
	
	public static function errno()
	{
		return self::call()->errno;
	}	
	
	public static function server_version()
	{
		return self::call()->server_version;
	}
	public static function server_info()
	{
		return self::call()->server_info;
	}

	public static function client_info()
	{
		return self::call()->client_info;
	}	

	public static function client_encoding()
	{
		return self::call()->character_set_name();
	}	
	
	public static function list_tables($database){
		
	}
	
    /**
     * 
     * 
     * @param string $query 
     * 
     * @return object statement
     */
	public static function query($query="")
	{
		$result=false;
		/* wenn sql_inject Überpruefung eingeschaltet */
		if (!pmxBase::mxSkipSqlDetect() && pmxBase::vkpSafeSqlinject()) {
			if (class_exists("pmxDetect")) $query = pmxDetect::query($query);
		}
		pmxBase::set("mxSkipSqlDetect",false);

		/* Query Ausgabe fuellen */
		if (class_exists("pmxDebug")) pmxDebug::querystack($query);

		/* die eigentliche DB-Anfrage ausfuehren */
		self::$result = self::call()->query($query);
		
		
		/* Fehlerbehandlung */
		if (self::call()->error) {
			self::sql_error($query);
			return false;
		}

		return self::$result;		
	}
	
	public static function system_query($query)
	{
		pmxBase::set("mxSkipSqlDetect", true);
		return self::query($query);
	}
	
	private static function sql_error ($query)
	{
		pmxDebug::sql_trigger_error(pmxBase::dbtype(), __FILE__, self::call()->error,self::call()->errno, $query);
	}
} 


?>
