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


class pmxDb 
{
 
	private static $dbConf = array();
    private static $active = false;
    private static $dbHandle = null;
   
    private static $__dataset = array();
    private static $queryCounter = 0;
	
    private static $instances = array();
	private static $dbtypes = array("insert", 
									"select", 
									"update", 
									"delete", 
									"replace", 
									"show", 
									"set", 
									"create", 
									"alter", 
									"truncate",
									"drop",
									"rename",
									"optimize",
									"truncate",
									"do",
									);
	private static $dblisttypes=array("select","show");
	private static $checktypes = false;
	private static $nostack=false;
	private static $con_attr = array();

    private function  __construct()
    {
	}
    private function __clone()
    {

    }	

    public function __set($value_name,$value)
    {
		self::$__dataset[$value_name]=$value;
		
        return ;
    }	
    public function set($value_name,$value)
    {
		self::$__dataset[$value_name]=$value;
		
        return ;
    }		
    public function __get($value_name)
    {
		if (!array_key_exists($value_name,self::$__dataset)) return false;
		
        return self::$__dataset[$value_name];
    }

    public static function get($value_name)
    {
		if (!array_key_exists($value_name,self::$__dataset)) return false;
		
        return self::$__dataset[$value_name];
    }
	
	private static function _connect ($database="default")
	{
		if (!self::$active) {
				self::_setDefault();
				$database="default";
		}
		
        if (!isset(self::$dbConf[$database]))
            throw new Exception("No supported connection scheme");

        $dbConf = self::$dbConf[$database];
		
        try
        {
            //Connect
            $db = new PDO($dbConf['db_base'].":host=".$dbConf['db_host'].";dbname=".$dbConf['db'],$dbConf['db_user'],$dbConf['db_pw']);
            //error behaviour
            $db->setAttribute(PDO::ATTR_PERSISTENT , true);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_CURSOR , PDO::CURSOR_SCROLL);
			
			$attributes = array("AUTOCOMMIT","SERVER_INFO", "SERVER_VERSION", "ERRMODE", "CLIENT_VERSION");
			//	 "ERRMODE", "CLIENT_VERSION", "CONNECTION_STATUS",
			//	"PERSISTENT", "PREFETCH", "SERVER_INFO", "SERVER_VERSION",
			//	"TIMEOUT"
			// PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL
			

			foreach ($attributes as $val) {
				// "PDO::ATTR_$val: ";
				self::$con_attr["PDO::ATTR_$val"] = $db->getAttribute(constant("PDO::ATTR_$val")) ;
			}			
			
			/* Probleme mit evtl. falschem Charset beheben */
            $db->query("set character set utf8");
            $db->query("set names utf8");
            $db->query("set character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
			$db->query("set character_set_server = 'utf8'");
			
			/* Zeitzone einstellen */
			$db->query("SET time_zone = '" . date('P') . "';");
			
			/* Version ermitteln*/
			$result=$db->query("SELECT VERSION() as version");
			list($mysqlversion) = $result->fetch(PDO::FETCH_NUM);
			define('MX_SQL_VERSION', $mysqlversion);	
            self::$dbHandle = $db;
			self::$dbConf[$database]["db_dbi"]=$db;
            self::$active = true; //mark as active
        } catch (PDOException $ex)
        {
            throw new myDbException($ex);
        }
		return $db;		
    }


	
	public static function get_Attribute($attr)
	{
		if (array_key_exists("PDO::ATTR_$attr",self::$con_attr)) return self::$con_attr["PDO::ATTR_$attr"];
		return false;
	}


	private static function _setDefault () 
	{
			self::$dbConf = array("default" =>    array("db" => $GLOBALS['dbname'],
                                                        "db_host" => $GLOBALS['dbhost'],
                                                        "db_user" => $GLOBALS['dbuname'],
                                                        "db_pw" => $GLOBALS['dbpass'],
														"db_base" =>  $GLOBALS['dbtype']));
			self::$__dataset=array('laststmnt'=>false, 'irowCount'=>false);
			
	}
	
	/* überprüft den db-Type */
	
	public static function CheckDBtype($type)
	{
		return (!in_array($type,PDO::getAvailableDrivers()));
		
		
	}

	public static function getDBVersion ()
	{
		if (!defined('MX_SQL_VERSION')) {

			try {
				self::_setDefault();
				$db= self::connect();
				self::$checktypes=false;
				self::$nostack=true;
				$result=$db->query("SELECT VERSION() as version");
				list($mysqlversion) = $result->fetch();
				define('MX_SQL_VERSION', $mysqlversion);
				return ;
				
			} 
			 catch (PDOException $ex)
			{
				throw new myDbException($ex);
			}		
		}			
	}
	/*  
	*	setzt die Datenbank-Verbindungsdaten
	*
	*/
	
	public static function set_Db ($database, array $dbase)
	{
		self::$dbConf[$database]=$dbase;
		return;
	}
	
	/* 
	* öffnet eine Datenbankverbindung
	*
	*/
	
	public static function connect($database="default")
	{											
       if (!isset(self::$dbConf[$database])) {
			if ($database=="default" or $database=="") {
				self::_setDefault();
				} else {
				throw new Exception("Unexisting db-config $database");
			}
		}
        if (!isset(self::$instances[$database]))
            self::$instances[$database] = self::_connect($database);

        return self::$instances[$database];	
	}

	/*
	* Schließt eine Datenbbankverbindung 
	*/
	
    public static function disconnect($database)
    {
        
        unset(self::$instances[$active]);
		
		return;
    }	
	
	/*
	* wählt eine Datenverbindung aus 
	*
	*/
	
	public static function select_db ($database,$dbi=NULL)
	{
		self::$dbHandle=($dbi===NULL)?self::$instances[$database]:$dbi;
		return self::$dbHandle;
	}
		

