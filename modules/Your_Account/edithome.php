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
defined('mxYALoaded') or die('access denied');

/**
 * pmxUserEditHome
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2010
 * @version $Id: edithome.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxUserEditHome {
    // const session = 'editusersession';
    private $_user = array();
    private $_userpic = false;
    private $_setlang = '';

    /**
     * pmxUserEditHome::__construct()
     *
     * @param mixed $op
     */
    public function __construct($op)
    {
        /* kein User? weg hier... */
        if (!MX_IS_USER) {
            return main();
        }

        require_once(PMX_SYSTEM_DIR . DS . 'mx_userfunctions.php');

        /* aktuelle Userdaten aus Datenbank lesen */
        $this->_user = mxGetUserData();

        switch ($op) {
            case 'savehome':
                if (isset($_POST['uid'])) {
                    $this->savehome($_POST);
                    break;
                }
            case 'edithome':
            default:
                $this->edithome();
        }
    }

    /**
     * edithome()
     * Maske zum editieren der Benutzereinstellungen
     *
     * @return
     */
    private function edithome()
    {
        $userconfig = load_class('Userconfig');

        ob_start();

        $sel = (empty($this->_user['user_viewemail'])) ? '' : 'checked="checked"';
        echo '
			<div class="control-group">
				<label for="user_viewemail">' . _ALLOWEMAILVIEW . '</label>
				<input type="radio" name="user_viewemail" id="user_viewemail" value="1" ' . ((empty($this->_user['user_viewemail'])) ? '' : ' checked="checked"') . ' />' . _YES . '
				<input type="radio" name="user_viewemail" value="0" ' . ((empty($this->_user['user_viewemail'])) ? ' checked="checked"' : '') . ' />' . _NO . '
			</div>';

        /* verfuegbare der Sprachen ermitteln */
        $languages = mxGetAvailableLanguages();
        ksort($languages);

        $linklist = array();

        $sel = (empty($this->_user['user_lang']) ? ' selected="selected" class="current"' : '');
        $linklist[] = '<option value=""' . $sel . '>&nbsp;- ' . _YA_SITEDEFAULT . '</option>';
        foreach($languages as $caption => $langu) {
            $sel = (!$sel && $langu == $this->_user['user_lang']) ? ' selected="selected" class="current"' : '';
            $linklist[] = '<option value="' . $langu . '" ' . $sel . '>' . $caption . '</option>';
        }
        if (count($linklist) > 2) {
            echo '
				<div class="control-group">
					<label for="user_lang">' . _PREFEREDLANG . '</label>
					<select name="user_lang" id="user_lang">' . implode("\n", $linklist) . '</select>
				</div>';
        }

        if ($userconfig->allowchangetheme || MX_IS_ADMIN) {
            $themelist = mxGetAvailableThemes();
            natcasesort($themelist);
            $sel = (empty($this->_user['theme']) ? ' selected="selected" class="current"' : '');
            $options[] = '<option value=""' . $sel . '>&nbsp;- ' . _YA_SITEDEFAULT . '</option>';
            foreach ($themelist as $theme) {
                $sel = (!$sel && $this->_user['theme'] == $theme) ? ' selected="selected" class="current"' : '';
                $options[] = '<option value="' . $theme . '"' . $sel . '>' . str_replace('_', ' ', $theme) . '</option>';
            }
            if (count($options) > 2) {
                $cthemes = implode("\n", $options);
                echo '
					<div class="control-group">
						<label for="theme">' . _SELECTTHEME . '</label>
						<select name="theme" id="theme">' . $cthemes . '</select>
					</div>';
            }
        }

        if (vkpYaIsUblockActive()) {
            $ublockdisplay = mxNL2BR(mxPrepareToHTMLDisplay($this->_user["ublock"]));
            $allowed = '&lt;' . implode('&gt; &lt;', mxGetAllowedHtml()) . '&gt;';
            if (empty($this->_user["ublock"])) {
                $this->_user["ublock"] = "";
            }
            $sel = (empty($this->_user["ublockon"])) ? '' : 'checked="checked"';
            echo '
				<div class="control-group">
					<label for="ublockon">' . _ACTIVATEPERSONAL . '</label>
					<input type="checkbox" name="ublockon" id="ublockon" ' . $sel . ' />
					' . _CHECKTHISOPTION . '
					<textarea cols="45" rows="14" name="ublock" id="ublockon">' . htmlspecialchars($this->_user['ublock'], ENT_QUOTES) . '</textarea>
					<div class="tiny">' . _ALLOWEDHTML . '&nbsp;' . $allowed . '</div>
					<table width="35%" class="list" style="margin: 1em auto 1em auto">
						<tr>
							<th>' . _MENUFOR . ' ' . $this->_user['uname'] . '</th>
						</tr>"
						<tr>
							<td>' . $ublockdisplay . '&nbsp;</td>
						</tr>
					</table>
				</div>';
        }

        $options = "";
        $storynum = (empty($this->_user["storynum"])) ? $GLOBALS['storyhome'] : (int)$this->_user["storynum"];
        for ($i = 1; $i <= 30; $i++) {
            $options .= "<option value=\"" . $i . "\"" . (($storynum == $i) ? 'selected="selected" class="current"' : "") . ">" . $i . "</option>\n";
        }

        echo '
			<div class="control-group">
				<label for="storynum">' . _NEWSINHOME . '</label>
				<select name="storynum" id="storynum">' . $options . '</select>
			</div>';

        if (mxModuleAllowed('UserGuest')) {
            $sel = (empty($this->_user['user_guestbook'])) ? '' : 'checked="checked"';
            echo '
				<div class="control-group">
					<label for="user_guestbook">' . _USERGUESTBOOK . '</label>
					<input type="checkbox" name="user_guestbook" id="user_guestbook" value="1" ' . $sel . ' />' . _ACTIVATEUSERGUESTBOOK . '
				</div>';
        }

        if ($userconfig->pm_poptime) {
            $user_pm_poptime = (empty($this->_user["user_pm_poptime"])) ? 0 : (int)$this->_user["user_pm_poptime"];
            $start = (ceil($userconfig->pm_poptime / 10)) * 10;
            $options = "<option value=\"0\"" . ((empty($user_pm_poptime)) ? ' selected="selected" class="current"' : '') . ">0 " . _YA_PMPOPTIME5 . "</option>\n";
            for ($i = $start; $i <= 600;) {
                $tv = ($i < 180) ? $i . " " . _YA_PMPOPTIME4 : ($i / 60) . " " . _YA_PMPOPTIME5;
                $options .= "<option value=\"" . $i . "\"" . (($user_pm_poptime == $i) ? ' selected="selected" class="current"' : '') . ">" . $tv . "</option>\n";
                $i = ($i < 180) ? $i + 10 : $i + 60;
            }

            echo '
				<div class="control-group">
					<label for="user_pm_poptime">' . _YA_PMPOPTIME1 . '</label>
					' . _YA_PMPOPTIME3 . ' 
					<select name="user_pm_poptime" id="user_pm_poptime>' . $options . '</select> 
					<span class="tiny">' . _YA_PMPOPTIME2 . '</span>
				</div>';
        }

        $content = ob_get_clean();

        /* Template initialisieren */
        $template = load_class('Template');
        $template->init_path(__FILE__);

        /* hier die Ausgabefelder angeben */
        $template->assign($this->_user);
        $template->assign('content', $content);

        /* Template auslesen */
        $content = $template->fetch('edithome.html');

        /* Userpage erstellen */
        $view = new pmxUserPage($this->_user);
        $view->innertab = 'edithome';
        $view->show($content);
    }

    /**
     * savehome()
     * Speichern der Benutzereinstellungen in der Datenbank
     *
     * @param array $pvs
     * @return
     */
    private function savehome($pvs)
    {
        global $user_prefix, $prefix;

        $userconfig = load_class('Userconfig');
        $themes = load_class("Config","pmx.themes");

        $pvs['storynum'] = intval($pvs['storynum']);
        $pvs['storynum'] = (empty($pvs['storynum'])) ? $GLOBALS['storyhome'] : $pvs['storynum'];
        $pvs['theme'] = (empty($pvs['theme']) || $pvs["theme"] == $themes->defaulttheme || !file_exists(PMX_THEMES_DIR . DS . $pvs['theme'] . DS . 'theme.php') || (!$userconfig->allowchangetheme && !MX_IS_ADMIN)) ? '' : $pvs['theme'];
        $pvs['user_pm_poptime'] = (empty($pvs['user_pm_poptime'])) ? 0 : (int)$pvs['user_pm_poptime'];
        if (mxModuleAllowed('UserGuest')) {
            $pvs['user_guestbook'] = (empty($pvs['user_guestbook'])) ? 0 : 1;
        }
        if (vkpYaIsUblockActive()) {
            $pvs['ublockon'] = (empty($pvs['ublockon'])) ? 0 : 1;
            $pvs['ublock'] = (empty($pvs['ublock'])) ? '' : $pvs['ublock'];
        } else {
            unset($pvs['ublockon']);
            unset($pvs['ublock']);
        }
        $uid = (int)$this->_user['uid'];

        $allowed = array('user_viewemail', 'storynum', 'theme', 'user_pm_poptime', 'user_guestbook', 'ublockon', 'ublock', 'user_lang');
        foreach($pvs as $key => $keyval) {
            if (in_array($key, $allowed)) {
                $fieldkeys[] = $key . "='" . mxAddSlashesForSQL($keyval) . "'";
            }
        }

        $fieldkeys = implode(', ', $fieldkeys);
        $qry = "update {$user_prefix}_users set " . $fieldkeys . " where uid=" . $uid . "";

        $result = sql_query($qry);
        if ($result) {
            $newinfo = array_merge($this->_user, $pvs);
            pmx_user_setlogin($newinfo, false); // ohne login-hook
            mxSessionSetVar('theme', $newinfo['theme']);
            mxSetCookie('theme', '', -1);
            if ($this->_user['user_lang'] != $newinfo['user_lang'] || mxSessionGetVar('lang') != $newinfo['user_lang']) {
                mxSessionSetVar('lang', $newinfo['user_lang']);
                $this->_setlang = '&newlang=' . $newinfo['user_lang'];
            }

            /* Modulspezifische Usereinstellungen durchfuehren */
            pmx_run_hook('user.edithome', $uid);
        } else {
            $saveerror = _SOMETHINGWRONG . ' (1)';
        }

        switch (true) {
            case isset($saveerror):
                return $this->redirect($saveerror);

            default:
                return $this->redirect(_YA_EDITDATAOK);
        }
    }

    /**
     * pmxUserEdit::redirect()
     *
     * @param mixed $message
     * @return
     */
    private function redirect($message)
    {
        // if ($message && $message != _YA_EDITDATAOK) {
        // mxSessionSetVar(self::session, $message);
        // }
        return mxRedirect('modules.php?name=Your_Account&op=edithome' . $this->_setlang, $message);
    }
}

?>