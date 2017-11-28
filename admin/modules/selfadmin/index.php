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
MX_IS_ADMIN or die('access denied');

/**
 * self_admin
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2014
 * @version $Id: index.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class self_admin {
    private $_messages = array();
    private $_errors = array();
    private $_newpass = '';

    /**
     * self_admin::__construct()
     *
     * @param mixed $op
     */
    public function __construct($op)
    {
        /* Sprachdatei auswählen */
        mxGetLangfile(__DIR__);

        /* was tun? */
        switch ($op) {
            case PMX_MODULE . '/edit/save':
                return $this->edit_save();
            default:
                return $this->edit();
        }
    }

    /**
     * self_admin::edit()
     *
     * @return
     */
    private function edit($data = array())
    {
        $admindata = mxGetAdminData();
        $data = array_merge($this->_defaults(), $admindata, $data);

        $template = load_class('Template');
        $template->init_path(__FILE__);
        $template->assign($data);
        $template->assign('messages', $this->_messages);
        $template->assign('errors', $this->_errors);

        include ('header.php');
        $template->display('editSelfAdmin.html');
        include ('footer.php');
    }

    /**
     * self_admin::edit_save()
     *
     * @return
     */
    private function edit_save()
    {
        global $prefix, $adminmail, $sitename;

        if (empty($_POST)) {
            return $this->main();
        }

        $pvs = array_merge($this->_defaults(), $_POST);

        $admindata = mxGetAdminData();

        $this->_check_admin_data($pvs, 'edit');

        if ($this->_errors) {
            mxStripSlashes($pvs);
            return $this->edit($pvs);
        }

        $fields = $this->_get_qry_fields($pvs);
        $qry = "UPDATE `{$prefix}_authors` SET " . implode(', ', $fields) . " WHERE aid='" . mxAddSlashesForSQL($admindata['aid']) . "'";

        $result = sql_query($qry);
        if (!$result) {
            return mxErrorScreen(_UADBERROR, _CHANGESNOTOK, false);
        }

        if ($pvs['pwd'] && $this->_newpass) {
            $mailpass = $pvs['pwd'];
        } else {
            $mailpass = _UAKNOWNPASS;
        }

        $msg1 = _UANOTIFYADMIN . " " . $admindata['aid'] . " / " . $pvs['name'] . " \n " . _UAINFOEDITADMIN . " : \n\n" . _UANICKNAME . ": " . $pvs['name'] . "\n" . _UAEMAIL . ": " . $pvs['email'] . "\n" . _UAURL . ": " . $pvs['url'] . "\n" . _UAPASSWORD . ": $mailpass\n";
        mxMail($pvs['email'], $sitename . " : " . _UAINFOEDITADMIN , $msg1);
        $msg2 = _UANOTIFYADMIN . "\n " . $admindata['aid'] . " " . _UAINFOEDITADMIN02 . "\n\n" . _UANICKNAME . ": " . $pvs['name'] . "\n" . _UAEMAIL . ": " . $pvs['email'] . "\n" . _UAURL . ": " . $pvs['url'] . "\n";
        mxMail($adminmail, $admindata['aid'] . " : " . _UAINFOEDITADMIN , $msg2);

        /* Modulspezifische Useränderungen durchführen */
        if ($admindata['user_uid']) {
            pmx_run_hook('user.edit', $admindata['user_uid']);
        }

        /* wenn Passwort geändert, neu einloggen */
        pmx_admin_setlogin($admindata['aid'], $this->_newpass);

        return mxRedirect(adminUrl(PMX_MODULE), _CHANGESAREOK);
    }

    /**
     * self_admin::_defaults()
     *
     * @return
     */
    private function _defaults()
    {
        $defaults = array(/* Standardwerte */
            // 'aid' => '',
            'name' => '',
            'url' => '',
            'email' => '',
            'pwd' => '',
            'pwd2' => '',
            // 'counter' => 0,
            // 'user_uid' => 0,
            'pwd_salt' => '',
            // 'isgod' => 0,
            );
        return $defaults;
    }

    /**
     * self_admin::_check_admin_data()
     *
     * @param mixed $data
     * @return
     */
    private function _check_admin_data($data)
    {
        if (empty($data['email']) || empty($data['name'])) {
            $this->_errors[] = _UACOMPLETEFIELDS;
        }

        if (!mxCheckEmail($data['email'])) {
            $this->_errors[] = _UAINVEMAIL;
        }

        if (pmx_is_mail_banned($data['email'])) {
            $this->_errors[] = _MAILISBLOCKED;
        }

        if ((!empty($data['pwd'])) && ($data['pwd'] != $data['pwd2']) || (!empty($data['pwd2'])) && ($data['pwd'] != $data['pwd2'])) {
            $this->_errors[] = _UAPASSWDNOMATCH;
        }

        if (!empty($data['pwd'])) {
            $userconfig = load_class('Userconfig');
            if (strlen($data['pwd']) < $userconfig->minpass) {
                $this->_errors[] = sprintf(_UAYOUPASSMUSTBE, $userconfig->minpass);
            }
        }
    }

    /**
     * self_admin::_get_qry_fields()
     *
     * @param mixed $pvs
     * @return
     */
    private function _get_qry_fields($pvs)
    {
        global $prefix;

        if (!empty($pvs['pwd']) && !empty($pvs['pwd2'])) {
            $salt = pmx_password_salt();
            $pwd = pmx_password_hash($pvs['pwd'], $salt);
            $fields[] = "`pwd` = '" . mxAddSlashesForSQL($pwd) . "'";
            $fields[] = "`pwd_salt` = '" . mxAddSlashesForSQL($salt) . "'";
            $this->_newpass = $pwd;
        }

        $fields[] = "`name` = '" . mxAddSlashesForSQL($pvs['name']) . "'";
        $fields[] = "`url` = '" . mxAddSlashesForSQL(mx_urltohtml(mxCutHTTP($pvs['url']))) . "'";
        $fields[] = "`email` = '" . mxAddSlashesForSQL($pvs['email']) . "'";

        return $fields;
    }
}

$tmp = new self_admin($op);
$tmp = null;

?>