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
 *
 * based on: probe.php from http://www.a51dev.com
 */

error_reporting(0);

/* umbenannte Datei nicht ausführen */
if (basename(__file__) != 'pmx_check.php') {
    die('locked');
}

/* nur Grafik anzeigen */
if (isset($_GET['img']) && $_GET['img'] === 'sprite') {
    die(sprite());
}

/* Datenbank Einstellungen */
if (!file_exists('config.php')) {
    /* Database settings, for new install */
    define('DB_HOST', '');
    define('DB_USER', '');
    define('DB_PASS', '');
    define('DB_NAME', '');
} else {
    /* Database settings, for update */
    include('config.php');
    define('DB_HOST', $mxConf['dbhost']);
    define('DB_USER', $mxConf['dbuname']);
    define('DB_PASS', $mxConf['dbpass']);
    define('DB_NAME', $mxConf['dbname']);
}

/* pragmaMx Version, die getestet wird */
define('MX_SETUP_VERSION', 'pragmaMx&nbsp;2.2');

define("STATUS_OK", "ok");
define("STATUS_WARNING", "warning");
define("STATUS_ERROR", "error");
define("THIS_FILE", basename(__FILE__));

/* min. Voraussetzungen definieren */
// gut: http://www.oxid-esales.com/de/produkte/facts/oxid-eshop-community-edition/systemvoraussetzungen.html
$minvalues = array(/* minimale Versionen und Werte */
    'mysql' => '5.0.33',
    'php' => '5.4',
    'memlimit' => '33554432', // in byte = 32MB
    );

/* Sprache einstellen und Sprachkonstanten laden */
lang_define();

/* Werte und Umgebung auslesen */
$results = array();
$php_ok = validate_php($results);
$memory_ok = validate_memory_limit($results);
$extensions_ok = validate_extensions($results);
// $pdo_ok = validate_pdo($results); // noch nicht jetzt :-)
$dbresults = array();
$mysql_ok = check_db($dbresults);

?><!DOCTYPE html>
<html>
  <head>
    <title><?php echo _TITLE ?></title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <style type="text/css">
    <!--