    private static function _query($dbi, $qry, array $params)
    {
		
		/* query-Type auslesen */
		$qry = trim($qry);
		$qry .= " ";     
		$qry=str_replace(array("\r","\n"),array(" "," "),$qry);
		
		list($type,$dummy ) = explode(" ", strtolower($qry), 2);	
		if (trim($type)=="") return;
		
		/* check auf Listenausgabe ... */
		$listok=in_array($type, self::$dblisttypes);
		
		/* Verbindung festlegen */
		$dbi=($dbi==NULL)?self::$dbHandle:$dbi;
		;
	try
        {	
		
			self::$__dataset['lastInsertId'] = false;
			self::$__dataset['irowCount']= false;
			
			/* evtl. auf nicht erlaubte Query-Typen prüfen */
			if (!$GLOBALS['mxSkipSqlDetect'])  {
				if (in_array($type, self::$dbtypes) === false)  {
					trigger_error("Unsupported Query Type :" . $type . "-> ".$qry);
					return;
				}
			}
			/* query ausführen */
						
			$stmnt = $dbi->prepare($qry);
			$stmnt->execute();
						
			self::$__dataset['laststmnt'] = $stmnt;			
			self::$queryCounter++;
			
			if ($type === "update" or $type === "replace" or $type === "delete") self::$__dataset['irowCount'] = $stmnt->rowCount();

            if ($type === "insert" or $type === "replace") self::$__dataset['lastInsertId'] = $dbi->lastInsertId();
			           			
            return $stmnt;//($listok) ? $stmnt : self::$__dataset['irowCount'];
			
        } catch (PDOException $ex)
        {
            throw new myDbException($ex,$qry);
        }
    }

    private static function _getQueryType($qry)
    {
		$qry .= " ";
        list($type,$dummy ) = explode(" ", strtolower($qry), 2);
        return $type;
    }
	/*
	* gibt aktuelle Datenbankverbindung (Handle) zurück 
	*
	*/
    public static function getHandle()
    {
        return self::$dbHandle;
    }

	
	public static function quote($str)
    {
        return self::$dbHandle->quote($str);
    }

	
    public static function lastInsertId($dbi=NULL)
    {
		if ($dbi!==NULL) return $dbi->lastInsertId();
        return self::$__dataset['lastInsertId'];
    }

    /* public static function sql_rowCount()
    {
        return self::$__dataset['rowCount'];
    } */
	
	public static function affected_rows($stmnt=NULL)
	{
		global $dbi;
		if (!is_object($stmnt)) {
		  $stmnt = self::$__dataset['laststmnt'];
		}
		//return $this->irowCount;
		return self::$__dataset['irowCount'];
		//return ((is_object($stmnt))? $stmnt->rowCount():self::$__dataset['irowCount']);
		
		
	}

	
    public static function query($qry,$dbi=NULL)
    {
		$params=array();
		return self::_query($dbi,$qry,$params);
    }
	
	/* Abwärtskompatibilität */
	public static function num_rows ($stmnt)
	{
	    if (!is_object($stmnt)) {
        return 0;
		}
		return self::$rowCount($stmnt);
	}

	private function my_rowCount($queryString) {
        $regex = '/^SELECT\s+(?:ALL\s+|DISTINCT\s+)?(?:.*?)\s+FROM\s+(.*)$/i';
        if (preg_match($regex, $queryString, $output) > 0) {
            $stmt = parent::query("SELECT COUNT(*) FROM {$output[1]}", PDO::FETCH_NUM);

            return $stmt->fetchColumn();
        }

        return false;
    }	
	
	/**
	 * mxDetectCheckQuery()
	 *
	 * @param mixed $query
	 * @return
	 */	
	public static function DetectCheckQuery($query)
	{
		return mxDetectCheckQuery($query);
		//return pmxDetect::query($query);
	}
	
	public static function sql_error()
	{
		$tmp=self::$dbHandle->errorInfo();
		return $tmp;
		
	}
}

class myDbException extends PDOException {
	protected $code = NULL;
	protected $message = NULL;
	protected $file = NULL;
    
	public function __construct(PDOException $e,$query="") {
        
		$this->code = $e->getCode();
		
		switch ($this->code) {
			case 1045:
				$this->message = "Die Benutzerdaten zur Datenbank sind falsch angegeben!<br />The user data to the database are given wrong!";
				break;
			case 2005:
				$this->message = "Der Datenbank-Host ist falsch angegeben!<br />The database host is set incorrectly!";
				break;
			case 1049:
				$this->message = "Der Datenbank-Name ist falsch angegeben!<br />The database name is set incorrectly!";
				break;
			
		}

		if ($this->message) {
			if (!defined('MX_SQL_VERSION')) {
				define('MX_SQL_VERSION', '0');
			}

			/* Fehlerbehandlung */
			if ($this->code) {
				pmxDebug::sql_trigger_error($GLOBALS['dbtype'],$e->file, $this->message, $this->code,$query);
			} 
				$this->message = "Sorry, wir haben zur Zeit Probleme mit der Datenbank.<br/>Sorry, we have some database-problems.<br/><br/>(" . $this->code . ")";
			
				die(mxSqlPrepareMessage('<html><head><title>' . $GLOBALS['sitename'] . '</title></head><body text="#000080"><h1>' . $GLOBALS['sitename'] . '</h1>' . $this->message . '<br/><br/><br/>pragmaMx ' . PMX_VERSION . '</body></html>'));
			
		}
		
		$this->file= $e->getFile();
		$this->message=$e->message;
		
		pmxDebug::sql_trigger_error($GLOBALS['dbtype'],$this->file, $this->message, $this->code, $query);
		
		return;
				
	}
	

}

?>