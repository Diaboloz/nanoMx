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
 * modified by (c) 2007 by jubilee
 */

/**
 * MysqlPhrase
 * PHP Session "Backend" for storing and retrieving a captcha value
 * use this class to check wether a use rpassed captcha value
 * is valid or not...
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2009
 * @version $Id: phrase.mysql.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class MysqlPhrase {
    /**
     * MysqlPhrase::get()
     *
     * @param mixed $reset
     * @param string $newPhrase
     * @param string $givenphrase
     * @return
     */
    public static function get($reset, $newPhrase = '', $givenphrase = '')
    {
        $instance = new MysqlPhrase();
        if ($reset || ((!empty($newPhrase)) && ($givenphrase == ''))) {
            return $instance->setPhrase($newPhrase);
        } else if ($givenphrase) {
            return $instance->getPhrase($givenphrase);
        }
    }

    /**
     * MysqlPhrase::setPhrase()
     *
     * @param mixed $number
     * @return
     */
    private function setPhrase($number)
    {
        $this->updateMemory();
        $ctime = date("d.m.y. h:i:s");
        $timestamp = time();
        $cip = $_SERVER['REMOTE_ADDR'];
        $sql = "INSERT INTO " . $GLOBALS['prefix'] . "_captcher (ckey, ctime, timestamp, cip) VALUES ('$number', '$ctime', $timestamp, '$cip')";
        if (!$result = sql_query($sql)) {
            return false;
        } else {
            return $number;
        }
    }

    /**
     * MysqlPhrase::getPhrase()
     *
     * @param mixed $paramNumber
     * @return
     */
    private function getPhrase($paramNumber)
    {
        $sql = "SELECT * FROM " . $GLOBALS['prefix'] . "_captcher WHERE ckey='" . mxAddSlashesForSQL($paramNumber) . "'";

        if (!$result = sql_fetch_assoc(sql_query($sql))) {
            return false;
        } else {
            return $result['ckey'];
        }
    }

    /**
     * MysqlPhrase::updateMemory()
     *
     * @return
     */
    private function updateMemory()
    {
        $deleteborder = time()-120;
        $sql = "DELETE FROM " . $GLOBALS['prefix'] . "_captcher WHERE timestamp < ${deleteborder}";
        $result = sql_query($sql);
    }
}

?>
