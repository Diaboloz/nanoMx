<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: PragmaMx $
 * $Revision: 6 $
 * $Date: 2015-07-08 09:07:06 +0200 (mer. 08 juil. 2015) $
 *
 * this file based on
 * theme-engine from pragmaMx - Content Management System
 * Copyright (c) 2005 pragmaMx Dev Team - http://pragmaMx.org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * ersetzt die php-Variablen und Konstanten innerhalb des Templates
 * Beispiele:  {$sitename} {_HOME}
 */
function mxb_theme_replace_vars($content, $searcharray = array())
{
    $pattern = "#\{([\$]?)([[:alpha:]0-9_]*)}#s";

    if (preg_match_all($pattern, $content, $matches)) {
        if (isset($matches[2])) {
            if (!count($searcharray)) {
                $searcharray = $GLOBALS;
            }
            foreach($matches[2] as $key => $vari) {
                if ($matches[1][$key] == '$') {
                    if (isset($searcharray[$vari])) {
                        $search[$key] = $matches[0][$key];
                        $replace[$key] = $searcharray[$vari];
                    }
                } else if (defined($vari)) {
                    $search[$key] = $matches[0][$key];
                    $replace[$key] = constant($vari);
                }
            }
            if (isset($search)) {
                $content = str_replace($search, $replace, $content);
            }
        }
    }
    return $content;
}

/**
 * Extract and return block '$part_name' from the template, the part is replaced by $subst
 */
function mxb_theme_extract_part(&$template, $part_name, $subst = '')
{
    $pattern = "#(<!-- START $part_name -->)(.*?)(<!-- END $part_name -->)#s";
    if (!preg_match($pattern, $template, $matches)) {
        die('<h4>Template error</h4><p>Failed to find theme part \'' . $part_name . '\'<br />(' . htmlspecialchars($pattern) . ')</p><hr /><p>in generated html-source:</p><pre>' . htmlspecialchars($template) . '</pre>');
    }
    $template = str_replace($matches[1] . $matches[2] . $matches[3], $subst, $template);
    return $matches[2];
}

/**
 * Extract and return optional block '$part_name' from the template, the part is replaced by $subst
 */
function mxb_theme_extract_optional_part(&$template, $part_name, $subst = '')
{
    $pattern = "#(<!-- START $part_name -->)(.*?)(<!-- END $part_name -->)#s";
    if (!preg_match($pattern, $template, $matches)) {
        return '';
    }
    $template = str_replace($matches[1] . $matches[2] . $matches[3], $subst, $template);
    return $matches[2];
}

/**
 * Extract and return block '$part_name' from the template, the part is replaced by $subst
 */
function mxb_theme_extract_comments(&$template)
{
    $pattern = "#(<!-- START comment -->)(.*?)(<!-- END comment -->)#s";
    if (preg_match_all($pattern, $template, $matches)) {
        $template = str_replace($matches[0], '', $template);
    }
    return $matches;
}

/**
 * bestimmte Texte, vor allem Image-Pfade, die ersetzt werden sollen, definieren (suche/ersetze).
 */
function mxb_theme_replace_parts($string, $part)
{
    // wenn nix zu ersetzen, wieder zurueck
    if (!count($part)) return $string;
    // array fuer ersetzungsvorgang fuellen
    foreach ($part as $key => $item) {
        if ('preg' !== $key) { // preg rausnehmen
            $searches[$key] = $item[0];
            $replaces[$key] = $item[1];
        }
    }
    // ersetzen und das Ergebnis in das template-Array speichern
    if (isset($searches)) {
        $string = str_replace($searches, $replaces, $string);
    }
    // die oben definierten Elemente per preg_replace ersetzen
    if (isset($part['preg'])) {
        // alte Arrays wieder loeschen
        $searches = array();
        $replaces = array();
        // array fuer ersetzungsvorgang fuellen
        foreach ($part['preg'] as $i => $item) {
            $searches[$i] = $item[0];
            $replaces[$i] = $item[1];
        }
        // ersetzen und Ausgabe in das template-Array speichern
        if (count($searches)) {
            $string = preg_replace($searches, $replaces, $string);
        }
    }
    return $string;
}

?>
