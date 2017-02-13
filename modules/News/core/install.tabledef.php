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
$modname = basename(dirname(dirname(__FILE__)));

/**
 * ////////////////////////////////////////////
 * ////// zuerst die topics...  ///////////////
 * ////////////////////////////////////////////
 */
unset($sqlqry);
// Topics
if (!isset($tables["${prefix}_topics"])) {
    $sqlqry[] = "
CREATE TABLE  ${prefix}_topics  (
   topicid  int(3) NOT NULL auto_increment,
   topicname  varchar(20) default NULL,
   topicimage  varchar(100) default NULL,
   topictext  varchar(40) default NULL,
   counter  int(11) NOT NULL default '0',
  PRIMARY KEY  ( topicid ),
  KEY  topictext  ( topictext (30))
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;";
} else {
    // Felder aktualisieren
    $tf = setupGetTableFields("${prefix}_topics");
    if ($tf['topicimage']['Type'] != 'varchar(100)') {
        $sqlqry[] = "ALTER TABLE  ${prefix}_topics  CHANGE  topicimage   topicimage  varchar(100) default NULL ";
    }
    // Änderungen von evolution zurücknehmen
    if (!isset($tf['topicname'])) {
        $sqlqry[] = "ALTER TABLE  ${prefix}_topics  ADD  topicname  VARCHAR( 20 ) NULL DEFAULT NULL AFTER  topicid ";
    }
    if (!isset($tf['counter'])) {
        $sqlqry[] = "ALTER TABLE  ${prefix}_topics  ADD  counter  INT( 11 ) NOT NULL DEFAULT '0'";
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}
// Daten einfuegen
$numrows = sql_num_rows(sql_query("SELECT  topicid  FROM  ${prefix}_topics  LIMIT 1"));
if (!$numrows) {
    $sqlqry[] = "INSERT INTO ${prefix}_topics (topicid, topicname, topicimage, topictext, counter) VALUES (1, 'News', 'news.gif', 'aktuelle News', 0);";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

$indexes = setupGetTableIndexes("${prefix}_topics");
if (!isset($indexes['topictext'])) $sqlqry[] = "ALTER TABLE  ${prefix}_topics  ADD INDEX  topictext  (  topictext  ( 30 ) ) ";

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}
// aufraeumen
unset($numrows);

/**
 * /////////////////////////////////////////////////////////
 * ////// AB HIER DAS EIGENTLICHE NEWSMODUL  ///////////////
 * /////////////////////////////////////////////////////////
 */
// News-Modul - queue
if (isset($tables["${prefix}_queue"])) {
    // Felder aktualisieren
    $tf = setupGetTableFields("${prefix}_queue");
    switch (true) {
        case $tf['storyext']['Default'] !== null:
        case $tf['storyext']['Type'] != 'longtext':
        case $tf['uname']['Default'] === null:
        case $tf['uname']['Type'] != 'varchar(25)':
        case $tf['subject']['Default'] === null:
        case $tf['subject']['Type'] != 'varchar(80)':
        case $tf['uid']['Type'] != 'int(11)':
        case $tf['topic']['Type'] != 'int(11)':
        case $tf['topic']['Default'] != '1':
        case $tf['timestamp']['Type'] != 'timestamp':
        case $tf['timestamp']['Default'] != 'CURRENT_TIMESTAMP':
            /* vorher gültige Daten erzeugen, sonst Fehler:
              * Incorrect datetime value: '0000-00-00 00:00:00' for column '***' at....*/
            $sqlqry[] = "UPDATE  ${prefix}_queue  SET  timestamp  = NOW( ) WHERE  timestamp  LIKE '0000-00-00%'";
            $sqlqry[] = "ALTER TABLE  ${prefix}_queue 
                CHANGE  storyext   storyext  longtext NULL,
                CHANGE  uname   uname  varchar(25) NOT NULL default '',
                CHANGE  subject   subject  varchar(80) NOT NULL default '',
                CHANGE  uid   uid  INT( 11 ) NOT NULL DEFAULT '0',
                CHANGE  topic   topic  INT( 11 ) NOT NULL DEFAULT '1',
                CHANGE  timestamp   timestamp  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
                ";
    }
} else {
    $sqlqry[] = "
CREATE TABLE  ${prefix}_queue  (
   qid  smallint(5) unsigned NOT NULL auto_increment,
   uid  int(11) NOT NULL default '0',
   uname  varchar(25) NOT NULL default '',
   subject  varchar(80) NOT NULL default '',
   story  text NULL,
   storyext  longtext NULL,
   timestamp  timestamp NOT NULL default CURRENT_TIMESTAMP,
   topic  int(11) NOT NULL default '1',
   alanguage  varchar(30) NOT NULL default '',
  PRIMARY KEY  ( qid )
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;";
}
// News-Modul - stories
if (isset($tables["${prefix}_stories"])) {
    $tf = setupGetTableFields("${prefix}_stories");
    // Felder ergaenzen
    if (!isset($tf['acomm'])) $sqlqry[] = "ALTER TABLE  ${prefix}_stories  ADD  acomm  int(1) NOT NULL default '0';";
    if (!isset($tf['score'])) $sqlqry[] = "ALTER TABLE  ${prefix}_stories  ADD  score  int(10) NOT NULL default '0';";
    if (!isset($tf['ratings'])) $sqlqry[] = "ALTER TABLE  ${prefix}_stories  ADD  ratings  int(10) NOT NULL default '0';";
    // Felder aktualisieren
    switch (true) {
        case $tf['hometext']['Default'] !== null:
        case $tf['hometext']['Type'] != 'text':
        case $tf['bodytext']['Default'] !== null:
        case $tf['bodytext']['Type'] != 'longtext':
        case $tf['notes']['Default'] !== null:
        case $tf['aid']['Default'] === null:
        case $tf['aid']['Type'] != 'varchar(25)':
        case $tf['title']['Default'] === null:
        case $tf['title']['Type'] != 'varchar(80)':
        case $tf['informant']['Default'] === null:
        case $tf['informant']['Type'] != 'varchar(25)':
        case $tf['topic']['Type'] != 'int(11)':
        case $tf['counter']['Type'] != 'int(11)':
        case $tf['time']['Null'] != 'NO':
        case $tf['time']['Type'] != 'timestamp':
        case $tf['time']['Default'] != 'CURRENT_TIMESTAMP':
            /* vorher gültige Daten erzeugen, sonst Fehler:
              * Incorrect datetime value: '0000-00-00 00:00:00' for column '***' at....*/
            $sqlqry[] = "UPDATE  ${prefix}_stories  SET  time  = NOW( ) WHERE  time  LIKE '0000-00-00%'";
            $sqlqry[] = "ALTER TABLE  ${prefix}_stories 
                CHANGE  hometext   hometext  text NULL,
                CHANGE  bodytext   bodytext  longtext NULL,
                CHANGE  notes   notes  text NULL,
                CHANGE  aid   aid  varchar(25) NOT NULL default '',
                CHANGE  title   title  varchar(80) NOT NULL default '',
                CHANGE  informant   informant  varchar(25) NOT NULL default '',
                CHANGE  topic   topic  INT( 11 ) NOT NULL DEFAULT '1',
                CHANGE  counter   counter  INT( 11 ) NOT NULL DEFAULT '0',
                CHANGE  time   time  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
                ";
    }
    // nuke/cp rueckgaengig
    if (isset($tf['haspoll'])) $sqlqry[] = "ALTER TABLE  ${prefix}_stories  DROP  haspoll ;"; // bis pragmaMx 1.12
    if (isset($tf['pollID'])) $sqlqry[] = "ALTER TABLE  ${prefix}_stories  DROP  pollID ;"; // bis pragmaMx 1.12
    if (isset($tf['rating_ip'])) $sqlqry[] = "ALTER TABLE  ${prefix}_stories  DROP  rating_ip  ;"; // ab nuke 7.5
    if (isset($tf['associated'])) $sqlqry[] = "ALTER TABLE  ${prefix}_stories  DROP  associated  ;"; // ab nuke 7.5
    if (isset($tf['has_media'])) $sqlqry[] = "ALTER TABLE  ${prefix}_stories  DROP  has_media  ;"; // cp
    if (isset($tf['display_order'])) $sqlqry[] = "ALTER TABLE  ${prefix}_stories  DROP  display_order  ;"; // cp
} else {
    $sqlqry[] = "
CREATE TABLE  ${prefix}_stories  (
   sid  int(11) NOT NULL auto_increment,
   catid  int(11) NOT NULL default '0',
   aid  varchar(25) NOT NULL default '',
   title  varchar(80) NOT NULL default '',
   time  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
   hometext  text NULL,
   bodytext  longtext NULL,
   comments  int(11) default '0',
   counter  int(11) NOT NULL default '0',
   topic  int(11) NOT NULL default '1',
   informant  varchar(25) NOT NULL default '',
   notes  text NULL,
   ihome  int(1) NOT NULL default '0',
   alanguage  varchar(30) NOT NULL default '',
   acomm  int(1) NOT NULL default '0',
   score  int(10) NOT NULL default '0',
   ratings  int(10) NOT NULL default '0',
  PRIMARY KEY  ( sid ),
  KEY  catid  ( catid ),
  KEY  topic  ( topic ),
  KEY  time  ( time ),
  KEY  alanguage  ( alanguage ),
  KEY  ihome  ( ihome )
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}
// News-Modul, autonews an normale stories tabelle anfuegen
if (isset($tables["${prefix}_autonews"])) {
    $sqlqry[] = "INSERT INTO  ${prefix}_stories (  catid ,  aid ,  title ,  time ,  hometext ,  bodytext ,  topic ,  informant ,  notes ,  ihome ,  alanguage ,  acomm  )
	SELECT  catid ,  aid ,  title ,  time ,  hometext ,  bodytext ,  topic ,  informant ,  notes ,  ihome ,  alanguage ,  acomm 
	FROM  ${prefix}_autonews 
	ORDER BY time DESC;";
    $sqlqry[] = "RENAME TABLE  ${prefix}_autonews  TO  " . RENAME_PREFIX . "${prefix}_autonews ;";
}
// News-Kommentare
$newfound = 0;
if (isset($tables["${prefix}_comments"])) {
    // Anzahl der Kommentare in der stories Tabelle aktualisieren
    $qry = "SELECT s.sid, s.comments, Count(c.tid) AS anz
	FROM ${prefix}_stories AS s LEFT JOIN ${prefix}_comments AS c ON s.sid = c.sid
	GROUP BY s.sid, s.comments
	HAVING (((Count(c.tid))<>s.comments))";
    $result = sql_query($qry);
    while (list($sid, $comments, $anz) = sql_fetch_row($result)) {
        $anz = (int)$anz;
        $sid = (int)$sid;
        $sqlqry[] = "UPDATE ${prefix}_stories set comments=$anz where sid=$sid;";
    }
    // aenderungen an der Kommentar Tabelle
    $tf = setupGetTableFields("${prefix}_comments");
    $newfields = array("modul_name", "reply_date", "uid");
    foreach ($newfields as $newfield) {
        if (isset($tf[$newfield])) $newfound++;
    }
    if ($newfound != count($newfields)) {
        $oldtable = RENAME_PREFIX . "${prefix}_comments";
        $sqlqry[] = "RENAME TABLE  ${prefix}_comments  TO  $oldtable ;";
        $sqlqry[] = "CREATE TABLE  ${prefix}_comments  (
           tid  int(11) NOT NULL auto_increment,
           pid  int(11) NOT NULL default '0',
           sid  int(11) NOT NULL default '0',
           modul_name  varchar(30) NOT NULL default '',
           reply_date  int(11) NOT NULL default '0',
           name  varchar(60) NOT NULL default '',
           uid  int(11) NOT NULL default '0',
           host_name  varchar(60) default NULL,
           subject  varchar(85) NOT NULL default '',
           comment  text NULL,
          PRIMARY KEY  ( tid ),
          KEY  modul_name  ( modul_name ),
          KEY  sid  ( sid ),
          KEY  reply_date  ( reply_date ),
          KEY  uid  ( uid ),
          KEY  host_name  ( host_name )
    		) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci ;";
        $sqlqry[] = "INSERT INTO ${prefix}_comments ( tid, pid, sid, modul_name, reply_date, name, uid, host_name, subject, comment )
      		SELECT old.tid, old.pid, old.sid, 'News' AS modname, UNIX_TIMESTAMP( date ) AS reply_date, old.name, u.uid, old.host_name, old.subject, old.comment
      		FROM $oldtable AS old LEFT JOIN {$user_prefix}_users AS u ON old.name = u.uname;";
        // Ende	aenderungen an der Kommentar Tabelle
    }
} else {
    $sqlqry[] = "
CREATE TABLE  ${prefix}_comments  (
   tid  int(11) NOT NULL auto_increment,
   pid  int(11) NOT NULL default '0',
   sid  int(11) NOT NULL default '0',
   modul_name  varchar(30) NOT NULL default '',
   reply_date  int(11) NOT NULL default '0',
   name  varchar(60) NOT NULL default '',
   uid  int(11) NOT NULL default '0',
   host_name  varchar(60) default NULL,
   subject  varchar(85) NOT NULL default '',
   comment  text NULL,
  PRIMARY KEY  ( tid ),
  KEY  modul_name  ( modul_name ),
  KEY  sid  ( sid ),
  KEY  reply_date  ( reply_date ),
  KEY  uid  ( uid ),
  KEY  host_name  ( host_name )
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}
// ende ab pragmaMx 0.1.6
// Indexe
if (isset($tables["${prefix}_stories"])) {
    // Indexe aktualisieren
    $indexes = setupGetTableIndexes("${prefix}_stories");
    if (!isset($indexes['catid'])) $sqlqry[] = "ALTER TABLE  ${prefix}_stories  ADD INDEX  catid  (  catid  );";
    if (!isset($indexes['topic'])) $sqlqry[] = "ALTER TABLE  ${prefix}_stories  ADD INDEX  topic  (  topic  );";
    if (!isset($indexes['time'])) $sqlqry[] = "ALTER TABLE  ${prefix}_stories  ADD INDEX  time  (  time  ) ;";
    if (!isset($indexes['alanguage'])) $sqlqry[] = "ALTER TABLE  ${prefix}_stories  ADD INDEX  alanguage  (  alanguage  ) ;";
    if (!isset($indexes['ihome'])) $sqlqry[] = "ALTER TABLE  ${prefix}_stories  ADD INDEX  ihome  (  ihome  ) ;";
    if (isset($indexes['catid_2'])) $sqlqry[] = "ALTER TABLE  ${prefix}_stories  DROP INDEX  catid_2  ";
    if (isset($indexes['topic_2'])) $sqlqry[] = "ALTER TABLE  ${prefix}_stories  DROP INDEX  topic_2  ";
    if (isset($indexes['pollID_2'])) $sqlqry[] = "ALTER TABLE  ${prefix}_stories  DROP INDEX  pollID_2  ";
    if (isset($indexes['pollID'])) $sqlqry[] = "ALTER TABLE  ${prefix}_stories  DROP INDEX  pollID  ";
}

if (isset($tables["${prefix}_comments"])) {
    $indexes = setupGetTableIndexes("${prefix}_comments");
    if (!isset($indexes['modul_name'])) $sqlqry[] = "ALTER TABLE  ${prefix}_comments  ADD INDEX  modul_name  (  modul_name  ) ";
    if (!isset($indexes['sid'])) $sqlqry[] = "ALTER TABLE  ${prefix}_comments  ADD INDEX  sid  (  sid  ) ";
    if (!isset($indexes['reply_date'])) $sqlqry[] = "ALTER TABLE  ${prefix}_comments  ADD INDEX  reply_date  (  reply_date  ) ";
    if (!isset($indexes['uid'])) $sqlqry[] = "ALTER TABLE  ${prefix}_comments  ADD INDEX  uid  (  uid  ) ";
    if (!isset($indexes['host_name'])) $sqlqry[] = "ALTER TABLE  ${prefix}_comments  ADD INDEX  host_name  (  host_name  ) ";
    // Felder aktualisieren
    $tf = setupGetTableFields("${prefix}_comments");
    if (isset($tf['date'])) $sqlqry[] = "ALTER TABLE  ${prefix}_comments  DROP  date  ";
}
if (isset($tables["${prefix}_comments"])) {
    $tf = setupGetTableFields("${prefix}_comments");
    if ($tf['comment']['Default'] !== null) {
        $sqlqry[] = "ALTER TABLE  ${prefix}_comments 
                CHANGE  comment   comment  text NULL";
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}
// News-Modul - related-links
if (!isset($tables["${prefix}_related"])) {
    $sqlqry[] = "
CREATE TABLE  ${prefix}_related  (
   rid  int(11) NOT NULL auto_increment,
   tid  int(11) NOT NULL default '0',
   name  varchar(30) NOT NULL default '',
   url  varchar(200) NOT NULL default '',
  PRIMARY KEY  ( rid ),
  KEY  tid  ( tid )
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;";
} else {
    $indexes = setupGetTableIndexes("${prefix}_related");
    if (!isset($indexes['tid'])) $sqlqry[] = "ALTER TABLE  ${prefix}_related  ADD INDEX  tid  (  tid  ) ";
}
// News-Modul - Kategorien
if (!isset($tables["${prefix}_stories_cat"])) {
    $sqlqry[] = "
CREATE TABLE  ${prefix}_stories_cat  (
   catid  int(11) NOT NULL auto_increment,
   title  varchar(40) NOT NULL default '',
   counter  int(11) NOT NULL default '0',
  PRIMARY KEY  ( catid )
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;";
} else {
    // Felder aktualisieren
    $tf = setupGetTableFields("${prefix}_stories_cat");
    if ($tf['title']['Default'] === null || $tf['title']['Type'] != 'varchar(40)') {
        $sqlqry[] = "ALTER TABLE  ${prefix}_stories_cat 
                CHANGE  title   title  varchar(40) NOT NULL default '' ";
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

$numrows = sql_num_rows(sql_query("SELECT  catid  FROM  ${prefix}_stories_cat  LIMIT 1;"));
if (!$numrows) {
    $sqlqry[] = "INSERT INTO  ${prefix}_stories_cat  VALUES (1, 'News', 0)";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>