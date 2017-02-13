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

$hook = function($module_name, $options, &$entries)
{
    global $prefix;
	$book = load_class('Book', $module_name);
	
    switch (true) {
        case $options['type'] === 'modules':
            return $entries[$module_name] = $module_name;

        case $options['type'] === 'items':

            extract($options);

			//$book->module_name = $module_name;			
			$items=$book->getRecords_New(999, 99, 0);
			
            
            //if (count($items)) {
                foreach ($items as $docs) {
                    $item['module'] = $module_name;
                    $item['link'] = 'modules.php?name=' . $module_name . '&act=page&id=' . $docs['id'];
                    $item['description'] = $docs['text1'] ;
					$item['date']=$docs['date_edit'];
					$item['title']=$docs['title'];

                    $entries[] = $item;
                }
            //}
    }
} ;

?>