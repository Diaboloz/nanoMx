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
 * this is a part of SiriusGallery
 *
 * $Revision: 6 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (Mi, 08. Jul 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');
// ermitteld die aktuellen Userdaten
$mx_user = mxGetUserData();

if (MX_IS_USER) {
    define("MX_USERNAME", $mx_user['uname']);
    define("MX_USERID", $mx_user['uid']);
}
// --------------------------------------
include_once("functions.tecmu.module.php");

function searchUser($search_query = array())
{
    global $user_prefix;
    $search = "user_stat=1 ";
    if (is_array($search_query)) {
        foreach ($search_query as $key => $value) {
            $search .= " and $key='$value'";
        }
    } else {
        $search .= $search_query;
    }
    $result = sql_query("select * from {$user_prefix}_users WHERE $search ");
    return sql_fetch_assoc($result);
}

function addNews($userid, $title, $text, $topic = 1)
{
    global $prefix;
    $title = trim($title);
    $text = trim($text);
    if (empty($title) || empty($text)) return;

    $user = mxGetUserDataFromUid($userid);
    $htmltag = "";
    $htmltags = mxGetAllowedHtml();
    foreach($htmltags as $key => $tag) {
        $htmltag .= "<" . $tag . ">";
    }
    $topic = intval($topic);

    $title = mxAddSlashesForSQL(strip_tags($title));
    $text = mxPrepareToHTMLDisplay(strip_tags($text, $htmltag));
    $text = mxAddSlashesForSQL($text);

    $qry = sql_query("INSERT INTO ${prefix}_queue (uid, uname, subject, story, storyext, timestamp, topic) VALUES ('$userid', '" . $user['name'] . "', '$title', '$text', '', '" . time() . "', $topic)");

    return;
}

function onClick ()
{
    // add_script verwenden
    // die $bgcolor Variablen sind unnötig
    global $prefix, $currentlang, $module_name, $WSCFG;
    pmxHeader::add_script('includes/tecmu/js/click.js');
}

function popWindowJS ()
{
    // add_script verwenden
    // die $bgcolor Variablen sind unnötig, bzw. eigentlich alles was global ist
    pmxHeader::add_script('includes/tecmu/js/popWindow.js');
}

function addChecklist ()
{
    // add_script verwenden
    // die $bgcolor Variablen sind unnötig, bzw. eigentlich alles was global ist
    pmxHeader::add_script('includes/tecmu/js/checklist.js');
}

function addCss ()
{
    // add_style verwenden
    // die $bgcolor Variablen sind unnötig, bzw. eigentlich alles was global ist
    pmxHeader::add_style('layout/style/default.forms.css');
    pmxHeader::add_style('layout/style/default.tables.css');
}

function goHomeBACK()
{
    global $module_name, $WSCFG;
    // $img2="<img src='modules/" . $module_name . "/style/images/bullet_arrow_up.png' alt='"._GOHOME."' title='"._GOHOME."'/>";
    // / Tabellen sind veraltet
    echo "<table width=\"100%\"><tr><td width=\"30%\"><p class='align-center'>" . _GOBACK . "</p></td>";
    echo "<td>&nbsp;</td><td width=\"30%\"><p class='align-center'>[<a href='#home'>" . _GOHOME . "</a>]</p></td></tr></table><br/>";
}

function goCpanel($stab = 0)
{
    // wofür ist $stab, wird nicht verwendet..
    global $module_name;
    return mxRedirect(adminUrl($module_name));
}

function isHome()
{
    echo "<a name='home'></a>";
}

function if_GD_exists()
{
    if (! extension_loaded('gd')) {
        return false;
    }
    return true;
}
function if_EXIF_exists()
{
    if (! extension_loaded('exif')) {
        return false;
    }
    return true;
}
function if_IMAGICK_exists()
{
    exec("convert -version ", $out, $rcode);
    if ($rcode == 0) {
        return false;
    }
    return true;
}

function getGDVersion($user_ver = 2)
{
    if (! extension_loaded('gd')) {
        return false;
    }
    static $gd_ver = 0;
    // Just accept the specified setting if it's 1.
    // if ($user_ver == 1) { $gd_ver = 1; return 1; }
    // Use the static variable if function was called previously.
    // if ($user_ver !=2 && $gd_ver > 0 ) { return $gd_ver; }
    // Use the gd_info() function if possible.
    if (function_exists('gd_info')) {
        $ver_info = gd_info();
        preg_match('/\d/', $ver_info['GD Version'], $match);
        $gd_ver = $match[0];
        // return $match[0];
    }
    // If phpinfo() is disabled use a specified / fail-safe choice...
    if (!preg_match('/phpinfo/', ini_get('disable_functions')) and $gd_ver == 0) {
        /*        if ($user_ver == 2) {
            $gd_ver = 2;
            return 2;
        } else {
            $gd_ver = 1;
            return 1;
        }
    } else {
        */// ...otherwise use phpinfo().
        ob_start();
        phpinfo(8);
        $info = ob_get_contents();
        ob_end_clean();
        $info = stristr($info, 'gd version');
        preg_match('/\d/', $info, $match);
        $gd_ver = $match[0];
    }
    if (!function_exists("getimagesize") && !function_exists("image_type_to_mime_type") && !function_exists("imagecreatefromgif") && !function_exists("imagecreatefromjpeg") && !function_exists("imagecreatefrompng") && !function_exists("floor") && !function_exists("imagecreatetruecolor") && !function_exists("imagecopyresampled") && !function_exists("imagedestroy") && !function_exists("imagealphablending") && !function_exists("imagecolorallocatealpha") && !function_exists("imagecopy") && !function_exists("imagecolorallocate") && !function_exists("imagesetpixel")
            ) $match[0] = false;

    return $match[0];
}
// created thumbnail from filename $original
// $thumbnail is the filename of thumb without extension
// scale = 0 image resizes to natural height and width
// sclae = 1 image is fixed to supplied width and height and cropped
// quality = 1...100 jpg-quality
// thumb is saved as jpg-file
// return = true ... thumb created
function create_thumb($original, $thumbnail, $max_width, $max_height, $scale = 0, $quality = 80)
{
    if (!is_file($original)) return false;

    $filtersmooth = -15; // -15
    list ($src_width, $src_height, $type, $w) = getimagesize($original);
    $imgtype = image_type_to_mime_type($type);

    set_time_limit(20);

    switch (strtolower($imgtype)) {
        case "image/gif":
            // "Image is a gif";
            if (!$srcImage = @imagecreatefromgif($original)) {
                return false;
            }
            break;
        case "image/jpeg":
        case "image/jpg":
        case "image/pjpeg":
            // "Image is a jpeg";
            if (!$srcImage = @imagecreatefromjpeg($original)) {
                return false;
            }
            break;
        case "image/png":
        case "image/x-png":
            // "Image is a png";
            if (!$srcImage = @imagecreatefrompng($original)) {
                return false;
            }
            break;
        case "image/bmp":
            // "Image is a bmp";
            if (!$srcImage = @imagecreatefrombmp($original)) {
                return false;
            }
            break;
    }

    switch ($scale) {
        default:
        case '0': // image resizes to natural height and width
            if ($src_width > $src_height) {
                $thumb_width = $max_width;
                $thumb_height = floor($src_height * ($max_width / $src_width));
            } else if ($src_width < $src_height) {
                $thumb_height = $max_height;
                $thumb_width = floor($src_width * ($max_height / $src_height));
            } else {
                $thumb_width = $max_height;
                $thumb_height = $max_height;
            }

            if (!@$destImage = imagecreatetruecolor($thumb_width, $thumb_height)) {
                return false;
            }

            if (!@imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $thumb_width, $thumb_height, $src_width, $src_height)) {
                return false;
            }
            break;

        case '1': // thumbnail is free - image cropped
            $ratio_thumb = $max_width / $max_height;
            $ratio_orig = $src_width / $src_height;

            if ($ratio_thumb > $ratio_orig) {
                $new_width = $max_width;
                $new_height = $max_width / $ratio_orig;
            } else {
                $new_width = $max_height * $ratio_orig;
                $new_height = $max_height;
            }

            $x_mid = $new_width / 2; //horizontal middle
            $y_mid = $new_height / 2; //vertical middle
            $process = imagecreate(round($new_width), round($new_height));
            if (!imagecopyresampled($process, $srcImage, 0, 0, 0, 0, $new_width, $new_height, $src_width, $src_height)) {
                return false;
            }

            $destImage = imagecreatetruecolor($max_width, $max_height);

            if (!imagecopyresampled($destImage, $process, 0, 0, ($x_mid - ($max_width / 2)), ($y_mid - ($max_height / 2)), $max_width, $max_height, $max_width, $max_height)) {
                return false;
            }
            @imagedestroy($process);

            break;
    } // end switch
    @imagedestroy($srcImage);
    if (function_exists("imagefilter") && (getMemoryCheck($max_width * $max_height))) imagefilter($destImage, IMG_FILTER_SMOOTH, $filtersmooth);
    // ------------------------
    switch (strtolower($imgtype)) {
        case "image/jpeg":
        case "image/jpg":
        case "image/pjpeg":
        case "image/bmp":

            if (!@imagejpeg($destImage, $thumbnail . ".jpg", $quality)) {
                return false;
            }
            break;
        case "image/gif":
            if (!@imagegif($destImage, $thumbnail . ".gif", $quality)) {
                return false;
            }
            break;
        case "image/png":
        case "image/x-png":
            if (!@imagepng($destImage, $thumbnail . ".png")) {
                return false;
            }
            break;
    }
    @imagedestroy($destImage);
    return true;
}

