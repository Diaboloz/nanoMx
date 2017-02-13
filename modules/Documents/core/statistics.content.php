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

$hook = function($module_name, $dummy, &$items)
{
    /* $dummy wird hier nicht übergeben, ist also immer leer */
    /* $items ergänzt die bestehende Liste */

    $book = load_class('Book', $module_name);
    $book->module_name = $module_name;

    $num = count($book->getRecords_RootDocuments(false));
    if ($num) {
        $items[] = array(/* Atttribute */
            'module' => $module_name,
            'url' => "modules.php?name=$module_name",
            'icon' => "modules/$module_name/images/document.png",
            'caption' => $book->getModuleTitle() . " " . _STAT_DOCUMENTS,
            'value' => $num,
            );
    }

    $num = count($book->getRecords_Documents(false));
    if ($num) {
        $items[] = array(/* Atttribute */
            'module' => $module_name,
            'icon' => "modules/$module_name/images/document.png",
            'caption' => ($book->getModuleTitle()) . " " . _STAT_DOCS_CHILDS,
            'value' => $num,
            );
    }

    $book = null;
} ;

?>