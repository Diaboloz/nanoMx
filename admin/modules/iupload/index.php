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
 * $Revision: 205 $
 * $Author: PragmaMx $
 * $Date: 2016-08-19 10:09:41 +0200 (Fr, 19. Aug 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * imgupload_admin
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2011
 * @version $Id: index.php 205 2016-08-19 08:09:41Z PragmaMx $
 * @access public
 */
 
class imgupload_admin
{
    private $_config = array();
    private $_pathes = array();
    private $_tabs = array();
    private $_upload_dir;
    private $_max_view_width = 400;

    private $_messages = array();
    private $_warnings = array();

    private $_template;

    /**
     * imgupload_admin::__construct()
     *
     * @param mixed $op
     */
    public function __construct($op)
    {
        /* Sprachdatei auswählen */
        mxGetLangfile(__DIR__);

        $fb = load_class('Filebrowse');
        $this->_config = $fb->get_config();
        if (!$this->_config) {
            return mxErrorScreen('there is something wrong...');
        }

        if (!(in_array('images', $this->_config['filetype']) || in_array('any', $this->_config['filetype']))) {
            $this->_config['allow_upload'] = false;
            $this->_config['allow_modify'] = false;
        }

        /* Template initialisieren */
        $this->_template = load_class('Template');
        $this->_template->init_path(__FILE__);

        if (file_exists('images/iupload')) {
            $this->_pathes[] = 'images/iupload';
        }
        if (file_exists('media/images')) {
            $this->_pathes[] = 'media/images';
        }
        if (!$this->_pathes) {
            return mxErrorScreen('upload directory "media/images/" is not available');
        }

        switch (true) {
            case $op == PMX_MODULE . '/manager' && $fb->is_active():
                mxSessionSetVar('iuploadpath', -1);
                break;
            case isset($_GET['path']) && isset($this->_pathes[intval($_GET['path'])]):
                $this->_upload_dir = $this->_pathes[intval($_GET['path'])];
                mxSessionSetVar('iuploadpath', intval($_GET['path']));
                break;
            case $op == PMX_MODULE && (mxSessionGetVar('iuploadpath', -1) === -1) && $fb->is_active():
                mxSessionSetVar('iuploadpath', -1);
                $op = PMX_MODULE . '/manager';
                break;
            case mxSessionGetVar('iuploadpath') && isset($this->_pathes[mxSessionGetVar('iuploadpath')]):
                $this->_upload_dir = $this->_pathes[mxSessionGetVar('iuploadpath')];
                break;
            default:
                $this->_upload_dir = $this->_pathes[0];
                mxSessionSetVar('iuploadpath', 0);
                break;
        }

        if ($fb->is_active()) {
            $this->_tabs['manage']['caption'] = _IUP_MANAGER;
            $this->_tabs['manage']['current'] = mxSessionGetVar('iuploadpath') === -1;
            $this->_tabs['manage']['link'] = adminUrl(PMX_MODULE, 'manager');
        }
        foreach ($this->_pathes as $key => $path) {
            $this->_tabs[$path]['caption'] = _IUP_IUPLOAD . ': <i>' . $path . '</i>';
            $this->_tabs[$path]['current'] = mxSessionGetVar('iuploadpath') === $key;
            $this->_tabs[$path]['link'] = adminUrl(PMX_MODULE, '', array('path' => $key));
        }

        switch ($op) {
            case PMX_MODULE . '/manager':
                if ($fb->is_active()) {
                    return $this->_manager();
                }
                $op = PMX_MODULE;
                return $this->_main();

            case PMX_MODULE . '/upload':
                $this->_upload();
                return $this->_main();

            case PMX_MODULE . '/actions':
                if (isset($_POST['deletePic'])) {
                    $this->_delete();
                    if (!$this->_messages && !$this->_warnings) {
                        // bei der Sicherheitsabfrage wird die Seite bereits in der Funktion generiert
                        return;
                    }
                }
                return $this->_main();

            default:
                return $this->_main();
        }
    }

    /**
     * imgupload_admin::_main()
     *
     * @return
     */
    private function _main()
    {
        if (isset($_POST['imgview']) && file_exists($this->_upload_dir . '/' . $_POST['imgview'])) {
            $imgview = $_POST['imgview'];
            $temp_image = $this->_upload_dir . '/' . $imgview;
            $viewpath = $this->_upload_dir . '/' . $imgview;
        } else {
            $imgview = '';
            $temp_image = 'images/pixel.gif';
            $viewpath = ' ' . _FILEURL . '...';
        }

        $endings = Textarea::get_filetypegroups();
        $image_options = array();
        foreach ((array)glob(str_replace(DS, '/', $this->_upload_dir . '/*')) as $image) {
            if ($image) {
                $info = pathinfo($image);
                if (isset($info['extension']) && isset($endings['images'][strtolower($info['extension'])])) {
                    $image = $info['basename'];
                    $image_options[] = '<option value="' . $image . '"' . (($imgview == $image) ? ' selected="selected" class="current"' : '') . '>' . $image . '</option>';
                }
            }
        }
        natcasesort($image_options);
        array_unshift($image_options, '<option value="None"' . ((empty($imgview)) ? ' selected="selected" class="current"' : '') . '>- ' . _IUP_NONEIM . '</option>');

        $this->_template->assign('allow_upload', $this->_config['allow_upload']);
        $this->_template->assign('allow_modify', $this->_config['allow_modify']);
        $this->_template->assign('image_options', $image_options);
        $this->_template->assign('max_view_width', $this->_max_view_width);
        $this->_template->assign('imagename', $imgview);
        $this->_template->assign('image', $temp_image);
        $this->_template->assign('viewpath', $viewpath);
        $this->_template->assign('upload_dir', $this->_upload_dir);

        $out = $this->_template->fetch('main.html');
        $this->_page($out);
    }

    /**
     * imgupload_admin::_upload()
     *
     * @return
     */
    private function _upload()
    {
        if (!$this->_config['allow_upload']) {
            return $this->_warnings[] = 'upload not allowed...';
        }

        $updir = PMX_REAL_BASE_DIR . DS . $this->_upload_dir;
        $mime = Textarea::get_filetypegroups();

        load_class('Upload', false);
        /* http://www.verot.net/php_class_upload_faq.htm */
        /* What about multiple uploads? */
        $files = array();
        foreach ($_FILES['imgfile'] as $k => $l) {
            foreach ($l as $i => $v) {
                if (!array_key_exists($i, $files)) {
                    $files[$i] = array();
                }
                $files[$i][$k] = $v;
            }
        }

        foreach ($files as $file) {
            if (!$file['name']) {
                continue;
            }

            $handle = new pmxUpload($file);
            if (!$handle->uploaded) {
                $this->_warnings[] = _IUP_PROBSIM . ' <em>' . $handle->file_src_name . '</em>:' . $handle->error;
                continue;
            }

            /* nur Bilder zulassen */
            $handle->allowed = array_values($mime['images']);

            /* falls noch andere Punkte als fuer die Dateiendung vorhanden, */
            /* diese durch _ ersetzen (Apache Bug) */
            $handle->file_new_name_body = str_replace(array('.', ' '), '_', $handle->file_src_name_body);
            /* Dateiendung immer klein */
            $handle->file_new_name_ext = strtolower($handle->file_src_name_ext);;
            /* Leerzeichen im Dateinamen konvertieren */
            $handle->file_safe_name = true;
            /* automatisch umbenennen wenn vorhanden */
            $handle->file_auto_rename = true;

            if ($this->_config['max_img_width'] >= 0) {
                $handle->image_max_width = intval($this->_config['max_img_width']);
            }
            if ($this->_config['max_img_height'] >= 0) {
                $handle->image_max_height = intval($this->_config['max_img_height']);
            }
            /* wenn max-size == 0, Standardwert der Klasse (php.ini) verwenden */
            if ($this->_config['upload_max_size'] >= 0) {
                $handle->file_max_size = intval($this->_config['upload_max_size']); // byte
            }

            /* Seitenverhältnis beibehalten */
            $handle->image_ratio = true;
            $handle->image_ratio_no_zoom_in = true;

            $handle->process($updir);

            if (!$handle->processed) {
                $this->_warnings[] = _IUP_PROBSIM . ' <em>' . $handle->file_src_name . '</em>: ' . $handle->error;
                continue;
            }

            $this->_template->assign('pretty', ($handle->image_dst_x > $this->_max_view_width));
            $this->_template->assign('imagename', $handle->file_dst_name);
            $this->_template->assign('image', $this->_upload_dir . '/' . $handle->file_dst_name);
            $this->_template->assign('max_view_width', $this->_max_view_width);
            $this->_messages[] = $this->_template->fetch('imageview.html');
            unset($handle);
        }
    }

    /**
     * imgupload_admin::_delete()
     *
     * @return
     */
    private function _delete()
    {
        switch (true) {
            case !$this->_config['allow_upload']:
            case !$this->_config['allow_modify']:
                $this->_warnings[] = 'not allowed...';
                break;
            case !isset($_POST['deleteOK']):
            case !isset($_POST['imgview']):
            case !$_POST['imgview']:
            case $_POST['imgview'] == 'None':
            case !file_exists($this->_upload_dir . '/' . $_POST['imgview']):
                $this->_warnings[] = '<em>' . $_POST['imgview'] . '</em> not exist...';
                break;
            case $_POST['deleteOK'] == 1:
                if (unlink($this->_upload_dir . '/' . $_POST['imgview'])) {
                    $this->_messages[] = '<em>' . $_POST['imgview'] . '</em> ' . _IUP_SELECTDELETEIM;
                } else {
                    $this->_warnings[] = '<em>' . $_POST['imgview'] . '</em> ' . _IUP_DELETEFAILDIM2 . '<br />' . $img;
                }
                break;
            default:
                $this->_template->assign('imagename', htmlspecialchars($_POST['imgview']));
                $this->_template->assign('image', $this->_upload_dir . '/' . $_POST['imgview']);
                $this->_template->assign('max_view_width', $this->_max_view_width);
                $out = $this->_template->fetch('delete_confirm.html');
                $this->_page($out);
        }
    }

    /**
     * imgupload_admin::_manager()
     *
     * @return
     */
    private function _manager()
    {
        switch ($this->_config['editor']) {
            case 'spaw':
                $this->_template->assign('spawpath', PMX_SYSTEM_PATH . 'wysiwyg/spaw/filemanager.php');
                $out = $this->_template->fetch('manager.html');
                break;
            default:
                $fb = load_class('Filebrowse');
                $out = $fb->manager();
        }
        $this->_page($out);
    }

    /**
     * imgupload_admin::_page()
     *
     * @param mixed $content
     * @param mixed $title
     * @return
     */
    private function _page($content, $title = '')
    {
        // http://iblog.ikarius.net/index.php?/archives/135-jQuery-und-iframes.html
        $this->_template->assign('tabs', $this->_tabs);
        $this->_template->assign('messages', $this->_messages);
        $this->_template->assign('warnings', $this->_warnings);
        $this->_template->assign('content', $content);
        $out = $this->_template->fetch('page.html');

        if (!$title) {
            $title = _IUP_IUPLOAD . ' / ' . _IUP_MANAGER;
        }

        include('header.php');
        title($title);
        echo $out;
        include('footer.php');
    }
}

$tmp = new imgupload_admin($op);
$tmp = null;

?>