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
 * Author: Olaf Herfurth / TerraProject  http://www.tecmu.de
 *
 * $Revision: 6 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (Mi, 08. Jul 2015) $
 */

$hook = function($module_name, $options, &$entries)
{
    global $prefix;

    switch (true) {

        case $options['type'] === 'modules':
            $book = load_class('Book', $module_name);
            return $entries[$module_name] = $book->getModuleTitle();

        case $options['type'] === 'items':

            extract($options);

            $book = load_class('Book', $module_name);
            
            
            $nodes = $book->getRecords_LastChange(200, $limit);
            
            if ($nodes && is_array($nodes)) {
                foreach ($nodes as $node) {
                    $entries[] = array(/* Moduldaten */
                        'loc' => "modules.php?name=" . $module_name . "&act=page&id=" . $node['id'],
                        'lastmod' => $node['date_edit'],
                        'text' => $node['text1'] . ' ' . $node['text2'] . ' ' . $node['keywords'],
                        );
                }
            }
    }
} ;

?>