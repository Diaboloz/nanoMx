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
 *
 *
 */

defined('mxMainFileLoaded') or die('access denied');

/* der passende Modulname */
$module_name = basename(dirname(dirname(__FILE__)));

if (!mxModuleAllowed($module_name)) {
    /* Block darf nicht gecached werden und weg... */
    $mxblockcache = false;
    return;
}

switch (true) {
    case MX_MODULE != $module_name:
    case empty($GLOBALS['story_blocks']):
        return;
}

/* Block kann gecached werden? */
$mxblockcache = true;

if ($GLOBALS['story_blocks']["ratings"] != 0) {
    $rate = substr($GLOBALS['story_blocks']["score"] / $GLOBALS['story_blocks']["ratings"], 0, 4);
    $r_image = round($rate);
    $the_image = '<br /><br />' . mxCreateImage('images/articles/stars-' . $r_image . '.gif');
} else {
    $rate = 0;
    $the_image = '';
}

$content = '
<div class="align-center">' . _AVERAGESCORE . ': <b>' . mxValueToString($rate, 0) . '</b><br />' . _NEWSPOLLVOTES . ': <b>' . mxValueToString($GLOBALS['story_blocks']['ratings'], 0) . '</b>' . $the_image . '</div>
<br />
<div>' . _RATETHISARTICLE . '</div>
<form action="modules.php?name=' . $module_name . '" method="post">
<div class="align-center">
<div title="' . _EXCELLENT . '"><input type="radio" name="score" value="5" /> ' . mxCreateImage('images/articles/stars-5.gif', _EXCELLENT) . '</div>
<div title="' . _VERYGOOD . '"><input type="radio" name="score" value="4" /> ' . mxCreateImage('images/articles/stars-4.gif', _VERYGOOD) . '</div>
<div title="' . _GOOD . '"><input type="radio" name="score" value="3" /> ' . mxCreateImage('images/articles/stars-3.gif', _GOOD) . '</div>
<div title="' . _REGULAR . '"><input type="radio" name="score" value="2" /> ' . mxCreateImage('images/articles/stars-2.gif', _REGULAR) . '</div>
<div title="' . _BAD . '"><input type="radio" name="score" value="1" /> ' . mxCreateImage('images/articles/stars-1.gif', _BAD) . '</div>
<br />
<div><input type="submit" value="' . _CASTMYVOTE . '" /></div>
<input type="hidden" name="sid" value="' . $GLOBALS['story_blocks']['sid'] . '" />
<input type="hidden" name="op" value="rate_article" />
<input type="hidden" name="name" value="' . $module_name . '" />
</div>
</form>
';

$blockfiletitle = _RATEARTICLE;

?>