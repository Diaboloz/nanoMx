<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * $Author: PragmaMx $
 * $Revision: 6 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (mer. 08 juil. 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');
$modname = basename(dirname(dirname(__FILE__)));
if (!include(dirname(dirname(__FILE__)) . DS . 'settings.php')) {
    return;
}

if (!isset($prefix) || !isset($tablepre) || !isset($table_forums) || !isset($topicperpage)) {
    return;
}

unset($sqlqry);

if (!isset($tables[$table_banned])) {
    $sqlqry[] = "
CREATE TABLE $table_banned (
  ip1 smallint(3) NOT NULL default 0,
  ip2 smallint(3) NOT NULL default 0,
  ip3 smallint(3) NOT NULL default 0,
  ip4 smallint(3) NOT NULL default 0,
  dateline bigint(30) NOT NULL default 0,
  id smallint(6) NOT NULL default 0,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;";
}

if (!isset($tables[$table_forums])) {
    $sqlqry[] = "
CREATE TABLE $table_forums (
  type varchar(15) NOT NULL default '',
  fid int(7) NOT NULL auto_increment,
  name varchar(50) NOT NULL default '',
  status varchar(15) NOT NULL default '',
  lastpost varchar(40) NOT NULL default '',
  moderator varchar(100) NOT NULL default '',
  displayorder smallint(6) NOT NULL default 0,
  private varchar(30) default NULL,
  description text,
  allowhtml char(3) NOT NULL default '',
  allowsmilies char(3) NOT NULL default '',
  allowbbcode char(3) NOT NULL default '',
  guestposting char(3) NOT NULL default '',
  userlist text NOT NULL,
  theme varchar(30) NOT NULL default '',
  posts bigint(20) NOT NULL default 0,
  threads bigint(20) NOT NULL default 0,
  fup int(7) NOT NULL default 0,
  postperm char(3) NOT NULL default '',
  allowimgcode char(3) NOT NULL default '',
  totaltime bigint(30) NOT NULL default 0,
  PRIMARY KEY  (fid),
  KEY lastpost (lastpost),
  KEY name (name),
  KEY posts (posts),
  KEY theme (theme)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;";
}

if (!isset($tables[$table_links])) {
    $sqlqry[] = "
CREATE TABLE $table_links (
  lid bigint(11) NOT NULL auto_increment,
  type varchar(6) NOT NULL default '',
  fromid bigint(10) NOT NULL default 0,
  toid int(7) NOT NULL default 0,
  status char(3) NOT NULL default '',
  lastpost varchar(40) NOT NULL default '',
  UNIQUE KEY lid (lid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;";
}

if (!isset($tables[$table_members])) {
    $sqlqry[] = "
CREATE TABLE $table_members (
  uid int(7) NOT NULL auto_increment,
  username varchar(25) NOT NULL default '',
  postnum int(7) NOT NULL default 0,
  status varchar(35) NOT NULL default '',
  timeoffset int(5) NOT NULL default 0,
  customstatus varchar(100) NOT NULL default '',
  theme varchar(30) NOT NULL default '',
  langfile varchar(40) NOT NULL default '',
  tpp tinyint(3) NOT NULL default 0,
  ppp tinyint(3) NOT NULL default 0,
  newsletter char(3) NOT NULL default '',
  timeformat int(5) NOT NULL default 0,
  dateformat varchar(10) NOT NULL default '',
  lastvisit bigint(10) NOT NULL default 0,
  lastvisitstore bigint(10) NOT NULL default 0,
  lastvisitdate bigint(10) NOT NULL default 0,
  trackingfid int(7) NOT NULL default 0,
  trackingtime bigint(10) NOT NULL default 0,
  totaltime bigint(20) NOT NULL default 0,
  u2u char(3) NOT NULL default '',
  notifyme char(3) NOT NULL default '',
  notifythread char(3) NOT NULL default 0,
  notifypost char(3) NOT NULL default '',
  notifyedit char(3) NOT NULL default '',
  notifydelete char(3) NOT NULL default '',
  keeplastvisit tinyint(3) NOT NULL default '1',
  PRIMARY KEY  (uid),
  KEY username (username),
  KEY postnum (postnum)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;";
} else {
    $tf = setupGetTableFields($table_members);
    if (isset($tf['password'])) {
        $sqlqry[] = "ALTER TABLE `$table_members` DROP `password` ";
    }
    if (isset($tf['avatar'])) {
        $sqlqry[] = "ALTER TABLE `$table_members` DROP `avatar` ";
    }
    if (isset($tf['notifypm'])) {
        $sqlqry[] = "ALTER TABLE `$table_members` DROP `notifypm` ";
    }
    if (isset($tf['pmuserid'])) {
        $sqlqry[] = "ALTER TABLE `$table_members` DROP `pmuserid` ";
    }
    if (isset($tf['regdate'])) {
        $sqlqry[] = "ALTER TABLE `$table_members` DROP `regdate` ";
    }
    if (isset($tf['email'])) {
        $sqlqry[] = "ALTER TABLE `$table_members` DROP `email` ";
    }
    if (isset($tf['site'])) {
        $sqlqry[] = "ALTER TABLE `$table_members` DROP `site` ";
    }
    if (isset($tf['sig'])) {
        $sqlqry[] = "ALTER TABLE `$table_members` DROP `sig` ";
    }
    if (isset($tf['showemail'])) {
        $sqlqry[] = "ALTER TABLE `$table_members` DROP `showemail` ";
    }
    if (isset($tf['advanceduinfo'])) {
        $sqlqry[] = "ALTER TABLE `$table_members` DROP `advanceduinfo` ";
    }
}

if (!isset($tables[$table_posts])) {
    $sqlqry[] = "
CREATE TABLE $table_posts (
  fid int(7) NOT NULL default 0,
  tid bigint(10) NOT NULL default 0,
  pid bigint(10) NOT NULL auto_increment,
  author varchar(40) NOT NULL default '',
  message text NOT NULL,
  dateline int(10) NOT NULL default 0,
  icon varchar(50) default NULL,
  usesig char(3) NOT NULL default '',
  useip varchar(40) NOT NULL default '',
  bbcodeoff char(3) NOT NULL default '',
  smileyoff char(3) NOT NULL default '',
  emailnotify char(3) NOT NULL default '',
  PRIMARY KEY (pid),
  KEY dateline(dateline),
  KEY tid(tid),
  KEY fid(fid),
  KEY author(author)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;";
}

if (!isset($tables[$table_ranks])) {
    $sqlqry[] = "
CREATE TABLE $table_ranks (
  title varchar(40) NOT NULL default '',
  posts smallint(6) NOT NULL default 0,
  id smallint(6) NOT NULL auto_increment,
  stars smallint(6) NOT NULL default 0,
  PRIMARY KEY (id),
  KEY stars(stars),
  KEY posts(posts)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;";
} else {
    // allowavatars char(3) NOT NULL default '',
    // avatarrank varchar(90) default NULL,
    $tf = setupGetTableFields($table_ranks);
    if (isset($tf['allowavatars'])) {
        $sqlqry[] = "ALTER TABLE `$table_ranks` DROP `allowavatars` ";
    }
    if (isset($tf['avatarrank'])) {
        $sqlqry[] = "ALTER TABLE `$table_ranks` DROP `avatarrank` ";
    }
}

if (!isset($tables[$table_smilies])) {
    $sqlqry[] = "
CREATE TABLE $table_smilies (
  type varchar(15) NOT NULL default '',
  code varchar(40) NOT NULL default '',
  url varchar(40) NOT NULL default '',
  id smallint(6) NOT NULL auto_increment,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;";
}

if (!isset($tables[$table_themes])) {
    $sqlqry[] = "
CREATE TABLE $table_themes (
  name varchar(30) NOT NULL default '',
  bgcolor varchar(15) NOT NULL default '',
  altbg1 varchar(15) NOT NULL default '',
  altbg2 varchar(15) NOT NULL default '',
  link varchar(15) NOT NULL default '',
  bordercolor varchar(15) NOT NULL default '',
  header varchar(15) NOT NULL default '',
  headertext varchar(15) NOT NULL default '',
  top varchar(15) NOT NULL default '',
  catcolor varchar(15) NOT NULL default '',
  tabletext varchar(15) NOT NULL default '',
  text varchar(15) NOT NULL default '',
  borderwidth varchar(15) NOT NULL default '',
  tablewidth varchar(15) NOT NULL default '',
  tablespace varchar(15) NOT NULL default '',
  font varchar(40) NOT NULL default '',
  fontsize varchar(40) NOT NULL default '',
  altfont varchar(40) NOT NULL default '',
  altfontsize varchar(40) NOT NULL default '',
  replyimg varchar(50) default NULL,
  newtopicimg varchar(50) default NULL,
  boardimg varchar(50) default NULL,
  postscol varchar(5) NOT NULL default '',
  color1 varchar(15) NOT NULL default 'red',
  color2 varchar(15) NOT NULL default 'blue',
  PRIMARY KEY (name)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;";
}

if (!isset($tables[$table_threads])) {
    $sqlqry[] = "
CREATE TABLE $table_threads (
  tid bigint(10) NOT NULL auto_increment,
  fid int(7) NOT NULL default 0,
  subject varchar(100) NOT NULL default '',
  lastpost varchar(40) NOT NULL default '',
  views bigint(20) NOT NULL default 0,
  replies bigint(20) NOT NULL default 0,
  author varchar(40) NOT NULL default '',
  message text NOT NULL,
  dateline bigint(10) NOT NULL default 0,
  icon varchar(50) default NULL,
  usesig char(3) NOT NULL default '',
  closed char(3) NOT NULL default '',
  topped tinyint(3) NOT NULL default 0,
  useip varchar(40) NOT NULL default '',
  bbcodeoff char(3) NOT NULL default '',
  smileyoff char(3) NOT NULL default '',
  emailnotify char(3) NOT NULL default '',
  PRIMARY KEY (tid),
  KEY lastpost(lastpost),
  KEY dateline(dateline),
  KEY fid(fid),
  KEY subject(subject),
  KEY views(views),
  KEY author(author)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;";
}

if (!isset($tables[$table_whosonline])) {
    $sqlqry[] = "
CREATE TABLE $table_whosonline (
  username varchar(40) NOT NULL default '',
  ip varchar(40) NOT NULL default '',
  `time` bigint(40) NOT NULL default 0,
  location varchar(250) NOT NULL default '',
  KEY username(username),
  KEY ip(ip),
  KEY time(time)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;";
}

if (!isset($tables[$table_words])) {
    $sqlqry[] = "
CREATE TABLE $table_words (
  find varchar(60) NOT NULL default '',
  replace1 varchar(60) NOT NULL default '',
  id smallint(6) NOT NULL auto_increment,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

if (class_exists('mxbInstall', false)) {
    $result = sql_query("SELECT COUNT(name) FROM `$table_themes`");
    if ($result) {
        list($idcount) = sql_fetch_row($result);
        if (empty($idcount)) {
            $themes = mxbInstall::get_themes();
            foreach ($themes as $key => $value) {
                $sqlqry[] = "INSERT IGNORE INTO `$table_themes` VALUES ( '" . implode("','", array_values($value)) . "')";
            }
             $sqlqry[] = "INSERT IGNORE INTO `$table_themes` VALUES ( 'standard', '#ffffff', '#dededf', '#eeeeee', '#333399', '#9999ff', '#9999ff', '#ffffff', '#eeeeee', '#dcdcde', '#000000', '#000000', '1', '97%', '6', 'Verdana', '12px', 'sans-serif', '10px', 'grau', '', '', '', 'red', 'blue')";
        }
    }
}

$result = sql_query("SELECT COUNT(id) FROM `$table_smilies`");
if ($result) {
    list($idcount) = sql_fetch_row($result);
    if (empty($idcount)) {
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':)', 'smilies/smile.gif', 1)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('picon', '', 'posticons/arrow.gif', 2)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':D', 'smilies/bigsmile.gif', 3)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('picon', '', 'posticons/attention.gif', 4)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':(', 'smilies/sad.gif', 5)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('picon', '', 'posticons/question.gif', 6)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ';(', 'smilies/cry.gif', 7)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('picon', '', 'posticons/sad.gif', 8)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ';)', 'smilies/wink.gif', 9)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('picon', '', 'posticons/thumbup.gif', 10)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':cool:', 'smilies/cool.gif', 11)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('picon', '', 'posticons/thumbdown.gif', 12)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':redhead:', 'smilies/mad3.gif', 13)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('picon', '', 'posticons/wink.gif', 14)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':P', 'smilies/tongue.gif', 15)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('picon', '', 'posticons/light.gif', 16)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':o', 'smilies/shocked.gif', 17)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':puzz:', 'smilies/puzzled.gif', 18)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':exclam:', 'smilies/exclamation_smile.gif', 19)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':mad:', 'smilies/mad.gif', 20)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':thumbup:', 'smilies/thumbup.gif', 21)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':thumbdown:', 'smilies/thumbdown.gif', 22)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':red:', 'smilies/rougi.gif', 23)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':yltype:', 'smilies/yltype.gif', 24)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':crash:', 'smilies/crash.gif', 25)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':knockout:', 'smilies/knockout.gif', 26)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':mad2:', 'smilies/mad2.gif', 27)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':pray:', 'smilies/pray.gif', 28)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':hallucine:', 'smilies/hallucine.gif', 29)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':gunman:', 'smilies/gun.gif', 30)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':partyon:', 'smilies/partyon.gif', 31)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':idea:', 'smilies/idea.gif', 32)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':hello:', 'smilies/hello.gif', 33)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':devil:', 'smilies/devil.gif', 34)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':angle:', 'smilies/angel.gif', 35)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':bitte:', 'smilies/bitte.gif', 36)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':ka:', 'smilies/ka.gif', 37)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':jop:', 'smilies/jop.gif', 38)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':egypt:', 'smilies/egypt.gif', 39)";
        $sqlqry[] = "INSERT IGNORE INTO `$table_smilies` VALUES ('smiley', ':klapp:', 'smilies/klapp.gif', 40)";
    }
}

