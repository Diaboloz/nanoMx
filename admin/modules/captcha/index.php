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
 * $Revision: 109 $
 * $Author: PragmaMx $
 * $Date: 2016-02-24 08:07:05 +0100 (Mi, 24. Feb 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

load_class('Captcha', false);

class captcha_admin extends pmxCaptcha {
    /**
     * captcha_admin::__construct()
     *
     * @param mixed $op
     */
    public function __construct($op)
    {
        if (!mxGetAdminPref('radminuser')) {
            return mxRedirect(adminUrl(), 'Access Denied');
        }

        parent::__construct();

        /* Sprachdatei auswählen */
        mxGetLangfile(__DIR__);

        switch ($op) {
            case PMX_MODULE . '/save':
                $this->_save();
                break;
            default :
                $this->_view();
                break;
        }
    }

    /**
     * captcha_admin::_save()
     * Speichern der Captcha Einstellungen
     *
     * @return
     */
    private function _save()
    {
        if (!empty($_POST['do_html_opt_reset'])) {
            /* Standardwerte einlesen */
            $defaults = $this->_defaultvalues();
            $conf = $defaults;
        } else {
            /* aktuelle Config einlesen */
            include($this->_settingsfile);
            $pvs = array_merge(get_defined_vars(), $_POST);

            /* Standardwerte einlesen */
            $defaults = $this->_defaultvalues();

            /* numerische Werte korrigieren */
            foreach ($defaults as $key => $value) {
                switch (true) {
                    case is_numeric($value):
                    case is_bool($value):
                        settype($defaults[$key], 'float');
                        // settype($pvs[$key], 'float');
                }
            }

            /* $_POST überschreibt aktuelle config und diese überschreibt Standardwerte */
            $conf = array_intersect_key($pvs, $defaults);
            $conf = array_merge($defaults, $conf);
        }

        /* eventuelle Leerzeichen aus charstouse entfernen */
        $conf['charstouse'] = preg_replace('#\s#u', '', $conf['charstouse']);
        /* versch. Werte korrigieren */
        $conf['charstouse'] = (!$conf['charstouse'] || !trim($conf['charstouse'])) ? $defaults['charstouse'] : $conf['charstouse'];
        $conf['passphraselenght'] = ($conf['passphraselenght'] > 0) ? $conf['passphraselenght']: $defaults['passphraselenght'];
        $conf['imagewidth'] = ($conf['imagewidth'] < 20) ? $defaults['imagewidth'] : $conf['imagewidth'];
        $conf['imageheight'] = ($conf['imageheight'] < 20) ? $defaults['imageheight'] : $conf['imageheight'];
        $conf['fontsize'] = ($conf['fontsize'] < 20) ? $defaults['fontsize'] : $conf['fontsize'];
		$conf['angle'] = (!is_numeric($conf['angle'])) ? $defaults['angle'] : $conf['angle'];
        $conf['bgintensity'] = (!is_numeric($conf['bgintensity'])) ? $defaults['bgintensity'] : $conf['bgintensity'];
		$conf['bgfonttype'] = (!is_numeric($conf['bgfonttype'])) ? $defaults['bgfonttype'] : $conf['bgfonttype'];
		$conf['scratchamount'] = (!is_numeric($conf['scratchamount'])) ? $defaults['scratchamount'] : $conf['scratchamount'];
		$conf['minsize'] = (!is_numeric($conf['minsize'])) ? $defaults['minsize'] : $conf['minsize'];

        $conf['bgfonttype'] = ($conf['bgfonttype'] < 1) ? 1 : $conf['bgfonttype'];
        $conf['filtertype'] = (!$conf['filtertype'] || !preg_match('#^(Wavy|Bubbly|Breaktype)$#', $conf['filtertype'])) ? $defaults['filtertype'] : $conf['filtertype'];
        $conf['minsize'] = ($conf['minsize'] >= 10) ? $conf['minsize']: 10;

        /* den inhalt der Settings-Datei zusammenstellen */
        $content = "<?php\n";
        $content .= "/**\n";
        $content .= " * pragmaMx - Web Content Management System\n";
        $content .= " * Copyright by pragmaMx Developer Team - http://www.pragmamx.org\n";
        $content .= " * written with: \$Id: index.php 109 2016-02-24 07:07:05Z PragmaMx $\n";
        $content .= " */\n\n";
        $content .= "defined('mxMainFileLoaded') or die('access denied');\n\n";

        foreach ($defaults as $key => $defaultvalue) {
            switch (true) {
                case is_numeric($defaultvalue):
                case is_bool($defaultvalue):
                    $content .= "\$" . $key . " = " . $conf[$key] . ";\n";
                    break;
                case is_scalar($defaultvalue):
                    $content .= "\$" . $key . " = '" . $conf[$key] . "';\n";
                    break;
                // case self::is_assoc($defaultvalue) && self::is_assoc($conf[$key]):
                // $tmp = array();
                // foreach ($conf[$key] as $xkey => $xvalue) {
                // $tmp[] = "'$xkey'=>'$xvalue'";
                // }
                // $content .= "\$" . $key . " = array(" . implode(',', $tmp) . ");\n";
                // break;
                // case is_array($defaultvalue):
                // $content .= "\$" . $key . " = array('" . implode("','", $conf[$key]) . "');\n";
                // break;
                default:
                    $content .= "\$" . $key . " = '" . serialize($conf[$key]) . "';\n";
            }
        }
        $content .= "\n?>";

        /* Settings schreiben: */
        $ok = mx_write_file($this->_settingsfile, $content, true);

        /* error > exit */
        if (!$ok) {
            return mxRedirect(adminUrl(PMX_MODULE), _ADMIN_SETTINGNOSAVED, 5);
        }

        include_once(PMX_SYSTEM_DIR . DS . 'mx_reset.php');
        resetPmxCache();

        return mxRedirect(adminUrl(PMX_MODULE), _ADMIN_SETTINGSAVED, 1);
    }

    /**
     * captcha_admin::_view()
     * Anzeige des Formulars
     *
     * @return
     */
    private function _view()
    {
        /* Konfigurationsfallback... */
        $defaults = $this->_defaultvalues();
        extract($defaults);

        include($this->_settingsfile);

        /* die aktuellen Einstellungen */
        $settings = get_defined_vars();

        /* Einstellungen aus den Modul-Hooks... */
        $hookvars = $this->_get_hook_vars();

        /* prüfen ob Captchas überhaupt möglich sind */
        $check = $this->_check();

        /* diese Instanz für das Captcha Beispielbildchen */
        $captcha_object = $this;

        /* für Captcha Beispiel die Instanz aktivieren */
        $this->set_active();

        /* Template initialisieren */
        $template = load_class('Template');
        $template->init_path(__DIR__);

        $template->assign($settings);
        $template->assign(compact('hookvars', 'check', 'captcha_object'));

        /* aufräumen, Speicher sparen */
        unset($settings, $hookvars, $check, $captcha_object);

        include('header.php');
        $template->display('form.html');
        include('footer.php');
    }

    /**
     * captcha_admin::_check()
     * Prüfung ob Captchas vom Server unterstützt
     *
     * @return
     */
    private function _check()
    {
        switch (true) {
            case !function_exists('gd_info'):
            case !function_exists('imagefontwidth'):
            case !function_exists('imagecolorallocate'):
            case !function_exists('imagestring'):
            case !function_exists('imagejpeg'):
                return _CAPTCHAERR_MISSINGGD;
            case !function_exists('imagettfbbox'):
            case !function_exists('imagettftext'):
                return _CAPTCHAERR_FALSEFT;
            case !($gd_info = gd_info()):
            case !is_array($gd_info):
            case !isset($gd_info['GD Version']):
                return _CAPTCHAERR_FALSEGD;
            case !($gd_info['GD_Version'] = preg_replace('#[^0-9.]#', '', $gd_info['GD Version'])):
            case version_compare($gd_info['GD_Version'], '2.0', '<'):
                return _CAPTCHAERR_FALSEGD;
                // Ab PHP 5.3.0: $gd_info['JPEG Support'] !!
            case empty($gd_info['JPG Support']) && empty($gd_info['JPEG Support']):
                return _CAPTCHAERR_NOJPG;
            case !$gd_info['FreeType Support']:
            case !$gd_info['FreeType Linkage'] == 'with freetype':
                return _CAPTCHAERR_MISSINGFT;
            default:
                return true;
        }
    }
}

$tmp = new captcha_admin($op);
$tmp = null;

?>