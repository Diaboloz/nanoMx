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

if (!mxGetAdminPref('radminsuper')) {
    mxErrorScreen("Access Denied");
    die();
}

/* Initialisiert mxMenu... */
load_class('Menu', false);

class Mx_Menu_Admin extends pmxMenu {
    protected $row = array('id' => 0,
        'bid' => 0,
        'pid' => 0,
        'title' => '',
        'description' => '',
        'url' => '',
        'weight' => 0,
        'active' => 1,
        'token' => 'new',
        'expanded' => '',
        'target' => '',
        );

    private $nav_item = array();
    private $page = array();
    private $event = array();
    private $message;
    // das Template Objekt
    private $template;

    public function __construct()
    {
        /* Elternklasse initialisieren */
        parent::__construct();

        $get = $this->handleEvent();

        define('IMG_MXMENU_EDIT', '<i class="fa fa-edit fa-lg"></i>&nbsp;'. _EDIT . '');
       // define('IMG_MXMENU_DEACTIVATE', mxCreateImage('images/activate.gif', _DEACTIVATE, array('title' => _DEACTIVATE)));
       // define('IMG_MXMENU_ACTIVATE', mxCreateImage('images/deactivate.gif', _ACTIVATE, array('title' => _ACTIVATE)));
        define('IMG_MXMENU_DELETE', '<i class="fa fa-trash fa-lg"></i>&nbsp;'. _DELETE . '');
        define('IMG_MXMENU_HOME', '<i class="fa fa-home fa-lg"></i>&nbsp;'. _HOME . '');
        // define('IMG_MXMENU_MODULE_IMPORT', mxCreateImage('images/import.gif', _MX_MENU_MODULE_IMPORT, array('title' => _MX_MENU_MODULE_IMPORT)));
        // define('IMG_MXMENU_MODULE_ADMIN', mxCreateImage('images/admin.gif', _MX_MENU_MODULE_ADMIN, array('title' => _MX_MENU_MODULE_ADMIN)));
        /* Template initialisieren */
        $this->template = load_class('Template');
        $this->template->init_path(__FILE__);
        $this->set_navigation();

        include('header.php');

        switch ($get['menu']) {
            case "add_menu":
                $this->add_menu();
                break;
            case "add_item":
                $this->add_item();
                break;
            default:
                $this->show_all();
        }

        $selected = $this->page['selected'];
        $this->nav_item[$selected]['class'] = " class= 'current'";
        $items = array('nav_item' => $this->nav_item, 'page' => $this->page['content']);
        $this->template->assign($items);
        $this->template->assign('nomenublocks', $this->get_bids_db('blocks') == array() ? true : false);
        $this->template->display("navigation.html");

        include("footer.php");
    }

    function handleEvent()
    {
        $this->event = $this->get_admin_url();

        $this->message = '';

        $this->onchange_events();
        $this->ajax_events();

        $this->show_all_events();
        $this->add_menu_events();
        $this->add_item_events();
        return $this->event;
    }

    function show_all()
    {
        $menu_items = array();
        $mnu_array = array();

        $menus = $this->read_menus_db();
        foreach ($menus as $menu) {
            $bid = $menu['bid'];
            $this->read_items_4showall_db($bid, 0, "- ", $mnu_array);
            $menu_items[$bid] = $mnu_array;
            unset($mnu_array);
        }
        if (count($menus) == 0) {
            $this->message = $this->get_message(_MX_MENU_SHOWALL_NO_MENUS);
        }
        $array = array('message' => $this->message,
            'main_module' => mxGetMainModuleName(),
            'menus' => $menus,
            'menu_items' => $menu_items,
            'this_menu' => $this,
            );
        $this->template->assign($array);
        $this->page['content'] = $this->template->fetch('show_all.html');
        $this->page['selected'] = 1;
    }

