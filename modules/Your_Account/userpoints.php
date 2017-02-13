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

/**
 * ya_userpoints
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2010
 * @version $Id: userpoints.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class ya_userpoints {
    private $_userinfo = array();
    private $_config = array();

    private $_hours = 6; // sechs Stunden

    /**
     * ya_userpoints::__construct()
     *
     * @param mixed $userinfo
     */
    public function __construct($userinfo)
    {
        $this->_userinfo = $userinfo;
        $userconfig = load_class('Userconfig');
        $this->_config = $userconfig->get_config();
    }

    /**
     * ya_userpoints::get()
     *
     * @return
     */
    public function get()
    {
        global $prefix, $user_prefix;

        $reset = 3600 * $this->_hours; // sechs Stunden

        if (empty($this->_userinfo['uname']) || empty($this->_userinfo['uid'])) {
            return;
        }

        if (!$this->useuserpoints) {
            return;
        }

        $exusers = explode (',', $this->excludedusers);
        $exusers[] = $GLOBALS['anonymous'];
        $exusers[] = "Anonymous";
        foreach ($exusers as $key => $val) {
            if (strtolower($this->_userinfo['uname']) == strtolower(trim($val))) {
                sql_query("DELETE FROM ${prefix}_userpoints WHERE uid=" . intval($this->_userinfo['uid']));
                return;
            }
        }

        $result = sql_query("SELECT uid, punkte, updated FROM ${prefix}_userpoints WHERE uid=" . intval($this->_userinfo['uid']));
        list($already_registered, $old_points, $updated) = sql_fetch_row($result);

        $outdated = (($updated < (MX_TIME - $reset)) || !$updated);

        /* Wenn Daten noch aktuell und nicht der aktuelle Benutzer, aktuelle Daten zurückgeben */
        if ($already_registered && (!($this->_userinfo['current'] || $outdated))) {
            return self::_get_resulttext($old_points);
        }

        /* Modulspezifische Userpunkte abfragen */
        $hook = load_class('Hook', 'user.userpoints');
        $hook->userinfo = $this->_userinfo;
        $hook->userconfig = $this->_config;

        $userpoints = (int)$hook->get();

        switch (true) {
            case $already_registered && $outdated:
            case $already_registered && ($old_points != $userpoints):
                // nur aendern, wenn alt und neu unterschiedlich
                sql_query("UPDATE ${prefix}_userpoints SET punkte=" . intval($userpoints) . ", updated=" . intval(MX_TIME) . " WHERE uid=" . intval($this->_userinfo['uid']));
                break;
            case !$already_registered && $userpoints:
                sql_query("REPLACE INTO ${prefix}_userpoints (uid, punkte, updated) VALUES(" . intval($this->_userinfo['uid']) . ", " . intval($userpoints) . ", " . intval(MX_TIME) . ")");
                if (date('H') == 11) {
                    // Karteileichen löschen, nicht immer ausfuehren
                    $del = array();
                    $qry = "SELECT p.uid FROM ${prefix}_userpoints AS p
                        LEFT JOIN {$user_prefix}_users AS u ON p.uid = u.uid
                        WHERE ISNULL(u.uid);";
                    $result = sql_system_query($qry);
                    while (list($tmp) = sql_fetch_row($result)) {
                        $del[] = $tmp;
                    }
                    if ($del) {
                        sql_query("DELETE FROM ${prefix}_userpoints WHERE uid IN(" . implode(',', $del) . ")");
                    }
                }
                break;
        }

        return self::_get_resulttext($userpoints);
    }

    /**
     * ya_userpoints::_get_resulttext()
     *
     * @param mixed $userpoints
     * @return
     */
    private function _get_resulttext($userpoints)
    {
        switch ($userpoints) {
            case 0:
                return _YA_NOPOINTS;
            case 1:
                return _YA_YESPOINTS;
            default:
                return "<b>" . $userpoints . "</b> " . _YA_HASPOINTS;
        }
    }

    /**
     * ya_userpoints::__get()
     *
     * @param string $name
     * @return
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_config)) {
            return $this->_config[$name];
        }
        $trace = debug_backtrace();
        trigger_error('undefined property \'' . $name . '\' in ' . mx_strip_sysdirs($trace[0]['file']) . ' line ' . $trace[0]['line'], E_USER_NOTICE);
        return null;
    }
}

?>