function create_cropped_image($original, $thumbnail, $max_width, $max_height, $quality = 80)
{
    if (!is_file($original)) return false;

    $filtersmooth = -15; // -15
    list ($src_width, $src_height, $type, $w) = getimagesize($original);
    $imgtype = image_type_to_mime_type($type);

    switch ($imgtype) {
        case "image/gif":
            // "Image is a gif";
            if (!$srcImage = @imagecreatefromgif($original)) {
                return false;
            }
            break;
        case "image/jpeg":
        case "image/jpg":
        case "image/pjpeg":
            // "Image is a jpeg";
            if (!$srcImage = @imagecreatefromjpeg($original)) {
                return false;
            }
            break;
        case "image/png":
        case "image/x-png":
            // "Image is a png";
            if (!$srcImage = @imagecreatefrompng($original)) {
                return false;
            }
            break;
        case "image/bmp":
            // "Image is a bmp";
            if (!$srcImage = @imagecreatefrombmp($original)) {
                return false;
            }
            break;
    }

    $ratio_thumb = $max_width / $max_height;
    $ratio_orig = $src_width / $src_height;

    if ($ratio_thumb > $ratio_orig) {
        $new_width = $max_width;
        $new_height = $max_width / $ratio_orig;
    } else {
        $new_width = $max_height * $ratio_orig;
        $new_height = $max_height;
    }

    $x_mid = $new_width / 2; //horizontal middle
    $y_mid = $new_height / 2; //vertical middle
    $process = imagecreatetruecolor(round($new_width), round($new_height));
    if (!@imagecopyresampled($process, $srcImage, 0, 0, 0, 0, $new_width, $new_height, $src_width, $src_height)) {
        return false;
    }

    $destImage = imagecreatetruecolor($max_width, $max_height);

    if (!@imagecopyresampled($destImage, $process, 0, 0, ($x_mid - ($max_width / 2)), ($y_mid - ($max_height / 2)), $max_width, $max_height, $max_width, $max_height)) {
        return false;
    }
    @imagedestroy($process);

    if (function_exists("imagefilter")) @imagefilter($destImage, IMG_FILTER_SMOOTH, $filtersmooth);
    @imagedestroy($srcImage);
    // ------------------------
    if (trim($thumbnail) == "") {
        // direct output
        // eventuell vorhandenen Ausgabepuffer loeschen
        while (ob_get_length()) {
            ob_end_clean();
        }
        // $_SERVER['HTTP_ACCEPT_ENCODING'] = 'none';
        header("HTTP/1.1 200 OK");
        // Die Content-Type-Kopfzeile senden, in diesem Fall immer image/jpeg
        header('Content-Type: image/jpeg');
        @imagejpeg($destImage);
        // @imagedestroy($destImage);
        exit;
    } else {
        // save as file
        if (!@imagejpeg($destImage, $thumbnail . ".jpg", $quality)) {
            return false;
        }
        @imagedestroy($destImage);
        return true;
    }
}
// watermarkfunction
function imgwatermark (&$dst_image, $src_image, $dst_w, $dst_h, $src_w, $src_h, $position = 'bottom-left')
{
    imagealphablending($dst_image, true);
    imagealphablending($src_image, true);
    imagecolorallocatealpha($src_image, 187, 187, 187, 50);
    if ($position == 'random') {
        $position = rand(1, 8);
    }
    switch ($position) {
        case 'top-right':
        case 'right-top':
        case 1:
            imagecopy($dst_image, $src_image, ($dst_w - $src_w), 0, 0, 0, $src_w, $src_h);
            break;
        case 'top-left':
        case 'left-top':
        case 2:
            imagecopy($dst_image, $src_image, 0, 0, 0, 0, $src_w, $src_h);
            break;
        case 'bottom-right':
        case 'right-bottom':
        case 3:
            imagecopy($dst_image, $src_image, ($dst_w - $src_w), ($dst_h - $src_h), 0, 0, $src_w, $src_h);
            break;
        case 'bottom-left':
        case 'left-bottom':
        case 4:
            imagecopy($dst_image, $src_image, 0, ($dst_h - $src_h), 0, 0, $src_w, $src_h);
            break;
        case 'center':
        case 5:
            imagecopy($dst_image, $src_image, (($dst_w / 2) - ($src_w / 2)), (($dst_h / 2) - ($src_h / 2)), 0, 0, $src_w, $src_h);
            break;
        case 'top':
        case 6:
            imagecopy($dst_image, $src_image, (($dst_w / 2) - ($src_w / 2)), 0, 0, 0, $src_w, $src_h);
            break;
        case 'bottom':
        case 7:
            imagecopy($dst_image, $src_image, (($dst_w / 2) - ($src_w / 2)), ($dst_h - $src_h), 0, 0, $src_w, $src_h);
            break;
        case 'left':
        case 8:
            imagecopy($dst_image, $src_image, 0, (($dst_h / 2) - ($src_h / 2)), 0, 0, $src_w, $src_h);
            break;
        case 'right':
        case 9:
            imagecopy($dst_image, $src_image, ($dst_w - $src_w), (($dst_h / 2) - ($src_h / 2)), 0, 0, $src_w, $src_h);
            break;
    }
    return $dst_image;
}

