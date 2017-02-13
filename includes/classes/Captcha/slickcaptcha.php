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
 * $Revision: 171 $
 * $Author: PragmaMx $
 * $Date: 2016-06-29 13:59:03 +0200 (Mi, 29. Jun 2016) $
 *
 * written by swalkner@gmail.com
 * thanks to uwe slick! http://www.deruwe.de/captcha.html
 * - just copy/pasted his thoughts GPLed!
 * modified by (c) 2007 by jubilee
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * this class main responsibilities are:
 * 1) use a given passphrase -> generation has to be done outside!
 * 2) create an image which display (human readable) this string
 * 3) offer methods to get/set various properties
 */

include(dirname(__FILE__) . '/class.filter.php');

class SlickCaptcha {
    var $passphrase = null;
    var $width = 170;
    var $height = 60;
    var $fontsPath = "includes/classes/Captcha/fonts";
    var $fontColor = array();
    var $useRandomColors = true;
    var $bgColor = array();
    var $fontSize = 20;
    var $image = null;
    var $background_intensity = 50;
    var $font_type = 5;
    var $image_font_width;
    var $image_font_height;
    var $scratches = true;
    var $scraches_amount = 25;
    var $use_filter = true;
    var $filter_name = 'BreakType'; //Wavy, Bubbly or BreakType
    var $angle = 45; // Angle of disortion
    var $minsize = 20;
    var $maxsize = 30;
    var $addagrid = true;
    var $addhorizontallines = true;
    /**
     * default constructor
     *
     * @param  $passphrase set the passphrase will be used
     */
    function __construct ($passphrase = null, $useRandomColors = false)
    {
        ob_start();
        $this->captchaPhrase = $passphrase;

        srand((double)microtime() * 1000000);
        // init font color: black by default
        $this->fontColor['r'] = 0;
        $this->fontColor['g'] = 0;
        $this->fontColor['b'] = 0;
        // init font color: grey by default
        $this->bgColor['r'] = 225;
        $this->bgColor['g'] = 225;
        $this->bgColor['b'] = 225;

        $this->useRandomColors = $useRandomColors;
    }

    /**
     * Set the disortion angle
     */
    function set_angle($angle = 45)
    {
        // Intensitaet des Hintergrunds zuweisen
        $this->angle = $angle;
    }

    /**
     * Set the background intensity
     */
    function set_background_intensity($background_intensity = 50)
    {
        // Intensitaet des Hintergrunds zuweisen
        $this->background_intensity = $background_intensity;
    }

    /**
     * Set min and maxsize for function addgrid()
     */
    function set_minmax_size($minsize = 20, $maxsize = 30)
    {
        // Systemfont angeben
        $this->minsize = $minsize;
        $this->maxsize = $maxsize;
    }

    /**
     * Show a grid in Captcha
     */
    function set_showgrid($what = true)
    {
        // Systemfont angeben
        $this->addagrid = $what;
    }

    /**
     * Show colored lines in Captcha
     */
    function set_showcoloredlines($what = true)
    {
        // Systemfont angeben
        $this->addhorizontallines = $what;
    }

    /**
     * Set the font type for background
     */
    function set_font_type($font_type = 5)
    {
        // Systemfont angeben
        $this->font_type = $font_type;
    }

    /**
     * Set deformation Filter
     */
    function set_filter_name($filtername = 'Wavy')
    {
        // Systemfont angeben
        $this->filter_name = $filtername;
    }

    /**
     * Use deformation-filter
     */
    function use_filter($usefilter = true)
    {
        // Systemfont angeben
        $this->use_filter = $usefilter;
    }

    /**
     * Enable Scratches
     */
    function enable_scratches($scratches = true)
    {
        // Zufallslinien auf Bild
        $this->scratches = $scratches;
    }

    /**
     * Set amount of scratches in pic when activated
     */
    function set_scratches_amount($amount = 25)
    {
        // Systemfont angeben
        $this->scraches_amount = $amount;
    }

    /**
     * change the width of the resulting
     * captcha image
     */
    function setImageWidth($width)
    {
        $this->width = $width;
    }

    /**
     * change the height of the resulting
     * captcha image
     */
    function setImageHeight($height)
    {
        $this->height = $height;
    }

    /**
     * change the default fonts directory path
     * from includes/classes/Captcha/fonts to a user specific value
     */
    function setFontsPath($fontsPath)
    {
        $this->fontsPath = $fontsPath;
    }

