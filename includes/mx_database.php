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
 * $Revision: 196 $
 * $Author: PragmaMx $
 * $Date: 2016-07-27 22:09:26 +0200 (Mi, 27. Jul 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

global $dbi, $vkpIntranet;
/* Datenbanktyp  auf MySql stellen */

pmxBase::set_system('dbtype',"mysql");
pmxBase::set('dbi',NULL);
pmxBase::set('mxSqlErrorDebug',0);
pmxBase::set('mxQueryCount',0);
$dbi=NULL;


/* since PHP 7 only permitted MYSQLY */
require_once(PMX_SYSTEM_DIR . DS . 'mx_db_mysqli.php');


?>