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
 * $Revision: 205 $
 * $Author: PragmaMx $
 * $Date: 2016-08-19 10:09:41 +0200 (Fr, 19. Aug 2016) $
 *
 * based on prettyPhoto-plugin by Olaf Herfurth/TerraProject (Germany)
 * - http://www.terraproject.de
 * enhanced by BdMdesigN
 * - http://www.osc4pragmamx.org/
 *
 * Doku:  http://www.no-margin-for-errors.com/projects/prettyPhoto-jquery-lightbox-clone/
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * lightbox_admin
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2010
 * @version $Id: index.php 205 2016-08-19 08:09:41Z PragmaMx $
 * @access public
 */
class lightbox_admin {
    protected $_config = array();
    protected $_source_path = '';
    protected $_configfile = '';

    protected $_defaults = array(/* Standardwerte */
        /* fast/slow/normal */
        'animation_speed' => 'normal',
        /* false OR interval time in ms */
        'slideshow' => '5000',
        /* true/false */
        'autoplay_slideshow' => false,
        /* Value between 0 and 1 */
        'opacity' => '0.5',
        /* true/false */
        'show_title' => true,
        /* Resize the photos bigger than viewport. true/false */
        'allow_resize' => true,
        /* Allow the user to expand a resized image. true/false */
        'allow_expand' => true,
        /* light_rounded / dark_rounded / light_square / dark_square */
        'theme' => 'light_rounded',
        /* The separator for the gallery counter 1 'of' 2 */
        'counter_separator_label' => '/',
        /* Hides all the flash object on a page, set to TRUE if flash appears over prettyPhoto */
        'hideflash' => true,
        /* Set the flash wmode attribute */
        'wmode' => 'opaque',
        /* Automatically start videos: True/False */
        'autoplay' => true,
        /* If set to true, only the close button will close the window */
        'modal' => false,
        /* Allow prettyPhoto to update the url to enable deeplinking. */
        'deeplinking' => true,
        /* If set to true, a gallery will overlay the fullscreen image on mouse over */
        'overlay_gallery' => true,
        /* Maximum number of pictures in the overlay gallery */
        'overlay_gallery_max' => 30,
        /* Set to false if you open forms inside prettyPhoto */
        'keyboard_shortcuts' => true,
        );

    /**
     * lightbox_admin::__construct()
     *
     * @param mixed $op
     */
    public function __construct($op)
    {
        if (!mxGetAdminPref('radminsuper')) {
            return mxRedirect(adminUrl(), 'Access Denied');
        }

        /* Pfad zu den Dateien */
        $this->_source_path = PMX_SYSTEM_PATH . 'prettyPhoto';
        $this->_configfile = PMX_SYSTEM_DIR . DS . 'prettyPhoto' . DS . 'config.php';
		
        $this->_init_config();

        /* Sprachdatei auswählen */
        mxGetLangfile(__DIR__);

        /* Was ist zu tun... */
        switch ($op) {
            case PMX_MODULE . '/save':
                $this->_save();
                break;
            default:
                $this->_settings1();
                break;
        }
    }