body{background:#2D2D2D;color:#333;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:14px;line-height:20px;text-align:center;}
h1{font-size:39px;color:#799d24;text-align:center;border-bottom:1px solid #EBEBEB;padding:15px;text-transform:uppercase;text-shadow:0 1px 0 rgba(0,0,0,0.8);}
h2{font-size:32px;color:#494949;text-shadow:0 2px 0 rgba(0,0,0,0.1);}
h3{font-size:25px;color:#799D24;text-shadow:0 2px 0 rgba(0,0,0,0.1);}
h1,h2,h3{font-weight:700;line-height:40px;margin:15px 0;}
ul{list-style:none;margin:0 0 10px 40px;padding:0;}
.warning span,.error span,.ok span{font-weight:700;display:inline-block;margin:2px 0;padding:2px 4px;text-shadow:0 -1px 0 rgba(0,0,0,0.25);border-radius:3px;}
.warning span{background-color:#F89406;color:#FFF;}
.error span{background-color:#B94A48;color:#FFF;}
.ok span{background-color:#468847;color:#FFF;}
.warning .details,.error .details{display:block;font-size:13px;font-weight:400;padding:5px;text-shadow:none;}
.warning .details{background-color:#F6F6F6;color:#F89406;}
.error .details{background-color:#F6F6F6;color:#B94A48;}
.sprite{background:transparent url('<?php echo THIS_FILE ?>?img=sprite') no-repeat;display:block;float:left;margin-left:6px;}
.sprite.da{background-position:0 0;height:12px;width:18px;}
.sprite.de{background-position:0 -72px;height:12px;width:18px;}
.sprite.en{background-position:0 -24px;height:12px;width:18px;}
.sprite.fr{background-position:0 -48px;height:12px;width:18px;}
.sprite.tr{background-position:0 -96px;height:12px;width:18px;}
#lang{float:right;margin:15px 0;}
#verdict{border-color:rgba(0,0,0,0.2);border-radius:10px;border-style:solid;border-width:1px;box-shadow:0 1px 0 rgba(255,255,255,0.2);color:#FFF;margin-bottom:0;text-align:center;text-shadow:0 -1px 0 rgba(0,0,0,0.25);vertical-align:middle;font-size:160%;margin:20px 0;padding:20px;}
#verdict.all_ok{background-color:#497c00;}
#verdict.not_ok{background-color:#bc0000;}
#wrapper{background:#fff;border:10px solid #424242;border-radius:10px;box-shadow:0 0 10px 0 rgba(0,0,0,0.5);color:inherit;margin:25px auto 20px;padding:0 25px 25px;text-align:left;width:700px;}
    -->
    </style>
  </head>
  <body>
    <div id="wrapper">
      <p id="lang" title="<?php echo _SELECTLANG ?>">
        <a href="<?php echo THIS_FILE ?>?lang=de" class="sprite de"></a>
        <a href="<?php echo THIS_FILE ?>?lang=en" class="sprite en"></a>
        <a href="<?php echo THIS_FILE ?>?lang=fr" class="sprite fr"></a>
        <a href="<?php echo THIS_FILE ?>?lang=da" class="sprite da"></a>
        <a href="<?php echo THIS_FILE ?>?lang=tr" class="sprite tr"></a>
      </p>
      <h1><?php echo MX_SETUP_VERSION ?></h1>
      <h2><?php echo _ENVTEST ?></h2>
      <ul>
<?php foreach($results as $result) {
    echo '<li class="' . $result->status . '"><span>' . $result->status . '</span> - ' . $result->message . '</li>';
}

?>
      </ul>

      <h2><?php echo _DBTEST ?></h2>
    <?php if (!is_null($mysql_ok)) {

    ?>
      <ul>
        <?php foreach($dbresults as $result) {
        echo '<li class="' . $result->status . '"><span>' . $result->status . '</span> - ' . $result->message . '</li>';
    }

    ?>
      </ul>
    <?php } else {

    ?>
      <p><?php echo _DBTEST_ISOFF ?>:</p>
      <ul>
        <li>DB_HOST - <?php echo _DBTEST_HOST ?></li>
        <li>DB_USER - <?php echo _DBTEST_USER ?></li>
        <li>DB_PASS - <?php echo _DBTEST_PASS ?></li>
        <li>DB_NAME - <?php echo _DBTEST_NAME ?></li>
      </ul>
      <p><?php echo _DBTEST_SETTHEM ?></p>
    <?php }
if ($mysql_ok !== null) {
    if ($php_ok && $memory_ok && $extensions_ok && $mysql_ok) {

        ?>
      <p id="verdict" class="all_ok"><?php echo _TEST_ISOK ?></p>
<?php } else {

        ?>
      <p id="verdict" class="not_ok"><?php echo _TEST_ISNOTOK ?></p>

      <div id="legend">
      <h2><?php echo _LEGEND ?></h2>
        <ul>
          <li class="ok"><?php echo _LEGEND_OK ?></li>
          <li class="warning"><?php echo _LEGEND_WARN ?></li>
          <li class="error"><?php echo _LEGEND_ERR ?></li>
        </ul>
      </div>
<?php }
}
if ($php_ok && $memory_ok && $extensions_ok && $mysql_ok) {
    if (file_exists('setup') && file_exists('setup/index.php') && file_exists('setup/includes/functions_check.php')) {

        ?>
     <h3><big>&rArr;</big> <small><a href="./setup"><?php echo _GOTOSETUP ?></a></small></h3>
     <?php
    }
}

?>
    </div>
  </body>
</html>


<?php

/**
 * TestResult
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2013
 * @version $Id: pmx_check.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class TestResult {
    var $message;
    var $status;

    function __construct($message, $status = STATUS_OK)
    {
        $this->message = $message;
        $this->status = $status;
    }
}

/**
 * check_db()
 *
 * @param mixed $dbresults
 * @return
 */
function check_db(&$dbresults)
{
    global $minvalues;

    if (!(DB_HOST && DB_NAME)) {
        return null;
    }

    $dbresults = array();

    if ($connection = @mysql_connect(DB_HOST, DB_USER, DB_PASS)) {
        $dbresults[] = new TestResult(_DBTEST_CONNECT, STATUS_OK);

        if (mysql_select_db(DB_NAME, $connection)) {
            $dbresults[] = new TestResult(_DBTEST_SELECTED, STATUS_OK);

            $mysql_version = mysql_get_server_info($connection);

            if (version_compare($mysql_version, $minvalues['mysql']) >= 0) {
                $dbresults[] = new TestResult(sprintf(_DBTEST_VERSION, $mysql_version), STATUS_OK);
            } else {
                $dbresults[] = new TestResult(sprintf(_DBTEST_VERSIONOLD, $mysql_version), STATUS_ERROR);
                return false;
            }
        } else {
            $dbresults[] = new TestResult(sprintf(_DBTEST_SELECTFAIL, mysql_error()), STATUS_ERROR);
            return false;
        }
    } else {
        $dbresults[] = new TestResult(sprintf(_DBTEST_CONNECTFAIL, mysql_error()), STATUS_ERROR);
        return false;
    }

    return true;
}

/**
 * Validate PHP platform
 *
 * @param array $result
 */
function validate_php(&$results)
{
    global $minvalues;
    if (version_compare(PHP_VERSION, $minvalues['php']) == -1) {
        $results[] = new TestResult(sprintf(_ENVTEST_PHPFAIL, $minvalues['php'], PHP_VERSION), STATUS_ERROR);
        return false;
    } else {
        $results[] = new TestResult(sprintf(_ENVTEST_PHPOK, PHP_VERSION), STATUS_OK);
        return true;
    }
}

/**
 * Validate memory limit
 *
 * @param array $result
 */
function validate_memory_limit(&$results)
{
    global $minvalues;

    $memory_limit = php_config_value_to_bytes(ini_get('memory_limit'));

    $formatted_memory_limit = $memory_limit === -1 ? 'unlimited' : format_file_size($memory_limit);

    if ($memory_limit === -1 || $memory_limit >= $minvalues['memlimit']) {
        $results[] = new TestResult(sprintf(_ENVTEST_MEMOK, $formatted_memory_limit), STATUS_OK);
        return true;
    } else {
        $results[] = new TestResult(sprintf(_ENVTEST_MEMFAIL, format_file_size($minvalues['memlimit']), $formatted_memory_limit), STATUS_ERROR);
        return false;
    }
}

/**
 * Validate PHP extensions
 *
 * @param array $results
 */
function validate_extensions(&$results)
{
    $ok = true;

    $required_extensions = array(/* benötigte */
        'mysql',
        'pcre',
        'session',
        'mbstring', // => _EXTTEST_MB,
        'gd', // => _EXTTEST_GD,
        'iconv', // => _EXTTEST_ICONV,
        'json',
		'pdo',
        );

    $recommended_extensions = array(/* empfohlene */
        // 'gd' => _EXTTEST_GD,
        // 'mbstring' => _EXTTEST_MB,
        // 'imap' => _EXTTEST_IMAP,
        'xml' => _EXTTEST_XML,
        'curl' => _EXTTEST_CURL,
        'tidy' => _EXTTEST_TIDY,
        //'pdo' => _EXTTEST_PDO,
        'zip' => _EXTTEST_ZIP,
        );

    foreach($required_extensions as $required_extension) {
        if (extension_loaded($required_extension)) {
            $results[] = new TestResult(sprintf(_EXTTEST_REQFOUND, $required_extension), STATUS_OK);
        } else {
            $results[] = new TestResult(sprintf(_EXTTEST_REQFAIL, $required_extension), STATUS_ERROR);
            $ok = false;
        }
    }

    foreach($recommended_extensions as $recommended_extension => $recommended_extension_desc) {
        if (extension_loaded($recommended_extension)) {
            $results[] = new TestResult(sprintf(_EXTTEST_RECFOUND, $recommended_extension), STATUS_OK);
        } else {
            $results[] = new TestResult(sprintf(_EXTTEST_RECNOTFOUND, $recommended_extension, $recommended_extension_desc), STATUS_WARNING);
        }
    }
    return $ok;
}

/**
 * Validate available PDO drivers
 *
 * @param array $result
 */
function validate_pdo(&$results)
{
    if (!extension_loaded('pdo')) {
        $results[] = new TestResult(sprintf(_EXTTEST_REQFAIL, 'pdo'), STATUS_ERROR);
        return false;
    }

    $usable_drivers = array('mysql');
    $drivers = PDO::getAvailableDrivers();
    $available_drivers = array_intersect($drivers, $usable_drivers);

    if (!$available_drivers) {
        $drivers = implode(',', $usable_drivers);
        $results[] = new TestResult(sprintf(_PDOTEST_FAIL, $drivers), STATUS_ERROR);
        return false;
    }

    foreach ($available_drivers as $driver) {
        $results[] = new TestResult(sprintf(_PDOTEST_OK, $driver), STATUS_OK);
    }
    return true;
}

/**
 * Convert filesize value from php.ini to bytes
 *
 * Convert PHP config value (2M, 8M, 200K...) to bytes. This function was taken from PHP documentation. $val is string
 * value that need to be converted
 *
 * @param string $val
 * @return integer
 */
function php_config_value_to_bytes($val)
{
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch ($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return (integer) $val;
}

/**
 * Format filesize
 *
 * @param string $value
 * @return string
 */
function format_file_size($value)
{
    $data = array('TB' => 1099511627776,
        'GB' => 1073741824,
        'MB' => 1048576,
        'kb' => 1024,
        );

    $value = (integer) $value;
    foreach($data as $unit => $bytes) {
        $in_unit = $value / $bytes;
        if ($in_unit > 0.9) {
            return trim(trim(number_format($in_unit, 2), '0'), '.') . $unit;
        }
    }
    return $value . 'b';
}

/**
 * check_type_support()
 * Check version of GDLib
 *
 * @return
 */
function check_type_support()
{
    // GDlib installiert?
    if (!function_exists('gd_info')) {
        return false;
    }
    $gd_info = gd_info();
    if (!is_array($gd_info) || !isset($gd_info['GD Version'])) {
        return false;
    }
    $gd_info['GD_Version'] = @preg_replace('#[^0-9.]#', '', $gd_info['GD Version']);
    return $gd_info;
}

/**
 * Get the language code from the browser
 * based on:
 * http://burian.appfield.net/entwicklung/php-mysql/php-browsersprache-fur-mehrsprachige-anwendungen-ermitteln.htm
 *
 * @param array $ Allowed Languages "array('de','en')"
 * @param string $ Default lang
 * @param string $ Language string from HTTP-Header
 * @param bool $ Strict-Mode
 * @return array |int Data as array or null
 */
function getBrowserLanguage($arrAllowedLanguages, $strDefaultLanguage, $strLangVariable = null, $boolStrictMode = true)
{
    $req = array_merge($_COOKIE, $_GET);
    if (isset($req['lang'])) {
        if (strlen($req['lang']) == 2) {
            setcookie('lang', $req['lang']);
            return strtolower($req['lang']);
        }
        foreach ($arrAllowedLanguages as $strValue) {
            if (preg_match('/^' . $strValue . '\-/i', $req['lang'])) {
                setcookie('lang', $req['lang']);
                return strtolower($strValue);
            }
        }
    }
    if (!is_array($arrAllowedLanguages)) {
        if (strpos($arrAllowedLanguages, ';')) {
            $array = explode(';', $arrAllowedLanguages);
            $arrAllowedLanguages = $array;
        }
    }
    if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        return $arrAllowedLanguages[0];
    }
    if ($strLangVariable === null) $strLangVariable = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    if (empty($strLangVariable)) return $strDefaultLanguage;
    $arrAcceptedLanguages = preg_split('/,\s*/', $strLangVariable);
    $strCurrentLanguage = $strDefaultLanguage;
    $intCurrentQ = 0;
    foreach ($arrAcceptedLanguages as $arrAcceptedLanguage) {
        $boolResult = preg_match ('/^([a-z]{1,8}(?:-[a-z]{1,8})*)' . '(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i', $arrAcceptedLanguage, $arrMatches);
        if (!$boolResult) continue;
        $arrLangCode = explode ('-', $arrMatches[1]);
        if (isset($arrMatches[2]))
            $intLangQuality = (float)$arrMatches[2];
        else
            $intLangQuality = 1.0;
        while (count ($arrLangCode)) {
            if (!is_array($arrAllowedLanguages)) $arrAllowedLanguages = array($arrAllowedLanguages);
            if (in_array (strtolower (join ('-', $arrLangCode)), $arrAllowedLanguages)) {
                if ($intLangQuality > $intCurrentQ) {
                    $strCurrentLanguage = strtolower (join ('-', $arrLangCode));
                    $intCurrentQ = $intLangQuality;
                    break;
                }
            }
            if ($boolStrictMode) break;
            array_pop ($arrLangCode);
        }
    }
    return $strCurrentLanguage;
}

/**
 * sprite()
 * PNG-Grafik für Flaggen ausgeben
 *
 * @return
 */
function sprite()
{
    header('Content-type: image/png');
    echo base64_decode('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'); // Das Bild an sich
    exit;
}

/**
 * lang_define()
 * Sprachkonstanten erstellen
 *
 * @param string $lang
 * @return
 */
function lang_define()
{
    // http://burian.appfield.net/entwicklung/php-mysql/php-browsersprache-fur-mehrsprachige-anwendungen-ermitteln.htm
    $lang = getBrowserLanguage(array('de', 'en', 'tr'), 'de');

    switch ($lang) {
        case 'en':

            define("_TITLE", "" . MX_SETUP_VERSION . " environment test");
            define("_ENVTEST", "Environment test");
            define("_SELECTLANG", "Please select a language");
            define("_DBTEST", "Database test");
            define("_DBTEST_ISOFF", "Database test is <strong>turned off</strong>. To turn it On, please open <i>(" . THIS_FILE . ")</i> in your favorite text editor and set DB_XXXX connection parameters in database section at the beginning of the file:");
            define("_DBTEST_HOST", "Address of your MySQL server (usually localhost)");
            define("_DBTEST_USER", "Username that is used to connect to the server");
            define("_DBTEST_PASS", "User's password");
            define("_DBTEST_NAME", "Name of the database you are connecting to");
            define("_DBTEST_SETTHEM", "Once these settings are set, <i>(" . THIS_FILE . ")</i> will check if your database meets the system requirements.");
            define("_TEST_ISOK", "OK, this system can run " . MX_SETUP_VERSION . "");
            define("_TEST_ISNOTOK", "This system does not meet " . MX_SETUP_VERSION . " system requirements");
            define("_LEGEND", "Legend");
            define("_LEGEND_OK", "<span>ok</span> - All OK");
            define("_LEGEND_WARN", "<span>warning</span> - Not a deal breaker, but it's recommended to have this installed for some features to work");
            define("_LEGEND_ERR", "<span>error</span> - " . MX_SETUP_VERSION . " require this feature and can't work without it");
            define("_DBTEST_CONNECT", "Connected to selected database");
            define("_DBTEST_SELECTED", "selected Database found");
            define("_DBTEST_VERSION", "MySQL version is: %s");
            define("_DBTEST_VERSIONOLD", "Your MySQL version is. %s. We recommend upgrading to at least MySQL5!");
            define("_DBTEST_SELECTFAIL", "Failed to select database. MySQL said: %s");
            define("_DBTEST_CONNECTFAIL", "Failed to connect to database. MySQL said: %s");
            define("_ENVTEST_PHPFAIL", "Minimum PHP version required in order to run " . MX_SETUP_VERSION . " is PHP %s. Your PHP version: %s");
            define("_ENVTEST_PHPOK", "Your PHP version is: %s");
            define("_ENVTEST_MEMOK", "Your memory limit is: %s");
            define("_ENVTEST_MEMFAIL", "Your memory is too low to complete the installation. Minimal value is %s, and you have it set to: %s");
            define("_EXTTEST_REQFOUND", "Required extension '%s' found");
            define("_EXTTEST_REQFAIL", "Extension '%s' is required in order to run " . MX_SETUP_VERSION . ".");
            define("_EXTTEST_GD", "GD is used for image manipulation. Without it, system is not able to create thumbnails for files or manage avatars, logos and project icons");
            define("_EXTTEST_MB", "MultiByte String is used for work with Unicode. Without it, system may not split words and string properly and you can have weird question mark characters in Recent Activities for example");
            // define("_EXTTEST_ICONV", "Iconv is used for character set conversion. Without it, system is a bit slower when converting different character set");
            define("_EXTTEST_IMAP", "IMAP is used to connect to POP3 and IMAP servers. Without it, Incoming Mail module will not work");
            define("_EXTTEST_CURL", "This functions optimizes the access to external data.");
            define("_EXTTEST_TIDY", "When TIDY extension is active, the HTML output will be validated automatically. This can speed up the page layout in the browser and make the website W3C compliant.");
            define("_EXTTEST_XML", "The XML extension is needed among others for the creation of RSS feeds.");
            define("_EXTTEST_RECFOUND", "Recommended extension '%s' found");
            define("_EXTTEST_RECNOTFOUND", "Extension '%s' was not found. <span class=\"details\">%s</span>");
            define("_GOTOSETUP", "Install " . MX_SETUP_VERSION . " now!");

            define("_PDOTEST_OK", "PDO database driver (% s) available");
            define("_PDOTEST_FAIL", "It found no useful PDO database driver (for example,%s)");
            define("_EXTTEST_PDO", "PDO extension will be the future standard database driver for pragmaMx. The extension should be available as soon as possible.");
            define("_EXTTEST_ZIP", "The Zip functionality is used by some add-on modules and should be available.");
            break;

        case 'tr':
            define("_TITLE", "" . MX_SETUP_VERSION . " ortamında test");
            define("_ENVTEST", "Çevre testi");
            define("_SELECTLANG", "Lütfen bir dil seçin");
            define("_DBTEST", "Veritabanı testi");
            define("_DBTEST_ISOFF", "Veritabanı testi <strong>kapalıdır</strong>. Açmak için, lütfen favori metin editörünüz ile <i>(" . THIS_FILE . ")</i> dosyasını açıp başında veritabanı bölümünde <i>(DB_XXXX)</i> bağlantı parametrelerini ayarlayın:");
            define("_DBTEST_HOST", "MySQL sunucu adresi (genellikle 'localhost')");
            define("_DBTEST_USER", "Sunucusuna bağlanmak için kullanılan kullanıcı adı");
            define("_DBTEST_PASS", "Kullanıcı şifresi");
            define("_DBTEST_NAME", "Bağlandığınız veritabanının adı");
            define("_DBTEST_SETTHEM", "Bu ayarlar tanımlandıktan sonra, <i>(" . THIS_FILE . ")</i> veritabanı sistem gereksinimlerini karşılayıp karşılamadığını kontrol eder.");
            define("_TEST_ISOK", "Tamam, bu sistem " . MX_SETUP_VERSION . " çalıstırabilir");
            define("_TEST_ISNOTOK", "Bu sistem " . MX_SETUP_VERSION . " Sistem gereksinimlerini karşılamıyor");
            define("_LEGEND", "Başlık");
            define("_LEGEND_OK", "<span>Tamam</span> - Hepsi Tamam");
            define("_LEGEND_WARN", "<span>Uyarı</span> - Bu özellik olmadan " . MX_SETUP_VERSION . " bazı işlevler kullanılabilir değil.");
            define("_LEGEND_ERR", "<span>Hata</span> - " . MX_SETUP_VERSION . "  bu özelliği gerektir ve o olmadan çalışamaz");
            define("_DBTEST_CONNECT", "Veritabanı bağlantısı kuruldu.");
            define("_DBTEST_SELECTED", "Seçili veritabanı bulundu");
            define("_DBTEST_VERSION", "MySQL sürümü: %s");
            define("_DBTEST_VERSIONOLD", "MySQL sürümü. %s. Biz en az MySQL5 yükseltme öneririz!");
            define("_DBTEST_SELECTFAIL", "Veritabanını seçmek için başarısız oldu. MySQL dedi: %s");
            define("_DBTEST_CONNECTFAIL", "Veritabanına bağlanamadı. MySQL dedi: %s");
            define("_ENVTEST_PHPFAIL", "" . MX_SETUP_VERSION . " çalıstırmak için minimum PHP %s sürümü gereklidir. PHP sürümü: %s");
            define("_ENVTEST_PHPOK", "PHP sürümü: %s");
            define("_ENVTEST_MEMOK", "PHP Hafıza sınırı: %s");
            define("_ENVTEST_MEMFAIL", "PHP Hafızanız " . MX_SETUP_VERSION . " Yüklemeyi tamamlamak için çok düşük. Minimal değer %s, ve buna ayarlanmış: %s");
            define("_EXTTEST_REQFOUND", "Gerekli uzantı '%s' bulundu");
            define("_EXTTEST_REQFAIL", "" . MX_SETUP_VERSION . " çalıştırmak için uzantı '%s' gereklidir");
            define("_EXTTEST_GD", "GD resim işleme için kullanılır. Onsuz, sistemin dosyaları küçük resim olarak, avatarlar, logolar ve proje simgeleri oluşturması veya yönetmesi mümkün değildir");
            define("_EXTTEST_MB", "Baytlı dize Unicode ile çalışması için kullanılır. Onsuz, sistemin düzgün kelime ve dize bölme yapmayabilir ve örneğin yeni etkinlikler garip soru işareti karakter ile olabilir");
            // define("_EXTTEST_ICONV", "Iconv karakter kümesi dönüstürme için kullanilir. Onsuz, sistem farkli karakter kümesi dönüstürme esnasinda biraz daha yavas");
            define("_EXTTEST_IMAP", "IMAP, POP3 ve IMAP sunucularına bağlanmak için kullanılır. Onsuz, Gelen Posta modülü çalışmaz");
            define("_EXTTEST_CURL", "Dış veri erişimi geliştirmek için CURL fonksiyonları.");
            define("_EXTTEST_TIDY", "TIDY uzantısı etkin olduğunda, HTML çıktısı otomatik onaylanacak. Bu tarayıcıda sayfa düzenini hızlandırabilir ve web sitesini W3C uyumlu yapar.");
            define("_EXTTEST_XML", "XML uzantısı RSS beslemeleri oluşturulması için diğerleri arasında gereklidir.");
            define("_EXTTEST_RECFOUND", "Önerilen uzantı '%s' bulundu");
            define("_EXTTEST_RECNOTFOUND", "Uzantı '%s' bulunamadı. <span class=\"details\">%s</span>");
            define("_GOTOSETUP", "" . MX_SETUP_VERSION . " şimdi yükleyin");
            define("_PDOTEST_OK", "PDO veritabanı sürücüsü (%s) kullanılabilir");
            define("_PDOTEST_FAIL", "Kullanılabilir bir PDO veritabanı sürücüsü (örneğin %s) bulunamadı");
            define("_EXTTEST_PDO", "PDO eklentisi gelecekte pragmaMx için varsayılan veritabanı sürücüsü olacak. Eklenti kısa sürede mevcut olmalıdır.");
            define("_EXTTEST_ZIP", "Zip işlevselliği bazı eklenti modülleri tarafından kullanılmaktadır ve mevcut olmalıdır.");
            break;
			
        case 'fr':
            define("_TITLE", "" . MX_SETUP_VERSION . " test d'environnement");
            define("_ENVTEST", "Test d'environnement");
            define("_SELECTLANG", "Veuillez sélectionner une langue");
            define("_DBTEST", "Test de la base de données");
            define("_DBTEST_ISOFF", "Le test de la base de données est <strong>désactivé</strong>. Pour l'activer, veuillez ouvrir <i>(" . THIS_FILE . ")</i> avec votre éditeur de texte favoris et réglez les paramètres de la base données DB_XXXX au début du fichier:");
            define("_DBTEST_HOST", "Adresse de votre serveur MySQL (généralement localhost)");
            define("_DBTEST_USER", "Nom d'utilisateur pour la connection au serveur");
            define("_DBTEST_PASS", "Mot de passe utilisateur");
            define("_DBTEST_NAME", "Nom de la base de données");
            define("_DBTEST_SETTHEM", "Une fois que ces paramètres sont définis, <i>(" . THIS_FILE . ")</i> vérifiera si votre base de données est conforme aux exigences du système.");
            define("_TEST_ISOK", "OK, le système peut faire fonctionner " . MX_SETUP_VERSION . "");
            define("_TEST_ISNOTOK", "Votre système ne répond pas aux exigences requises pour " . MX_SETUP_VERSION . "");
            define("_LEGEND", "Légende");
            define("_LEGEND_OK", "<span>ok</span> - Tout est bon");
            define("_LEGEND_WARN", "<span>warning</span> - Pas indispensable, mais recommandé pour profiter de certaines fonctionnalités");
            define("_LEGEND_ERR", "<span>error</span> - " . MX_SETUP_VERSION . " en a besoin et ne peut pas fonctionner sans");
            define("_DBTEST_CONNECT", "Connection avec la base de données");
            define("_DBTEST_SELECTED", "La base de données sélectionnée a été trouvée");
            define("_DBTEST_VERSION", "Votre version MySQL est: %s");
            define("_DBTEST_VERSIONOLD", "Votre version MySQL est %s. Nous recommandons une mise à jour vers MySQL5 minimum!");
            define("_DBTEST_SELECTFAIL", "Impossible de sélectionner la base de donnnées. MySQL affiche: %s");
            define("_DBTEST_CONNECTFAIL", "Impossible de se connecter à la base de donnnées. MySQL affiche: %s");
            define("_ENVTEST_PHPFAIL", "La version PHP minimum requise pour faire fonctionner " . MX_SETUP_VERSION . " est PHP %s. Votre version PHP: %s");
            define("_ENVTEST_PHPOK", "Votre version PHP est: %s");
            define("_ENVTEST_MEMOK", "Votre limite de mémoire est: %s");
            define("_ENVTEST_MEMFAIL", "Votre mémoire est trop faible pour terminer l'installation. La valeur minimum est %s, et vous avez: %s");
            define("_EXTTEST_REQFOUND", "L'extension '%s' est présente");
            define("_EXTTEST_REQFAIL", "L'extension '%s' est requise pour faire fonctionner " . MX_SETUP_VERSION . ".");
            define("_EXTTEST_GD", "GD est utilisé pour la manipulation d'images. Sans cela, le système n'est pas capable de créer des vignettes pour les fichiers ou gérer les avatars, logos et icônes du projet.");
            define("_EXTTEST_MB", "Chaîne multi-octets est utilisé pour le travail avec Unicode. Sans cela, le système ne peut pas séparer les mots et les chaines correctement et vous pouvez avoir d'étranges caractères comme par exemple des points d'interrogations dans les activités récentes.");
            // define("_EXTTEST_ICONV", "Iconv est utilisé pour la conversion du jeu de caractères. Sans cela, le système est un peu plus lent lors de la conversion d'un jeu de caractères différent.");
            define("_EXTTEST_IMAP", "IMAP est utilisé pour se connecter aux serveurs POP3 et IMAP. Sans cela, le module de courrier entrant ne fonctionne pas.");
            define("_EXTTEST_CURL", "Cette fonction permet l'accès aux données externes.");
            define("_EXTTEST_TIDY", "Lorsque l'extension Tidy est active, la sortie HTML est validée automatiquement. Cela permet également d'accélérer la mise en page dans le navigateur et affiche le site conforme aux normes W3C.");
            define("_EXTTEST_XML", "L'extension XML est nécessaire notamment pour générer le flux RSS.");
            define("_EXTTEST_RECFOUND", "L'extension '%s' est présente");
            define("_EXTTEST_RECNOTFOUND", "L'extension '%s' est absente <span class=\"details\">%s</span>");
            define("_GOTOSETUP", "Installez le " . MX_SETUP_VERSION . " maintenant!");
            define("_PDOTEST_OK", "Pilote de base de données PDO (%s) est fonctionnel");
            define("_PDOTEST_FAIL", "Aucun pilote utilisable de base de données PDO (z.B. %s) trouvé");
            define("_EXTTEST_PDO", "L'extension PDO sera dans l'avenir le moteur de base de données par défaut pour le système. L'extension devrait être disponible dès que possible.");
            define("_EXTTEST_ZIP", "La fonctionnalité ZIP est utilisée par certains modules/add-on et devrait être disponible.");
            break;

        case 'da';
            define("_TITLE", "" . MX_SETUP_VERSION . " environment test");
            define("_ENVTEST", "Installations Tjek");
            define("_SELECTLANG", "Vælg venligst et sprog");
            define("_DBTEST", "Databasetesten");
            define("_DBTEST_ISOFF", "Databasetesten er deaktiveret. For at aktivere testen, åbne denne fil <i>(" . THIS_FILE . ")</i> ved hjælp en teksteditor og sæt database forbindelse parametre <i>(DB_XXXX)</i> entsprechend ein:");
            define("_DBTEST_HOST", "Adressen på MySQL serveren (normalt 'localhost')");
            define("_DBTEST_USER", "Brugernavnet anvendes til at forbinde til MySQL Server");
            define("_DBTEST_PASS", "Den tilsvarende brugeradgangskode");
            define("_DBTEST_NAME", "Navnet på database, hvor skal forbindes til");
            define("_DBTEST_SETTHEM", "Når disse parametre er angivet korrekt, kan <i>(" . THIS_FILE . ")</i> tjekke, om databasen opfylder systemkravene.");
            define("_TEST_ISOK", "Okay, på dette system bør " . MX_SETUP_VERSION . " køre korrekt.");
            define("_TEST_ISNOTOK", "Dette system opfylder ikke minimumsystemkravene for driften af " . MX_SETUP_VERSION . ".");
            define("_LEGEND", "Legende");
            define("_LEGEND_OK", "<span>OK</span> - Alt i orden");
            define("_LEGEND_WARN", "<span>Advarsel</span> - Uden denne funktion kan nogle funktioner af " . MX_SETUP_VERSION . " ikke bruges.");
            define("_LEGEND_ERR", "<span>Fejl</span> - Denne funktion kræves af" . MX_SETUP_VERSION . ".");
            define("_DBTEST_CONNECT", "Der er oprettet forbindelse til databasen.");
            define("_DBTEST_SELECTED", "valgte database blev fundet");
            define("_DBTEST_VERSION", "MySQL version er: %s");
            define("_DBTEST_VERSIONOLD", "Din MySQL version er: %s. Vi anbefaler, at du opdatere til den nyeste MySQL 5.x version!");
            define("_DBTEST_SELECTFAIL", "Fejl ved valg af databasen. MySQL besked: <br />%s");
            define("_DBTEST_CONNECTFAIL", "Forbindelsen til databasen mislykkedes. MySQL besked: <br />%s");
            define("_ENVTEST_PHPFAIL", "Den af " . MX_SETUP_VERSION . " påkrævede, minimum PHP version er %s. Din PHP version er: %s");
            define("_ENVTEST_PHPOK", "Din PHP version er: %s");
            define("_ENVTEST_MEMOK", "Din PHP hukommelse grænse er: %s");
            define("_ENVTEST_MEMFAIL", "Din PHP hukommelse grænse er for lav til " . MX_SETUP_VERSION . " installationen. Minimumværdien er %s.");
            define("_EXTTEST_REQFOUND", "Den påkrævede udvidelse '%s' eksisterer");
            define("_EXTTEST_REQFAIL", "Udvidelsen '%s' er nødvendig til drift af " . MX_SETUP_VERSION . ".");
            define("_EXTTEST_GD", "GD bruges til billedredigering. Uden denne udvidelse, kan systemet ikke oprette f.eks. miniaturer af billedfiler.");
            define("_EXTTEST_MB", "Multi byte streng bruges til håndtering af Unicode-tegn. Uden denne udvidelse, kan der muligvis være visningsfejl på visse specialtegn.");
            // define("_EXTTEST_ICONV", "Iconv wird teilweise zur Zeichensatz-Konvertierung verwendet.  Ohne diese Erweiterung kann es evtl. zu Anzeigefehlern bei bestimmten Sonderzeichen kommen.");
            define("_EXTTEST_IMAP", "IMAP bruges til at forbinde til POP3 og IMAP-servere.");
            define("_EXTTEST_CURL", "CURL-funktionen forbedrer adgang til eksterne data.");
            define("_EXTTEST_TIDY", "Når TIDY udvidelsen er aktiveret, kan HTML-output automatisk valideres. Dette kan fremskynde sidelayoutet i browseren og gør hjemmesiden W3C-kompatibel.");
            define("_EXTTEST_XML", "XML-udvidelse er blandt andet nødvendig til at generere RSS-feed.");
            define("_EXTTEST_RECFOUND", "Den anbefalede udvidelse '%s' eksisterer");
            define("_EXTTEST_RECNOTFOUND", "Den anbefalede udvidelse '%s' eksisterer ikke. <span class=\"details\">%s</span>");

            define("_GOTOSETUP", "" . MX_SETUP_VERSION . " installere nu!");
            define("_PDOTEST_OK", "PDO-database driver (%s) er tilgængelig");
            define("_PDOTEST_FAIL", "Det blev fundet nogen brugbar PDO-database-driver (f.eks %s)");
            define("_EXTTEST_PDO", "PDO forlængelse vil være den fremtidige standard database driver til pragmaMx. Forlængelsen bør være til rådighed så hurtigt som muligt.");
            define("_EXTTEST_ZIP", "Zip-funktionalitet bruges af nogle add-on moduler og skal være tilgængelige.");
            break;

        default:

            define("_TITLE", "" . MX_SETUP_VERSION . " environment test");
            define("_ENVTEST", "Installations Umgebungs Test");
            define("_SELECTLANG", "Bitte wählen Sie eine Sprache");
            define("_DBTEST", "Datenbank Test");
            define("_DBTEST_ISOFF", "Der Datenbank Test ist deaktiviert. Um den Test zu aktivieren, öffnen Sie bitte diese Datei <i>(" . THIS_FILE . ")</i> in einem Texteditor und stellen Sie die Datenbank Verbindungs Parameter <i>(DB_XXXX)</i> entsprechend ein:");
            define("_DBTEST_HOST", "Addresse des MySQL Servers (in der Regel 'localhost')");
            define("_DBTEST_USER", "Benutzername für die Verbindung zum MySQL Server");
            define("_DBTEST_PASS", "Das entsprechende Benutzerpasswort");
            define("_DBTEST_NAME", "Name der Datenbank zu der eine Verbindung aufgebaut weerden soll");
            define("_DBTEST_SETTHEM", "Sobald diese Parameter korrekt eingestellt sind, kann <i>(" . THIS_FILE . ")</i> überprüfen, ob Ihre Datenbank die Systemanforderungen erfüllt.");
            define("_TEST_ISOK", "OK, auf diesem System sollte " . MX_SETUP_VERSION . " korrekt laufen.");
            define("_TEST_ISNOTOK", "Dieses System erfüllt nicht die Mindestvoraussetzungen zum Betrieb von " . MX_SETUP_VERSION . ".");
            define("_LEGEND", "Legende");
            define("_LEGEND_OK", "<span>OK</span> - Alles OK");
            define("_LEGEND_WARN", "<span>Warnung</span> - Ohne dieses Feature können einige Funktionen von " . MX_SETUP_VERSION . " nicht genutzt werden.");
            define("_LEGEND_ERR", "<span>error</span> - Dieses Feature wird von" . MX_SETUP_VERSION . " unbedingt benötigt.");
            define("_DBTEST_CONNECT", "Verbindung zur Datenbank wurde hergestellt.");
            define("_DBTEST_SELECTED", "gewählte Datenbank wurde gefunden");
            define("_DBTEST_VERSION", "MySQL Version ist: %s");
            define("_DBTEST_VERSIONOLD", "Ihre MySQL Version ist: %s. Wir empfehlen ein Update zur neusten MySQL 5.x Version!");
            define("_DBTEST_SELECTFAIL", "Fehler bei der Auswahl der Datenbank. MySQL Meldung: <br />%s");
            define("_DBTEST_CONNECTFAIL", "Fehler bei der Verbindung zur Datenbank. MySQL Meldung: <br />%s");
            define("_ENVTEST_PHPFAIL", "Die von " . MX_SETUP_VERSION . " benötigte, minimale PHP Version ist %s. Ihre PHP Version ist: %s");
            define("_ENVTEST_PHPOK", "Ihre PHP Version ist: %s");
            define("_ENVTEST_MEMOK", "Ihr PHP Speicher Limit ist: %s");
            define("_ENVTEST_MEMFAIL", "Ihr PHP Speicher Limit ist zu gering um " . MX_SETUP_VERSION . " zu installieren. Der minimale Wert ist %s.");
            define("_EXTTEST_REQFOUND", "Die benötige Erweiterung '%s' ist vorhanden");
            define("_EXTTEST_REQFAIL", "Die Erweiterung '%s' wird benötigt um " . MX_SETUP_VERSION . " zu betreiben.");
            define("_EXTTEST_GD", "GD wird für die Bildbearbeitung verwendet. Ohne diese Erweiterung, ist das System nicht in der Lage, um z.B. Miniaturansichten von Bild-Dateien zu erstellen.");
            define("_EXTTEST_MB", "Multibyte-String wird für den Umgang mit Unicode Zeichen verwendet. Ohne diese Erweiterung kann es evtl. zu Anzeigefehlern bei bestimmten Sonderzeichen kommen.");
            // define("_EXTTEST_ICONV", "Iconv wird teilweise zur Zeichensatz-Konvertierung verwendet.  Ohne diese Erweiterung kann es evtl. zu Anzeigefehlern bei bestimmten Sonderzeichen kommen.");
            define("_EXTTEST_IMAP", "IMAP wird verwendet, um eine Verbindung zu POP3- und IMAP-Servern herzustellen.");
            define("_EXTTEST_CURL", "Die CURL-Funktionen verbessern den Zugriff auf externe Daten.");
            define("_EXTTEST_TIDY", "Wenn die TIDY Erweiterung aktiv ist, kann die HTML-Ausgabe automatisch validiert werden. Dies kann den Seitenaufbau im Browser beschleunigen und macht die Webseite W3C konform.");
            define("_EXTTEST_XML", "Die XML-Erweiterung wird unter anderem für die Generierung der RSS-Feeds benötigt.");
            define("_EXTTEST_RECFOUND", "Die empfohlene Erweiterung '%s' ist vorhanden");
            define("_EXTTEST_RECNOTFOUND", "Die empfohlene Erweiterung '%s' ist nicht vorhanden. <span class=\"details\">%s</span>");
            define("_GOTOSETUP", "" . MX_SETUP_VERSION . " jetzt installieren!");
            define("_PDOTEST_OK", "PDO-Datenbanktreiber (%s) ist verfügbar");
            define("_PDOTEST_FAIL", "Es wurde kein verwendbarer PDO-Datenbanktreiber (z.B. %s) gefunden");
            define("_EXTTEST_PDO", "Die PDO Erweiterung wird künftig der Standarddatenbanktreiber für pragmaMx sein. Die Erweiterung sollte baldmöglichst verfügbar sein.");
            define("_EXTTEST_ZIP", "Die Zip-Funktionalität wird von einigen Zusatzmodulen verwendet und sollte verfügbar sein.");
            break;
    }
}

?>
