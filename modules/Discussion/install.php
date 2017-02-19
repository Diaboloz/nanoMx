<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: PragmaMx $
 * $Revision: 179 $
 * $Date: 2016-07-05 15:00:35 +0200 (mar. 05 juil. 2016) $
 *
 * based on eBoard v1.1, rewrite and modified by
 * vkpMx-Developer-Team (http://www.maax-design.de)
 * Original source-code made by the XMB-team
 * (XMB-Forum, http://www.xmbforum.com), modified for nukestyle-systems
 * by Trollix (XForum, http://www.trollix.com).
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

defined('mxMainFileLoaded') or die('access denied');

/* check ob die verwendete pragmaMx Version korrekt ist */
(defined('PMX_VERSION') && version_compare(PMX_VERSION, '1.12', '>=')) or
die('Sorry, pragmaMx-Version >= 2.0 is required for mxBoard.');

/* nur Sysadmins dürfen das */
(mxGetAdminPref('radminsuper')) or
die ("<h1>Access Denied</h1> You must be administrator of your system to install this module.");

define('MXB_INIT', true);

include_once(dirname(__FILE__) . DS . 'includes' . DS . 'initvar.php');
include_once(dirname(__FILE__) . DS . 'includes' . DS . 'functions.php');
include_once(PMX_SYSTEM_DIR . DS . 'mx_install.php');
include_once(dirname(__FILE__) . DS . 'includes' . DS . 'helper.php');

class mxbInstall {
    private static $_config = array();
    private static $_lang = 'german';

    public function __construct()
    {
        global $prefix;

        $this->_init_config();

        $this->_setlanguage();

        $err = $this->_error();

        switch (true) {
            case isset($_POST['act']) && $_POST['act'] == 'step2':
                if (!trim($_POST['tablepre']) || !preg_match("#^[a-z][a-z0-9_]+$#", $_POST['tablepre'])) {
                    return header('Location: ' . htmlspecialchars_decode(MXB_BASEMOD) . 'install&err=prefix');
                }

                if (isset($_POST['setpmxprefix']) && $_POST['setpmxprefix']) {
                    // settings.php fehlt, Neuinstallation
                    $_POST['tablepre'] = rtrim($GLOBALS['prefix'] . '_' . $_POST['tablepre'], ' _') . '_';
                }

                $ok = $this->_savesettings($_POST);
                if (!$ok) {
                    return header('Location: ' . htmlspecialchars_decode(MXB_BASEMOD) . 'install&err=savesett');
                }

                ob_start();
                $tables = setupGetTables();
                include(dirname(__FILE__) . DS . 'core' . DS . 'install.tabledef.php');

                $this->_delfiles(); // unnötige Dateien löschen
                $basemessages = ob_get_clean();

                $template = load_class('Template');
                $template->init_path(__FILE__);
                $template->assign('basemessages', $basemessages);
                self::_header();
                $template->display('install.step2.html');
                self::_footer();
                break;

            default:
                /* ******************************************************************** */

                if (file_exists(MXB_SETTINGSFILE)) {
                    if (!is_writable(MXB_SETTINGSFILE)) {
                        mx_chmod(MXB_SETTINGSFILE, PMX_CHMOD_UNLOCK);
                    }
                    if (!is_writable(MXB_SETTINGSFILE)) {
                        self::_msg_file_not_written(MXB_SETTINGSFILE);
                        return;
                    }
                } else if (!is_writable(dirname(MXB_SETTINGSFILE))) {
                    self::_msg_file_not_written(dirname(MXB_SETTINGSFILE));
                    return;
                }

                $langlist = mxbGetAvailableLanguages();
                $langlist = array_merge(array('- ' . _TEXTDEFAULT => ''), $langlist);
                $languageoptions = '';
                foreach ($langlist as $key => $value) {
                    $sel = ($value == self::$_config['langfile']) ? ' selected="selected" class="current"' : ''; // Standard vorselektieren
                    $languageoptions .= '<option value="' . $value . '"' . $sel . '>' . $key . '</option>';
                }

                switch (true) {
                    case !self::$_config['tablepre']:
                        // settings.php fehlt, Neuinstallation
                        $pmxprefix = $GLOBALS['prefix'] . '_';
                        $mxbprefix = strtolower(str_ireplace('eboard', 'mxboard', preg_replace('#[^a-zA-Z0-9_]#', '_', MXB_MODNAME)));
                        break;
                    case strpos(self::$_config['tablepre'], $prefix . '_') === 0:
                        // prefix am Anfang
                        $pmxprefix = $GLOBALS['prefix'] . '_';
                        $mxbprefix = rtrim(substr(self::$_config['tablepre'], strlen($pmxprefix)), '_');
                        break;
                    default:
                        // settings.php bereits vorhanden, Update
                        $pmxprefix = '';
                        $mxbprefix = self::$_config['tablepre'];
                }

                $assigns = array('err' => $err,
                    'languageflags' => $this->_show_languageflags(mxbGetAvailableLanguages('install')),
                    'languageoptions' => $languageoptions,
                    'themeoptions' => self::_get_options_themes(),
                    'pmxprefix' => $pmxprefix,
                    'mxbprefix' => $mxbprefix,
                    'tablepre' => self::$_config['tablepre'],
                    );

                $template = load_class('Template');
                $template->init_path(__FILE__);
                $template->assign($assigns);

                self::_header();
                $template->display('install.step1.html');
                self::_footer();
                break;
        }
    }

    private static function _header($tit = _TITRE)
    {
        $template = load_class('Template');
        $template->init_path(__FILE__);
        $template->assign('title', $tit);
        $template->display('install.header.html');
    }

    private static function _footer()
    {
        $template = load_class('Template');
        $template->init_path(__FILE__);
        $template->display('install.footer.html');
        return;
    }

    private function _savesettings($pvs)
    {
        $content = mxbHelper::get_config_string($pvs);

        if (file_exists(MXB_SETTINGSFILE)) {
            $result = mx_write_file(str_replace('.php', '', MXB_SETTINGSFILE) . '_bak_' . date('YmdHi') . '.php', file_get_contents(MXB_SETTINGSFILE), true);
        }
        $result = mx_write_file(MXB_SETTINGSFILE, trim($content), true);
        return $result !== false;
    }

    private static function _msg_file_not_written($filename)
    {
        self::_header();

        ?>
<div class="error">
<h2><?php echo _INSTFAILED ?></h2>
<p><?php echo _FILE_NOT_WRITEN ?><b><?php echo $filename ?></b></p>
<p><?php echo _MANUAL_RIGHTS ?></p>
</div>
<?php
        self::_footer();
    }

    private static function _get_options_themes()
    {
        $themes = self::get_themes();

        $options = '<option value="default">default</option>';
        foreach ($themes as $value) {
            $sel = ($value[0] == self::$_config['XFtheme']) ? ' selected="selected" class="current"' : ''; // Standard vorselektieren
            $options .= '<option value="' . $value[0] . '"' . $sel . '>' . str_replace('_', ' ', $value[0]) . '</option>';
        }
        return $options;
    }

    private function _setlanguage()
    {
        $langs[] = $GLOBALS['currentlang'];
        $langs[] = self::$_config['langfile'];
        $langs[] = 'german';
        $langs = array_unique(array_merge($langs, array_values(mxbGetAvailableLanguages('install'))));
        foreach ($langs as self::$_lang) {
            if (!self::$_lang) {
                continue;
            }
            $file = MXB_ROOTMOD . 'language' . DS . 'install-' . self::$_lang . '.php';
            if (file_exists($file)) {
                return include_once($file);
            }
        }
    }

    public static function get_themes()
    {
        $defaults = array(/* alle Themes */
            array('standard', '#ffffff', '#dededf', '#eeeeee', '#333399', '#9999ff', '#9999ff', '#ffffff', '#eeeeee', '#dcdcde', '#000000', '#000000', '1', '97%', '6', 'Verdana', '12px', 'sans-serif', '10px', 'grau', '', '', '', 'red', 'blue'),
           /* array('mxboard_html_layout_grau', '#FFFFFF', '#E1E1E1', '#CCCCCC', '#666666', '#FFFFFF', '#999999', '#FFFFFF', '#CCCCCC', '#F8F8F8', '#666666', '#666666', '1', '100%', '6', 'Verdana', '12', 'sans-serif', '9', 'grau', '', '', '', 'red', 'blue'),
            array('mxboard_html_layout_schwarz', '#333333', '#666666', '#999999', '#E9E9E9', '#333333', '#000000', '#E9E9E9', '#666666', '#999999', '#E9E9E9', '#E9E9E9', '1', '100%', '6', 'Verdana', '12', 'sans-serif', '9', 'schwarz', '', '', '', 'red', 'blue'),
            array('mxboard_html_layout_rot', '#FFFFFF', '#FF7800', '#CC0000', '#FF7800', '#FFFFFF', '#500404', '#FFFFFF', '#FF7800', '#FF7800', '#FFFFFF', '#FFFFFF', '1px', '100%', '6', 'Verdana', '12px', 'sans-serif', '9px', 'rot', '', '', '', 'red', 'blue'),
            array('mxboard_html_layout_gruen', '#FFFFFF', '#8DBE24', '#73A949', '#FFFFFF', '#FFFFFF', '#AEA651', '#FFFFFF', '#73A949', '#8DBE24', '#FFFFFF', '#FFFFFF', '1', '100%', '6', 'Verdana', '12px', 'sans-serif', '9px', 'gruen', '', '', '', 'red', 'blue'),
            array('mxboard_html_layout_gelb', '#FFFFFF', '#FF9900', '#FFCC33', '#000000', '#FFFFFF', '#FF6633', '#FFFFFF', '#FF9900', '#FFCC33', '#000000', '#000000', '1px', '100%', '6', 'Verdana', '12px', 'sans-serif', '9px', 'gelb', '', '', '', 'red', 'blue'),
            array('mxboard_html_layout_blau', '#ffffff', '#b0c0d0', '#F0F0F0', '#6780B8', '#ffffff', '#6780B8', '#ffffff', '#d0e0f0', '#b0c0d4', '#000000', '#000000', '1', '100%', '6', 'Verdana', '12px', 'sans-serif', '10px', 'blau', '', '', '', 'red', 'blue'),
            array('gray', '#ffffff', '#dededf', '#eeeeee', '#333399', '#778899', '#778899', '#ffffff', '#eeeeee', '#dcdcde', '#000000', '#000000', '1', '97%', '6', 'Verdana', '12px', 'sans-serif', '10px', 'grau', '', '', '', 'red', 'blue'),
            array('blue', '#ffffff', '#b0c0d0', '#d0e0f0', '#cc6600', '#000000', '#e0f0f9', '#000000', '#d0e0f0', '#b0c0d4', '#000000', '#000000', '1', '97%', '6', 'Arial', '12px', 'sans-serif', '10px', 'blau', '', '', '', 'red', 'blue'),
            array('coursapied', '#ffffff', '#dededf', '#eeeeee', '#333399', '#9999ff', '#9999ff', '#ffffff', '#eeeeee', '#dcdcde', '#000000', '#000000', '1', '97%', '6', 'Verdana', '12px', 'sans-serif', '10px', 'blau', '', '', '', 'red', 'blue'),
            array('moderngray', '#ffffff', '#f0f0f0', '#fbfbfb', '#6633cc', '#2a2f79', '#2a2f79', '#ffffff', '#f0f0f0', '#dededf', '#000000', '#000000', '1', '97%', '4', 'Arial', '12px', 'sans-serif', '10px', 'blau', '', '', '', 'red', 'blue'),
            array('woodlike', '#f6f7eb', '#e1e4ce', '#f6f7eb', '#000000', '#b1b78b', '#d9dcc2', '#000000', '#b1b78b', '#e1e4ce', '#000000', '#000000', '1', '97%', '4', 'Arial', '12px', 'Verdana', '10px', 'gruen', '', '', '', 'red', 'blue'),
			*/
            );
        return $defaults;
    }

    private function _init_config()
    {
        if (self::$_config) {
            return self::$_config;
        }

        $conf = array();
        if (file_exists(MXB_SETTINGSFILE)) {
            include(MXB_SETTINGSFILE);
            $conf = get_defined_vars();
            unset($conf['conf'], $conf['prefix'], $conf['user_prefix']);
        }
        self::$_config = array_merge(mxbHelper::get_defaults(), $conf);
    }

    private function _error()
    {
        $err = '';
        switch (true) {
            case !isset($_REQUEST['err']):
                return '';
            case $_REQUEST['err'] == 'prefix':
                return _ERRPREFIX;
            case $_REQUEST['err'] == 'savesett':
                return _FILE_NOT_WRITEN . MXB_SETTINGSFILE;
            default:
                return _ERRDEFAULT;
        }
    }

    private function _show_languageflags($languagelist = array(), $path = 'images/language', $extension = 'png')
    {
        $query = $_SERVER['QUERY_STRING'];
        if (isset($_GET['newlang'])) {
            $query = preg_replace('#[&?]?newlang=[a-zA-Z_]*#', '', $query);
        }
        $to = basename($_SERVER['PHP_SELF']);
        // index.php ist auch php_self=modules.php, deswegen hier index.php verwenden, falls $name leer ist
        if ($to == 'modules.php' && empty($_GET['name'])) {
            $to = './';
        }
        if ($query) {
            $to .= '?' . str_replace('&', '&amp;', $query) . '&amp;newlang=';
        } else {
            $to .= '?newlang=';
        }

        $languages = array_flip(mxGetAvailableLanguages());
        $linklist = array();

        switch (true) {
            case $tmp = array_intersect_key((array)$languagelist, $languages):
                foreach ($tmp as $language => $title) {
                    $linklist[] = '<a href="' . $to . $language . '" title="' . $title . '" rel="nofollow">' . mxCreateImage($path . '/flag-' . $language . '.' . $extension) . '</a>';
                }
                break;

            case $tmp = array_intersect_key($languages, array_flip((array)$languagelist)):
                foreach ($tmp as $language => $title) {
                    $linklist[] = '<a href="' . $to . $language . '" title="' . _SELECTGUILANG . ': ' . $title . '" rel="nofollow">' . mxCreateImage($path . '/flag-' . $language . '.' . $extension) . '</a>';
                }
                break;

            default:
                foreach ($languages as $language => $title) {
                    $linklist[] = '<a href="' . $to . $language . '" title="' . _SELECTGUILANG . ': ' . $title . '" rel="nofollow">' . mxCreateImage($path . '/flag-' . $language . '.' . $extension) . '</a>';
                }
                break;
        }

        if (count($linklist) < 2) {
            return false;
        }

        return implode("\n", $linklist);
    }

    private function _is_update()
    {
        $defaults = mxbHelper::get_defaults();
        return isset($defaults['table_nukemembers'], $defaults['table_nukepm'], $defaults['avatarpath'], $defaults['teamavatarpath']);
    }

    private function _delfiles()
    {
        $files = array(/* unnötige Dateien */
            'defaulttheme.php',
            'footer.php',
            'functions.php',
            'functions2.php',
            'functions_unsafe.php',
            'header.php',
            'initvar.php',
            'jumper.php',
            'mxb_mod_rewrite.inc.php',
            'images/lastpost.gif',
            'images/linked.gif',
            'images/print1.gif',
            'lang/english.install.php',
            'lang/english.lang.php',
            'lang/french.install.php',
            'lang/french.lang.php',
            'lang/german.install.php',
            'lang/german.lang.php',
            'lang/german_du.lang.php',
            'lang/turkish.lang.php',
            'templates/themes.inc.php',
            );

        foreach ($files as $file) {
            $dir = dirname(__FILE__) . DS;
            if (is_file($dir . $file)) {
                @mx_chmod($dir . $file, PMX_CHMOD_FULLUNOCK);
                @unlink($dir . $file);
            }
        }
    }
}

if (!isset($mxb_in_setup)) {
    $temp = new mxbInstall();
    $temp = null;
}

?>