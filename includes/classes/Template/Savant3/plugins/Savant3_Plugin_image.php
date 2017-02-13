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

class Savant3_Plugin_image extends Savant3_Plugin {
    /**
     * The base directory for images within the document root.
     *
     * @access public
     * @var string
     */
    protected $imageDir = null;

    /**
     * Check, ob png-Fix fuer aeltere IE noetig ist
     *
     * @access private
     * @var boolean
     */
    private $__iepngfix = null;

    /**
     * Zwischenspeicher fuer bereits generierte Grafiken
     *
     * @access private
     * @var array
     */
    private static $__pool = array();

    /**
     * Outputs an <img ... /> tag.
     *
     * @access public
     * @param string $image The path to the image on the local file system
     * @param string $alt Alternative descriptive text for the image;
     * defaults to the filename of the image.
     * @return string An <img ... /> tag.
     */
    public function image($image, $alt = null, $attr = null)
    {
        /* the image file type code (PNG = IMAGETYPE_PNG) */
        $type = null;

        /* don't attempt to get file info from streams, it takes way too long. */
        $info = false;

        /* get the file information */
        if (strpos($image, '://') === false) {
            /* no "://" in the file, so it's local */
            $path = $this->Savant->findimage($image); // plugin findimage

            $image = PMX_BASE_PATH . substr($path, strlen(PMX_REAL_BASE_DIR) + 1);

            /* check, ob gleiches Bild bereits bearbeitet */
            if (isset(self::$__pool[$path])) {
                $info = self::$__pool[$path];
            } else {
                if (is_file($path)) {
                    $info = @getimagesize($path);
                }
                self::$__pool[$image] = $info;
            }
        }

        if (isset($attr['border'])) {
            $attr['border'] = floatval($attr['border']);
        } else {
            $attr['border'] = 0;
        }

        if (isset($attr['style'])) {
            $styles = explode(';', $attr['style']);
        } else {
            $styles = array();
        }

        /* did we find the file info? */
        if (is_array($info)) {
            /* capture type info regardless */
            $type = $info[2];

            /* TODO: Masseinheit beachten ? */

            switch (true) {
                case is_array($info) && isset($attr['scale-width']):
                    $styles[] = 'width:' . round($attr['scale-width'], 2) . 'px';
                    $styles[] = 'height:' . round($info[1] * ($attr['scale-width'] / $info[0]), 2) . 'px';
                    break;
                case is_array($info) && isset($attr['scale-height']):
                    $styles[] = 'height:' . round($attr['scale-height'], 2) . 'px';
                    $styles[] = 'width:' . round($info[0] * ($attr['scale-height'] / $info[1]), 2) . 'px';
                    break;
                case is_array($info) && isset($attr['shrink-width']) && ($info[0] > $attr['shrink-width']):
                    $styles[] = 'width:' . round($attr['shrink-width'], 2) . 'px';
                    $styles[] = 'height:' . round($info[1] * ($attr['shrink-width'] / $info[0]), 2) . 'px';
                    break;
                case is_array($info) && isset($attr['shrink-height']) && ($info[1] > $attr['shrink-height']):
                    $styles[] = 'height:' . round($attr['shrink-height'], 2) . 'px';
                    $styles[] = 'width:' . round($info[0] * ($attr['shrink-height'] / $info[1]), 2) . 'px';
                    break;
                default:
                    if (isset($attr['width'])) {
                        $styles[] = 'width:' . floatval($attr['width']) . 'px';
                    } else {
                        $styles[] = 'width:' . $info[0] . 'px';
                    }
                    if (isset($attr['height'])) {
                        $styles[] = 'height:' . floatval($attr['height']) . 'px';
                    } else {
                        $styles[] = 'height:' . $info[1] . 'px';
                    }
            }
        } else {
            $tmp = parse_url($image);
            $tmp = pathinfo($tmp['path']);
            if (isset($tmp['extension']) && strtolower($tmp['extension']) === 'png') {
                $type = IMAGETYPE_PNG;
            }
        }

        if ($styles) {
            $attr['style'] = implode(';', $styles);
        } else {
            unset($attr['style']);
        }

        /* add the alt attribute */
        if (is_null($alt)) {
            $alt = explode('.', basename($image));
            $alt = $alt[0];
        }

        /* is the file a PNG? if so, check user agent, we will need to make special allowances for Microsoft IE. */
        if ($type === IMAGETYPE_PNG) {
            /* support alpha transparency for PNG files in MSIE */
            $this->_initpngfix();
            if ($this->__iepngfix === true) {
                /* in der default.css.php, wird die Klasse .png mit dem Filter definiert */
                if (!isset($attr['class'])) {
                    $attr['class'] = 'png';
                } else {
                    $attr['class'] .= ' png';
                }
            }
        }

        /* unnoetige Attribute entfernen */
        unset($attr['scale-width'], $attr['scale-height'], $attr['width'], $attr['height']);

        $html = '<img src="' . htmlspecialchars($image) . '" alt="' . htmlspecialchars($alt, ENT_COMPAT | ENT_HTML5, 'UTF-8', false) . '"' . $this->Savant->htmlAttribs($attr) . ' />';

        return $html;
    }

    /**
     * Savant3_Plugin_image::_initpngfix()
     *
     * @return
     */
    private function _initpngfix()
    {
        if ($this->__iepngfix === null) {
            /* is the file a PNG? if so, check user agent, we will need to make special allowances for Microsoft IE. */
            if (stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE') && preg_match('#MSIE\s[123456].+(Win|Mac)#i', $_SERVER['HTTP_USER_AGENT'])) {
                $this->__iepngfix = true;
                // pmxHeader::add_style_code('img.png { behavior: url(' . PMX_JAVASCRIPT_PATH . 'iepngfix/iepngfix.htc);}');
            } else {
                $this->__iepngfix = false;
            }
        }
    }
}

?>