    /**
     * lightbox_admin::_settings()
     *
     * @param integer $ok
     * @return
     */
    function _settings($ok = 0)
    {
        ob_start();

        ?>
<style type="text/css">

.cinput div {
   margin-bottom: 1em;
}

.cinput span {
  height: 2em;
  width: 30px;
  overflow: hidden;
  background: transparent url(images/info.gif) no-repeat 10px 50%;
  cursor: help;
}

.cinput label {
   width: 23em;
}

.cinput label,
.cinput span,
.cinput select,
.cinput input {
   float: left;
}

</style>
           <?php
        pmxHeader::add_style_code(ob_get_clean());
        pmxHeader::add_jquery('ui/jquery.ui.tooltip.js');
        pmxHeader::add_lightbox(true) ;
        pmxHeader::add_script_code('$(document).ready(function() {$(".cinput span[title]").tooltip()});') ;

        include('header.php');
        title(_LIGHTBOX);

        echo '<form action="' . adminUrl(PMX_MODULE, 'save') . '" method="post">';
        echo '<input type="hidden" name="ok" value="1" size="0" />';
        echo '<fieldset class="cinput">';
        echo '<legend>' . _LIGHTBOXSETTINGS . '</legend>';

        $path = str_replace('/', DS, PMX_LAYOUT_DIR . '/jquery/images/prettyPhoto/*/sprite.png');
        $options = array();
        foreach ((array)glob($path) as $theme) {
            if ($theme) {
                $theme = basename(dirname($theme));
                $themex = ($theme == 'default') ? 'pp_default' : $theme;
                $options[] = '<option value="' . $themex . '"' . (($this->_config['theme'] == $themex) ? ' selected="selected" class="current"' : '') . '>' . $theme . '</option>';
            }
        }

        echo '<div class="clearfix"><label>' . _DESIGN . '</label>
             <select name="theme" size="1">' . implode("\n", $options) . '</select></div> ';

        if ($this->_config['overlay_gallery'] == 0) {
            $ss31 = ' selected="selected" class="current"';
            $ss32 = "";
        } else {
            $ss31 = "";
            $ss32 = ' selected="selected" class="current"';
        }

        if ($this->_config['show_title'] == 0) {
            $ss01 = ' selected="selected" class="current"';
            $ss02 = "";
        } else {
            $ss01 = "";
            $ss02 = ' selected="selected" class="current"';
        }

        echo '<div class="clearfix"><label>' . _SHOWTITLE . '</label>
         <select name="show_title" size="1">
           <option value="0" ' . $ss01 . '>' . _NO . '</option>
           <option value="1" ' . $ss02 . '>' . _YES . '</option>
         </select>
        </div>  ';
        echo '<div class="clearfix"><label>' . _OPACITY . '</label><input type="text" name="opacity" maxlength="3" value="' . $this->_config['opacity'] . '" size="5" /> <span title=" 0 - 1 "></span></div>';

        if ($this->_config['allow_resize'] == 0) {
            $ss11 = ' selected="selected" class="current"';
            $ss12 = "";
        } else {
            $ss11 = "";
            $ss12 = ' selected="selected" class="current"';
        }
        echo '<div class="clearfix"><label>' . _ALLOWRESIZE . '</label>
         <select name="allow_resize" size="1">
           <option value="0" ' . $ss11 . '>' . _NO . '</option>
           <option value="1" ' . $ss12 . '>' . _YES . '</option>
         </select>
         </div> ';

        if ($this->_config['allow_expand'] == 0) {
            $ss15 = ' selected="selected" class="current"';
            $ss16 = "";
        } else {
            $ss15 = "";
            $ss16 = ' selected="selected" class="current"';
        }

        echo '<div class="clearfix"><label>' . _ALLOWEXPAND . '</label>
         <select name="allow_expand" size="1">
           <option value="0" ' . $ss15 . '>' . _NO . '</option>
           <option value="1" ' . $ss16 . '>' . _YES . '</option>
         </select>
          <span title="' . _ALLOWEXPANDTEXT . '"></span></div> ';

        $ss21 = ($this->_config['animation_speed'] == 'normal')? ' selected="selected" class="current"' : '';
        $ss22 = ($this->_config['animation_speed'] == 'slow')? ' selected="selected" class="current"' : '';
        $ss23 = ($this->_config['animation_speed'] == 'fast')? ' selected="selected" class="current"' : '';

        echo '<div class="clearfix"><label>' . _ANIMATIONSPEED . '</label>
         <select name="animation_speed" size="1">
           <option value="normal" ' . $ss21 . '>' . _ANINORMAL . '</option>
           <option value="slow" ' . $ss22 . '>' . _ANISLOW . '</option>
           <option value="fast" ' . $ss23 . '>' . _ANIFAST . '</option>
         </select>
         </div> ';

        $ss21 = ($this->_config['animation_speed'] == 'normal')? ' selected="selected" class="current"' : '';
        $ss22 = ($this->_config['animation_speed'] == 'slow')? ' selected="selected" class="current"' : '';
        $ss23 = ($this->_config['animation_speed'] == 'fast')? ' selected="selected" class="current"' : '';

        echo '<div class="clearfix"><label>' . _SLIDESHOW . '</label><input type="text" name="slideshow" maxlength="50" value="' . $this->_config['slideshow'] . '" size="5" /> <span title="' . _SLIDESHOWTEXT . '"></span></div>';

        if ($this->_config['autoplay_slideshow'] == 0) {
            $ss25 = ' selected="selected" class="current"';
            $ss26 = "";
        } else {
            $ss25 = "";
            $ss26 = ' selected="selected" class="current"';
        }

        echo '<div class="clearfix"><label>' . _AUTOPLAYSLIDESHOW . '</label>
         <select name="autoplay_slideshow" size="1">
           <option value="0" ' . $ss25 . '>' . _NO . '</option>
           <option value="1" ' . $ss26 . '>' . _YES . '</option>
         </select>
         </div> ';

        echo '<div class="clearfix"><label>' . _OVERLAY_GALLERY . '</label>
         <select name="overlay_gallery" size="1">
           <option value="0" ' . $ss31 . '>' . _NO . '</option>
           <option value="1" ' . $ss32 . '>' . _YES . '</option>
         </select>
         </div> ';

        echo '<div class="clearfix"><label>' . _OVERLAY_GALLERY_MAX . '</label><input type="text" name="overlay_gallery_max" maxlength="50" value="' . $this->_config['overlay_gallery_max'] . '" size="5" /> <span title="' . _OVERLAY_GALLERY_MAX_TEXT . '"></span></div>';

        if ($this->_config['hideflash'] == 0) {
            $ss33 = ' selected="selected" class="current"';
            $ss34 = "";
        } else {
            $ss33 = "";
            $ss34 = ' selected="selected" class="current"';
        }

        echo '<div class="clearfix"><label>' . _HIDEFLASH . '</label>
         <select name="hideflash" size="1">
           <option value="0" ' . $ss33 . '>' . _NO . '</option>
           <option value="1" ' . $ss34 . '>' . _YES . '</option>
         </select>
         </div> ';

        $ss35 = ($this->_config['wmode'] == 'window')? ' selected="selected" class="current"' : '';
        $ss36 = ($this->_config['wmode'] == 'direct')? ' selected="selected" class="current"' : '';
        $ss37 = ($this->_config['wmode'] == 'opaque')? ' selected="selected" class="current"' : '';
        $ss38 = ($this->_config['wmode'] == 'transparent')? ' selected="selected" class="current"' : '';
        $ss39 = ($this->_config['wmode'] == 'gpu')? ' selected="selected" class="current"' : '';

        echo '<div class="clearfix"><label>' . _WMODE . '</label>
         <select name="wmode" size="1">
           <option value="window" ' . $ss35 . '>' . _WINDOW . '</option>
           <option value="direct" ' . $ss36 . '>' . _DIRECT . '</option>
           <option value="opaque" ' . $ss37 . '>' . _OPAQUE . '</option>
           <option value="transparent" ' . $ss38 . '>' . _TRANSPARENT . '</option>
           <option value="gpu" ' . $ss39 . '>' . _GPU . '</option>
         </select>
          <span title="' . _WMODETEXT . '"></span></div> ';

        $ss35 = ($this->_config['wmode'] == 'window')? ' selected="selected" class="current"' : '';
        $ss36 = ($this->_config['wmode'] == 'direct')? ' selected="selected" class="current"' : '';
        $ss37 = ($this->_config['wmode'] == 'opaque')? ' selected="selected" class="current"' : '';
        $ss38 = ($this->_config['wmode'] == 'transparent')? ' selected="selected" class="current"' : '';
        $ss39 = ($this->_config['wmode'] == 'gpu')? ' selected="selected" class="current"' : '';

        if ($this->_config['autoplay'] == 0) {
            $ss41 = ' selected="selected" class="current"';
            $ss42 = "";
        } else {
            $ss41 = "";
            $ss42 = ' selected="selected" class="current"';
        }

        echo '<div class="clearfix"><label>' . _AUTOPLAYVIDEOS . '</label>
         <select name="autoplay" size="1">
           <option value="0" ' . $ss41 . '>' . _NO . '</option>
           <option value="1" ' . $ss42 . '>' . _YES . '</option>
         </select>
         </div> ';

        if ($this->_config['modal'] == 0) {
            $ss45 = ' selected="selected" class="current"';
            $ss46 = "";
        } else {
            $ss45 = "";
            $ss46 = ' selected="selected" class="current"';
        }

        echo '<div class="clearfix"><label>' . _MODAL . '</label>
         <select name="modal" size="1">
           <option value="0" ' . $ss45 . '>' . _NO . '</option>
           <option value="1" ' . $ss46 . '>' . _YES . '</option>
         </select>
         <span title="' . _MODALTEXT . '"></span></div> ';

        if ($this->_config['deeplinking'] == 0) {
            $ss47 = ' selected="selected" class="current"';
            $ss48 = "";
        } else {
            $ss47 = "";
            $ss48 = ' selected="selected" class="current"';
        }

        echo '<div class="clearfix"><label>' . _DEEPLINKING . '</label>
         <select name="deeplinking" size="1">
           <option value="0" ' . $ss47 . '>' . _NO . '</option>
           <option value="1" ' . $ss48 . '>' . _YES . '</option>
         </select>
         </div> ';

        if ($this->_config['keyboard_shortcuts'] == 0) {
            $ss51 = ' selected="selected" class="current"';
            $ss52 = "";
        } else {
            $ss51 = "";
            $ss52 = ' selected="selected" class="current"';
        }

        echo '<div class="clearfix"><label>' . _KEYBOARDSHORTCUTS . '</label>
         <select name="keyboard_shortcuts" size="1">
           <option value="0" ' . $ss51 . '>' . _NO . '</option>
           <option value="1" ' . $ss52 . '>' . _YES . '</option>
         </select>
         </div> ';

        echo '<center><input type="submit" value="' . _SAVE . '" size="20" /></center><br /><br />';

        echo '<center><a title="' . _DESCRIPTION . '" rel="pretty[box]" href="' . $this->_source_path . '/test.jpg">' . mxCreateImage($this->_source_path . '/test.jpg', _TITLE, array('width' => '100', 'height' => '100')) . '</a></center><br />';

        echo '</fieldset></form>';


        include('footer.php');
    }
	
