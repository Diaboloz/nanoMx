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

$modpath = 'modules/' . basename(dirname(__DIR__)) . '/';

$deldirs = array(/* Ordner zum löschen */
    $modpath . 'mxinstall',
    $modpath . 'admin/language',
    );

$delfiles = array(/* Dateien zum löschen */
    'blocks/block-Categories.php',
    'blocks/block-Big_Story_of_Today.php',
    'blocks/block-Last_5_Articles.php',
    'blocks/block-News.php',
    'blocks/block-News_short-2spalten.php',
    'blocks/block-News_short-more-columns.php',
    'blocks/block-News_short.php',
    'blocks/block-Old_Articles.php',
    'blocks/block-vkp_News_Lastarticles.php',
    'blocks/block-vkp_News_Login.php',
    'blocks/block-vkp_News_Options.php',
    'blocks/block-vkp_News_Poll.php',
    'blocks/block-vkp_News_Rating.php',
    'blocks/block-vkp_News_Related.php',
    'blocks/block-vkp_News_Socialshare.php',
    'admin/case/case.stories.php',
    'admin/case/case.topics.php',
    'admin/links/links.submissions.php',
    'admin/modules/comments.php',
    'admin/modules/stories.php',
    'admin/modules/topics.php',
    $modpath . 'core/prepare.header.php',
    $modpath . '.htaccess',
    $modpath . 'admin/case.php',
    $modpath . 'admin/index.php',
    $modpath . 'admin/language/lang-albanian.php',
    $modpath . 'admin/language/lang-brazilian.php',
    $modpath . 'admin/language/lang-catala.php',
    $modpath . 'admin/language/lang-chinese.php',
    $modpath . 'admin/language/lang-czech.php',
    $modpath . 'admin/language/lang-danish.php',
    $modpath . 'admin/language/lang-dutch.php',
    $modpath . 'admin/language/lang-english.php',
    $modpath . 'admin/language/lang-estonian.php',
    $modpath . 'admin/language/lang-euskara.php',
    $modpath . 'admin/language/lang-finnish.php',
    $modpath . 'admin/language/lang-french.php',
    $modpath . 'admin/language/lang-galego.php',
    $modpath . 'admin/language/lang-german.php',
    $modpath . 'admin/language/lang-hungarian.php',
    $modpath . 'admin/language/lang-icelandic.php',
    $modpath . 'admin/language/lang-indonesian.php',
    $modpath . 'admin/language/lang-macedonian.php',
    $modpath . 'admin/language/lang-norwegian.php',
    $modpath . 'admin/language/lang-polish.php',
    $modpath . 'admin/language/lang-portuguese.php',
    $modpath . 'admin/language/lang-romanian.php',
    $modpath . 'admin/language/lang-slovak.php',
    $modpath . 'admin/language/lang-slovenian.php',
    $modpath . 'admin/language/lang-swedish.php',
    $modpath . 'admin/language/lang-thai.php',
    $modpath . 'admin/language/lang-turkish.php',
    $modpath . 'admin/language/lang-ukrainian.php',
    $modpath . 'admin/language/lang-vietnamese.php',
    $modpath . 'admin/links.php',
    $modpath . 'associates.php',
    $modpath . 'core/admin.case.php',
    $modpath . 'functions.php',
    $modpath . 'images/0.gif',
    $modpath . 'images/1m.gif',
    $modpath . 'images/1p.gif',
    $modpath . 'images/e.gif',
    $modpath . 'images/l.gif',
    $modpath . 'images/leer.gif',
    $modpath . 'images/x.gif',
    $modpath . 'images/z.gif',
    $modpath . 'index.html',
    $modpath . 'language/lang-albanian.php',
    $modpath . 'language/lang-brazilian.php',
    $modpath . 'language/lang-catala.php',
    $modpath . 'language/lang-chinese.php',
    $modpath . 'language/lang-czech.php',
    $modpath . 'language/lang-dutch.php',
    $modpath . 'language/lang-estonian.php',
    $modpath . 'language/lang-euskara.php',
    $modpath . 'language/lang-finnish.php',
    $modpath . 'language/lang-galego.php',
    $modpath . 'language/lang-hungarian.php',
    $modpath . 'language/lang-icelandic.php',
    $modpath . 'language/lang-indonesian.php',
    $modpath . 'language/lang-macedonian.php',
    $modpath . 'language/lang-norwegian.php',
    $modpath . 'language/lang-polish.php',
    $modpath . 'language/lang-portuguese.php',
    $modpath . 'language/lang-romanian.php',
    $modpath . 'language/lang-slovak.php',
    $modpath . 'language/lang-slovenian.php',
    $modpath . 'language/lang-swedish.php',
    $modpath . 'language/lang-thai.php',
    $modpath . 'language/lang-ukrainian.php',
    $modpath . 'language/lang-vietnamese.php',
    $modpath . 'templates/block-vkp_News_Poll.html',
    );

?>