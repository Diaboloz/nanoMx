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
 * mxTranslate()
 * Wenn der String als Sprachkonstante angegeben ist, diese verwenden
 *
 * @param mixed $string
 * @return {string}
 */
function mxTranslate($string)
{
	if ($string && defined($string) && preg_match('#^_[A-Z][A-Z0-9]#', $string)) {
		return constant($string);
	}
	/* ansonsten bereits gespeichert ? */

	$string = pmxTranslate::get($string); // wenn nicht in Sprachtabelle vorhanden, wird String unverändert zurückgegeben

	
	return $string;
}


/**
 *   mxText
 *
 *  alias für mxTranslate
 *
 *  @param {string} $string - Description
 *  @return
 *
 */
function mxText($string)
{
	return mxTranslate($string);
}

/**
 *   mxTextDefine
 *
 *  @param $key       - Konstante
 *  @param $value     - Wert
 *  @param $overwrite - überschreiben alter werte ?
 *  @return
 *
 */
function mxTextDefine($key,$value,$overwrite=true)
{
	pmxTranslate::add($key,$value,$overwrite);
}

/**
 *  mxOutput
 *  
 *  @param $string zu konvertierender String
 *  @return $string 
 *  
 */
 function mxFilterText($string)
{
	$htmlalloweds="";

	/* alle nicht erlaubten Tags entfernen  */
	$htmlallowed=mxGetAllowedHtml();
	foreach($htmlallowed as $tag){
		$htmlalloweds.="<".$tag.">";
	}
	
	$string=strip_tags($string,$htmlalloweds);
	/* Alle Zensierten begriffe entfernen */
	$string= mxPrepareCensored($string);
	/* nun noch ggf. emailadressen umschreiben */
	$string= mxPrepareToDisplay($string);
	
	return $string;
}

/**
 *   pmxTranslate
 *
 *  @return
 *
 */
class pmxTranslate {
	
	static private $lang=array();

	/**
	 *  __construct
	 *  
	 *  @return 
	 *  
	 */
	private function __construct(){

	}
	/**
	 *  pmxTranslate::init
	 *  
	 *  @return 
	 *  
	 */
	static function init()
	{
		//$temp=self::get_user_constants();
		//self::$lang=array_merge(self::$lang,$temp);

	}
	
	/**
	 *  pmxTranslate::get
	 *  
	 *  @param $key Description
	 *  @return 
	 *  
	 */
	static function get($key)
	{
		if (array_key_exists($key,self::$lang)){
			return self::$lang[$key];
		}
		return "$key";
	}
	
	/**
	 *  pmxTranslate::add
	 *  
	 *  @param $key       Description
	 *  @param $value     Description
	 *  @param $overwrite Description
	 *  @return 
	 *  
	 */
	static public function add($key,$value="",$overwrite=false)
	{
		if (is_array($key)) {
			self::add_user_constants($key);
		}else{
			self::add_user_constant($key,$value,$overwrite);
		}

	}

	/**
	 *  pmxTranslate::add_user_constant
	 *  
	 *  @param $key       Description
	 *  @param $value     Description
	 *  @param $overwrite Description
	 *  @return 
	 *  
	 */
	 private static function add_user_constant($key,$value,$overwrite=false)
	{
		if (array_key_exists($key,self::$lang)) {
			if ($overwrite) self::$lang[$key]=$value;
			return;
		}
		self::$lang[$key]=$value;
		return;
	}
	
	/**
	 *  pmxTranslate::add_user_constants
	 *  
	 *  @return 
	 *  
	 */
	private static function add_user_constants($constants=array())
	{
		self::$lang = array_merge(self::$lang,$constants);
	}

	/**
	 *  pmxTranslate::get_user_constants
	 *  
	 *  @return 
	 *  
	 */
	 static private function get_user_constants()
	{
		//$temp=get_defined_constants(true);
		//return  $temp['user'];
		return array();
	}
	
	/**
	 *  pmxTranslate::view
	 *  
	 *  @return 
	 *  
	 */
	static function view(){
		//asort(self::$lang,SORT_STRING);
		//mxDebugFuncVars(self::$lang);
	}
}

?>
