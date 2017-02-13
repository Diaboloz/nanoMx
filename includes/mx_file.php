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

define("FTP_NATIVE",FALSE); //pmxBase::mxFTPon()//

/*load_class ("Ftp",false);*/


/**
 * Setzen von File-Permissions (chmods)
 * DEPRECATED!! use mx_chmod() instead.
 *
 * @param string $file Angabe des Files dessen Berechtigungen geändert werden sollen (kopmletter
 * Pfad)
 * @param string $mode Modus lock ergibt chmod 444, unllock ergibt chmod 777)
 * @return bool Liefert true wenn die Aktion erfolgreich war, ansonsten false
 */
function mxChangeFilePerms($file, $mode)
{
	trigger_error('Use of deprecated phpnuke-function ' . __FUNCTION__ . '()', E_USER_WARNING);
    if (!file_exists($file)) {
        return false;
    }
    $err = false;
    if ($mode == 'lock') {
        mx_chmod($file, PMX_CHMOD_LOCK);
        $err = (is_writable($file));
    } else if ($mode == 'unlock') {
        mx_chmod($file, PMX_CHMOD_FULLUNOCK);
        $err = (!is_writable($file));
    }
    return (isset($php_errormsg) || $err) ? false : true;
}

/**
 * mx_chmod()
 *
 * @param mixed $path
 * @param mixed $mode
 * @return
 */
function mx_chmod($path, $mode, $umask = true)
{
    if (!function_exists('chmod')) {
        return false;
    }

    if ($umask && !function_exists('umask')) {
        $umask = false;
    }

    if ($umask) {
        $old = umask(0);
    }

    pmxDebug::pause();
    $result = chmod($path, $mode);
    pmxDebug::restore();

    if ($umask) {
        umask($old);
    }

    return $result;
}

/**
 * mx_write_file()
 * Schreibt eine Datei ohne Berücksichtigung der Schreibberechtigung.
 *
 * @param mixed $filename
 * @param mixed $content
 * @param mixed $lock
 * @return
 */
function mx_write_file($filename, $content, $lock = false)
{
    $folder = dirname($filename);

    switch (true) {
        /* wenn der Ordner nicht existiert kann auch nix geschrieben werden */
        case !file_exists($folder):
            return false;

            /* wenn ordner nicht beschreibbar ist */
        case !is_writable($folder):
            /* aktuellen chmod des Ordners zwischenspeichern */
            $dirmode = fileperms($folder);
            /* versuchen beschreibbar zu machen */
            mx_chmod($folder, PMX_CHMOD_FULLUNOCK);
            clearstatcache();
    }

    /* wenn Datei bereits existiert, aber schreibgeschützt ist */
    if (file_exists($filename) && !is_writable($filename)) {
        /* versuchen beschreibbar zu machen */
        mx_chmod($filename, PMX_CHMOD_UNLOCK);
        clearstatcache();
    }

    switch (true) {
        case !file_exists($filename): // Datei existiert nicht
        case is_writable($filename): // oder ist vorhanden und beschreibbar
            /* Inhalte schreiben */
            $result = file_put_contents($filename, $content);
            break;
        default: // Datei existiert ist aber nicht beschreibbar
            $result = false;
    }

    /* nach dem schreiben schreibschützen ? */
    if ($lock) {
        mx_chmod($filename, PMX_CHMOD_LOCK);
    }

    /* Wenn chmod des Ordners geändert wurde */
    if (isset($dirmode)) {
        /* den alten chmod des Ordners wieder herstellen */
        mx_chmod($folder, $dirmode, false); // chmod ohne umask!!
    }

    return $result !== false;
}


/**
 *  bool mx_copy ( string $source , string $dest [, resource $context ] )
 *  
 *  @param [string] $source  Parameter_Description
 *  @param [string] $dest    Parameter_Description
 *  
 *  @return Return_Description
 *  
 *  @details Details
 */

function mx_copy($source, $dest, $context=NULL) 
{
	if (FTP_NATIVE) //FTP verwenden
	{
		return pmxFtp::fcopy($source,$dest);
		
	} else {
		return @copy($source,$dest);
	}
	
	
}

/**
 *  @brief Brief
 *  
 *  @param [st] $dir Parameter_Description
 *  @return Return_Description
 *  
 *  @details Details
 *  
 */
 
function mx_chdir($dir)
{
	return chdir($dir);
}

/**
 *  @brief Brief
 *  
 *  @param [st] $dir Parameter_Description
 *  @return Return_Description
 *  
 *  @details Details
 *  
 */
 
function mx_scandir($dir)
{
	return scandir($dir);
}	 
	
/**
	 *  @brief Brief
	 *  
	 *  @param [st] $filename Parameter_Description
	 *  @return Return_Description
	 *  
	 *  @details Details
 */
function mx_is_file( string $filename) 
{
	return is_file($filename);
}

