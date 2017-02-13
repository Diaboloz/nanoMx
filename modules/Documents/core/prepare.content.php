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
 * $Revision: 37 $
 * $Author: PragmaMx $
 * $Date: 2015-07-28 14:58:55 +0200 (Di, 28. Jul 2015) $
 * utf-8: äöüß
 */

$hook = function($module_name, $dummy, &$content)
{
    /* $dummy wird hier nicht übergeben, ist also immer leer */

    $bookmodule = $module_name;

    if (MX_MODULE != $bookmodule) {
        $search = array();
        $book = load_class('Book', $bookmodule);
        $book->module_name = $bookmodule;
        $config = $book->getConfig();
        if ($config['link_other'] == 1) {
            $search2 = $book->getBookSections(0);
            
            foreach ($search2 as $key => $value) {
                $title = html_entity_decode(trim($value['title']), ENT_COMPAT | ENT_HTML5, 'UTF-8');
                $search[$title] = "<a class=\"doculinkext\" href=\"modules.php?name=$bookmodule&amp;act=page&amp;id=" . $value['id'] . "\" title=\"" . $bookmodule . " - " . $value['title'] . "\" >" . $value['title'] . "</a>";
            }
            pmxHeader::add_style("modules/$bookmodule/style/style.css");

            $content = mxChangeContent($content, $search, $config['link_count']);
            unset ($search, $search2);
        }

        /* ersetzungen in den Texten */

        $output = array("id", "title", "text1");

        $book->setFilter("tree", "publish", "=", "1");
        $filter = $book->getFilter("tree");
        $liste = $book->getRecordList(1, $output, "tree");
        foreach ($liste as $item) {
            $search['{'.$module_name.'|link|' . $item['id'] . '}'] = '<a href="modules.php?name=' . $bookmodule . '&amp;act=page&amp;id=' . $item['id'] . '" title="' . $item['title'] . '">' . $item['title'] . '</a>';
            $search['{'.$module_name.'|content|' . trim($item['id']) . '}'] = '<blockquote cite="'.MX_BASE_URL.'modules.php?name=' . $bookmodule . '&amp;act=page&amp;id=' . $item['id'] . '" >
				<a class="doculinkext" href="modules.php?name=' . $bookmodule . '&amp;act=page&amp;id=' . $item['id'] . '" title="' . $item['title'] . '">' . $item['title'] . '</a>
				<p>
				'.$item['text1'].'</p></blockquote>';
        }
		$search[$bookmodule] = "<a class=\"doculinkext\" href=\"modules.php?name=$bookmodule\" title=\"" . $bookmodule . "\" >" . $bookmodule . "</a>";
        $content = mxChangeContent($content, $search);
        unset ($liste, $item, $book);
    }
} ;

?>