$result = sql_query("SELECT COUNT(id) FROM `$table_words`");
if ($result) {
    list($idcount) = sql_fetch_row($result);
    if (empty($idcount)) {
        $sqlqry[] = "INSERT IGNORE INTO $table_words VALUES ('damn', '****', '')";
        $sqlqry[] = "INSERT IGNORE INTO $table_words VALUES ('shit', '****', '')";
        $sqlqry[] = "INSERT IGNORE INTO $table_words VALUES ('fuck', '****', '')";
        $sqlqry[] = "INSERT IGNORE INTO $table_words VALUES ('bitch', '*****', '')";
        $sqlqry[] = "INSERT IGNORE INTO $table_words VALUES ('asshole', '***', '')";
    }
}

$result = sql_query("SELECT COUNT(id) FROM `$table_ranks`");
if ($result) {
    list($idcount) = sql_fetch_row($result);
    if (empty($idcount)) {
        $sqlqry[] = "INSERT IGNORE INTO $table_ranks VALUES ('Newbie'       , '1'   , NULL, '1')";
        $sqlqry[] = "INSERT IGNORE INTO $table_ranks VALUES ('Junior Member', '2'   , NULL, '2')";
        $sqlqry[] = "INSERT IGNORE INTO $table_ranks VALUES ('Member'       , '100' , NULL, '3')";
        $sqlqry[] = "INSERT IGNORE INTO $table_ranks VALUES ('Senior Member', '500' , NULL, '4')";
        $sqlqry[] = "INSERT IGNORE INTO $table_ranks VALUES ('Posting Freak', '1000', NULL, '5')";
    }
}

