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
 * $Revision: 41 $
 * $Author: PragmaMx $
 * $Date: 2015-07-29 13:41:56 +0200 (Mi, 29. Jul 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * logfiler_admin
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2014
 * @version $Id: index.php 41 2015-07-29 11:41:56Z PragmaMx $
 * @access public
 */
class logfiler_admin {
    private $_pathes = array();
    private $_messages = array();
    private $_warnings = array();
    private $_template;
    private $_perpage = 30;
    private $_allsize = 0;
    private $_form;
    private $_homepage = '';

    /**
     * logfiler_admin::__construct()
     *
     * @param mixed $op
     */
    public function __construct($op)
    {
        /* Sprachdatei auswählen */
        mxGetLangfile(__DIR__);

        $this->_pathes[] = PMX_DYNADATA_DIR . DS . 'logfiles' . DS;
        $this->_homepage = adminUrl(PMX_MODULE);

        /* Template initialisieren */
        $this->_template = load_class('Template');
        $this->_template->init_path(__DIR__);

        /* Datei in prettyPhoto anzeigen */
        if ('logfiler/details' == $op) {
            return $this->_ajaxfile();
        }

        $this->_form = load_class('AdminForm', PMX_MODULE);
        $action = $this->_form->CheckButton();

        switch ($action) {
            case 'content':
                return $this->_viewfile();
            case 'delete':
                return $this->_deletefile();
            case 'deleteall':
                return $this->_deleteall();
				
            default:
                return $this->_main();
        }
    }

    /**
     * logfiler_admin::_main()
     *
     * @return
     */
    private function _main()
    {
        $start = (empty($_GET['start']) || $_GET['start'] < 0) ? 0 : intval($_GET['start']);
        $show = (empty($_GET['show']) || $_GET['show'] < 0) ? intval($this->_perpage) : intval($_GET['show']);

        $files = $this->_get_files();
        $allrows = count($files);
        if ($allrows > $show) {
            // Ausschnitt extrahieren
            $files = array_slice($files, $start, $show, true);
        }

        $link = adminUrl(PMX_MODULE) . '&amp;start=%d';
        if (isset($_GET['show'])) {
            $link .= '&amp;show=' . $show;
        }

        $pagination = $this->pagination($allrows, $start, $show, $link);

        /* Form Einstellungen */
        $this->_form->tb_pic_heigth = 25;
        $this->_form->infobutton = false;

        /* Toolbar zusammenstellen */
        $this->_form->addToolbar('contentx', _LOGF_FILECONT);
        $this->_form->addToolbar('deletex');

        $this->_template->assign('files', $files);
        $this->_template->assign('count', count($files));
        $this->_template->assign('sumsize', $this->_format_filesize($this->_allsize));
        $this->_template->assign('form', $this->_form);

        $this->_template->assign('messages', $this->_messages);
        $this->_template->assign('warnings', $this->_warnings);
        $this->_template->assign('pagination', $pagination);

        include('header.php');
        $this->_template->display('fileslist.html');
        include('footer.php');
    }

    /**
     * logfiler_admin::_viewfile()
     *
     * @return
     */
    private function _viewfile()
    {
        $files = $this->_get_files();
        switch (true) {
            case !isset($_POST['del']):
            case !is_array($_POST['del']):
            case !($file_id = array_key_exists($_POST['del'][0], $files)):
                $this->_warnings[] = _NOACTION;
                return $this->_main();
                break;
            default:
        }

        $file_id = $_POST['del'][0];
        $file = $files[$file_id];

        $file['content'] = file_get_contents($file['file']);
        $file['content'] = $this->_cleancontent($file['content']);

        $this->_form->addToolbarLink('back', $this->_homepage);

        $this->_template->assign('form', $this->_form);
        $this->_template->assign($file);

        include('header.php');
        $this->_template->display('file.view.html');
        include('footer.php');
    }

    /**
     * logfiler_admin::_ajaxfile()
     * Datei in prettyPhoto anzeigen
     *
     * @return
     */
    private function _ajaxfile()
    {
        pmxDebug::pause();
        switch (true) {
            case !isset($_GET['file']):
            case !($filename = base64_decode($_GET['file'])):
            case !($filename = realpath($filename)):
                die('no file selected...');
            default:
                pmxDebug::restore();
        }

        $file['filename'] = $filename;
        $file['basename'] = basename($filename);
        $file['content'] = file_get_contents($filename);
        $file['content'] = $this->_cleancontent($file['content']);

        $this->_template->assign($file);
        $this->_template->display('file.ajax.html');
    }

    /**
     * logfiler_admin::_deletefile()
     *
     * @return
     */
    private function _deletefile()
    {
        switch (true) {
            case !isset($_POST['del']):
            case !is_array($_POST['del']):
                // case !(array_search(1, $_POST['del'])):
                $this->_warnings[] = _NOACTION;
                return $this->_main();
            default:
        }

        $files = $this->_get_files();
        $deleted = array();
        $notdeleted = array();
        foreach ($_POST['del'] as $filename) {
            if (isset($files[$filename])) {
                if (unlink($files[$filename]['file'])) {		
                    $deleted[] = $files[$filename]['basename'];
                } else {
                    $notdeleted[] = $files[$filename]['basename'];
                }
            }
        }

        switch (count($deleted)) {
            case 0:
                $this->_warnings[] = _LOGF_DELOK0;
                break;
            case 1:
                $this->_messages[] = sprintf(_LOGF_DELOK1, $deleted[0]);
                break;
            default:
                $this->_messages[] = sprintf(_LOGF_DELOK2, '<ul><li>' . implode('</li><li>', $deleted) . '</li></ul>');
        }

        switch (count($notdeleted)) {
            // case 0:
            // $msg = 'Es wurden keine Dateien gelöscht.';
            // break;
            case 1:
                $this->_warnings[] = sprintf(_LOGF_DELNOK1, $notdeleted[0]);
                break;
            default:
                if (count($notdeleted) > 1) $this->_warnings[] = sprintf(_LOGF_DELNOK2, '<ul><li>' . implode('</li><li>', $notdeleted) . '</li></ul>');
        }

        return $this->_main();
    }

    /**
     * logfiler_admin::_get_files()
     *
     * @return
     */
    private function _get_files()
    {
        $files = array();
        $ignores = array('index.html', 'readme.txt', '.htaccess', 'CVS', '.svn', 'SVN' );

        foreach ($this->_pathes as $path) {
            foreach ((array)glob($path . '*') as $file) {
                $basename = basename($file);
                if (is_file($file) && !in_array($basename, $ignores)) {
                    $time = filemtime($file);
                    $size = filesize($file);
                    $filename = str_replace(DS, '/', mx_strip_sysdirs($file));
                    $id = $time . "-" . base64_encode($filename);
                    $files[$id] = array(/* Dateieigenschaften */
                        'id' => $id,
                        'file' => $filename,
                        'basename' => $basename,
                        'time' => mx_strftime(_SHORTDATESTRING . ' %H:%M', filemtime($file)),
                        'size' => $this->_format_filesize($size),
                        'perms' => substr(sprintf('%o', fileperms($file)), -4));
                    $this->_allsize += $size;
                }
            }
        }

        krsort($files);
        return $files;
    }

    /**
     * logfiler_admin::_format_filesize()
     *
     * @param mixed $size
     * @return
     */
    private function _format_filesize($size)
    {
        return sprintf ("%01.2f", $size / 1024) . " Kb";

        $mb = 1024 * 1024;
        if ($size > $mb) {
            return sprintf ("%01.2f", $size / $mb) . " MB";
        } elseif ($size >= 1024) {
            return sprintf ("%01.2f", $size / 1024) . " Kb";
        } else {
            return $size . " bytes";
        }
    }

    /**
     * logfiler_admin::pagination()
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

    /**
     * logfiler_admin::_cleancontent()
     *
     * @param mixed $content
     * @return
     */
    private function _cleancontent($content)
    {
        $content = str_replace('in ' . PMX_REAL_BASE_DIR, 'in .', $content);
        return $content;
    }
	
	private function _deleteall()
	{
       switch (true) {
            case intval($_POST['deleteallrem'])==0:
                $this->_warnings[] = _LOGF_DELETEALLERR;
				$this->_warnings[] = _LOGF_DELOK0;
                return $this->_main();
	   }		
        $files = $this->_get_files();
        $deleted = array();
        $notdeleted = array();
        foreach ($files as $filename) {
            if (isset($filename)) {
                if (unlink($filename['file'])) {		//unlink
                    $deleted[] = $filename['basename'];
                } else {
                    $notdeleted[] = $filename['basename'];
                }
            }
        }

        switch (count($deleted)) {
            case 0:
                $this->_warnings[] = _LOGF_DELOK0;
                break;
            case 1:
                $this->_messages[] = sprintf(_LOGF_DELOK1, $deleted[0]);
                break;
            default:
                $this->_messages[] = sprintf(_LOGF_DELOK2, '<ul><li>' . implode('</li><li>', $deleted) . '</li></ul>');
        }

        switch (count($notdeleted)) {
            // case 0:
            // $msg = 'Es wurden keine Dateien gelöscht.';
            // break;
            case 1:
                $this->_warnings[] = sprintf(_LOGF_DELNOK1, $notdeleted[0]);
                break;
            default:
                if (count($notdeleted) > 1) $this->_warnings[] = sprintf(_LOGF_DELNOK2, '<ul><li>' . implode('</li><li>', $notdeleted) . '</li></ul>');
        }

        return $this->_main();
	}
}

$tmp = new logfiler_admin($op);
$tmp = null;

?>