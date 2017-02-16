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
 * $Revision: 171 $
 * $Author: PragmaMx $
 * $Date: 2016-06-29 13:59:03 +0200 (Mi, 29. Jun 2016) $
 *
 * based on: probe.php from http://www.a51dev.com
 */

define("STATUS_OK", "ok");
define("STATUS_WARNING", "warning");
define("STATUS_ERROR", "error");
define('PMX_CHMOD_LOCK', octdec('0444'));
define('PMX_CHMOD_NORMAL', octdec('0644'));
define('PMX_CHMOD_UNLOCK', octdec('0666'));
define('PMX_CHMOD_FULLOCK', octdec('0400'));
define('PMX_CHMOD_FULLUNOCK', octdec('0777'));

/* Ordner mit dynamischen Medien (Bilder, Dokumente, etc.) */
define('PMX_MEDIA_DIR', PMX_REAL_BASE_DIR . DS . 'media');

/* Ordner mit den Systemdateien fuer die HTML-Ausgabe (view) */
define('PMX_LAYOUT_DIR', PMX_REAL_BASE_DIR . DS . 'layout');
define('PMX_SESSION_DIR', PMX_REAL_BASE_DIR . DS . 'dynadata');


// define("THIS_FILE", basename(__FILE__));

/* min. Voraussetzungen definieren */
// gut: http://www.oxid-esales.com/de/produkte/facts/oxid-eshop-community-edition/systemvoraussetzungen.html
$minvalues = array(/* minimale Versionen und Werte */
    'mysql' => MX_SETUP_MIN_MYSQLVERSION,
    'php' => MX_SETUP_MIN_PHPVERSION,
    'memlimit' => '33554432', // in byte = 32MB
    );

/**
 * TestResult
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2013
 * @version $Id: functions_check.php 171 2016-06-29 11:59:03Z PragmaMx $
 * @access public
 */
class TestResult {
    var $message;
    var $status;
    var $class;

    function __construct($message, $status = STATUS_OK)
    {
        $this->message = $message;
        $this->status = $status;
        // switch ($status) {
        // case STATUS_ERROR:
        // $this->class = 'btn-danger';
        // break;
        // case STATUS_WARNING:
        // $this->class = 'btn-warning';
        // break;
        // case STATUS_OK:
        // default:
        // $this->class = 'btn-success';
        // }
    }
}

/**
 * show_test_results()
 *
 * @param mixed $results
 * @return
 */
function show_test_results($results)
{
    ob_start();

    ?>
    <div class="row">
        <ul>
            <?php foreach($results as $result) {
                echo '<li class="' . $result->status . '"><span>' . $result->status . '</span> - ' . $result->message . '</li>';
            }?>
        </ul>
        <div id="legend">
            <h3><?php echo _LEGEND ?></h3>
            <ul>
                <li class="ok"><?php echo _LEGEND_OK ?></li>
                <li class="warning"><?php echo _LEGEND_WARN ?></li>
                <li class="error"><?php echo _LEGEND_ERR ?></li>
            </ul>
        </div>
    </div>
  <?php
    return ob_get_clean();
}

/**
 * check_db()
 *
 * @param mixed $dbresults
 * @return
 */
function check_db(&$dbresults)
{
    // Funktion wird hier nicht benötigt!!
    return null;

    global $minvalues;

    if (!(DB_HOST && DB_NAME)) {
        return null;
    }

    $dbresults = array();

    if ($connection = @sql_connect(DB_HOST, DB_USER, DB_PASS)) {
        $dbresults[] = new TestResult(_DBTEST_CONNECT, STATUS_OK);

        if (sql_select_db(DB_NAME, $connection)) {
            $dbresults[] = new TestResult(_DBTEST_SELECTED, STATUS_OK);

            $sql_version = sql_get_server_info($connection);

            if (version_compare($sql_version, $minvalues['mysql']) >= 0) {
                $dbresults[] = new TestResult(sprintf(_DBTEST_VERSION, $sql_version), STATUS_OK);
            } else {
                $dbresults[] = new TestResult(sprintf(_DBTEST_VERSIONOLD, $sql_version), STATUS_ERROR);
                return false;
            }
        } else {
            $dbresults[] = new TestResult(sprintf(_DBTEST_SELECTFAIL, sql_error()), STATUS_ERROR);
            return false;
        }
    } else {
        $dbresults[] = new TestResult(sprintf(_DBTEST_CONNECTFAIL, sql_error()), STATUS_ERROR);
        return false;
    }

    return true;
}

/**
 * Validate PHP platform
 *
 * @param array $result
 */
