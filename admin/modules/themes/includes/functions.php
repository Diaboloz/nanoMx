<?php
/**
 * This file is part of
 *
 * ......................
 *
 * for pragmamx (www.pragmamx.org)
 *
 * $Revision 1.0 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (Mi, 08. Jul 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * BLANK_functions
 *
 * @package
 * @author ????
 * @copyright Copyright (c) 2014
 * @version $Id: functions.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class themetest_functions {
    private static $config;
    private static $__set = array(); // Konfigurtion

    /**
     * class::__construct()
     *
     * @param mixed $parameter
     */
    function __construct ($parameter = null)
    {
        $this->modulname = $parameter;
        self::$config = load_class("Config", "pmx.themes");
		//$this->config_load();
    }

    /**
     * class::GetDefaultConfig()
     *
     * @return
     */
    static function GetDefaultConfig()
    {
        $temp = array('admintheme' => 'admin-pmx',
            'defaulttheme' => 'default-pmx',
			'mobiletheme'=>'default-pmx',
            'theme' => array(),
            
            );
        return $temp;
    }

    /**
     * class::config_save()
     * Konfiguration auslesen ....
     *
     * @param array $temp
     * @return
     */
    public function config_save ($temp = array())
    {
        self::$config->setSection('pmx.themes', $temp);
        return;
    }

    /**
     * class::config_load()
     *
     * @return
     */
    public function config_load ()
    {
        
        $temp = self::$config->getSection('pmx.themes');
		
        if (count($temp) == 0) { // Array leer
            // defaultwerte eintragen
            $temp = $this->GetDefaultConfig();
            self::$config->setSection('pmx.themes', $temp);
			$temp = self::$config->getSection('pmx.themes');
        }

        return $temp;
    }
    /**
     * class::__get()
     *
     * @param mixed $value_name
     * @return
     */
    public function __get($value_name)
    {
        if (isset(self::$__set[$value_name])) {
            return self::$__set[$value_name];
        }
        return false;
    }

    /**
     * class::__set()
     *
     * @param mixed $name
     * @param mixed $val
     * @return
     */
    public function __set($name, $val)
    {
        self::$__set[$name] = $val ;
    }
	
	public function set_theme($key,$value)
	{
		self::$config->setValue($key,$value,'pmx.themes');
	}
	
	
	public function get_themes ()
	{
		$files = array();
		foreach ((array)glob('themes' . DS . '*' . DS . 'setup.pmx.php', GLOB_NOSORT) as $filename) {
			if ($filename && $info = pathinfo($filename)) {
				$theme_name = basename($info['dirname']);
				global $credits;
				$credits="";
				include_once($filename);
				$thumbnail=file_exists($info['dirname']. DS . 'theme_thumbnail.png');
				$preview=file_exists($info['dirname'] . DS . 'theme_preview.png');
				$adminfunktion=file_exists($info['dirname']. DS . 'admin/admin.php');
				$template_thumbnail = ($thumbnail)?'themes/'. $theme_name . '/theme_thumbnail.png':"";
				$template_preview = ($preview)?'themes/'. $theme_name . '/theme_preview.png':"";
				$admintheme=(substr($theme_name,0,6)=="admin-")?true:false;
				
				$files[$theme_name]['info']=$credits;
				$files[$theme_name]['thumbnail']=$template_thumbnail;
				$files[$theme_name]['preview']=$template_preview;
				$files[$theme_name]['admin']=$adminfunktion;
				$files[$theme_name]['admintheme']=$admintheme;
			}
		}
        if (!isset($files)) {
           // return false;
        }
		
        return $files;		
	}
}

?>