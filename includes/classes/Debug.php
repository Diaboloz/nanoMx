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

if (!defined('E_DEPRECATED')) {
    define('E_DEPRECATED', 8192);
    define('E_USER_DEPRECATED', 16384);
}

define('VIEW_NOBODY', 0);
define('VIEW_GOD', 1);
define('VIEW_ADMIN', 2);
define('VIEW_USER', 4);
define('VIEW_ANONYM', 8);
define('VIEW_ALL', 16);

/* die alte Variable aus pragmaMx vor 1.12 initialisieren */
/* deprecated, fliegt nach 1.12 endgültig raus !! */
$mxDebugService = false;

/**
 * pmxDebug
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2009
 * @version $Id: Debug.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxDebug {
    /* nur Logfile */
    const level_logfile = 1;
    /* nur Bildschirm */
    const level_screen = 2;
    /* Logfile und Bildschirm */
    const level_screenlog = 3;
    /* alles still */
    const level_silent = 0;

    const E_EXCEPTION = -1;

    /* Fehlermeldungen zur Scriptlaufzeit nicht wiederholen */
    private static $__errors = array();
    private static $__config = array();

    private static $__logfile = '';

    private static $__level = 0;
    private static $__startlevel = false;

    private static $__sqlerror = false;
    private static $__queries = array();
    private static $__ignores = null;
    private static $__reminders = null;

    /**
     * pmxDebug::start()
     *
     * @param integer $level
     * @return
     */
    public static function init()
    {
        /* Level aus config lesen */
        self::_setconfig();

        /* die alten Variablen aus pragmaMx vor 1.12 setzen */
        /* deprecated, fliegt nach 1.12 endgültig raus !! 
        $GLOBALS['mxDebugService'] = self::is_debugmode();
        $GLOBALS['mxSqlErrorDebug'] = $GLOBALS['mxDebugService'];*/

        $level = self::_getlevel();
        self::_set($level);
    }
	
    /**
     * pmxDebug::stop()
     *
     * @return nothing
	 *
	 * schaltet Debug aus, muss zum Neustart mit pmxDebug::init() neu initialisiert werden
     */
	public static function stop()
    {
        /* Level abschalten */
		self::$__config['mxDebug']=array('log'=>0,'screen'=>0,'enhanced'=>0);
        $level = 0;
        self::_set($level);
    }
	
    /**
     * pmxDebug::start()
     *
     * @return nothing
     */
    public static function start()
    {
        $level = self::_getlevel();

        self::$__startlevel = $level;
        self::_set($level);
    }

    /**
     * pmxDebug::pause()
     *
     * @return nothing
     */
    public static function pause()
    {
        if (self::$__level != self::level_silent) {
            self::_set(self::level_silent);
        }
    }

    /**
     * pmxDebug::level()
     *
     * @param integer $level
     * @return nothing
     */
    public static function level($level)
    {
        if (self::$__level != intval($level)) {
            self::_set(intval($level));
        }
    }

    /**
     * pmxDebug::restore()
     *
     * @param boolean $reset
     * @return nothing
     */
    public static function restore($reset = false)
    {
        switch (true) {
            case $reset:
            case self::$__level != self::$__startlevel:
            case ini_get('log_errors') != true:
            case ini_get('error_log') != self::$__logfile:
                self::_set(self::$__startlevel);
        }
    }

    /**
     * pmxDebug::reset()
     *
     * @return nothing
     */
    public static function reset()
    {
        self::restore(true);
    }

    /**
     * pmxDebug::_set()
     * konfiguriert die Fehlerbehandlung
     * info: http://de3.php.net/manual/de/errorfunc.configuration.php
     *
     * @param mixed $level
     * @return nothing
     */
    private static function _set($level)
    {
        self::$__level = $level;
        self::$__logfile = PMX_DYNADATA_DIR . DS . 'logfiles' . DS . 'errors-' . date('y-m-d') . '.log';

        /* abfangbare Fehler Extra behandeln */
        set_error_handler(array(__CLASS__, 'handler'), E_ALL);

        /* nicht gefangene Exceptions Extra behandeln */
        set_exception_handler(array(__CLASS__, 'exc_handler'));

        switch (self::$__level) {
            case self::level_logfile:/* nur Logfile */
                @ error_reporting(E_ALL | E_STRICT);
                @ ini_set('display_errors', false);
                break;

            case self::level_screen:/* nur Bildschirm */
            case self::level_screenlog:/* Logfile und Bildschirm */
                @ error_reporting(E_ALL | E_STRICT);
                @ ini_set('display_errors', true);
                break;

            case self::level_silent:/* alles still */
            default:
                @ error_reporting(E_ERROR | E_PARSE);
                @ ini_set('display_errors', false);
        }

        /* kritische Fehler, die ja nicht vom errorhandler abgefangen werden, immer ins Logfile schreiben */
        @ ini_set('log_errors', true);
        @ ini_set('error_log', self::$__logfile);
        /* weitere Einstellungen zur Fehlerbehandlung */
        @ ini_set('ignore_repeated_errors', true);
        @ ini_set('ignore_repeated_source', false);
        @ ini_set('html_errors', false);
        @ ini_set('track_errors', true);
    }

    /**
     * pmxDebug::error_log()
     * Ersatz für die evtl. abgeschaltete PHP-Funktion error_log()
     *
     * @param mixed $message
     * @param integer $message_type
     * @param string $destination
     * @param string $extra_headers
     * @return boolean successfull
     */
    public static function error_log($message, $message_type = 0, $destination = '', $extra_headers = '')
    {
        $message = trim($message);
        switch (true) {
            case !function_exists('error_log'):
            case !function_exists('ini_set'):
            case !is_callable('error_log'):
            case !is_callable('ini_set'):
                /* wenn error_log() deaktiviert, Fehlermeldung zusammensetzen */
                $message = date('[d.M.Y H:i:s] ') . $message . "\n\n";
                /* ..und Logfile fuellen */
                return file_put_contents(self::$__logfile, $message, FILE_APPEND);
                // TODO: hier abfangen, ob die Datei beschrieben werden konnte try/catch
            default:
                return error_log($message, $message_type, $destination, $extra_headers);
        }
    }

    /**
     * pmxDebug::_build_message_screen()
     *
     * @param string $caption
     * @param string $errstr
     * @param string $errfile
     * @param integer $errline
     * @return string message
     */
    private static function _build_message_screen($caption, $errstr, $errfile, $errline)
    {
        /* absoluten Pfad aus Fehler-Dateiname entfernen */
        $errfile = str_replace(DS, '/', mx_strip_sysdirs($errfile));
        /* zusammensetzen */
        $message = $caption . ":\n  " . strip_tags($errstr) . "\n  in " . $errfile . ' on line ' . $errline;
        return $message;
    }

    /**
     * pmxDebug::_build_message_log()
     *
     * @param mixed $caption
     * @param mixed $errstr
     * @param mixed $errfile
     * @param mixed $errline
     * @return string message
     */
    private static function _build_message_log($caption, $errstr, $errfile, $errline)
    {
        $message = $caption . ":\n  " . trim(strip_tags($errstr)) . "\n  in " . $errfile . ' on line ' . $errline;
        /* Fehlermeldung optimieren */
        $message = str_replace(array('&quot;', "\r\n", "\n\r", "\r", "\n\n"), array('"', "\n", "\n", "\n", "\n"), $message);
        return $message;
    }

    /**
     * pmxDebug::sql_clean_message()
     *
     * @param string $msg
     * @return string message
     */
    public static function sql_clean_message($msg)
    {
        $names = array('dbhost', 'dbname', 'dbuname', 'dbpass', 'prefix', 'user_prefix');
        foreach ($names as $key) {
            $search[] = self::$__config[$key];
            $replace[] = "{{$key}}";
        }
        return str_replace($search, $replace, $msg);
    }

    /**
     * pmxDebug::sql_trigger_error()
     *
     * @param string $dbtype
     * @param string $file
     * @param string $errstr
     * @param integer $errno
     * @param string $query
     * @return nothing
     */
    public static function sql_trigger_error($dbtype, $file, $errstr, $errno, $query = '')
    {
        switch (true) {
            /* Fehler des Clienten sind immer fatal */
            // http://dev.mysql.com/doc/refman/5.1/de/error-handling.html
            case $errno >= 2000:
            /* und wenn es keine sql-query ist, auch ;-)) */
            case !$query:
                $level = E_USER_ERROR;
                break;
            default:
                /* ansonsten, bei normalen Abfragefehlern, nur Warning */
                $level = E_USER_WARNING;
                /* wenn $query, dann die $query an den Fehlertext drankleben */
                $errstr .= "\n  query: " . $query;
        }

        self::$__sqlerror['type'] = $dbtype . ' error (' . intval($errno) . ')';
        self::$__sqlerror['errstr'] = self::sql_clean_message($errstr);
        self::$__sqlerror['errfile'] = $file;
        self::$__sqlerror['errline'] = 0;

        $traces = (array)debug_backtrace();
        foreach ($traces as $trace) {
            /* verursachende Datei ermitteln */
            if ($trace['file'] != __FILE__ && $trace['file'] != $file) {
                self::$__sqlerror['errfile'] = $trace['file'];
                self::$__sqlerror['errline'] = $trace['line'];
                break;
            }
        }

        trigger_error($errstr, $level);
        self::$__sqlerror = null;
    }

    /**
     * pmxDebug::querystack()
     *
     * @param mixed $query
     * @return array , all queries
     */
    public static function querystack($query = null)
    {
        if ($query) {
            self::$__queries[] = self::sql_clean_message($query);
            /* Counter fuer Query-Zaehlung erhoehen */
            $GLOBALS['mxQueryCount'] = count(self::$__queries);
        }
        return self::$__queries;
    }

    /**
     * pmxDebug::querycount()
     *
     * @return integer , count of all queries
     */
    public static function querycount()
    {
        return count((array)self::$__queries);
    }

    /**
     * pmxDebug::format_queries()
     * formatiert die SQL-Anfragen als HTML-String zur Ausgabe
     *
     * @return HTML
     */
    public static function format_queries()
    {
        $out = '';
        $args = self::querystack();
        if ($args) {
            $args = array_map('htmlspecialchars', $args);
            $out = '<ol><li>' . implode('</li><li>', $args) . '</li></ol>';
            $out = preg_replace('#[[:cntrl:]]+\s+#', "\n ", $out);
        }
        return $out;
    }

    /**
     * pmxDebug::format_request()
     * formatiert die Übergabeparameter als HTML-String zur Ausgabe
     *
     * @return HTML
     */
    public static function format_request()
    {
        $out = '';
        $args = array('GET' => $_GET, 'POST' => $_POST, 'COOKIE' => $_COOKIE, 'FILES' => $_FILES);

        foreach ($args as $type => $values) {
            if ($values) {
                $out .= '<h4>' . $type . ":</h4><ul>";
                $values = array_map_recursive('htmlspecialchars', $values);
                ksort($values);
                foreach ($values as $key => $value) {
                    $value = wordwrap2($value, 75, "\n", true);
                    $value = '<span>&nbsp;' . trim(print_r($value, true)) . '</span>';
                    $out .= '<li>' . $key . " =>" . $value . "</li>";
                }
                $out .= '</ul>';
            }
        }
        return $out;
    }

    /**
     * pmxDebug::format_errors()
     * formatiert die Fehlermeldungen als HTML-String zur Ausgabe
     *
     * @return HTML
     */
    public static function format_errors()
    {
        $out = '';
        $args = self::$__errors;

        foreach ($args as $errfile => $errors) {
            /* absoluten Pfad aus Fehler-Dateiname entfernen */
            $errfile = str_replace(DS, '/', mx_strip_sysdirs($errfile));
            if ($errors) {
                $out .= '<dl><dt>' . $errfile . ":</dt>";
                asort($errors);
                foreach ($errors as $errstr => $errline) {
                    $out .= '<dd>&middot; ' . wordwrap(strip_tags($errstr)) . " <i>on line " . $errline . "</i></dd>";
                }
                $out .= "</dl>";
            }
        }
        return $out;
    }

    /**
     * pmxDebug::is_debugmode()
     *
     * @return integer
     */
    public static function is_debugmode()
    {
        $conf = self::$__config['mxDebug'];

        switch (true) {
            case $conf['screen'] === VIEW_ALL:
            case $conf['enhanced'] === VIEW_ALL:
                return VIEW_ALL;

            case $conf['log']:
            case $conf['screen']:
            case $conf['enhanced']:
                return VIEW_ADMIN;

            default:
                return VIEW_NOBODY;
        }
    }

    /**
     * pmxDebug::is_error()
     *
     * @return boolean
     */
    public static function is_error()
    {
        return (boolean)self::$__errors;
    }

    /**
     * pmxDebug::is_mode()
     *
     * @return boolean
     */
    public static function is_mode($mode)
    {
        switch ($mode) {
            case 'log':
                $level = self::_getlevel();
                switch ($level) {
                    case self::level_screenlog:
                    case self::level_logfile:
                        return true;
                    default:
                        return false;
                }

            case 'screen':
                $level = self::_getlevel();
                switch ($level) {
                    case self::level_screenlog:
                    case self::level_screen:
                        return true;
                    default:
                        return false;
                }

            case 'enhanced':
                $conf = self::$__config['mxDebug'];
                $isadmin = defined('MX_IS_ADMIN') && MX_IS_ADMIN;
                switch (true) {
                    case $isadmin && $conf[$mode]:
                    case !$isadmin && $conf[$mode] == VIEW_ALL:
                        return true;
                    default:
                        return false;
                }
        }
        return false;
    }

    /**
     * pmxDebug::screen()
     *
     * @param mixed $message
     * @return string $message
     */
    private static function _screen($message)
    {
        /* absoluten Pfad aus Fehler-Dateiname entfernen */
        $message = mxNL2BR($message);
        /* Ausgabe */ // white-space: pre;
        ?><div class="dbg-error warning"><?php echo $message ?></div><?php
    }

    /**
     * pmxDebug::die()
     *
     * @param string $message
     * @return nothing
     */
    private static function _die($message)
    {
        ob_start();
        self::_screen($message);
        $message = ob_get_clean();
        $isadmin = defined('MX_IS_ADMIN') && MX_IS_ADMIN;

        $before = '';
        if (isset(self::$__config['sitename'])) {
            $before = '<h2>' . self::$__config['sitename'] . '</h2>';
        }
        $before .= '<p>' . (defined('_DEBUG_DIE_1') ? _DEBUG_DIE_1 : 'A error occured while processing this page.') . '</p>';

        if (!$isadmin) {
            $before .= '<p>' . (defined('_DEBUG_DIE_2') ? _DEBUG_DIE_2 : 'Please report the following error to the owner of this website.') . '</p>';
        }
        die($before . $message);
    }

    /**
     * pmxDebug::_setconfig()
     *
     * @return nothing
     */
    private static function _setconfig()
    {
        if (self::$__config === array()) {
            include(PMX_CONFIGFILE);
            self::$__config = $mxConf;
        }
    }

    /**
     * pmxDebug::_getlevel()
     *
     * @return integer
     */
    private static function _getlevel()
    {
        $conf = self::$__config['mxDebug'];

        $isadmin = defined('MX_IS_ADMIN') && MX_IS_ADMIN;

        switch (true) {
            case $isadmin && $conf['log'] && $conf['screen']:
            case !$isadmin && $conf['log'] == VIEW_ALL && $conf['screen'] == VIEW_ALL:
                $level = self::level_screenlog;
                break;

            case $isadmin && $conf['log']:
            case !$isadmin && $conf['log'] == VIEW_ALL:
                $level = self::level_logfile;
                break;

            case $isadmin && $conf['screen']:
            case !$isadmin && $conf['screen'] == VIEW_ALL:
                $level = self::level_screen;
                break;

            default:
                $level = self::level_silent;
        }
        return $level;
    }

    /**
     * pmxDebug::_init_ignores()
     *
     * @return nothing
     */
    private static function _init_ignores()
    {
        if (self::$__ignores === null) {
            $ignores = array('file' => array(), 'text' => array());

            /**
             * bestimmte Fehlermeldungen nicht beachten, dazu einfach einen Teilstring
             * der entsprechenden Fehlermeldung als Array-Wert hier einfuegen.
             * ACHTUNG: die Dinger muessen natuerlich noch gefixt werden !!!
             */
            $ignores['text'][] = 'The /e modifier is deprecated'; // PHP 5.5
            $ignores['text'][] = 'The mysql extension is deprecated'; // PHP 5.5

            if ($file = realpath(dirname(__FILE__) . DS . 'Debug' . DS . 'ignores.php')) {
                include_once($file);
            }
            self::$__ignores = $ignores;
        }
    }

    /**
     * pmxDebug::_init_reminders()
     *
     * @return nothing
     */
    private static function _init_reminders()
    {
        if (self::$__reminders === null) {
            $reminders = array('file' => array(), 'text' => array());
            if ($file = realpath(dirname(__FILE__) . DS . 'Debug' . DS . 'reminders.php')) {
                include_once($file);
            }
            self::$__reminders = $reminders;
        }
    }

    /**
     * pmxDebug::handler()
     *
     * @param integer $errno
     * @param string $errstr
     * @param string $errfile
     * @param integer $errline
     * @param array $errcontext
     * @return nothing
     */
    public static function handler($errno, $errstr, $errfile = '', $errline = 0, $errcontext = array())
    {
        switch (true) {
            /* Dying on these errors only causes MORE problems (blank pages!) */
            case $errfile == 'Unknown':
            /* level zum nichts machen */
            case self::$__level == self::level_silent:
                return true;

            case self::$__sqlerror && is_array(self::$__sqlerror):
                extract(self::$__sqlerror, EXTR_OVERWRITE);
        }

        /* Serverpfad anpassen */
        $tmp_errfile = str_replace(DS, '/', mx_strip_sysdirs($errfile));

        /**
         * bestimmte Fehlermeldungen extra mitteilen, dazu die Kennung der
         * entspr. Fehlermeldung als Array-Wert in der reminders.php einfuegen.
         */
        self::_init_reminders();
        switch (true) {
            case self::$__reminders['file'] && in_array($errfile, self::$__reminders['file']):
            case self::$__reminders['file'] && in_array($tmp_errfile, self::$__reminders['file']):
                self::_remind($errno, $errstr, $errfile, $errline);
                // self::_remind($errno, $errstr, $errfile, $errline, $errcontext);
            case self::$__reminders['text']:
                /* ACHTUNG: regulare Ausdruecke !! */
                $pattern = '#(' . str_replace('#', '\\#', implode('|', self::$__reminders['text'])) . ')#is';
                if (preg_match($pattern, $errstr)) {
                    self::_remind($errno, $errstr, $errfile, $errline);
                    // self::_remind($errno, $errstr, $errfile, $errline, $errcontext);
                }
        }

        /**
         * bestimmte Fehlermeldungen nicht beachten, dazu die Kennung der
         * entspr. Fehlermeldung als Array-Wert in der ignores.php einfuegen.
         */
        // TODO: evtl. noch ne Option, dass man per Einstellung die komplette Backtrace bekommt
        self::_init_ignores();

        switch (true) {
            case isset(self::$__errors[$errfile][$errstr]):
            case self::$__ignores['file'] && in_array($errfile, self::$__ignores['file']):
            case self::$__ignores['file'] && in_array($tmp_errfile, self::$__ignores['file']):
                return true;

            case self::$__ignores['text']:
                /* ACHTUNG: regulare Ausdruecke !! */
                $pattern = '#(' . str_replace('~', '|', preg_quote(implode('~', self::$__ignores['text']), '#')) . ')#is';
                if (preg_match($pattern, $errstr)) {
                    return true;
                }
        }

        /* Fehlermeldungen zur Scriptlaufzeit nicht wiederholen */
        self::$__errors[$errfile][$errstr] = $errline;

        $errortype = array (
            // E_ERROR => 'PHP Fatal run-time error',
            // E_PARSE => 'PHP Compile-time parse error',
            // E_CORE_ERROR => 'PHP Fatal initial startup error',
            // E_CORE_WARNING => 'PHP initial startup Warning',
            // E_COMPILE_ERROR => 'PHP Fatal compile-time error',
            // E_COMPILE_WARNING => 'PHP Compile-time warning',
            E_WARNING => 'PHP Run-time warning',
            E_NOTICE => 'PHP Run-time notice',
            E_USER_ERROR => 'pragmaMx error',
            E_USER_WARNING => 'pragmaMx warning',
            E_USER_NOTICE => 'pragmaMx notice',
            E_STRICT => 'PHP Strict run-time notice',
            E_RECOVERABLE_ERROR => 'PHP Catchable fatal error',
            E_DEPRECATED => 'PHP Deprecated warning',
            E_USER_DEPRECATED => 'pragmaMx deprecated warning',
            // folgendes wird nur vom Exception-Handler übergeben
            self::E_EXCEPTION => 'Fatal error: Uncaught exception',
            );

        switch (true) {
            case isset(self::$__sqlerror['type']):
                $caption = self::$__sqlerror['type'];
                self::$__sqlerror = null;
                break;
            case isset($errortype[$errno]):
                $caption = $errortype[$errno];
                break;
            default:
                $caption = 'other (' . $errno . ')';
        }

        /* ins Logfile */
        switch (self::$__level) {
            case self::level_logfile:
            case self::level_screenlog:
                $msg = self::_build_message_log($caption, $errstr, $errfile, $errline);
                self::error_log($msg, 0);
                break;
        }

        /* auf Bildschirm */
        switch (true) {
            case $errno === E_USER_ERROR:
            case $errno === self::E_EXCEPTION:
                $msg = self::_build_message_screen($caption, $errstr, $errfile, $errline);
                self::_die($msg);
                break;
            case self::$__level == self::level_screen:
            case self::$__level == self::level_screenlog:
                $msg = self::_build_message_screen($caption, $errstr, $errfile, $errline);
                self::_screen($msg);
                break;
        }
    }

    /**
     * pmxDebug::exc_handler()
     *
     * @param mixed $exception
     * @return
     */
    public static function exc_handler($exception)
    {
        return self::handler(self::E_EXCEPTION, $exception->getMessage() . ' (code: ' . $exception->getCode() . ')', $exception->getFile(), $exception->getLine(), array());
    }

    /**
     * pmxDebug::_remind()
     *
     * @param mixed $errno
     * @param mixed $errstr
     * @param mixed $errfile
     * @param mixed $errline
     * @return
     */
    private static function _remind($errno, $errstr, $errfile, $errline)
    {
        $vars = debug_backtrace();
        array_shift($vars);
        // foreach ($vars as $key => $value) {
        // if (isset($vars[$key]['args'][4]['mxConf'])) {
        // unset($vars[$key]['args'][4]['mxConf']);
        // }
        // }
        ob_start();
        print_r($_SERVER['REQUEST_URI']) . "\n";
        print_r($vars);
        $msg = ob_get_clean() . "\n\n";
        self::error_log($msg, 0);
    }
}

?>