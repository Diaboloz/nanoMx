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

if (!defined('mxMainFileLoaded')) {
    if (!($mainfile = realpath(__DIR__ . '/../../../mainfile.php'))) {
        die('mainfile missing...');
    }
    include_once($mainfile);
}

/* bestimmte _GET Parameter an Klassenkonstruktor übergeben */
$para = array('editor' => 'standallone');
foreach ($_GET as $key => $value) {
    switch ($key) {
        case 'editor':
        case 'type':
        case 'getback':
            $para[$key] = $value;
    }
}

$fileman = load_class(basename(__DIR__), $para);
$method = $para['editor']; // läuft per__call()
echo $fileman->$method();

?>