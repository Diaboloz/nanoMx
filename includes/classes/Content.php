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
 * $Revision: 147 $
 * $Author: PragmaMx $
 * $Date: 2016-05-06 10:27:20 +0200 (Fr, 06. Mai 2016) $
 */

/**
 * pmxContent
 *
 * @package pragmaMx
 * @author terraproject
 * @copyright Copyright (c) 2011
 * @access public
 */

/*
 *    Definition:
 *    access : 0=all, 1=user, 2=usergroup see 'group_access=array(usergroupID_1,usergroupID_2,...)',  3=owner,
 *  publish: -2=deleted, -1=waiting, 0=no , 1=yes, 2=archived
*/
if (!defined("mxMainFileLoaded")) die ("You can't access this file directly...");

/**
 * beinhaltet alle funktionen zum verwalten der Contentcategories und Datensätze
 */
global $prefix;

class pmxContent {
    private $catarray = array();
    private $config = array();
    private $rootID = false;
    private $db_columns = array(); // Aray mit dern Tabellendefinitionen
    private $content_count = 0;
    private $selectfilter = array();
    private $action = array();
    private $tempnodes = array();
    private $rootrecord = false;

    public function __construct($parameter = null)
    {
        global $prefix;

        $this->_setStandard($parameter);
		$this->_read_Columns();
        /* Root-ID einlesen */
        $this->getRoot();
        return;
    }

    /**
     * Configuration
     *
     * @param mixed $name
     * @return boolean true = ok
     * @return boolean false=Error
     */
    public function _setStandard($parameter = null)
    {
        global $prefix;
        /* hier Standard-DB-Tabelle festlegen*/
        // geladen
        $this->__set('modulname', $parameter);
        $this->__set('prefix', $prefix);
        $this->__set('dbtable', $prefix . "_content");
        $this->__set('logtable', $prefix . "_content_log");
        /* set standard-publish */
        $this->__set('waiting', -1);
        $this->__set('unpublish', 0);
        $this->__set('publish', 1);
        $this->__set('archive', -2);
        $this->__set('logging', 0);

        /* hier Standard-Einfügerichtung festlegen*/
        $this->__set('insertfirst', false);
        // $this->_createDBTables();
        return;
    }

    public function __set($name, $value = null)
    {
        /* check */
        switch ($name) {
            case "dbtable":
                $this->config[$name] = $value;
                /* check ob Db-Tabelle den Vorgaben entspricht */
                if (!$this->_read_Columns()) return false;
                /* neue RootNode einlesen */
                $this->getRoot();
                break;
        }

        /* in Array eintragen */
        if (is_array($name) && $value === null) {
            foreach ($name as $key => $value) {
                $this->config[$key] = $value;
            }
        } else {
            $this->config[$name] = $value;
        }
        return true;
    }
    public function __get($name)
    {
        if (array_key_exists($name, $this->config)) return $this->config[$name];
        return false;
    }
    /*
    *  Liest Tabellendefinition ein
    *    @return :  boolean
    */

    private function _read_Columns()
    {
        global $prefix;

        $tables = sql_query("SHOW TABLES LIKE '" . $this->config['dbtable'] . "'");
        list($table) = sql_fetch_row($tables);
        if ($this->config['dbtable'] != $table) {
            $this->_createDBTables();
        }

        //if (trim($this->config['modulname']) == '') return false;

        //if (!is_array($this->db_columns)) {
            $result = sql_query("SHOW COLUMNS FROM " . $this->config['dbtable'] . "");
            while ($col = sql_fetch_assoc($result)) {
                $this->db_columns[$col['Field']] = '0';

            }
        //}
		//var_dump($this->db_columns);
		//die();
        /* unbedingt vorhandene Felder in der Tabelle !!! */
        unset ($result);
        if (!array_key_exists('id', $this->db_columns)) return false;
        if (!array_key_exists('leftID', $this->db_columns)) return false;
        if (!array_key_exists('rightID', $this->db_columns)) return false;
        if (!array_key_exists('publish', $this->db_columns)) return false;
        if (!array_key_exists('parent_id', $this->db_columns)) return false;
        if (!array_key_exists('access', $this->db_columns)) return false;
        if (!array_key_exists('owner_id', $this->db_columns)) return false;
        if (!array_key_exists('config', $this->db_columns)) return false;
        if (!array_key_exists('title', $this->db_columns)) return false;
        if (!array_key_exists('status', $this->db_columns)) return false;

        return true;
    }

    /*
    * Überprüft das Nested-Model auf Richtigkeit - alle LeftID's und rightID's
    *
    * return : boolean
    */
    public function _CheckNestedSets()
    {
        if (trim($this->config['modulname']) == '') return false;
        $result1 = sql_query("SELECT rightID FROM " . $this->config['dbtable'] . " WHERE module_name='" . $this->config['modulname'] . "' and leftID=1");
        $result2 = sql_query("SELECT count(id) FROM " . $this->config['dbtable'] . " WHERE module_name='" . $this->config['modulname'] . "'");
        // $result=sql_query("SELECT Sum(leftID+rightID)-((count(id)*2 / 2)+count(id)) FROM ".$this->config['dbtable']." WHERE module_name='".$this->config['modulname']."'");
        list($check1) = sql_fetch_row($result1);
        list($check2) = sql_fetch_row($result2);
        // if ($check==0) return $check;
        unset($result1, $result2);
        return ($check1 / 2 - $check2);
    }