	function _settings1($ok=0)
	{
		$returnflag=false;
		pmxHeader::add_lightbox(true) ;
		$tb = load_class('AdminForm', 'lightbox'); /* erstmal AdminForm laden */

		switch (pmxAdminForm::CheckButton()) {
			case "save":
				$this->_save();
				break;
			default:
				break;
		}
		
		/* Werte vorbereiten */
		
		$info = _LIGHTBOX;	

	/* Form zusammen stellen */
	
    
		//$tb->__set('target_url', adminUrl(PMX_MODULE));
		
		//$tb->__set("tb_direction", 'right');
		//$tb->__set("infobutton", false);
		//$tb->__set("tb_pic_heigth", 25);
		//$tb->__set("csstoolbar", "toolbar1");
		//$tb->__set("cssform", "a304030");
		$tb->__set('buttontext', true);
		$tb->__set('homelink', false);
		//$tb->__set('fieldhomebutton', true);
		
		
		//$tb->addToolbar("accept");
		//$tb->addToolbar("save");
		
		/* general settings */
		$tb->addFieldset("lb", "", "", false);
		$tb->add("lb","hidden","ok",1);
		$tb->add("lb","html",'
                <div class="card text-center" style="width: 20rem;">
                    <div class="card-block">
                        <a title="' . _DESCRIPTION . '" rel="pretty[box]" href="' . $this->_source_path . '/test.jpg">' . mxCreateImage($this->_source_path . '/test.jpg', _TITLE, array('width' => '100', 'height' => '100')) . '</a>
                    </div>
                </div>');
		
		/* Designs */
        $path = str_replace('/', DS, PMX_LAYOUT_DIR . '/jquery/images/prettyPhoto/*/sprite.png');
        $options = array();
        foreach ((array)glob($path) as $theme) {
            if ($theme) {
                $theme = basename(dirname($theme));
                $themex = ($theme == 'default') ? 'pp_default' : $theme;
                $options[$themex] =  $theme ;
            }
        }	
		$tb->add("lb","select","theme",$this->_config['theme'],_DESIGN,"",1,$options);
		$tb->add("lb","yesno","show_title",$this->_config['show_title'],_SHOWTITLE);
		$tb->add("lb","number","opacity",$this->_config['opacity'],_OPACITY,"0 - 1",3,"min='0' max='1' step='0.05'");
		$tb->add("lb","yesno","allow_resize",$this->_config['allow_resize'],_ALLOWRESIZE ,"");
		$tb->add("lb","yesno","allow_expand",$this->_config['allow_expand'],_ALLOWEXPAND,_ALLOWEXPANDTEXT);

		$aspeed=array("normal"=>_ANINORMAL,"slow"=>_ANISLOW,"fast"=>_ANIFAST);
		$tb->add("lb","select","",$this->_config['animation_speed'],_ANIMATIONSPEED,"",1,$aspeed);
		
		$tb->add("lb","input","slideshow",$this->_config['slideshow'],_SLIDESHOW,_SLIDESHOWTEXT,5);
		$tb->add("lb","yesno","autoplay_slideshow",$this->_config['autoplay_slideshow'],_AUTOPLAYSLIDESHOW,"",1);
		$tb->add("lb","yesno","overlay_gallery",$this->_config['overlay_gallery'],_OVERLAY_GALLERY,"",1);
		$tb->add("lb","input","overlay_gallery_max",$this->_config['overlay_gallery_max'],_OVERLAY_GALLERY_MAX,_OVERLAY_GALLERY_MAX_TEXT,5);
		$tb->add("lb","yesno","hideflash",$this->_config['hideflash'],_HIDEFLASH);
		
		/* this->_config['wmode']*/
		$wmode=array("window"=>_WINDOW,"direct"=>_DIRECT,"opaque"=>_OPAQUE,"gpu"=>_GPU,"transparent"=>_TRANSPARENT);
		$tb->add("lb","select","wmode",$this->_config['wmode'],_WMODE,_WMODETEXT,1,$wmode);
		
		$tb->add("lb","yesno","autoplay",$this->_config['autoplay'],_AUTOPLAYVIDEOS);
		$tb->add("lb","yesno","modal",$this->_config['modal'],_MODAL,_MODALTEXT);
		$tb->add("lb","yesno","deeplinking",$this->_config['deeplinking'],_DEEPLINKING);
		$tb->add("lb","yesno","keyboard_shortcuts",$this->_config['keyboard_shortcuts'],_KEYBOARDSHORTCUTS);
		
		
		   /* formular abrufen */
		$form = $tb->Show();
		/*
		 * Template
		 */
		// Template initialisieren
		$template = load_class('Template');
		$template->init_path(__FILE__);

		/* hier die Ausgabefelder angeben */

		/* Variablen an das Template uebergeben */
		$template->assign(compact('form', 'info'));

		mxIncludeHeader();
		/* Template ausgeben (echo) */
		$template->display('main.html');

		mxIncludeFooter();	
	
	}

    /**
     * lightbox_admin::_save()
     *
     * @return
     */
    function _save()
    {
        /* $_POST überschreibt aktuelle config */
        $conf = array_merge($this->_config, $_POST);

		$config=load_class('Config','lightbox');
		$ok= $config->setSection('lightbox', $conf);
		
        // $content = "<?php\n";
        // $content .= "/**\n";
        // $content .= " * pragmaMx - Web Content Management System\n";
        // $content .= " * Copyright by pragmaMx Developer Team - http://www.pragmamx.org\n";
        // $content .= " * config.php for LightBox -  writen by Olaf Herfurth\n";
        // $content .= " */\n\n";
        // $content .= "defined('mxMainFileLoaded') or die('access denied');\n\n";
        // $content .= "\$mxPrettyPhoto['animation_speed'] = '" . $conf['animation_speed'] . "';\n";
        // $content .= "\$mxPrettyPhoto['slideshow'] = '" . $conf['slideshow'] . "';\n";
        // $content .= "\$mxPrettyPhoto['autoplay_slideshow'] = " . intval($conf['autoplay_slideshow']) . ";\n";
        // $content .= "\$mxPrettyPhoto['opacity'] = " . strval(floatval(str_replace(',', '.', $conf['opacity']))) . ";\n";
        // $content .= "\$mxPrettyPhoto['show_title'] = " . intval($conf['show_title']) . ";\n";
        // $content .= "\$mxPrettyPhoto['allow_resize'] = " . intval($conf['allow_resize']) . ";\n";
        // $content .= "\$mxPrettyPhoto['allow_expand'] = " . intval($conf['allow_expand']) . ";\n";
        // $content .= "\$mxPrettyPhoto['counter_separator_label'] = '" . $conf['counter_separator_label'] . "';\n";
        // $content .= "\$mxPrettyPhoto['theme'] = '" . $conf['theme'] . "';\n";
        // $content .= "\$mxPrettyPhoto['hideflash'] = " . intval($conf['hideflash']) . ";\n";
        // $content .= "\$mxPrettyPhoto['wmode'] = '" . $conf['wmode'] . "';\n";
        // $content .= "\$mxPrettyPhoto['autoplay'] = " . intval($conf['autoplay']) . ";\n";
        // $content .= "\$mxPrettyPhoto['modal'] = " . intval($conf['modal']) . ";\n";
        // $content .= "\$mxPrettyPhoto['deeplinking'] = " . intval($conf['deeplinking']) . ";\n";
        // $content .= "\$mxPrettyPhoto['overlay_gallery'] = " . intval($conf['overlay_gallery']) . ";\n";
        // $content .= "\$mxPrettyPhoto['overlay_gallery_max'] = " . intval($conf['overlay_gallery_max']) . ";\n";
        // $content .= "\$mxPrettyPhoto['keyboard_shortcuts'] = " . intval($conf['keyboard_shortcuts']) . ";\n";
        /*  $content .= "\n?>"; */

        // $ok = mx_write_file($this->_configfile, $content); 
		
        /* error > exit */
        if (!$ok) {
            return mxErrorScreen(_ADMIN_SETTINGNOSAVED, '', true);
        }

        /* alles ok > redirect */
        return mxRedirect(adminUrl(PMX_MODULE), _ADMIN_SETTINGSAVED, 1);
    }

    /**
     * lightbox_admin::_init_config()
     *
     * @return
     */
    private function _init_config()
    {
		$config=load_class('Config','lightbox');
		$temp = $config->getSection('lightbox');
        $mxPrettyPhoto = array();
        if (file_exists($this->_configfile)){
			include($this->_configfile);
			/* alte Version der Konfigurationsdatei */
			if (isset($mxPrettyPhoto['animationSpeed'], $mxPrettyPhoto['autoplaySlideshow'], $mxPrettyPhoto['showTitle'], $mxPrettyPhoto['allowresize'])) {
				$mxPrettyPhoto['animation_speed'] = $mxPrettyPhoto['animationSpeed'];
				$mxPrettyPhoto['autoplay_slideshow'] = $mxPrettyPhoto['autoplaySlideshow'];
				$mxPrettyPhoto['show_title'] = $mxPrettyPhoto['showTitle'];
				$mxPrettyPhoto['allow_resize'] = $mxPrettyPhoto['allowresize'];
			}
			@unlink($this->_configfile);
		}
        $this->_config = array_merge($this->_defaults, $mxPrettyPhoto, $temp);
    }
}

$tmp = new lightbox_admin($op);
$tmp = null;

?>