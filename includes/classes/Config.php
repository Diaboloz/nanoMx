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

/**
 * Configuration class
 *
 * Holds global (when accessed through class) pragmaMx configuration and
 * instance configuration when insantiated
 *
 * @package pragmaMx
 * @subpackage Configuration
 */
class pmxConfig {
    /**
     * array for instance config settings
     *
     * @access private
     */
    private static $__config;

    /**
     * string
     *
     * @access private
     */
    private $__section;

    /**
     * die Konfigurationstabelle
     * string
     *
     * @access protected
     */
    protected static $_dbtable;

    /**
     * pmxConfig::__construct()
     *
     * Initialisert die Klasse
     * Der Parameter $section dient zum Ansprechen einer Sektion von
     * zusammengehörigen Konfigurationswerten, z.B. eines Moduls
     * Wird der Parameter nicht angegeben, so wird die 'pmx.main' Haupt-
     * Sektion verwendet, welche die Grundkonfiguration von pragmaMx bereithält.
     *
     * @param string $section Config item's name
     * @return nothing /false
     */
    public function __construct($section = null)
    {
        /* Name der Datenbanktabelle */
        self::$_dbtable = $GLOBALS['prefix'] . '_sys_config';

        switch ($section) {
            /* wenn Sektion nicht angegeben, Haupt-Sektion verwenden */
            case null:
            case 'pmx.main':
                $this->__section == 'pmx.main';
                $this->_init_global();
                break;
            default:
                $this->__section = $this->_sectionname($section);
                $this->getSection($this->__section);
        }
    }

    /**
     * pmxConfig::__destruct()
     * Zur Zeit ohne Funktion...
     */
    public function __destruct()
    {
        // TODO: hier könnte man die Einstellungen gesammelt speichern
    }

    /**
     * pmxConfig::setValue()
     *
     * Speichert einen einzelnen Wert in der Konfiguration.
     * Durch die Angabe von $section, kann auch eine andere Sektion beeinflusst
     * werden, als die, die in der aktuellen Klasseninstanz durch den
     * Konstruktor festgelegt ist.
     *
     * @param string $name , Name des Wertes
     * @param mixed $value , der Wert selbst
     * @param string $section , übergeordnete Sektion
     * @return boolean , bei Erfolg TRUE, im Fehlerfall FALSE
     */
    public function setValue($name, $value, $section)
    {
        $section = $this->_sectionname($section);

        self::$__config[$section][$name] = $value;

        return $this->_set_value_in_database($name, $value, $section);
    }

    /**
     * pmxConfig::__set()
     *
     * Speichert einen einzelnen Wert in der Konfiguration.
     * Überladung der setValue() Funktion.
     * gugge: http://www.php.net/manual/de/language.oop5.overloading.php
     *
     * @param string $name , Name des Wertes
     * @param mixed $value , der Wert selbst
     * @return boolean , bei Erfolg TRUE, im Fehlerfall FALSE
     */
    public function __set($value_name, $value)
    {
        return $this->setValue($value_name, $value, $this->__section);
    }

    /**
     * pmxConfig::getValue()
     *
     * Liest einen einzelnen Wert aus der Konfiguration.
     * Durch die Angabe von $section, kann auch eine andere Sektion ausgelesen
     * werden, als die, die in der aktuellen Klasseninstanz durch den
     * Konstruktor festgelegt ist.
     * Mit $default kann ein optionaler Standardwert angegeben werden, der wenn
     * der angeforderte Wert nicht vorhanden ist, verwendet und auch in der
     * Datenbank gespeichert wird.
     *
     * @param string $name , Name des Wertes
     * @param string $section , übergeordnete Sektion
     * @param mixed $default , optionaler Standardwert
     * @return mixed $value, der ausgelesene Wert oder der Standardwert wenn
     * angegeben, ansonsten false
     */
    public function getValue($name, $section, $default = null)
    {
        $section = $this->_sectionname($section);
        if (isset(self::$__config[$section][$name])) {
            return self::$__config[$section][$name];
        }

        if (!isset(self::$__config[$section])) {
            self::$__config[$section] = self::getSection($section);
        }

        if (isset(self::$__config[$section][$name])) {
            return self::$__config[$section][$name];
        }

        if ($default === null) {
            /* NULL erzeugt in __get() einen trigger_error, damit Variable als fehlende gemeldet wird */
            self::$__config[$section][$name] = null;
            return null;
        } else {
            self::$__config[$section][$name] = $default;
            $this->_set_value_in_database($name, strval($default), $section);
        }

        return self::$__config[$section][$name];
    }

