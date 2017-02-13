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
 * $Revision: 186 $
 * $Author: PragmaMx $
 * $Date: 2016-07-21 07:45:48 +0200 (Do, 21. Jul 2016) $
 */

require_once(dirname(__file__) . DS . 'Upload' . DS . 'class.upload.php');

/**
 * pmxUpload
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2009
 * @version $Id: Upload.php 186 2016-07-21 05:45:48Z PragmaMx $
 * @access public
 */
class pmxUpload extends upload {
    /* Speicher fuer getter und setter... */
    private $_config = array();

    /**
     * pmxUpload::__construct()
     *
     * @param string $file
     */
    public function __construct($file)
    {
        /* Sprache einstellen */
        switch (true) {
            case !empty($this->_config['lang']):
                $lang = $this->_config['lang'];
                break;
            case defined('_LOCALE'):
                $lang = _LOCALE;
                break;
            default:
                $lang = 'en_GB';
        }

        /* Konstruktor der Elternklasse aufrufen */
        parent::__construct($file, $lang);

        /* die Sprachdateien sind leider in utf-8 */
        // if ($this->lang != 'en_GB') {
        // $this->translation = array_map('utf8_decode', $this->translation);
        // }
    }

    /**
     * pmxUpload::__get()
     *
     * @param string $name
     * @return
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_config)) {
            return $this->_config[$name];
        }
        $trace = debug_backtrace();
        trigger_error('undefined property \'' . $name . '\' in ' . mx_strip_sysdirs($trace[0]['file']) . ' line ' . $trace[0]['line'], E_USER_NOTICE);
        return null;
    }

    /**
     * pmxUpload::__set()
     *
     * @param string $name
     * @param mixed $value
     * @return
     */
    public function __set($name, $value)
    {
        $this->_config[$name] = $value;
    }

    /**
     * pmxUpload::set()
     *
     * @param string $name
     * @param mixed $value
     * @return
     */
    public function set($name, $value = null)
    {
        if (is_array($name) && $value === null) {
            foreach ($name as $key => $value) {
                $this->_config[$key] = $value;
            }
        } else {
            $this->_config[$name] = $value;
        }
    }

    /**
     * pmxUpload::fetch_web_data()
     * Inhalte einer Datei im Web auslesen
     *
     * @param mixed $url
     * @param mixed $info
     * @return
     */
    public function fetch_web_data($url, &$info)
    {
        // Die maximale Ausfuehrungszeit in Sekunden
        $timeout = 12; // set to zero for no timeout
        // interne Variablen initialisieren
        $file_contents = "";

        switch (true) {
            case function_exists('curl_init'):
                // wenn moeglich die curl-Biblithek verwenden
                $ch = curl_init();
                curl_setopt ($ch, CURLOPT_URL, $url);
                curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                $file_contents = curl_exec($ch);
                $info = curl_getinfo($ch);
                $info['status'] = $info['http_code'];
                $info['content-type'] = $info['content_type'];
                $info['content-length'] = $info['download_content_length'];
                $info['method'] = 'curl';
                curl_close($ch);
                break;

            case mxIniGet('allow_url_fopen') && $file_contents = @file_get_contents($url):
                // geht ueber file_get_contents?
                $info['method'] = 'file';
                $info['status'] = 200;
                break;

            case function_exists('fsockopen'):
                // ansonsten die herkoemmliche Methode von nuke/pragmaMx verwenden
                $errno = '';
                $errstr = '';
                $file = parse_url($url);
                $file['host'] = strtolower($file['host']);
                if (isset($file['query'])) {
                    $file['query'] .= '?';
                } else {
                    $file['query'] = '';
                }
                /**
                 * following snipped based on:
                 * http://www.php.net/manual/de/function.fsockopen.php
                 * comment from jbr at ya-right dot com, 02-Mar-2007 04:17
                 */
                if (($fp = fsockopen($file['host'], 80, $errno, $errstr, $timeout)) !== false) {
                    $send = "GET " . $file['path'] . $file['query'] . " HTTP/1.1\r\n";
                    $send .= "Host: " . $file['host'] . "\r\n";
                    $send .= "User-Agent: " . MX_USER_AGENT . "\r\n";
                    $send .= "Referer: http://" . $file['host'] . "/\r\n";
                    $send .= "Accept: text/xml,application/xml,application/xhtml+xml,";
                    $send .= "text/html;q=0.9,text/plain;q=0.8,video/x-mng,image/png,";
                    $send .= "image/jpeg,image/gif;q=0.2,text/css,*/*;q=0.1\r\n";
                    $send .= "Accept-Language: en-us, en;q=0.50\r\n";
                    $send .= "Accept-Encoding: gzip, deflate, compress;q=0.9\r\n";
                    $send .= "Connection: Close\r\n\r\n";
                    fputs ($fp, $send);
                    $send = '';
                    do {
                        $send .= fgets ($fp, 4096);
                    } while (strpos ($send, "\r\n\r\n") === false);
                    $info = self::decode_header($send);
                    $file_contents = '';
                    while (! feof ($fp)) {
                        $file_contents .= fread ($fp, 8192);
                    }
                    fclose ($fp);
                    $file_contents = self::decode_body($info, $file_contents);
                }
                $info['method'] = 'socket';
                break;

            default:
                $info['method'] = '';
                trigger_error('no url-wrapper found for: ' . $url, E_USER_NOTICE);
        }
        // $info['content-length'] = $info['content_type'];
        if (empty($info['content-length'])) {
            $info['content-length'] = strlen($file_contents);
        }
        return $file_contents;
    }

