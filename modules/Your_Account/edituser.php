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
 * pmxUserEdit
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2010
 * @version $Id: edituser.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxUserEdit {
    const session = 'editusersession';
    private $_user = array();
    private $_userpic = false;

    /**
     * pmxUserEdit::__construct()
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
        $this->_user['url'] = mxCutHTTP($this->_user['url']);
        $this->_user['realname'] = $this->_user['name'];

        $this->_userpic = load_class('Userpic');

        switch ($op) {
            case 'saveuser':
                if (isset($_POST['pass'], $_POST['email'])) {
                    $this->saveuser($_POST);
                    break;
                }
            case 'edituser':
            default:
                $this->edituser();
        }
    }

    /**
     * pmxUserEdit::edituser()
     *
     * @return
     */
    private function edituser()
    {
        global $prefix;

        $messages = mxSessionGetVar(self::session);
        mxSessionDelVar(self::session);

        $picnotes = array();
        if ($this->_userpic->file_maxsize) {
            $picnotes[] = sprintf(_UPIC_MAX_KB_UPLOAD, $this->_userpic->file_maxsize / 1024);
        }
        $picnotes[] = sprintf(_UPIC_MAXPROPERTIES, $this->_userpic->width_full, $this->_userpic->height_full);
        $picnotes[] = _UPIC_AUTORESIZESIZING;

        $pic_allowed_upload = permission_granted($this->_userpic->access_upload, $this->_user['groups']);
        $pic_allowed_avatar = permission_granted($this->_userpic->access_avatars, $this->_user['groups']);

        /* Template initialisieren */
        $template = load_class('Template');
        $template->init_path(__FILE__);

        /* hier die Ausgabefelder angeben */
        $template->assign($this->_user);
        $template->assign('user', $this->_user);
        $template->assign('userpic', $this->_userpic);
        $template->assign(compact('messages', 'picnotes', 'pic_allowed_upload', 'pic_allowed_avatar'));
        /* Template auslesen */
        $content = $template->fetch('edituser.html');

        $view = new pmxUserPage($this->_user);
        $view->innertab = 'edituser';
        $view->show($content);
    }

    /**
     * pmxUserEdit::saveuser()
     *
     * @param mixed $pvs
     * @return
     */
    private function saveuser($pvs)
    {
        global $user_prefix, $prefix;

        $userconfig = load_class('Userconfig');

        $pvs = array_merge($this->_user, $pvs);
        $pvs = userCheck($pvs);
        switch (true) {
            case !is_array($pvs):
                return $this->redirect($pvs);
            case (!empty($pvs['pass'])) && ($pvs['pass'] != $pvs['vpass']):
            case (!empty($pvs['vpass'])) && ($pvs['pass'] != $pvs['vpass']):
                return $this->redirect(_PASSDIFFERENT);
            case (!empty($pvs['pass'])) && (strlen($pvs['pass']) < $userconfig->minpass):
                return $this->redirect(_YOUPASSMUSTBE . " <b>" . $userconfig->minpass . "</b> " . _CHARLONG);
        }
        $sqlvars = mxAddSlashesForSQL($pvs);
        extract($sqlvars);

        $setbday = (empty($birthday)) ? "NULL" : "'" . strftime('%Y-%m-%d', $birthday) . "'";

        unset($user_avatar);

        $pic_allowed_upload = permission_granted($this->_userpic->access_upload, $this->_user['groups']);
        $pic_allowed_avatar = permission_granted($this->_userpic->access_avatars, $this->_user['groups']);

        switch (true) {
            case !isset($pvs['upic-choice']):
                $user_avatar = '';
                break;

            case 'serverstored' === $pvs['upic-choice'] && $pic_allowed_avatar:
                $tmp = parse_url($pvs['foto-avatar']);
                switch (true) {
                    case empty($tmp['path']):
                    case isset($tmp['scheme']):
                    case isset($tmp['host']):
                        return $this->redirect(_UPIC_WRONGAVATARFILE);
                    case basename($tmp['path']) == 'blank.gif':
                        $user_avatar = '';
                        break;
                    case is_file($this->_userpic->path_avatars . DS . basename($tmp['path'])):
                        $user_avatar = $this->_userpic->path_avatars . '/' . basename($tmp['path']);
                }
                break;

            case 'uploaded' === $pvs['upic-choice'] && $pic_allowed_upload:
                $file = $this->_userpic->exist();
                if ($file && is_file($this->_userpic->path_upload . DS . basename($file))) {
                    $user_avatar = $file;
                } else {
                    return $this->redirect(_UPIC_NOUPLOADEDPIC);
                }
                break;

            case 'nopic' === $pvs['upic-choice']:
            default:
                $user_avatar = '';
                break;
        }

        /* wenn Userbildupload nicht erlaubt, evtl. vorhandene Bilder löschen */
        if (!$pic_allowed_upload && $this->_userpic->exist()) {
            $this->_userpic->delete_uploaded();
        }

        $_newpass = '';
        if (!empty($pvs['pass']) && !empty($pvs['vpass'])) {
            // nur wenn neues Paswort
            $salt = pmx_password_salt();
            $pass = pmx_password_hash($pvs['pass'], $salt);
            $fields[] = "`pass` = '" . mxAddSlashesForSQL($pass) . "'";
            $fields[] = "`pass_salt` = '" . mxAddSlashesForSQL($salt) . "'";
            $_newpass = $pass;
        }

        $fields[] = "`email` = '$email'";
        $fields[] = "`name` = '$realname'";
        $fields[] = "`user_sexus` =  $user_sexus";
        $fields[] = "`url` = '" . mx_urltohtml(mxCutHTTP($url)) . "'";
        $fields[] = "`user_avatar` = '$user_avatar'";
        $fields[] = "`user_occ` = '$user_occ'";
        $fields[] = "`user_from` = '$user_from'";
        $fields[] = "`bio` = '$bio' ";
        $fields[] = "`user_intrest` = '$user_intrest'";
        $fields[] = "`user_sig` = '$user_sig'";
        $fields[] = "`user_icq` = '$user_icq'";
        $fields[] = "`user_aim` = '$user_aim'";
        $fields[] = "`user_yim` = '$user_yim'";
        $fields[] = "`user_msnm` = '$user_msnm'";
        $fields[] = "`user_bday` =  $setbday";
        if (function_exists('saveuser_option')) {
            // loeschen oder hinzufuegen von Insert-Elementen
            $fields = saveuser_option($pvs, $fields);
        }

        $qry = "UPDATE {$user_prefix}_users SET " . implode(', ', $fields) . " WHERE uid=" . intval($this->_user['uid']);
        $result = sql_query($qry);
        if (!$result) {
            return $this->redirect(_SOMETHINGWRONG . " (1)");
        }

        /* nur wenn Passwort geändert, Session neu schreiben */
        if ($_newpass) {
            $this->_user['pass'] = $_newpass;
            pmx_user_setlogin($this->_user);
        }

        if (function_exists('saveuser_option_2')) {
            // weitere Operationen nach dem erfolgreichen Speichern der Daten
            saveuser_option_2($pvs);
        }

        /* Modulspezifische Userdatenänderungen durchfuehren */
        pmx_run_hook('user.edit', $this->_user['uid']);

        return $this->redirect(_YA_EDITUSEROK);
    }

    /**
     * pmxUserEdit::redirect()
     *
     * @param mixed $message
     * @return
     */
    private function redirect($message)
    {
        if ($message && $message != _YA_EDITUSEROK) {
            mxSessionSetVar(self::session, $message);
        }
        return mxRedirect('modules.php?name=Your_Account&op=edituser', $message);
    }
}

?>