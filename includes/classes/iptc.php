<?php

    /************************************************************\
   
        IPTC EASY 1.0 - IPTC data manipulator for JPEG images
           
        All reserved www.image-host-script.com
       
        Sep 15, 2008
   
    \************************************************************/

    define('IPTC_OBJECT_NAME', '005');
    define('IPTC_EDIT_STATUS', '007');
    define('IPTC_PRIORITY', '010');
    define('IPTC_CATEGORY', '015');
    define('IPTC_SUPPLEMENTAL_CATEGORY', '020');
    define('IPTC_FIXTURE_IDENTIFIER', '022');
    define('IPTC_KEYWORDS', '025');
    define('IPTC_RELEASE_DATE', '030');
    define('IPTC_RELEASE_TIME', '035');
    define('IPTC_SPECIAL_INSTRUCTIONS', '040');
    define('IPTC_REFERENCE_SERVICE', '045');
    define('IPTC_REFERENCE_DATE', '047');
    define('IPTC_REFERENCE_NUMBER', '050');
    define('IPTC_CREATED_DATE', '055');
    define('IPTC_CREATED_TIME', '060');
    define('IPTC_ORIGINATING_PROGRAM', '065');
    define('IPTC_PROGRAM_VERSION', '070');
    define('IPTC_OBJECT_CYCLE', '075');
    define('IPTC_BYLINE', '080');
    define('IPTC_BYLINE_TITLE', '085');
    define('IPTC_CITY', '090');
    define('IPTC_PROVINCE_STATE', '095');
    define('IPTC_COUNTRY_CODE', '100');
    define('IPTC_COUNTRY', '101');
    define('IPTC_ORIGINAL_TRANSMISSION_REFERENCE', '103');
    define('IPTC_HEADLINE', '105');
    define('IPTC_CREDIT', '110');
    define('IPTC_SOURCE', '115');
    define('IPTC_COPYRIGHT_STRING', '116');
    define('IPTC_CAPTION', '120');
    define('IPTC_LOCAL_CAPTION', '121');

    class pmxIPTC {
        var $meta=Array();
        var $hasmeta=false;
        var $file=false;
       
       
        function __construct($filename) {
			
			if (file_exists($filename)) {
				$size = getimagesize($filename,$info);
				$this->hasmeta = isset($info["APP13"]);
			}
            if($this->hasmeta)
                $this->meta = iptcparse ($info["APP13"]);
            $this->file = $filename;
        }
/*
        function set($tag, $data) {
            $this->meta ["2#$tag"]= Array( $data );
            $this->hasmeta=true;
        }

*/
        function set($tag, $data) {
            if(is_array($data)){
                $c = count($data);
                for ($i=0; $i <$c; $i++){
                       $this->meta["2#$tag"][$i] = $data[$i];
                }               
                $this->hasmeta=true;
            } else {
                $this->meta["2#$tag"]= array( $data );
                $this->hasmeta=true;
            }
        }

        function setall($extmeta = array()) {
            $this->meta=$extmeta;
        }

        function get($tag) {
            return isset($this->meta["2#$tag"]) ? $this->meta["2#$tag"][0] : false;
        }
       
        function getall() {
            return $this->meta;
        }
		
        function dump() {
            print_r($this->meta);
        }
        function binary() {
            $iptc_new = '';
            foreach (array_keys($this->meta) as $s) {
                $tag = str_replace("2#", "", $s);
                $iptc_new .= $this->iptc_maketag(2, $tag, $this->meta[$s][0]);
            }       
            return $iptc_new;   
        }
        function iptc_maketag($rec,$dat,$val) {
            $len = strlen($val);
            if ($len < 0x8000) {
                   return chr(0x1c).chr($rec).chr($dat).
                   chr($len >> 8).
                   chr($len & 0xff).
                   $val;
            } else {
                   return chr(0x1c).chr($rec).chr($dat).
                   chr(0x80).chr(0x04).
                   chr(($len >> 24) & 0xff).
                   chr(($len >> 16) & 0xff).
                   chr(($len >> 8 ) & 0xff).
                   chr(($len ) & 0xff).
                   $val;
                  
            }
        } 
		  
        function write() {
            if(!function_exists('iptcembed')) return false;
            $mode = 0;
            $content = iptcembed($this->binary(), $this->file, $mode);   
            $filename = $this->file;
			
            if ($content !="") {   
				@unlink($filename); #delete if exists
			   
				$fp = fopen($filename, "w");
				fwrite($fp, $content);
				fclose($fp);
			}
        }   
       
        #requires GD library installed
        function removeAllTags() {
            $this->hasmeta=false;
            $this->meta=Array();
            $img = imagecreatefromstring(implode(file($this->file)));
            @unlink($this->file); #delete if exists
            imagejpeg($img,$this->file,100);
        }
    };
   
   

/**
Example read copyright string:

$i = new iptc("test.jpg");
echo $i->get(IPTC_COPYRIGHT_STRING);

Update copyright statement:
$i = new iptc("test.jpg");
echo $i->set(IPTC_COPYRIGHT_STRING,"Here goes the new data");
$i->write();

NOTE1: Data may be anything, even a binary file. I have so far tested and embedded an MS-Excel file directly to jpeg and it worked just perfect.

NOTE2: The writing purpose, it uses GD Library.

Further imporvements / changes may be followed at www.image-host-script.com

I hope it helps.
Ali..
*/
?>