    /**
     * pmxUpload::decode_header()
     * Hilfsfunktion zu fetch_web_data(), GET Header parsen
     * based on:
     * http://www.php.net/manual/de/function.fsockopen.php
     * comment from jbr at ya-right dot com, 02-Mar-2007 04:17
     *
     * @param mixed $str
     * @return
     */
    protected function decode_header ($str)
    {
        $part = preg_split ("/\r?\n/", $str, -1, PREG_SPLIT_NO_EMPTY);

        $out = array ();

        for ($h = 0; $h < sizeof ($part); $h++) {
            if ($h != 0) {
                $pos = strpos ($part[$h], ':');
                $k = strtolower (str_replace (' ', '', substr ($part[$h], 0, $pos)));
                $v = trim (substr ($part[$h], ($pos + 1)));
            } else {
                $k = 'status';
                $v = explode (' ', $part[$h]);
                $v = $v[1];
            }

            if ($k == 'set-cookie') {
                $out['cookies'][] = $v;
            } else if ($k == 'content-type') {
                if (($cs = strpos ($v, ';')) !== false) {
                    $out[$k] = substr ($v, 0, $cs);
                } else {
                    $out[$k] = $v;
                }
            } else {
                $out[$k] = $v;
            }
        }

        return $out;
    }

    /**
     * pmxUpload::decode_body()
     * Hilfsfunktion zu fetch_web_data(), GET Inhalte parsen
     * based on:
     * http://www.php.net/manual/de/function.fsockopen.php
     * comment from jbr at ya-right dot com, 02-Mar-2007 04:17
     *
     * @param mixed $info
     * @param mixed $str
     * @param string $eol
     * @return
     */
    protected function decode_body ($info, $str, $eol = "\r\n")
    {
        if (!function_exists('gzinflate')) {
            return $str;
        }
        $tmp = $str;
        $add = strlen ($eol);
        $str = '';
        if (isset ($info['transfer-encoding']) && $info['transfer-encoding'] == 'chunked') {
            do {
                $tmp = ltrim ($tmp);
                $pos = strpos ($tmp, $eol);
                $len = hexdec (substr ($tmp, 0, $pos));
                if (isset ($info['content-encoding'])) {
                    $str .= gzinflate (substr ($tmp, ($pos + $add + 10), $len));
                } else {
                    $str .= substr ($tmp, ($pos + $add), $len);
                }

                $tmp = substr ($tmp, ($len + $pos + $add));
                $check = trim ($tmp);
            } while (! empty ($check));
        } else if (isset ($info['content-encoding'])) {
            $str = gzinflate (substr ($tmp, 10));
        } else {
            $str = $tmp;
        }
        return $str;
    }
}

?>