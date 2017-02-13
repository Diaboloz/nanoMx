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
 * $Revision: 165 $
 * $Author: PragmaMx $
 * $Date: 2016-06-09 10:27:55 +0200 (Do, 09. Jun 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * delfiles
 * Hilfsfunktionen um unnötige Dateien im System aufzuspüren
 * und zu entfernen
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2014
 * @version $Id: functions.php 165 2016-06-09 08:27:55Z PragmaMx $
 * @access public
 */
class delfiles {
    const CHMODLOCK = "0444";
    const CHMODNORMAL = "0644";
    const CHMODUNLOCK = "0666";
    const CHMODFULLOCK = "0400";
    const CHMODFULLUNOCK = "0777";

    static $err = array('files' => array(), 'dirs' => array());

	private function __construct()
	{
		
	}
    /**
     * delfiles::get()
     *
     * @return
     */
    public static function get()
    {
        switch (true) {
            case $def_file = realpath(__DIR__ . '/delfiles.php'):
            case $def_file = realpath(PMX_REAL_BASE_DIR . '/setup/includes/delfiles.php'):
                break;
            default:
                /* delfiles.php nicht vorhanden */
                return false;
        }

        $deletes['files'] = array(); // files zuerst im Array!!
        $deletes['dirs'] = array();

        include($def_file);
        $iswin = !strncasecmp(PHP_OS, 'WIN', 3);

        /* delFile Dateien aus den Modulen auslesen */
        $hookfiles = self::get_hook('install.delfiles');

        /* TODO: evtl. hier noch deen Modulnamen auslesen und als
           Zwischen-Überschrift in der Tabelle verwenden */

        foreach ($hookfiles as $filename) {
            self::get_modules($filename, $deldirs, $delfiles);
        }
        foreach($deldirs as $delfile) {
            $delfile = trim($delfile);
            if (!$delfile) {
                continue;
            }
            $delfile = PMX_REAL_BASE_DIR . DS . $delfile;

            if ($real = realpath($delfile)) {
                if (!is_file($real)) {
                    if ($iswin) {
                        /* Gross-Kleinschreibung bei Windows beachten */
                        $needle = ltrim(str_replace('/', DS, $delfile), '.');
                        if (strrpos($real, $needle) === false) {
                            continue;
                        }
                    }
                    $deletes['dirs'][] = $real;
                    // Dateien aus den Unterordnern von deldirs
                    // an das Dateien-Array anfügen !
                    self::get_subdirs($real, $deletes);
                }
            }
        }

        foreach($delfiles as $delfile) {
            $delfile = trim($delfile);
            if (!$delfile) {
                continue;
            }
            $delfile = PMX_REAL_BASE_DIR . DS . $delfile;
            if ($real = realpath($delfile)) {
                if ($iswin) {
                    /* Gross-Kleinschreibung bei Windows beachten */
                    $needle = ltrim(str_replace('/', DS, $delfile), '.');
                    if (strrpos($real, $needle) === false) {
                        continue;
                    }
                }
                $deletes['files'][] = $real;
            }
        }

        $deletes['dirs'] = array_unique($deletes['dirs']);
        $deletes['files'] = array_unique($deletes['files']);

        natcasesort($deletes['dirs']);
        natcasesort($deletes['files']);

        return $deletes;
    }

    /**
     * delfiles::get_modules()
     *
     * @param mixed $filename
     * @param mixed $dirs
     * @param mixed $files
     * @return
     */
    protected static function get_modules($filename, &$dirs, &$files)
    {
        $deldirs = array();
        $delfiles = array();

        if (include($filename)) {
            if ($deldirs) {
                $dirs = array_merge($dirs, $deldirs);
            }
            if ($delfiles) {
                $files = array_merge($files, $delfiles);
            }
        }
    }

    /**
     * delfiles::get_subdirs()
     *
     * @param mixed $dir
     * @param mixed $deletes
     * @return
     */
    protected static function get_subdirs($dir, &$deletes)
    {
        $items = glob($dir . '/*');
        if (!$items || !is_array($items)) {
            return;
        }

        for ($i = 0; $i < count($items); $i++) {
            if (is_dir($items[$i])) {
                $add = glob($items[$i] . '/*');
                if ($add && is_array($add)) {
                    $items = array_merge($items, $add);
                }
            }
        }

        foreach ($items as $file) {
            if (is_dir($file)) {
                $deletes['dirs'][] = realpath($file);
            } else {
                $deletes['files'][] = realpath($file);
            }
        }
    }

    /**
     * delfiles::delete_all()
     *
     * @return
     */
    public static function delete_all()
    {
        $deletes = self::get();

        foreach($deletes as $type => $files) {
            foreach($files as $del) {
                if (is_dir($del)) {
                    self::erase_folder($del);
                } else {
                    self::erase_file($del);
                }
            }
        }

        /* alle Dateien und Ordner gelöscht = true */
        return self::get_notdeleted('count') === 0;
    }

    /**
     * delfiles::erase_folder()
     *
     * @param mixed $dir = the target directory
     * @return
     */
    public static function erase_folder($dir)
    {
        $dir = realpath(trim($dir, '\\ /'));

        if ($dir && !is_writable($dir)) {
            chmod($dir, octdec(self::CHMODUNLOCK));
            clearstatcache(true, $dir);
        }

        if (!$dir || !$handle = opendir($dir)) {
            // gibts garnet
            return true;
        }

        /* Unterordner lesen */
        while (false !== ($file = readdir($handle))) {
            switch (true) {
                case $file == '.':
                case $file == '..':
                    break;
                case is_dir($file):
                    self::erase_folder($dir . '/' . $file);
                    break;
                default:
                    self::erase_file($dir . '/' . $file);
            }
        }
        closedir($handle);

        $ok = is_writable($dir) && @rmdir($dir);
        if (!$ok) {
            self::$err['dirs'][] = $dir;
        }
        return $ok;
    }

    /**
     * delfiles::erase_file()
     * versucht eine Datei zu löschen.
     * wenn nicht möglich, wird zumindest versucht den Inhalt der Datei zu entfernen
     *
     * @param mixed $delfile
     * @return
     */
    public static function erase_file($delfile)
    {
        // ###############################
        // zum Testen !!!
        // if (basename($delfile) == 'lang-german.php') {
        // self::$err['files'][] = $delfile;
        // return false;
        // }
        // ###############################
        switch (true) {
            case !$delfile = realpath($delfile):
                // prima, die Datei gibts garnicht
                return true;
            case is_file($delfile):
                if (!is_writable($delfile)) {
                    chmod($delfile, octdec(self::CHMODUNLOCK));
                    clearstatcache(true, $delfile);
                }
                if (is_writable($delfile) && @unlink($delfile)) {
                    return true;
                }
                break;
            case is_dir($delfile):
                return self::erase_folder($delfile);
            default:
                // keine Datei?
                self::$err['files'][] = $delfile;
                return false;
        }
        // zumindest versuchen zu leeren
        $ok = is_writable($delfile) && file_put_contents($delfile, '');
        if (!$ok) {
            self::$err['files'][] = $delfile;
        }
        return $ok;
    }

    /**
     * delfiles::get_hook()
     *
     * @param mixed $hook_name
     * @return
     */
    protected static function get_hook($hook_name)
    {
        if (function_exists('load_class')) {
            $hook = load_class('Hook', $hook_name);
            return $hook->get_files();
        }

        $files = array();
        foreach ((array)glob(PMX_MODULES_DIR . '/*/core/' . $hook_name . '.php', GLOB_NOSORT) as $filename) {
            if ($filename) {
                $files[] = $filename;
            }
        }
        return $files;
    }

    /**
     * delfiles::get_notdeleted()
     *
     * @param mixed $type
     * @return
     */
    public static function get_notdeleted($type = false)
    {
        switch ($type) {
            case 'dirs':
            case 'files':
                return self::$err[$type];
            case 'merge':
                return array_merge(self::$err['dirs'], self::$err['files']);
            case 'count':
                return count(self::$err['dirs']) + count(self::$err['files']);
            default:
                return self::$err;
        }
    }
}

?>