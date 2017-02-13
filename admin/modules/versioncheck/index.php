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
 * $Revision: 51 $
 * $Author: PragmaMx $
 * $Date: 2015-08-05 09:03:39 +0200 (Mi, 05. Aug 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * versioncheck_admin
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2011
 * @version $Id: index.php 51 2015-08-05 07:03:39Z PragmaMx $
 * @access public
 */
class versioncheck_admin {
    /* globales Template */
    private $_template;

    /* globale Fehlermeldung */
    private $_errormessage = array();

    /* globale Benachrichtigung */
    private $_message = array();

    /* externe Versionskontrolldatei */
    protected $verfile;
    /* lokale Versionskontrolldatei */
    protected $verlocal;
    /* wirklich verwendete Versionskontrolldatei */
    protected $checkfile;

    /**
     * Unterordner des pragmaMx-root,
     * nur diese werden in das Kontrollarray aufgenommen.
     */
    protected $rootfolders = array(/* info */
        'admin',
        'blocks',
        'includes',
        'language',
        'layout',
        'modules',
        'spaw',
        );

    /* Dateien und Ordner, die nicht gecheckt werden sollen. */
    protected $excludes = array(/* info */
        'setup',
        'themes',
        'dynadata',
        'includes/utf8/exp',
        'media',
        'restrictor',
        'config.php',
        'convert_collatation.php',
        'convert_username.php',
        'convert_user_regdate.php',
        'create-cvs.php',
        'includes/classes/Captcha/settings.php',
        'includes/classes/Textarea/config.inc.php',
        // 'includes/classes/Upload/sources/phpdoc/stylesheet.css',
        'includes/detection/config.php',
        // 'includes/javascript/jquery/ui/i18n',
        // 'includes/javascript/jquery/ui/jquery.ui.menu.min.js',
        'includes/mxRelatedArray.php',
        'includes/mx_userfunctions_options.php',
        'includes/mx_userfunctions_options.sample.php',
        'includes/my_footer.php',
        'includes/my_header.php',
        'includes/prettyPhoto/config.php',
        // 'includes/utf8/utils/ascii.php',
        'includes/versioncontrol.php',
        // 'includes/wysiwyg/spaw/editor/js/common/entities.js',
        'modules/Downloads/d_config.php',
        'modules/Guestbook/include/config.inc.php',
        'modules/legal/Version.php',
        'modules/My_eGallery/settings.php',
        'modules/Newsletter/includes/settings.php',
        'modules/Private_Messages/config.php',
        'modules/Reviews/config.php',
        'modules/Siteupdate/includes/settings.php',
        'modules/Topics/config.inc.php',
        'modules/UserGuest/settings.php',
        'modules/Web_Links/l_config.php',
        'modules/Web_News/config.inc.php',
        'modules/Your_Account/config.php',
        'includes/javascript/jquery/color/css/colorpicker.css',
        'includes/javascript/jquery/color/css/layout.css',
        'includes/javascript/jquery/social/socialshareprivacy.css',
        'includes/wysiwyg/ckeditor/build-download-config.js',
        'modules/Documents/style/custom.style.css',
        );

    /* allgemeine Datei und Ordnernamen, die nirgends beachtet werden */
    protected $exfiles = array(/* info */
        '.',
        '..',
        'CVS',
        '__history',
        'custom',
        '.cvsignore',
        '.htaccess',
		'.quarantain',
		'.svn*',
		'sess_*.php',
		'.tmp',
		
        );

    /**
     * versioncheck_admin::__construct()
     *
     * @param mixed $op
     */
    public function __construct($op)
    {
        /* Versionskontrolldateien */
        $this->verfile = 'http://version.pragmamx.de/pragma_stable/version.' . PMX_VERSION;
        $this->verlocal = PMX_SYSTEM_DIR . DS . 'versioncontrol.php';

        /* Sprachdatei auswählen */
        mxGetLangfile(__DIR__);

        /* was ist zu tun? */
        $x = explode('/', $op);
        if (isset($x[1])) {
            $filter = $x[1];
        } else {
            $filter = '';
        }

        /* Template initialisieren */
        $this->_template = load_class('Template');
        $this->_template->init_path(__FILE__);
        $this->_template->assign('filter', $filter);

        /* was ist zu tun #2 ? */
        switch ($filter) {
            case '':
                return $this->_main();
            case 'unnecessary':
                return $this->_unnecessary();
            default:
                return $this->_results($filter);
        }
    }

    /**
     * versioncheck_admin::_main()
     *
     * @return
     */
    private function _main()
    {
        $this->_init_results();

        include('header.php');
        $this->_template->display('main.html');
        include('footer.php');
    }

    /**
     * versioncheck_admin::_results()
     *
     * @return
     */
    private function _results($filter)
    {
        $this->_init_results();

        $items = $this->_get_items($filter);
        $message = (array)$this->_message;

        /* Daten dem Template zuweisen */
        $this->_template->assign(compact('items', 'message'));

        $this->_template->content = $this->_template->fetch('results.html');

        include('header.php');
        $this->_template->display('main.html');
        include('footer.php');
    }

    /**
     * versioncheck_admin::_unnecessary()
     *
     * @return
     */
    private function _unnecessary()
    {
        include_once(PMX_SYSTEM_DIR . DS . 'delfiles/functions.php');

        $preselected = array();
        switch (true) {
            case isset($_POST['delsome']) && $_POST['delsome']:
            case isset($_POST['delall']) && $_POST['delall']:
                /* Dateien löschen */
                $this->_unnecessary_delete();
                /* Dateien zum vorselektieren, falls nicht gelöscht */
                $preselected = delfiles::get_notdeleted('merge');
        }

        $items = array();
        $content = '';
        $myitems = delfiles::get();

        /* Array mit unnötig gewordenen Dateien */
        foreach ($myitems as $type => $files) {
            foreach ($files as $file) {
                $items[$type][] = array('file' => $file,
                    'shortfile' => str_replace(array(PMX_REAL_BASE_DIR, DS), array('.', '/'), $file),
                    'id' => base64_encode($file),
                    );
            }
        }
        unset($myitems);

        if ($items) {
            $lang['dirs'] = _VERCHECK_UNNDIRS;
            $lang['files'] = _VERCHECK_UNNFILES;
            /* Daten dem Template zuweisen */
            $this->_template->assign(compact('lang', 'items', 'preselected'));
            $content = $this->_template->fetch('unnecessary.html');
        } else {
            $this->_message[] = _VERCHECK_UNNNOTFOUND;
        }

        $message = (array)$this->_message;
        $errormessage = (array)$this->_errormessage;

        /* Daten dem Template zuweisen */
        $this->_template->assign(compact('content', 'message', 'errormessage'));

        include('header.php');
        $this->_template->display('main.html');
        include('footer.php');
    }

    private function _unnecessary_delete()
    {
        $defaults = array('id-dirs' => array(), 'id-files' => array());
        $pvs = array_merge($defaults, $_POST);

        $files = false;
        $dirs = false;
        $action = false;

        switch (true) {
            case isset($pvs['delsome']) && $pvs['delsome']:
                $files = array_filter($pvs['id-files']);
                $dirs = array_filter($pvs['id-dirs']);
                $action = 'delsome';
                break;
            case isset($pvs['delall']) && $pvs['delall']:
                $files = $pvs['id-files'];
                $dirs = $pvs['id-dirs'];
                $action = 'delall';
                break;
        }

        switch (true) {
            case $action === false:
            case !$files && !$dirs:
                return mxRedirect(adminUrl(PMX_MODULE, 'unnecessary'), _VERCHECK_DELNONE);
            case $action === 'delall':
                delfiles::delete_all();
                break;
            case $action === 'delsome':
                foreach ($files as $file => $tmp) {
                    $file = base64_decode($file);
                    delfiles::erase_file($file);
                }
                foreach ($dirs as $file => $tmp) {
                    $file = base64_decode($file);
                    delfiles::erase_folder($file);
                }
        }

        $err = delfiles::get_notdeleted('count');
        if ($err !== 0) {
            $this->_errormessage[] = _VERCHECK_DELNOALL;
        }
    }

    /**
     * versioncheck_admin::_main()
     *
     * @return
     */
    private function _init_results()
    {
        /* Die Kontrolldatei auslesen, Fehlermeldungen und $checkfile ermitteln */
        $this->verfiles = $this->_get_version_info();

        $errormessage = (array)$this->_errormessage;
        $message = (array)$this->_message;
        $checkfile = (string)$this->checkfile;
        $items = array();
        $content = '';

        $this->_template->assign(compact('filter', 'items', 'errormessage', 'message', 'checkfile', 'content'));
    }

    /**
     * versioncheck_admin::_get_items()
     *
     * @param mixed $filter
     * @return
     */
    protected function _get_items($filter)
    {
        $items = array();

        if (!isset($this->verfiles['files'])) {
            return $items;
        }

        @ini_set('max_execution_time', 0);
        @set_time_limit(0);

        /* Sprachdateien, deren Hauptsprachdatei fehlt, ebenfalls nicht beachten */
        foreach ((array)glob('language/lang-*.php', GLOB_NOSORT) as $filename) {
            if (preg_match('#^lang-(.+)\.php$#', basename($filename), $matches)) {
                $this->exfiles[] = 'hello-' . $matches[1] . '.php';
                $this->exfiles[] = 'option-' . $matches[1] . '.php';
                $this->exfiles[] = 'option-' . $matches[1] . '.sample.php';
            }
        }

        $out = '';
        $i = 0;

        /* Cache initialisieren */
        $cacheid = __METHOD__ . PMX_VERSION_NUM;
        $cache = load_class('Cache');
        /* Ist was im Cache? */
        if (($myfiles = $cache->read($cacheid)) === false) {
            // Nö, nix drin...
            $myfiles = $this->_get_dir_contents(PMX_REAL_BASE_DIR, true);
        }
        /* Cache schreiben */
        $cache->write($myfiles, $cacheid);

        foreach ($this->verfiles['files'] as $file => $value) {
            $version = $value['version'];
            $tmp = explode('/', $file);
            switch (true) {
                case in_array($file, $this->excludes):
                case in_array(array_pop($tmp), $this->exfiles):
                    // Datei/Ordner soll ignoriert werden
                    continue;
                    break;
                case !isset($myfiles[$file]):
                    // Datei fehlt
                    $soll = '??';
                    $msg = _VERCHECK_01;
                    $status = 'missed';
                    $class = 'vcred';
                    break;
                case empty($myfiles[$file]['version']) && !empty($version):
                    // unbekannte Dateiversion
                    $soll = '??';
                    $msg = _VERCHECK_02 . ' ' . _VERCHECK_07;
                    $status = 'conflicts';
                    $class = 'vcred';
                    break;
                case $myfiles[$file]['version'] != $version:
                    // neuere oder ältere Version
                    $xx = version_compare($myfiles[$file]['version'], $version);
                    if ($xx == 1) {
                        // neuere Version
                        $soll = $myfiles[$file]['version'];
                        $msg = _VERCHECK_03 . ' ' . _VERCHECK_07;
                        $class = 'vcblue';
                    } else {
                        // ältere Version
                        $soll = $myfiles[$file]['version'];
                        $msg = _VERCHECK_04 . ' ' . _VERCHECK_07;
                        $class = 'vcred';
                    }
                    $status = 'conflicts';
                    break;
                case $myfiles[$file]['md5'] != $value['md5']:
                    // editiert
                    $soll = $myfiles[$file]['version'];
                    $msg = _VERCHECK_05;
                    $xx = filemtime($file);
                    if ($xx) {
                        $msg .= '<br /><small>' . mx_strftime(_SHORTDATESTRING . ' %H:%M', $xx) . '</small>';
                    }
                    $status = 'edited';
                    $class = 'vcblue';
                    break;
                default:
                    // OK
                    $soll = $myfiles[$file]['version'];
                    $msg = _VERCHECK_06;
                    $status = 'ok';
                    $class = 'vcgreen';
            }

            $version = (empty($version)) ? '??' : $version;

            $items[$status][$file] = compact('version', 'soll', 'msg', 'class');

            $i++;
            // if ($i == 30) break;
        }

        switch ($filter) {
            case 'conflicts':
            case 'edited':
            case 'missed':
            case 'ok':
                if (isset($items[$filter])) {
                    $items = $items[$filter];
                } else {
                    $items = array();
                }
                break;
            case 'allfiles':
                $tmp = array();
                foreach ($items as $key => $value) {
                    $tmp = array_merge($tmp, $value);
                }
                if (isset($items['ok']) && count($tmp) == count($items['ok'])) {
                    $this->_message = _VERCHECK_15;
                }
                $items = $tmp;
                break;
            default:
                $items = array();
        }

        if (!$items) {
            switch ($filter) {
                case 'conflicts':
                    $msg = _VERCHECK_VCONFL;
                    break;
                case 'edited':
                    $msg = _VERCHECK_EDITED;
                    break;
                case 'missed':
                    $msg = _VERCHECK_MISSED;
                    break;
                default:
                    $msg = _VERCHECK_08;
                    break;
            }
            $this->_message = sprintf(_VERCHECK_NOTFOUND, $msg);
        }

        ksort($items);
        return $items;
    }

    /**
     * versioncheck_admin::_get_dir_contents()
     * ermittelt rekursiv die Daten des angegebenen Ordners
     *
     * @param mixed $dir
     * @param mixed $init
     * @return
     */
    protected function _get_dir_contents($dir, $init = false)
    {
        static $initfolder;

        /* sicherstellen, dass der Pfad einen Slash am Ende hat und Backslashes durch normale ersetzt werden */
        $dir = str_replace(DS, '/', rtrim($dir, '/' . DS) . '/');

        if ($init) {
            $initfolder = $dir;
            // echo '>> ' . $initfolder . '<br />'; #exit;
        }
        $nameoffset = strlen($initfolder);
        // echo($dir . '<br />');
        /* Resource erstellen */
        $root = opendir($dir);

        /* wenn Ordner-Resource erstellt werden konnte */
        if (!$root) {
            if (defined('_VERCHECK_17')) {
                $this->_errormessage[] = sprintf(_VERCHECK_17, $dir);
            } else {
                // für create-cvs.php ;-)
                die("<p>Fehler in Funktion _get_dir_contents: kein gültiges Verzeichnis: $dir !</p>");
            }
        }
        // Rueckgabearray initialisieren
        $files = array();

        /* Schleife durch Ordner */
        while (false !== ($file = readdir($root))) {
            // den Pfad vor den Dateinamen setzen
            $newfile = $dir . $file;
            $filename = substr($newfile, $nameoffset);
            // echo 'y '.$filename; exit;
            switch (true) {
                /* Datei soll unabhaengig vom Pfad ignoriert werden, einfach weiter */
                case in_array($file, $this->exfiles):
                /* Dateipfad soll ignoriert werden, einfach weiter */
                case in_array($filename, $this->excludes):
                /* Verknuepfungen werden ignoriert */
                case is_link($newfile):
                    break;

                case is_file($newfile):
                    // leere Dateien extra behandeln
                    if (filesize($filename) === 0) {
                        $files[$filename]['md5'] = md5('');
                        $files[$filename]['version'] = '1.0';
                        break;
                    }
                    // nur bestimmte Dateiendungen beachten
                    if (!preg_match('#\.(php|js|inc|htc|css|html)$#i', $file)) {
                        break;
                    }
                    // Inhalt der Datei lesen und zwischenspeichern
                    // echo '>> '.$file.'<br />';
                    // echo '>> '.$newfile.'<br />';
                    // echo '>> ' . $filename . '<br />'; #exit;
                    $tmp = file_get_contents($newfile);
                    // Versionsnummer aus dem Dateiinhalt, bzw. CVS-Header auslesen
                    preg_match('#\$Revision\:[[:space:]]*([0-9\.]*)[[:space:]]*\$#i', $tmp, $matches);
                    if (empty($matches[1])) {
                        // alte Version des CVS-Headers
                        //preg_match('#\$Id\:.*\.(?:php|js|inc|htc|css|html),v[[:space:]]*([0-9\.]*)[[:space:]]*.*\$#i', $tmp, $matches);
						
						preg_match('#\$Id\:.*\.(?:php|js|inc|htc|css|html)[[:space:]]*([0-9\.]*)[[:space:]]*.*\$#i', $tmp, $matches);
                        if (empty($matches[1])) {
                            // oder halt sonst was wie eine Versionsnummer aussieht
                            preg_match('#((?:[0-9]+[:.-])+[0-9]+)#', $tmp, $matches);
                        }
                    }
                    // vorher alle Leer und unbestimmte Sonderzeichen entfernen
                    $tmp = str_replace(chr(0), '', $tmp);
                    $tmp = preg_replace('#[^0-9a-zA-Z<>\\\\`"\'/\$\#\*\?\+\^&@!,.;:~\[\]\{\}\(\)%=_-]#', '', $tmp);
                    // den md5 String des Dateinhalts im Ausgabearray zwischenspeichern
                    $files[$filename]['md5'] = md5($tmp);
                    // falls keine Versionsinfo vorhanden, diese auf 0 setzen
                    $tmp = (empty($matches[1])) ? '0' : $matches[1];
                    $tmp = str_replace(array(':', '-'), '.', $tmp);
                    // unnütze nullen am ende entfernen
                    $tmp = preg_replace('#(.[0-9+])\.0+$#', '$1', $tmp);
                    // die ermittelte Version ebenfalls im Ausgabearray speichern
                    $files[$filename]['version'] = $tmp;
                    break;

                case $init && !in_array($file, $this->rootfolders):
                    /* in der ersten Ebene nur bestimmte Ordner erfassen*/
                    // die Dateien in der ersten Ebene werden im Case vorher behandelt!!
                    break;

                default:
                    /* Ordner rekusiv durchlaufen, das Ausgabearray entsprechend erweitern */
                    $files = array_merge($files, $this->_get_dir_contents($newfile));
            }
        }
        return $files;
    }

    /**
     * versioncheck_admin::_get_version_info()
     * ermittelt die Referenzdaten anhand der remote Datei auf pragmaMx.org
     * kann diese nicht eingelesen werden, wird die lokale Datei verwendet
     *
     * @return array $sysver
     */
    protected function _get_version_info()
    {
        /* interne Variablen initialisieren */
        $sysver = array();
        $file_contents = '';
        $case = '';

        /* Cache initialisieren */
        $cacheid = __METHOD__ . PMX_VERSION_NUM;
        $cache = load_class('Cache');

        switch (true) {
            /* Ist was im Cache? */
            case (($sysver = $cache->read($cacheid)) !== false):
                $case = 'cache';
                break;

            case function_exists('curl_init'):
                // wenn möglich die curl-Biblithek verwenden
                $case = 'curl';
                $ch = curl_init();
                $timeout = 20; // set to zero for no timeout
                curl_setopt ($ch, CURLOPT_URL, $this->verfile);
                curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                $file_contents = curl_exec($ch);
                curl_close($ch);
                break;

            case function_exists('fsockopen'):
                // ansonsten fsockopen() verwenden
                pmxDebug::pause();
                $case = 'socket';
                $errno = '';
                $errstr = '';
                $v_file = parse_url($this->verfile);
                $v_file['host'] = strtolower($v_file['host']);
                $fp = fsockopen($v_file['host'], 80, $errno, $errstr, 15);
                if ($fp) {
                    if (!isset($v_file['query'])) {
                        $v_file['query'] = "";
                    }
                    fputs($fp, "GET " . $v_file['path'] . "?" . $v_file['query'] . " HTTP/1.0\r\n");
                    fputs($fp, "HOST: " . $v_file['host'] . "\r\n\r\n");
                    while (!feof($fp)) {
                        $pagetext = fgets($fp, 16777216);
                        $file_contents .= trim($pagetext);
                    }
                    fputs($fp, "Connection: close\r\n\r\n");
                    fclose($fp);
                } else {
                    $this->_errormessage[] = sprintf(_VERCHECK_16, basename(dirname($this->verfile)) . '/' . basename($this->verfile)) ;
                    $this->_errormessage[] = _VERCHECK_18 . '&nbsp;&nbsp;' . $errno . ', ' . $errstr . ' ';
                }
                pmxDebug::restore();
                break;

            case mxIniGet('allow_url_fopen') && $file_contents = file_get_contents($this->verfile):
                $case = 'fopen';
                break;

            case mxIniGet('allow_url_include') && include($this->verfile):
                $case = 'include';
                $sysver = unserialize($sysver);
                break;

            default:
                $case = 'none';
                $this->_errormessage[] = 'no url-wrapper found for: ' . $this->verfile;
        }

        /* externe Datei konnte eingelesen werden */
        if ($file_contents && stripos($file_contents, '404 Not Found') === false) {
            if (preg_match("#='([^']*)'#", $file_contents, $matches)) {
                $sysver = unserialize($matches[1]);
            }
        }
        if ($sysver && isset($sysver['files'])) {
            $this->checkfile = $this->verfile;
        }

        /* externe Datei konnte NICHT eingelesen werden, oder war falsches Format */
        if (!$sysver || !isset($sysver['files'])) {
            $this->_errormessage[] = sprintf(_VERCHECK_16, basename(dirname($this->verfile)) . '/' . basename($this->verfile)) ;
        }
        /* versuchen, interne Datei einzulesen */
        if ((!$sysver || !isset($sysver['files'])) && file_exists($this->verlocal) && include($this->verlocal)) {
            $sysver = unserialize($sysver);
            if ($sysver && isset($sysver['files'])) {
                $this->checkfile = $this->verlocal;
            }
        }
        /* interne Datei konnte auch NICHT eingelesen werden */
        if (!$sysver || !isset($sysver['files'])) {
            $this->_errormessage[] = sprintf(_VERCHECK_19, mx_strip_sysdirs($this->verlocal));
        }

        /* Fehlermeldungen optimieren */
        if ($this->_errormessage) {
            $this->_errormessage[] = 'case: ' . $case;
            $this->_errormessage = array_unique($this->_errormessage);
        }

        /* Cache schreiben */
        if ($sysver && $case != 'cache') {
            $cache->write($sysver, $cacheid);
        }

        return $sysver;
    }
}

if (MX_IS_ADMIN) {
    /* das stellt sicher, dass die Klasse auch in create-cvs.php verwendet werden kann ;-)  */
    $tmp = new versioncheck_admin($op);
    $tmp = null;
}

?>