    /**
     * Check version of GDLib
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
     * Create a filled random background
     */
    function create_captcha_background()
    {
        // Breite eines Zeichens
        $this->image_font_width = ImageFontWidth($this->font_type) + 2;
        // Hoehe eines Zeichens
        $this->image_font_height = ImageFontHeight($this->font_type) + 2;
        // Zufallswerte für hintergrundfarbe
        if ($this->useRandomColors) {
            $this->setBgColor(intval(rand(225, 255)), intval(rand(225, 255)), intval(rand(225, 255)));
        } else {
            $this->setBgColor(225, 225, 225);
        }
        // Hintergrund-Farbe setzen
        $captcha_background_color = ImageColorAllocate($this->image, $this->bgColor['r'], $this->bgColor['g'], $this->bgColor['b']);
        // Flaeche fuellen
        ImageFilledRectangle($this->image, 0, 0, $this->width, $this->height, $captcha_background_color);
        // Zufallsstrings durchlaufen
        for ($x = 0; $x < $this->background_intensity; $x ++) {
            // Zufallsstring-Farbe
            $random_string_color = ImageColorAllocate($this->image, intval(rand(164, 254)), intval(rand(164, 254)), intval(rand(164, 254)));
            // Zufalls-String generieren
            $random_string = chr(intval(rand(65, 122)));
            // X-Position
            $x_position = intval(rand(0, $this->width - $this->image_font_width * strlen($random_string)));
            // Y-Position
            $y_position = intval(rand(0, $this->height - $this->image_font_height));
            // Zufalls-String
            ImageString($this->image, $this->font_type, $x_position, $y_position, $random_string, $random_string_color);
        }
        if ($this->addagrid) {
            $this->addgrid();
        }
        if ($this->addhorizontallines) {
            $this->addhorlines();
        }
    }

    /**
     * Create the Image
     */
    function open_captcha_image()
    {
        // Grafik anlegen
        $gd_lib_version = $this->check_type_support();
        if ((isset($gd_lib_version['GD_VERSION'])) && ($gd_lib_version['GD_VERSION'] > "2.0.0"))
            return ImageCreateTrueColor($this->width, $this->height);
        else
            return ImageCreate($this->width, $this->height);
    }

    /**
     * change the default font color (black)
     * to a user specific value
     */
    function setFontColor($r, $g, $b)
    {
        $this->fontColor['r'] = $r;
        $this->fontColor['g'] = $g;
        $this->fontColor['b'] = $b;
    }

    /**
     * change the default background color (grey)
     * to a user specific value
     */
    function setBgColor($r, $g, $b)
    {
        $this->bgColor['r'] = $r;
        $this->bgColor['g'] = $g;
        $this->bgColor['b'] = $b;
    }

    /**
     * change the default font size (20)
     * to a user specific value
     */
    function setFontSize($size)
    {
        $this->fontSize = $size;
    }

    function getRandomFont()
    {
        static $fonts = array();
        if (count($fonts) == 0) {
            $dh = opendir($this->fontsPath);
            while (false !== ($font = readdir($dh))) {
                if (($font != ".") && ($font != "..")) {
                    if (substr(strtolower($font), -3) == "ttf") {
                        $fonts[] = sprintf("%s/%s", $this->fontsPath, $font);
                    }
                }
            }
            closedir($dh);
        }

        return $fonts[rand(0, count($fonts)-1)];
    }

