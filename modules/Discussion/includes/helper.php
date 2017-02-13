<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: PragmaMx $
 * $Revision: 6 $
 * $Date: 2015-07-08 09:07:06 +0200 (mer. 08 juil. 2015) $
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

defined('mxMainFileLoaded') or die('access denied');
defined('MXB_INIT') or die('Not in mxBoard...');

/**
 * mxbHelper
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2010
 * @version $Id: helper.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class mxbHelper {
    /**
     * mxbHelper::get_defaults()
     *
     * @return
     */
    public static function get_defaults()
    {
        $defaults = Array(/* Standardwerte */
            'tablepre' => '',
            'bbname' => 'mxBoard ' . MXB_VERSION . ' (change this to your board name)',
            'avastatus' => 'on',
            'langfile' => '',
            'XFtheme' => 'default',
            'lastpostsubj' => 'off',
            'lastpostsubjchars' => '25',
            'linkforumstatus' => 'on',
            'linkthreadstatus' => 'on',
            'postperpage' => '25',
            'topicperpage' => '30',
            'memberperpage' => '45',
            'hottopic' => '20',
            'floodctrl' => '10',
            'bbstatus' => 'on',
            'bboffreason' => '',
            'whosonlinestatus' => 'on',
            'advancedonlinestatus' => 'on',
            'regviewonly' => 'off',
            'hideprivate' => 'on',
            'showsort' => 'on',
            'searchstatus' => 'on',
            'messotdstatus' => 'on',
            'dureemessotd' => '1',
            'faqstatus' => 'on',
            'memliststatus' => 'on',
            'memlistanonymousstatus' => 'off',
            'statspage' => 'on',
            'nbitemsinstats' => '10',
            'indexstats' => 'on',
            'affjumper' => 'on',
            'affjumperdynamic' => 'off',
            'piconstatus' => 'on',
            'smilieslinenumber' => '5',
            'smiliesrownumber' => '3',
            'colorsubject' => 'off',
            'allowu2u' => 'off',
            'reportpost' => 'off',
            'showtotaltime' => 'off',
            'adminstars' => 'maxusersp3',
            'adminemail' => $GLOBALS['adminmail'],
            'emailalltoadmin' => 'on',
            'mailonthread' => 'on',
            'mailonpost' => 'on',
            'mailonedit' => 'on',
            'mailondele' => 'on',
            'emailalltomoderator' => 'on',
            'moderatormailonthread' => 'on',
            'moderatormailonpost' => 'on',
            'moderatormailonedit' => 'on',
            'moderatormailondelete' => 'on',
            'textarearows' => '10',
            'textareacols' => '100',
            'sigbbcode' => 'on',
            'sightml' => 'off',
            'sigimgXxXauth' => 'off',
            'sigimgwidth' => '100',
            'sigimgheight' => '50',
            'timeformat' => '24',
            'dateformat' => 'dd/mm/yyyy',
            'globaltimestatus' => 'on',
            'globaltimeoffset' => '2',
            'eb_defhtml' => 'no',
            'eb_defsmilies' => 'yes',
            'eb_defbbcode' => 'yes',
            'eb_defanoposts' => 'no',
            'eb_defimgcode' => 'yes',
            'eb_defstaff' => '',
            'eb_defmain2sub' => 'yes',
            'eb_defmods' => '',
            'eb_defmessslv' => '1',
            'eb_defmembernews' => 'no',
            'eb_defmemberu2u' => 'no',
            'bbrules' => 'on',
            'bbrulestxt' => 'Here is the text for the bb-rules popup-window',
            'captcha' => 'on',
            'captchauser' => 'off',
            );
        return $defaults;
    }

    /**
     * mxbHelper::get_config_string()
     *
     * @param array $newconfig
     * @return
     */
    public static function get_config_string($newconfig = array())
    {
        global $prefix;

        $newconfig = array_merge(self::get_defaults(), (array)$newconfig);
        extract($newconfig);

        /* falls prefix am Anfang steht, diesen extrahieren */
        if (strpos($tablepre, $prefix . '_') === 0) {
            $tablepre = substr($tablepre, strlen($prefix) + 1);
            $generated_tablepre = "\$GLOBALS['prefix'] . '_$tablepre'";
        } else {
            $generated_tablepre = "'$tablepre'";
        }

        $content = "
<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 * write with: \$Id: helper.php 6 2015-07-08 07:07:06Z PragmaMx $
 */

defined('mxMainFileLoaded') or die('access denied');

// Database settings
\$tablepre = $generated_tablepre;

\$table_banned     = \$tablepre . 'banned';
\$table_forums     = \$tablepre . 'forums';
\$table_links      = \$tablepre . 'links';
\$table_members    = \$tablepre . 'members';
\$table_posts      = \$tablepre . 'posts';
\$table_ranks      = \$tablepre . 'ranks';
\$table_smilies    = \$tablepre . 'smilies';
\$table_themes     = \$tablepre . 'themes';
\$table_threads    = \$tablepre . 'threads';
\$table_whosonline = \$tablepre . 'whosonline';
\$table_words      = \$tablepre . 'words';

// general settings
\$bbname = '$bbname';
\$avastatus = '$avastatus';
\$langfile = '$langfile';
\$XFtheme = '$XFtheme';
\$lastpostsubj = '$lastpostsubj';
\$lastpostsubjchars = '$lastpostsubjchars';
\$linkforumstatus = '$linkforumstatus';
\$linkthreadstatus = '$linkthreadstatus';
\$postperpage = '$postperpage';
\$topicperpage = '$topicperpage';
\$memberperpage = '$memberperpage';
\$hottopic = '$hottopic';
\$floodctrl = '$floodctrl';
\$bbstatus = '$bbstatus';
\$bboffreason = '$bboffreason';
\$whosonlinestatus = '$whosonlinestatus';
\$advancedonlinestatus = '$advancedonlinestatus';
\$regviewonly = '$regviewonly';
\$hideprivate = '$hideprivate';
\$showsort = '$showsort';
\$searchstatus = '$searchstatus';
\$messotdstatus = '$messotdstatus';
\$dureemessotd = '$dureemessotd';
\$faqstatus = '$faqstatus';
\$memliststatus = '$memliststatus';
\$memlistanonymousstatus = '$memlistanonymousstatus';
\$statspage = '$statspage';
\$nbitemsinstats = '$nbitemsinstats';
\$indexstats = '$indexstats';
\$affjumper = '$affjumper';
\$affjumperdynamic = '$affjumperdynamic';
\$piconstatus = '$piconstatus';
\$smilieslinenumber = '$smilieslinenumber';
\$smiliesrownumber = '$smiliesrownumber';
\$colorsubject = '$colorsubject';
\$allowu2u = '$allowu2u';
\$reportpost = '$reportpost';
\$showtotaltime = '$showtotaltime';
\$adminstars = '$adminstars';

// Notify-settings
\$adminemail = '$adminemail';
\$emailalltoadmin = '$emailalltoadmin';
\$mailonthread = '$mailonthread';
\$mailonpost = '$mailonpost';
\$mailonedit = '$mailonedit';
\$mailondele = '$mailondele';
\$emailalltomoderator = '$emailalltomoderator';
\$moderatormailonthread = '$moderatormailonthread';
\$moderatormailonpost = '$moderatormailonpost';
\$moderatormailonedit = '$moderatormailonedit';
\$moderatormailondelete = '$moderatormailondelete';

// Text-settings
\$textarearows = '$textarearows';
\$textareacols = '$textareacols';
\$sigbbcode = '$sigbbcode';
\$sightml = '$sightml';
\$sigimgXxXauth = '$sigimgXxXauth';
\$sigimgwidth = '$sigimgwidth';
\$sigimgheight = '$sigimgheight';

// Time-settings
\$timeformat = '$timeformat';
\$dateformat = '$dateformat';
\$globaltimestatus = '$globaltimestatus';
\$globaltimeoffset = '$globaltimeoffset';

// Default values
\$eb_defhtml = '$eb_defhtml';
\$eb_defsmilies = '$eb_defsmilies';
\$eb_defbbcode = '$eb_defbbcode';
\$eb_defanoposts = '$eb_defanoposts';
\$eb_defimgcode = '$eb_defimgcode';
\$eb_defstaff = '$eb_defstaff';
\$eb_defmain2sub = '$eb_defmain2sub';
\$eb_defmods = '$eb_defmods';
\$eb_defmessslv = '$eb_defmessslv';
\$eb_defmembernews = '$eb_defmembernews';
\$eb_defmemberu2u = '$eb_defmemberu2u';

// Boardrules-settings
\$bbrules = '$bbrules';
\$bbrulestxt = '$bbrulestxt';

// Captcha
\$captcha = '$captcha';
\$captchauser = '$captchauser';

?>";
        return trim($content);
    }
}

?>