function imagecreatefrombmp($p_sFile)
{
    // Load the image into a string
    $file = fopen($p_sFile, "rb");
    $read = fread($file, 10);
    while (!feof($file) && ($read <> ""))
    $read .= fread($file, 1024);

    $temp = unpack("H*", $read);
    $hex = $temp[1];
    $header = substr($hex, 0, 108);
    // Process the header
    // Structure: http://www.fastgraph.com/help/bmp_header_format.html
    if (substr($header, 0, 4) == "424d") {
        // Cut it in parts of 2 bytes
        $header_parts = str_split($header, 2);
        // Get the width        4 bytes
        $width = hexdec($header_parts[19] . $header_parts[18]);
        // Get the height        4 bytes
        $height = hexdec($header_parts[23] . $header_parts[22]);
        // Unset the header params
        unset($header_parts);
    }
    // Define starting X and Y
    $x = 0;
    $y = 1;
    // Create newimage
    $image = imagecreatetruecolor($width, $height);
    // Grab the body from the image
    $body = substr($hex, 108);
    // Calculate if padding at the end-line is needed
    // Divided by two to keep overview.
    // 1 byte = 2 HEX-chars
    $body_size = (strlen($body) / 2);
    $header_size = ($width * $height);
    // Use end-line padding? Only when needed
    $usePadding = ($body_size > ($header_size * 3) + 4);
    // Using a for-loop with index-calculation instaid of str_split to avoid large memory consumption
    // Calculate the next DWORD-position in the body
    for ($i = 0;$i < $body_size;$i += 3) {
        // Calculate line-ending and padding
        if ($x >= $width) {
            // If padding needed, ignore image-padding
            // Shift i to the ending of the current 32-bit-block
            if ($usePadding)
                $i += $width % 4;
            // Reset horizontal position
            $x = 0;
            // Raise the height-position (bottom-up)
            $y++;
            // Reached the image-height? Break the for-loop
            if ($y > $height)
                break;
        }
        // Calculation of the RGB-pixel (defined as BGR in image-data)
        // Define $i_pos as absolute position in the body
        $i_pos = $i * 2;
        $r = hexdec($body[$i_pos + 4] . $body[$i_pos + 5]);
        $g = hexdec($body[$i_pos + 2] . $body[$i_pos + 3]);
        $b = hexdec($body[$i_pos] . $body[$i_pos + 1]);
        // Calculate and draw the pixel
        $color = imagecolorallocate($image, $r, $g, $b);
        imagesetpixel($image, $x, $height - $y, $color);
        // Raise the horizontal position
        $x++;
    }
    // Unset the body / free the memory
    unset($body);
    // Return image-object
    return $image;
}
// -------------------------------------------------------------------------------------------------------------------------------
function create_pngthumb($original, $thumbnail, $max_width, $max_height, $quality)
{
    global $WSCFG;

    if (!is_file($original)) return false;

    list ($src_width, $src_height, $type, $w) = getimagesize($original);

    if (!$srcImage = @imagecreatefrompng($original)) {
        return false;
    }
    // image resizes to natural height and width
    // width
    $thumb_width = $max_width;
    $thumb_height = floor($src_height * ($max_width / $src_width));

    if (!@$destImage = imagecreatetruecolor($thumb_width, $thumb_height)) {
        return false;
    }
    imagealphablending($destImage, true);
    imagesavealpha($destImage, true);
    imagefill($destImage, 0, 0, imagecolorallocatealpha($destImage, 255, 255, 255, 127));

    if (!@imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $thumb_width, $thumb_height, $src_width, $src_height)) {
        return false;
    }

    @imagedestroy($srcImage);

    if (!@imagepng($destImage, $thumbnail)) {
        return false;
    }
    @imagedestroy($destImage);
    return true;
}
// $src = imagecreatefrompng('png-transparent.png');
// $dst = imagecreatetruecolor(100,100);
// //sage dem bild, dass es mit alpha kanal arbeiten soll
// imagesavealpha($dst,true);
// //fuelle den hintergrund mit voll transparentem schwarz
// imagefill( $dst, 0, 0, imagecolorallocatealpha( $dst, 0, 0, 0, 127 ) );
// imagecopyresampled($dst,$src,0,0,0,0,100,100,200,200);
// if (!headers_sent()) {
// header("Content-type: image/png");
// imagepng($dst);
function create_jpgThumb($original, $thumbnail, $max_width, $max_height, $quality)
{
    global $WSCFG;

    if (!is_file($original)) return false;

    list ($src_width, $src_height, $type, $w) = getimagesize($original);

    if (!$srcImage = @imagecreatefromjpeg($original)) {
        return false;
    }
    // image resizes to natural height and width
    // width
    $thumb_width = $max_width;
    $thumb_height = floor($src_height * ($max_width / $src_width));

    if (!@$destImage = imagecreatetruecolor($thumb_width, $thumb_height)) {
        return false;
    }

    if (!@imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $thumb_width, $thumb_height, $src_width, $src_height)) {
        return false;
    }

    @imagedestroy($srcImage);

    if (!@imagejpeg($destImage, $thumbnail, $quality)) {
        return false;
    }
    @imagedestroy($destImage);
    return true;
}
// Pfadfunktionen
// Dateierweiterung auslesen
function get_file_ext($filename)
{
    return strtolower(substr(strrchr($filename, '.'), 1));
}