    function create_scratches()
    {
        for($i = 1;$i < $this->scraches_amount;$i++) {
            $randPixSpaceLeft = mt_rand(0, $this->width);
            $randPixSpaceTop = mt_rand(0, $this->height);
            $style = mt_rand(0, 2);
            switch ($style) {
                case 0:
                    $txtColor = imagecolorallocate($this->image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
                    ImageLine($this->image, $randPixSpaceLeft, $randPixSpaceTop, $randPixSpaceLeft + 10, $randPixSpaceTop + 7, $txtColor);
                    break;
                case 1:
                    $noiseColor = imagecolorallocate($this->image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
                    ImageLine($this->image, $randPixSpaceLeft, $randPixSpaceTop, $randPixSpaceLeft-3, $randPixSpaceTop + 7, $noiseColor);
                    break;
                default:
                    $bgColor = imagecolorallocate($this->image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
                    ImageLine($this->image, $randPixSpaceLeft, $randPixSpaceTop, $randPixSpaceLeft-5, $randPixSpaceTop-5, $bgColor);
            }
        }
    }

    /**
     * generate the captcha
     * and return the image
     * this function is called by display/get themself
     */
    function generate()
    {
        // create a white empty image
        $this->image = $this->open_captcha_image();
        // create a random filled Background
        $this->create_captcha_background();
        // draw the passphrase chars and use a different font every time...
        $phraseLength = strlen($this->captchaPhrase);
        if (!$phraseLength) {
            // fix gegen Divison by Zero
            $phraseLength = 1;
        }
        $widthPerChar = $this->width / $phraseLength;
        $heightPerChar = $this->height - 2; //2pix spacing...
        // default font color...
        $color = imagecolorallocate($this->image, $this->fontColor['r'], $this->fontColor['g'], $this->fontColor['b']);

        $i = 0;
        for ($idx = 0; $idx < $phraseLength; $idx++) {
            $currentFont = $this->getRandomFont();
            // readable angle between +/-32
            $disangle = rand(- $this->angle, $this->angle);

            $charInfo = $this->calculateTextBox($this->fontSize, $disangle, $currentFont, $this->captchaPhrase[$idx]);

            /* negative Breiten ignorieren (http://www.pragmamx.org/Forum-topic-31593.html) */
            if ($charInfo['width'] <= 0 && $i <= ($phraseLength * 2)) {
                $idx--;
                $i++;
                continue;
            }

            if ($charInfo['width'] > $widthPerChar) {
                echo "Please increase image width or use a smaller fon/font size";
                exit(1);
            }
            // every char has $widthPerChar space to draw itself!
            // calculate left/right margin from $widthPerChar
            $xMargin = ($widthPerChar - $charInfo['width']) / 2;
            // current x: widthPerChars*Chars[we already processed]+margin
            $x = ($idx * $widthPerChar) + $xMargin;

            /* negative Höhen ignorieren */
            if ($charInfo['height'] <= 0 && $i <= ($phraseLength * 2)) {
                $idx--;
                $i++;
                continue;
            }
            if ($charInfo['height'] > $heightPerChar) {
                echo "Please increase image height or use a smaller fon/font size";
                exit(1);
            }
            // upper=0 -> lower=imageHeight
            $baseline = ($heightPerChar - $charInfo['height']) / 2;
            $y = $baseline + $charInfo['height']; //heightPerChar/2 = baseline -> see php.net!
            // font color
            if ($this->useRandomColors) {
                // generate random colors
                do {
                    $r = rand(0, 255);
                    $g = rand(0, 255);
                    $b = rand(0, 255);
                }
                // of course we wont't have the background color! (+some tolerance area...)
                while (!$this->inRgbTolerance($this->bgColor, array("r" => $r, "g" => $g, "b" => $b)));

                $color = imagecolorallocate($this->image, $r, $g, $b);
            }

            imagettftext($this->image, $this->fontSize, $disangle, $x, $y, $color, $currentFont, $this->captchaPhrase[$idx]);
        }

        if ($this->use_filter) {
            $this->applyFilter($this->filter_name);
        }
        if ($this->scratches) {
            $this->create_scratches($color);
        }

        /* Ausgabepuffer leeren um Fehlerausgaben abzufangen */
        for ($i = 1; ($i <= 10 && ob_get_contents()); $i++) {
            ob_end_clean();
        }
    }

    /**
     * SlickCaptcha::calculateTextBox()
     * calculate the box of a text for a given font
     * by Alexander Gavazov, 04-Sep-2008 10:25
     * at http://www.php.net/manual/en/function.imagettfbbox.php
     *
     * @param mixed $font_size
     * @param mixed $font_angle
     * @param mixed $font_file
     * @param mixed $text
     * @return
     */
    function calculateTextBox($font_size, $font_angle, $font_file, $text)
    {
        $box = imagettfbbox($font_size, $font_angle, $font_file, $text);
        $min_x = min(array($box[0], $box[2], $box[4], $box[6]));
        $max_x = max(array($box[0], $box[2], $box[4], $box[6]));
        $min_y = min(array($box[1], $box[3], $box[5], $box[7]));
        $max_y = max(array($box[1], $box[3], $box[5], $box[7]));

        return array(/* Ausgabe */
            'left' => ($min_x >= -1) ? - abs($min_x + 1) : abs($min_x + 2),
            'top' => abs($min_y),
            'width' => $max_x - $min_x,
            'height' => $max_y - $min_y,
            // 'box' => $box
            );
    }

    /**
     * check whether a "newColor" is within a tolerance area of a "originalColor"
     * this function should avoid unreadable colors!
     */
    function inRgbTolerance($originalColors, $newColors)
    {
        // if more than two index are closer than 20 > false
        $matches = 0;
        foreach ($originalColors as $rgbIdx => $value) {
            if (abs($newColors[$rgbIdx] - $value) < 60) { // means on the "other" side of the rgb spectrum
                return false;
            }
        }

        return true;
    }

    /**
     * displays the generated
     * captcha in a browser
     */
    function display()
    {
        if ($this->image == null) {
            $this->generate();
        }
        // output the image in the browser
        header("Content-type: image/jpeg");
        imagejpeg($this->image);
        // free allocated mem ;-)
        imagedestroy($this->image);
    }

    /**
     * store the generated captcha in $fileName
     * returns the result of imagejpeg()
     */
    function store($fileName)
    {
        if ($this->image == null) {
            $this->generate();
        }

        return imagejpeg($this->image, $fileName);
    }

    function applyFilter($filtername)
    {
        if (!$filtername)
            return;
        $im = new $filtername;
        $im->setImage($this->image);
        $im->run();
        $im_filtered = $im->getImage();
        $this->image = $im_filtered;
    }

    function addhorlines()
    {
        $grey = imagecolorallocate($this->image, 235, 235, 235);
        $white = imagecolorallocate($this->image, 255, 255, 255);
        $black = imagecolorallocate($this->image, 0, 0, 0);
        $red = imagecolorallocatealpha($this->image, 255, 0, 0, 75);
        $green = imagecolorallocatealpha($this->image, 0, 255, 0, 75);
        $blue = imagecolorallocatealpha($this->image, 0, 0, 255, 75);
        imageline($this->image, rand(1, $this->width), rand(1, $this->height), rand(101, $this->width), rand(26, $this->height), $red);
        imageline($this->image, rand(1, $this->width), rand(1, $this->height), rand(101, $this->width), rand(26, $this->height), $green);
        imageline($this->image, rand(1, $this->width), rand(1, $this->height), rand(101, $this->width), rand(26, $this->height), $blue);
        imageline($this->image, rand(1, $this->width), rand(1, $this->height), rand(101, $this->width), rand(26, $this->height), $red);
        imageline($this->image, rand(1, $this->width), rand(1, $this->height), rand(101, $this->width), rand(26, $this->height), $green);
        imageline($this->image, rand(1, $this->width), rand(1, $this->height), rand(101, $this->width), rand(26, $this->height), $blue);
    }

    function random_color($min, $max)
    {
        $randcol['r'] = intval(rand($min, $max));
        $randcol['g'] = intval(rand($min, $max));
        $randcol['b'] = intval(rand($min, $max));
        return $randcol;
    }

    function addgrid()
    {
        // generate grid
        for($i = 0; $i < $this->width; $i += (int)($this->minsize / 1.5)) {
            $randcol = $this->random_color(160, 224);
            $color = imagecolorallocate($this->image, $randcol['r'], $randcol['g'], $randcol['b']);
            @imageline($this->image, $i, 0, $i, $this->height, $color);
        }
        for($i = 0 ; $i < $this->height; $i += (int)($this->minsize / 1.8)) {
            $randcol = $this->random_color(160, 224);
            $color = imagecolorallocate($this->image, $randcol['r'], $randcol['g'], $randcol['b']);
            @imageline($this->image, 0, $i, $this->width, $i, $color);
        }
        @imageline($this->image, $this->width, 0, $this->width, $this->height, $color);
        @imageline($this->image, 0, $this->height, $this->width, $this->height, $color);
    }

    function pickRandomBackground()
    {
        $bg_color = imagecolorallocate ($this->image, 255, 255, 255);
        imagefill ($this->image, 0, 0, $bg_color);
        for($i = 0; $i < $this->height; $i++) {
            $c = rand (140, 170);
            $d = rand (0, 10);
            $e = rand (0, 10);
            $f = rand (0, 10);
            $line_color = imagecolorallocate ($this->image, $c + $d, $c + $e, $c + $f);
            imagesetthickness ($this->image, rand(1, 5));
            imageline($this->image, 0, $i + rand(-15, 15), $this->width, $i + rand(-15, 15), $line_color);
        }
    }
}

?>