function validate_php(&$results)
{
    global $minvalues;
    if (version_compare(PHP_VERSION, $minvalues['php']) == -1) {
        $results[] = new TestResult(sprintf(_ENVTEST_PHPFAIL, $minvalues['php'], PHP_VERSION), STATUS_ERROR);
        return false;
    } else {
        $results[] = new TestResult(sprintf(_ENVTEST_PHPOK, PHP_VERSION), STATUS_OK);
        return true;
    }
}

/**
 * Validate memory limit
 *
 * @param array $result
 */
function validate_memory_limit(&$results)
{
    global $minvalues;

    $memory_limit = php_config_value_to_bytes(ini_get('memory_limit'));

    $formatted_memory_limit = $memory_limit === -1 ? 'unlimited' : format_file_size($memory_limit);

    if ($memory_limit === -1 || $memory_limit >= $minvalues['memlimit']) {
        $results[] = new TestResult(sprintf(_ENVTEST_MEMOK, $formatted_memory_limit), STATUS_OK);
        return true;
    } else {
        $results[] = new TestResult(sprintf(_ENVTEST_MEMFAIL, format_file_size($minvalues['memlimit']), $formatted_memory_limit), STATUS_ERROR);
        return false;
    }
}

/**
 * Validate PHP extensions
 *
 * @param array $results
 */
function validate_extensions(&$results)
{
    $ok = true;

    $required_extensions = array(/* benötigte */
        /*'pdo',*/
		/* since PHP7 only prmitted MYSQLI */
		/*'mysql',*/
        'pcre',
        'session',
        'mbstring', // => _EXTTEST_MB,
        'gd', // => _EXTTEST_GD,
        'iconv', // => _EXTTEST_ICONV,
        'json',
		
        );

    $recommended_extensions = array(/* empfohlene */
        // 'gd' => _EXTTEST_GD,
        // 'mbstring' => _EXTTEST_MB,
        // 'imap' => _EXTTEST_IMAP,
        'xml' => _EXTTEST_XML,
        'curl' => _EXTTEST_CURL,
        'tidy' => _EXTTEST_TIDY,
        //'pdo' => _EXTTEST_PDO,
        'zip' => _EXTTEST_ZIP,
        );

    foreach($required_extensions as $required_extension) {
        if (extension_loaded($required_extension)) {
            $results[] = new TestResult(sprintf(_EXTTEST_REQFOUND, $required_extension), STATUS_OK);
        } else {
            $results[] = new TestResult(sprintf(_EXTTEST_REQFAIL, $required_extension), STATUS_ERROR);
            $ok = false;
        }
    }

    foreach($recommended_extensions as $recommended_extension => $recommended_extension_desc) {
        if (extension_loaded($recommended_extension)) {
            $results[] = new TestResult(sprintf(_EXTTEST_RECFOUND, $recommended_extension), STATUS_OK);
        } else {
            $results[] = new TestResult(sprintf(_EXTTEST_RECNOTFOUND, $recommended_extension, $recommended_extension_desc), STATUS_WARNING);
        }
    }
    return $ok;
}

/**
 * Validate available PDO drivers
 *
 * @param array $result
 */
function validate_pdo(&$results)
{
    if (!extension_loaded('pdo')) {
        $results[] = new TestResult(sprintf(_EXTTEST_REQFAIL, 'pdo'), STATUS_ERROR);
        return false;
    }

    $usable_drivers = array('mysql');
    $drivers = PDO::getAvailableDrivers();
    $available_drivers = array_intersect($drivers, $usable_drivers);

    if (!$available_drivers) {
        $drivers = implode(',', $usable_drivers);
        $results[] = new TestResult(sprintf(_PDOTEST_FAIL, $drivers), STATUS_ERROR);
        return false;
    }

    foreach ($available_drivers as $driver) {
        $results[] = new TestResult(sprintf(_PDOTEST_OK, $driver), STATUS_OK);
    }
    return true;
}

/**
 * Convert filesize value from php.ini to bytes
 *
 * Convert PHP config value (2M, 8M, 200K...) to bytes. This function was taken from PHP documentation. $val is string
 * value that need to be converted
 *
 * @param string $val
 * @return integer
 */
