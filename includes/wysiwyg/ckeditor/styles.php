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

$styles['inline'] = array('caption' => 'inline',
    'type' => 'inline',
    'items' => array('big.bigger',
        'del.del',
        'span.highlight',
        'span.nowrap',
        'span.required',
        'span.smaller',
        ),
    );

$styles['block'] = array('caption' => 'block level',
    'type' => 'block',
    'items' => array('div.alternate-0',
        'div.alternate-1',
        'div.alternate-2',
        'div.alternate-3',
        'div.alternate-4',
        'div.bgcolor1',
        'div.bgcolor2',
        'div.bgcolor3',
        'div.bgcolor4',
        'div.border',
        'div.box',
        'div.code',
        'div.error',
        'div.important',
        'div.indent',
        'div.info',
        'div.middot',
        'div.note',
        'div.quote',
        'div.success',
        'div.warning',
        'p.content',
        ),
    );

$styles['image'] = array('caption' => 'image',
    'type' => 'selector',
    'items' => array('img.align-center',
        'img.align-left',
        'img.align-right',
        'img.border',
        'img.float-left',
        'img.float-right',
        'img.margin',
        ),
    );

$styles['list'] = array('caption' => 'lists',
    'type' => 'selector',
    'items' => array('ol.infolist',
        'ol.list',
        'ul.infolist',
        'ul.list',
        ),
    );

$styles['table'] = array('caption' => 'table',
    'type' => 'selector',
    'items' => array('table.align-center',
        'table.blind',
        'table.fixed',
        'table.full',
        'table.list',
        'td.alternate-a',
        'td.alternate-b',
        'td.alternate-c',
        'td.alternate',
        'td.head',
        'tr.alternate-a',
        'tr.alternate-b',
        'tr.alternate-c',
        'tr.alternate',
        'tr.head',
        ),
    );

foreach ($styles as $key => $value) {
    foreach ($value['items'] as $item) {
        /* normaler style: { name : 'CSS Style', element : 'span', attributes : { 'class' : 'my_style' } } */
        list($selector, $class) = explode('.', $item);
        $parts[] = "{name:'" . $item . "',element:'" . $selector . "',attributes:{'class':'" . $class . "'}}";
    }
}

echo implode(",", $parts);

?>