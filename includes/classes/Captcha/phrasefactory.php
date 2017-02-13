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
 * thanks to uwe slick! http://www.deruwe.de/captcha.html
 * - just copy/pasted his thoughts GPLed!
 * modified by (c) 2007 by jubilee
 */

/**
 * PhraseFactory
 * this class allows to have multiple
 * "handshake backends" (i.e. session, file, db)
 * just call this class and you should receive a captchaPhrase
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2009
 * @version $Id: phrasefactory.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class PhraseFactory {
    /**
     * PhraseFactory::__construct()
     */
    private function __construct()
    {
    }

    /**
     * PhraseFactory::get()
     *
     * @param mixed $reset
     * @param string $phraseLength
     * @param string $given
     * @param string $DEPRECATED_storage
     * @return
     */
    public static function get($reset = false, $phraseLength = '', $given = '', $DEPRECATED_storage = '')
    {
        // Speichern des Vergleichswertes in der Datenbank
        include_once(__DIR__ . '/phrase.mysql.php');

        if (isset($GLOBALS['casesensitive'])) {
            $casesensitive = $GLOBALS['casesensitive'];
        } else {
            include(__DIR__ . '/settings.php');
        }

        if ($given) {
            $return = MysqlPhrase::get($reset, '', $given);
            // beim Check, ob der eingegebene Wert mit dem gespeicherten Wert uebereinstimmt,
            // wird die uebergebene post-Variable $given mit dem Rueckgabewert $return
            // dieser Funktion verglichen.
            if (empty($casesensitive)) {
                // Wenn die Gross-Kleinschreibung nicht beachtet werden soll, einfach hier bereits
                // vergleichen und bei Erfolg den Eingabewert als Rueckgabewert zurueckgeben.
                if (strtolower($given) == strtolower($return)) {
                    return $given;
                }
            }

            return $return;
        } else {
            return MysqlPhrase::get($reset, self::getPhrase($phraseLength), '');
        }
    }

    /**
     * PhraseFactory::getPhrase()
     *
     * @param mixed $phraseLength
     * @return
     */
    private static function getPhrase($phraseLength)
    {
        static $captchaPhrase = null;
        $idx = 0;

        if ($captchaPhrase == null) {
            if (empty($GLOBALS['charstouse'])) {
                // avoid heavy readable and ambiguous chars
                $availableChars = "23456789abcdfghjkmnpqrstvwxABCDEFGHJKLMNPRSTUVWXYZ";
            } else {
                $availableChars = str_shuffle($GLOBALS['charstouse']);
                /* eventuelle Leerzeichen aus charstouse entfernen */
                $availableChars = preg_replace('#\s#u', '', $availableChars);
            } while ($idx < $phraseLength) {
                $currentChar = $availableChars[rand(0, strlen($availableChars)-1)];
                if (!strstr($captchaPhrase, $currentChar)) {
                    $captchaPhrase .= $currentChar;
                    $idx++;
                }
            }
        }

        return $captchaPhrase;
    }
}

?>