function IsDir($dateipfad, $file_ext)
{
    $handle = opendir($dateipfad); // Oeffnet ein Unterverzeichnis mit dem Namen $dateipfad
    $output = "";
    while ($file = readdir($handle)) { // Verzeichnis lesen
        if ($file != "." && $file != "..") { // Hoehere Verzeichnisse nicht anzeigen!
            $testfile = "$dateipfad/$file";

            if (is_dir("$testfile")) { // testen ob ein Verzeichnis
                $output .= '<a href="index.php?"><img src="myicons/dir.gif" border="0"></a><br /><B>' . $testfile . '</b><br />';
            }
        }
        return $output;
    }

    closedir($handle); // Verzeichnis schlie&szlig;en
}

function IsFile($dateipfad, $file_ext)
{
    $handle = opendir($dateipfad); // Oeffnet ein Unterverzeichnis mit dem Namen $dateipfad
    $output = "";
    while ($file = readdir($handle)) { // Verzeichnis lesen
        if ($file != "." && $file != "..") { // Hoehere Verzeichnisse nicht anzeigen!
            $testfile = "$dateipfad/$file";

            if (!is_dir("$testfile")) { // testen ob ein Verzeichnis
                foreach($file_ext as $key => $value) {
                    $ext = ws_get_file_ext($file);
                    if ($ext == $key) {
                        $output .= '<img src="' . $value . '"> ' . $file . '<br />';
                    }
                }
            }
        }
        return $output;
    }

    closedir($handle); // Verzeichnis schlie&szlig;en
}