    function add_menu()
    {
        if ($this->event['menu'] == 'add_menu' || $this->event['add_menu'] == 'edit') {
            $id = (!empty($this->event['edit']) ? $this->event['edit'] : 0);

            $items = array('intro' => ($id > 0) ? sprintf(_MX_MENU_ADDMENU_BLOCKEDIT, adminUrl('blocks', 'Edit' , 'bid=' . $id)) : _MX_MENU_ADDMENU_INTRO,
                'message' => $this->message,
                'title' => ($id > 0) ? $this->get_menu_title_db($id) : "",
                'id' => $id
                );
            $this->template->assign($items);
            if ($id > 0) {
                $this->nav_item[2]['url'] = adminUrl(PMX_MODULE, "add_menu/edit/$id");
                $this->nav_item[2]['title'] = _MX_MENU_ADDMENU_EDIT;
            }
            $this->page['content'] = $this->template->fetch('add_menu.html');
            $this->page['selected'] = 2;
        }
    }

    function add_item()
    {
        $sub_arr = array();
        $row = array();
        $token = '';
        $id = 0;

        $modules = $this->read_modules_db();

        if (!empty($this->event['add_item'])) {
            if (intval($this->event['add_item']) > 0) {
                $row['bid'] = intval($this->event['add_item']); // von show_all kommend mit vorgegeb. block id
            }

            switch (true) {
                case is_array($this->event['add_item']):
                    $token = 'new';
                    $row = $this->event['add_item']; // temp. eintraege aus formular uebernehmen
                    break;

                case $this->event['add_item'] == 'edit':
                    $token = 'update';
                    $id = intval($this->event['edit']);
                    $row = $this->get_item_db($id); # aus db holen und in formular eintragen
                    $row['expanded'] = ($row['expanded']) ? ' checked="checked"' : '';
                    $this->nav_item[3]['url'] = adminUrl(PMX_MODULE, "add_item/edit/" . $id);
                    $this->nav_item[3]['title'] = _MX_MENU_ADDITEM_EDIT;
                    break;

                case $this->event['add_item'] == 'module':
                    $token = 'new';
                    $mid = intval($this->event['module']);
                    $module = $this->get_module_db($mid);
                    $row['expanded'] = "";
                    $row['active'] = $module['active'];
                    $row['title'] = $module['custom_title'];
                    $row['url'] = 'modules.php?name=' . $module['title'];
                    $row['description'] = $module['custom_title'];
                    break;
            }
        }

        /* Standardwerte mit erstelletn Werten überschreiben */
        $row = array_merge($this->row, $row);

        $_menus = $this->read_menus_db();
        foreach ($_menus as $_menu) {
            $bid = $_menu['bid'];
            if ($token == 'new') {
                $id = null;
            }
            $this->read_items_4select_db($id, $bid, 0, "- ", $sub_arr[$bid]);
        }

        $items = array('token' => $token,
            'message' => $this->message,
            'main_module' => mxGetMainModuleName(),
            'menus' => $_menus,
            'submenus' => $sub_arr,
            'row' => $row,
            'modules' => $modules,
            );
        $this->template->assign($items);

        $this->page['content'] = $this->template->fetch("add_item.html");
        $this->page['selected'] = 3;
    }

    private function set_navigation()
    {
        $this->nav_item[1]['url'] = adminUrl(PMX_MODULE);
        $this->nav_item[1]['title'] = _MX_MENU_SHOWALL;
        $this->nav_item[2]['url'] = adminUrl(PMX_MODULE, 'add_menu');
        $this->nav_item[2]['title'] = _MX_MENU_ADDMENU;
        $this->nav_item[3]['url'] = adminUrl(PMX_MODULE, 'add_item');
        $this->nav_item[3]['title'] = _MX_MENU_ADDITEM;

        for ($i = 1; $i <= count($this->nav_item); $i++) {
            $this->nav_item[$i]['class'] = "";
        }
    }

