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
 * Menumanager for pragmaMx, written by Siggi Braunert
 * bugfixed by Joerg & Andi
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * pmxMenu_model
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2009
 * @version $Id: model.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxMenu_model {
    protected $title = '';
    protected $prefix = '';
    // die aktuellen Request Parameter
    protected $request = false;

    protected $_current_link = 0;
    protected $_current_link_parents = array();

    private $_mainmod = array();
    private $_module = '';

    protected function __construct()
    {
        $this->prefix = $GLOBALS['prefix'];
        $this->_init_request();
    }

    public function get_menu_title_db($id)
    {
        $title = "";
        $result = sql_query("SELECT title FROM {$this->prefix}_blocks WHERE bid=" . intval($id));
        list($title) = sql_fetch_row($result);
        return $title;
    }

    public function read_menus_db($field = false)
    {
        switch (true) {
            case $field === false:
                $and = '';
                break;

            case is_numeric($field):
                $and = ' AND bid=' . intval($field);
                break;

            case is_string($field):
                $and = " AND title='" . mxAddSlashesForSQL($field) . "'";
                break;

            default:
                $and = '';
        }
        $content = array();
        $sql = "SELECT * FROM {$this->prefix}_blocks WHERE blockfile='block-Menu.php'" . $and . " ORDER BY title ASC";
        $result = sql_query($sql);
        while ($row = sql_fetch_assoc($result)) {
            $content[] = $row;
        }
        return $content;
    }

    public function getmenuid_by_name_db($blocktitle)
    {
        $menues = $this->read_menus_db($blocktitle);
        foreach ($menues as $value) {
            return $value['bid'];
        }
        return 0;
    }

    public function add_menu_db($title)
    {
        $result=sql_query("INSERT INTO {$this->prefix}_blocks (bid, bkey, title, content, url, position, weight, active, refresh, time, blanguage, blockfile, view)
    		                                 VALUES (NULL, '', '" . mxAddSlashesForSQL($title) . "', '', '', 'l', '1', '0', '0', '0', '', 'block-Menu.php', '0')");
        return sql_affected_rows($result);
    }

    public function update_menu_db($bid, $title)
    {
        $result=sql_query("UPDATE {$this->prefix}_blocks SET title = '" . mxAddSlashesForSQL($title) . "' WHERE bid=" . intval($bid));
        return sql_affected_rows($result);
    }

    public function change_menu_status_db($bid)
    {
        $result = sql_query("SELECT bid, title, active from {$this->prefix}_blocks WHERE bid=" . intval($bid));
        $row = sql_fetch_array($result);
        $active = ($row['active'] == 1) ? 0 : 1;
        sql_query("UPDATE {$this->prefix}_blocks SET active=" . intval($active) . " WHERE bid=" . intval($bid));
        $this->title = $row['title'];
        return $active;
    }

    public function delete_menu_db($id)
    {
        $result = sql_query("SELECT bid, blockfile FROM {$this->prefix}_blocks WHERE bid=" . intval($id));
        list($_id, $blockfile) = sql_fetch_row($result);
        $affected = 0;
        if ($blockfile == "block-Menu.php") {
            sql_query("DELETE FROM {$this->prefix}_blocks WHERE bid = " . intval($id));
            $affected = sql_affected_rows();
            sql_query("DELETE FROM {$this->prefix}_menu WHERE bid = " . intval($id));
        }
        return $affected;
    }

    public function get_bids_db($table = "menu")
    {
        $bids = array();
        $where = ($table == "blocks") ? "WHERE blockfile='block-Menu.php'" : "";
        $sql = "SELECT bid FROM {$this->prefix}_" . $table . " " . $where . "";
        $result = sql_query($sql);
        while ($row = sql_fetch_array($result)) {
            $bids[] = $row['bid'];
        }
        return $bids;
    }

    public function get_item_title_db($id)
    {
        $title = "";
        $result = sql_query("SELECT id, title FROM {$this->prefix}_menu WHERE id=" . intval($id));
        list($id, $title) = sql_fetch_row($result);
        return $title;
    }

    public function get_item_db($id, $by = "")
    {
        $row = array();
        if (empty($by)) {
            $by = "id";
        }
        $result = sql_query("SELECT * FROM {$this->prefix}_menu WHERE $by='" . mxAddSlashesForSQL($id) . "'");
        $row = sql_fetch_assoc($result);
        return $row;
    }

    public function get_item_childs_db($pid, $bid = 0)
    {
        $wherebid = ($bid) ? " AND bid=" . intval($bid) : '';
        $result = sql_query("SELECT * FROM {$this->prefix}_menu WHERE pid=" . intval($pid) . $wherebid . " ORDER BY weight,id ASC");
        $rows = array();
        while ($row = sql_fetch_assoc($result)) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function add_item_db($row)
    {
        $ok = sql_query("INSERT INTO {$this->prefix}_menu (
                        bid,
                        pid,
                        title,
                        description,
                        url,
                        weight,
                        target,
                        expanded
                    ) VALUES (
                        " . intval($row['bid']) . ",
                        " . intval($row['pid']) . ",
                        '" . mxAddSlashesForSQL($row['title']) . "',
                        '" . mxAddSlashesForSQL($row['description']) . "',
                        '" . mxAddSlashesForSQL($row['url']) . "',
                        " . intval($row['weight']) . ",
                        '" . mxAddSlashesForSQL($row['target']) . "',
                        " . intval($row['expanded']) . "
                    )");

        if ($ok) {
            $id = sql_insert_id();
            $qry = "UPDATE {$this->prefix}_menu SET weight = weight+1
                WHERE pid=" . intval($row['pid']) . "
                AND bid=" . intval($row['bid']) . "
                AND id<>" . intval($id) . "
                AND weight>=" . intval($row['weight']);
            sql_query($qry);

            $checkrows = $this->get_item_childs_db($row['pid'], $row['bid']);
            $this->check_weight($checkrows);
        }

        return $ok;
    }

    public function update_item_db($row)
    {
        $oldvals = $this->get_item_db($row['id']);

        $ok = sql_query("UPDATE {$this->prefix}_menu SET
                        bid = " . intval($row['bid']) . ",
                        pid = " . intval($row['pid']) . ",
                        title = '" . mxAddSlashesForSQL($row['title']) . "',
                        description = '" . mxAddSlashesForSQL($row['description']) . "',
                        url = '" . mxAddSlashesForSQL($row['url']) . "',
                        weight = " . intval($row['weight']) . ",
                        target = '" . mxAddSlashesForSQL($row['target']) . "',
                        expanded = " . intval($row['expanded']) . "
                    WHERE id = " . intval($row['id']));

        if ($ok) {
            /* Prüfen ob Untermenüs mit verschoben werden müssen */
            if ($oldvals['bid'] != $row['bid']) {
                $this->check_childrens($row['bid'], $row['id']);
            }

            /* Reihenfolge der items auf der gleichen Ebene anpassen */
            if ($oldvals['weight'] != $row['weight']) {
                $qry = "UPDATE {$this->prefix}_menu SET weight = weight+1
                    WHERE pid=" . intval($row['pid']) . "
                     AND id<>" . intval($row['id']) . "
                     AND weight>=" . intval($row['weight']);
                sql_query($qry);

                $checkrows = $this->get_item_childs_db($row['pid'], $row['bid']);
                $this->check_weight($checkrows);
            }
        }

        return $ok;
    }

    public function check_childrens($bid, $id)
    {
        $childrens = $this->get_item_childs_db($id);
        if (!$childrens) {
            return;
        }

        $updated = false;
        foreach ($childrens as $child) {
            if (false === $updated && $child['bid'] != $bid) {
                // falls item in anderen Block verschoben wird alle childs mitnehmen, da sonst die childs
                // auf einen parent mit anderer Blockzugehoerigkeit verweisen wuerden
                $qry = "UPDATE {$this->prefix}_menu SET bid = " . intval($bid) . " WHERE pid = " . intval($id);
                $updated = sql_system_query($qry);
            }
            /* hat das child auch childs? */
            $this->check_childrens($bid, $child['id']);
        }
    }

    public function check_weight(&$rows)
    {
        $cnt = count($rows);
        switch (true) {
            case !$cnt:
                return;
            case $rows[0]['weight'] != 1:
            case $rows[$cnt-1]['weight'] != $cnt:
                break;
            default:
                return;
        }

        foreach ($rows as $key => $row) {
            $newval = $key + 1;
            if ($rows[$key]['weight'] != $newval) {
                $qry = "UPDATE {$this->prefix}_menu SET weight = " . intval($newval) . " WHERE id = " . intval($row['id']);
                sql_system_query($qry);
                $rows[$key]['weight'] = $newval;
            }
        }
    }

    /* ---------------- functions to handle links from the modules ----|begin|----------------- */

    public function read_modules_db()
    {
        $modules = array();
        $sql = "SELECT * FROM {$this->prefix}_modules WHERE title<>'blank_Home' ORDER BY custom_title ASC";
        $result = sql_query($sql);
        while ($row = sql_fetch_assoc($result)) {
            $row['link'] = 'modules.php?name=' . $row['title'];
            $modules[] = $row;
        }
        return $modules;
    }

    public function get_module_db($mid)
    {
        $row = array();
        $result = sql_query("SELECT * FROM {$this->prefix}_modules WHERE mid=" . intval($mid));
        $row = sql_fetch_assoc($result);
        return $row;
    }

    /* ---------------- functions to handle links from the modules ----|end|----------------- */

    public function delete_item_db($id, $by = "id")
    {
        // first reset parent items to Zero and deactivate them
        $result = sql_query("UPDATE {$this->prefix}_menu SET pid = 0 WHERE pid = " . intval($id));
        // then delete item
        sql_query("DELETE FROM {$this->prefix}_menu WHERE $by = " . intval($id));

        return sql_affected_rows();
    }

    public function delete_abandoned_items_db()
    {
        // sucht alle items aus db menu nach bid's von einem in der BlockAdmin schon geloeschten Block
        // vergleicht sie mit den Eintraegen in den db blocks
        // und loescht diese gegebenenfalls auch aus der menu-tabelle
        $menu_bids = $this->get_bids_db('menu');
        $block_bids = $this->get_bids_db('blocks');
        $todelete = array_diff($menu_bids, $block_bids);
        if (count($todelete) > 0)
            foreach ($todelete as $id) {
            $this->delete_item_db($id, "bid");
        }
    }

    /**
     * theme_mx_menu::read_items_4menu_db()
     * treelist adds
     *
     * @return
     */
    public function read_items_4menu_db($bid)
    {
        if (!empty($_REQUEST['name']) && is_string($_REQUEST['name'])) {
            /* ich weiss, request prüfen ist doof, aber MX_Module etc. ist zu komplex für diesen Anwendungsfall */
            $this->_module = $_REQUEST['name'];
        } else {
            $this->_module = basename($_SERVER['PHP_SELF']);
        }

        $sql = "SELECT id, pid, title, description, url, expanded, target FROM {$this->prefix}_menu WHERE bid=" . intval($bid) . " ORDER BY pid,weight,id ASC";
        $result = sql_system_query($sql);
        $arr = array();
        while ($row = sql_fetch_assoc($result)) {
            $row['title'] = mxTranslate($row['title']); # . ' (' . $row['id'] . ')';
            $row['description'] = mxTranslate($row['description']);
            $row['item'] = intval($bid) . '-' . intval($row['pid']) . '-' . intval($row['id']);
            $row['allowed'] = true;
            $row['disabled'] = false;
            $row['current'] = false;
            $row['currents_parent'] = false;
            $row['sub'] = array();

            $row = $this->_modprop($row);

            if (isset($arr[$row['id']])) {
                // falls sub vom Parent bereits existiert
                $arr[$row['id']] = array_merge($row, $arr[$row['id']]);
            } else {
                $arr[$row['id']] = $row;
            }

            /* diesen Datensatz als sub von seinem Parent */
            $arr[intval($row['pid'])]['sub'][] = $row['id'];
        }

        if (!$this->_current_link && isset($this->_mainmod[$this->_module])) {
            $this->_current_link = $this->_mainmod[$this->_module];
        }

        if ($this->_current_link) {
            /* den aktuellen Link auch im Array kennzeichnen */
            $arr[$this->_current_link]['current'] = true;
            /* Array mit allen Parents des aktuellen Links */
            $this->_set_current_parents($arr, $this->_current_link);
        }

        foreach ($this->_current_link_parents as $id) {
            $arr[$id]['expanded'] = true;
            $arr[$id]['currents_parent'] = true;
        }

        ksort($arr);
        return $arr;
    }

    /**
     * theme_mx_menu::read_items_4admin_menu()
     * treelist adds
     *
     * @return
     */
    public function read_items_4admin_menu()
    {
        $defaults = array(/* Standardwerte für alle Items */
            'url' => '',
            'title' => '',
            'image' => '',
            'panelname' => '',
            'class' => '',
            'id' => 0,
            'pid' => 0,
            'bid' => 0,
            'weight' => 0,
            'target' => '',
            'expanded' => false,
            'description' => '',
            'item' => '',
            'allowed' => true,
            'disabled' => false,
            'current' => false,
            'currents_parent' => false,
            'sub' => array(),
            'links' => array(),
            );

        /* aktuellen Request und URL-Parameter ermitteln */
        switch (true) {
            case defined('PMX_MODULE'):
                /* wenn Modulname eindeutig erkennbar, diesen verwenden */
                $curentmodule = PMX_MODULE;
                break;
            case defined('PMX_ADMIN_OP'):
                $curentmodule = PMX_ADMIN_OP;
                break;
            case !empty($_REQUEST['op']):
                $curentmodule = $_REQUEST['op'];
                break;
            default:
                $curentmodule = 'main';
        }

        $menu = load_class('Adminmenue');
        $items = $menu->fetch();

        /* Schleife durch die Hauptmenüpunkte (Tabs) */
        foreach ($items as $key => $row) {
            if (!$row['links']) {
                // wenn keine Menüpunkte für diesen Tab vorhanden,
                // einfach weiter, Tab wird dann nicht angezeigt
                continue;
            }
            $row['id'] = $key;
            $row['weight'] = $key;
            $row['item'] = '00-0-' . $key;
            $row = array_merge($defaults, $row);
            if (isset($arr[$key])) {
                // falls sub vom Parent bereits existiert
                $arr[$key] = array_merge($row, $arr[$key]);
            } else {
                $arr[$key] = $row;
            }

            /* diesen Datensatz als sub von seinem Parent */
            $arr[$row['pid']]['sub'][] = $key;

            /* Schleife durch die Untermenüpunkte (nur eine Ebene) */
            $i = 0;
            foreach ($row['links'] as $subrow) {
                $i++;
                $id = ($key * 1000) + $i;
                $subrow['id'] = $id;
                $subrow['pid'] = $key;
                $subrow['weight'] = $id;
                $subrow['item'] = '00-' . $key . '-' . $id;
                $subrow = array_merge($defaults, $subrow);

                if ($curentmodule == $subrow['module']) {
                    $this->_current_link = $id;
                }

                if (isset($arr[$id])) {
                    // falls sub vom Parent bereits existiert
                    $arr[$id] = array_merge($subrow, $arr[$id]);
                } else {
                    $arr[$id] = $subrow;
                }

                /* diesen Datensatz als sub von seinem Parent */
                $arr[$key]['sub'][] = $id;
            }
        }

        if ($this->_current_link) {
            /* den aktuellen Link auch im Array kennzeichnen */
            $arr[$this->_current_link]['current'] = true;
            /* Array mit allen Parents des aktuellen Links */
            $this->_set_current_parents($arr, $this->_current_link);
        }

        foreach ($this->_current_link_parents as $id) {
            $arr[$id]['expanded'] = true;
            $arr[$id]['currents_parent'] = true;
        }

        ksort($arr);

        return $arr;
    }

    public function read_items_4select_db($id , $bid = '', $who = 0, $ebene = '', &$arr)
    {
        $and = (empty($bid) ? "" : "AND bid=" . intval($bid) . " ");
        if (!isset($tmp)) {
            $tmp = array();
        }
        $sql = "SELECT * FROM {$this->prefix}_menu WHERE pid=" . intval($who) . " " . $and . " ORDER BY weight,id ASC";
        $result = sql_query ($sql);
        while ($parent = sql_fetch_array($result)) {
            $tmp['id'] = $parent['id'];
            $tmp['pid'] = $parent['pid'];
            $tmp['title'] = $ebene . " " . mxTranslate($parent['title']);
            if (!empty($id) && $tmp['pid'] == $id) {
                // falls gewaehlter Item Subitems hat, weiter, weil Subitems nicht als uebergeordnete menues fuer Items erscheinen koennen
                continue;
            }
            $arr[] = $tmp;
            $this->read_items_4select_db($id, $bid, $parent['id'], $ebene . '- ', $arr);
        }
        unset($tmp);
    }

    public function read_items_4showall_db($bid = '', $who = 0, $ebene = '', &$arrall)
    {
        $and = (empty($bid) ? "" : "AND bid=" . intval($bid) . " ");
        if (!isset($tmp)) {
            $tmp = array();
        }
        $sql = "SELECT * FROM {$this->prefix}_menu WHERE pid=" . intval($who) . " " . $and . " ORDER BY weight,id ASC";
        $result = sql_query ($sql);
        while ($parent = sql_fetch_array($result)) {
            $tmp['id'] = $parent['id'];
            $tmp['pid'] = $parent['pid'];
            $parent['title'] = mxTranslate($parent['title']);
            if ($who == 0) {
                $parent['title'] = '<strong>' . $parent['title'] . '</strong>';
            }
            $tmp['title'] = $ebene . " " . $parent['title'];
            $tmp['url'] = $parent['url'];
            $tmp['description'] = mxTranslate($parent['description']);
            $tmp['weight'] = $parent['weight'];
            $tmp['target'] = $parent['target'];
            $tmp['expanded'] = $parent['expanded'];

            $arrall[] = $tmp;
            $this->read_items_4showall_db($bid, $parent['id'], $ebene . "- ", $arrall);
        }
        unset($tmp);
    }

    protected function _init_request()
    {
        // if ($this->request !== false) {
        // return $this->request;
        // }
        /* aktuellen Request abfragen */
        $this->request = array();
        /* aktuelle Requestparameter ermitteln, zum Vergleich, ob Modul aktiv und ob im Menue enthalten */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            /* aus _REQUEST erstellen */
            foreach ($_REQUEST as $key => $value) {
                $this->request[] = "{$key}={$value}";
            }
        } else {
            /* aus _GET erstellen */
            $this->request = explode('&', str_ireplace('&amp;', '&', $_SERVER['QUERY_STRING']));
        }
        return $this->request;
    }

    /**
     * theme_mx_menu::_modprop()
     *
     * @param mixed $row
     * @return
     */
    private function _modprop($row)
    {
        $parsed = array('host' => '', 'path' => '', 'query' => '');
        pmxDebug::pause();
        $url = parse_url($row['url']);
        pmxDebug::restore();

        if (is_array($url)) {
            $url = array_merge($parsed, $url);
        } else {
            $url = $parsed;
        }

        if ($url['query']) {
            /* den Querystring in seine Einzelteile aufsplitten (variable=wert) */
            $urlparts = explode('&', str_replace('&amp;', '&', $url['query']));
            /* bei der Gelegenheit gleich den URL escapen ;) */
            $newquery = implode('&amp;', $urlparts);
            $row['url'] = str_replace($url['query'], $newquery, $row['url']);
        } else {
            $urlparts = array();
        }

        /* falls Serverpfad, und Host angegeben, aber normaler interner Link, dies umschreiben */
        if ($url['path'] && strpos($url['path'], PMX_BASE_PATH) === 0) {
            if (!$url['host'] || ($url['host'] == $_SERVER['SERVER_NAME'])) {
                // Pfad extrahieren, dbei auf Unterordner achten
                $url['path'] = substr_replace ($url['path'], '' , 0, strlen(PMX_BASE_PATH));
                $url['host'] = '';
            }
        }
        switch (true) {
            case $url['host']: // externer Link
            case $url === $parsed: // leeres array(), ungültige URL
                break;

            case (!$url['path']):
            case ($url['path'] == './'):
                if (defined('MX_HOME_FILE') && !$this->_current_link) {
                    $this->_current_link = $row['id'];
                }
                break;

            case ($url['query'] && ($url['path'] == 'modules.php' || $url['path'] == 'index.php')):

                $modul = '';
                foreach ($urlparts as $value) {
                    if (substr($value, 0, 5) === 'name=') {
                        $modul = substr($value, 5);
                        break;
                    }
                }

                if (!$modul) {
                    $row['allowed'] = false;
                    break;
                }

                $row['allowed'] = mxModuleAllowed($modul);

                switch (true) {
                    case $this->_current_link:
                    case empty($this->_module):
                    case $this->_module != $modul:
                        break 2; // beide switches beenden
                }

                if (count($urlparts) === 1) {
                    $this->_mainmod[$modul] = $row['id'];
                }

                if (!array_diff($this->request, $urlparts) && !array_diff($urlparts, $this->request)) {
                    $this->_current_link = $row['id'];
                }

                break;

            default:

                if ($url['path'] == adminUrl()) {
                    $row['allowed'] = MX_IS_ADMIN;
                }

                switch (true) {
                    case $this->_current_link:
                    case empty($this->_module):
                    case $this->_module != $url['path']:
                        break 2; // beide switches beenden
                }

                if (!$urlparts) {
                    $this->_mainmod[$url['path']] = $row['id'];
                }
                if (!array_diff($this->request, $urlparts) && !array_diff($urlparts, $this->request)) {
                    $this->_current_link = $row['id'];
                    break; // beide switches beenden
                }
        }

        return $row;
    }

    private function _set_current_parents($arr, $current)
    {
        /* Array mit allen Parents des aktuellen Links erstellen */
        if (!$current) {
            return;
        }
        $this->_current_link_parents[] = $current;
        if ($arr[$current]['pid']) {
            $this->_set_current_parents($arr, $arr[$current]['pid']);
        }
    }
}

?>