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
 * $Revision: 1 $
 * $Author: PragmaMx $
 * $Date: 2016-09-29 15:13:55 +0200 (Do, 29. Sep 2016) $
 */



defined('mxMainFileLoaded') or die('access denied');

class pmxTheme {
    private static $__config = array();
	public static $value = array();
	private static $theme_vars=array();

	
	
     /**
     * pmxTheme::__construct()
     *
     * Initialisert die Klasse
     * 
     *
     * @param string $section Config item's name
     * @return nothing /false
     */
    function __construct()
    {
		
		/* standard-Themanme holen */
		
		/* Konfiguration für Theme holen */
		
		
    }
    
	public function setTheme ($themename)
	{
		self::$value['themename']=$themename;
	}
	
	public function setContent ($content)
	{
		self::$value['content']=$content;
	}
		
	
	
	
	/* ********************************************************************************************************
	*		Helper 
	*
	*/
 
	
    /**
     * pmxTheme::__get()
     *
     * Liest einen einzelnen Wert aus der Konfiguration.
     * Überladung der get() Funktion.
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
     * pmxTheme::__set()
		*
     * speichet einen einzelnen Wert in der Konfiguration.
     * Überladung der set() Funktion.
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
		    return false;		
		}
        return true ;
    }


	/**
	 *  @ __unset
	 *  
	 *  @param [in] $value_name Description
	 *  @return Description
	 *  
	 */
	 public function __unset($value_name)
    {
        unset(self::$value[$value_name]);
    }
	
    /**
     *  @pmxTheme::get()
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

	/**
	 *   : set
	 *  
	 *  @param [in] $value_name Description
	 *  @param [in] $value      Description
	 *  @return 
	 *  
	 */
	static function set($value_name, $value)
    {
		if (!array_key_exists($value_name,self::$__config)){
			self::$value[$value_name]=$value;
		} 
        return false;
    }
	
	
	/**
	 *  @ __callStatic
	 *  
	 *  @param [in] $value_name Description
	 *  @param [in] $arguments  Description
	 *  @return .
	 *  
	 */
	public static function __callStatic($value_name, $arguments=NULL) 
    {
        // Achtung: Der Wert von $name beachtet die Groß-/Kleinschreibung
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
     *  @pmxTheme::....()
     *  
     *  @param [in] $value_name 
     *  @return mixed $value
     *  
     *  @details Details
     *  
     */    
	public function __call($value_name, $arguments=NULL) 
    {
        // Achtung: Der Wert von $name beachtet die Groß-/Kleinschreibung
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
     * pmxTheme::read()
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
     * pmxTheme::write()
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
     * pmxTheme::get_defaults()
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
	
    /**
     * pmxTheme::__destruct()
     * Zur Zeit ohne Funktion...
     */
    public function __destruct()
    {
        // TODO: hier könnte man die Einstellungen gesammelt speichern
    }
}
?>