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
 *
 * based on: Centerblock mxTabs 1.1 for pragmaMx 0.1.10
 * written by (c) 2008 Siggi Braunert, http://www.sb-websoft.com
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * pmxMultiblock
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2009
 * @version $Id: Multiblock.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxMultiblock {
    private $_message = '';

    private $_forbidden = array(/* nicht zugelassene Blöcke */
        'block-AdminAlert.php',
        'block-AdminNews.php',
        'block-vkp_News_Lastarticles.php',
        'block-vkp_News_Login.php',
        'block-vkp_News_Options.php',
        'block-vkp_News_Poll.php',
        'block-vkp_News_Rating.php',
        'block-vkp_News_Related.php',
        'block-Menu.php',
        'block-mxTabs_center.php',
        );

    private $_defaulttab = array(/* Grundinstallation */
        'bid' => 0,
        'title' => 'Hello',
        'content' => '<img src="images/logo.gif" alt="pragmaMx" />',
        'position' => '',
        'blockfile' => '',
        );

    private $_blockdata = array();

    private $_resource_dir;
    private $_template_dir;
    private $_tableprefix;
    private $_admin_link;

    /**
     * pmxMultiblock::__construct()
     *
     * @param mixed $block
     */
    function __construct($block)
    {
        global $prefix;

        $this->_tableprefix = $prefix;

        $this->_resource_dir = dirname(__FILE__) . DS . 'Multiblock' . DS;
        $this->_template_dir = PMX_LAYOUT_DIR . str_replace('/', DS, '/templates/includes/classes/Multiblock/');

        $this->_blockdata = $block;

        $this->_extend_blockdata();

        $parts = array(/* z.B.: &mxtabs=admin&bid=97#wraptabs-97 */
            'mxtabs' => 'admin',
            'bid' => $this->_blockdata['bid'],
            );
        $parts = array_merge($_GET, $parts);
        $this->_admin_link = $_SERVER['SCRIPT_NAME'] . '?' . http_build_query($parts, '', '&amp;') . '#wraptabs-' . $this->_blockdata['bid'];

        $this->_forbidden[] = $this->blockfile;

        if (isset($_POST['form_main_' . $this->bid])) {
            $this->_update_bids($_POST['form_main_' . $this->bid]);
        }
    }

    /**
     * pmxMultiblock::__get()
     *
     * @param string $name
     * @return
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_blockdata)) {
            return $this->_blockdata[$name];
        }
        $trace = debug_backtrace();
        trigger_error('undefined property \'' . $name . '\' in ' . mx_strip_sysdirs($trace[0]['file']) . ' line ' . $trace[0]['line'], E_USER_NOTICE);
        return null;
    }

    /**
     * pmxMultiblock::__set()
     *
     * @param string $name
     * @param mixed $value
     * @return
     */
    public function __set($name, $value)
    {
        $this->_blockdata[$name] = $value;
    }

    /**
     * pmxMultiblock::get()
     *
     * @param string $mode
     * @return
     */
    public function get($mode = 'tabs')
    {
        /* Templateausgabe erstellen */
        $tpl = load_class('Template');
        $tpl->init_path(__FILE__);
        $tpl->bid = $this->bid;
        $tpl->admin_link = $this->_admin_link;

        switch (true) {
            case $this->position != 'c' && $this->position != 'd':
                $this->_load_language();
                return $tpl->fetch('nocenterblock.html');

            case $mode == 'admin' && mxGetAdminPref('radminsuper'):
                $this->_load_language();
                $this->_load_jquery();
                $tpl->resource_dir = $this->_resource_dir;
                $tpl->message = $this->_message;
                $tpl->bids = $this->bids;
                $tpl->allblocks = $this->_load_blocks();
                return $tpl->fetch('admin.html');

            case $mode == 'cols':
                $tpl->tabcontent = $this->_get_tabcontent();
                return $tpl->fetch('view.cols.html');

            case $mode == 'tabs':
            // case $mode == 'accordion':
            default:
                $this->_load_jquery();
                $tpl->tabcontent = $this->_get_tabcontent();
                return $tpl->fetch('view.tabs.html');
        }
    }

    /**
     * pmxMultiblock::_extend_blockdata()
     *
     * @return
     */
    private function _extend_blockdata()
    {
        $pieces = explode('|', ($this->content) . '|');

        if (preg_match('#[^0-9, ]#', $pieces[0])) {
            // nur Zahlen, Komma und Leerzeichen erlaubt
            $this->bids = 0;
        } else {
            $this->bids = trim($pieces[0]);
        }

        if (empty($pieces[1])) {
            $this->conf = '';
        } else {
            $this->conf = trim($pieces[1]);
        }
    }

    /**
     * pmxMultiblock::_get_tabcontent()
     *
     * @return
     */
    private function _get_tabcontent()
    {
        $tmp = array();
        $ret = array();

        if ($this->bids) {
            $qry = "SELECT * FROM " . $this->_tableprefix . "_blocks WHERE bid IN(" . $this->bids . ") AND blockfile NOT IN('" . implode("','", $this->_forbidden) . "')";
            $result = sql_query($qry);
            while ($row = sql_fetch_assoc($result)) {
                $therow = mxGetBlockData($row);
                if (!empty($therow['content'])) {
                    $tmp[$row['bid']] = $therow;
                }
            }
            if ($tmp) {
                /* Array, entsprechend den Daten sortieren */
                $bids = explode(',', $this->bids);
                foreach ($bids as $key) {
                    if (isset($tmp[$key])) {
                        $ret[] = $tmp[$key];
                    }
                }
            }
        }

        if (!($ret)) {
            $ret[] = $this->_defaulttab;
        }
        return $ret;
    }

    /**
     * pmxMultiblock::_update_bids()
     *
     * @param mixed $f
     * @return
     */
    private function _update_bids($f)
    {
        $exist_bids = array();
        $allblocks = $this->_load_blocks();
        foreach($allblocks as $_block) {
            $exist_bids[] = $_block['bid'];
        }
        $set_bids = array_unique(preg_split('#[^0-9]+#', $f['bids']));
        $set_bids = array_intersect($set_bids, $exist_bids);
        $set_bids = implode(',', $set_bids);
        $content = $set_bids . "|" . $this->conf;

        $sql = "UPDATE " . $this->_tableprefix . "_blocks SET content = '" . mxAddSlashesForSQL($content) . "' WHERE bid=" . intval($this->bid);
        $result = sql_query($sql);
        if ($result && sql_affected_rows() > 0) {
            mxRedirect($this->_admin_link);
        }
    }

    /**
     * pmxMultiblock::_load_blocks()
     *
     * @return
     */
    private function _load_blocks()
    {
        $array = array();
        $sql = "SELECT * FROM " . $this->_tableprefix . "_blocks WHERE blockfile NOT IN('" . implode("','", $this->_forbidden) . "') ORDER BY title";
        $result = sql_query($sql);
        while ($block = sql_fetch_assoc($result)) {
            $block['blkpos'] = $this->_blkpos($block['position']);
            switch (true) {
                case $block['title']:
                    // Wenn der Titel als Sprachkonstante angegeben ist, diese verwenden
                    $block['title'] = mxTranslate($block['title']);
                    break;
                case !$block['title'] && $block['blockfile']:
                    $block['title'] = str_replace('.php', '', str_replace('block-', '', $block['blockfile']));
                    break;
                default:
                    $block['title'] = '(' . _NOTITLE . ')';
            }
            $array[] = $block;
        }
        return $array;
    }

    /**
     * pmxMultiblock::_blkpos()
     *
     * @param mixed $ch
     * @return
     */
    private function _blkpos($ch)
    {
        $this->_load_language();
        switch ($ch) {
            case "l" : return _MXTABS_LEFT;
            case "r" : return _MXTABS_RIGHT;
            case "c" : return _MXTABS_CENTERUP;
            case "d" : return _MXTABS_CENTERDOWN;
        }
    }

    /**
     * pmxMultiblock::_load_language()
     *
     * @return
     */
    private function _load_language()
    {
        if (!defined('_MXTABS_EDIT')) {
            mxGetLangfile($this->_resource_dir . 'language');
        }
    }

    /**
     * pmxMultiblock::_load_jquery()
     * Javascript & jquery for tabs
     *
     * @return
     */
    private function _load_jquery()
    {
        pmxHeader::add_tabs();
    }
}

?>