/**
 *  mx_is_writable
 *  
 *  param $filename
 *  return 
 *  
 */
function mx_is_writable($filename)
{
	if (!file_exists($filename)) {
		return true;
	}

	$oldmode = false;
	$oldmode = fileperms($filename);
	if (FTP_NATIVE) {
			/* aktuellen chmod der Datei zwischenspeichern */
			$oldmode = fileperms($filename);
			/* versuchen beschreibbar zu machen */
			@mxFTP::chmod($filename, PMX_CHMOD_UNLOCK);
			//clearstatcache();		
	} else {
		if (!is_writable($filename)) {
			/* aktuellen chmod der Datei zwischenspeichern */
			$oldmode = fileperms($filename);
			/* versuchen beschreibbar zu machen */
			@chmod($filename, PMX_CHMOD_UNLOCK);
			//clearstatcache();
		}
	}
		$result = is_writable($filename);

		if ($oldmode !== false) {
			@chmod($filename, $oldmode);
		}
	
	return $result;
}

function mx_stat($file) {

	clearstatcache();
	$ss=@stat($file);
	if(!$ss) return false; //Couldnt stat file

	$ts=array(
	  0140000=>'ssocket',
	  0120000=>'llink',
	  0100000=>'-file',
	  0060000=>'bblock',
	  0040000=>'ddir',
	  0020000=>'cchar',
	  0010000=>'pfifo'
	);

	$p=$ss['mode'];
	$t=decoct($ss['mode'] & 0170000); // File Encoding Bit

	$str =(array_key_exists(octdec($t),$ts))?$ts[octdec($t)]{0}:'u';
	$str.=(($p&0x0100)?'r':'-').(($p&0x0080)?'w':'-');
	$str.=(($p&0x0040)?(($p&0x0800)?'s':'x'):(($p&0x0800)?'S':'-'));
	$str.=(($p&0x0020)?'r':'-').(($p&0x0010)?'w':'-');
	$str.=(($p&0x0008)?(($p&0x0400)?'s':'x'):(($p&0x0400)?'S':'-'));
	$str.=(($p&0x0004)?'r':'-').(($p&0x0002)?'w':'-');
	$str.=(($p&0x0001)?(($p&0x0200)?'t':'x'):(($p&0x0200)?'T':'-'));

	$s=array(
	'perms'=>array(
	  'umask'=>sprintf("%04o",@umask()),
	  'human'=>$str,
	  'octal1'=>sprintf("%o", ($ss['mode'] & 000777)),
	  'octal2'=>sprintf("0%o", 0777 & $p),
	  'decimal'=>sprintf("%04o", $p),
	  'fileperms'=>@fileperms($file),
	  'mode1'=>$p,
	  'mode2'=>$ss['mode']),

	'owner'=>array(
	  'fileowner'=>$ss['uid'],
	  'filegroup'=>$ss['gid'],
	  'owner'=>
	  (function_exists('posix_getpwuid'))?
	  @posix_getpwuid($ss['uid']):'',
	  'group'=>
	  (function_exists('posix_getgrgid'))?
	  @posix_getgrgid($ss['gid']):''
	  ),

	'file'=>array(
	  'filename'=>$file,
	  'realpath'=>(@realpath($file) != $file) ? @realpath($file) : '',
	  'dirname'=>@dirname($file),
	  'basename'=>@basename($file)
	  ),

	'filetype'=>array(
	  'type'=>substr($ts[octdec($t)],1),
	  'type_octal'=>sprintf("%07o", octdec($t)),
	  'is_file'=>@is_file($file),
	  'is_dir'=>@is_dir($file),
	  'is_link'=>@is_link($file),
	  'is_readable'=> @is_readable($file),
	  'is_writable'=> @is_writable($file)
	  ),
	 
	'device'=>array(
	  'device'=>$ss['dev'], //Device
	  'device_number'=>$ss['rdev'], //Device number, if device.
	  'inode'=>$ss['ino'], //File serial number
	  'link_count'=>$ss['nlink'], //link count
	  'link_to'=>($ss['type']=='link') ? @readlink($file) : ''
	  ),

	'size'=>array(
	  'size'=>$ss['size'], //Size of file, in bytes.
	  'blocks'=>$ss['blocks'], //Number 512-byte blocks allocated
	  'block_size'=> $ss['blksize'] //Optimal block size for I/O.
	  ),

	'time'=>array(
	  'mtime'=>$ss['mtime'], //Time of last modification
	  'atime'=>$ss['atime'], //Time of last access.
	  'ctime'=>$ss['ctime'], //Time of last status change
	  'accessed'=>@date('Y M D H:i:s',$ss['atime']),
	  'modified'=>@date('Y M D H:i:s',$ss['mtime']),
	  'created'=>@date('Y M D H:i:s',$ss['ctime'])
	  ),
	);

	clearstatcache();
	return $s;
}


?>