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
 * $Revision: 206 $
 * $Author: PragmaMx $
 * $Date: 2016-09-12 13:33:26 +0200 (Mo, 12. Sep 2016) $
 */

if (!defined('mxMainFileLoaded')) {
    if (!($mainfile = realpath(__DIR__ . '/../../../mainfile.php'))) {
        die('mainfile missing...');
    }
    include_once($mainfile);
}

$fileman = load_class(basename(__DIR__));

echo $fileman->connector();

?>