function php_config_value_to_bytes($val)
{
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch ($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return (integer) $val;
}

/**
 * Format filesize
 *
 * @param string $value
 * @return string
 */
function format_file_size($value)
{
    $data = array('TB' => 1099511627776,
        'GB' => 1073741824,
        'MB' => 1048576,
        'kb' => 1024,
        );

    $value = (integer) $value;
    foreach($data as $unit => $bytes) {
        $in_unit = $value / $bytes;
        if ($in_unit > 0.9) {
            return trim(trim(number_format($in_unit, 2), '0'), '.') . $unit;
        }
    }
    return $value . 'b';
}

/**
 * check_type_support()
 * Check version of GDLib
 *
 * @return
 */
function check_type_support()
{
    // GDlib installiert?
    if (!function_exists('gd_info')) {
        return false;
    }
    $gd_info = gd_info();
    if (!is_array($gd_info) || !isset($gd_info['GD Version'])) {
        return false;
    }
    $gd_info['GD_Version'] = @preg_replace('#[^0-9.]#', '', $gd_info['GD Version']);
    return $gd_info;
}

/**
 * Validate available files
 *
 * @param array $result
 */
function validate_files(&$results)
{
	$err_dir = '';
    $err_file = '';
    $falsechmods = false;
	$driver="";
    
	$err_dir = array();
	$err_file = array();
	admin_chmods::check($err_dir, $err_file);
	$falsechmods = array_merge($err_file, $err_dir);
    
    if (count($falsechmods)>0) {
		foreach ($falsechmods as $driver) {
			$driver=str_replace(PMX_REAL_BASE_DIR,"",$driver);
			$driver=(empty($driver))?"ROOT":$driver;
			$results[] = new TestResult(sprintf(_EXTTEST_FILE_FAIL, $driver), STATUS_ERROR);
		}        
        
        return false;
    }
	
   
    $results[] = new TestResult(_EXTTEST_FILE_OK, STATUS_OK);
    
    return true;	
}

/**
 * admin_chmods
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2011
 * @version $Id: functions_check.php 171 2016-06-29 11:59:03Z PragmaMx $
 * @access public
 */
 
class admin_chmods{
    /**
     * admin_chmods::check()
     * > http://www.pragmamx.org/doku.php?id=handbuch:installation_und_upgrade_des_systems
     *
     * @param mixed $err_dir
     * @param mixed $err_file
     * @return
     */
    public static function check(&$err_dir, &$err_file)
    {
        $dyndirs = array();
        $root = dirname(__FILE__);

        $checkfiles = array(/* zu testende Dateien */
            /*'config.php',*/
            'includes/classes/Textarea/config.inc.php',
            'includes/classes/Captcha/settings.php',
            'includes/prettyPhoto/config.php',
			'modules/Your_Account/config.php',
            'modules/Downloads/d_config.php',
            'modules/Guestbook/include/config.inc.php',
            'modules/My_eGallery/settings.php',
            'modules/UserGuest/settings.php',
            'modules/Web_Links/l_config.php',
            
            );

        foreach ($checkfiles as $modname => $value) {
				$file = (PMX_REAL_BASE_DIR . DS . $value);
				if (!file_exists($file)) {
					$dir = dirname($file);
					if ($root != $dir) {
						$dyndirs[] = $dir;
					}
				} elseif (!self::_isit_writable($file)) {
					$err_file[] = $file;
				}
            
        }

        $dyndirs = array_merge($dyndirs, self::_scandir(PMX_DYNADATA_DIR, GLOB_ONLYDIR | GLOB_NOSORT));
        $dyndirs = array_merge($dyndirs, self::_scandir(PMX_MEDIA_DIR, GLOB_ONLYDIR | GLOB_NOSORT));
        $dyndirs[] = PMX_LAYOUT_DIR . DS . 'style';

        foreach ($dyndirs as $file) {
			$tempfile=$file.'/dummy.txt';
			
			$isit_writable = @file_put_contents($tempfile, "hello") or $err_dir[] = $file;

			@unlink($tempfile);
            
        }
    }

    /**
     * admin_chmods::_scandir()
     *
     * @param mixed $dir
     * @param integer $flags
     * @return
     */
    private static function _scandir($dir, $flags = 0)
    {
        $items = glob($dir . '/*', $flags);

        for ($i = 0; $i < count($items); $i++) {
            if (is_dir($items[$i])) {
                $add = self::_scandir($items[$i] . '/*', $flags);
                if ($add) {
                    $items = array_merge($items, $add);
                }
            }
        }

        return $items;
    }

    /**
     * admin_chmods::_isit_writable()
     *
     * @param mixed $filename
     * @return
     */
    private static function _isit_writable($filename)
    {
        if (!file_exists($filename)) {
            return true;
        }

        $oldmode = false;

        if (!is_writable($filename)) {
            /* aktuellen chmod der Datei zwischenspeichern */
            $oldmode = fileperms($filename);
            /* versuchen beschreibbar zu machen */
            @chmod($filename, PMX_CHMOD_UNLOCK);
            //clearstatcache();
        }

        $result = is_writable($filename);

        if ($oldmode !== false) {
            @chmod($filename, $oldmode);
        }

        return $result;
    }
}
?>