    function show_all_events()
    {
        // change_menu_status begin
        if (!empty($this->event['change_status'])) {
            $bid = $this->event['change_status'];
            $ret = $this->change_menu_status_db($bid);
            $txt = ($ret == 1) ? _MX_MENU_ENABLED : _MX_MENU_DISABLED;
            $this->message = $this->get_message(sprintf($txt, $this->title));
        }
        // change_menu_status end
        // delete_menu begin
        if (!empty($this->event['delete_menu'])) {
            $delev = $this->event['delete_menu'];
            $arr = explode(",", $delev);
            if ($arr[0] == 'id' && $arr[1] > 0) {
                $id = $arr[1];
                $title = $this->get_menu_title_db($arr[1]);
                if (empty($arr[2])) {
                    $delurl = adminUrl(PMX_MODULE, "delete_menu/id," . $id . ",ok");
                    $this->message = $this->get_message(sprintf(_MX_MENU_DELETE_AREYOUSURE, $title , $delurl), 1);
                } elseif ($arr[2] == 'ok' && $this->delete_menu_db($id) > 0) {
                    $this->message = $this->get_message(sprintf(_MX_MENU_DELETED, $title));
                }
            }
        }
        // delete_menu end
        // delete_item begin
        if (!empty($this->event['delete_item'])) {
            $delev = $this->event['delete_item'];
            $arr = explode(",", $delev);
            if ($arr[0] == 'id' && $arr[1] > 0) {
                $id = $arr[1];
                $title = $this->get_item_title_db($arr[1]);
                $childs = $this->get_item_childs_db($arr[1]);
                switch (true) {
                    case empty($arr[2]) && $childs:
                        // Unterpunkte vorhanden
                        $delurl = adminUrl(PMX_MODULE, "delete_item/id," . $id . ",ok");
                        $this->message = $this->get_message(sprintf(_MX_MENU_ADDITEM_DELETE_AREYOUSURE_2, $title , $delurl), 1);
                        break;
                    case empty($arr[2]):
                        // keine Unterpunkte vorhanden
                        $delurl = adminUrl(PMX_MODULE, "delete_item/id," . $id . ",ok");
                        $this->message = $this->get_message(sprintf(_MX_MENU_ADDITEM_DELETE_AREYOUSURE_1, $title , $delurl), 1);
                        break;
                    case ($arr[2] == 'ok') && ($this->delete_item_db($id) > 0):
                        // Abschlussmeldung
                        $this->message = $this->get_message(sprintf(_MX_MENU_ADDITEM_DELETED, $title));
                        break;
                }
            }
        }
        // delete_item end
    }

    function add_menu_events()
    {
        if (isset($_POST['add_menu_form'])) {
            $add_menu_form = $_POST['add_menu_form'];
            // check title
            if (empty($add_menu_form['title'])) {
                $this->message = $this->get_message(sprintf(_MX_MENU_INPUTREQUIRED, _TITLE), 1);
                $this->event['menu'] = 'add_menu';
            } else {
                if ($add_menu_form['id'] > 0) {
                    $do = $this->update_menu_db($add_menu_form['id'], $add_menu_form['title']);
                } else {
                    $do = $this->add_menu_db($add_menu_form['title']);
                }

                if ($do > 0) {
                    $this->message = ($add_menu_form['id'] > 0 ? $this->get_message(sprintf(_MX_MENU_ADDMENU_UPDATED, $add_menu_form['title'])) : $this->get_message(sprintf(_MX_MENU_ADDMENU_ADDED, $add_menu_form['title'])));
                    $this->event['menu'] = '';
                } elseif ($do == -1) {
                    $this->message = $this->get_message(sprintf(_MX_MENU_ADDMENU_EXISTEDALREADY, $add_menu_form['title']), 1);
                    $this->event['menu'] = 'add_menu';
                    if ($add_menu_form['id'] > 0) {
                        $this->event['add_menu'] = 'edit';
                        $this->event['edit'] = $add_menu_form['id'];
                    }
                }
            }
        }
    }

