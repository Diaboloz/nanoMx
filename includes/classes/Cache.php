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

defined('mxMainFileLoaded') or die('access denied');

/**
 * Cache
 *
 * based on article: Simples Caching System mittels Dateien
 * http://www.developers-guide.net/c/165-simples-caching-system-mittels-dateien.html
 * by Christian Mühlroth (http://www.chrisdiary.de/)
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2011
 * @version $Id: Cache.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxCache {
    const PHP_EXITER = '<?php exit ?>';
    // Gibt an, ob die Dateinamen mit sha1 gehasht werden  sollen, oder nicht
    public $bHashFilenamesSHA1 = true;
    // Die Lebenszeit in Sekunden (1800 Sekunden = 30 Minuten)
    public $iDefaultLifetime = 1800;
    // Der Pfad zu den Cache-Dateien (absolut oder relativ)
    public $sCachePath = './cache';
    // Die Dateierweiterung für die Cache-Dateien (z.B. ".cache")
    public $sFileExtension = '.cache.php';

    /**
     * Konstruktor der Klasse
     */
    public function __construct()
    {
        $this->sCachePath = PMX_DYNADATA_DIR . DS . 'cache';
        // Wandelt den eingegebenen Pfad in einen absoluten Pfad um
        $this->sCachePath = realpath($this->sCachePath);
    }

    /**
     * Gibt den Dateinamen inklusive Pfadangabe zurück
     * Auf Wunsch wird dieser Dateiname mittels der Funktion sha1() gehasht.
     *
     * @param string $sCacheName
     * @return string
     */
    protected function _get_file_name($sCacheName = false)
    {
        global $mxSecureKey;
        // Leere Cache-Eintrag-Namen sind nicht erlaubt :)
        if (trim($sCacheName) === '') {
            return false;
        }
        $sFileIdentifier = ($this->bHashFilenamesSHA1 === true) ? sha1($sCacheName . $mxSecureKey) : $sCacheName;
        return $this->sCachePath . DS . $sFileIdentifier . $this->sFileExtension;
    }

    /**
     * Liest die Daten aus dem Cache, falls diese existieren
     * und noch gültig sind.
     *
     * @param string $sCacheName
     * @return boolean (if not succeeded) | mixed (if succeeded)
     */
    public function read($sCacheName = false)
    {
        if (false === $sCacheName) {
            $sCacheName = $this->_key();
        }
        // Leere Cache-Eintrag-Namen sind nicht erlaubt :)
        if (trim($sCacheName) === '') {
            return false;
        }
        // Der Cache-Name ist also nicht leer, nun wird
        // der Dateiname inklusive Pfad generiert
        $sFileName = $this->_get_file_name($sCacheName);
        // Nun müssen wir prüfen, ob die Datei überhaupt
        // existiert.. Wenn nicht, wird die Funktion abgebrochen
        // und false zurückgegeben.
        if (file_exists($sFileName) === false) {
            return false;
        }
        // Hier müssen wir prüfen, ob die Datei lesbar ist.
        // Wenn nicht, wird die Funktion ebenfalls abgebrochen
        // und false zurückgegeben.
        if (is_readable($sFileName) === false) {
            return false;
        }
        // Nun lesen wir die Daten aus ..
        $sData = file_get_contents($sFileName);
        // Nun ent-serialisieren wir die gegebenen Daten
        $mData = @unserialize($sData);
        // Falls beim ent-serialisieren etwas schiefgelaufen ist,
        // oder der aktuelle Zeitstempel bereits größer als der
        // im Cache ist (d.h. der Cache ist verfallen) wird
        // die Datei gelöscht, und es wird false zurückgegeben.
        if (!$mData or time() > $mData[1]) {
            // Delete that file and return false
            $this->delete($sCacheName);
            return false;
        }

        return $mData[2];
    }

    /**
     * Schreibt die übergebenen Daten in den Cache
     *
     * @param string $sCacheName
     * @param mixed $mData
     * @param integer $iLifetime ( in seconds )
     * @return boolean
     */
    public function write($mData, $sCacheName = false, $iLifetime = -1)
    {
        if (false === $sCacheName) {
            $sCacheName = $this->_key();
        }
        // Leere Cache-Eintrag-Namen sind nicht erlaubt :)
        if (trim($sCacheName) === '') {
            return false;
        }
        if (is_int($iLifetime) === false or $iLifetime < 0) {
            // Falls der übergebene Lebensdauer-Wert keine Zahl
            // ist oder kleiner Null, wird der standardmäßige Wert
            // genommen
            $iLifetime = $this->iDefaultLifetime;
        }
        // Hier wird wieder der Dateiname zusammengebaut
        $sFileName = $this->_get_file_name($sCacheName);
        // Nun werden die übergebenen Daten ($mData) zu einem String serialisiert
        $sSerializedData = serialize(array(self::PHP_EXITER, (time() + $iLifetime), $mData));
        // Nun schreiben wir die neuen Cache-Daten
        // (oder versuchen es zumindest)
        if (file_put_contents($sFileName, $sSerializedData) === false) {
            // Sollte hier an dieser Stelle ein Fehler auftreten,
            // wird false zurückgegeben
            return false;
        }
        return true;
    }

    /**
     * Säubert die Cache-Daten
     * - Sucht nach alten, abgelaufenen Cache-Daten und entfernt diese
     * - Gibt die Anzahl der gelöschten Cache-Einträge zurück
     *
     * @return integer
     */
    public function clean()
    {
        // Suche nach allen Dateien, die zum Cache gehören
        $aFiles = glob($this->sCachePath . DS . '*' . $this->sFileExtension);
        // Falls keine Dateien verfübar sind
        if (count($aFiles) < 1) {
            // gib 0 zurück (0 Dateien wurden entfernt)
            return 0;
        }

        $iCounter = 0;
        // Nun, es gibt mindestens eine Datei ...
        foreach($aFiles as $sFileName) {
            // Lesen wir die Daten ein ...
            $sData = file_get_contents($sFileName);

            $mData = @unserialize($sData);
            if (!$mData) {
                // Es ist wohl etwas schief gelaufen,
                // weiter mit der nächsten Datei
                continue;
            }

            if (time() + $this->iDefaultLifetime > $mData[1]) {
                // Die Lebenszeit ist abgelaufen,
                // also wird dieser Cache-Eintrag gelöscht
                if ($this->delete($sFileName, true) === true) {
                    $iCounter ++;
                }
            }
        }
        // Jetzt nur noch die Anzahl der gelöschten Dateien
        // zurückgeben, fertig.
        return $iCounter;
    }

    /**
     * Diese Funktion löscht jedliche Cache-Einträge, die zu finden sind
     * und nimmt dabei keine Rücksicht auf die Verfallsdaten
     * der jeweiligen Dateien.
     *
     * @return integer
     */
    public function truncate()
    {
        // Suche nach allen Dateien, die zum Cache gehören
        $aFiles = glob($this->sCachePath . DS . '*' . $this->sFileExtension);
        // Falls keine Dateien verfübar sind ..
        if (count($aFiles) < 1) {
            // .. geben wir 0 zurück (integer), denn die Funktion
            // gibt die Anzahl der gelöschten Dateien zurück
            return 0;
        }
        // gib 0 zurück (0 Dateien wurden entfernt)
        $iCounter = 0;
        // Nun, es gibt mindestens eine Datei ...
        foreach($aFiles as $sFileName) {
            // Jetzt prüfen wir nicht, ob der Eintrag gültig ist
            // oder nicht - sondern löschen ihn einfach :)
            if ($this->delete($sFileName, true) === true) {
                $iCounter ++;
            }
        }
        // Zuletzt geben wie die Anzahl der gelöschten
        // Dateien zurück
        return $iCounter;
    }

    /**
     * Löscht einen existierenden Cache-Eintrag
     *
     * @param string $sCacheName
     * @param boolean $bIsFileName
     * @return boolean
     */
    public function delete($sCacheName = false, $bIsFileName = false)
    {
        if (false === $sCacheName) {
            $sCacheName = $this->_key();
        }
        // Leere Cache-Eintrag-Namen sind nicht erlaubt :)
        if (trim($sCacheName) === '') {
            return false;
        }
        // Falls der übergebene Wert kein Dateiname ist,
        // muss dieser erst generiert werden
        if (false === $bIsFileName) {
            $sFileName = $this->_get_file_name($sCacheName);
        } else {
            // Ansonsten kann der Name 1:1 übernommen werden
            $sFileName = $sCacheName;
        }
        // wenn Datei micht mehr existiert, ist sie auch gelöscht ;-)
        if (!file_exists($sFileName)) {
            return true;
        }
        // Löscht die Datei und gibt bei Erfolg "true",
        // bei Misserfolg "false" zurück.
        return @unlink($sFileName);
    }

    /* key()
     *
     * Returns a hashvalue for the current. Maybe md5 is too heavy,
     * so you can implement your own hashing-function.
     */
    protected function _key()
    {
        return $_SERVER['REQUEST_URI'] . serialize(array_merge($_GET, $_POST));
    }
}

?>