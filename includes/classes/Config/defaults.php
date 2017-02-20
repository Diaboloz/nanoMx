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

// TODO: guggen dass nicht immer die config.php benötigt wird.
// TODO: auch sectionsweise ausgeben $section = xx



/* In anonyme Funktion gekapselt um den namensraum nicht zu belasten ;-) */
$values = function($section = NULL)
{
    $defaults['pmx.seo'] = array (/*  */
        'metakeywords' => array ('nanoMx', 'CMS'),
        'modrewrite' => array (/*  */
            'anony' => false,
            'users' => false,
            'admin' => false,
            ),
        'modrewriteextend' => false,
        'sitemap' => true,
        'sitemapcache' => 2,
        'sitemapkeywords' => array('nanoMx', 'php'),
        'sitemaplimit' => 30,
        'sitemapexmod' => array('_blank', 'blank_Home', 'User_Registration', 'Userinfo'),
        );

    $defaults['pmx.rss'] = array (/*  */
        'title' => 'your title',
        'description' => 'your description',
        'logo' => 'images/logo.gif',
        'cachetime' => 2,

        'max_limit' => 30,
        'max_desclen' => 400,
        'active_sections' => array(),

        'default_limit' => 15,
        'default_desclen' => 200,
        'default_format' => 'RSS',
        'default_sections' => array(),

        'header_sections' => array(),
        'header_sections_join' => 1,
        );
	
	$defaults['pmx.themes'] = array ( /*  */
		'defaulttheme' => 'light-pmx',
		'admintheme' => 'admin-pmx',
		'mobiletheme' => 'light-pmx',
		'themes' => array(),
		);
	
    /* die alten Werte aus der config.php berücksichtigen
     * falls diese noch vorhanden sind
     */
    $baseconfigfile = realpath(__DIR__ . '/../../../config.php');
    if ($baseconfigfile && include($baseconfigfile)) {
        if (isset($metakeywords)) {
            if (!is_array($metakeywords)) {
                // das ist jetzt ein Array und kein String mehr...
                $metakeywords = preg_split('#\s*[;,]\s*#', $metakeywords);
            }
            $defaults['pmx.seo']['metakeywords'] = $metakeywords;
        }
        if (isset($mxUseModrewrite)) {
            $defaults['pmx.seo']['modrewrite'] = $mxUseModrewrite;
        }
        if (isset($mxModrewriteExtend)) {
            $defaults['pmx.seo']['modrewriteextend'] = $mxModrewriteExtend;
        }
        if (isset($backend_title)) {
            $defaults['pmx.rss']['title'] = $backend_title;
        } else if (isset($sitename)) {
            $defaults['pmx.rss']['title'] = $sitename;
        }
        if (isset($slogan)) {
            $defaults['pmx.rss']['description'] = $slogan;
        }
        if (isset($backend_logo)) {
            $defaults['pmx.rss']['logo'] = $backend_logo;
        }
        if (isset($backend_limit)) {
            $defaults['pmx.rss']['default_limit'] = $backend_limit;
        }
        if (isset($backend_itemdescriptiontrunk)) {
            $defaults['pmx.rss']['default_desclen'] = $backend_itemdescriptiontrunk;
        }
        if (isset($admintheme)) {
            $defaults['pmx.themes']['admintheme'] = $admintheme;
        }		
        if (isset($Default_Theme)) {
            $defaults['pmx.themes']['defaulttheme'] = $Default_Theme;
        }		
    }

    return $defaults;
} ;

return $values();

?>