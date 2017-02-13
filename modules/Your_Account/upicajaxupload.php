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

load_class('Userpic', false);

/**
 * upic_ajax_upload
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2009
 * @version $Id: upicajaxupload.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class upic_ajax_upload extends pmxUserpic {
    protected $message = '';
    protected $redirect = '';
    protected $admin = false;

    protected static $current = null;

    /**
     * upic_ajax_upload::__construct()
     */
    public function __construct()
    {
        $this->module_name = basename(dirname(__FILE__));
        $this->message = _CHANGESAREOK;
        $this->admin = mxGetAdminPref('radminuser');

        mxgetlangfile($this->module_name);

        /* Das Charset ist erforderlich für JS-String-Vergleiche mit Sprachkonstanten !! */
        header('Content-type: text/html; charset=utf-8');

        switch (true) {
            case isset($_GET['delete']) && !empty($_GET['uid']) && is_numeric($_GET['uid']):
                parent::__construct($_GET['uid']);
                die(self::delete());
            case !isset($_FILES['upload']):
                die(_ACCESSDENIED);
            default:
                parent::__construct();
                die(self::handle_uploads());
        }
    }

    /**
     * upic_ajax_upload::handle_uploads()
     *
     * @return
     */
    private function handle_uploads()
    {
        $this->errors = array();

        /* Berechtigung prüfen */
        // TODO: Uploadberechtigt? Admin? !!!
        if (!MX_IS_USER) {
            return _ACCESSDENIED;
        }

        /* we instanciate the class for each element of $file */
        $handle = new pmxUserpic_upload($_FILES['upload']);
        $handle->set($this->config);
        $handle->handle($this->userdata['uid']);

        $logs = implode('<hr/>', $handle->logs);
        if ($handle->errors) {
            $this->errors += $handle->errors;
        }

        /* Wenn keine Fehler aufgetreten sind: */
        if ($handle->success) {
            /* E-Mail Funktion! */
            $this->adminmail();
        }

        switch (true) {
            case !$handle->success && !$this->errors:
                $message = '<div class="warning">' . _UPIC_UPLOAD1 . '</div>';
                break;
            case !$handle->success && $this->errors:
                $message = _UPIC_UPLOAD2;
                break;
            case $handle->success && $this->errors:
                $message = _UPIC_UPLOAD4;
                break;
            default:
                $attribs = array(/* HTML-Attribute */
                    // 'src' => $this->url_upload . '/' . basename($handle->success) . '?a' . rand(),
                    'src' => $handle->success . '?a' . rand(),
                    'alt' => 'uploaded userpic',
                    );
                $message = $this->_createpic($attribs, true);
        }

        if ($this->errors) {
            $message .= '<ul>';
            foreach ($this->errors as $msg) {
                $message .= '<li>' . $msg . '</li>';
            }
            $message .= '</ul>';
            $message = '
				<div class="alert alert-warning">' . $message . '</div>';
        }

        return $message;
    }

    /**
     * upic_ajax_upload::delete()
     *
     * @return
     */
    private function delete()
    {
        /* Berechtigung prüfen */
        // TODO: Uploadberechtigt? !!!
        switch (true) {
            case !MX_IS_USER && !$this->admin:
            case !$this->is_own_image() && !$this->admin:
                return _ACCESSDENIED;
        }

        $deleted = $this->delete_uploaded();
        if ($deleted) {
            return _UPIC_DELETED;
        } else {
            return _UPIC_DELETEERR;
        }
    }
}

$tmp = new upic_ajax_upload();
$tmp = null;

?>