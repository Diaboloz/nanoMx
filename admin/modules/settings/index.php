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
 * $Revision: 216 $
 * $Author: PragmaMx $
 * $Date: 2016-09-20 15:29:30 +0200 (Di, 20. Sep 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

/* Sprachdatei auswählen */
mxGetLangfile(__DIR__);

if (!mxGetAdminPref("radminsuper")) {
    mxErrorScreen("Access Denied");
    die();
}
// hier kann die Anzeige der Demomodus-Konfiguration aktiviert werden.
// Standardmaessig wird die nicht angezeigt, sondern der demomodus muss
// manuell in der config.php geaendert werden.
$show_demo_mode = false;



/**
 * Configure()
 *
 * @param integer $ok
 * @return
 */
function Configure()
{
    global $fieldset_names;
	$returnflag=false;
	
	$tb = load_class('AdminForm', "settings"); /* erstmal AdminForm laden */

    switch (pmxAdminForm::CheckButton()) {
        case "save":
            $returnflag = true;
        case "accept":
			ConfigSave($_POST);
            break;
        default:
            break;
    }
	
	/* Werte vorbereiten */
	
	$info2 = 'pragmaMx ' . PMX_VERSION . ' - ' . _SITECONFIG;

    /* falls config.php nicht schreibgeschuetzt, dieses tun... */
    if (is_writable(PMX_CONFIGFILE)) {
        pmxDebug::pause();
        mx_chmod(PMX_CONFIGFILE, PMX_CHMOD_LOCK);
        pmxDebug::restore();
    } 
	include (PMX_CONFIGFILE);

    extract(mxGetAdminSession());
	/* since PHP7 only prmitted MYSQLI */
	$xdbconnect=1;
    $vkpInactiveMins = (empty($vkpInactiveMins)) ? 10 : intval($vkpInactiveMins);
    $vkpSessLifetime = (!isset($vkpSessLifetime)) ? 30 : intval($vkpSessLifetime);
    $vkpIntranet = (empty($vkpIntranet)) ? 0 : intval($vkpIntranet);
	
	$mxSessionLoc = (empty($mxSessionLoc)) ? 0 : intval($mxSessionLoc);

    $mailhost = (empty($mailhost)) ? '' : $mailhost;
    $mailuname = (empty($mailuname)) ? '' : $mailuname;
    $mailpass = (empty($mailpass)) ? '' : $mailpass;
    $mailport = (empty($mailport)) ? "25" : (int)$mailport;
    $mailauth = (empty($mailauth)) ? "mail" : $mailauth;
    $popauth = (empty($popauth)) ? '' : $popauth;

    $mxSiteService = (empty($mxSiteService)) ? 0 : $mxSiteService;
    $mxSiteServiceText = (empty($mxSiteServiceText)) ? '' : $mxSiteServiceText;

    $mxUseThemecache = (empty($mxUseThemecache)) ? 0 : (int)$mxUseThemecache;

    $vkpTracking = (empty($vkpTracking)) ? 0 : $vkpTracking;
    $storyhome_cols = (empty($storyhome_cols)) ? 1 : (int)$storyhome_cols;
    $foot1 = (empty($foot1)) ? '' : htmlspecialchars($foot1);
    $foot2 = (empty($foot2)) ? '' : htmlspecialchars($foot2);
    $foot3 = (empty($foot3)) ? '' : htmlspecialchars($foot3);
    $foot4 = (empty($foot4)) ? '' : htmlspecialchars($foot4);
    $vkpSafeCookie1 = (empty($vkpSafeCookie1)) ? 0 : intval($vkpSafeCookie1);
    $vkpSafeCookie2 = (empty($vkpSafeCookie2)) ? 0 : intval($vkpSafeCookie2);
    $mxCookieInfo = (empty($mxCookieInfo)) ? 0 : intval($mxCookieInfo);
    $mxCookieLink = (empty($mxCookieLink)) ? "modules.php?name=legal" : $mxCookieLink;
    $mxCookiePos = (empty($mxCookiePos)) ? "top" : $mxCookiePos;
	
    $mxFTPon = (empty($mxFTPon)) ? 0 : intval($mxFTPon);
	$mxFTPhost= (empty($mxFTPhost)) ? "localhost" : $mxFTPhost;
	$mxFTPport= (empty($mxFTPport)) ? "21" : $mxFTPport;
	$mxFTPuser= (empty($mxFTPuser)) ? "" : $mxFTPuser;
	$mxFTPpass= (empty($mxFTPpass)) ? "" : $mxFTPpass;
	$mxFTPssl= (empty($mxFTPssl)) ? 0 : intval($mxFTPssl);
	$mxFTPdir= (empty($mxFTPdir)) ? "" : $mxFTPdir;

    $vkpSafeSqlinject = (empty($vkpSafeSqlinject)) ? ((isset($vkpSafeSqlinject)) ? $vkpSafeSqlinject : 1) : 1;

    $vkpsec_logging = (empty($vkpsec_logging)) ? 0 : intval($vkpsec_logging);
    $DOCTYPE = (empty($DOCTYPE)) ? 0 : intval($DOCTYPE);
    $doctype_array = mxDoctypeArray();

    $tidy_available = is_tidy_available();
    $TidyOutput = (empty($TidyOutput) || !$tidy_available) ? 0 : intval($TidyOutput);

    $uthemes = array();
    foreach ((array)glob(PMX_LAYOUT_DIR . DS . 'jquery/ui/*/jquery-ui.css', GLOB_NOSORT) as $filename) {
        $theme = basename(dirname($filename));
        $uthemes[$theme] =  $theme ;//$juitheme
    }
    
    // option Blöcke
    $defaultblock = (isset($vkpBlocksRight)) ? $vkpBlocksRight : 0;
    $blockoptions1 = array();
    for ($i = 0; $i <= 3; $i++) {
        if (defined('_BLOCKSHOW_' . $i)) {
            $blockoptions1[constant('_BLOCKSHOW_' . $i)] = $i ;
        }
    }
    // ende option Bloecke
    if (!isset($mxEntitieLevel)) {
        $mxEntitieLevel = 1;
    }
    if (!isset($mxUseThemecache)) {
        $mxUseThemecache = 1;
    }

    /**
     * JavaScript-Framework jQuery und fieldset events/styles einbinden
     */
    pmxHeader::add_jquery('ui/jquery.ui.datepicker.js',
        'ui/i18n/jquery.ui.datepicker-' . _DOC_LANGUAGE . '.js'
        );
    pmxHeader::add_script(PMX_JAVASCRIPT_PATH . 'fieldset.js.php');
    include (PMX_JAVASCRIPT_DIR . DS . 'fieldset.js.php');
    /* Lightbox für Logo-Vorschau */
    pmxHeader::add_lightbox();

    /* Filebrowser für Logo's initialisieren */
    $fb = load_class('Filebrowse');
    $fb->set_type('images');
    $fb->add_root('images', 'images');
    $fb->dialog();

    $tmp = mkfromstrptime($GLOBALS['startdate'], _DATEPICKER);
    if ($tmp) {
        /* wenn in config gültiges Datum angegeben, dieses verwenden */
        $startdate = strftime(_DATEPICKER, $tmp);
    }

	/* Form zusammen stellen */
	
    
    //$tb->__set('target_url', "admin.php?op=settings");
    $tb->__set("tb_text", "" . _ADMIN_LASTCHANGE . ' ' . mx_strftime(_DATESTRING . ' %H:%M:%S', filemtime(PMX_CONFIGFILE)));
    $tb->__set("tb_direction", 'right');
    $tb->__set("infobutton", false);
    $tb->__set("tb_pic_heigth", 25);
    $tb->__set("csstoolbar", "toolbar1");
    $tb->__set("cssform", "a305020");
    $tb->__set('buttontext', false);
    $tb->__set('homelink', true);
    $tb->__set('fieldhomebutton', true);
    $tb->addToolbar("accept");
    $tb->addToolbar("save");
    
	/* general settings */
    $tb->addFieldset("siteinfo", _GENSITEINFO, "", true);
	$tb->add("siteinfo","input","xsitename", mxEntityQuotes($sitename),_SITENAME,"",50);
	$tb->add("siteinfo","input","xslogan", mxEntityQuotes($slogan),_SITESLOGAN,"",50);
	$tb->add("siteinfo","date","xstartdate", mxEntityQuotes($startdate),_STARTDATE,"",20);
	$tb->add("siteinfo","input","xadminmail", mxEntityQuotes($adminmail),_ADMINEMAIL,"",50);
	$tb->add("siteinfo","input","xanonymous", mxEntityQuotes($anonymous),_ANONYMOUSNAME,"",50);

	$tb->add("siteinfo","special","nn",pmxTimezoneSelect('xdefault_timezone'),_TIMEZONEDEFAULT);
	$tb->add("siteinfo","yesno","xmultilingual",$GLOBALS['multilingual'],_ACTMULTILINGUAL);
	
    $langlist = mxGetAvailableLanguages(true);
	$sellanguage="";
    if (!$langlist) {
        $tb->add("siteinfo","warning","Error: the language-folder is empty!");
    } else {	
		$i=0;
		foreach ((array)$langlist as $caption => $value) {
        $sel1 = ($value == $language) ? ' checked="checked"' : '';
        $sel2 = (in_array($value, $language_avalaible)) ? ' checked="checked"' : '';
        $sellanguage.= '
			<li>
				<input type="radio" name="xlanguage" value="' . $value . '"' . $sel1 . ' />
				<input type="checkbox" name="xlanguage_avalaible[]" value="' . $value . '"' . $sel2 . ' /> ' . $caption . '
			</li>';
		}
	}
	$tb->add("siteinfo","special","languages",$sellanguage,_SELLANGUAGE ." / ". _LANGUAGES);
    $tb->add("siteinfo", "yesno", "xuseflags", $useflags, _ACTUSEFLAGS );
	$tb->add("siteinfo","number","xvkpInactiveMins", $vkpInactiveMins,_SECINACTIVELENGTH . "  [". _SECMINUTES . "]","",5);
    $tb->add("siteinfo", "yesno", "xbanners", $banners, _ACTBANNERS );
	
	/* graphics */
    $tb->addFieldset("graph", _GRAPHICOPT, "", true);
	$tb->add("graph","note",_THEME_INFO);
	$tb->add("graph","select","xjuitheme",$juitheme,_THEME4JQUI,"",1,$uthemes);
	$tb->add("graph","select","xvkpBlocksRight",$vkpBlocksRight,_BLOCKSHOW_RIGHT,"",1,$blockoptions1);
	$tb->add("graph","filebrowse","xsite_logo",mxEntityQuotes($site_logo),_SITELOGO,"",30,array("title"=>_SITELOGO,"showimage"=>true));
	
	/* news */
    $tb->addFieldset("news", _NEWSMODULE, "", true);
	$cxarticlecomm=array(_YES=>1,_COMMSCHOWONLY=>2,_NO=>0);
	$tb->add("news","select","xarticlecomm",$articlecomm,_COMMENTSARTICLES,"",3,$cxarticlecomm);
	$tb->add("news","range","xstoryhome", $storyhome,_STORIESHOME,"",200,"min=1 max=30 step=1");
	$tb->add("news","range","xstoryhome_cols", $storyhome_cols,_STORIESHOMECOLS,"",200,"min=1 max=5 step=1");
	$tb->add("news","range","xtop", $top,_ITEMSTOP,"",200,"min=5 max=30 step=1");
	$tb->add("news","range","xoldnum", $oldnum,_OLDSTORIES,"",200,"min=5 max=30 step=1");
    $tb->add("news","yesno", "xnotify", $notify, _NOTIFYSUBMISSION );
	$tb->add("news","input","xnotify_email",mxEntityQuotes($notify_email),_EMAIL2SENDMSG,"",30);
	$tb->add("news","input","xnotify_subject",mxEntityQuotes($notify_subject),_EMAILSUBJECT,"",30);
	$tb->add("news","textbox","xnotify_message",htmlspecialchars($notify_message),_EMAILMSG,"",50);
	$tb->add("news","input","xnotify_from",$notify_from,_EMAILFROM,"",30);
	/* Comments */
    $tb->addFieldset("comments", _COMMENTSOPT, "", true);
    $tb->add("comments", "yesno", "xanonpost", $anonpost, _ALLOWANONPOST );
	$tb->add("comments","number","xcommentlimit", $commentlimit,_COMMENTSLIMIT ,"",11);
    $tb->add("comments", "yesno", "xnotifycomment", $notifycomment, _NOTIFYCOMMENT . ' (' . _ALL . ') ' );	
	
	/* security */
	$sessloc=array(_FILE=>0,_DATABASE=>1);
    $tb->addFieldset("secopt", _SECOPT, "", true);
	$tb->add("secopt","warning",_LOGGINWARNING);
	$tb->add("secopt","number","xvkpSessLifetime", $vkpSessLifetime,_SECMEDLENGTH . "  [". _SECDAYS . "]",_SECDAYS2,5);
	$tb->add("secopt","select","xmxSessionLoc",$mxSessionLoc,_SESS_LOC,"",2,$sessloc);
    $tb->add("secopt", "yesno", "xvkpSafeCookie1", $vkpSafeCookie1, _SAFECOOKIE1 );
    $tb->add("secopt", "yesno", "xvkpSafeCookie2", $vkpSafeCookie2, _SAFECOOKIE2 );
    $tb->add("secopt", "yesno", "xvkpSafeSqlinject", $vkpSafeSqlinject , _SECSQLINJECT1 ,_SECSQLINJECT3);
	$tb->add("secopt", "yesno", "xmxCookieInfo", $mxCookieInfo, _COOKIECHOICE1,"");
	$tb->add("secopt", "input", "xmxCookieLink", $mxCookieLink, _COOKIECHOICE2,"");
	$tb->add("secopt", "select", "xmxCookiePos", $mxCookiePos, _COOKIECHOICE3,"",2,array(_CCTOP=>"top",_CCBOTTOM=>"bottom"));
	
	$options = array();
    for ($i = 0; $i <= 2; $i++) {
        $options[constant('_REQHTMLFILTER_' . $i)] = $i  ;
    }
	$tb->add("secopt","select","xmxEntitieLevel",$mxEntitieLevel,_REQHTMLFILTER,"",1,$options);
    $tb->add("secopt", "yesno", "xvkpsec_logging", $vkpsec_logging, _SEC_LOGGING );
    $tb->add("secopt", "yesno", "xvkpTracking",$vkpTracking, _TRACK_ACTIVATEIT );
    $refoptions = array();
    for ($i = 0; $i <= 3000;) {
        $refoptions[$i] = $i;
        $i = ($i < 1000) ? $i + 50 : $i + 500;
    }	
	$tb->add("secopt","select","xhttprefmax",$httprefmax,_MAXREF,_ADMIN_NOREFS,1,$refoptions);	
    $tb->add("secopt", "yesno", "xvkpIntranet", $vkpIntranet, _INTRANETOPT );
	$tb->add("secopt","warning",_INTRANETWARNING,"&nbsp;");
	
	/* FTP Data for future version */
	/*
	$tb->addFieldset("ftp", _FTPOPT, _FTPOPT_TEXT, true);
	$tb->add("ftp","yesno","xmxFTPon",$mxFTPon,_FTP_ON);
	$tb->add("ftp","input","xmxFTPhost",$mxFTPhost,_FTP_HOST,"",50);
	$tb->add("ftp","input","xmxFTPuser",$mxFTPuser,_MAILUNAME,"",50);
	$tb->add("ftp","input","xmxFTPpass",$mxFTPpass,_MAILPASS,"",50);
	$tb->add("ftp","number","xmxFTPport",$mxFTPport,_FTP_PORT,"",4);
	$tb->add("ftp","yesno","xmxFTPssl",$mxFTPssl,_FTP_SSL);
	$tb->add("ftp","input","xmxFTPdir",$mxFTPdir,_FTP_DIR);
	*/
	
	/* Mailsettings */
    $tb->addFieldset("mail", _MAILSETTINGS, "", true);
	$mailoptions=array(_MAILAUTH_0=>"mail",_MAILAUTH_1=>"smtp");
	$tb->add("mail","select","xmailauth",$mailauth,_MAILAUTH,"",1,$mailoptions);
	$tb->add("mail","input","xmailhost",$mailhost,_MAILHOST,_MAILHOST_2,50);
	$tb->add("mail","input","xmailuname",$mailuname,_MAILUNAME,"",50);
	$tb->add("mail","password","xmailpass",$mailpass,_MAILPASS,"",20);
	$tb->add("mail","number","xmailport", $mailport,_MAILPORT,_MAILPORT_2 ,5);
	$tb->add("mail","input","xpopauth",$popauth,_MAILPOP3AUTH,"",50);
	
	/* Footer */
    $tb->addFieldset("footer", _FOOTERMSG, "", true);
	$tb->add("footer","textbox","xfoot1",trim($foot1),_FOOTERLINE1);
	$tb->add("footer","textbox","xfoot2",trim($foot2),_FOOTERLINE2);
	$tb->add("footer","textbox","xfoot3",trim($foot3),_FOOTERLINE3);
	$tb->add("footer","textbox","xfoot4",trim($foot4),_FOOTERLINE4);
	$tb->add("footer","note", _ADMIN_FOOTCONSTMSG );
	
	
	/* Censor - options */
    $tb->addFieldset("censor", _CENSOROPTIONS, "", true);
    $censorstring = implode(", ", $CensorList);
    $CensorReplace = (empty($CensorReplace)) ? "*****" : $CensorReplace;	
	$tb->add("censor","textbox","xCensorstring",htmlspecialchars($censorstring),_CENSORMODEWORDS,_CUTWITHCOMMATA,50);
	$coptions=array(_NOFILTERING=>0,_MATCHANY=>1);
	$tb->add("censor","select","xCensorMode",$CensorMode,_CENSORMODE,"",1,$coptions);
	$tb->add("censor","input","xCensorReplace", mxEntityQuotes($CensorReplace),_CENSORREPLACE ,"",20);
	$tb->add("censor","special","b2","<a href=\"" . adminUrl('setban') . "\" target=\"_blank\"><em>" . _CLCKHERE . "</em></a>",_CENSORUSERNAMES);
	
	
	/* HTML-Optionen */
    $tb->addFieldset("htmlopt", _HTMLOPT, "", true);
	$options = array();
    foreach($doctype_array as $key => $var) {
        $options[$var['name']] =  $key;
    }
	$tb->add("htmlopt","select","xDOCTYPE",$DOCTYPE,_SITEDOCTYPE,"",1,$options);
    $tb->add("htmlopt", "yesno", "xTidyOutput", $TidyOutput, _USEHTMLTIDY );
    $tb->add("htmlopt", "yesno", "xmxJpCacheUse", $mxJpCacheUse, _JPCACHEUSE1A,_JPCACHEUSE1B );
    $tb->add("htmlopt", "number", "xmxJpCacheTimeout", ((empty($mxJpCacheTimeout)) ? MX_SETINACTIVE_MINS : $mxJpCacheTimeout), _JPCACHEUSE2A,_JPCACHEUSE2B,5 );
    if (extension_loaded("zlib") && (!mxIniGet("zlib.output_compression")) && (mxIniGet('output_handler') != 'ob_gzhandler')) {
		$tb->add("htmlopt", "yesno", "xmxUseGzipCompression", $mxUseGzipCompression, _GZIPCOMPRESS );
    } else {
		$tb->add("htmlopt", "hidden", "xmxUseGzipCompression", intval($mxUseGzipCompression) );
    }	
	
   // Allowed HTML
    $cols = 3;
    $tags_current = $GLOBALS['AllowableHTML'];

    $tags_normal = settingsGetHTMLTags('allowed');
    $tags_danger = settingsGetHTMLTags('dangerous');

    $tags_normal = array_diff($tags_normal, $tags_danger);

    $buildrows = function($tags, $tags_current, $cols)
    {
        ob_start();

        $y = ceil(count($tags) / $cols); // in 3 Teile splitten
        $parts = array_chunk($tags, $y);
        foreach ($parts as $chunks) {
            echo '<td>
            <table class="list" >
            <tr>
              <th style="width:25%;">' . _HTMLTAGNAME . '</th>
              <th class="align-center">' . mxCreateImage('images/allowed.gif', _HTMLTAGALLOWED, 0, 'title="' . _HTMLTAGALLOWED . '"') . '</th>
              <th class="align-center">' . mxCreateImage('images/allowed-plus.gif', _HTMLTAGALLOWEDWITHPARAMS, 0, 'title="' . _HTMLTAGALLOWEDWITHPARAMS . '"') . '</th>
              <th class="align-center">' . mxCreateImage('images/notallowed.gif', _HTMLTAGNOTALLOWED, 0, 'title="' . _HTMLTAGNOTALLOWED . '"') . '</th>
            </tr>';
            foreach ($chunks as $htmltag) {
                $sel = array(' ', ' ', ' ');
                if (empty($tags_current[$htmltag])) {
                    $sel[0] = ' checked="checked" ';
                    $tag = '&lt;' . $htmltag . '&gt;';
                } else {
                    $sel[$tags_current[$htmltag]] = ' checked="checked" ';
                    $tag = '<b>&lt;' . $htmltag . '&gt;</b>';
                }
                echo '<tr>
                  <td>' . $tag . '</td>
                  <td align="center"><input type="radio" value="1" name="htmlallow[' . $htmltag . ']"' . $sel[1] . '/></td>
                  <td align="center"><input type="radio" value="2" name="htmlallow[' . $htmltag . ']"' . $sel[2] . '/></td>
                  <td align="center"><input type="radio" value="0" name="htmlallow[' . $htmltag . ']"' . $sel[0] . '/></td>
                </tr>';
            }
            echo '</table></td>';
        }
        return ob_get_clean();
    } ;	
	$htmlopt='<table class="form full">
    <tr><td colspan="' . $cols . '"><br />' . _HTMLALLOWED . '</td></tr>
    <tr style="vertical-align:top">' . $buildrows($tags_normal, $tags_current, $cols) . '</tr>
    <tr><td colspan="' . $cols . '"><div class="warning" style="margin:0;">' . _HTMLWARNING . '</div></td></tr>
    <tr style="vertical-align:top">' . $buildrows($tags_danger, $tags_current, $cols) . '</tr>
    </table>';
	
	$tb->add("htmlopt","html",$htmlopt);
    $tb->add("htmlopt", "checkbox", "do_html_opt_reset", 0, _HTMLOPTRESET );
	
	
	/* Siteservice und debug */
    $tb->addFieldset("debug", _SETSERVICE, "", true);
    $tb->add("debug", "yesno", "xmxSiteService", $mxSiteService, _SITESERVICE );
	$tb->add("debug","textarea","xmxSiteServiceText",htmlspecialchars($mxSiteServiceText),_SITESERVICETEXT,"",50);

    $tb->add("debug", "yesno", "xmxUseThemecache", $mxUseThemecache, _DEACTHEMECACHE );
		$debug_errors=array(_NO=>VIEW_NOBODY,_MVADMIN=>VIEW_ADMIN,_MVALL=>VIEW_ALL);
	$tb->add("debug","output",_DEBUG_ERRORS);
	$tb->add("debug","select","debug[log]",$mxDebug['log'],_DEBUG_ERRORS_1,"",_NO,$debug_errors);
	$tb->add("debug","select","debug[screen]",$mxDebug['screen'],_DEBUG_ERRORS_2,"",_NO,$debug_errors);
	$tb->add("debug","select","debug[enhanced]",$mxDebug['enhanced'],_DEBUG_ENHANCED,"",_NO,$debug_errors);
	
	/* Database */
    $tb->addFieldset("dbinfo", _DBSETTINGS, "", true);
	$dbpassclick = "&nbsp;<a href=\"#\" onclick=\"alert('" . $dbpass . "'); return false;\">click</a>";
	$tb->add("dbinfo","input","xdbhost", $dbhost,_DBSERVER,"",30,"readonly='readonly'");
	$tb->add("dbinfo","input","xdbuname", $dbuname,_DBUSERNAME,"",30,"readonly='readonly'");
	$tb->add("dbinfo","password","xdbpass", "fzbtudtburuzt",_DBPASS,$dbpassclick,30,"readonly='readonly'");
	$tb->add("dbinfo","input","xdbname", $dbname,_DBNAME,"",30,"readonly='readonly'");
	$tb->add("dbinfo","input","xprefix", $prefix,_PREFIX,"",30,"readonly='readonly'");
	$tb->add("dbinfo","input","xuser_prefix", $user_prefix,_USERPREFIX,"",30,"readonly='readonly'");
		//$dbtypes=array("mysql"=>"0","mysqli"=>"1","pdo"=>"2");		//
	//$tb->add("dbinfo","select","xdbconnect",$xdbconnect,_DB_TYPE,_DB_TYPE_TEXT,1,$dbtypes);
	$tb->add("dbinfo","hidden","xdbconnect",1);
	
   /* formular abrufen */
    $form = $tb->Show();
    /*
     * Template
     */
    // Template initialisieren
    $template = load_class('Template');
    $template->init_path(__FILE__);

    /* hier die Ausgabefelder angeben */

    /* Variablen an das Template uebergeben */
    $template->assign(compact('form', 'info2'));

    mxIncludeHeader();
    /* Template ausgeben (echo) */
    $template->display('main.html');

    mxIncludeFooter();	
}


/**
 * ConfigSave()
 *
 * @param mixed $pvs
 * @return
 */
function ConfigSave($pvs)
{
    $allowedhtml = array();
    $tags = settingsGetHTMLTags('allowed');
    if (isset($pvs['do_html_opt_reset'])) {
        $pvs['htmlallow'] = array('a' => 2, 'address' => 1, 'b' => 1, 'big' => 1, 'blockquote' => 1, 'br' => 2, 'cite' => 1, 'code' => 1, 'div' => 2, 'dl' => 1, 'dt' => 1, 'em' => 1, 'fieldset' => 1, 'h3' => 2, 'h4' => 2, 'h5' => 2, 'h6' => 2, 'hr' => 2, 'i' => 1, 'img' => 2, 'li' => 1, 'ol' => 1, 'p' => 2, 'pre' => 1, 'small' => 1, 'span' => 2, 'strike' => 1, 'strong' => 1, 'sub' => 1, 'sup' => 1, 'table' => 2, 'tbody' => 1, 'td' => 2, 'tfoot' => 1, 'th' => 2, 'thead' => 1, 'tr' => 2, 'tt' => 1, 'u' => 1, 'ul' => 2);
    }
    foreach ($tags as $htmltag) {
        if (isset($pvs['htmlallow'][$htmltag])) {
            $tagval = intval($pvs['htmlallow'][$htmltag]);
        } else {
            $tagval = 0;
        }
        $allowedhtml[] = "'$htmltag'=>$tagval";
    }
    $newAllowableHTML = 'array(' . implode(',', $allowedhtml) . ')';

    include(PMX_CONFIGFILE);
    extract($pvs, EXTR_OVERWRITE);
	/* since PHP7 only prmitted MYSQLI */
	$dbconnect=1;
    $xvkpSessLifetime = (empty($xvkpSessLifetime)) ? 0 : $xvkpSessLifetime ;
	$xmxSessionLoc=(empty($xmxSessionLoc)) ? 0 : intval($xmxSessionLoc);
    $xvkpInactiveMins = (empty($xvkpInactiveMins)) ? 10 : $xvkpInactiveMins;
    $xvkpIntranet = (empty($xvkpIntranet)) ? 0 : intval($xvkpIntranet);
    $xvkpTracking = (empty($xvkpTracking)) ? 0 : intval($xvkpTracking);
    $xvkpSafeCookie1 = (empty($xvkpSafeCookie1)) ? 0 : intval($xvkpSafeCookie1);
    $xvkpSafeCookie2 = (empty($xvkpSafeCookie2)) ? 0 : intval($xvkpSafeCookie2);
    $xvkpSafeSqlinject = (empty($xvkpSafeSqlinject)) ? 0 : intval($xvkpSafeSqlinject);
    $xmxEntitieLevel = (empty($xmxEntitieLevel)) ? 0 : intval($xmxEntitieLevel);
    $xvkpsec_logging = (empty($xvkpsec_logging)) ? 0 : intval($xvkpsec_logging);
    $xstoryhome_cols = (empty($xstoryhome_cols)) ? 1 : $xstoryhome_cols;
    $xmxDeactNukeCompatible = (empty($xmxDeactNukeCompatible)) ? 0 : intval($xmxDeactNukeCompatible);
    $xmxCreateNukeCookie = (empty($xmxCreateNukeCookie)) ? 0 : intval($xmxCreateNukeCookie);
    $xmailport = (empty($xmailport)) ? 25 : $xmailport;
    $xmailauth = (empty($xmailauth)) ? "mail" : $xmailauth;
    $xpopauth = (empty($xpopauth)) ? '' : $xpopauth; // ab 0.1.8
    $xanonymous = (empty($xanonymous)) ? "Anonymous" : $xanonymous;
    $xmxSiteService = ($xmxSiteService && empty($xmxSiteServiceText)) ? 0 : $xmxSiteService;
    $xUseGzipCompression = (empty($xUseGzipCompression)) ? 0 : intval($xUseGzipCompression);
    $xmxJpCacheUse = (empty($xmxJpCacheUse)) ? 0 : intval($xmxJpCacheUse);
    $xmxJpCacheTimeout = (empty($xmxJpCacheTimeout)) ? MX_SETINACTIVE_MINS : $xmxJpCacheTimeout;
    $xvkpBlocksRight = (empty($xvkpBlocksRight)) ? 0 : intval($xvkpBlocksRight);
	$xmxCookieInfo = (empty($xmxCookieInfo)) ? 0 : intval($xmxCookieInfo);
	$xmxCookieLink = (empty($xmxCookieLink)) ? "" : $xmxCookieLink;	
	$xmxCookiePos = (empty($xmxCookiePos)) ? "top" : $xmxCookiePos;

    $xDOCTYPE = (empty($xDOCTYPE)) ? 0 : intval($xDOCTYPE);
    $xTidyOutput = (empty($xTidyOutput) || !is_tidy_available()) ? 0 : intval($xTidyOutput);

    $xmxFTPon = (empty($xmxFTPon)) ? 0 : intval($xmxFTPon);
	$xmxFTPhost= (empty($xmxFTPhost)) ? "localhost" : $xmxFTPhost;
	$xmxFTPport= (empty($xmxFTPport)) ? "21" : $xmxFTPport;
	$xmxFTPuser= (empty($xmxFTPuser)) ? "" : $xmxFTPuser;
	$xmxFTPpass= (empty($xmxFTPpass)) ? "" : $xmxFTPpass;	
	$xmxFTPssl = (empty($xmxFTPssl)) ? 0 : intval($xmxFTPssl);
	$xmxFTPdir= (empty($xmxFTPdir)) ? "" : $xmxFTPdir;	
	
    /* nicht interaktiv konfigurierbare Optionen (ohne x) */
    $xshow_pragmamx_news = (isset($show_pragmamx_news)) ? intval($show_pragmamx_news) : 1;
    $xcheck_chmods = (isset($check_chmods)) ? intval($check_chmods) : 1;

    if (!defined("MX_FIRSTGROUPNAME")) define("MX_FIRSTGROUPNAME", "User");

    $xcensorlist = preg_split('#\s*,\s*#m', trim($xCensorstring));
    foreach ($xcensorlist as $word) {
        $xx = trim($word);
        if (!empty($xx)) {
            $words[] = '"' . trim($word) . '"';
        }
    }
    $newcensorlist = (isset($words)) ? 'array(' . implode(',', $words) . ')' : 'array()';
    $xCensorMode = (empty($xCensorMode)) ? 0 : $xCensorMode;
    $xCensorReplace = (empty($xCensorReplace)) ? "*****" : $xCensorReplace;

    /* debug */
    $debdef = array('log' => 0, 'screen' => 0, 'enhanced' => 0);
    foreach ($debdef as $key => $value) {
        //if (($debug[$key]==1)) {
            $value = intval($debug[$key]);
        //}
        $newdebug[] = "'$key'=>$value";
    }
    $newdebug = 'array(' . implode(',', $newdebug) . ')';

    /* Standard Zeitzone */
    if (!$xdefault_timezone) {
        $xdefault_timezone = (defined('_SETTIMEZONE')) ? _SETTIMEZONE : date_default_timezone_get();
    }

    /* verfügbare Sprachen */
    $newlanguage_avalaible[] = $xlanguage;
    if (isset($xlanguage_avalaible)) {
        $newlanguage_avalaible = array_merge($newlanguage_avalaible, $xlanguage_avalaible);
    }
    $newlanguage_avalaible = 'array("' . implode('", "', array_unique($newlanguage_avalaible)) . '")';

    if (empty($mxSecureKey)) {
        $xmxSecureKey = addslashes(md5(mt_rand() . __file__));
    } else {
        $xmxSecureKey = addslashes($mxSecureKey);
    }
    // //// beginn setup-string
    $cont = "<?php
/*
 pragmaMx - Web Content Management System
 Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 write with: \$Id: index.php 216 2016-09-20 13:29:30Z PragmaMx $
 Version: pragmaMx " . PMX_VERSION . "
 */

/**
 * Database & System Config
 *   dbhost:      Database Hostname      (ask your Provider)
 *   dbname:      Database Name          (ask your Provider)
 *   dbuname:     Database Username      (ask your Provider)
 *   dbpass:      Database User-Password (ask your Provider)
 *   prefix:      Your Database table's prefix
 *   user_prefix: Your Users' Database table's prefix (To share it)
 */
\$mxConf['dbhost']      = '$dbhost';
\$mxConf['dbname']      = '$dbname';
\$mxConf['dbuname']     = '$dbuname';
\$mxConf['dbpass']      = '$dbpass';
\$mxConf['prefix']      = '$prefix';
\$mxConf['user_prefix'] = '$user_prefix';
\$mxConf['dbtype']      = 'mysql';
\$mxConf['dbconnect']   = '$dbconnect';
/**
 * all others...
 */
if(!defined('MX_FIRSTGROUPNAME')) define('MX_FIRSTGROUPNAME','" . MX_FIRSTGROUPNAME . "');
\$mxConf['mxSecureKey']           = '$xmxSecureKey';
\$mxConf['vkpSessLifetime']       = '$xvkpSessLifetime';
\$mxConf['mxSessionLoc']          = '$xmxSessionLoc';
\$mxConf['vkpInactiveMins']       = '$xvkpInactiveMins';
\$mxConf['vkpIntranet']           = '$xvkpIntranet';
\$mxConf['vkpSafeCookie1']        = '$xvkpSafeCookie1';
\$mxConf['vkpSafeCookie2']        = '$xvkpSafeCookie2';
\$mxConf['mxCookieInfo']		  = '$xmxCookieInfo';
\$mxConf['mxCookieLink']		  = '$xmxCookieLink';
\$mxConf['mxCookiePos']		  	  = '$xmxCookiePos';
\$mxConf['vkpSafeSqlinject']      = '$xvkpSafeSqlinject';
\$mxConf['mxEntitieLevel']        = '$xmxEntitieLevel';
\$mxConf['vkpsec_logging']        = '$xvkpsec_logging';
\$mxConf['mxUseGzipCompression']  = '$xmxUseGzipCompression';
\$mxConf['mxJpCacheUse']          = '$xmxJpCacheUse';
\$mxConf['mxJpCacheTimeout']      = '$xmxJpCacheTimeout';

\$mxConf['mxFTPon']          = '$xmxFTPon';
\$mxConf['mxFTPhost']        = '$xmxFTPhost';
\$mxConf['mxFTPuser']        = '$xmxFTPuser';
\$mxConf['mxFTPpass']        = '$xmxFTPpass';
\$mxConf['mxFTPport']        = '$xmxFTPport';
\$mxConf['mxFTPssl']         = '$xmxFTPssl';
\$mxConf['mxFTPdir']         = '$xmxFTPdir';
\$mxConf['mailuname']        = '$xmailuname';
\$mxConf['mailhost']         = '$xmailhost';
\$mxConf['mailpass']         = '$xmailpass';
\$mxConf['mailport']         = '$xmailport';
\$mxConf['mailauth']         = '$xmailauth';
\$mxConf['popauth']          = '$xpopauth';
\$mxConf['sitename']         = '$xsitename';
\$mxConf['site_logo']        = '$xsite_logo';
\$mxConf['slogan']           = '$xslogan';
\$mxConf['startdate']        = '$xstartdate';
\$mxConf['adminmail']        = '$xadminmail';
\$mxConf['anonpost']         = '$xanonpost';
\$mxConf['DOCTYPE']          = '$xDOCTYPE';
\$mxConf['TidyOutput']       = '$xTidyOutput';
\$mxConf['juitheme']         = '$xjuitheme';
\$mxConf['foot1']            = '$xfoot1';
\$mxConf['foot2']            = '$xfoot2';
\$mxConf['foot3']            = '$xfoot3';
\$mxConf['foot4']            = '$xfoot4';
\$mxConf['commentlimit']     = '$xcommentlimit';
\$mxConf['anonymous']        = '$xanonymous';
\$mxConf['articlecomm']      = '$xarticlecomm';
\$mxConf['vkpBlocksRight']   = '$xvkpBlocksRight';
\$mxConf['top']              = '$xtop';
\$mxConf['storyhome']        = '$xstoryhome';
\$mxConf['storyhome_cols']   = '$xstoryhome_cols';
\$mxConf['oldnum']           = '$xoldnum';
\$mxConf['banners']          = '$xbanners';
\$mxConf['language_avalaible']            = $newlanguage_avalaible;
\$mxConf['language']          = '$xlanguage';
\$mxConf['default_timezone']  = '$xdefault_timezone';
\$mxConf['multilingual']      = '$xmultilingual';
\$mxConf['useflags']          = '$xuseflags';
\$mxConf['notify']            = '$xnotify';
\$mxConf['notifycomment']     = '$xnotifycomment';
\$mxConf['notify_email']      = '$xnotify_email';
\$mxConf['notify_subject']    = '$xnotify_subject';
\$mxConf['notify_message']    = '$xnotify_message';
\$mxConf['notify_from']       = '$xnotify_from';
\$mxConf['httprefmax']        = '$xhttprefmax';
\$mxConf['vkpTracking']       = '$xvkpTracking';
\$mxConf['AllowableHTML']     = $newAllowableHTML;
\$mxConf['CensorList']        = $newcensorlist;
\$mxConf['CensorMode']        = '$xCensorMode';
\$mxConf['CensorReplace']     = '$xCensorReplace';
\$mxConf['tipath']            = 'images/topics/';
\$mxConf['mxSiteService']     = '$xmxSiteService';
\$mxConf['mxSiteServiceText'] = '$xmxSiteServiceText';
\$mxConf['mxUseThemecache']   = '$xmxUseThemecache';
\$mxConf['mxDebug']           = $newdebug;
\$mxConf['show_pragmamx_news']= '$xshow_pragmamx_news';
\$mxConf['check_chmods']      = '$xcheck_chmods';

/**************************************
 * Do not touch the following options!
 */
(stripos(\$_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied');
// only dbtype MySQL is supported by pragmaMx " . PMX_VERSION . "!

// set globals
foreach (\$mxConf as \$key => \$value) {
  \$mxConf[\$key] = (!is_array(\$value)) ? stripslashes(\$value) : \$value;
}
unset(\$key, \$value);
extract(\$mxConf, EXTR_OVERWRITE);

?>";
//\$mxConf['mxDeactNukeCompatible'] = '$xmxDeactNukeCompatible';
//\$mxConf['mxCreateNukeCookie']    = '$xmxCreateNukeCookie';
    // //// ende setup-string
    mxSecureLog("SecLog", "Change config.php");

    $ok = mx_write_file(PMX_CONFIGFILE, $cont, true);

    if ($ok) {
        $statmsg = _ADMIN_SETTINGSAVED;
        $delay = 2;
        include_once(PMX_SYSTEM_DIR . DS . 'mx_reset.php');
        resetPmxCache();
    } else {
        $statmsg = "<big>" . _ADMIN_SETTINGNOSAVED . "</big><p>" . _ADMIN_CANTCHANGE . " (config.php)</p>";
        $delay = 5;
    }

    mxRedirect(adminUrl(PMX_MODULE), $statmsg, $delay);
	
}



/**
 * settingsGetHTMLTags()
 * Local function to provide list of all possible HTML tags
 *
 * @return array ()
 */
function settingsGetHTMLTags($type = 'allowed')
{
    $allowed = array(/* HTML-Tags die erlaubt werden können */
        '!--',
        'a',
        'abbr',
        'acronym',
        'address',
        'applet',
        'area',
        'article',
        'aside',
        'audio',
        'b',
        'base',
        'basefont',
        'bdi',
        'bdo',
        'big',
        'blockquote',
        'br',
        'button',
        'canvas',
        'caption',
        'center',
        'cite',
        'code',
        'col',
        'colgroup',
        'data',
        'datalist',
        'dd',
        'del',
        'details',
        'dfn',
        'dialog',
        'dir',
        'div',
        'dl',
        'dt',
        'em',
        'embed',
        'fieldset',
        'figcaption',
        'figure',
        'font',
        'footer',
        'form',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'header',
        'hgroup',
        'hr',
        'i',
        'img',
        'input',
        'ins',
        'kbd',
        'keygen',
        'label',
        'legend',
        'li',
        'link',
        'main',
        'map',
        'mark',
        'marquee',
        'menu',
        'menuitem',
        'meta',
        'meter',
        'nav',
        'nobr',
        'object',
        'ol',
        'optgroup',
        'option',
        'output',
        'p',
        'param',
        'pre',
        'progress',
        'q',
        's',
        'samp',
        'script',
        'section',
        'select',
        'small',
        'source',
        'span',
        'strike',
        'strong',
        'sub',
        'summary',
        'sup',
        'table',
        'tbody',
        'td',
        'textarea',
        'tfoot',
        'th',
        'thead',
        'time',
        'tr',
        'track',
        'tt',
        'u',
        'ul',
        'var',
        'video',
        'wbr',
		'iframe',
		'iframeset',
        );

    $notallowed = array(/* HTML-Tags die nie erlaubt sind und in der Liste nicht erscheinen sollen */
        'applet',
        'body',
        'embed',
        'frame',
        'frameset',
        'head',
        'html',
        'isindex',
        'link',
        'meta',
        'noframes',
        'noscript',
        'object',
        'rp',
        'rt',
        'ruby',
        'script',
        'style',
        'template',
        'title',
        );

    $dangerous = array(/* erlaubte Tags, die aber als gefährlich gelten */
        'body',
        'img',
        'form',
        'dialog',
        'figure',
        'keygen',
        'output',
        'source',
        'video',
        'iframe',
		'iframeset',
		'object',
        );

    /* nicht erlaubte Tags aus globaler config importieren */
    $notallowed = array_merge($notallowed, $GLOBALS['mxBadHtmlTags']);

    /* erlaubt ist nur das, was nicht nicht erlaubt ist :-)) */
    $allowed = array_diff($allowed, $notallowed);

    /* gefährlich muss auch erlaubt sein */
    $dangerous = array_intersect($allowed, $dangerous);

    /* guggen was ausgegeben werden soll... */
    switch ($type) {
        case 'notallowed':
            $return = $notallowed;
            break;
        case 'dangerous':
            $return = $dangerous;
            break;
        default:
            $return = $allowed;
    }

    /* alphabetisch sortieren... */
    asort($return);
    return $return;
}

/* Was ist zu tun ;-) */
switch ($op) {
    case PMX_MODULE . '/save':
        //ConfigSave($_POST);
        break;

    default:
        Configure();
        break;
}

?>