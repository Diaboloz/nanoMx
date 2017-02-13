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
 * pmxExif
 *
 * @package pragmaMx
 * @author terraproject
 * @copyright Copyright (c) 2011
 * @access public
 */


/*
*/
if (!defined("mxMainFileLoaded")) die ("You can't access this file directly...");

/**
 * EXIF-Reader

*/

//require_once("Exif/exifWriter.php");
 

class pmxExif  {
	
	private $checkarray=array('IMAGETYPE_TIF_II'=>0,
	                          'IMAGETYPE_JPEG'=>0,
	                          'IMAGETYPE_JPG'=>0,
							  'IMAGETYPE_TIF_MM'=>0,
							  );	
	private $exifarray=array();
	private $exifkeys=array();
	private $config=array();
	private $checkexif=false;
	

	

    public function __construct($parameter=NULL)
    {
		/* test ob EXIF-Komponente geladen ist */
		if (function_exists("exif_read_data")== false) return false;
		
		if ( $parameter==NULL or (!file_exists($parameter))) return false;
		
		$fileinfo= getimagesize($parameter);
	    if (!in_array( strtolower($fileinfo['mime']),array("image/jpg","image/jpeg"))) return false;	
			
		if (exif_read_data($parameter,0,true)==false) return false;		/* wenn nicht, dann raus hier */
			$this->checkexif=true;
			$this->config['linkimage']="images/view.gif";
			$this->config['image']=$parameter;
		
			//$this->_exifReader($this->config['image']);
    }
	
    public function __set($key, $value)
    {
        if (!empty($value)) {
            $this->config[$key] = $value;
        } else {
            unset($this->config[$key]);
        }
        return true;
    }

    public static function set($key, $value)
    {
        if (!empty($value)) {
            $this->config[$key] = $value;
        } else {
            unset($this->config[$key]);
        }
        return true;
    }


    public function __get($key)
    {
        return isset($this->config[$key]) ? $this->config[$key] : false;
    }
	
    public static function get($key)
    {
        return isset($this->config[$key]) ? $this->config[$key] : false;
    }

	
	public function getKey($keyname)
	{
	    if (array_key_exists($keyname,$this->exifkeys)) {
			return $this->exifkeys[$keyname];
		}
		return false;
	}
	
	public function _exifReader($parameter=NULL)
	{
		if (!$this->checkexif) return false;
		if ($parameter==NULL) $parameter=$this->config['image'];
		if (!file_exists($parameter)) return;
		$this->exifarray=exif_read_data($parameter,0,true);
		  foreach ($this->exifarray as $key => $section) {
			foreach ($section as $name => $value2) {
			
				 if (!is_array($value2) and strpos($value2,"/")>0) {
					  list($numerator, $denumerator) = explode("/", $value2);
					  if ($denumerator>0)  $value2 = round(($numerator/$denumerator),2);
				 }
			
			  
			  switch($name) {
			  	case "ShutterSpeedValue":
				    $this->exifkeys[$name] = "1/".$value2*100;
					break;
				case "WhiteBalance":
					switch($value2) {
					   case "1":
						$this->exifkeys['ExposureMode'] = "Manual";
						break;
					   default:
						$this->exifkeys['ExposureMode'] = "auto";
						break;
					}
				//case "DateTimeOriginal":
				case "DateTime":
					$datum = $value2;
					   // Datum zu Unix-Timestamp
					  if ($datum != '' && $datum != "0000:00:00 00:00:00") {
						$datum = preg_replace("/(\d{4}):(\d{2}):(\d{2}) (\d{2}):(\d{2}):(\d{2})/",'\1-\2-\3 \4:\5:\6', $datum);
						$this->exifkeys['DateTime'] = $datum ;//mx_strftime(_XDATESTRING,strtotime($datum));
					  } /*else {
						$this->exifkeys['DateTime'] = mx_strftime('%x', 0);
					  }*/
					 break;
				case "ExposureMode":
					switch($value2) {
					   case "0":
						$this->exifkeys['ExposureMode'] = "auto";
						break;
					   case "1":
						$this->exifkeys['ExposureMode'] = "Manual";
						break;
					   case "2":
						$this->exifkeys['ExposureMode'] = "auto bracket";
						break;
					   
					   default:
						$this->exifkeys['ExposureMode'] = "auto";
						break;
					}
				default:
				 $this->exifkeys[$name]=$value2;	
				 break;
			  }
			  
			}
		  }  
		/* ab hier keine standardisierten EXIF-Daten mehr */
		if (array_key_exists('GPSLatitudeRef',$this->exifkeys))	{
		   foreach($this->exifkeys["GPSLatitude"] as $key=>$value3) {
			  list($numerator, $denumerator) = explode("/", $value3);
			  if ($denumerator>0)  $this->exifkeys["GPSLatitude"][$key] = round(($numerator/$denumerator),3);		   
		   }
		   foreach($this->exifkeys["GPSLongitude"] as $key=>$value3) {
			  list($numerator, $denumerator) = explode("/", $value3);
			  if ($denumerator>0) $this->exifkeys["GPSLongitude"][$key] = round(($numerator/$denumerator),3);		   
		   }
		
			$deg=$this->exifkeys["GPSLatitude"][0];
			$min=$this->exifkeys["GPSLatitude"][1];
			$sec=$this->exifkeys["GPSLatitude"][2];
			//Hemisphere (N, S, W ou E):
			$hem=$this->exifkeys["GPSLatitudeRef"];
			if ($hem!="N" or $hem!="S")$hem="N";
			$latitude=$hem." ".$deg."°".$min.".".$sec;
			$latDec=($hem=="N")?$deg."+".$min:"-".$deg."+".$min;
			
			$deg=$this->exifkeys["GPSLongitude"][0];
			$min=$this->exifkeys["GPSLongitude"][1];
			$sec=$this->exifkeys["GPSLongitude"][2];
			//Hemisphere (N, S, W ou E):
			$hem=$this->exifkeys["GPSLongitudeRef"];
			if ($hem!="W" or $hem!="E")$hem="E";
			$longitude=$hem." ".$deg."°".$min.".".$sec;
			$lonDec=($hem!="W")?$deg."+".$min:"-".$deg."+".$min;
			
			
			//Altitude:
			$alt=$this->exifkeys["GPSAltitude"];
				$this->exifkeys["Position"]=$latitude." / ".$longitude. " - ".$alt." [m]";
		}
		
		
		if (array_key_exists('Position',$this->exifkeys))	{
		    
			$this->exifkeys["MapLink"]="<a href=\"http://maps.google.de/?q=$latDec,$lonDec\" target=\"_blank\" ><img src=\"".$this->config['linkimage']."\" alt=\"\" /></a>";
			
		}
	   return;
	}
}	
?>