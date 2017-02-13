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
 * this is a part of Tecmu 
 *
 * $Revision: 6 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (Mi, 08. Jul 2015) $
 *
 */

defined('mxMainFileLoaded') or die('access denied');

function tecmu_isSchedule($path="") 
{
    global $prefix;

        $files =false;
        foreach ((array)glob(PMX_MODULES_DIR . DS . '*' .  DS . 'inc' . DS . 'tecmu.schedule.php', GLOB_NOSORT) as $filename) {
            if ($filename) {
                $files= basename(dirname(pathinfo($filename,PATHINFO_DIRNAME)));//$filename; //, PATHINFO_FILENAME
				continue;
            }
        }
	return $files;

}

function tecmu_isContest($path="") 
{
    global $prefix;

        $files =false;
        foreach ((array)glob(PMX_MODULES_DIR . DS . '*' .  DS . 'inc' . DS . 'tecmu.contest.php', GLOB_NOSORT) as $filename) {
            if ($filename) {
                $files= basename(dirname(pathinfo($filename,PATHINFO_DIRNAME)));//$filename; //, PATHINFO_FILENAME
				continue;
            }
        }
	return $files;


}
function tecmu_isSirius($path="") 
{
    global $prefix;

        $files =false;
        foreach ((array)glob(PMX_MODULES_DIR . DS . '*' .  DS . 'inc' . DS . 'tecmu.siriusgallery.php', GLOB_NOSORT) as $filename) {
            if ($filename) {
                $files= basename(dirname(pathinfo($filename,PATHINFO_DIRNAME)));//$filename; //, PATHINFO_FILENAME
				continue;
            }
        }
	return $files;
}

function tecmu_addNews ($userid,$uname,$title,$text,$topic='1',$language="en")
{
    global $prefix;
	$qry = sql_query("INSERT INTO ${prefix}_queue (uid, uname, subject, story, storyext, timestamp, topic) VALUES ('".$userid."', '".$uname."', '".$title."', '".$text."', '', '".time()."', ".$topic.")");

}

function tecmu_checkversion ($module="",$version=0)
{
    global $currentlang;
	
	$updateurl=tecmu_update_connect("http://www.versionscheck.tecmu.de/software/index.php");
	if ($updateurl==false) return;
	$update1=tecmu_update_connect("http://".$updateurl."&modul=".$module."&version=".$version."&clanguage=".$currentlang);
	if ($update1==false) return;
	$update=@unserialize($update1);
	if (!is_array($update)) return;
	if ($update['check']) {	//ok
		echo "<div class=\"note\">";
		echo "<h4>".$update['title']."</h4>";
		echo $update['text'].":". $update['version']."<br />";
		echo"<br />";
	} else {				// new version
		echo "<div class=\"warning\">";
		echo "<h4>".$update['title']."</h4>";
		echo $update['text']." [ ". $update['version']." ]<br />";
		echo "<a href=\"".$update['updateurl']."\" target=\"_blank\" >".$update['updateurl']."</a><br />";
	
	}
		echo "<hr /><div class=\"tiny\">".$update['info']."&nbsp;:&nbsp;";
		echo "<a href=\"".$update['url']."\" target=\"_blank\" >".$update['url']."</a></div>";
		echo "</div>";
		//$result="<iframe src=\"".$updateurl."\" width=\"95%\" height=\"140\" frameborder=\"0\" ></iframe>";
	return;
}

function tecmu_update_connect($url="") {
		if ($url=="") return;
		$useragent=$_SERVER['HTTP_USER_AGENT'];
	    $result=false;
	if (function_exists("curl_init")) {
        /**
        * cURL-Session initialisieren
        */
        $ch = curl_init($url);
        /**
        * Weiter Parameter der cURL-Session übergeben
        */
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        /**
        * cURL Anweisen, das die Übergabe
            zurückgegeben werden soll
        */
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        /**
        * cURL mitteilen, welcher Useragent übergeben
            werden soll.
        */
        curl_setopt ($ch, CURLOPT_USERAGENT, $useragent);
        /**
        * cURL Rückgabe in $result speichern
        */
        $result = @curl_exec ($ch);
        curl_close ($ch);
	}
		if (trim($result)=="FORBIDDEN") return false;
//    if ($content) {
//        $content .= "\n\n<p class='hide'>$comment</p>";
//    } else {
//        $splits = preg_split('#[?]#', $url);
//        $content = '<p class="warning">no url-wrapper found for: <a href="' . $url . '" target="_blank" rel="pretty">' . $splits[0] . '</a></p>';
//    }

    //die($content);

    return $result;
}



?>