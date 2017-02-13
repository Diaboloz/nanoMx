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

/**
 * pmxZip
 *
 * @package pragmaMx
 * @author terraproject
 * @copyright Copyright (c) 2011
 * @access public
 */


/*
*/
if (!defined("mxMainFileLoaded")) die ("You can't access this file directly...");

class pmxZip {
    private $config = array();
	private $zipfiles = array();
	private $zip ;

    public function __construct($parameter = null)
    {
			$this->_setDefault();
			$this->zip = new ZipArchive();
			$res = true;
			if (file_exists($parameter)) {
				$ret=self::open($parameter);
			}
			
		return $ret;
	}

	   /**
     * Configuration
     *
     * @param mixed $name
     * @return boolean true = ok
     * @return boolean false=Error
     */
    public function _setDefault()
    {
        global $prefix;
        
		$this->__set('autoopen',ZipArchive::OVERWRITE);
        $this->__set('autocreate', true);   // legt Zip-File an, wenn es nicht existiert
		$this->__set('zipname', "pmxzip.zip");  
		$this->__set('path', PMX_REAL_BASE_DIR."temp/");  
		
		
        return;
    }
   public function __set($name, $value = null)
    {
        /* in Array eintragen */
        if (is_array($name) && $value === null) {
            foreach ($name as $key => $value) {
                $this->config[$key] = $value;
            }
        } else {
            $this->config[$name] = $value;
        }
        return true;
    }
    public function __get($name)
    {
        if (array_key_exists($name, $this->config)) return $this->config[$name];
        return false;
    }
	
	public open ($zipfile='')
	{
	
	}
	function ZipStatusString( $status )
{
		switch( (int) $status )
		{
			case ZipArchive::ER_OK           : return 'N No error';
			case ZipArchive::ER_MULTIDISK    : return 'N Multi-disk zip archives not supported';
			case ZipArchive::ER_RENAME       : return 'S Renaming temporary file failed';
			case ZipArchive::ER_CLOSE        : return 'S Closing zip archive failed';
			case ZipArchive::ER_SEEK         : return 'S Seek error';
			case ZipArchive::ER_READ         : return 'S Read error';
			case ZipArchive::ER_WRITE        : return 'S Write error';
			case ZipArchive::ER_CRC          : return 'N CRC error';
			case ZipArchive::ER_ZIPCLOSED    : return 'N Containing zip archive was closed';
			case ZipArchive::ER_NOENT        : return 'N No such file';
			case ZipArchive::ER_EXISTS       : return 'N File already exists';
			case ZipArchive::ER_OPEN         : return 'S Can\'t open file';
			case ZipArchive::ER_TMPOPEN      : return 'S Failure to create temporary file';
			case ZipArchive::ER_ZLIB         : return 'Z Zlib error';
			case ZipArchive::ER_MEMORY       : return 'N Malloc failure';
			case ZipArchive::ER_CHANGED      : return 'N Entry has been changed';
			case ZipArchive::ER_COMPNOTSUPP  : return 'N Compression method not supported';
			case ZipArchive::ER_EOF          : return 'N Premature EOF';
			case ZipArchive::ER_INVAL        : return 'N Invalid argument';
			case ZipArchive::ER_NOZIP        : return 'N Not a zip archive';
			case ZipArchive::ER_INTERNAL     : return 'N Internal error';
			case ZipArchive::ER_INCONS       : return 'N Zip archive inconsistent';
			case ZipArchive::ER_REMOVE       : return 'S Can\'t remove file';
			case ZipArchive::ER_DELETED      : return 'N Entry has been deleted';
		   
			default: return sprintf('Unknown status %s', $status );
		}
	}	
}
?>