$result = sql_query("SELECT COUNT(fid) FROM `$table_forums`");
if ($result) {
    list($idcount) = sql_fetch_row($result);
    if (empty($idcount)) {
        $sqlqry[] = "INSERT IGNORE INTO $table_forums (type, fid, name, status, lastpost, moderator, displayorder, private, description, allowhtml, allowsmilies, allowbbcode, guestposting, userlist, theme, posts, threads, fup, postperm, allowimgcode, totaltime) VALUES ('group', 1, 'First Category', 'on', '', '', 0, '', '', '', '', '', '', '', '', 0, 0, 0, '1|1', '', 0);";
        $sqlqry[] = "INSERT IGNORE INTO $table_forums (type, fid, name, status, lastpost, moderator, displayorder, private, description, allowhtml, allowsmilies, allowbbcode, guestposting, userlist, theme, posts, threads, fup, postperm, allowimgcode, totaltime) VALUES ('forum', 2, 'First Forum', 'on', '', '', 0, '', '', 'no', 'yes', 'yes', 'yes', '', '', 0, 0, 1, '1|1', 'yes', 0);";
        $sqlqry[] = "INSERT IGNORE INTO $table_forums (type, fid, name, status, lastpost, moderator, displayorder, private, description, allowhtml, allowsmilies, allowbbcode, guestposting, userlist, theme, posts, threads, fup, postperm, allowimgcode, totaltime) VALUES ('sub', 3, 'First Sub-Forum', 'on', '', '', 0, '', '', 'no', 'yes', 'yes', 'yes', '', '', 0, 0, 2, '1|1', 'yes', 0);";
    }
}

/* veraltete Blöcke umschreiben */
$result = sql_query("SELECT `blockfile` FROM ${prefix}_blocks WHERE `blockfile`='block-eBoard_Center.php'");
if (sql_num_rows($result)) {
    $sqlqry[] = "UPDATE `${prefix}_blocks` SET `blockfile`='block-mxBoard_Center.php', `content`='' WHERE `blockfile`='block-eBoard_Center.php'";
}
// indexe aktualisieren
// $indexes = setupGetTableIndexes("${prefix}_downloads_categories");
// if (isset($indexes['cid'])) $sqlqry[] = "ALTER TABLE `${prefix}_downloads_categories` DROP INDEX `cid` ";
if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>