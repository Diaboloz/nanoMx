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

/* Sprachdatei auswählen */
mxGetLangfile(__DIR__);

load_class('Userconfig', false);

/**
 * userconfig_admin
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2010
 * @version $Id: index.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class userconfig_admin extends pmxUserconfig {
    private $_pm_config = array();

    /**
     * pmxUserEdit::__construct()
     *
     * @param mixed $op
     */
    public function __construct($op)
    {
        if (!mxGetAdminPref("radminuser")) {
            mxErrorScreen("Access Denied");
            die();
        }

        if (!defined("mxYALoaded")) {
            define("mxYALoaded", 1);
        }
        // mxSessionSetVar('panel', MX_ADMINPANEL_USERS);
        parent::__construct();

        switch ($op) {
            case PMX_MODULE . '/save':
                $this->_save();
                break;

            default:
                $this->_view();
                break;
        }
    }

    /**
     * userconfig_admin::_view()
     *
     * @return
     */
    private function _view()
    {
        global $prefix;

        mxGetLangfile('Your_Account');

        $this->_set_pmconfig();
        $pagetitle = _PTITLE;

        $config = (array)$this->get_config();

        /* Template initialisieren */
        $template = load_class('Template');
        $template->init_path(__FILE__);
        $template->assign($config);

        /* Reiter Optionen / Register */
        $template->radio_registertype = $this->radio_registertype($this->register_option);
        $template->select_minpass = $this->select_minchars($this->minpass);
        $template->select_uname_min_chars = $this->select_minchars($this->uname_min_chars);
        $template->select_usergroups = $this->select_usergroups($this->default_group);
        $template->tab_register = $template->fetch('register.html');

        /* Reiter Benutzerbild */
        $template->tab_photo = $template->fetch('photo.html');

        /* Reiter Begrüssungsnachricht */
        $template->tab_welcome = '';
        if ($this->_pm_config['activ']) {
            $template->select_pm_users = $this->select_pm_users($this->msgadminid);
            $template->select_pm_language = $this->select_pm_language($this->msgdefaultlang);
            $template->showsmilies = $this->showsmilies($this->msgicon);
            $template->showsmilies_folder = $this->_pm_config['url_icons'];
            $template->tab_welcome = $template->fetch('welcome.html');
        }

        /* Reiter Benutzerpunkte */
        $template->pointsarray = $this->pointsarray();
        $template->tab_points = $template->fetch('points.html');

        /* Reiter Sonstiges */
        $template->tab_other = $template->fetch('other.html');

        include('header.php');
        title(_USERSADMINHEAD);
        $template->display('usersconfig.html');
        include('footer.php');
    }

    /**
     * userconfig_admin::_save()
     *
     * @return
     */
    private function _save()
    {
        /* Standardwerte und aktuelle Config einlesen */
        $defaults = (array)$this->get_config();

        /* numerische Werte korrigieren */
        foreach ($defaults as $key => $value) {
            switch (true) {
                case is_numeric($value):
                case is_bool($value):
                    settype($defaults[$key], 'float');
                    settype($_POST[$key], 'float');
            }
        }

        /* file_maxsize wird in KB angegeben, aber in byte gespeichert! */
        $_POST['file_maxsize'] = $_POST['file_maxsize'] * 1024;

        $pici = load_class('Userpic');
        $pici->check_config($_POST, $defaults);

        if (isset($_POST['image'])) {
            /* anderer Name, weil's aus PM-Funktion mxPmGetPictos() kommt... */
            $_POST['msgicon'] = $_POST['image'];
            unset($_POST['image']);
        }

        if (empty($_POST['yastartpage'])) {
            unset($_POST['yastartpage']);
        }

        /* AGB-Zustimmung nur wenn auch sinnig */
        if (empty($_POST['agb_agree_link'])) {
            $_POST['agb_agree'] = 0;
        }

        /* $_POST überschreibt aktuelle config und diese überschreibt Standardwerte */
        $conf = array_intersect_key($_POST, $defaults);
        $conf = array_merge($defaults, $conf);

        $content = "<?php\n";
        $content .= "/**\n";
        $content .= " * pragmaMx - Web Content Management System\n";
        $content .= " * Copyright by pragmaMx Developer Team - http://www.pragmamx.org\n";
        $content .= " * written with: \$Id: index.php 6 2015-07-08 07:07:06Z PragmaMx $\n";
        $content .= " */\n\n";
        $content .= "defined('mxMainFileLoaded') or die('access denied');\n\n";

        foreach ($defaults as $key => $defaultvalue) {
            switch (true) {
                case is_numeric($defaultvalue):
                case is_bool($defaultvalue):
                    $content .= "\$" . $key . " = " . $conf[$key] . ";\n";
                    break;
                case is_scalar($defaultvalue):
                    $content .= "\$" . $key . " = '" . $conf[$key] . "';\n";
                    break;
                case self::is_assoc($defaultvalue) && self::is_assoc($conf[$key]):
                    $tmp = array();
                    foreach ($conf[$key] as $xkey => $xvalue) {
                        $tmp[] = "'$xkey'=>'$xvalue'";
                    }
                    $content .= "\$" . $key . " = array(" . implode(',', $tmp) . ");\n";
                    break;
                case is_array($defaultvalue):
                    $content .= "\$" . $key . " = array('" . implode("','", $conf[$key]) . "');\n";
                    break;
                default:
                    $content .= "\$" . $key . " = '" . serialize($conf[$key]) . "';\n";
            }
        }
        $content .= "\n?>";

        /* Settings schreiben: */
        $ok = mx_write_file($this->_configfile, $content, true);

        /* error > exit */
        if (!$ok) {
            return mxRedirect(adminUrl(PMX_MODULE), _ADMIN_SETTINGNOSAVED, 5);
        }

        include_once(PMX_SYSTEM_DIR . DS . 'mx_reset.php');
        resetPmxCache();

        return mxRedirect(adminUrl(PMX_MODULE), _ADMIN_SETTINGSAVED, 1);
    }

    /**
     * userconfig_admin::select_pm_users()
     *
     * @param mixed $msgadminid
     * @return
     */
    private function select_pm_users($msgadminid)
    {
        global $user_prefix, $prefix;

        $qry = "SELECT a.user_uid AS uid, u.uname, u.user_stat
            FROM ${prefix}_authors AS a
            INNER JOIN {$user_prefix}_users AS u ON a.user_uid = u.uid
            WHERE u.user_stat=1
            ORDER BY u.uname";

        $result = sql_query($qry);
        $options = '';
        while ($row = sql_fetch_array($result)) {
            $options .= '<option value="' . $row['uid'] . '"' . (($row['uid'] == $msgadminid) ?' selected="selected" class="current"' : '') . '>' . $row['uname'] . '</option>';
        }
        return $options;
    }

    /**
     * userconfig_admin::select_pm_language()
     *
     * @param mixed $msgdefaultlang
     * @return
     */
    private function select_pm_language($msgdefaultlang)
    {
        $handle = opendir('modules/User_Registration/language');
        while ($file = readdir($handle)) {
            if (preg_match("/^hello\-(.+)\.php/", $file, $matches)) {
                $langlist[] = str_replace(".php", "", str_replace("hello-", "", $file));
            }
        }
        asort($langlist);
        closedir($handle);
        $options = '';
        while (list ($key, $file) = each ($langlist)) {
            $options .= '<option value="' . $file . '"' . (($msgdefaultlang == $file) ? ' selected="selected" class="current">' : '>') . mxGetLanguageString($file) . '</option>';
        }
        return $options;
    }

    /**
     * userconfig_admin::select_minchars()
     *
     * @param mixed $min_chars
     * @param integer $start
     * @param integer $end
     * @return
     */
    private function select_minchars($min_chars, $start = 3, $end = 12)
    {
        $options = '<option value="0"' . (($min_chars == 0) ? ' selected="selected" class="current">' : '>') . _WORDOFF . '</option>';
        for($i = $start; $i <= $end; $i++) {
            $options .= '<option value="' . $i . '"' . (($min_chars == $i) ? ' selected="selected" class="current">' : '>') . $i . '</option>';
        }
        return $options;
    }

    /**
     * userconfig_admin::radio_registertype()
     *
     * @param mixed $regoption
     * @return
     */
    private function radio_registertype($regoption)
    {
        // Registrierung neuer Benutzer
        $options = array();
        for ($i = 0; $i <= 4; $i++) {
            $options[] = '<input type="radio" name="register_option" value="' . $i . '"' . (($regoption == $i) ? ' checked="checked" class="current"' : '') . ' /> ' . constant('_ADMIN_UREGTYPE_' . $i);
        }
        return implode('<br />', $options);
    }

    /**
     * userconfig_admin::select_usergroups()
     *
     * @param mixed $defaultgroup
     * @return
     */
    private function select_usergroups($defaultgroup)
    {
        $options = getAllAccessLevelSelectOptions($defaultgroup);
        return $options;
    }

    /**
     * userconfig_admin::pointsarray()
     *
     * @return
     */
    private function pointsarray()
    {
        // array('entries','pics','comments','votes','posts','threads');
        $out = array();
        $out[] = array('label' => _UPOINTS_ENTRIES, 'name' => 'upoints_entries', 'value' => $this->upoints_entries, 'size' => '5');
        $out[] = array('label' => _UPOINTS_PICS, 'name' => 'upoints_pics', 'value' => $this->upoints_pics, 'size' => '5');
        $out[] = array('label' => _UPOINTS_COMMENTS, 'name' => 'upoints_comments', 'value' => $this->upoints_comments, 'size' => '5');
        $out[] = array('label' => _UPOINTS_VOTES, 'name' => 'upoints_votes', 'value' => $this->upoints_votes, 'size' => '5');
        $out[] = array('label' => _UPOINTS_POSTS, 'name' => 'upoints_posts', 'value' => $this->upoints_posts, 'size' => '5');
        $out[] = array('label' => _UPOINTS_THREADS, 'name' => 'upoints_threads', 'value' => $this->upoints_threads, 'size' => '5');
        return $out;
    }

    /**
     * userconfig_admin::_set_pmconfig()
     * Konfiguration des PM-Moduls abfragen
     *
     * @return nothing
     */
    private function _set_pmconfig()
    {
        /* TODO: auslesen der config über hook ??? */
        $PMMODULENAME = 'Private_Messages';

        $defaults = array(/* Standardwerte */
            'activ' => mxModuleActive($PMMODULENAME),
            'url_icons' => '',
            'subjectsubjectdefaulticon' => '',
            );

        if ($defaults['activ'] && file_exists(PMX_MODULES_DIR . DS . $PMMODULENAME . DS . 'config.php')) {
            include_once(PMX_MODULES_DIR . DS . $PMMODULENAME . DS . 'config.php');
        }

        $this->_pm_config = array_merge($defaults, get_defined_vars());

        if (empty($this->_pm_config['url_icons'])) {
            $this->_pm_config['activ'] = false;
        }
    }

    /* geklaut aus mxPmGetPictos() des PM Moduls */
    private function showsmilies($current, $max = 10)
    {
        if (!$this->_pm_config['activ']) {
            return;
        }

        $count = 1;
        $endings = array('gif', 'png', 'jpg', 'jpeg');

        foreach ((array)glob($this->_pm_config['url_icons'] . '/*.*') as $filepath) {
            $pathinfo = pathinfo($filepath);

            switch (true) {
                case empty($pathinfo['extension']):
                case !in_array(strtolower($pathinfo['extension']), $endings):
                    break;
                default:
                    $count++;
                    $file = $pathinfo['basename'];
                    switch ($file) {
                        case $this->_pm_config['subjectsubjectdefaulticon']:
                            $index = 0;
                            break;
                        case $current:
                            $index = 1;
                            break;
                        default:
                            $index = $count;
                    }

                    $pictos[$index] = '
                      <input type="radio" name="msgicon" id="pixto' . $pathinfo['filename'] . '" value="' . $file . '"' . (($file == $current) ? ' checked="checked"' : '') . ' />
                      <label for="pixto' . $pathinfo['filename'] . '">
                      ' . mxCreateImage($filepath) . '
                      </label>
                      ';
            }
        }
        ksort($pictos);
        $pictos = array_slice($pictos, 0, $max);
        return implode('', $pictos);
    }

    /**
     * userconfig_admin::is_assoc()
     *
     * @param mixed $array
     * @return bool
     */
    private static function is_assoc($array)
    {
        return (is_array($array) && (0 !== count(array_diff_key($array, array_keys(array_keys($array)))) || count($array) == 0));
    }
}

$tmp = new userconfig_admin($op);
$tmp = null;

?>