    /**
     * pmxConfig::__get()
     *
     * Liest einen einzelnen Wert aus der Konfiguration.
     * Überladung der getValue() Funktion.
     *
     * gugge: http://www.php.net/manual/de/language.oop5.overloading.php
     *
     * @param string $name , Name des Wertes
     * @return mixed $value, der ausgelesene Wert, oder false, wenn der
     * Wert nicht existiert
     */
    public function __get($value_name)
    {
        $value = $this->getValue($value_name, $this->__section);

        if (is_null($value)) {
            $trace = debug_backtrace();
            // mxDebugFuncVars($trace);
            trigger_error('undefined property \'' . $value_name . '\' in ' . mx_strip_sysdirs($trace[0]['file']) . ' line ' . $trace[0]['line'], E_USER_NOTICE);
            return false;
        }

        return $value;
    }

    /**
     * pmxConfig::getSection()
     *
     * Liest die ganze Sektion von zusammengehörigen Konfigurationswerten aus.
     * Durch die Angabe von $section, kann auch eine andere Sektion ausgelesen
     * werden, als die, die in der aktuellen Klasseninstanz durch den
     * Konstruktor festgelegt ist.
     *
     * @param string $section , übergeordnete Sektion
     * @return array , die Werte der gesamten Sektion
     */
    public function getSection($section)
    {
        $section = $this->_sectionname($section);

        if (!isset(self::$__config[$section])) {
            self::$__config[$section] = $this->_get_section_from_database($section);
        }

        return self::$__config[$section];
    }

    /**
     * pmxConfig::get()
     *
     * Liest alle zusammengehörigen Konfigurationswerte der Sektion aus, die
     * in der aktuellen Klasseninstanz durch den Konstruktor festgelegt ist.
     *
     * @return array , die Werte der gesamten Sektion
     */
    public function get()
    {
        return $this->getSection($this->__section);
    }

    /**
     * pmxConfig::read()
     *
     * alias von get()
     *
     * @deprecated !!
     */
    public function read()
    {
        return $this->get();
    }

    /**
     * pmxConfig::setSection()
     *
     * Speichert die ganze Sektion von zusammengehörigen Konfigurationswerten.
     * Durch die Angabe von $section, kann auch eine andere Sektion geändert
     * werden, als die, die in der aktuellen Klasseninstanz durch den
     * Konstruktor festgelegt ist.
     * Mit dem Parameter $replace = FALSE, werden bereits vorhandene Werte in
     * dieser Sektion nicht überschrieben, sondern nur neue Werte ergänzt.
     *
     * @param string $section , Name der Sektion
     * @param array $settings_array , die neuen Werte der gesamten Sektion
     * @param boolean $replace , ergänzen, oder ersetzen
     * @return boolean , bei Erfolg TRUE, im Fehlerfall FALSE
     */
    public function setSection($section, $settings_array, $replace = true)
    {
        $section = $this->_sectionname($section);
        self::$__config[$section] = (array)$settings_array;

        return $this->_set_section_in_database($section, self::$__config[$section], $replace);
    }

    /**
     * pmxConfig::set()
     *
     * Speichert die neuen Konfigurationswerte in der Sektion ab, die in der
     * aktuellen Klasseninstanz durch den Konstruktor festgelegt ist.
     * Mit dem Parameter $replace = FALSE, werden bereits vorhandene Werte in
     * dieser Sektion nicht überschrieben, sondern nur neue Werte ergänzt.
     *
     * @param array $settings_array , die neuen Werte der gesamten Sektion
     * @param boolean $replace , ergänzen, oder ersetzen
     * @return boolean , bei Erfolg TRUE, im Fehlerfall FALSE
     */
    public function set($settings_array, $replace = true)
    {
        return $this->setSection($this->__section, $settings_array, $replace);
    }

    /**
     * pmxConfig::write()
     *
     * alias von set()
     *
     * @deprecated !!
     */
    public function write($settings_array, $replace = true)
    {
        return $this->set($settings_array, $replace);
    }

    /**
     * pmxConfig::get_defaults()
     *
     * Liest die in der entsprechenden Datei gespeicherten Standardwerte
     * für die aktuelle Section.
     * TODO: das kann/soll erweitert werden auf die Module über Hooks
     *
     * @return array , die Werte der gesamten Sektion
     */
    public function get_defaults()
    {
        $defaults = include(__DIR__ . DS . 'Config' . DS . 'defaults.php');
        if (isset($defaults[$this->__section])) {
            return $defaults[$this->__section];
        }
        return array();
    }

    /**
     * pmxConfig::_get_section_from_database()
     *
     * Interne Funktion zum auslesen einer Sektion aus der Datenbank
     *
     * @param string $section
     * @return
     */
    protected function _get_section_from_database($section)
    {
        $qry = "SELECT `key`, `value`, `serialized`
                 FROM `" . self::$_dbtable . "`
                 WHERE `section`='" . $section . "'";
        $result = sql_system_query($qry);
        $out = array();
        while ($row = sql_fetch_assoc($result)) {
            if ($row['serialized']) {
                $row['value'] = unserialize($row['value']);
            }
            $out[$row['key']] = $row['value'];
        }
        return $out;
    }

