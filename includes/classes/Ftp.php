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
 * $Revision: 23 $
 * $Author: PragmaMx $
 * $Date: 2015-07-13 18:57:06 +0200 (Mo, 13. Jul 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

/** Error Codes:
 * - 30 : Unable to connect to host
 * - 31 : Not connected
 * - 32 : Unable to send command to server
 * - 33 : Bad username
 * - 34 : Bad password
 * - 35 : Bad response
 * - 36 : Passive mode failed
 * - 37 : Data transfer error
 * - 38 : Local filesystem error
 */
 


if (!defined('CRLF'))
{
	define('CRLF', "\r\n");
}

if (!defined("FTP_AUTOASCII"))
{
	define("FTP_AUTOASCII", -1);
}

if (!defined("FTP_BINARY"))
{
	define("FTP_BINARY", 1);
}

if (!defined("FTP_ASCII"))
{
	define("FTP_ASCII", 0);
}

if (!defined('FTP_NATIVE'))
{
	define('FTP_NATIVE', ((pmxBase::mxFTPon()==1) && function_exists('ftp_connect'))? true : false) ;
}

class pmxFTP
{
	/*	$_ftph resource 	*/
	private static  $_ftph = false;
	
	private static  $_conf=array();
	
	/*$_autoAscii  array  Array to hold ascii format file extensions */
	protected $_autoAscii = array(
		"asp",
		"bat",
		"c",
		"cpp",
		"csv",
		"h",
		"htm",
		"html",
		"shtml",
		"ini",
		"inc",
		"log",
		"php",
		"php3",
		"pl",
		"perl",
		"sh",
		"sql",
		"txt",
		"xhtml",
		"xml");
	
	/* 
     * Ein private Konstruktor; verhindert die direkte Erzeugung des Objektes
     */
    private function __construct()
    {
    }
	
	private function _connect()
	{
		if (self::$_ftph==false) $this->_login($_conf);
	}
	
    /**
     *  _login
     * 
     * @param <type> $options 
     * 
     * @return <type>
     */
	private static function _login ($options=array()) 
	{
		mxGetLangfile(dirname(__FILE__) . DS . 'Ftp' . DS . 'language');
		if (!self::$_ftph) {
			if (!function_exists('ftp_connect')) {
				/* keine FTP möglich */
				self::_error(FTP_NO_CONNECT_SOCKET);				
				return false;
			}
			if (pmxBase::mxFTPssl()=="1") {
				self::$_ftph=@ftp_ssl_connect(pmxBase::mxFTPhost(),pmxBase::mxFTPport());
			} else {
				self::$_ftph=@ftp_connect(pmxBase::mxFTPhost(),pmxBase::mxFTPport());
			}
			if (self::$_ftph) {
				/* Verbindung aufgebaut */
				if (!@ftp_login(self::$_ftph,pmxBase::mxFTPuser(),pmxBase::mxFTPpass())) {
					/* login fehlgeschlagen */
					self::_error(FTP_SYS_BAD_RESPONSE_NATIVE);
					@ftp_close(self::$_ftph);
					self::$_ftph=false;
					return false;
				}
				/* passivmodus erzwingen */
				@ftp_pasv (self::$_ftph,true);
				/* ins Basisverzeichnis wechseln */
				$path= (trim(pmxBase::mxFTPdir())=="")?"/":pmxBase::mxFTPdir();
				@ftp_chdir(self::$_ftph, $path);
				//echo ftp_pwd(self::$_ftph);
				
			} else {
				/* Verbindung fehlgeschlagen */
				self::_error(FTP_NO_CONNECT_SOCKET);
				self::$_ftph=false;
			}
		}
		return self::$_ftph;
	}
 
	public static function ServerType ()
	{
		if (self::$_ftph) {
			return @ftp_systype(self::$_ftph);	
		}
		return false;
	}
 
    /**
     * 
     * 
     * 
     * @return 
     */
	private static function _logout()
	{
		if (self::$_ftph) {
			@ftp_close(self::$_ftph);
			self::$_ftph=false;
		}
	}
	
	private static function _reconnect()
	{
		if (@ftp_site(self::$_ftph, 'REIN') === false) {
			self::_logout();
		}
	}
	
    /**
 
     */
	static function connect()
	{
		return self::_login();
	}
	
	static function isconnect()
	{
		return (self::_login())?true:false;
	}	
	
	
	static function fcopy($source, $dest, $context=NULL)
	{
		$fp = fopen($source, 'r');
		$iscopy=@ftp_put(self::$_ftph, $dest, $fp, self::_findmode($source));
		fclose($fp);
		
		if (!$iscopy) {
			self::_error("$file ".FTP_STORE_BAD_RESPONSE);
		}		
		return $iscopy;
	}
	
	static function chdir($ftp_path)
	{
		self::_login();
		
		// No slash on the end, please...
		if ($ftp_path !== '/' && substr($ftp_path, -1) === '/')
			$ftp_path = substr($ftp_path, 0, -1);	
		
		return @ftp_chdir(self::$_ftph,$ftp_path);
		
	}
	
	
	static function chmod($ftp_file,$chmode)
	{
		self::_login();
		
		if ($ftp_file == '')
			$ftp_file = '.';
		
		$chmode=(intval($chmode)==0)?755:intval($chmode);
		
		return @ftp_chmod(self::$_ftph,"0".$chmode, $ftp_file);	
	}
	
	static function delete($ftp_file)
	{
		self::_login();
		if ($ftp_file == '' or !is_file($ftp_file)) return false;
		
		return @ftp_delete(self::$_ftph,$ftp_file);
	}
	
	/**
	 * Method to find out the correct transfer mode for a specific file
	 *
	 * @param   string  $fileName  Name of the file
	 *
	 * @return  integer Transfer-mode for this filetype [FTP_ASCII|FTP_BINARY]
	 *
	 *
	 */
	protected static function _findMode($fileName)
	{
		$dot = strrpos($fileName, '.') + 1;
		$ext = substr($fileName, $dot);

		if (in_array($ext, $this->_autoAscii))
		{
			$mode = FTP_ASCII;
		}
		else
		{
			$mode = FTP_BINARY;
		}
		return $mode;
	}	
	
	static function fstat($file)
	{
		
	}
	
	protected static function _error($errstr,$mode=E_USER_WARNING){
		
		trigger_error("FTP ERROR:  - ".$errstr, $mode);
	}	

	static function parse_rawlist( $array )
	{
		foreach($array as $curraw)
		{
			$struc = array();
			$current = preg_split("/[\s]+/",$curraw,9);

			$struc['perms']  = $current[0];
			$struc['number'] = $current[1];
			$struc['owner']  = $current[2];
			$struc['group']  = $current[3];
			$struc['size']  = $current[4];
			$struc['month']  = $current[5];
			$struc['day']    = $current[6];
			$struc['time']  = $current[7];
			$struc['year']  = $current[8];
			$struc['raw']  = $curraw;
			$structure[$struc['name']] = $struc;
		}
	   return $structure;
	}	
}


/**
 * FTP client class
 *
 * @since  2.2.6.
 */
class FtpBase
{
	/**
	 * @var    resource  Socket resource
	 * @since  12.1
	 */
	protected $_conn = null;

	/**
	 * @var    resource  Data port connection resource
	 * @since  12.1
	 */
	protected $_dataconn = null;

	/**
	 * @var    array  Passive connection information
	 * @since  12.1
	 */
	protected $_pasv = null;

	/**
	 * @var    string  Response Message
	 * @since  12.1
	 */
	protected $_response = null;

	/**
	 * @var    integer  Timeout limit
	 * @since  12.1
	 */
	protected $_timeout = 15;

	/**
	 * @var    integer  Transfer Type
	 * @since  12.1
	 */
	protected $_type = null;

	/**
	 * @var    array  Array to hold ascii format file extensions
	 *
	 */
	protected $_autoAscii = array(
		"asp",
		"bat",
		"c",
		"cpp",
		"csv",
		"h",
		"htm",
		"html",
		"shtml",
		"ini",
		"inc",
		"log",
		"php",
		"php3",
		"pl",
		"perl",
		"sh",
		"sql",
		"txt",
		"xhtml",
		"xml");

	/**
	 * Array to hold native line ending characters
	 *
	 * @var    array
	 * @since  12.1
	 */
	protected $_lineEndings = array('UNIX' => "\n", 'WIN' => "\r\n");

	/**
	 * @var    array  FTP instances container.
	 * @since  12.1
	 */
	protected static $instances = array();

	/**
	 * FTP object constructor
	 *
	 * @param   array  $options  Associative array of options to set
	 *
	 *
	 */
	public function __construct(array $options = array())
	{
		// If default transfer type is not set, set it to autoascii detect
		if (!isset($options['type']))
		{
			$options['type'] = FTP_BINARY;
		}

		$this->setOptions($options);

		if (FTP_NATIVE)
		{
			// Import the generic buffer stream handler
			//jimport('joomla.utilities.buffer');

			// Autoloading fails for JBuffer as the class is used as a stream handler
			//JLoader::load('JBuffer');
		}
	}

	/**
	 * FTP object destructor
	 *
	 * Closes an existing connection, if we have one
	 *
	 *
	 */
	public function __destruct()
	{
		if (is_resource($this->_conn))
		{
			$this->quit();
		}
	}

	/**
	 * Returns the global FTP connector object, only creating it
	 * if it doesn't already exist.
	 *
	 * You may optionally specify a username and password in the parameters. If you do so,
	 * you may not login() again with different credentials using the same object.
	 * If you do not use this option, you must quit() the current connection when you
	 * are done, to free it for use by others.
	 *
	 * @param   string  $host     Host to connect to
	 * @param   string  $port     Port to connect to
	 * @param   array   $options  Array with any of these options: type=>[FTP_AUTOASCII|FTP_ASCII|FTP_BINARY], timeout=>(int)
	 * @param   string  $user     Username to use for a connection
	 * @param   string  $pass     Password to use for a connection
	 *
	 * @return          The FTP Client object.
	 *

	 */
	public static function getInstance($host = '127.0.0.1', $port = '21', array $options = array(), $user = null, $pass = null)
	{
		$signature = $user . ':' . $pass . '@' . $host . ":" . $port;

		// Create a new instance, or set the options of an existing one
		if (!isset(static::$instances[$signature]) || !is_object(static::$instances[$signature]))
		{
			static::$instances[$signature] = new static($options);
		}
		else
		{
			static::$instances[$signature]->setOptions($options);
		}

		// Connect to the server, and login, if requested
		if (!static::$instances[$signature]->isConnected())
		{
			$return = static::$instances[$signature]->connect($host, $port);

			if ($return && $user !== null && $pass !== null)
			{
				static::$instances[$signature]->login($user, $pass);
			}
		}

		return static::$instances[$signature];
	}

	/**
	 * Set client options
	 *
	 * @param   array  $options  Associative array of options to set
	 *
	 * @return  boolean  True if successful
	 *
	 *
	 */
	public function setOptions(array $options)
	{
		if (isset($options['type']))
		{
			$this->_type = $options['type'];
		}

		if (isset($options['timeout']))
		{
			$this->_timeout = $options['timeout'];
		}

		return true;
	}

	/**
	 * Method to connect to a FTP server
	 *
	 * @param   string  $host  Host to connect to [Default: 127.0.0.1]
	 * @param   string  $port  Port to connect on [Default: port 21]
	 *
	 * @return  boolean  True if successful
	 *
	 *
	 */
	public function connect($host = '127.0.0.1', $port = 21)
	{
		$errno = null;
		$err = null;

		// If already connected, return
		if (is_resource($this->_conn))
		{
			return true;
		}

		
			$this->_conn = @ftp_connect($host, $port, $this->_timeout);

			if ($this->_conn === false)
			{
				$this->_error(FTP_NO_CONNECT_SOCKET ." Host:". $host ." Port:". $port , E_USER_ERROR);

				return false;
			}
			// Set the timeout for this connection
			ftp_set_option($this->_conn, FTP_TIMEOUT_SEC, $this->_timeout);

			return true;
	}


	/**
	 * Method to determine if the object is connected to an FTP server
	 *
	 * @return  boolean  True if connected
	 *
	 *
	 */
	public function isConnected()
	{
		return is_resource($this->_conn);
	}

	/**
	 * Method to login to a server once connected
	 *
	 * @param   string  $user  Username to login to the server
	 * @param   string  $pass  Password to login to the server
	 *
	 * @return  boolean  True if successful
	 *
	 *
	 */
	public function login($user = 'anonymous', $pass = 'jftp@joomla.org')
	{
		

			if (@ftp_login($this->_conn, $user, $pass) === false)
			{
				$this->_error('Ftp::login: Unable to login', E_USER_WARNING);

				return false;
			}

			return true;

	}

	/**
	 * Method to quit and close the connection
	 *
	 * @return  boolean  True if successful
	 *
	 *
	 */
	public function quit()
	{
		

			@ftp_close($this->_conn);

			return true;

	}

	/**
	 * Method to retrieve the current working directory on the FTP server
	 *
	 * @return  string   Current working directory
	 *
	 *
	 */
	public function pwd()
	{


			if (($ret = @ftp_pwd($this->_conn)) === false)
			{
				$this->_error(FTP_PWD_BAD_RESPONSE_NATIVE,E_USER_WARNING);

				return false;
			}

			return $ret;

	}

	/**
	 * Method to system string from the FTP server
	 *
	 * @return  string   System identifier string
	 *
	 *
	 */
	public function syst()
	{

			if (($ret = @ftp_systype($this->_conn)) === false)
			{
				$this->_error(FTP_SYS_BAD_RESPONSE_NATIVE,E_USER_WARNING);

				return false;
			}


		// Match the system string to an OS
		if (strpos(strtoupper($ret), 'MAC') !== false)
		{
			$ret = 'MAC';
		}
		elseif (strpos(strtoupper($ret), 'WIN') !== false)
		{
			$ret = 'WIN';
		}
		else
		{
			$ret = 'UNIX';
		}

		// Return the os type
		return $ret;
	}

	/**
	 * Method to change the current working directory on the FTP server
	 *
	 * @param   string  $path  Path to change into on the server
	 *
	 * @return  boolean True if successful
	 *
	 *
	 */
	public function chdir($path)
	{


			if (@ftp_chdir($this->_conn, $path) === false)
			{
				$this->_error(FTP_CHDIR_BAD_RESPONSE_NATIVE, E_USER_WARNING);

				return false;
			}

			return true;

	}

	/**
	 * Method to reinitialise the server, ie. need to login again
	 *
	 * NOTE: This command not available on all servers
	 *
	 * @return  boolean  True if successful
	 *
	 *
	 */
	public function reinit()
	{


			if (@ftp_site($this->_conn, 'REIN') === false)
			{
				$this->_error(FTP_REINIT_BAD_RESPONSE_NATIVE, E_USER_WARNING);

				return false;
			}

			return true;

	}

	/**
	 * Method to rename a file/folder on the FTP server
	 *
	 * @param   string  $from  Path to change file/folder from
	 * @param   string  $to    Path to change file/folder to
	 *
	 * @return  boolean  True if successful
	 *
	 *
	 */
	public function rename($from, $to)
	{

			if (@ftp_rename($this->_conn, $from, $to) === false)
			{
				$this->_error(FTP_RENAME_BAD_RESPONSE_NATIVE, E_USER_WARNING);

				return false;
			}

			return true;
		


	}

	/**
	 * Method to change mode for a path on the FTP server
	 *
	 * @param   string  $path  Path to change mode on
	 * @param   mixed   $mode  Octal value to change mode to, e.g. '0777', 0777 or 511 (string or integer)
	 *
	 * @return  boolean  True if successful
	 *
	 *
	 */
	public function chmod($path, $mode)
	{
		// If no filename is given, we assume the current directory is the target
		if ($path == '')
		{
			$path = '.';
		}

		// Convert the mode to a string
		if (is_int($mode))
		{
			$mode = decoct($mode);
		}


			if (@ftp_site($this->_conn, 'CHMOD ' . $mode . ' ' . $path) === false)
			{
				if (!IS_WIN)
				{
					$this->_error(FTP_CHMOD_BAD_RESPONSE_NATIVE,E_USER_WARNING);
				}

				return false;
			}

			return true;

	}

	/**
	 * Method to delete a path [file/folder] on the FTP server
	 *
	 * @param   string  $path  Path to delete
	 *
	 * @return  boolean  True if successful
	 *
	 *
	 */
	public function delete($path)
	{

			if (@ftp_delete($this->_conn, $path) === false)
			{
				if (@ftp_rmdir($this->_conn, $path) === false)
				{
					$this->_error(FTP_DELETE_BAD_RESPONSE_NATIVE,E_USER_WARNING);

					return false;
				}
			}

			return true;

	}

	/**
	 * Method to create a directory on the FTP server
	 *
	 * @param   string  $path  Directory to create
	 *
	 * @return  boolean  True if successful
	 *
	 *
	 */
	public function mkdir($path)
	{


			if (@ftp_mkdir($this->_conn, $path) === false)
			{
				$this->_error(FTP_MKDIR_BAD_RESPONSE_NATIVE,E_USER_WARNING);

				return false;
			}

			return true;

	}

	/**
	 * Method to restart data transfer at a given byte
	 *
	 * @param   integer  $point  Byte to restart transfer at
	 *
	 * @return  boolean  True if successful
	 *
	 *
	 */
	public function restart($point)
	{

			if (@ftp_site($this->_conn, 'REST ' . $point) === false)
			{
				$this->_error(FTP_RESTART_BAD_RESPONSE_NATIVE,E_USER_WARNING);

				return false;
			}

			return true;

	}

	/**
	 * Method to create an empty file on the FTP server
	 *
	 * @param   string  $path  Path local file to store on the FTP server
	 *
	 * @return  boolean  True if successful
	 *
	 *
	 */
	public function create($path)
	{

			// Turn passive mode on
			if (@ftp_pasv($this->_conn, true) === false)
			{
				$this->_error(FTP_BAD_RESPONSE_PASSIVE);

				return false;
			}

			$buffer = fopen('buffer://tmp', 'r');

			if (@ftp_fput($this->_conn, $path, $buffer, FTP_ASCII) === false)
			{
				$this->_error(FTP_CREATE_BAD_RESPONSE_BUFFER.":".$path);
				fclose($buffer);

				return false;
			}

			fclose($buffer);

			return true;

	}

	/**
	 * Method to read a file from the FTP server's contents into a buffer
	 *
	 * @param   string  $remote   Path to remote file to read on the FTP server
	 * @param   string  &$buffer  Buffer variable to read file contents into
	 *
	 * @return  boolean  True if successful
	 *
	 *
	 */
	public function read($remote, &$buffer)
	{
		// Determine file type
		$mode = $this->_findMode($remote);


			// Turn passive mode on
			if (@ftp_pasv($this->_conn, true) === false)
			{
				$this->_error(FTP_BAD_RESPONSE_PASSIVE);

				return false;
			}

			$tmp = fopen('buffer://tmp', 'br+');

			if (@ftp_fget($this->_conn, $tmp, $remote, $mode) === false)
			{
				fclose($tmp);
				$this->_error(FTP_READ_BAD_RESPONSE_BUFFER.":".$remote);

				return false;
			}
			// Read tmp buffer contents
			rewind($tmp);
			$buffer = '';

			while (!feof($tmp))
			{
				$buffer .= fread($tmp, 8192);
			}

			fclose($tmp);

			return true;

	}

	/**
	 * Method to get a file from the FTP server and save it to a local file
	 *
	 * @param   string  $local   Local path to save remote file to
	 * @param   string  $remote  Path to remote file to get on the FTP server
	 *
	 * @return  boolean  True if successful
	 *
	 *
	 */
	public function get($local, $remote)
	{
		// Determine file type
		$mode = $this->_findMode($remote);


			// Turn passive mode on
			if (@ftp_pasv($this->_conn, true) === false)
			{
				$this->_error(FTP_BAD_RESPONSE_PASSIVE);

				return false;
			}

			if (@ftp_get($this->_conn, $local, $remote, $mode) === false)
			{
				$this->_error(FTP_GET_BAD_RESPONSE.":".$remote ." ". _TO ." ". $local);

				return false;
			}

			return true;

	}

	/**
	 * Method to store a file to the FTP server
	 *
	 * @param   string  $local   Path to local file to store on the FTP server
	 * @param   string  $remote  FTP path to file to create
	 *
	 * @return  boolean  True if successful
	 *
	 *
	 */
	public function store($local, $remote = null)
	{
		// If remote file is not given, use the filename of the local file in the current
		// working directory.
		if ($remote == null)
		{
			$remote = basename($local);
		}

		// Determine file type
		$mode = $this->_findMode($remote);


			// Turn passive mode on
			if (@ftp_pasv($this->_conn, true) === false)
			{
				$this->_error(FTP_BAD_RESPONSE_PASSIVE);

				return false;
			}

			if (@ftp_put($this->_conn, $remote, $local, $mode) === false)
			{
				trigger_error(FTP_STORE_BAD_RESPONSE.": ".$local ." ". _TO ." ".$remote);

				return false;
			}

			return true;

	}

	/**
	 * Method to write a string to the FTP server
	 *
	 * @param   string  $remote  FTP path to file to write to
	 * @param   string  $buffer  Contents to write to the FTP server
	 *
	 * @return  boolean  True if successful
	 *
	 *
	 */
	public function write($remote, $buffer)
	{
		// Determine file type
		$mode = $this->_findMode($remote);


			// Turn passive mode on
			if (@ftp_pasv($this->_conn, true) === false)
			{
				$this->_error(FTP_BAD_RESPONSE_PASSIVE);

				return false;
			}

			$tmp = fopen('buffer://tmp', 'br+');
			fwrite($tmp, $buffer);
			rewind($tmp);

			if (@ftp_fput($this->_conn, $remote, $tmp, $mode) === false)
			{
				fclose($tmp);
				$this->_error(FTP_WRITE_BAD_RESPONSE.": ".$remote);

				return false;
			}

			fclose($tmp);

			return true;

	}

	/**
	 * Method to list the filenames of the contents of a directory on the FTP server
	 *
	 * Note: Some servers also return folder names. However, to be sure to list folders on all
	 * servers, you should use listDetails() instead if you also need to deal with folders
	 *
	 * @param   string  $path  Path local file to store on the FTP server
	 *
	 * @return  string  Directory listing
	 *
	 *
	 */
	public function listNames($path = null)
	{
		$data = null;


			// Turn passive mode on
			if (@ftp_pasv($this->_conn, true) === false)
			{
				$this->_error(FTP_BAD_RESPONSE_PASSIVE);

				return false;
			}

			if (($list = @ftp_nlist($this->_conn, $path)) === false)
			{
				// Workaround for empty directories on some servers
				if ($this->listDetails($path, 'files') === array())
				{
					return array();
				}

				$this->_error(FTP_LISTNAMES_BAD_RESPONSE.": ".$path);

				return false;
			}

			$list = preg_replace('#^' . preg_quote($path, '#') . '[/\\\\]?#', '', $list);

			if ($keys = array_merge(array_keys($list, '.'), array_keys($list, '..')))
			{
				foreach ($keys as $key)
				{
					unset($list[$key]);
				}
			}

			return $list;

	}

	/**
	 * Method to list the contents of a directory on the FTP server
	 *
	 * @param   string  $path  Path to the local file to be stored on the FTP server
	 * @param   string  $type  Return type [raw|all|folders|files]
	 *
	 * @return  mixed  If $type is raw: string Directory listing, otherwise array of string with file-names
	 *
	 *
	 */
	public function listDetails($path = null, $type = 'all')
	{
		$dir_list = array();
		$data = null;
		$regs = null;

		// TODO: Deal with recurse -- nightmare
		// For now we will just set it to false
		$recurse = false;

		// If native FTP support is enabled let's use it...

			// Turn passive mode on
			if (@ftp_pasv($this->_conn, true) === false)
			{
				$this->_error(FTP_BAD_RESPONSE_PASSIVE);

				return false;
			}


			if (($contents = @ftp_rawlist($this->_conn, $path)) === false)
			{
				$this->_error(FTP_LISTDETAILS_BAD_RESPONSE.":".$path);

				return false;
			}


		// If only raw output is requested we are done
		if ($type == 'raw')
		{
			return $data;
		}

		// If we received the listing of an empty directory, we are done as well
		if (empty($contents[0]))
		{
			return $dir_list;
		}

		// If the server returned the number of results in the first response, let's dump it
		if (strtolower(substr($contents[0], 0, 6)) == 'total ')
		{
			array_shift($contents);

			if (!isset($contents[0]) || empty($contents[0]))
			{
				return $dir_list;
			}
		}

		// Regular expressions for the directory listing parsing.
		$regexps = array(
			'UNIX' => '#([-dl][rwxstST-]+).* ([0-9]*) ([a-zA-Z0-9]+).* ([a-zA-Z0-9]+).* ([0-9]*)'
				. ' ([a-zA-Z]+[0-9: ]*[0-9])[ ]+(([0-9]{1,2}:[0-9]{2})|[0-9]{4}) (.+)#',
			'MAC' => '#([-dl][rwxstST-]+).* ?([0-9 ]*)?([a-zA-Z0-9]+).* ([a-zA-Z0-9]+).* ([0-9]*)'
				. ' ([a-zA-Z]+[0-9: ]*[0-9])[ ]+(([0-9]{2}:[0-9]{2})|[0-9]{4}) (.+)#',
			'WIN' => '#([0-9]{2})-([0-9]{2})-([0-9]{2}) +([0-9]{2}):([0-9]{2})(AM|PM) +([0-9]+|<DIR>) +(.+)#'
		);

		// Find out the format of the directory listing by matching one of the regexps
		$osType = null;

		foreach ($regexps as $k => $v)
		{
			if (@preg_match($v, $contents[0]))
			{
				$osType = $k;
				$regexp = $v;
				break;
			}
		}

		if (!$osType)
		{
			$this->_error(FTP_LISTDETAILS_UNRECOGNISED);

			return false;
		}

		// Here is where it is going to get dirty....
		if ($osType == 'UNIX' || $osType == 'MAC')
		{
			foreach ($contents as $file)
			{
				$tmp_array = null;

				if (@preg_match($regexp, $file, $regs))
				{
					$fType = (int) strpos("-dl", $regs[1]{0});

					// $tmp_array['line'] = $regs[0];
					$tmp_array['type'] = $fType;
					$tmp_array['rights'] = $regs[1];

					// $tmp_array['number'] = $regs[2];
					$tmp_array['user'] = $regs[3];
					$tmp_array['group'] = $regs[4];
					$tmp_array['size'] = $regs[5];
					$tmp_array['date'] = @date("m-d", strtotime($regs[6]));
					$tmp_array['time'] = $regs[7];
					$tmp_array['name'] = $regs[9];
				}

				// If we just want files, do not add a folder
				if ($type == 'files' && $tmp_array['type'] == 1)
				{
					continue;
				}

				// If we just want folders, do not add a file
				if ($type == 'folders' && $tmp_array['type'] == 0)
				{
					continue;
				}

				if (is_array($tmp_array) && $tmp_array['name'] != '.' && $tmp_array['name'] != '..')
				{
					$dir_list[] = $tmp_array;
				}
			}
		}
		else
		{
			foreach ($contents as $file)
			{
				$tmp_array = null;

				if (@preg_match($regexp, $file, $regs))
				{
					$fType = (int) ($regs[7] == '<DIR>');
					$timestamp = strtotime("$regs[3]-$regs[1]-$regs[2] $regs[4]:$regs[5]$regs[6]");

					// $tmp_array['line'] = $regs[0];
					$tmp_array['type'] = $fType;
					$tmp_array['rights'] = '';

					// $tmp_array['number'] = 0;
					$tmp_array['user'] = '';
					$tmp_array['group'] = '';
					$tmp_array['size'] = (int) $regs[7];
					$tmp_array['date'] = date('m-d', $timestamp);
					$tmp_array['time'] = date('H:i', $timestamp);
					$tmp_array['name'] = $regs[8];
				}
				// If we just want files, do not add a folder
				if ($type == 'files' && $tmp_array['type'] == 1)
				{
					continue;
				}
				// If we just want folders, do not add a file
				if ($type == 'folders' && $tmp_array['type'] == 0)
				{
					continue;
				}

				if (is_array($tmp_array) && $tmp_array['name'] != '.' && $tmp_array['name'] != '..')
				{
					$dir_list[] = $tmp_array;
				}
			}
		}

		return $dir_list;
	}

	/**
	 * Send command to the FTP server and validate an expected response code
	 *
	 * @param   string  $cmd               Command to send to the FTP server
	 * @param   mixed   $expectedResponse  Integer response code or array of integer response codes
	 *
	 * @return  boolean  True if command executed successfully
	 *
	 *
	 */
	protected function _putCmd($cmd, $expectedResponse)
	{
		// Make sure we have a connection to the server
		if (!is_resource($this->_conn))
		{
			$this->_error(FTP_NO_CONNECT_SOCKET);

			return false;
		}

		// Send the command to the server
		if (!fwrite($this->_conn, $cmd . "\r\n"))
		{
			$this->_erro(FTP_PUTCMD_SEND);
		}

		return $this->_verifyResponse($expectedResponse);
	}

	/**
	 * Verify the response code from the server and log response if flag is set
	 *
	 * @param   mixed  $expected  Integer response code or array of integer response codes
	 *
	 * @return  boolean  True if response code from the server is expected
	 *
	 *
	 */
	protected function _verifyResponse($expected)
	{
		$parts = null;

		// Wait for a response from the server, but timeout after the set time limit
		$endTime = time() + $this->_timeout;
		$this->_response = '';

		do
		{
			$this->_response .= fgets($this->_conn, 4096);
		}
		while (!preg_match("/^([0-9]{3})(-(.*" . CRLF . ")+\\1)? [^" . CRLF . "]+" . CRLF . "$/", $this->_response, $parts) && time() < $endTime);

		// Catch a timeout or bad response
		if (!isset($parts[1]))
		{
			$this->_error(FTP_VERIFYRESPONSE." ". $this->_response);

			return false;
		}

		// Separate the code from the message
		$this->_responseCode = $parts[1];
		$this->_responseMsg = $parts[0];

		// Did the server respond with the code we wanted?
		if (is_array($expected))
		{
			if (in_array($this->_responseCode, $expected))
			{
				$retval = true;
			}
			else
			{
				$retval = false;
			}
		}
		else
		{
			if ($this->_responseCode == $expected)
			{
				$retval = true;
			}
			else
			{
				$retval = false;
			}
		}

		return $retval;
	}

	/**
	 * Set server to passive mode and open a data port connection
	 *
	 * @return  boolean  True if successful
	 *
	 *
	 */
	protected function _passive()
	{
		$match = array();
		$parts = array();
		$errno = null;
		$err = null;

		// Make sure we have a connection to the server
		if (!is_resource($this->_conn))
		{
			$this->_error(FTP_NO_CONNECT_SOCKET);

			return false;
		}

		// Request a passive connection - this means, we'll talk to you, you don't talk to us.
		@ fwrite($this->_conn, "PASV\r\n");

		// Wait for a response from the server, but timeout after the set time limit
		$endTime = time() + $this->_timeout;
		$this->_response = '';

		do
		{
			$this->_response .= fgets($this->_conn, 4096);
		}
		while (!preg_match("/^([0-9]{3})(-(.*" . CRLF . ")+\\1)? [^" . CRLF . "]+" . CRLF . "$/", $this->_response, $parts) && time() < $endTime);

		// Catch a timeout or bad response
		if (!isset($parts[1]))
		{
			$this->_error(FTP_VERIFYRESPONSE." ". $this->_response);

			return false;
		}

		// Separate the code from the message
		$this->_responseCode = $parts[1];
		$this->_responseMsg = $parts[0];

		// If it's not 227, we weren't given an IP and port, which means it failed.
		if ($this->_responseCode != '227')
		{
			$this->_error(FTP_VERIFYRESPONSE." ". $this->_responseMsg);

			return false;
		}

		// Snatch the IP and port information, or die horribly trying...
		if (preg_match('~\((\d+),\s*(\d+),\s*(\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+))\)~', $this->_responseMsg, $match) == 0)
		{
			$this->_error(FTP_VERIFYRESPONSE." ". $this->_responseMsg);
			return false;
		}

		// This is pretty simple - store it for later use ;).
		$this->_pasv = array('ip' => $match[1] . '.' . $match[2] . '.' . $match[3] . '.' . $match[4], 'port' => $match[5] * 256 + $match[6]);

		// Connect, assuming we've got a connection.
		$this->_dataconn = @fsockopen($this->_pasv['ip'], $this->_pasv['port'], $errno, $err, $this->_timeout);

		if (!$this->_dataconn)
		{
			$this->_error(FTP_VERIFYRESPONSE." ".  $this->_pasv['ip'] ." . " .$this->_pasv['port']."  [" .$errno."]  " .$err);
			return false;
		}

		// Set the timeout for this connection
		socket_set_timeout($this->_conn, $this->_timeout, 0);

		return true;
	}

	/**
	 * Method to find out the correct transfer mode for a specific file
	 *
	 * @param   string  $fileName  Name of the file
	 *
	 * @return  integer Transfer-mode for this filetype [FTP_ASCII|FTP_BINARY]
	 *
	 *
	 */
	protected function _findMode($fileName)
	{
		if ($this->_type == FTP_AUTOASCII)
		{
			$dot = strrpos($fileName, '.') + 1;
			$ext = substr($fileName, $dot);

			if (in_array($ext, $this->_autoAscii))
			{
				$mode = FTP_ASCII;
			}
			else
			{
				$mode = FTP_BINARY;
			}
		}
		elseif ($this->_type == FTP_ASCII)
		{
			$mode = FTP_ASCII;
		}
		else
		{
			$mode = FTP_BINARY;
		}

		return $mode;
	}

	/**
	 * Set transfer mode
	 *
	 * @param   integer  $mode  Integer representation of data transfer mode [1:Binary|0:Ascii]
	 * Defined constants can also be used [FTP_BINARY|FTP_ASCII]
	 *
	 * @return  boolean  True if successful
	 *
	 *
	 */
	protected function _mode($mode)
	{
		if ($mode == FTP_BINARY)
		{
			if (!$this->_putCmd("TYPE I", 200))
			{
				$this->_error(FTP_VERIFYRESPONSE." ". $this->_response);

				return false;
			}
		}
		else
		{
			if (!$this->_putCmd("TYPE A", 200))
			{
				$this->_error(FTP_VERIFYRESPONSE." ". $this->_response);

				return false;
			}
		}

		return true;
	}
	
	/**
	 * Set error
	 *
	 * 
	 * @return  
	 *
	 * $this->_error()
	 *
	 */	
	
	protected function _error($errstr,$mode=E_USER_WARNING){
		
		trigger_error("FTP ERROR: ".$errno." - ".$errstr, $mode);
	}
}

