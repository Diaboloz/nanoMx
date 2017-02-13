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

$hook = function($module_name, $options, &$items)
{
    /* $options enthält nur den Schlüssel $top */
    /* $items ergänzt die bestehende Liste */

    global $prefix, $user_prefix;

    /* die Variable $top extrahieren */
    extract($options, EXTR_SKIP);

    $i_num = 0;

    /**
     * Top 10 read stories
     * $top  enthält die Anzahl der max anzuzeigenden Links 
	*/
    $book = load_class('Book', $module_name);
    $book->module_name = $module_name;
	
	$stories=$book->getRecords_Best( $top);
	$rows= count($stories);
	
    if ($rows > 1) {
        $i_num++;
        $items[$module_name . $i_num]['caption'] = $rows . ' ' . _TOP_DOC_BESTRATEDSTORIES . ' - '.$book->getModuleTitle();
        foreach ($stories as $story) {
            
                $items[$module_name . $i_num]['list'][] = '<a href="modules.php?name=' . $module_name . '&amp;act=page&amp;id=' . $story['id'] . '">' . $story['title'] . '</a> - ('.$story['rating'].' '.')';
            
        }
    }
	
	$stories=$book->getRecords_MostViewed( $top);
	$rows= count($stories);
	
    if ($rows > 1) {
        $i_num++;
        $items[$module_name . $i_num]['caption'] = $rows . ' ' . _TOP_DOC_READSTORIES . ' - '.$book->getModuleTitle();
        foreach ($stories as $story) {
            
                $items[$module_name . $i_num]['list'][] = '<a href="modules.php?name=' . $module_name . '&amp;act=page&amp;id=' . $story['id'] . '">' . $story['title'] . '</a> - ('.$story['views'].' '.')';
            
        }
    }	
}
?>