    /**
     * pmxConfig::_set_value_in_database()
     *
     * Interne Funktion zum speichern eines einzelnen Wertes einer Sektion
     * in der Datenbank
     *
     * @param string $name
     * @param mixed $value
     * @param string $section
     * @return
     */
    protected function _set_value_in_database($name, $value, $section)
    {
        if (!(is_scalar($value))) {
            $value = serialize($value);
            $isserial = 1;
        } else {
            $isserial = 0;
        }
        $qry = "SELECT `value`, `serialized`
                FROM `" . self::$_dbtable . "`
                WHERE `section`='" . mxAddSlashesForSQL($section) . "'
                  AND `key`='" . mxAddSlashesForSQL($name) . "'";
        $result = sql_system_query($qry);

        $row = sql_fetch_assoc($result);
		
        switch (true) {
            case !isset($row['value']):
                // neu anlegen
                $qry = "INSERT INTO `" . self::$_dbtable . "` (`section`, `key`, `value`, `serialized`)
                        VALUES ('" . mxAddSlashesForSQL($section) . "', '" . mxAddSlashesForSQL($name) . "', '" . mxAddSlashesForSQL($value) . "', '" . $isserial . "')";
                $result = sql_system_query($qry);
		
                break;
            case $row['value'] != $value:
                // aktualisieren
                $qry = "UPDATE `" . self::$_dbtable . "`
                        SET `value`='" . mxAddSlashesForSQL($value) . "', `serialized`='" . $isserial . "'
                        WHERE `section`='" . mxAddSlashesForSQL($section) . "'
                          AND `key`='" . mxAddSlashesForSQL($name) . "'";
		
                $result = sql_system_query($qry);
                break;
            default:
		
                return true;
        }

        if (!$result) {
            // TODO: Fehlerbehandlung
            return false;
        }
    }

    /**
     * pmxConfig::_set_section_in_database()
     *
     * Interne Funktion zum speichern einer Sektion in der Datenbank
     *
     * @param string $section
     * @param array $settings_array
     * @param boolean $replace
     * @return
     */
    protected function _set_section_in_database($section, $settings_array, $replace = true)
    {
        $current = array();
        $sql = array();

        $qry = "SELECT `key`, `value`
                FROM `" . self::$_dbtable . "`
                WHERE `section`='" . $section . "'";
        $result = sql_system_query($qry);
        while (list($key, $value) = sql_fetch_row($result)) {
            if ($replace) {
                $current[$key] = $value;
            } else {
                unset($settings_array[$key]);
            }
        }

        $parts = array();
        foreach ($settings_array as $key => $value) {
            if (!is_scalar($value)) {
                $value = serialize($value);
                $isserial = 1;
            } else {
                $isserial = 0;
            }
            if (isset($current[$key])) {
                if ($current[$key] != $value) {
                    // nur wenn geändert auch aktualisieren
                    $sql[] = "UPDATE `" . self::$_dbtable . "`
                              SET `value`='" . mxAddSlashesForSQL($value) . "', `serialized`='" . $isserial . "'
                              WHERE `section`='" . mxAddSlashesForSQL($section) . "'
                                AND `key`='" . mxAddSlashesForSQL($key) . "'";
                }
            } else {
                // einfuegen
                $parts[] = "('" . mxAddSlashesForSQL($section) . "', '" . mxAddSlashesForSQL($key) . "', '" . mxAddSlashesForSQL($value) . "', '" . $isserial . "')";
            }
        }
        if ($parts) {
            $part = implode(', ', $parts);
            // REPLACE wegen evtl. unterschiedlicher Gross-Kleinschreibung der Schlüssel ;-)
            $sql[] = "REPLACE INTO `" . self::$_dbtable . "` (`section`, `key`, `value`, `serialized`)
                      VALUES " . $part . "";
        }

        foreach ($sql as $qry) {
            sql_system_query($qry);
        }
        return true;
    }

    /**
     * pmxConfig::get_all()
     *
     * Gibt alle Konfigurationswerte aus, die bisher aus der DB ausgelesen
     * wurden.
     * Achtung! Das ist aber nicht alles was in der DB abgespeichert ist!
     *
     * @return array
     */
    public function get_all()
    {
        return self::$__config;
    }

    /**
     * pmxConfig::_sectionname()
     *
     * Interne Funktion zum vereinheitlichen des übergebenen Sektionsnamens
     *
     * @param string $section section item name
     * @return string lowercase
     */
    protected function _sectionname($section)
    {
        if ($section) {
            return strtolower($section);
        } else {
            // kann auch mal leer sein, dann Haupt-Sektion...
            return 'pmx.main';
        }
    }
}

/**
 * Config
 * Alias von pmxConfig
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2011
 * @version $Id: Config.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class Config extends pmxConfig {
    /**
     * Config::__construct()
     */
    public function __construct()
    {
        $args = func_get_args();
        parent::__construct($args);
    }
}

?>
