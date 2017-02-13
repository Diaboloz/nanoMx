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

$backend = function(&$para)
{
    /* neue Übergabeparameter generieren */
    $para['name'] = 'rss';

    /* veraltete Übergabeparameter konvertieren */
    switch (true) {
        case isset($para['dln']):
            $para['dln'] = intval($para['dln']);
            break;
        case isset($para['idt']) && intval($para['idt']):
            $para['dln'] = intval($para['idt']);
            break;
        case isset($para['len']) && intval($para['len']):
            $para['dln'] = intval($para['len']);
    }

    switch (true) {
        case isset($para['fmt']) && $para['fmt']:
            break;
        case isset($para['format']) && $para['format']:
            $para['fmt'] = $para['format'];
            break;
        case isset($para['version']) && $para['version']:
            $para['fmt'] = $para['version'];
    }

    switch (true) {
        case isset($para['feed']):
            break;
        case isset($para['mod']):
            if ($para['mod'] == 'all') {
                $para['feed'] = 'all';
            } else {
                $para['feed'] = $para['mod'];
            }
            break;
        case empty($para['op']):
            break;
        case $para['op'] == 'all':
            $para['feed'] = 'all';
            break;
        case $para['op'] == 'news':
        case $para['op'] == 'News':
        case $para['op'] == 'story':
            $para['feed'] = 'News';
            break;
        case $para['op'] == 'downs':
        case $para['op'] == 'Downloads':
            $para['feed'] = 'Downloads';
            break;
        case $para['op'] == 'link':
        case $para['op'] == 'links':
        case $para['op'] == 'Web_Links':
            $para['feed'] = 'Web_Links';
            break;
        case $para['op'] == 'calendar':
        case $para['op'] == 'Kalender':
            $para['feed'] = 'Kalender';
            break;
        case $para['op'] == 'board':
        case $para['op'] == 'Board':
        case $para['op'] == 'eboard':
        case $para['op'] == 'eBoard':
        case $para['op'] == 'mxboard':
            $para['feed'] = 'board.Board.eboard.eBoard.mxboard.mxBoard';
            break;
    }

    /* wenn $feed noch nicht definiert, den Standard-Feed setzen */
    if (!isset($para['feed'])) {
        $para['feed'] = '';
    }

    unset($para['fmt'], $para['format'], $para['len'], $para['op'], $para['mod']);
} ;

$backend($_GET);
unset($backend);

return include_once('modules.php');

?>