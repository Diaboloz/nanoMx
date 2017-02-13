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

defined('mxMainFileLoaded') or die('access denied');

$bookmodule = basename(dirname(__dir__));

include_once(PMX_MODULES_DIR . DS . $bookmodule . DS . "setup.pmx.php");

$doc_cfg = array();
$doc = load_class('Book', $bookmodule);
$doc->module_name = $bookmodule;
$doc->modulename = $bookmodule;

$doc_cfg = $doc->getConfig();
$doc->logging = $doc_cfg['logging'];
$doc->insertfirst = $doc_cfg['insertfirst'];

?>