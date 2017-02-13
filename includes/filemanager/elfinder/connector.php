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
 * http://elrte.org/redmine/projects/elfinder/wiki/Connector_Configuration_EN
 * https://github.com/Studio-42/elFinder/blob/2.x/php/elFinderVolumeDriver.class.php#L153
 *
 * TODO:
 * - [allow_modify] beachten: Löschen und Umbenennen von Dateien erlauben
 * - Benutzerordner besser konfigurieren, evtl. automatisch erstellen mit uid
 * - Benutzerordner verwenden in  ini Datei
 * - Einstellungen für Anonyme extra in ini Datei
 */

defined('mxMainFileLoaded') or die('access denied');

pmxDebug::pause();

$path = __DIR__ . DS . 'manager' . DS . 'php' . DS;

include_once($path . 'elFinderConnector.class.php');
include_once($path . 'elFinder.class.php');
include_once($path . 'elFinderVolumeDriver.class.php');
include_once($path . 'elFinderVolumeLocalFileSystem.class.php');

/* Required for MySQL storage connector */
// include_once($path.'elFinderVolumeMySQL.class.php');
// include_once($path . 'elFinderVolumeFTP.class.php');
/*  */

pmxDebug::restore();

/**
 * elfinder_connector
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2013
 * @version $Id: connector.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class elfinder_connector {
    private $_parent;

    /**
     * elfinder_connector::__construct()
     *
     * @param mixed $parent_object
     */
    public function __construct($parent_object)
    {
        $this->_parent = $parent_object;
    }

    /**
     * elfinder_connector::get()
     *
     * @return
     */
    public function get()
    {
        /* run elFinder */
        header('Access-Control-Allow-Origin: *');
        $connector = new elFinderConnector(new pmxElfinder($this->_parent));
        $connector->run();
    }
}

