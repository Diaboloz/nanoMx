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
 * $Revision: 287 $
 * $Author: SvenLang $
 * $Date: 2016-12-06 17:09:11 +0100 (Di, 06. Dez 2016) $
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
class pmxBase {
	/**
	 * array for instance config settings
	 *
	 * @access private
	 */
	private static $__config = array();
	public static $value = array();


	/**
	 * pmxBase::__construct()
	 *
	 * Initialisert die Klasse
	 * Der Parameter $section dient zum Ansprechen einer Sektion von
	 * zusammengeh�rigen Konfigurationswerten, z.B. eines Moduls
	 * Wird der Parameter nicht angegeben, so wird die 'pmx.main' Haupt-
	 * Sektion verwendet, welche die Grundkonfiguration von pragmaMx bereith�lt.
	 *
	 * @param string $section Config item's name
	 * @return nothing /false
	 */
	public function __construct($mxConf )
	{
		foreach ($mxConf as $key => $value) {
			self::$__config[$key] = (!is_array($value)) ? stripslashes($value) : $value;
			self::$value[$key] = (!is_array($value)) ? stripslashes($value) : $value;
		}
	}

	/**
	 * pmxBase::__destruct()
	 * Zur Zeit ohne Funktion...
	 */
	public function __destruct()
	{
		// TODO: hier k�nnte man die Einstellungen gesammelt speichern
	}

	/**
	 * pmxBase::__get()
	 *
	 * Liest einen einzelnen Wert aus der Konfiguration.
	 * �berladung der get() Funktion.
	 *
	 * gugge: http://www.php.net/manual/de/language.oop5.overloading.php
	 *
	 * @param string $name , Name des Wertes
	 * @return mixed $value, der ausgelesene Wert, oder false, wenn der
	 * Wert nicht existiert
	 */
	public function __get($value_name)
	{
		if (!array_key_exists($value_name,self::$value)) return false;

		return self::$value[$value_name];
	}

	/**
	 * pmxBase::__set()
	 *
	 * speichet einen einzelnen Wert in der Konfiguration.
	 * �berladung der set() Funktion.
	 *
	 * gugge: http://www.php.net/manual/de/language.oop5.overloading.php
	 *
	 * @param string $name , Name des Wertes
	 *
	 */
	public function __set($value_name, $value)
	{
		if (!array_key_exists($value_name,self::$__config)){
			self::$value[$value_name]=$value;
		} else {
			trigger_error("Variable name reserved: ". htmlentities($value_name),E_USER_WARNING  );
		}
		return ;
	}


	public function __unset($value_name)
	{
		unset(self::$value[$value_name]);
	}

	/**
	 *  @pmxBase::get()
	 *
	 *  @param [in] $value_name
	 *  @return mixed $value
	 *
	 *  @details Details
	 *
	 */
	static function get($value_name)
	{
		if (!array_key_exists($value_name,self::$value)) return false;

		return self::$value[$value_name];
	}

	static function set($value_name, $value)
	{
		if (!array_key_exists($value_name,self::$__config)){
			self::$value[$value_name]=$value;
		} else {
			trigger_error("Variable name reserved: ". htmlentities($value_name),E_USER_WARNING );
		}
		return ;
	}

	static function set_system($value_name, $value)
	{
		/* hiermit k�nnen SystemGlobals dennoch �berschrieben werden */
		if (array_key_exists($value_name,self::$__config)){
			self::$value[$value_name]=$value;
		} else {
			trigger_error("Variable name not found: ". htmlentities($value_name),E_USER_WARNING );
		}
		return ;
	}

	/* function __invoke($value_name)
    {
        if (!array_key_exists($value_name,self::$value)) return false;
        
        return self::$value[$value_name];
    }    */

	/**
	 *  @pmxBase::....()
	 *
	 *  @param [in] $value_name
	 *  @return mixed $value
	 *
	 *  @details Details
	 *
	 */
	public static function __callStatic($value_name, $arguments=NULL)
	{
		// Achtung: Der Wert von $name beachtet die Gro�-/Kleinschreibung
		//echo "Rufe die statische Methode '$name' "
		//. implode(', ', $arguments). "\n";
		if ($arguments==NULL) {
			if (!array_key_exists($value_name,self::$value)) return false;

			return self::$value[$value_name];
		}
		if (count($arguments)>1) {
			self::set($value_name,$arguments);
			return;
		}
		self::set($value_name,$arguments[0]);
		return;

	}

	/**
	 *  @pmxBase::....()
	 *
	 *  @param [in] $value_name
	 *  @return mixed $value
	 *
	 *  @details Details
	 *
	 */
	public function __call($value_name, $arguments=NULL)
	{
		// Achtung: Der Wert von $name beachtet die Gro�-/Kleinschreibung
		//echo "Rufe die statische Methode '$name' "
		//. implode(', ', $arguments). "\n";
		if ($arguments==NULL) {
			if (!array_key_exists($value_name,self::$value)) return false;

			return self::$value[$value_name];
		}
		if (count($arguments)>1) {
			self::set($value_name,$arguments);
			return;
		}
		self::set($value_name,$arguments[0]);
		return;

	}
	/**
	 * pmxBase::read()
	 *
	 *
	 * TODO : Lesen der Configuration
	 *
	 */
	private function read()
	{
		return ;
	}

	/**
	 * pmxBase::write()
	 *
	 *
	 *  TODO: Schreiben der Configuration
	 *
	 */
	private function write($replace = true)
	{
		return ;
	}

	/**
	 * pmxBase::get_defaults()
	 *
	 * TODO: Einstellen der Standardwerte
	 *
	 *
	 * @return array , die Werte der gesamten Sektion
	 */
	private function get_defaults()
	{

		return array();
	}

}

/**
 * Base
 * Alias von pmxBase
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2011
 * @version $Id: Base.php 287 2016-12-06 16:09:11Z SvenLang $
 * @access public
 */
class Base extends pmxBase {
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
