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
 * $Date: 2015-07-08 09:07:06 +0200 (mer. 08 juil. 2015) $
 */

$hook = function($module_name, $options, &$item)
{
    /* $options ist nur ein leerer dummy */

    $item[] = array(/*  */
        // der Modulname
        'module' => $module_name,
        'caption' => (defined('_' . strtoupper($module_name))) ? constant('_' . strtoupper($module_name)) : ucwords(str_replace('_', ' ', $module_name)),
        'varname' => strtolower($module_name) . 'on',
        'default' => true,
        'hidden' => true,
        // Link zum entsprechenden Adminmodul
        'adminlink' => 'modules.php?name=' . $module_name . '&amp;file=cp.settings',
        );
} ;

?>