<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: dia_bolo $
 * $Revision: 1.1.2.3 $
 * $Date: 2012-01-22 16:18:13 $
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

defined('MXB_INIT') or die('Not in mxBoard...');

$qry = "INSERT IGNORE INTO `$table_themes` VALUES ( '".basename(dirname(__file__))."', '#FFFFFF', '#FF7800', '#CC0000', '#FF7800', '#FFFFFF', '#500404', '#FFFFFF', '#FF7800', '#FF7800', '#FFFFFF', '#FFFFFF', '1px', '100%', '6', 'Verdana', '12px', 'sans-serif', '9px', 'rot', '', '', '', 'red', 'blue')";

?>