function addCaptcha2 ()
{
    // captcha_java.php, bzw. den ganzen Captcha Ordner gibets nicht mehr
    global $prefix, $module_name, $WSFG;

    $captcha_object = load_class('Captcha', 'commentson');
    $captcha_object->set_active($active);

    echo '<br /><center>' . $captcha_object->image() . '<br />'
     . _CAPTCHAINSERT . '<br />' . $captcha_object->inputfield() . '<br /><br />'
     . '' . $captcha_object->reloadbutton() . '</center>';
}

function addCaptcha ($active = true,$ffieldname='commentson')
{
    

    $captcha_object = load_class('Captcha',$ffieldname);
    $captcha_object->set_active($active);

    echo "<div style=\"text-align:center; width:100% \">";
    echo '<p>' . $captcha_object->image() . '</p>';
    echo "<p>" . $captcha_object->caption() . " </p><p>" . $captcha_object->inputfield() . "</p>";
    echo "<p>" . $captcha_object->reloadbutton() . "</p>";
    echo "</div>";
}

function IsCaptchaok($active = true, $ffieldname='commentson')
{
        

        $captcha_object = load_class('Captcha', $ffieldname);
		return $captcha_object->check($_POST, 'captcha');
		
        if (!$captcha_object->check($_POST, 'captcha')) {
            return false;
        } else {
            return true;
        }
}

