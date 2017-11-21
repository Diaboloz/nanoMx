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
 * $Revision: 81 $
 * $Author: PragmaMx $
 * $Date: 2015-08-19 08:59:13 +0200 (Mi, 19. Aug 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');
MX_IS_ADMIN or die('access denied');

/**
 * pmx_authors_admin
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2014
 * @version $Id: index.php 81 2015-08-19 06:59:13Z PragmaMx $
 * @access public
 */
class pmx_authors_admin {
    private $_messages = array();
    private $_errors = array();
    private $_template;
    private $_tabs;
    private $_newpass = '';

    /**
     * pmx_authors_admin::__construct()
     *
     * @param mixed $op
     */
    public function __construct($op)
    {
        /* Sprachdatei auswählen */
        mxGetLangfile(__DIR__);

        $this->_tabs = array(/* Standardtabs */
            _AUTHORS_ENTRIES => adminUrl(PMX_MODULE),
            _ADDAUTHOR2 => adminUrl(PMX_MODULE, 'add'),
            );

        /* Template initialisieren */
        $this->_template = load_class('Template');
        $this->_template->init_path(__FILE__);

        /* was tun? */
        switch ($op) {
            case PMX_MODULE . '/add':
                return $this->add();

            case PMX_MODULE . '/add/save':
                return $this->add_save();

            case PMX_MODULE . '/edit':
                return $this->edit();

            case PMX_MODULE . '/edit/save':
                return $this->edit_save();

            case PMX_MODULE . '/delete':
                return $this->delete();

            case PMX_MODULE . '/delete/send':
                return $this->delete_send();

            case PMX_MODULE . '/delete/assignstories':
                return $this->delete_assignstories();

            default:
                return $this->main();
        }
    }

    /**
     * pmxSiteup_admin::_page()
     *
     * @param mixed $content
     * @return
     */
    function _page($content)
    {
        $GLOBALS['pagetitle'] = _AUTHORSADMIN;

        $this->_template->assign('content', $content);
        $this->_template->assign('messages', $this->_messages);
        $this->_template->assign('errors', $this->_errors);
        $this->_template->assign('tabs', $this->_tabs);

        include_once('header.php');
        $this->_template->display('navTabs.html');
        include_once('footer.php');
    }

    /**
     * pmx_authors_admin::main()
     *
     * @return
     */
    private function main()
    {
        global $prefix, $user_prefix;

        $qry = "SELECT a.isgod, a.aid, a.name, a.user_uid, u.uname, u.user_stat
            FROM ${prefix}_authors AS a
            LEFT JOIN {$user_prefix}_users AS u ON a.user_uid = u.uid;";
        $result = sql_query($qry);

        $rows = array();
        while ($row = sql_fetch_assoc($result)) {
            $rows[] = $row;
        }

        /* aktueller Tab */
        $this->_tabs[_AUTHORS_ENTRIES] = '';

        $this->_template->assign('rows', $rows);

        $content = $this->_template->fetch('listAdmin.html');
        return $this->_page($content);
    }

    /**
     * pmx_authors_admin::add()
     *
     * @return
     */
    private function add($data = array())
    {
        $data = array_merge($this->_defaults(), $data);

        $useroptions = $this->_get_userlist_options(0, '');

        $prefs = $this->_adminprefs();
        foreach ($prefs as $pref => $caption) {
            $adminprefs[$pref] = array($data[$pref], $caption);
        }

        /* aktueller Tab */
        $this->_tabs[_ADDAUTHOR2] = '';

        /* Daten für Formular */
        $this->_template->assign($data);
        $this->_template->assign('caption2', '<i class="fa fa-plus" aria-hidden="true"></i>&nbsp;' . _ADDAUTHOR2 . '');
        $this->_template->assign('action', 'add/save');
        $this->_template->assign('useroptions', $useroptions);
        $this->_template->assign('reqcheck', $this->_set_session_check());
        $this->_template->assign('adminprefs', $adminprefs);
        $form = $this->_template->fetch('formAdmin.html');
        /* Daten für Seite */
        $this->_template->assign('form', $form);
        $this->_template->assign('caption', _ADDAUTHOR);
        $content = $this->_template->fetch('addAdmin.html');
        return $this->_page($content);
    }

    /**
     * pmx_authors_admin::add_save()
     *
     * @return
     */
    private function add_save()
    {
        global $prefix;

        if (empty($_POST)) {
            return $this->main();
        }

        $pvs = array_merge($this->_defaults(), $_POST);

        /* Sessiongültigkeit prüfen */
        if (empty($pvs['check']) || $pvs['check'] != $this->_session_check()) {
            mxUserSecureLog('bad Admincreation', $pvs['aid'] . ' (no session-check-id)');
            return mxErrorScreen(_BADSESSIONREQUEST, _CHANGESNOTOK, false);
        }

        $this->_check_admin_data($pvs, 'add');

        if ($this->_errors) {
            mxStripSlashes($pvs);
            return $this->add($pvs);
        }

        $fields = $this->_get_qry_fields($pvs);

        $qry = "INSERT INTO `{$prefix}_authors` SET " . implode(', ', $fields);
        $result = sql_query($qry);
        if (!$result) {
            return mxErrorScreen(_DBERROR, _CHANGESNOTOK, false);
        }
        // if ($mode == 'edit') {
        // evtl. nur bei EDIT??
        $this->_run_user_hook_edit($pvs);
        // }
        return mxRedirect(adminUrl(PMX_MODULE), _CHANGESAREOK);
    }

    /**
     * pmx_authors_admin::edit()
     *
     * @return
     */
    private function edit($data = array())
    {
        global $prefix;

        /* sicherstellen, dass $aid richtig initialisiert ist */
        switch (true) {
            case !empty($data['aid']):
                $aid = $data['aid'];
                break;
            case !empty($_GET['aid']):
                $aid = $_GET['aid'];
                break;
            default:
                return $this->main();
        }

        $result = sql_query("SELECT * 
							FROM `{$prefix}_authors` 
							WHERE aid='" . mxAddSlashesForSQL($aid) . "'");
        $admindata = sql_fetch_assoc($result);

        $data = array_merge($this->_defaults(), $admindata, $data);

        $useroptions = $this->_get_userlist_options($data['user_uid'], $data['aid']);

        $prefs = $this->_adminprefs();
        foreach ($prefs as $pref => $caption) {
            $adminprefs[$pref] = array($data[$pref], $caption);
        }

        /* zusätzlichen aktuellen Tab */
        $this->_tabs[_MODIFYINFO] = '';
        /* Daten für Formular */
        $this->_template->assign($data);
        $this->_template->assign('caption2', '<i class="fa fa-check" aria-hidden="true"></i>&nbsp;' . _SAVE . '');
        $this->_template->assign('action', 'edit/save');
        $this->_template->assign('useroptions', $useroptions);
        $this->_template->assign('reqcheck', $this->_set_session_check());
        $this->_template->assign('adminprefs', $adminprefs);
        $form = $this->_template->fetch('formAdmin.html');
        /* Daten für Seite */
        $this->_template->assign('form', $form);
        $this->_template->assign('caption', _MODIFYINFO);
        $content = $this->_template->fetch('editAdmin.html');
        return $this->_page($content);
    }

    /**
     * pmx_authors_admin::edit_save()
     *
     * @return
     */
    private function edit_save()
    {
        global $prefix;

        if (empty($_POST)) {
            return $this->main();
        }

        $pvs = array_merge($this->_defaults(), $_POST);

        /* Sessiongültigkeit prüfen */
        if (empty($pvs['check']) || $pvs['check'] != $this->_session_check()) {
            mxUserSecureLog('bad Admincreation', '' . $pvs['aid'] . ' (no session-check-id)');
            return mxErrorScreen(_BADSESSIONREQUEST, _CHANGESNOTOK, false);
        }

        $this->_check_admin_data($pvs, 'edit');

        if ($this->_errors) {
            mxStripSlashes($pvs);
            return $this->edit($pvs);
        }

        $fields = $this->_get_qry_fields($pvs);
        $qry = "UPDATE `{$prefix}_authors` SET " . implode(', ', $fields) . " WHERE aid='" . mxAddSlashesForSQL($pvs['aid']) . "'";

        $result = sql_query($qry);
        if (!$result) {
            return mxErrorScreen(_DBERROR, _CHANGESNOTOK, false);
        }

        $this->_run_user_hook_edit($pvs);

        /* nur wenn Admin selbst, evtl. neu einloggen !!!! */
        $admindata = mxGetAdminData();
        if ($pvs['aid'] == $admindata['aid']) {
            /* wenn Passwort geändert, neu einloggen */
            if ($pvs['pwd'] && $this->_newpass) {
                /* Daten für das Login übergeben */
                pmx_admin_setlogin($admindata['aid'], $this->_newpass);
            }
        }

        return mxRedirect(adminUrl(PMX_MODULE, 'edit', 'aid=' . $pvs['aid']), _CHANGESAREOK);
    }

    /**
     * pmx_authors_admin::delete()
     *
     * @return
     */
    private function delete()
    {
        /* sicherstellen, dass $aid initialisiert ist */
        switch (true) {
            case !empty($_GET['aid']):
                $aid = $_GET['aid'];
                break;
            default:
                return $this->main();
        }

        /* zusätzlichen aktuellen Tab */
        $this->_tabs[_AUTHORDEL] = '';

        $this->_template->assign('aid', $aid);
        $this->_template->assign('part', 1);
        $this->_template->assign('reqcheck', $this->_set_session_check());

        $content = $this->_template->fetch('deleteAdmin.html');
        return $this->_page($content);
    }

    /**
     * pmx_authors_admin::delete_send()
     *
     * @param mixed $gvs
     * @return
     */
    private function delete_send()
    {
        global $prefix;

        /* sicherstellen, dass $aid initialisiert ist */
        switch (true) {
            case !isset($_POST['deleteyes']):
                // Nein gewählt
                return $this->main();
            case !empty($_POST['aid']):
                $aid = $_POST['aid'];
                break;
            default:
                return $this->main();
        }

        /* Beiträge veröffentlicht? */
        $result = sql_query("SELECT aid 
							FROM " . $prefix . "_stories 
							WHERE aid='" . mxAddSlashesForSQL($aid) . "' LIMIT 1");
        if (!sql_fetch_row($result)) {
            /* wenn keine Beiträge veröffentlicht, direkt weiter zum löschen */
            return $this->delete_action($aid, $_POST['check']);
        }

        $result = sql_query("SELECT aid 
							FROM `{$prefix}_authors` 
							WHERE aid <> '" . mxAddSlashesForSQL($aid) . "' 
							ORDER BY aid");
        $items = array();
        while (list($oaid) = sql_fetch_row($result)) {
            $items[] = $oaid;
        }

        /* zusätzlichen aktuellen Tab */
        $this->_tabs[_AUTHORDEL] = '';

        $this->_template->assign('aid', $aid);
        $this->_template->assign('items', $items);
        $this->_template->assign('part', 2);
        $this->_template->assign('reqcheck', $_POST['check']);

        $content = $this->_template->fetch('deleteAdmin.html');
        return $this->_page($content);
    }

    /**
     * pmx_authors_admin::delete_assignstories()
     *
     * @param mixed $pvs
     * @return
     */
    private function delete_assignstories()
    {
        global $prefix;

        switch (true) {
            case !isset($_POST['deleteyes']): // Abbrechen gewählt
            case empty($_POST['aid']): // keine neue aid
            case empty($_POST['aid']): // keine neue aid
            case empty($_POST['check']): // keine check id
                return $this->main();

                /* Sessiongültigkeit prüfen */
            case $_POST['check'] == $this->_session_check(false): // false = sessionvar nicht löschen!!
                $qry2 = "UPDATE " . $prefix . "_stories SET aid='" . mxAddSlashesForSQL($_POST['newaid']) . "' WHERE aid='" . mxAddSlashesForSQL($_POST['aid']) . "'";
                sql_query($qry2);
        }

        return $this->delete_action($_POST['aid'], $_POST['check']);
    }

    /**
     * pmx_authors_admin::delete_action()
     *
     * @param mixed $aid
     * @param mixed $check
     * @return
     */
    private function delete_action($aid, $check)
    {
        global $prefix;

        /* Sessiongültigkeit prüfen */
        if ($check != $this->_session_check()) {
            mxUserSecureLog('bad Admindelete', '' . $aid . ' (no session-check-id)');
            return mxErrorScreen(_BADSESSIONREQUEST, _CHANGESNOTOK, false);
        }

        $uid = 0;
        $result = sql_query("SELECT user_uid 
							FROM `{$prefix}_authors` 
							WHERE aid='" . $aid . "'");
        list($uid) = sql_fetch_row($result);
        if ($uid) {
            /* Modulspezifische Useränderungen durchführen */
            pmx_run_hook('user.edit', $uid);
        }

        sql_query("DELETE FROM `{$prefix}_authors` WHERE aid='" . mxAddSlashesForSQL($aid) . "'");
        return mxRedirect(adminUrl(PMX_MODULE));
    }

    /**
     * pmx_authors_admin::_insert_new_user()
     *
     * @param mixed $aid
     * @param mixed $pwd
     * @param mixed $pwd_salt
     * @param mixed $url
     * @param mixed $email
     * @return
     */
    private function _insert_new_user($aid, $name, $pwd, $pwd_salt, $email, $url)
    {
        global $prefix, $user_prefix;
        $qry = "SELECT uid 
				FROM {$user_prefix}_users 
				WHERE uname='" . mxAddSlashesForSQL($aid) . "'";
        $result = sql_query($qry);
        list($uid) = sql_fetch_row($result);
        if (empty($uid)) {
            $userconfig = load_class('Userconfig');

            $fields[] = "uname='" . mxAddSlashesForSQL($aid) . "'";
            $fields[] = "name='" . mxAddSlashesForSQL($name) . "'";
            $fields[] = "email='" . mxAddSlashesForSQL($email) . "'";
            $fields[] = "url='" . mxAddSlashesForSQL(mx_urltohtml(mxCutHTTP($url))) . "'";
            $fields[] = "user_regdate=''"; //" . mxGetNukeUserregdate() . "
            $fields[] = "pass='" . mxAddSlashesForSQL($pwd) . "'";
            $fields[] = "pass_salt='" . mxAddSlashesForSQL($pwd_salt) . "'";
            $fields[] = "user_ingroup=" . intval($userconfig->default_group);
            $fields[] = "user_regtime=" . time() . "";
            $fields[] = "user_stat=1";

            $qry = "INSERT INTO {$user_prefix}_users SET " . implode(', ', $fields);

            $result = sql_query($qry);
            $uid = sql_insert_id();
            if ($uid) {
                /* Modulspezifische Useranfügungen durchführen */
                pmx_run_hook('user.add', $uid);
            }
        }

        return intval($uid);
    }

    /**
     * pmx_authors_admin::_get_userlist_options()
     * Erzeugt eine Liste der User für die Admineditmaske
     *
     * @param integer $admin_uid
     * @param string $aid
     * @return
     */
    private function _get_userlist_options($admin_uid, $aid)
    {
        global $user_prefix, $prefix;
        $qry = "SELECT uid, uname 
				FROM {$user_prefix}_users 
				WHERE user_stat=1 ORDER BY uname";
        $result = sql_query($qry);
        $sel = (empty($admin_uid) && empty($aid)) ? 'selected="selected" class="current"' : '';
        $liste = "<option value=\"0\" " . $sel . ">&nbsp;&nbsp;" . _UANOBODY . "</option>\n";
        $liste .= "<option value=\"-1\">&nbsp;&nbsp;" . _UAADDUSER . "</option>\n";
        while (list($uid, $uname) = sql_fetch_row($result)) {
            $sel = ($admin_uid == $uid || (empty($admin_uid) && $aid == $uname)) ? 'selected="selected" class="current"' : '';
            $liste .= "<option value=\"" . $uid . "\" " . $sel . ">" . $uname . "</option>\n";
        }
        return $liste;
    }

    /**
     * pmx_authors_admin::_defaults()
     *
     * @return
     */
    private function _defaults()
    {
        $defaults = array(/* Standardwerte */
            'aid' => '',
            'name' => '',
            'url' => '',
            'email' => '',
            'pwd' => '',
            'pwd2' => '',
            // 'counter' => 0,
            'user_uid' => 0,
            'pwd_salt' => '',
            'isgod' => 0,
            );

        $prefs = $this->_adminprefs();
        foreach ($prefs as $key => $caption) {
            $defaults[$key] = 0;
        }

        return $defaults;
    }

    /**
     * pmx_authors_admin::_adminprefs()
     *
     * @return
     */
    private function _adminprefs()
    {
        static $adminprefs = array();

        if (empty($adminprefs)) {
            $adminprefs['radminsuper'] = _SUPERUSER;
            $adminprefs['radminuser'] = _USERS;
            $adminprefs['radmingroups'] = _USERGROUPS;
            defined('_BBFORUMS') AND $adminprefs['radminforum'] = _BBFORUMS;
            defined('_CONTENT') AND $adminprefs['radmincontent'] = _CONTENT;
            defined('_ARTICLES') AND $adminprefs['radminarticle'] = _ARTICLES;
            defined('_TOPICS') AND $adminprefs['radmintopic'] = _TOPICS;
            defined('_SECTIONS') AND $adminprefs['radminsection'] = _SECTIONS;
            defined('_ENCYCLOPEDIA') AND $adminprefs['radminency'] = _ENCYCLOPEDIA;
            defined('_SURVEYS') AND $adminprefs['radminsurvey'] = _SURVEYS;
            defined('_CALENDARADMIN') AND $adminprefs['radmincalendar'] = _CALENDARADMIN;
            defined('_EPHEMERIDS') AND $adminprefs['radminephem'] = _EPHEMERIDS;
            defined('_FAQ') AND $adminprefs['radminfaq'] = _FAQ;
            defined('_WEBLINKS') AND $adminprefs['radminlink'] = _WEBLINKS;
            defined('_DOWNLOADS') AND $adminprefs['radmindownload'] = _DOWNLOADS;
            defined('_REVIEWS') AND $adminprefs['radminreviews'] = _REVIEWS;
            defined('_NEWSLETTER') AND $adminprefs['radminnewsletter'] = _NEWSLETTER;
        }

        return $adminprefs;
    }

    /**
     * pmx_authors_admin::_check_admin_data()
     *
     * @param mixed $data
     * @return
     */
    private function _check_admin_data($data, $mode)
    {
        global $user_prefix, $prefix;

        if ($mode == 'add') {
            $err = mxCheckNickname($data['aid']);
            if ($err !== true) {
                $this->_errors[] = _ERRORINVNICKNAME;
                $this->_errors[] = $err;
            }
        }

        if (empty($data['email']) || empty($data['name'])) {
            $this->_errors[] = _COMPLETEFIELDS;
        }

        if (!mxCheckEmail($data['email'])) {
            $this->_errors[] = _UAINVEMAIL;
        }

        if (pmx_is_mail_banned($data['email'])) {
            $this->_errors[] = _MAILISBLOCKED;
        }

        if ((!empty($data['pwd'])) && ($data['pwd'] != $data['pwd2']) || (!empty($data['pwd2'])) && ($data['pwd'] != $data['pwd2'])) {
            $this->_errors[] = _PASSWDNOMATCH;
        }

        if ($mode == 'add') {
            if (!($this->_errors)) {
                /* prüfen ob neuer Adminname bereits vorhanden */
                $result = sql_query("SELECT aid 
									FROM `{$prefix}_authors` 
									WHERE aid='" . mxAddSlashesForSQL($data['aid']) . "'");
                list($xaid) = sql_fetch_row($result);
                if ($xaid) {
                    $this->_errors[] = _AUTHOREXISTINDB . '(' . $data['aid'] . ')';
                }
            }
        }
    }

    /**
     * pmx_authors_admin::_get_qry_fields()
     *
     * @param mixed $pvs
     * @return
     */
    private function _get_qry_fields($pvs)
    {
        global $prefix;

        /* Standardfelder */
        $fields[] = "`aid` = '" . mxAddSlashesForSQL($pvs['aid']) . "'";
        $fields[] = "`name` = '" . mxAddSlashesForSQL($pvs['name']) . "'";
        $fields[] = "`url` = '" . mxAddSlashesForSQL(mx_urltohtml(mxCutHTTP($pvs['url']))) . "'";
        $fields[] = "`email` = '" . mxAddSlashesForSQL($pvs['email']) . "'";

        /* Berechtigungen */
        $prefs = $this->_adminprefs();
        if ($pvs['radminsuper']) {
            array_shift($prefs);
            $fields[] = "`radminsuper` = 1";
            foreach ($prefs as $pref => $caption) {
                $fields[] = "`$pref` = 0";
            }
        } else {
            foreach ($prefs as $pref => $caption) {
                $fields[] = "`$pref` = " . intval($pvs[$pref]);
            }
        }

        /* Passwort */
        if (!empty($pvs['pwd']) && !empty($pvs['pwd2'])) {
            $salt = pmx_password_salt();
            $pwd = pmx_password_hash($pvs['pwd'], $salt);
            $fields[] = "`pwd` = '" . mxAddSlashesForSQL($pwd) . "'";
            $fields[] = "`pwd_salt` = '" . mxAddSlashesForSQL($salt) . "'";
            $this->_newpass = $pwd;
        }

        /* zugehöriger User / Autologin */
        $user_uid = intval($pvs['autologin']);
        /* wenn neuer User automatisch angelegt werden soll */
        if ($user_uid == -1) {
            /* wenn das Passwort nicht geänert wurde, versuchen das alte Passwort des Admins auszulesen */
            if (!$this->_newpass) {
                $pwd = '';
                $salt = '';
                $result = sql_query("SELECT pwd, pwd_salt 
									FROM `{$prefix}_authors` 
									WHERE aid='" . mxAddSlashesForSQL($pvs['aid']) . "'");
                list($pwd, $salt) = sql_fetch_row($result);
            }
            /* neuen User in Usertabelle anlegen */
            $user_uid = $this->_insert_new_user($pvs['aid'], $pvs['name'], $pwd, $salt, $pvs['email'], $pvs['url']);
        }
        $user_uid = (empty($user_uid)) ? 'NULL' : $user_uid;
        $fields[] = "`user_uid` = " . $user_uid;

        return $fields;
    }

    /**
     * pmx_authors_admin::_run_user_hook_edit()
     *
     * @param mixed $pvs
     * @return
     */
    private function _run_user_hook_edit($pvs)
    {
        global $prefix;

        $uid = 0;
        $result = sql_query("SELECT user_uid 
							FROM `{$prefix}_authors` 
							WHERE aid='" . mxAddSlashesForSQL($pvs['aid']) . "'");
        list($uid) = sql_fetch_row($result);
        if ($uid) {
            /* Modulspezifische Useränderungen durchführen */
            pmx_run_hook('user.edit', $uid);
        }
    }

    /**
     * pmx_authors_admin::_set_session_check()
     *
     * @return
     */
    private function _set_session_check()
    {
        mt_srand((double) microtime() * 1000000);
        $reqcheck = mt_rand();
        mxSessionSetVar('reqcheck', $reqcheck);
        return md5($reqcheck);
    }

    /**
     * pmx_authors_admin::_session_check()
     *
     * @param mixed $delcheck
     * @return
     */
    private function _session_check($delcheck = true)
    {
        /* Sessiongültigkeit prüfen */
        $check_sess = md5(mxSessionGetVar('reqcheck'));
        if ($delcheck) {
            // Standardmässig die Sessionvariable löechen
            mxSessionDelVar('reqcheck');
        }
        return $check_sess;
    }
}

$tmp = new pmx_authors_admin($op);
$tmp = null;

?>