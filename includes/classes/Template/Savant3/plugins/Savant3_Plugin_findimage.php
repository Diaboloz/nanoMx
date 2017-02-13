<?php
/**
 * This file is part of
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Revision: 6 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (Mi, 08. Jul 2015) $
 *
 * this file based on:
 * Plugin to generate an <img ... /> tag.
 *
 * @package Savant3
 * @author Paul M. Jones <pmjones@ciaweb.net>
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * Support for alpha transparency of PNG files in Microsoft IE added by
 * Edward Ritter; thanks, Edward.
 */

class Savant3_Plugin_findimage extends Savant3_Plugin {
    /**
     * The base directory for images within the document root.
     *
     * @access public
     * @var string
     */
    protected $imageDir = 'images';

    /**
     * Zwischenspeicher fuer bereits generierte Grafiken
     *
     * @access private
     * @var array
     */
    private static $__pool = array();

    /**
     * Savant3_Plugin_image::__findpath()
     *
     * @param mixed $image
     * @return
     */
    function findimage($image, $templatedir = null)
    {
        $print = false;
        if (is_null($templatedir)) {
            $templatedir = $this->Savant->getConfig('create_template_path');
        }
        // PMX_BASE_PATH
        $details = pathinfo($image);
        $imagename = $details['basename'];
        $imagequery = '';
        if (strpos($imagename, '?')) {
            $tmp = explode('?', $imagename);
            $imagename = $tmp[0];
            $imagequery = '?' . str_replace('&amp;', '&', $tmp[1]);
        }

        $pathes = array();

        /* check, ob gleiches Bild bereits bearbeitet */
        /*
        if (isset(self::$__pool[$image])) {
            $info = self::$__pool[$image]['info'];
            $image = self::$__pool[$image]['path'];
        } else {
            $path = $this->Savant->find_image($image);
            $image = self::$__pool[$image]['path'];
        }
        */
        switch (true) {
            /* Die indexe der Pfade sind nach ihrer Wertigkeit aufsteigend */
            case ($image[0] === '.' && strpos($templatedir, PMX_MODULES_DIR) === 0):
                /* Module */
                $namepos[0] = strlen(PMX_MODULES_DIR) + 1 ;
                $namepos[1] = strpos($templatedir, DS, $namepos[0]);
                $pathes[0] = realpath(PMX_IMAGE_DIR);
                $pathes[1] = realpath(PMX_IMAGE_DIR . DS . 'custom');
                $pathes[2] = substr($templatedir, 0, $namepos[1] + 1) . 'images';
                $pathes[3] = realpath($pathes[2] . DS . 'custom');
                if (defined('MX_THEME')) {
                    if ($pathes[4] = realpath(PMX_THEMES_DIR . DS . MX_THEME . DS . 'images' . substr(substr($templatedir, 0, $namepos[1]), strlen(PMX_REAL_BASE_DIR)))) {
                        $pathes[5] = realpath($pathes[4] . DS . 'custom');
                    }
                }
                /* der Bildname ist hier der komplette Pfad */
                $imagename = $image;
                // $print = true;
                break;

            case empty($details['dirname']):
            case $details['dirname'] == '.':
            case $details['dirname'] == PMX_IMAGE_DIR:
            case $details['dirname'] . '/' == PMX_IMAGE_PATH:
                $pathes[0] = PMX_IMAGE_DIR;
                $pathes[1] = $pathes[0] . DS . 'custom';
                if (defined('MX_THEME')) {
                    if ($pathes[2] = realpath(PMX_THEMES_DIR . DS . MX_THEME . DS . 'images')) {
                        $pathes[3] = $pathes[2] . DS . 'custom';
                    }
                }
                break;

            default:
                // case ($image[0] != '.'):
                /* alle anderen */
                $pathes[0] = PMX_REAL_BASE_DIR . DS . $details['dirname'];
                $pathes[1] = realpath($pathes[0] . DS . 'custom');
                if (defined('MX_THEME') && strpos($details['dirname'], 'images') === 0) {
                    if ($pathes[2] = realpath(PMX_THEMES_DIR . DS . MX_THEME . DS . $details['dirname'])) {
                        $pathes[3] = realpath($pathes[2] . DS . 'custom');
                    }
                }
                // $print = true;
                break;
        }

        if ($pathes) {
            $pathes = array_unique(array_filter($pathes));
            krsort($pathes);
            foreach ($pathes as $key => $image) {
                if ($image = realpath($image . DS . $imagename)) {
                    /* Windows Backslash austauschen */
                    $image = str_replace(DS, '/', $image);
                    break;
                }
            }
        }

        if ($print)
            mxDebugFuncVars($image, $pathes, $templatedir);

        return $image . $imagequery;
    }
}

?>