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
 * $Revision: 130 $
 * $Author: PragmaMx $
 * $Date: 2016-04-26 12:20:38 +0200 (Di, 26. Apr 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * create_first_admin
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2014
 * @version $Id: index.php 130 2016-04-26 10:20:38Z PragmaMx $
 * @access public
 */
class create_first_admin {
    private $_messages = array();
    private $_errors = array();
    private $_template;
    private $_newpass = '';

    /**
     * create_first_admin::__construct()
     *
     * @param mixed $op
     */
    public function __construct($op)
    {
        /* Sprachdatei auswählen */
        mxGetLangfile(__DIR__);

        /* prüfen ob vielleicht doch schon ein Super-Account besteht */
        if (pmx_admin_exist_god()) {
            // wenn wirklich schon einer da ist...
            return mxRedirect(adminUrl(), _CFA_GODEXIST);
        }

        /* Template initialisieren */
        $this->_template = load_class('Template');
        $this->_template->init_path(__FILE__);

        /* was tun? */
        switch ($op) {
            case PMX_MODULE . '/add/save':
                return $this->add_save();
            default:
                return $this->add();
        }
    }

    /**
     * create_first_admin::add()
     *
     * @return
     */
    private function add($data = array())
    {
        $data = array_merge($this->_defaults(), $data);

        $this->_template->assign($data);
        $this->_template->assign('reqcheck', $this->_set_session_check());
        $this->_template->assign('messages', $this->_messages);
        $this->_template->assign('errors', $this->_errors);

        include ('header.php');
        $this->_template->display('add.html');
        include ('footer.php');
    }

    /**
     * create_first_admin::add_save()
     *
     * @return
     */
    private function add_save()
    {
        global $prefix;

        if (empty($_POST)) {
            return $this->add();
        }

        $pvs = array_merge($this->_defaults(), $_POST);

        // Sessiongültigkeit prüfen 
        if (empty($pvs['check']) || $pvs['check'] != $this->_session_check()) {
            mxUserSecureLog('bad Admincreation', '' . $pvs['aid'] . ' (no session-check-id)');
            return mxErrorScreen(_CFA_BADSESSIONREQUEST, _CHANGESNOTOK, false);
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
            return mxErrorScreen(_CFA_DBERROR, _CHANGESNOTOK, false);
        }

        /* eigentlich unnötig... */
        mxUserSecureLog('createfirst', 'OK - ' . $pvs['aid']);

        /* Daten für das Login übergeben */
        pmx_admin_setlogin($pvs['aid'], $this->_newpass);

        /* Erfolgsnachricht */
        $msg = "<b>" . _CFA_GODCREATEOK . "</b><br /><br /><b>" . _CFA_ASADMIN . "</b> " . $pvs['aid'];
        if ($pvs['createuser']) {
            $msg .= "<br /><b>" . _CFA_ASUSER . "</b> " . $pvs['aid'];
        }

        /* und ab zum Adminmenü :-)) */
        return mxRedirect(adminUrl('settings'), $msg); // zum Login
    }

    /**
     * create_first_admin::_insert_new_user()
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
        $qry = "SELECT uid FROM {$user_prefix}_users WHERE uname='" . mxAddSlashesForSQL($aid) . "'";
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
 
			$qry = "SELECT uid FROM {$user_prefix}_users WHERE uname='" . mxAddSlashesForSQL($aid) . "'";
			$result = sql_query($qry);
			list($uid) = sql_fetch_row($result);
            if ($uid) {
                /* Modulspezifische Useranfügungen durchführen */
				$useruid=$uid;
                pmx_run_hook('user.add', $useruid);
            }
        }

        return intval($uid);
    }

    /**
     * create_first_admin::_defaults()
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
            // 'user_uid' => 0,
            'pwd_salt' => '',
            'isgod' => 1,
            /* nur für das Formular */
            'dbpass' => '',
            'createuser' => 1,
            );
        return $defaults;
    }

    /**
     * create_first_admin::_check_admin_data()
     *
     * @param mixed $data
     * @return
     */
    private function _check_admin_data($data)
    {
        global $user_prefix, $prefix;

        $err = mxCheckNickname($data['aid']);
        if ($err !== true) {
            $this->_errors[] = _CFA_INVNICKNAME;
            $this->_errors[] = $err;
        }

        if (empty($data['email']) || empty($data['name'])) {
            $this->_errors[] = _CFA_COMPLETEFIELDS;
        }

        if (!mxCheckEmail($data['email'])) {
            $this->_errors[] = _CFA_INVEMAIL;
        }

        if ((!empty($data['pwd'])) && ($data['pwd'] != $data['pwd2']) || (!empty($data['pwd2'])) && ($data['pwd'] != $data['pwd2'])) {
            $this->_errors[] = _CFA_PASSWDNOMATCH;
        }

        if (!($this->_errors)) {
            /* prüfen ob neuer Adminname bereits vorhanden */
            $result = sql_query("SELECT aid FROM `{$prefix}_authors` WHERE aid='" . mxAddSlashesForSQL($data['aid']) . "'");
            list($xaid) = sql_fetch_row($result);
            if ($xaid) {
                $this->_errors[] = _CFA_AUTHOREXISTINDB . '(' . $data['aid'] . ')';
            }
        }

        if ($data['dbpass'] != $GLOBALS['dbpass']) {
            // Datenbankpasswort zur Sicherheit überprüfen
            // wer das nicht kennt, kann auch kein Super-Admin sein
            mxUserSecureLog('bad Admincreation', 'failed (bad dbPass) - ' . $data['aid']);
            $this->_errors[] = _CFA_ERRDBPASS;
        }
    }

    /**
     * create_first_admin::_get_qry_fields()
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

        /* Berechtigungen, immer System-Admin ;-) */
        $fields[] = "`radminsuper` = 1";
        $fields[] = "`isgod` = 1";

        /* Passwort */
        if (!empty($pvs['pwd']) && !empty($pvs['pwd2'])) {
            $salt = pmx_password_salt();
            $pwd = pmx_password_hash($pvs['pwd'], $salt);
            $fields[] = "`pwd` = '" . mxAddSlashesForSQL($pwd) . "'";
            $fields[] = "`pwd_salt` = '" . mxAddSlashesForSQL($salt) . "'";
            $this->_newpass = $pwd;
        }

        /* Soll ein normaler User miterstellt werden? */
        $user_uid = 0;
        if ($this->_newpass && $pvs['createuser']) {
            /* neuen User in Usertabelle anlegen */
            $user_uid = $this->_insert_new_user($pvs['aid'], $pvs['name'], $pwd, $salt, $pvs['email'], $pvs['url']);
        }
        $user_uid = (empty($user_uid)) ? 'NULL' : $user_uid;
        $fields[] = "`user_uid` = " . $user_uid;

        return $fields;
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
            // Standardmässig die Sessionvariable lösechen
            mxSessionDelVar('reqcheck');
        }
        return $check_sess;
    }
}

$tmp = new create_first_admin($op);
$tmp = null;

?>