/**
 * pmxElfinder
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2012
 * @version $Id: connector.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxElfinder extends elFinder {
    /* Einstellungen der textarea Klasse */
    protected $_config = array();

    /* die Optionen für die Vaterklasse */
    private $__options = array();

    /**
     * pmxElfinder::__construct()
     *
     * @param Filebrowse $ Object $parent_object
     */
    public function __construct($parent_object)
    {
        $this->_config = $parent_object->get_config();

        $title = '';
        $path = '';

        /* Angabe wird in Bytes benötigt ( * 1024 )!! */
        $maxsize = ($this->_config['allow_upload'] && $this->_config['upload_max_size']) ? ($this->_config['upload_max_size'] * 1024) : false;;

        $this->__options['roots'] = array();
        foreach ($this->_config['roots'] as $alias => $path) {
            $cpath = $path . '/';
            $alias = (defined($alias)) ? constant($alias) : $alias;

            /**
             * Object configuration
             * see: elFinder/php/elFinderVolumeDriver.class.php
             */
            $thisroot = array(/* Einstelllungen für jedes einzelne root... */
                'driver' => 'LocalFileSystem', // driver for accessing file system (REQUIRED)
                'path' => '../../../' . $cpath, // Pfad ausgehend von /includes/classes/Filebrowse !!
                'realpath' => realpath('../../../' . $cpath),
                'URL' => PMX_BASE_PATH . $cpath, // URL to files (REQUIRED)
                'alias' => $alias, // The name to replace your actual path name. (OPTIONAL)
                'mimeDetect' => 'internal',
                'tmbPath' => '.tmb',
                'utf8fix' => true,
                'tmbCrop' => false,
                'tmbBgColor' => 'transparent',
                // 'accessControl' => 'elfinder_access', // disable and hide dot starting files (OPTIONAL)
                'accessControl' => array($this, '_access_control'), // disable and hide dot starting files (OPTIONAL)
                'separator' => '/', // directory separator. required by client to show paths correctly
                'uploadMaxSize' => $maxsize, // maximum upload file size. NOTE - this is size for every uploaded files
                );

            $this->_set_uploadprefs($thisroot);
            $this->_set_disabled($thisroot);
            // $this->_set_perms($thisroot);
            $this->_set_locales($thisroot);
            $this->_set_chmod($thisroot);

            $this->__options['roots'][$path] = $thisroot;
        }

        return parent::__construct($this->__options);
    }

    /**
     * pmxElfinder::_set_disabled()
     *
     * @param mixed $thisroot
     * @return
     */
    protected function _set_disabled(&$thisroot)
    {
        $enabled = array();
        $disabled = array(/* list of not allowed commands */
            'open', // - open directory or send file to browser
            'mkdir', // - create new directory
            'mkfile', // - create new text file
            'rename', // - rename directory or file
            'upload', // - upload files
            'ping', // - service command, needed for Safari (file upload)
            'paste', // - make a copy or move files/directories to selected destination
            'rm', // - delete files/directories
            'duplicate', // - duplicate file/directory
            'read', // - get text file content
            'edit', // - save text file content
            'extract', // - extract archive
            'archive', // - compress files/directories into archive
            'tmb', // - create thumbnails for images that don't have them
            'resize', // - resize image
            );

        switch (true) {
            case MX_IS_SYSADMIN:
                // Superadmin darf alles und ueberall hochladen
                $disabled = array();
                break;

            case !$this->_config['allow_upload']:
                $enabled = array(/* list of not allowed commands */
                    'open', // - open directory or send file to browser
                    'read', // - get text file content
                    'tmb', // - create thumbnails for images that don't have them
                    );
                break;

            case $this->_config['allow_upload']:
            default:

                $enabled = array(/* list of allowed commands */
                    'open', // - open directory or send file to browser
                    'read', // - get text file content
                    'tmb', // - create thumbnails for images that don't have them
                    'upload', // - upload files
                    'ping', // - service command, needed for Safari (file upload)
                    );

                if ($this->_config['allow_modify']) {
                    $enabled[] = 'mkdir'; // - create new directory
                    $enabled[] = 'rename'; // - rename directory or file
                    $enabled[] = 'rm'; // - delete files/directories
                }
        }

        $thisroot['disabled'] = array_diff($disabled, $enabled);
    }

    /**
     * pmxElfinder::_set_uploadprefs()
     *
     * @param mixed $thisroot
     * @return
     */
    protected function _set_uploadprefs(&$thisroot)
    {
        switch (true) {
            case MX_IS_SYSADMIN:
                // Superadmin darf alles und ueberall hochladen
                $this->_config['allow_upload'] = true;
                $thisroot['uploadAllow'] = array('all');
                $thisroot['uploadDeny'] = array();
                $thisroot['uploadOrder'] = 'deny,allow';
                break;

            case !$this->_config['allow_upload']:
                $thisroot['uploadAllow'] = array();
                $thisroot['uploadDeny'] = array('all');
                $thisroot['uploadOrder'] = 'deny,allow';
                break;

            case $this->_config['allow_upload']:
            default:

                $allowed = array();
                $groups = Textarea::get_filetypegroups();
                foreach ($this->_config['filetype'] as $group) {
                    if (isset($groups[$group])) {
                        foreach ($groups[$group] as $key => $mime) {
                            $allowed[] = $mime;
                        }
                    }
                }
                if (!$allowed) {
                    // keine Dateitypen, dann auch kein Upload ;)
                    $this->_config['allow_upload'] = false;
                    $thisroot['uploadAllow'] = array();
                    $thisroot['uploadDeny'] = array('all');
                    $thisroot['uploadOrder'] = 'deny,allow';
                    break;
                }
                $thisroot['uploadAllow'] = $allowed;
                $thisroot['uploadDeny'] = array('all');
                $thisroot['uploadOrder'] = 'deny,allow';

                break;
        }
    }

    /**
     * pmxElfinder::_access_control()
     *
     * Simple function to demonstrate how to control file access using "accessControl" callback.
     * This method will disable accessing files/folders starting from  '.' (dot)
     *
     * @param string $attr attribute name (read|write|locked|hidden)
     * @param string $path file path relative to volume root directory started with directory separator
     * @param mixed $data
     * @param mixed $volume
     * @return bool
     */
    public function _access_control($attr, $path, $data, $volume)
    {
        $filename = basename($path);
        switch (true) {
            case strpos($filename, '.') === 0:// if file/folder begins with '.' (dot)
            case $filename == 'CVS': // or if file/folder name is CVS
            case $filename == 'index.html': // or if file/folder name is index.html
                // set read+write to false, other (locked+hidden) set to true
                return !($attr == 'read' || $attr == 'write');
            default:
                // else set read+write to true, locked+hidden to false
                return ($attr == 'read' || $attr == 'write');
        }
    }

    /**
     * pmxElfinder::_set_perms()
     *
     * @param mixed $thisroot
     * @return
     */
    protected function XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX_set_perms(&$thisroot)
    {
        // files attributes
        // 'attributes' => array(),
        // return;
        $thisroot['perms'] = array(/* bestimmte Dateien u. Ordner */
            '#index\.html$#i' => array('read' => false,
                'write' => false,
                'rm' => false
                ),
            '#CVS$#i' => array('read' => false,
                'write' => false,
                'rm' => false
                ),
            // '#userpics$#i' => array('read' => true,
            // 'write' => false,
            // 'rm' => false
            // ),
            );
    }

    /**
     * pmxElfinder::_set_locales()
     *
     * @param mixed $thisroot
     * @return
     */
    protected function _set_locales(&$thisroot)
    {
        switch (_DOC_LANGUAGE) {
            case 'de':
                // files dates format
                $thisroot['dateFormat'] = 'j.m.Y H:i';
                // files time format
                $thisroot['timeFormat'] = 'H:i';
                break ;
            default:
                $thisroot['dateFormat'] = 'j M Y H:i';
                $thisroot['timeFormat'] = 'H:i';
                break ;
        }
    }

    /**
     * pmxElfinder::_set_chmod()
     *
     * @return
     */
    protected function _set_chmod(&$thisroot)
    {
        if ($this->_config['allow_upload']) {
            if ($this->_config['chmod_dir_to']) {
                // new folders mode
                $thisroot['dirMode'] = octdec($this->_config['chmod_dir_to']);
            }
            if ($this->_config['chmod_to']) {
                // new files mode
                $thisroot['fileMode'] = octdec($this->_config['chmod_to']);
            }
        }
    }
}

?>