    function add_item_events()
    {
        $msg = '';

        if (isset($_POST['add_item_form'])) {
            $pvs = $_POST['add_item_form'];

            if (empty($pvs['title'])) {
                $msg .= sprintf(_MX_MENU_INPUTREQUIRED, _TITLE);
            }

            if ($msg) {
                $this->message = $this->get_message($msg, 1);
                $this->event['menu'] = 'add_item';
                $this->event['add_item'] = $pvs;
                return;
            }
            // pid aus 'bid:pid' rausholen
            $marray = explode(':', $pvs['bid']);
            $pvs['bid'] = intval($marray[0]);
            $pvs['pid'] = intval($marray[1]);

            $ok = false;
            $title = mxTranslate($pvs['title']);

            $pvs['url'] = mx_urltohtml($pvs['url']);

            if ($pvs['token'] == 'update') {
                $ok = $this->update_item_db($pvs);
                $this->message = $this->get_message(sprintf(_MX_MENU_ADDITEM_UPDATED, $title));
            } else {
                $ok = $this->add_item_db($pvs);
                $this->message = $this->get_message(sprintf(_MX_MENU_ADDITEM_ADDED, $title));
            }

            if (!$ok) {
                $this->message = $this->get_message(sprintf(_MX_MENU_SAVEERROR, $title), 1);
            }
            // $this->event['menu'] = 'add_item';
            // if ($do > 0) {
            // $this->message = ($pvs['token'] == 'update' ? $this->get_message(sprintf(_MX_MENU_ADDITEM_UPDATED, $title)) : $this->get_message(sprintf(_MX_MENU_ADDITEM_ADDED, $title)));
            // $this->event['menu'] = '';
            // }
        }
    }

    function ajax_events()
    {
        switch (true) {
            case !isset($_REQUEST['ajax']):
                return;
                // case isset($_REQUEST['ajax']):
            case $_REQUEST['ajax'] == 'weightselect' && isset($_POST['id']):
                header('Content-type: text/html; charset=utf-8');
                header('Content-Language: ' . _DOC_LANGUAGE);
                list($bid, $pid) = explode(':', $_POST['id']);
                $current = (isset($_POST['cur'])) ? intval($_POST['cur']) : 0;
                $rows = $this->get_item_childs_db($pid, $bid);
                $this->check_weight($rows);
                $var = array();
                foreach ($rows as $key => $row) {
                    switch (true) {
                        case $row['weight'] == $current:
                            // $var[$key] = '<option value="' . $row['weight'] . '">---</option>';
                            break;
                        case $row['weight'] == $current + 1:
                            $var[$key] = '<option value="' . $row['weight'] . '" selected="selected" class="current">' . _MX_MENU_POS_BEFORE . ' &gt; ' . mxTranslate($row['title']) . '</option>';
                            break;
                        default:
                            $var[$key] = '<option value="' . $row['weight'] . '">' . _MX_MENU_POS_BEFORE . ' &gt; ' . mxTranslate($row['title']) . '</option>';
                    }
                }
                if ($var) {
                    $var[0] = '<option value="0"> - ' . _MX_MENU_POS_BEGIN . '</option>';
                    $var[] = '<option value="' . ($row['weight'] + 1) . '"> - ' . _MX_MENU_POS_LAST . '</option>';
                } else {
                    $var[0] = '<option value="0" selected="selected" class="current"> - ' . _MX_MENU_POS_BEGIN . '</option>';
                }
                $out = implode("\n", $var);
                die($out);

            default:
                // mxDebugFuncVars($_REQUEST['ajax']);
        }
    }

    function onchange_events()
    {
        $this->delete_abandoned_items_db();
    }
    /* events end */

    function get_admin_url()
    {
        $build = array();
        if (isset($_GET['op'])) {
            $op = $_GET['op'];
            $array = explode("/", $op);
            foreach($array as $key => $var) {
                if (!empty($array[$key + 1])) {
                    $build[$var] = $array[$key + 1];
                } else {
                    $build[$var] = "";
                }
            }
        }
        if (!isset($build['menu'])) {
            return false;
        } else {
            return $build;
        }
    }

    function get_message($text, $type = 0)
    {
        $msg_style = "";
        switch ($type) {
            case 1:
                $msg_style = 'alert alert-danger text-center';
                break; # successful
            default:
                $msg_style = 'alert alert-info text-center';
                break; # error
        }
        return (!empty($text) ? '<div class="' . $msg_style . '">' . $text . '</div>' : '');
    }
}

$actions = new Mx_Menu_Admin();
unset($actions);

?>