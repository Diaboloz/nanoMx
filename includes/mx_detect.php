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
 * pmxDetect
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2009
 * @version $Id: mx_detect.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxDetect {
    /* die interne Konfiguration der Klasse */
    private static $__cnf = array();

    /* wird true, wenn start() ausgefuehrt */
    private static $__initialized = false;

    /**
     * pmxDetect::start()
     *
     * @return
     */
    public static function start()
    {
        /* wenn abgeschaltet, hier beenden */
        if (!$GLOBALS['vkpSafeSqlinject']) {
            return;
        }

        /* Konfiguration einlesen */
        self::_initconfig();

        self::$__initialized = true;
        // den RequestString erstellen und in Konstante speichern
        define('___MXREQUESTSTRING___', self::_querystring($_REQUEST));
        // mxDebugFuncVars(___MXREQUESTSTRING___);
        // alle vorhandenen Signaturen importieren
        $sig = self::_signatures(md5($GLOBALS['prefix']));
        // Schleife durch alle definierten Signaturen
        foreach ($sig['req'] as $i => $sigreq) {
            // prüfen ob die Signatur vollständig ist
            if (!isset($sig['msg'][$i])) {
                echo '<pre><b>Fehler in Signatur #' . $i . '</b></pre>';
                $sig['msg'][$i] = 'Hack detected (Fehler in Signatur)';
            }
            // prüfen ob verdächtiger String auch in den requests vorkommt
            if (preg_match($sigreq, ___MXREQUESTSTRING___, $matches)) {
                // mxDebugFuncVars($i,$sigreq, $matches);
                // wenn passende sql Signatur vorhanden
                if (isset($sig['sql'][$i])) {
                    $sql_detect['sql'][$i] = $sig['sql'][$i];
                    $sql_detect['msg'][$i] = $sig['msg'][$i];
                    self::_logging($sig['msg'][$i] . " ($i)", false);
                } else {
                    // sonstige
                    $req_detect['req'] = $sig['req'][$i];
                    $req_detect['msg'] = $sig['msg'][$i] . " ($i)";
                    // mxDebugFuncVars($i,$sigreq, $matches);
                    $critical = true;
                    break;
                }
            }
        }
        if (isset($sql_detect)) {
            // das Array mit den ermittelten "möglichen" sql_injects als string in einer Konstante zwischenspeichern
            // dieses Array wird später beim query-check verwendet, anstatt der eigentlichen Signaturen
            define('___MXSQLDETECT___', serialize($sql_detect));
            // todo: Logging von verdächtigen Variablen
        }
        // wenn eine Request-Injection gefunden wurde, oder ein Fehler vorliegt
        if (isset($req_detect)) {
            // verhindern, dass SQL-Fehlermeldungen bei eingeschaltetem debugmode ausgegeben werden
            pmxDebug::pause();
            // Log-Eintrag & mail
            self::_logging($req_detect['msg'], false);
            // destroy session?
            if (self::$__cnf['killsession']) {
                mxSessionDestroy();
            }
            // banning?
            if (self::$__cnf['ipbanning'] > 1) {
                self::_banning();
            }
            // wenn weitergeleitet werden soll, oder die Detection als Kritisch eingestuft wurde
            if (self::$__cnf['redirect'] || isset($critical)) {
                if (!headers_sent()) {
                    mxRedirect(self::$__cnf['redirect'], "Bad Request:<br />$req_detect[msg]<br /><br />", 60);
                }
                // auf jeden Fall abbrechen, falls header nicht funktioniert
                die("Bad Request:<br />$req_detect[msg]");
            }
        }
    }

    /**
     * pmxDetect::_signatures()
     *
     * @param mixed $key
     * @return
     */
    private static function _signatures($key)
    {
        // ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Teile für patterns und sonstige funktionsspezifische Variablen, nicht verändern !!
        // - alle Zeichen, die in mysql einen Kommentar oder Leerzeichen darstellen können
        // und das normale Leerzeichen ersetzen könnten
        $sqlspace = '(?:--[[:space:]]|\/\*|\*\/|[[:space:]]){1,}';
        // Teil für pattern
        // - evtl. führend der Datenbankname mit einem folgenden Punkt
        // - der prefix oder userprefix oder standard-mx oder standard-nuke gefolgt von einem _
        // - nach dem _ mindestens 1 alhanumerisches Zeichen oder ein weiterer _
        $table = '(?:\.|`|' . $sqlspace . ')(?:' . $GLOBALS['prefix'] . '|' . $GLOBALS['user_prefix'] . '|mx)(?:[[:alnum:]]|_|-){1,64}(?:`|' . $sqlspace . ')';
        // eindeutigen Index für Signaturen initialisieren
        $i = 0;
        // ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Definition der Prüf-Signaturen
        // $sig['sql'] : der Suchstring für die Datenbankanfrage
        // $sig['req'] : der Suchstring für die Request-Parameter
        // $sig['msg'] : Die entsprechende Meldung für den Log-Eintrag, falls beide Bedingungen wahr sind
        // - für sql-Überprüfung müssen immer diese 3 Teile vorhanden sein
        // - der erste Match ($matches[1]) stellt immer die evtl. gültige Datenbankabfrage dar und wird von der Funktion zurückgegeben
        // - der Rest wird radikal abgeschnitten
        // nach jeder Signatur, nicht vergessen, den Zähler zu erhöhen !!
        // ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Union Hack
        $sig['req'][$i] = "#${sqlspace}UNION(?:|${sqlspace})(?:ALL|SELECT).*?FROM.*?${table}#isU";
        $sig['sql'][$i] = "#(SELECT.*${table}.*?)${sqlspace}UNION(?:|${sqlspace})(?:ALL|SELECT).*?FROM.*?${table}#isU";
        $sig['msg'][$i] = '[Union] Hack detected';
        $i++;
        // OR/AND WHERE Hack
        // das ist noch nicht ausgereift !!!!
        // $sig['sql'][$i] = "#(.*?${table}.*${sqlspace}WHERE.*)${sqlspace}(or|and)${sqlspace}([a-zA-Z]{1}[[:alnum:]]*)${sqlspace}(?:=|like|in${sqlspace}?\(|between).*#isU";
        // $sig['req'][$i] = "#${sqlspace}(or|and)${sqlspace}([a-zA-Z][[:alnum:]]*$)${sqlspace}(=|like|in${sqlspace}?\(|between)(.*)#isU";
        // $sig['sql'][$i] = "#(.*?(${table}).*${sqlspace}WHERE.*)${sqlspace}(or|and)${sqlspace}([a-zA-Z]{1}[[:alnum:]]*)${sqlspace}(?:=|like|in${sqlspace}?\(|between).*#isU";
        // $sig['sql'][$i] = "#${sqlspace}(or|and)${sqlspace}([a-zA-Z][[:alnum:]]*$)${sqlspace}(=|like|in${sqlspace}?\(|between)(.*)#isU";
        // $sig['req'][$i] = "#${sqlspace}(or|and)${sqlspace}([a-zA-Z][[:alnum:]]*$)${sqlspace}(=|like|in${sqlspace}?\(|between)(.*)#isU";
        // $sig['msg'][$i] = '[OR/AND WHERE] Hack detected';
        // $i++;
        // UPDATE Table Hack
        $sig['req'][$i] = "#;(?:|${sqlspace})UPDATE.*(${table}).*?SET#isU";
        $sig['sql'][$i] = "#(.*);(?:|${sqlspace})UPDATE.*(${table}).*?SET#isU";
        $sig['msg'][$i] = '[UPDATE_SET] Hack detected';
        $i++;
        // Insert / Replace Hack
        $sig['req'][$i] = "#;(?:|${sqlspace})(?:INSERT|REPLACE).*(${table}|OUTFILE|DUMPFILE).*?(?:VALUES|SELECT|SET)#isU";
        $sig['sql'][$i] = "#(.*);(?:|${sqlspace})(?:INSERT|REPLACE).*(${table}|OUTFILE|DUMPFILE).*?(?:VALUES|SELECT|SET)#isU";
        $sig['msg'][$i] = '[INSERT/REPLACE] Hack detected';
        $i++;
        // DELETE From Hack
        $sig['req'][$i] = "#;(?:|${sqlspace})DELETE.*?FROM.*?(${table})#isU";
        $sig['sql'][$i] = "#(.*);(?:|${sqlspace})DELETE.*?FROM.*?(${table})#isU";
        $sig['msg'][$i] = '[DELETE_FROM] Hack detected';
        $i++;
        // Truncate/Drop Table
        $sig['req'][$i] = "#;(?:|${sqlspace})(?:TRUNCATE|DROP|ALTER).*TABLE.*?(${table})#isU";
        $sig['sql'][$i] = "#(.*);(?:|${sqlspace})(?:TRUNCATE|DROP|ALTER).*TABLE.*?(${table})#isU";
        $sig['msg'][$i] = '[TRUNCATE/DROP/ALTER_TABLE] Hack detected';
        $i++;
        // LOAD DATA INFILE Hack
        $sig['req'][$i] = "#;(?:|${sqlspace})LOAD${sqlspace}DATA.*?INFILE#isU";
        $sig['sql'][$i] = "#(.*);(?:|${sqlspace})LOAD${sqlspace}DATA.*?INFILE#isU";
        $sig['msg'][$i] = '[LOAD_DATA_INFILE] Hack detected';
        $i++;
        // Select Into Hack
        $sig['req'][$i] = "#;(?:|${sqlspace})SELECT.*?INTO${sqlspace}(?:OUTFILE|DUMPFILE)#isU";
        $sig['sql'][$i] = "#(.*);(?:|${sqlspace})SELECT.*?INTO${sqlspace}(?:OUTFILE|DUMPFILE)#isU";
        $sig['msg'][$i] = '[SELECT_INTO] Hack detected';
        $i++;
        // HANDLER Hack
        $sig['req'][$i] = "#;(?:|${sqlspace})HANDLER.*?(${table}).*?(?:OPEN|READ)#isU";
        $sig['sql'][$i] = "#(.*);(?:|${sqlspace})HANDLER.*?(${table}).*?(?:OPEN|READ)#isU";
        $sig['msg'][$i] = '[HANDLER] Hack detected';
        $i++;
        // sonstige Signaturen, die nur auf Request überprüft werden
        $sig['req'][$i] = '#\<(?:img|i?frame|object|embed|i?layer|script|link)[^\>]+(?:[[:space:]]{0,})(?:src|rel)(?:[[:space:]]{0,})?=(?:[^\>]+)?admin\.php[^\>]+\>|\[img(?:.+)?(?:admin\.php).+?\[/img#si';
        $sig['msg'][$i] = '[Admin Intrusion] Hack detected';
        $i++;
        // interne Sicherung, dass Signaturen nicht ausgelesen werden können
        if (md5($GLOBALS['prefix']) != $key) {
            $sig = array();
        }
        return $sig;
    }

    /**
     * pmxDetect::query()
     *
     * @param mixed $query
     * @return
     */
    public static function query($query)
    {
        if (!self::$__initialized) {
            return $query;
        }

        static $requestlen, $skipcheck;
        // abschliessende Semikolons und umgebende spaces vom querystring immer entfernen
        $query = trim($query, '; ');
        // abbrechen, wenn Query nicht geprueft werden muss
        if ($skipcheck || ($GLOBALS['mxSkipSqlDetect'])) {
            return $query;
        }

        /* Konfiguration einlesen */
        self::_initconfig();

        if (!$requestlen) {
            $requestlen = strlen(___MXREQUESTSTRING___);
        }
        // nur zum testen:
        // $query .= $_REQUEST['qsl'];
        // ende nur zum testen:
        // nur längere Strings testen und nur, wenn im Request bereits etwas gefunden wurde
        if ((!defined('___MXSQLDETECT___')) || ($requestlen < self::$__cnf['minlen_req'])) {
            $skipcheck = true;
            return $query;
        }
        if ((strlen($query) < self::$__cnf['minlen_sql'])) {
            return $query;
        }
        // den string mit den ermittelten "möglichen" sql_injects als Array umwandeln
        $sig = unserialize(___MXSQLDETECT___);
        // Anzahl der vorhandenen Signaturen ermitteln
        $countsigs = count($sig['sql']);
        // mxDebugFuncVars($countsigs, $sig); #exit;
        // wenn Signaturen vorhanden
        if ($countsigs) {
            // Schleife durch alle definierten Signaturen
            foreach ($sig['sql'] as $i => $sigsql) {
                // mxDebugFuncVars($i,$sigsql, $query);
                // wenn die SQL-Bedingung zutrifft
                if (preg_match($sigsql, $query, $matches)) {
                    // mxDebugFuncVars($i,$sigsql, $matches);
                    // Fehlermeldung für Logging zwischenspeichern
                    // - die entsprechende Nachricht
                    $detect['msg'] = $sig['msg'][$i] . " ($i)";
                    // - die unveränderte Datenbankanfrage
                    // - die komplette Request-Stringkette
                    $detect['qry'] = "\r\nquery:\r\n" . mxStripSlashes($query) . "\r\nrequest:\r\n" . self::_getmessage($_REQUEST);
                    // Wenn es nur ein select Befehl ist, die Datenbankabfrage so abändern, damit nur der erste
                    // gefundene Match zurückgegeben wird und die Datenbankabfrage ausgeführt werden kann
                    if (preg_match("#SELECT.*(?:^INTO)#isA", $matches[1])) {
                        $query = $matches[1];
                    }
                    // ansonsten, die Datenbankabfrage komplett löschen
                    else {
                        $query = '';
                        $critical = true;
                    }
                    // bei der ersten Fundstelle die Schleife abbrechen
                    break;
                }
            }
        }
        // mxDebugFuncVars($detect); exit;
        // wenn eine Injection gefunden wurde, oder ein Fehler vorliegt
        if (isset($detect)) {
            // verhindern, dass SQL-Fehlermeldungen bei eingeschaltetem debugmode ausgegeben werden
            // zusätzlich error_reporting abschalten
            pmxDebug::pause();
            // Log-Eintrag & mail
            self::_logging($detect['msg'], true, $detect['qry']);
            // destroy session?
            if (self::$__cnf['killsession']) {
                mxSessionDestroy();
            }
            // banning?
            if (self::$__cnf['ipbanning'] > 1) {
                self::_banning();
            }
            // wenn weitergeleitet werden soll, oder die Detection als Kritisch eingestuft wurde
            if (self::$__cnf['redirect'] || isset($critical)) {
                if (!headers_sent()) {
                    mxRedirect(self::$__cnf['redirect'], "Bad Request:<br />$detect[msg]<br /><br />", 60);
                }
                // auf jeden Fall abbrechen, falls header nicht funktioniert
                die("Bad Request:<br />$detect[msg]");
            }
        }
        // die überprüfte und evtl. geänderte Datenbankabfrage zurückgeben
        return $query;
    }

    /**
     * pmxDetect::_logging()
     *
     * @param mixed $msg
     * @param mixed $showmore
     * @param string $addinfo
     * @return
     */
    private static function _logging($msg, $showmore, $addinfo = '')
    {
        // mxDebugFuncVars($addinfo);
        // Benutzerdaten ermitteln falls vorhanden
        $aid = 'guest';
        $uname = 'guest';
        if (mxSessionGetVar("user_uname")) {
            $uname = substr(mxSessionGetVar("user_uname"), 0, 25);
        }
        if (mxSessionGetVar("admin")) {
            $stat = explode(":", preg_replace('#[[:cntrl:]]#', ' ', base64_decode(mxSessionGetVar("admin"))));
            $aid = substr($stat[0], 0, 25);
        }

        $trace = '';
        if ($showmore) {
            // die komplette Ausgabe der Funktion mxDebugBacktrace()
            $trace = trim("Backtrace:\r\n" . str_replace(array("\n ", "\n", "\n\n", "details: "), array("\n", "\n", "\r\n", ""), preg_replace('#( |&nbsp;){1,}#i', ' ', strip_tags(mxDebugBacktrace(false)))));
        } // end if ($showmore)
        // versch. Serverinformationen
        settype($_SERVER['HTTP_X_FORWARDED_FOR'], 'string');
        $serverdata = @"
Serverinfo:
REMOTE_ADDR:          $_SERVER[REMOTE_ADDR]
QUERY_STRING:         $_SERVER[QUERY_STRING]
REQUEST_URI:          $_SERVER[REQUEST_URI]
REMOTE_PORT:          $_SERVER[REMOTE_PORT]
REMOTE_HOST:          $_SERVER[REMOTE_HOST]
HTTP_REFERER:         $_SERVER[HTTP_REFERER]
HTTP_USER_AGENT:      $_SERVER[HTTP_USER_AGENT]
HTTP_X_FORWARDED_FOR: $_SERVER[HTTP_X_FORWARDED_FOR]
$trace
";

        $addinfo = (empty($addinfo)) ? '' : "\r\n" . $addinfo;
        $logentry = "\r\n" . str_repeat('-', 50) . "\r\n" . date("d-m-Y H:i:s") . ' ' . $msg . "\r\nuser: " . $uname . "\r\nadmin: " . $aid . $addinfo . "\r\nrequest:\r\n" . self::_getmessage($_REQUEST) . $serverdata;
        $logentry = preg_replace("/(\r\n)|(\r)|(\n)/", "\r\n", $logentry);
        // mxDebugFuncVars($logentry);
        // Meldung in Logdatei schreiben
        if (self::$__cnf['logtype'] == 'file' || self::$__cnf['logtype'] == 'both' || !isset($GLOBALS['dbi'])) {
            pmxDebug::error_log($logentry, 3, self::$__cnf['logfile']);
        }

        if ((self::$__cnf['logtype'] == 'sql' || self::$__cnf['logtype'] == 'both') && isset($GLOBALS['dbi'])) {
            $qry = "INSERT INTO " . $GLOBALS['prefix'] . "_securelog
                    (log_ip, log_time, log_eventid, log_event, uname, aid, request) VALUES
                    ('" . MX_REMOTE_ADDR . "', " . time() . ", 'Hack-Attack', '" . mxAddSlashesForSQL($msg) . "', '" . (($uname=='guest') ? '' : mxAddSlashesForSQL($uname)) . "', '" . (($aid=='guest') ? '' : mxAddSlashesForSQL($aid)) . "', '" . mxAddSlashesForSQL(trim($logentry)) . "')";
            sql_system_query($qry);
        }
        // mail senden
        if (isset(self::$__cnf['sendmail'])) {
            $logentry .= "\r\nsiteadress: " . PMX_HOME_URL;
            // Remove \x00 (nullbyte) from any passed variable for security reasons
            $logentry = str_replace("\x00", '', $logentry);
            $subject = $msg . ', on ' . $GLOBALS['sitename'];
            // mxDebugFuncVars($logentry);
            foreach (self::$__cnf['sendmail'] as $mailto) {
                if (strpos($mailto, '@') !== false) {
                    mxMail($mailto, $subject, $logentry);
                }
            }
        }
        return $logentry;
    }

    /**
     * pmxDetect::_querystring()
     * gibt sämtliche von $_REQUEST übergebenen Scriptparameter als eine gemeinsame Zeichenkette zurück
     * dies ist die Grundlage zur Überprüfung der request-Variablen
     *
     * @param mixed $request
     * @return
     */
    private static function _querystring($request)
    {
        // die Sessioncookies nicht prüfen
        unset($request[MX_SESSION_NAME], $request[MX_SAFECOOKIE_NAME_ADMIN], $request[MX_SAFECOOKIE_NAME_USER]);
        $thisrequest = '';
        $thisrequestenc = '';
        // alle Request-Parameter durchlaufen und prüfen, ob der verdächtige String vorkommt
        foreach ($request as $var => $value) {
            // bei numerischen Werten, die Schleife direkt fortsetzen
            if (is_numeric($value)) {
                continue;
            }
            // bei Arrays, die Funktion rekursiv aufrufen
            if (is_array($value)) {
                $value = self::_querystring($value);
            }
            // nur längere Strings testen
            if (strlen($value) > 12) {
                // den Wert an die Stringkette anhängen, falls url-codiert, decodieren
                $thisrequest .= $value . ' ';
                // wenn bestimmte Parameternamen vorhanden, oder der Parametername ein cookie ist, den Wert base64 decodieren
                // tit: für stories, pic & data für coppermine, db für eBoard, cookie für hotornot, p_msg für public-message aus nuke6.5
                // if (preg_match('#(admin|user|tit|pic|data|db|cookie|p_msg|auth)#i', $var, $base64match) || isset($_COOKIE[$var])) {
                // $thisrequest .= preg_replace('#[[:cntrl:]]#', ' ', base64_decode($value)) . ' ';
                // }
            }
        }
        // This will reproduce the option magic_quotes_gpc=1
        if (!get_magic_quotes_gpc()) {
            $thisrequest = addslashes($thisrequest);
        }
        // Stringkette von umgebenden Leerzeichen befreit, zurückgeben
        return trim(mxHtmlEntityDecode(rawurldecode($thisrequest)));
    }

    /**
     * /////
     */
    // gibt sämtliche von $_REQUEST übergebenen Scriptparameter als eine gemeinsame Zeichenkette zurück
    // dies ist die Grundlage zur Überprüfung der request-Variablen
    private static function _getmessage($request)
    {
        // die Sessioncookies nicht prüfen
        unset($request[MX_SESSION_NAME]);
        unset($request[MX_SAFECOOKIE_NAME_ADMIN]);
        unset($request[MX_SAFECOOKIE_NAME_USER]);
        $thisrequest = '';
        $thisrequestenc = '';
        // alle Request-Parameter durchlaufen und prüfen, ob der verdächtige String vorkommt
        foreach ($request as $var => $value) {
            // wenn String leer, weitermachen...
            if (!$value) {
                continue;
            }
            // bei Arrays, die Funktion rekursiv aufrufen
            if (is_array($value)) {
                $value = self::_getmessage($value);
            }
            // den Wert an die Stringkette anhängen
            if (isset($_COOKIE[$var])) $type = '_COOKIE';
            else if (isset($_POST[$var])) $type = '_POST';
            else $type = '_GET';
            $thisrequest .= " \r\n" . $type . "[" . $var . "] = " . $value . ' ';
            // wenn bestimmte Parameternamen vorhanden, oder der Parametername ein cookie ist, den Wert base64 decodieren
            // tit: für stories, pic & data für coppermine, db für eBoard, cookie für hotornot
            // if ((preg_match('#(admin|user|tit|pic|data|db|cookie)#i', $var, $base64match) || isset($_COOKIE[$var])) && !is_numeric($value)) {
            // $thisrequest .= " \r\n" . $type . "[" . $var . " (encoded)] = " . preg_replace('#[[:cntrl:]]#', ' ', base64_decode($value)) . ' ';
            // }
        }
        // evtl. vorhandene backslashes entfernen und die Stringkette von umgebenden Leerzeichen befreit, zurückgeben
        return trim(stripslashes($thisrequest));
    }

    /**
     * pmxDetect::check_banning()
     *
     * @return
     */
    public static function check_banning()
    {
        /* Konfigurationsvariablen einlesen */
        self::_initconfig();

        if (!$GLOBALS['vkpSafeSqlinject'] || !(self::$__cnf['ipbanning'])) {
            // wenn ipbanning abgeschaltet, Funktion beenden
            return;
        }
        if (!@is_file(self::$__cnf['ipfile'])) {
            return;
        }
        $realip = MX_REMOTE_ADDR;

        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        else if (isset($_SERVER["HTTP_CLIENT_IP"])) $realip = $_SERVER["HTTP_CLIENT_IP"];
        else $realip = $_SERVER["REMOTE_ADDR"];

        $allips = file_get_contents(self::$__cnf['ipfile']);
        // if (self::$__cnf['proxybanning']) {
        // if (is_file(self::$__cnf['proxyfile'])) {
        // $allips .= file_get_contents(self::$__cnf['proxyfile']);
        // mxDebugFuncVars($allips);
        // }
        // }
        if (strstr($allips, $realip)) {
            $info = "\r\n" . str_repeat('-', 50) . @"
REMOTE_ADDR:          $_SERVER[REMOTE_ADDR]
HTTP_X_FORWARDED_FOR: $_SERVER[HTTP_X_FORWARDED_FOR]
HTTP_REFERER:         $_SERVER[HTTP_REFERER]
QUERY_STRING:         $_SERVER[QUERY_STRING]
REQUEST_URI:          $_SERVER[REQUEST_URI]
REMOTE_PORT:          $_SERVER[REMOTE_PORT]
REMOTE_HOST:          $_SERVER[REMOTE_HOST]
HTTP_USER_AGENT:      $_SERVER[HTTP_USER_AGENT]
"
             . self::_getmessage($_REQUEST) . "\r\n";
            pmxDebug::error_log($info, 3, self::$__cnf['visitsfile']);
            die(self::$__cnf['ipbanmsg']);
        }
    }

    /**
     * pmxDetect::_banning()
     *
     * @return
     */
    private static function _banning()
    {
        $realip = MX_REMOTE_ADDR;

        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        else if (isset($_SERVER["HTTP_CLIENT_IP"])) $realip = $_SERVER["HTTP_CLIENT_IP"];
        else $realip = $_SERVER["REMOTE_ADDR"];

        pmxDebug::error_log($realip . "\r\n", 3, self::$__cnf['ipfile']);
        // mail senden
        if (isset(self::$__cnf['sendmail'])) {
            $msg = "siteadress: " . PMX_HOME_URL . "\r\nbanned-ip: " . $realip;
            foreach (self::$__cnf['sendmail'] as $mailto) {
                if (strpos($mailto, '@') !== false) {
                    mxMail($mailto, 'IP ' . $realip . ' banned, on ' . $GLOBALS['sitename'], $msg);
                }
            }
        }
    }

    /**
     * pmxDetect::_initconfig()
     *
     * @return
     */
    private static function _initconfig()
    {
        switch (true) {
            case self::$__cnf:
            case !$GLOBALS['vkpSafeSqlinject']:
                return;
            case @include_once(PMX_SYSTEM_DIR . DS . 'detection' . DS . 'config.php'):
                self::$__cnf = $conf;
                break;
            default:
                self::$__cnf = self::_config_default();
                break;
        }
    }

    /**
     * pmxDetect::_config_default()
     *
     * @return
     */
    private static function _config_default()
    {
        // ##############################################################################
        // die config befindet sich normalerweise in der /includes/detection/config.php
        // falls die Datei fehlt oder nicht includet werden kann, wird diese Funktion hier mit Standardwerten verwendet
        $conf['logtype'] = 'file'; # file, sql, both
        $conf['logfile'] = PMX_DYNADATA_DIR . '/logfiles/detect.' . date('Y-m-d') . '.log';
        $conf['redirect'] = './'; #
        $conf['sendmail'][] = $GLOBALS['adminmail'];
        $conf['sendmail'][] = 'ids@pragmamx.org';
        $conf['minlen_req'] = 12; #
        $conf['minlen_sql'] = 15; #
        $conf['killsession'] = 0;
        $conf['ipbanning'] = 0;
        $conf['restrictor'] = 1;
        $conf['ipfile'] = PMX_DYNADATA_DIR . '/logfiles/banned.log';
        $conf['ipbanmsg'] = '<html><head><title>banned</title></head><body bgcolor="#DDDDDD">
        <table width="99%" height="99%" border="10" cellspacing="30" cellpadding="100">
        <tr align="center" valign="middle"><td>
        <h2><font color="#800000">sorry, ip ' . MX_REMOTE_ADDR . ' is banned from our site</font></h2>
        <h4><br /><a href="mailto:' . mxPrepareToDisplay($GLOBALS['adminmail']) . '">' . mxPrepareToDisplay($GLOBALS['adminmail']) . '</a></h4>
        </td></tr></table>
        </body></html>';
        $conf['visitsfile'] = PMX_DYNADATA_DIR . '/logfiles/banned_visitors.' . date('Y-m-d') . '.log';
        return $conf;
    }
}

/**
 * mxDetectCheckQuery()
 *
 * @param mixed $query
 * @return
 */
function mxDetectCheckQuery($query)
{
    return pmxDetect::query($query);
}

?>