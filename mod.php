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

switch (@basename($_SERVER['REQUEST_URI'])) {
    case '';
        // falls leer, einfach nix machen
        break;
    case 'register-me.html':
        $_GET = array('name' => 'User_Registration');
        break;
    case 'myaccount.html':
    case 'log-me-in.html':
        $_GET = array('name' => 'Your_Account');
        break;
    case 'log-me-out.html':
        $_GET = array('name' => 'Your_Account', 'op' => 'logout');
        break;
    case 'mydata.html':
        $_GET = array('name' => 'Your_Account', 'op' => 'edituser');
        break;
    case 'mysettings.html':
        $_GET = array('name' => 'Your_Account', 'op' => 'edithome');
        break;
    default:

        preg_match('#^' . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/((.+)\.html)$#' . ((PHP_OS == "WINNT" || PHP_OS == "WIN32") ? 'i' : ''), $_SERVER['REQUEST_URI'], $matches);

        /*
         abfangen, ob eine html Datei mit gleichem Namen evtl. existiert
         falls ja, diese anzeigen und dann das script beenden
         */
        if (!empty($matches[1]) && is_file($matches[1])) {
            echo file_get_contents($matches[1]);
            exit;
        }

        /*
         falls eine nicht existierende .html Datei in einem Unterordner aufgerufen wird
         die Seite umleiten mit den gleichen Aufruf, aber ohne den Unterordnernamen
         */
        if (!empty($matches[1]) && (dirname($_SERVER['REQUEST_URI']) != dirname($_SERVER['PHP_SELF']))) {
            $newurl = array_pop(explode('/', $matches[1]));
            $newurl = dirname($_SERVER['PHP_SELF']) . '/' . $newurl;
            @header("HTTP/1.0 404 Not Found");
            @header("Status: 404 Not Found");
            @header("location: $newurl");
            exit;
        }

        /*
         die in dem URL eingebetteten Scriptparameter extrahieren
         dies muss zuerst passieren, damit diese Parameter von Anfang an bereit stehen
         */
        if (preg_match_all('![&\?]([[:alnum:]_]*)=([[:alnum:]_-]*)!', $_SERVER['REQUEST_URI'], $para)) {
            foreach($para[1] as $i => $key) {
                if (!isset($_GET[$key])) {
                    $_GET[$key] = $para[2][$i];
                }
            }
        }
        unset($para);
}

/* Anhand dieser Konstante wird erkannt, dass mod_rewrite im Einsatz ist */
define('PMXMODREWRITE', true);

include_once('modules.php');

?>