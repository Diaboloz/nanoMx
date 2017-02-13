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
 * $Date: 2016-09-20 15:29:30 +0200 (Di, 20. Sep 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

include_once("includes/functions.php");

/**
 * themes_admin
 *
 * @package
 * @author Olaf Herfurth
 * $Revision: 216 $
 * @copyright Copyright (c) 2014
 * @version $Id: index.php 216 2016-09-20 13:29:30Z PragmaMx $
 * @access public
 */
class themes_admin {
    private $errors = array();
    private $form = null;
    private $homepage = '';
    private static $__set = array(); // Konfiguration
    static $kf = null;

    private static $_config = null;

    /**
     * class::__construct()
     *
     * @param string $parameter
     */
    function __construct ($parameter = array())
    {
        global $prefix;
		list($this->act,$this->theme,$this->mod)=$parameter;
        mxGetLangfile(__DIR__);
		
        $this->modulname = PMX_MODULE;

        self::$kf = new themetest_functions($this->modulname);
        //$this->op = $parameter;
        $this->config = $this->config_load();
        $this->main($this->act,$this->theme, $this->mod);
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
        self::$__set[$name] = $val;
    }

    /**
     * class::main()
     *
     * @param string $action
     * @return
     */
    function main ($act, $theme,$mod)
    {
		switch ($act) 
		{
			case "setdefault":
				/* template auf default setzen */
				
				$this->set_theme("defaulttheme",$theme);
			break;
			case "setmobile":
				/* template auf default setzen */
				
				$this->set_theme("mobiletheme",$theme);
			break;			
			case "admindefault":
				/* admintemplate festlegen */
				
				$this->set_theme("admintheme",$theme);
				break;
			
			default:
				if ($theme!="") $this->themes_admin ($theme, $act,$mod);
				
		}
		
        $info2 = _TTAVAILTEMPLATES;
        $tb = load_class('AdminForm', "adminFormMain");

        switch (pmxAdminForm::CheckButton()) {
            case "save":
                $temp = $_POST['config'];
                $this->config_save ($temp) ;
                mxRedirect("admin.php?op=$info2", _CHANGESAREOK, 1);
                break;
        }

        $this->config = $this->config_load();

        $tb = load_class('AdminForm', "adminFormThemes");
        $tb->tb_text = $info2;
        $tb->tb_direction = 'right';
        $tb->infobutton = false;
        $tb->tb_pic_heigth = 22;
        $tb->cssclass = "toolbar1";
        $tb->homelink = true;

        /* Form elements */
        $tb->addFieldSet("head", _TTAVAILTEMPLATES, _TT_INFOTEXT, false);

		$themes = self::$kf->get_themes();
		
		
		foreach ($themes as $theme => $themeinfo) {
			$tb->add("head", "output", $theme);
		}
		$formOpen=$tb->FormOpen();
		$toolbar = $tb->getToolbar();
		$formClose=$tb->FormClose();
        //$form = $tb->Show();
		
		$xconfig=$this->config;
        /*
         * Template
         */
        /* Template initialisieren */
        $template = load_class('Template');
        $template->init_path(__FILE__);

        /* hier die Ausgabefelder angeben */
        $template->assign(compact('credits', 'toolbar', 'info2', 'formOpen', 'formClose', 'themes','xconfig'));

        include('header.php');
        /* Template ausgeben (echo) */
        $template->display('admin.html');
        include('footer.php');
    }

	function themes_admin ($theme, $act,$mod) {
	
		if (!file_exists('themes/'.$theme.'/admin/admin.php')) return;
		
		include('header.php');
		//echo "<div class='block' style='margin-bottom:40px;'></div>";
		include('themes/'.$theme.'/admin/admin.php');
		include('footer.php');
		exit;
	}
	
    /**
     * class::config_save()
     * Konfiguration auslesen ....
     *
     * @param array $temp
     * @return
     */
    function config_save ($temp = array())
    {
        self::$kf->config_save($temp);
    }

    /**
     * class::config_load()
     *
     * @return
     */
    function config_load ()
    {
        $temp = self::$kf->config_load();

        return $temp;
    }
	
	function set_theme($key,$value)
	{
		self::$kf->set_theme($key,$value);
		$this->config = $this->config_load();
	}
}
if (!isset($mod)) $mod = "";
if (!isset($act)) $act = "";
if (!isset($theme)) $theme = "";
$tmp = new themes_admin(array($act,$theme, $mod));
$tmp = null;

?>