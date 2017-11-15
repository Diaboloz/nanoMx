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
 * $Revision: 257 $
 * $Author: PragmaMx $
 * $Date: 2016-11-15 16:12:18 +0100 (Di, 15. Nov 2016) $
 *
 * @package pragmaMx
 */

defined('mxMainFileLoaded') or die('access denied');

require_once(PMX_SYSTEM_DIR . DS . 'mx_api.php');
require_once(PMX_SYSTEM_DIR . DS . 'mx_date.php');
require_once(PMX_SYSTEM_DIR . DS . 'mx_api_2.php');
require_once(PMX_SYSTEM_DIR . DS . 'mx_module.php');
require_once(PMX_SYSTEM_DIR . DS . 'mx_blockfunctions.php');
require_once(PMX_SYSTEM_DIR . DS . 'mx_file.php');

include_once(UTF8 . DS . 'utf8.php');

load_class("Translate",false);
pmxTranslate::init();


?>