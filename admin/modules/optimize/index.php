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
 * $Revision: 101 $
 * $Author: PragmaMx $
 * $Date: 2015-12-30 21:08:19 +0100 (Mi, 30. Dez 2015) $
 *
 *
 *
 * Copyright (c) 2001 by Xavier JULIE (webmaster@securite-internet.org
 * http://www.securite-internet.org
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * dboptimize_admin
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2013
 * @version $Id: index.php 101 2015-12-30 20:08:19Z PragmaMx $
 * @access public
 */
class dboptimize_admin {
    private $_template;

    /**
     * dboptimize_admin::__construct()
     *
     * @param mixed $op
     */
    public function __construct($op)
    {
        /* Admin-Modulnamen ermitteln */
        $module_name = basename(dirname(__FILE__));

        /* Sprachdatei auswählen */
        mxGetLangfile(dirname(__FILE__));

        /* Template initialisieren */
        $this->_template = load_class('Template');
        $this->_template->init_path(__FILE__);

        switch ($op) {
            case 'optimize/doit':
                return $this->_optimize();
            case 'optimize':
            default:
                return $this->_main();
        }
    }

    /**
     * dboptimize_admin::_main()
     *
     * @return
     */
    private function _main()
    {
         #$this->_template->assign(compact('history'));

        include('header.php');
        $this->_template->display('main.html');
        include('footer.php');
    }

    /**
     * dboptimize_admin::_optimize()
     *
     * @return
     */
    private function _optimize()
    {
        global $dbname, $prefix, $user_prefix;

        $total_gain = 0;
        $tableslist = array();
		$tableNames = array();
        $result = sql_query("SHOW TABLE STATUS FROM " . $dbname . " like '". $prefix ."%'");
        while ($table = sql_fetch_assoc($result)) {
            $total = ($table['Data_length'] + $table['Index_length']) / 1024 ;
            $table['size'] = round ($total, 3);
            if ($table['Data_free']) {
                sql_query("OPTIMIZE TABLE " . $table['Name']);
                $gain = $table['Data_free'] / 1024 ;
                $total_gain += $gain;
                $table['gain'] = round($gain, 3);
                $table['status'] = '<strong>' . _OPTIMIZED . '</strong>';
            } else {
                $table['gain'] = '--';
                $table['status'] = _ALREADYOPTIMIZED;
            }
            $tableslist[] = $table;
			$tableNames[] = $table['Name'];
        }
		
		/* wenn präfixe unterschiedlich, dann auch die Usertabellen optimieren */
		
		$result = sql_query("SHOW TABLE STATUS FROM " . $dbname . " like '". $user_prefix ."%'");
		while ($table = sql_fetch_assoc($result)) {
			if (!in_array($table['Name'],$tableNames) ) {
				$total = ($table['Data_length'] + $table['Index_length']) / 1024 ;
				$table['size'] = round ($total, 3);
				if ($table['Data_free']) {
					sql_query("OPTIMIZE TABLE " . $table['Name']);
					$gain = $table['Data_free'] / 1024 ;
					$total_gain += $gain;
					$table['gain'] = round($gain, 3);
					$table['status'] = '<strong>' . _OPTIMIZED . '</strong>';
				} else {
					$table['gain'] = '--';
					$table['status'] = _ALREADYOPTIMIZED;
				}
				$tableslist[] = $table;
			}
		}	
		

        $total_gain = round ($total_gain, 3);

        /* Daten dem Template zuweisen */
        $this->_template->assign(compact('tableslist', 'total_gain'));

        include('header.php');
        $this->_template->display('optimize.html');
        include('footer.php');
    }

}

$tmp = new dboptimize_admin($op);
$tmp = null;

?>