function title_replace($title) // Belndet Sonderzeichen aus
{
    $sonderzeichen = array(":", "|", "§", "%", "&", "/", "(", ")", "=", "$", "#", "'", "*", "+", "~", ";", "\\", "}", "]", "[", "{", "@", "€", "\"", "!", "µ", "<", ">");

    $title = str_replace($sonderzeichen, "", $title);

    return $title;
}

function getGetUser($uid = 0)
{
    // die $bgcolor Variablen sind unnötig
    // ist das wirklich so gewollt?
    // mxGetUserData() und mxGetAdminData() liefern die Daten des aktuell
    // angemeldeten Admins bzw. Users.
    // Die Übergabe der $uid ist dadurch unnötig und wird ignoriert.
    global $prefix, $currentlang, $module_name, $WSCFG;
    $user = array();
    $ruser = array();
    if (MX_IS_USER) {
        // wirklich als User angemeldet
        $user = mxGetUserData($uid);
        $ruser['uname'] = $user['uname'];
        $ruser['email'] = $user['email'];
        $ruser['uid'] = $user['uid'];
    } else {
        if (MX_IS_ADMIN) {
            // Admin aber nicht als User angemeldet
            $user = mxGetAdminData();
            // Wenn nur Admin angemeldet ist, kann man, falls vergeben, den zugehörigen
            // User über user_uid ermitteln und so den korrekten USernamen verwenden
            if ($user['user_uid']) {
                $user = mxGetUserDataFromUid($user['user_uid']);
                $ruser['uname'] = $user['uname'];
                $ruser['email'] = $user['email'];
                $ruser['uid'] = $user['uid'];
            } else {
                $ruser['uname'] = $user['aid'];
                $ruser['email'] = $user['email'];
                $ruser['uid'] = 0;
            }
        } else {
            // kein User
            $ruser['uname'] = "";
            $ruser['email'] = "";
            $ruser['uid'] = 0;
        }
    }

    return $ruser;
}