    public function _CheckNestesSetsMore($id = 1)
    {
        $retarray = array();

        $result = sql_query("SELECT s.*,
                            count(*)+(v.leftID >1) As level,
                            FLOOR((s.rightID-s.leftID)/2) as childs ,
                            ((min(v.rightID)-s.rightID-(s.leftID >1))/2) > 0 AS lower,
                            (((s.leftID-max(v.leftID)>1))) AS upper ,
                            ((min(v.leftID)>s.rightID)) AS upperid
                                from " . $this->config['dbtable'] . " as n,
                                     " . $this->config['dbtable'] . " as v,
                                     " . $this->config['dbtable'] . " as s
                              WHERE s.module_name='" . $this->config['modulname'] . "'
                                AND v.module_name='" . $this->config['modulname'] . "'
                                AND n.module_name='" . $this->config['modulname'] . "'
                                AND s.leftID BETWEEN v.leftID AND v.rightID
                                AND s.leftID BETWEEN n.leftID AND n.rightID
                                AND n.id=" . $id . "
                                AND (v.id != s.id OR s.leftID = 1)
                            GROUP BY s.leftID
                            ORDER by level desc
                            ");
        while ($record = sql_fetch_assoc($result)) {
            $result1 = sql_query("SELECT count(*)
                                from " . $this->config['dbtable'] . " as s
                              WHERE s.module_name='" . $this->config['modulname'] . "'
                              AND s.leftID>" . $record['leftID'] . "
                              AND s.rightID<" . $record['rightID'] . "
                            ");
            list($childid) = sql_fetch_row($result1);
            if ($childid != $record['childs']) {
                $retarray[] = "id: " . $record['id'];
            }
        }
        return $retarray;
    }

    public function _renumNestedSet()
    {
        $leftID = 1;
        $parent_id = $this->getRootID();
        $this->tempnodes = array();
        $rightID = $this->_renum_nodes($parent_id, $leftID) + 1;
        $nodes = array($parent_id);
        /* alle db-sätze auf -1 sezten */
        sql_query("UPDATE " . $this->config['dbtable'] . " SET  leftID='-1'  WHERE module_name='" . $this->config['modulname'] . "'");

        /* alle zuordenbare Datensätze richtig einordnen */

        foreach ($this->tempnodes as $node) {
            sql_query("UPDATE " . $this->config['dbtable'] . " SET  leftID=" . $node['leftID'] . ", rightID=" . $node['rightID'] . " WHERE id=" . $node['id']);
            $nodes[] = $node['id'];
        }
        /* Rootnode updaten */
        sql_query("UPDATE " . $this->config['dbtable'] . " SET  leftID=1, rightID=" . $rightID . "  WHERE id=" . $parent_id);
        $this->tempnodes = array();

        /* nichtzuordenbare Datensätze raussuchen */
        $nodelist = implode(",", $nodes);
        $errnodes = sql_query("SELECT * FROM " . $this->config['dbtable'] . " WHERE module_name='" . $this->config['modulname'] . "' and id NOT in (" . $nodelist . ")");
        $errcount = sql_num_rows($errnodes);

        if ($errcount > 0) {
            /* fehlerdokument erstellen */
            $errnode = array('title' => "Errorset " . date("Y-m-d H:i:s"));
            $err_parentid = $this->_addNode(0, $errnode);

            while ($nodes = sql_fetch_assoc($errnodes)) {
                /* neue Parent_id zuordnen in das Fehlerdokument - es wird eine kopie erstellt*/
                $this->_addNode($err_parentid, $nodes);
                /* alten record löschen */
                sql_query("DELETE FROM " . $this->config['dbtable'] . " WHERE module_name='" . $this->config['modulname'] . "' and id=" . $nodes['id']);
            }
        }

        return;
    }
    private function _renum_nodes($parent_id, $leftID)
    {
        $sql = "SELECT id,leftID,rightID,parent_id FROM " . $this->config['dbtable'] . " WHERE parent_id=" . $parent_id . " ORDER BY leftID asc ";
        $result = sql_query($sql);
        $rightID = $leftID;
        while ($node = sql_fetch_assoc($result)) {
            $leftID = $rightID + 1;
            /* erstmal richtig zuordnen */
            $node['leftID'] = $leftID;
            /* jetzt nachschauen, ob childs existieren */
            $rightID = $this->_renum_nodes($node['id'], $leftID);
            /* jetzt rightID erhöhen und zuordnen */
            $rightID++;
            $node['rightID'] = $rightID;
            /* und ab in den Tempspeicher */
            $this->tempnodes[] = $node;
        }
        return $rightID;
    }

    /*
    * Überprüft das Übergebene Array anhand der Tabellendefinition
    * $cat   : array()         Array mit dem Record
    * return : boolean
    */
    private function _checkRecord(&$cat)
    {
        if (trim($this->config['modulname']) == '') return false;

        if (!is_array($cat)) return false;

        $cat = array_intersect_key($cat, $this->db_columns);
        return true;
    }

    /*
    *  emitteln der RootNode  des Moduls
    */
    private function _getRootNode()
    {
        if (trim($this->config['modulname']) == '') return false;

        $result = sql_query("SELECT * FROM " . $this->config['dbtable'] . " WHERE module_name='" . $this->config['modulname'] . "' AND leftID=1 LIMIT 1 ");

        if (sql_num_rows($result) < 1) {
            /* wenn nicht vorhanden, dann einen erstellen - erspart zusätzliche Installation.... */
            $rootRecord = "INSERT INTO " . $this->config['dbtable'] . " SET
                        mid= 0,
                        leftID=1,
                        rightID=2,
                        publish=1,
                        parent_id=0,
                        module_name='" . $this->config['modulname'] . "',
                        owner_id='-1',
                        config='" . serialize(array()) . "',
                        access='0',
                        title='" . $this->config['modulname'] . "',
                        alias=''
                        ";
            $result = sql_query($rootRecord);
            $this->rootID['id'] = sql_insert_id();
            $this->rootID['leftID'] = 1;
            $this->rootID['rightID'] = 2;
        } else {
            $this->rootID = sql_fetch_assoc($result);
        }
        unset($result);
        return true;
    }

    /*
    *  ermittelt die rootID des für den aktuellen Modulnamen
    *
    * return : intval -
    */
    public function getRootID($update = false)
    {
        if (trim($this->config['modulname']) == '') return false;
        if ($this->rootID == false or $update == true) $this->_getRootNode();
        return $this->rootID['id'];
    }

    /*
    *  gibt den Datensatz des RootNodes für den aktuellen Modulnamen zurück
    * $fname = string optional Feldname
    * return : array
    */
    public function getRoot($fname = "", $update = false)
    {
        if (trim($this->config['modulname']) == '') return false;
        if ($this->rootID == false or $update == true) $this->_getRootNode();
        if ($fname != "" and array_key_exists($fname, $this->rootID)) return $this->rootID[$fname];
        return $this->rootID;
    }

    /*
    *  gibt den kompletten Datensatz für den aktuellen Modulnamen zurück
    * $id = intval
    * return : array
    */
    protected function _getNode($id = 0)
    {
        if (intval($id) == 0) $id = $this->getRootId();
        $result = sql_query("SELECT s.*,FLOOR((s.rightID-s.leftID)/2) AS childs ,count(*)+(v.leftID >1) AS level
                                 FROM " . $this->config['dbtable'] . "  AS s," . $this->config['dbtable'] . "  AS v
                                 WHERE s.module_name='" . $this->config['modulname'] . "' AND v.module_name='" . $this->config['modulname'] . "'
                                 AND s.leftID BETWEEN v.leftID AND v.rightID
                                 AND s.id=$id
                                 AND s.leftID>1
                                 ");
        return sql_fetch_assoc($result);
    }

    /*
    *  gibt den kompletten Datensatz für die ID zurück - Modulname ist uninteressant
    * $id = intval
    * return : array
    */
    protected function _getNodeFromID($id = 0)
    {
        if (intval($id) == 0) return false;
		
        $result = sql_query("SELECT s.*,FLOOR((s.rightID-s.leftID)/2) AS childs ,count(*)+(v.leftID >1) AS level
                                 FROM " . $this->config['dbtable'] . "  AS s
                                 WHERE s.id=$id
                                 ");
        return sql_fetch_assoc($result);
    }
    /*
    *  gibt den kompletten Datensatz für den aktuellen Modulnamen und der Kennung 'mid' zurück
  * $mid = intval
    * return : array
    */
    protected function _getNode_mid($mid)
    {
        $result = sql_query("SELECT s.*,FLOOR((s.rightID-s.leftID)/2) AS childs ,count(*)+(v.leftID >1) AS level
                                 FROM " . $this->config['dbtable'] . "  AS s," . $this->config['dbtable'] . "  AS v
                                 WHERE s.module_name='" . $this->config['modulname'] . "' AND v.module_name='" . $this->config['modulname'] . "'
                                 AND s.leftID BETWEEN v.leftID AND v.rightID
                                 AND s.mid=$mid
                                 AND s.leftID>1
                                 ORDER BY s.leftID asc LIMIT 1");
        return sql_fetch_assoc($result);
    }
    /**
     * Gibt den Pfad des Node zurück
     *
     * @parameter  $ : id intval
     * @return :    array
     */
    public function getRootNode($id)
    {
        if ($id == 0) $id = $this->getRootID();

        $result = sql_query("SELECT p.* FROM " . $this->__get('dbtable') . " as p," . $this->__get('dbtable') . " as n
                                WHERE n.module_name='" . $this->__get('modulname') . "'
                                      and p.module_name='" . $this->__get('modulname') . "'
                                      and n.id=$id
                                      and p.leftID < n.leftID
                                      AND p.rightID >n.rightID
                                      AND p.publish=1
                                      AND p.leftID>1
                                ORDER BY p.leftID
                                ");
        $output = array();
        $output = sql_fetch_assoc($result);
        unset ($result);
        return $output;
    }

    /*
    *  schreibt den kompletten Root-Datensatz für den aktuellen Modulnamen
    *
    * $cat : array - Feldinhalte  = array("felname"=>"inhalt",.....)
    */
    protected function _setRootRecord ($cat = array())
    {
        // if ($this->checkRecord($cat)==FALSE) return false; // check Record
        /* Insert SET-String aufbauen aus dem Array */
        $setstr = "";

        foreach ($cat as $key => $value) {
            switch ($key) {
                case "date_edit":
                case "id":
                case "parent_id":
                case "leftID":
                case "rightID":
                case "module_name":
                    $keyflag = false;
                    break;
                default:
                    $keyflag = true;
                    break;
            }
            if ($keyflag) $setstr .= " " . $key . "='" . $value . "',";
        }

        $setstr .= " date_edit='" . time() . "',module_name='" . $this->config['modulname'] . "' where id='" . $this->getRootID() . "' ";

        /* jetzt Datensatz ändern */
        sql_query("UPDATE " . $this->config['dbtable'] . " SET " . $setstr . " ");
        $this->rootrecord = false;
        $this->getRoot("", true);
        return;
    }

    /*
    *  gibt Records anhand des Filters für den aktuellen Modulnamen zurück
    *  $filter : array - array("feldname {equal}'value'",...)
    * return : array
    */
    protected function _getNodes($filter = array(), $orderby = "", $limit = "")
    {
        $selectfilter = "";
        if (!is_array($filter)) {
            $filter = $this->getFilter($filter);
            if (!$filter) return false;
        }

        $temp = "";
        foreach($filter as $key) {
            if (array_key_exists($key['value'], $this->db_columns)) $key['value'] = "" . $key['value'];
            $temp .= " " . trim($key['function']) . " " . $key['field'] . $key['equal'] . $key['value'] . " ";
        }

        $limit = (trim($limit) != "")?"LIMIT " . $limit:"";
        $result = sql_query("SELECT * ,FLOOR((rightID-leftID)/2) AS childs
                         FROM " . $this->config['dbtable'] . "
                         WHERE module_name='" . $this->config['modulname'] . "'
                             AND leftID>1 " . $temp . "
                            AND TRUE " . $orderby . " " . $limit);
        $output = array();
        while ($record = sql_fetch_assoc($result)) {
            $output[$record['id']] = $record; // Ausgabearray zusammenstellen
        }
        unset ($result);
        return $output;
    }

    /*
    *  gibt Records anhand des Filters für den aktuellen Modulnamen zurück
    *  $ids : array - array("id1","id2","id3",...)
    * return : array
    */
    public function _getNodesFromId($ids = array())
    {
        $sqlfilter = implode(",", $ids);
        // foreach ($ids as $id) {
        // $sqlfilter .= $id . ","; //Filter zu einem String zusammenführen
        // }
        // $sqlfilter = substr($sqlfilter, 0, -1);
        $result = sql_query("SELECT * FROM " . $this->config['dbtable'] . " WHERE module_name='" . $this->config['modulname'] . "' AND leftID>1  AND id IN(" . $sqlfilter . ") ");
        $output = array();
        while ($record = sql_fetch_assoc($result)) {
            $output[$record['id']] = $record; // Ausgabearray zusammenstellen
        }
        unset ($result);
        return $output;
    }

    /*
    *  gibt TRUE zurück, wenn der Record innerhalb eines Zeitraumes erstellt/geändert wurde
    *  $days : anzahl der Tage die geprüft werden sollen
    * return : false / 1,2
    */
    public function CheckRecord($days, $id)
    {
        $Cdays = intval($days);
        if ($Cdays == 0) return false;
        $cstart = time() - (86400 * $Cdays);
        $result = sql_query("SELECT count(*) as cnew,date_created,date_edit FROM " . $this->config['dbtable'] . " WHERE
                    module_name='" . $this->config['modulname'] . "' AND
                    id=" . $id . " AND
                    date_edit >" . $cstart . "
                      ");
        $output = sql_fetch_assoc($result);
        if ($output['cnew'] == 0) return false;

        if ($output['date_created'] > ($output['date_edit']-86400)) return 1; // New
        if (($output['date_created'] + 86400) < $output['date_edit']) return 2; // Changed
        return false;
    }

    /*
    *  gibt Records anhand des Erstellungs-Datums (absteigend) zurück über ein Zweig !!!
    *  $book : id des Zweiges
    *  $count : anzahl der Ergebnisse
    * return : array
    */
    public function _getNodes_New($count, $cmax = 5, $id = 0, $other = "TRUE")
    {
        $Ccount = intval($count);
        $Ccmax = intval($cmax);
        if ($Ccount == 0) return false;
        if ($id == 0) {
            $id = $this->getRootID();
            $node = $this->getRoot();
        } else {
            $node = $this->_getNode($id);
        }
        $cstart = time() - (86400 * $Ccount);

        if ($node['rightID']-1 > $node['leftID']) {
            $result = sql_query("SELECT * FROM " . $this->config['dbtable'] . " WHERE
                    module_name='" . $this->config['modulname'] . "' AND
                    leftID BETWEEN " . $node['leftID'] . " AND " . $node['rightID'] . " AND
                    publish=1 AND
                    date_created>" . $cstart . "
                      AND " . $other . "
                    ORDER BY date_created DESC
                    LIMIT $Ccmax ");
            $output = array();
            while ($record = sql_fetch_assoc($result)) {
                $output[$record['id']] = $record; // Ausgabearray zusammenstellen
            }
            unset ($result);
            return $output;
        }
        return false;
    }

    /*
    *  gibt Records anhand des Änderungs-Datums (absteigend) zurück über ein Zweig !!!
    *  $book : id des Zweiges
    *  $count : anzahl der Ergebnisse
    * return : array
    */
    public function _getNodes_LastChange($count, $cmax = 5, $id = 0, $other = "TRUE")
    {
        $Ccount = intval($count);
        $Ccmax = intval($cmax);
        if ($Ccount == 0) return false;
        if ($id == 0) {
            $id = $this->getRootID();
            $node = $this->getRoot();
        } else {
            $node = $this->_getNode($id);
        }

        $cstart = time() - (86400 * $Ccount);

        if ($node['rightID']-1 > $node['leftID']) {
            $result = sql_query("SELECT * FROM " . $this->config['dbtable'] . " WHERE
                    module_name='" . $this->config['modulname'] . "' AND
                    leftID BETWEEN " . $node['leftID'] . " AND " . $node['rightID'] . " AND
                    publish=1 AND
                    date_edit>" . $cstart . " AND
                    date_edit>(date_created+86400)
                      AND " . $other . "
                    ORDER BY date_edit DESC
                    LIMIT $Ccmax ");
            $output = array();
            while ($record = sql_fetch_assoc($result)) {
                $output[$record['id']] = $record; // Ausgabearray zusammenstellen
            }
            unset ($result);
            return $output;
        }
        return false;
    }

    /**
     * Gibt den vorherigen Node zurück
     *
     * @parameter  $ : id intval
     * @return :    intval
     */
    public function _getNodeUpper($id, $bookid)
    {
        $output = array();
        if ($id == 0) return false;
        $book = $this->_getNode($bookid);

        $result = sql_query("SELECT p.* FROM " . $this->__get('dbtable') . " as p," . $this->__get('dbtable') . " as n
                                WHERE n.module_name='" . $this->__get('modulname') . "'
                                      and p.module_name='" . $this->__get('modulname') . "'
                                      and n.id=$id
                                      AND p.leftID < n.leftID
                                      and p.leftID BETWEEN " . $book['leftID'] . " AND " . $book['rightID'] . "
                                      and p.leftID>1
                                      AND p.publish=1
                                      ORDER BY p.leftID desc
                                ");
        $output = sql_fetch_assoc($result);
        return $output;
    }

    /**
     * Gibt den nachfolgenden Node zurück
     *
     * @parameter  $ : id intval
     * @return :    intval
     */
    public function _getNodeLower($id, $bookid)
    {
        $output = array();
        if ($id == 0) return false;
        $book = $this->_getNode($bookid);

        $result = sql_query("SELECT p.* FROM " . $this->__get('dbtable') . " as p," . $this->__get('dbtable') . " as n
                                WHERE n.module_name='" . $this->__get('modulname') . "'
                                      AND p.module_name='" . $this->__get('modulname') . "'
                                      AND n.id=$id
                                      AND p.leftID > n.leftID
                                      AND p.leftID>1
                                      AND p.publish=1
                                      AND p.leftID BETWEEN " . $book['leftID'] . " AND " . $book['rightID'] . "
                                      ORDER BY p.leftID asc
                                ");
        $output = sql_fetch_assoc($result);
        return $output;
    }

    /**
     * fügt einen neuen Datensatz ein
     *
     * @parameter  $ : $parentid intval    - ParentRecord
     * @parameter  $ : $record array(mixed) - Record
     * @return Insert -ID or false
     */
    public function _addNode($parentid = 0, $record = array())
    {
        if ($this->_checkRecord($record) == false) return false; // Check record
        /* Parent-Knoten ermitteln*/
        // $result=sql_query("SELECT id,leftID,rightID FROM ".$this->config['dbtable']." WHERE module_name='".$this->config['modulname']."' AND id=".$parentid." ");
        $result = $this->_getNode($parentid);

        if ($result['id'] == $parentid and $parentid > 0) {
            $left = $result['leftID'];
            $right = $result['rightID'];
        } else {
            $left = 1;
            $parentid = $this->rootID['id']; // auf Root-Id setzen
            $right = $this->rootID['rightID'];
        }
        unset ($result);

        if ($left < 1 or $right <= $left) return false;

        /* Tabelle vorsorglich sperren um paralleles Schreiben zu unterbinden und die Update-Aktion weiter unten zu beschleunigen*/
        sql_system_query ("LOCK TABLES " . $this->config['dbtable'] . " WRITE");

        if ($this->config['insertfirst']) {
            /* nachfolgende Knoten verschieben*/
            sql_query("update " . $this->config['dbtable'] . " SET rightID=rightID+2 WHERE rightID >= " . $left . " and module_name='" . $this->config['modulname'] . "'");
            sql_query("update " . $this->config['dbtable'] . " SET leftID =leftID +2 WHERE leftID  > " . $left . "  and module_name='" . $this->config['modulname'] . "'");
            /* Knotennummern für den Insert festlegen */
            $record['leftID'] = $left + 1;
            $record['rightID'] = $left + 2 ;
        } else {
            /* nachfolgende Knoten verschieben*/
            sql_query("update " . $this->config['dbtable'] . " SET rightID=rightID+2 WHERE rightID >= " . $right . " and module_name='" . $this->config['modulname'] . "'");
            sql_query("update " . $this->config['dbtable'] . " SET leftID =leftID +2 WHERE leftID  > " . $right . "  and module_name='" . $this->config['modulname'] . "'");
            /* Knotennummern für den Insert festlegen */
            $record['leftID'] = $right;
            $record['rightID'] = $right + 1 ;
        }
		
        /* Tabelle wieder freigeben !!!! */
        sql_system_query ("UNLOCK TABLES "); //".$this->config['dbtable']."
		
        $record['parent_id'] = $parentid;
        $record['module_name'] = $this->config['modulname'];
        if (array_key_exists('date_created',$record)) {
			$record['date_created']=intval($record['date_created'] );
			} else {
			$record['date_created']=time();
		}
		$record['date_created'] =($record['date_created']==0)?time():$record['date_created'] ;
        $record['date_edit'] = $record['date_created'];
        //$record['id'] = '';
        // $record['views']=0;
        /* Insert SET-String aufbauen aus dem Array*/
        $setstr = "";

        foreach ($record as $key => $value) {
            $setstr .= " " . $key . "='" . $value . "',";
        }
        $setstr = substr($setstr, 0, -1); // letztes Komma entfernen
        /* jetzt Datensatz einfügen */
		//$dbid = pmxDb::getHandle();
        sql_system_query("INSERT INTO " . $this->config['dbtable'] . " SET " . $setstr . ""); // jetzt einfügen
		
        $insertid = sql_insert_id();
        // $this->writelog($insertid,"ADD",$record['title']);
        return $insertid;
    }

    /**
     * Update Record
     *
     * @parameter  $ : $cid intval    - Node
     * @parameter  $ : $record array(mixed) - Record
     */

    public function _updateNode ($cid = 0, $cat = array(), $update_time = true)
    {
        if (($cid == 0) or ($cid == $this->rootID))return false;

        if ($this->_checkRecord($cat) == false) return false; // check Record
        /* Insert SET-String aufbauen aus dem Array */
        $setstr = "";

        foreach ($cat as $key => $value) {
            switch ($key) {
                case "date_edit":
                case "id":
                case "parent_id":
                case "leftID":
                case "rightID":
                case "module_name":
                    $keyflag = false;
                    break;
                default:
                    $keyflag = true;
                    break;
            }
            if ($keyflag) $setstr .= " " . $key . "='" . $value . "',";
        }
        $setstr .= ($update_time)?" date_edit='" . time() . "',":"";
        $setstr .= "module_name='" . $this->config['modulname'] . "' where id='" . $cid . "' ";

        /* jetzt Datensatz ändern */
        sql_query("UPDATE " . $this->config['dbtable'] . " SET " . $setstr . " ");

        /* get parent_id */
        $id = $this->getParentID($cid);
        if ($id) {
            /* check parent rights */
            $this->update_rights($id);
        }
        /* and now , check this rights */
        $this->update_rights($cid);
        // $this->writelog($cid,"UPDATE");
        return $this->_getNode($cid);
    }

    /**
     * Update Record
     *
     * @parameter  $ : $cid intval    - Node
     * @parameter  $ : $record array(mixed) - Record
     */

    protected function _updateNodes ($cids = array(), $cat = array(), $update_time = true)
    {
        if (!is_array($cids))return false;

        if ($this->_checkRecord($cat) == false) return false; // check Record
        /* Insert SET-String aufbauen aus dem Array */
        $setstr = "";
        $records = implode (",", $cids);

        foreach ($cat as $key => $value) {
            switch ($key) {
                case "date_edit":
                case "id":
                case "parent_id":
                case "leftID":
                case "rightID":
                case "module_name":
                case "config":
                case "info":
                    $keyflag = false;
                    break;
                default:
                    $keyflag = true;
                    break;
            }
            if ($keyflag) $setstr .= " " . $key . "='" . $value . "',";
        }
        $setstr .= ($update_time)?" date_edit='" . time() . "',":"";
        $setstr .= " WHERE module_name='" . $this->config['modulname'] . "' AND id IN(" . $records . ") ";

        /* jetzt Datensatz ändern */
        sql_query("UPDATE " . $this->config['dbtable'] . " SET " . $setstr . " ");

        return true;
    }
    /**
     * Update Field +1
     *
     * @parameter  $ : $cid intval    - Node
     * @parameter  $ : $field - Fieldname
     */

    public function update_field_1 ($cid = 0, $field)
    {
        /* Insert SET-String aufbauen aus dem Array */
        $setstr = "";

        switch ($field) {
            case "date_edit":
            case "id":
            case "parent_id":
            case "leftID":
            case "rightID":
            case "module_name":
                $keyflag = false;
                break;
            default:
                $keyflag = true;
                break;
        }
        if ($keyflag) {
            $setstr .= $field . "=" . $field . "+1,";
            $setstr .= "module_name='" . $this->config['modulname'] . "' where id='" . $cid . "' ";
            /* jetzt Datensatz ändern */
            sql_query("UPDATE " . $this->config['dbtable'] . " SET " . $setstr . " ");
        }
        return;
    }

    /**
     * Update Field
     *
     * @parameter  $ : $cid intval    - Node
     * @parameter  $ : $field - Fieldname
     * @parameter  $ : $value
     */

    public function update_field ($cid = 0, $field, $value)
    {
        /* Insert SET-String aufbauen aus dem Array */
        $setstr = "";

        switch ($field) {
            case "date_edit":
            case "id":
            case "parent_id":
            case "leftID":
            case "rightID":
            case "module_name":
                $keyflag = false;
                break;
            default:
                $keyflag = true;
                break;
        }
        if ($keyflag) {
            $setstr .= $field . "=" . $value . ", " ;
            $setstr .= "module_name='" . $this->config['modulname'] . "' where id='" . $cid . "' ";
            /* jetzt Datensatz ändern */
            sql_query("UPDATE " . $this->config['dbtable'] . " SET " . $setstr . " ");
        }
        return;
    }

    /**
     * Get Field
     *
     * @parameter  $ : $cid intval    - Node
     * @parameter  $ : $field - Fieldname
     * return $value
     */

    public function get_field ($id = 0, $field)
    {
        $node = $this->_getNode($id);

        return $node[$field];
    }
    /**
     * Delete Node
     *
     *       Subnodes  moved to ParentNode
     *
     * @parameter  $ :  $cid intval
     * @return :
     */

    public function _deleteNode ($cid)
    {
        /* zu löschendes Element lesen */
        $result = sql_query("SELECT leftID,rightID,parent_id FROM " . $this->config['dbtable'] . " where id=" . $cid . "");
        if (sql_num_rows($result) < 1) return false;
        list($left, $right, $parentid) = sql_fetch_row($result);
        // sql_query ("LOCK TABLES ".$this->config['dbtable']." WRITE");
        /* Node löschen */
        sql_query ("Delete FROM " . $this->config['dbtable'] . " where id=" . $cid . "");

        /* moveing subnodes */
        sql_query ("Update  " . $this->config['dbtable'] . " set leftID=leftID-1, rightID=rightID-1, parent_id=" . $parentid . " WHERE leftID BETWEEN " . $left . " AND " . $right . " and module_name='" . $this->config['modulname'] . "'");

        /* updates other nodes */
        sql_query ("Update  " . $this->config['dbtable'] . " set leftID=leftID-2 WHERE leftID>" . $right . " and module_name='" . $this->config['modulname'] . "'");
        sql_query ("Update  " . $this->config['dbtable'] . " set rightID=rightID-2 WHERE rightID>" . $right . " and module_name='" . $this->config['modulname'] . "'");

        /* updates rights */
        $this->update_rights($parentid);
        // sql_query ("UNLOCK TABLES ");
        // $this->writelog($cid,"DELETE");
        return;
    }

    /**
     * Delete Nodes
     *
     *       Subnodes deleted
     *
     * @parameter  $ :  $cid intval
     * @return :
     */

    public function delete_all ($cid)
    {
        /* zu löschendes Element lesen */
        $result = sql_query("SELECT leftID,rightID,parent_id FROM " . $this->config['dbtable'] . " where id=" . $cid . "");
        if (sql_num_rows($result) < 1) return false;
        list($left, $right, $parentid) = sql_fetch_row($result);
        // sql_query ("LOCK TABLES ".$this->config['dbtable']." WRITE");
        /* Node löschen */
        sql_query ("Delete FROM " . $this->config['dbtable'] . " where id=" . $cid . "");

        /* delete subnodes */
        sql_query ("Delete FROM  " . $this->config['dbtable'] . " WHERE leftID BETWEEN " . $left . " AND " . $right . " and module_name='" . $this->config['modulname'] . "'");

        $width = $right - $left + 1;
        /* updates other nodes */
        sql_query ("Update  " . $this->config['dbtable'] . " set leftID=leftID-" . $width . " WHERE leftID>" . $right . " and module_name='" . $this->config['modulname'] . "'");
        sql_query ("Update  " . $this->config['dbtable'] . " set rightID=rightID-" . $width . " WHERE rightID>" . $right . " and module_name='" . $this->config['modulname'] . "'");

        /* updates rights */
        // $this->update_rights($parentid);
        // sql_query ("UNLOCK TABLES ");
        return;
    }
    /**
     * Move Node up
     *
     *       moved source-Node one position
     *
     * @parameter  $ :      $source_id intval
     * @return :  boolean
     */
    public function move_up ($source_id)
    {
        /* read source-node */
        $source = sql_query("SELECT leftID,rightID,parent_id FROM " . $this->config['dbtable'] . " where id=" . $source_id . " and module_name='" . $this->config['modulname'] . "'");
        if (sql_num_rows($source) < 1) return false;
        list($source_left, $source_right, $source_parentid) = sql_fetch_row($source);

        /* read target-node */
        $target = sql_query("SELECT leftID,rightID,parent_id FROM " . $this->config['dbtable'] . " where parent_id=" . $source_parentid . " and rightID=" . intval($source_left-1) . " and leftID>1 and module_name='" . $this->config['modulname'] . "'");
        if (sql_num_rows($target) < 1) return false;

        list($target_left, $target_right, $target_parentid) = sql_fetch_row($target);
        // prüfen auf source_left > target_left
        if ($source_left < $target_left) return false;

        $source_width = $source_right - $source_left + 1;
        $target_width = $target_right - $target_left + 1;
        $move_width = $source_left - $target_left + $source_width;

        unset($source, $target);
        // sql_query ("LOCK TABLES ".$this->config['dbtable']." WRITE");
        /* Target Knoten verschieben*/
        sql_query("UPDATE " . $this->config['dbtable'] . " SET rightID=rightID+" . $source_width . " WHERE rightID > " . $target_left . " and module_name='" . $this->config['modulname'] . "'");
        sql_query("UPDATE " . $this->config['dbtable'] . " SET leftID =leftID +" . $source_width . " WHERE leftID  >=" . $target_left . " and module_name='" . $this->config['modulname'] . "'");

        /* move source-nodes */
        sql_query ("UPDATE " . $this->config['dbtable'] . " SET leftID=leftID-" . $move_width . ", rightID=rightID-" . $move_width . "
        WHERE  module_name='" . $this->config['modulname'] . "' AND leftID BETWEEN " . intval($source_left + $source_width) . " AND " . intval($source_right + $source_width) . "");

        /* move target-nodes */
        sql_query ("UPDATE  " . $this->config['dbtable'] . " SET leftID=leftID-" . $source_width . " WHERE  module_name='" . $this->config['modulname'] . "' AND leftID>" . intval($source_right + $source_width) . " ");
        sql_query ("UPDATE  " . $this->config['dbtable'] . " SET rightID=rightID-" . $source_width . " WHERE  module_name='" . $this->config['modulname'] . "' AND rightID>" . intval($source_right + $source_width) . "");
        // sql_query ("UNLOCK TABLES ");
        /* updates rights */
        // $this->update_rights($parent_id);
        // $this->writelog($source_id,"MOVEUP");
        return true;
    }

    /**
     * Move Node down
     *
     *       moved source-Node one position
     *
     * @parameter  $ :      $source_id intval
     * @return :  boolean
     */
    public function move_dn ($source_id)
    {
        /* read source-node */
        $source = sql_query("SELECT leftID,rightID,parent_id FROM " . $this->config['dbtable'] . " where id=" . $source_id . " and module_name='" . $this->config['modulname'] . "'");
        if (sql_num_rows($source) < 1) return false;
        list($source_left, $source_right, $source_parentid) = sql_fetch_row($source);

        /* read target-node */
        $source = sql_query("SELECT leftID,rightID,parent_id FROM " . $this->config['dbtable'] . " where parent_id=" . $source_parentid . " and leftID=" . intval($source_right + 1) . " and leftID>1 and module_name='" . $this->config['modulname'] . "'");
        if (sql_num_rows($source) < 1) return false;

        /* read source-node again */
        $target = sql_query("SELECT leftID,rightID,parent_id FROM " . $this->config['dbtable'] . " where id=" . $source_id . " and module_name='" . $this->config['modulname'] . "'");
        if (sql_num_rows($target) < 1) return false;

        list($source_left, $source_right, $source_parentid) = sql_fetch_row($source);
        list($target_left, $target_right, $target_parentid) = sql_fetch_row($target);

        $source_width = $source_right - $source_left + 1;
        $target_width = $target_right - $target_left + 1;
        $move_width = $source_left - $target_left + $source_width;

        unset($source, $target);
        // sql_query ("LOCK TABLES ".$this->config['dbtable']." WRITE");
        /* Target Knoten verschieben*/
        sql_query("UPDATE " . $this->config['dbtable'] . " SET rightID=rightID+" . $source_width . " WHERE rightID > " . $target_left . " and module_name='" . $this->config['modulname'] . "'");
        sql_query("UPDATE " . $this->config['dbtable'] . " SET leftID =leftID +" . $source_width . " WHERE leftID  >=" . $target_left . " and module_name='" . $this->config['modulname'] . "'");

        /* move source-nodes */
        sql_query ("UPDATE " . $this->config['dbtable'] . " SET leftID=leftID-" . $move_width . ", rightID=rightID-" . $move_width . "
        WHERE  module_name='" . $this->config['modulname'] . "' AND leftID BETWEEN " . intval($source_left + $source_width) . " AND " . intval($source_right + $source_width) . "");

        /* move target-nodes */
        sql_query ("UPDATE  " . $this->config['dbtable'] . " SET leftID=leftID-" . $source_width . " WHERE  module_name='" . $this->config['modulname'] . "' AND leftID>" . intval($source_right + $source_width) . " ");
        sql_query ("UPDATE  " . $this->config['dbtable'] . " SET rightID=rightID-" . $source_width . " WHERE  module_name='" . $this->config['modulname'] . "' AND rightID>" . intval($source_right + $source_width) . "");
        // sql_query ("UNLOCK TABLES ");
        /* updates rights */
        // $this->update_rights($parent_id);
        // $this->writelog($source_id,"MOVEDN");
        return true;
    }

    /**
     * Move Node s to new ParentID
     *
     *       moved source-Node
     *
     * @parameter  $ :      $source_id intval
     * @return :  boolean
     */
    public function _move ($source_id, $parentid)
    {
        if ($source_id == $parentid) return false;

        if ($parentid == 0)$parentid = $this->rootID['id'];

        /* read source-node */
        $source = sql_query("SELECT leftID,rightID,parent_id FROM " . $this->config['dbtable'] . " where id=" . $source_id . " and module_name='" . $this->config['modulname'] . "'");
        if (sql_num_rows($source) < 1) return false;
        list($source_left, $source_right, $source_parentid) = sql_fetch_row($source);
        if ($source_parentid == $parentid) return false ;

        /* read target-node */
        $target = sql_query("SELECT leftID,rightID,parent_id FROM " . $this->config['dbtable'] . " where id=" . $parentid . " and module_name='" . $this->config['modulname'] . "'");
        if (sql_num_rows($source) < 1) return false;
        list($target_left, $target_right, $target_parentid) = sql_fetch_row($target);

        /* ein übergeordneter Knoten kann nicht in einen sich selbst untergeordneten Knoten verschoben werden */
        if ($target_left > $source_left AND $target_right < $source_right) return false;

        /* werte für die Verschiebung berechnen*/
        $source_width = $source_right - $source_left + 1;
        unset($source, $target);
        // sql_query ("LOCK TABLES ".$this->config['dbtable']." WRITE");
        /* am anfang des Knotens einfügen */
        $target_pos_right = $target_left;
        $target_pos_left = $target_left;

        $move_width = ($source_left > $target_left)?$target_left - $source_left - $source_width + 1:$target_left - $source_left + 1;
        $move_source_left = ($source_left > $target_left)?intval($source_left + $source_width):$source_left;
        $move_source_right = ($source_left > $target_left)?intval($source_right + $source_width):$source_right;
        $move_other = ($source_left > $target_left)?$source_right + $source_width:$source_right;
        /* Ziel freiräumen */
        sql_query("UPDATE " . $this->config['dbtable'] . " SET rightID=rightID+" . $source_width . " WHERE rightID>" . $target_pos_right . " and module_name='" . $this->config['modulname'] . "'");
        sql_query("UPDATE " . $this->config['dbtable'] . " SET leftID =leftID +" . $source_width . " WHERE leftID>" . $target_pos_left . " and module_name='" . $this->config['modulname'] . "'");

        /* move source-nodes */
        sql_query ("UPDATE " . $this->config['dbtable'] . " SET leftID=leftID+(" . $move_width . "), rightID=rightID+(" . $move_width . ")
        WHERE  module_name='" . $this->config['modulname'] . "' AND leftID BETWEEN " . intval($move_source_left) . " AND " . $move_source_right . "");
        sql_query("UPDATE " . $this->config['dbtable'] . " SET parent_id=" . $parentid . " WHERE id=" . $source_id);

        /* move target-nodes */
        sql_query ("UPDATE  " . $this->config['dbtable'] . " SET leftID=leftID-" . intval($source_width) . " WHERE  module_name='" . $this->config['modulname'] . "' AND leftID>" . intval($move_other) . " ");
        sql_query ("UPDATE  " . $this->config['dbtable'] . " SET rightID=rightID-" . intval($source_width) . " WHERE  module_name='" . $this->config['modulname'] . "' AND rightID>" . intval($move_other) . "");
        // sql_query ("UNLOCK TABLES ");
        /* updates rights */
        $this->update_rights($parentid);
        // $this->writelog($source_id,"MOVE");
        return true;
    }

    /**
     * copy Record
     *
     * @parameter  $ : $cid intval
     * @return :
     */
    public function copyNode($cid)
    {
        $source = $this->_getNode($cid);
        // $update=array("title"=>0,"parent_id"=>0,"text1"=>0,"text2"=>0,"position"=>0,"owner_id"=>0);
        // $source=array_intersect_key($source,$update);
        $source['publish'] = 0;
		unset($source['id']);
        $id = $this->_addNode($source['parent_id'], $source);
        // $this->writelog($cid,"COPY");
        return $id;
    }
    /**
     * Update Rights Subnodes
     *
     * @parameter  $ : $cid intval
     * @return :
     */
    public function update_rights($cid)
    {
        /* read node */
        $result = sql_query("SELECT leftID,rightID,parent_id,access,publish,owner_id,language FROM " . $this->config['dbtable'] . " where id=" . $cid . "");
        if (sql_num_rows($result) == 0) return false;
        list($left, $right, $parentid, $access, $publish, $owner_id, $language) = sql_fetch_row($result);

        /* update rights subnodes  */
        sql_query("UPDATE " . $this->config['dbtable'] . " SET
                    publish=IF(publish > " . $publish . ",IF(publish=" . $this->config['archive'] . ",publish," . $publish . "),publish),
                    access=IF(access < '" . $access . "'," . $access . ",access),
                    owner_id=IF((" . $owner_id . " > 0 OR " . $owner_id . " = '')," . $owner_id . ",owner_id),
                    language='" . $language . "'
                    WHERE rightID BETWEEN " . $left . " AND " . $right . " and module_name='" . $this->config['modulname'] . "' ");
        // $this->writelog($cid,"UPDATE_RIGHTS");
        return;
    }

    public function getParentRecord($cid)
    {
        $result = sql_query("SELECT parent.* FROM " . $this->config['dbtable'] . " as parent, " . $this->config['dbtable'] . " as child WHERE
                            parent.id=child.parent_id
                            and parent.leftID>1
                            and child.id='" . $cid . "'
                            and child.module_name='" . $this->config['modulname'] . "'
                            and parent.module_name='" . $this->config['modulname'] . "' LIMIT 1");
        if (sql_num_rows($result) == 1) {
            $id = sql_fetch_assoc($result);
            return $id;
        } else {
            return false;
        }
    }

    public function getChildRecords($cid)
    {
        $result = sql_query("SELECT parent.* FROM " . $this->config['dbtable'] . " as parent, " . $this->config['dbtable'] . " as child WHERE
                            parent.id=child.parent_id
                            and parent.leftID>1
                            and child.id='" . $cid . "'
                            and child.module_name='" . $this->config['modulname'] . "'
                            and parent.module_name='" . $this->config['modulname'] . "' LIMIT 1");
        if (sql_num_rows($result) == 1) {
            $id = sql_fetch_assoc($result);
            return $id;
        } else {
            return false;
        }
    }
    /**
     * pmxContent::getParentID()
     *
     * @param mixed $cid
     * @return
     */
    public function getParentID($cid)
    {
        $parent_record = $this->getParentRecord($cid);
        if (is_array($parent_record)) {
            return $parent_record['id'];
        }
        return false;
    }

    /**
     * holt alle Datensätze aus der DB in ein Array
     *
     * @parameter  $ :      $basecat intval    - baseNode
     *                       $filter  mixed
     * @return :        array()
     */
    private function _getNodelist ($basecat = 1, $filter = array(), $sqloutput = "", $start = 0, $limit = 0, $extrafilter = "")
    {
        if ($this->config['modulname'] == "") return false;

        if ($basecat < 1) $basecat = $this->getRootID();

        /* filter prüfen */
        if (is_array($filter)) {
            $temp = "";
            foreach($filter as $key) {
                if (array_key_exists($key['value'], $this->db_columns)) $key['value'] = "s." . $key['value'];
                $temp .= " " . trim($key['function']) . " s." . $key['field'] . $key['equal'] . "" . $key['value'] . " ";
            }
            $selectfilter = $temp;
        } else {
            $selectfilter = "";
        }
        // $selectfilter=(strtolower(substr(trim($filter),0,3))!="and" and trim($filter)!="")?" and ".$filter:$filter;
        /* erstmal array löschen */
        $this->catarray = array();

        /* Basis-Node abfragen*/
        $result1 = sql_query("SELECT id FROM " . $this->config['dbtable'] . " where module_name='" . $this->config['modulname'] . "' and id=$basecat");
        if (sql_num_rows($result1) == 0) {
            $bid = 1;
            /*$bleft=$this->getRoot['leftID'];
                    $bright=$this->getRoot['rightID'];*/
        } else {
            list($bid) = sql_fetch_row($result1);
        }
        // $outdefault=array("id","title","parent_id","publish","access");
        $outputsql = "";
        if ($sqloutput != "" and is_array($sqloutput)) {
            foreach($sqloutput as $sqlfield) {
                $outputsql .= "s." . $sqlfield . ", ";
            }
        } else {
            $outputsql = "s.id, s.parent_id,s.publish,s.access,s.title,s.import,";
        }
        /*jetzt endlich abfrage */
        $result = sql_query("SELECT " . $outputsql . "
                                    count(*)+(v.leftID >1) As level,
                                    FLOOR((s.rightID-s.leftID)/2) as childs ,
                                    ((min(v.rightID)-s.rightID-(s.leftID >1))/2) > 0 AS lower,
                                    (((s.leftID-max(v.leftID)>1))) AS upper ,
                                    ((min(v.leftID)>s.rightID)) AS upperid
                                        from " . $this->config['dbtable'] . " as n,
                                             " . $this->config['dbtable'] . " as v,
                                             " . $this->config['dbtable'] . " as s
                                        WHERE s.module_name='" . $this->config['modulname'] . "'
                                        AND v.module_name='" . $this->config['modulname'] . "'
                                        AND n.module_name='" . $this->config['modulname'] . "'
                                        AND n.id='" . $bid . "'
                                        AND s.leftID >'1'
                                        AND s.leftID BETWEEN v.leftID AND v.rightID
                                        AND s.leftID BETWEEN n.leftID AND n.rightID
                                        AND (v.id != s.id OR s.leftID = 1)
                                        " . $selectfilter . "
                                        " . $extrafilter . "
                                    GROUP BY s.leftID
                                    ORDER by s.leftID
                                    ");
        // AND s.id!='".$bid."'
        /* alle gefundenen Datensätze in Array speichern */
        $i = 0;
        $this->content_count = sql_num_rows($result);
        $limit = ($limit == 0)?$this->content_count + 1:$start + $limit;
        while ($cat = sql_fetch_assoc($result)) {
            if ($i >= $start and $i <= ($limit-1)) {
                $this->catarray[$cat['id']] = $cat;
            }
            $i++;
        }

        unset ($result, $result1);
        return $this->catarray;
    }

    /* Filter für DB-Abfrage zusammenstellen */

    public function setFilter($name, $field, $equal, $value, $funk = "AND")
    {
        $this->selectfilter[$name][] = array('field' => $field, 'value' => $value, 'equal' => $equal, 'function' => $funk);
    }
    /* gibt ein komplettes Filterarray zurück */

    public function getFilter($fname)
    {
        if (array_key_exists($fname, $this->selectfilter)) {
            return $this->selectfilter[$fname];
        }

        return false;
    }

    /* Filter für DB-Abfrage löschen */

    public function delFilter($name)
    {
        if (array_key_exists($name, $this->selectfilter)) $this->selectfilter[$name] = array();
    }
    /**
     *
     * @parameter  $ :
     * @return :
     */
    protected function _get_list($cat = 0, $output = "", $dbfilter = "", $start = 0, $limit = 0)
    {
        $tempfilter = array();
        if (!$dbfilter == "") $tempfilter = $this->getFilter($dbfilter);
        return $this->_getNodelist ($cat, $tempfilter, $output, $start, $limit);
    }

    /**
     *
     * @parameter  $ :
     * @return :
     */
    public function content_get_tree($cat = 0, $output = "", $dbfilter = "", $start = 0, $limit = 0)
    {
        // $dbfilter[]=[]=array('field'=>$field,'value'=>$value,'value'=>$value,'equal'=>$equal,'function'=>$funk);
        if ($dbfilter != "") $dbfilter = $this->getFilter($dbfilter);
        return $this->_getNodelist ($cat, $dbfilter, $output, $start, $limit, " AND n.rightID>n.leftID+1 ");
    }

    /**
     * gibt die Anzahl der Kategorien zurück
     *
     * @parameter  $ :
     * @return :
     */
    public function contentcount ()
    {
        // return count($this->catarray);
        return $this->content_count;
    }

    /**
     * gibt den Inhalt des Datensatzes zurück
     *
     * @parameter  $ : $cit intval
     * @return :
     */
    public function getNodeFromID($cid)
    {
        return (array_key_exists($cid, $this->catarray)?$this->catarray[$cid]:$this->_getNode($cid));
    }

    /**
     * gibt den Inhalt des Datensatzes zurück
     *
     * @parameter  $ :mid intval
     * @return :
     */
    public function getNodeFromMID($mid)
    {
        return $this->_getNode_mid($mid);
    }

    /**
     * prüft ob die ID existiert und gibt Datensatz zurück
     *
     * @parameter  $ :
     * @return :
     */
    private function getNodesArray($filter)
    {
        $content = array();

        return $content;
    }


    /**
     * Gibt den Pfad des Node zurück
     *
     * @parameter  $ : id intval, withroot boolean
     * @return :    array
     */
    public function getBreadcrump($id, $withroot = false)
    {
        if ($id < $this->getRootID()) $id = $this->getRootID();
        $getroot = ($withroot)?"":" and p.leftID>1 ";

        $result = sql_query("SELECT p.id, p.title FROM " . $this->config['dbtable'] . " as n," . $this->config['dbtable'] . " as p
                                WHERE p.module_name='" . $this->config['modulname'] . "'
                                      and n.module_name='" . $this->config['modulname'] . "'
                                      and n.id=$id
                                      and n.leftID BETWEEN p.leftID AND p.rightID
                                      " . $getroot . "
                                ORDER BY p.leftID asc
                                ");
        $output = array();
        while ($a = sql_fetch_assoc($result)) {
            // $output[$a['id']]=$a['title'];
            $output[] = $a;
        }
        unset ($result);
        return $output;
    }

    /**
     * Gibt den Pfad des Node zurück
     *
     * @parameter  $ : id intval, withroot boolean
     * @return :    array
     */
    public function getBreadcrumpAll($id, $withroot = false)
    {
        if ($id < $this->getRootID()) $id = $this->getRootID();
        $getroot = ($withroot)?"":" and p.leftID>1 ";

        $result = sql_query("SELECT p.* FROM " . $this->config['dbtable'] . " as n," . $this->config['dbtable'] . " as p
                                WHERE p.module_name='" . $this->config['modulname'] . "'
                                      and n.module_name='" . $this->config['modulname'] . "'
                                      and n.id=$id
                                      and n.leftID BETWEEN p.leftID AND p.rightID
                                      " . $getroot . "
                                ORDER BY p.leftID asc
                                ");
        $output = array();
        while ($a = sql_fetch_assoc($result)) {
            // $output[$a['id']]=$a['title'];
            $output[] = $a;
        }
        unset ($result);
        return $output;
    }

    /**
     * -- phpMyAdmin SQL Dump
     * -- version 2.6.4-pl3
     * -- http://www.phpmyadmin.net
     * --
     * -- Host: db1851.1und1.de
     * -- Erstellungszeit: 01. Oktober 2012 um 12:26
     * -- Server Version: 5.0.95
     * -- PHP-Version: 5.3.3-7+squeeze14
     * --
     * -- Datenbank: `db280553141`
     * --
     *
     * -- --------------------------------------------------------
     *
     * --
     * -- Tabellenstruktur für Tabelle `mx112rc1_content`
     * --
     *
     * //DROP TABLE IF EXISTS `mx112rc1_content`;
     */

    private function _createDBTables()
    {
        sql_system_query("CREATE TABLE IF NOT EXISTS " . $this->config['dbtable'] . " (
              `id` int(11) NOT NULL auto_increment,
              `leftID` int(12) NOT NULL default '0',
              `rightID` int(12) NOT NULL default '0',
              `mid` int(11) NOT NULL default '0',
              `parent_id` int(11) NOT NULL default '0',
              `publish` int(11) NOT NULL default '0',
              `access` int(11) NOT NULL default '0',
              `group_access` text,
              `module_name` text NOT NULL,
              `position` int(11) NOT NULL default '0',
              `owner_id` int(11) NOT NULL default '0',
              `owner_name` text,
              `edit_uid` int(11) NOT NULL default '0',
              `edit_uname` text,
              `version` int(11) NOT NULL default '0',
              `date_created` int(11) NOT NULL default '0',
              `date_start` int(11) NOT NULL default '0',
              `date_end` int(11) NOT NULL default '0',
              `date_edit` int(11) NOT NULL default '0',
              `title` text NOT NULL,
              `title_tag` text,
              `alias` text,
              `info` text,
              `text1` longtext,
              `text2` longtext,
              `text3` longtext,
              `keywords` text,
              `attachment` text,
              `config` longtext,
              `type` text,
              `language` text,
              `views` int(11) NOT NULL default '0',
              `votes` int(11) NOT NULL default '0',
              `rating` double NOT NULL default '0',
              `link` text,
              `import` text,
              `hash` text,
              `status` int(11) NOT NULL default '0',
              PRIMARY KEY  (`id`),
              KEY `parent_id` (`parent_id`,`publish`,`access`,`owner_id`),
              KEY `nested` (`leftID`,`rightID`),
              KEY `status` (`status`),
              FULLTEXT KEY `text` (`title`,`text1`,`text2`,`text3`,`keywords`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8  COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
        /*
        alternativ UNIQUE KEY `alias` (`alias`),
   */
        sql_query("CREATE TABLE IF NOT EXISTS " . $this->config['dbtable'] . "_log (
        `logid` int(11) NOT NULL auto_increment,
        `id` int(12) NOT NULL default '0',
        `action` text ,
        `title` text ,
        `date_action` int(11) NOT NULL default '0',
        `text_action` longtext,
        `module_name` text NOT NULL,
        `edit_uid` int(11) NOT NULL default '0',
        `edit_uname` text,
        PRIMARY KEY  (`logid`)

      ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
    }
}

?>