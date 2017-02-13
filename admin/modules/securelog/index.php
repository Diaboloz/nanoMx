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
 * system_log_admin
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2014
 * @version $Id: index.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class system_log_admin {
    private $_warnings = array();

    /* Einstellungen: */
    private $_perpage = 30;

    private $_dbtable;

    /**
     * system_log_admin::__construct()
     */
    public function __construct($op)
    {
        if (!mxGetAdminPref('radminsuper')) {
            return mxRedirect(adminUrl(), _ACCESSDENIED);
        }

        /* Sprachdatei auswählen */
        mxGetLangfile(__DIR__);

        $this->_dbtable = $GLOBALS['prefix'] . "_securelog";

        /* Was ist zu tun ? */
        switch ($op) {
            case PMX_MODULE . '/delete':
                return $this->delete();
            case PMX_MODULE . '/details':
                return $this->details();
            default:
                return $this->main();
        }
    }

    /**
     * system_log_admin::main()
     *
     * @return
     */
    private function main()
    {
        $start = (empty($_GET['start']) || $_GET['start'] < 0) ? 0 : intval($_GET['start']);
        $show = (empty($_GET['show']) || $_GET['show'] < 0) ? intval($this->_perpage) : intval($_GET['show']);

        $result = sql_query("SELECT count(id) FROM `{$this->_dbtable}`");
        list($allrows) = sql_fetch_row($result);

        $link = adminUrl(PMX_MODULE) . '&amp;start=%d';
        if (isset($_GET['show'])) {
            $link .= '&amp;show=' . $show;
        }

        $pagination = $this->pagination($allrows, $start, $show, $link);

        $result = sql_query("SELECT *
            FROM `{$this->_dbtable}`
            ORDER BY log_time DESC
            LIMIT {$start}, {$show}");

        $rows = array();
        while ($row = sql_fetch_assoc($result)) {
            $rows[] = $row;
        }

        if (empty($GLOBALS['vkpsec_logging'])) {
            $this->_warnings[] = _SECLOGDEACTIVATE;
        }

        /* Template initialisieren */
        $template = load_class('Template');
        $template->init_path(__DIR__);

        $template->assign('warnings', $this->_warnings);
        $template->assign('rows', $rows);
        $template->assign('pagination', $pagination);

        include('header.php');
        $template->display('list.html');
        include('footer.php');
    }

    /**
     * system_log_admin::details()
     *
     * @return
     */
    private function details()
    {
        if (empty($_GET['id'])) {
            echo 'empty...';
        }

        $result = sql_query("SELECT *
            FROM `{$this->_dbtable}`
            WHERE id=" . intval($_GET['id']) . "");

        $row = sql_fetch_assoc($result);

        $entry = '';
        $data = array();
        if ($row['request']) {
            /* normaler Logeintrag */
            pmxDebug::pause();
            $check = unserialize($row['request']);
            pmxDebug::restore();
            if (is_array($check) && $check) {
                $data = $check;
            } else {
                $entry = $row['request'];
            }
        }

        /* Template initialisieren */
        $template = load_class('Template');
        $template->init_path(__DIR__);

        $template->assign($row);
        $template->assign(compact('data', 'entry'));

        $template->display('details.html');
    }

    /**
     * system_log_admin::delete()
     *
     * @return
     */
    private function delete()
    {
        switch (true) {
            case isset($_POST['delsome']) && $_POST['delsome']:
                $where = array();
                foreach ($_POST['id'] as $key => $value) {
                    if ($value) {
                        $tmp[] = $key;
                    }
                }
                $qry = "DELETE FROM `{$this->_dbtable}` WHERE id IN(" . implode(',', $tmp) . ")";
                break;
            case isset($_POST['delall']) && $_POST['delall']:
                $qry = "TRUNCATE TABLE `{$this->_dbtable}`";
                break;
            default:
                return mxRedirect(adminUrl(PMX_MODULE));
        }

        sql_query($qry);
        // mxSecureLog("SecLog", _SECLOGDELOK);
        return mxRedirect(adminUrl(PMX_MODULE), _SECLOGDELOK);
    }

    /**
     * system_log_admin::pagination()
     * Page Numbering
     *
     * @param mixed $count_rows
     * @param mixed $start
     * @param mixed $show
     * @param mixed $link
     * @return
     */
    private function pagination($count_rows, $start, $show, $link)
    {
        $pages = ceil($count_rows / $show);
        // wenn weniger als 2 Seiten, nix anzeigen
        if ($pages <= 1) {
            return;
        }

        $max = intval($start + $show);
        $prev = intval($start - $show);
        $currentpage = ceil($max / $show);
        $start = '';
        $ende = '';
        $counter = 1;
        $part = array();

        if ($currentpage > 3 && $pages > 6) {
            $start = '<a href="' . sprintf($link, 0) . '" title="' . _GOTOPAGEFIRST . '">1&nbsp;<span class="arrows">&laquo;</span></a><span class="points">..</span>';
            if ($currentpage >= $pages - 3) {
                $counter = $currentpage - ($currentpage - $pages + 4);
            } else {
                $counter = $currentpage - 1;
            }
        }
        // Schleife durch Suchergebnisse
        while ($counter <= $pages) {
            $mintemp = ($show * $counter) - $show;
            switch (true) {
                case ($counter > 5) && ($counter > $currentpage + 2) && ($counter < $pages) && $pages > 6:
                    $ende = '<span class="points">..</span><a href="' . sprintf($link, ($pages-1) * $show) . '" title="' . _GOTOPAGELAST . '"><span class="arrows">&raquo;</span>&nbsp;' . $pages . ' </a>';
                    break 2;
                case $counter == $currentpage:
                    $part[] = '<a href="' . sprintf($link, $mintemp) . '" title="' . sprintf(_PAGEOFPAGES, $currentpage, $pages) . '" class="current">' . $counter . '</a>';
                    break;
                default:
                    $part[] = '<a href="' . sprintf($link, $mintemp) . '" title="' . _GOTOPAGE . ' ' . $counter . '">' . $counter . '</a>';
            }

            $counter++;
        }

        if ($part) {
            return '
            <div class="pagination align-right">
              <span class="counter">' . sprintf(_PAGEOFPAGES, $currentpage, $pages) . '</span>
              ' . $start . implode('', $part) . $ende . '
            </div>';
        }
    }
}

$tmp = new system_log_admin($op);
$tmp = null;

?>