/**
 * Gibt die Root URL der pragma-Installation zurück
 * Parameter : keine
 * Rückgabewert : string    z.Bsp: "http://www.yourdomain/path/"
 */

function pmxRootURL()
{
    // existierende Konstante und kein DS, sondern /
    return PMX_HOME_URL . '/';
}

/**
 * Abfragen der User-Gruppe des aktuellen Users.
 *
 * @staticvar mixed $myuserinfo Variable zum Speichern der Ergebnisse
 * @param bool $dummy_forceDB is deprecated and unused
 * @return mixed Liefert ein Array mit den Userdaten des aktuellen Users zurueck
 */

function mxGetUserGroup()
{
    // API Funktion vorhanden, pmxUserStored nicht verwenden
    if (MX_IS_USER) {
        $user = mxGetUserData();
        return $user['user_ingroup'];
    }
    return 0;
}

function GetUserData ($uid = 0)
{
    // API Funktion vorhanden, die bringt auch gleich alle berechneten Daten, wie Gruppe und Geburtstag mit ;-)
    return mxGetUserDataFromUid($uid);
}

function GetUserList ()
{
    // nur aktive Useraccounts berücksichtigen "user_stat=1"
    // ansonsten kommen die gelöschten und deaktivierten mit
    global $user_prefix;
    $userlist = array();
    $result = sql_query("SELECT * FROM {$user_prefix}_users WHERE user_stat=1");

    while ($userdata = sql_fetch_assoc($result)) {
        $userlist[$userdata['uid']] = $userdata;
    }
    return $userlist;
}
/* ermittelt maximalen Speicher */
function getMemoryMax()
{
    $memlimit = str_replace("M", "000000", ini_get('memory_limit'));
    $memlimit = floatval(str_replace("k", "000", $memlimit));
    if ($memlimit == 0)$memlimit = 100000000;
    return $memlimit;
}

function getMemoryFree()
{
    return floatval(getMemoryMax() - intval(memory_get_usage(true))) ;
}

function getMemoryCheck($bytesused)
{
    return ((floatval(getMemoryFree() - floatval($bytesused)) > 10000)?true:false) ;
}

?>