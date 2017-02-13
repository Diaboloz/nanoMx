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
 * modified by (c) 2007 by jubilee
 */

class Filter {
    var $image;
    var $filtered;
    var $width;
    var $height;

    function setImage($image)
    {
        $this->image = $image;
        $this->width = imagesx($image);
        $this->height = imagesy($image);
    }

    function getImage()
    {
        return $this->filtered;
    }
}

class Wavy extends Filter {
    var $filtered;

    function run()
    {
        /* variable for waving*/
        srand((double)microtime() * 1000000);
        $width_extra = rand(10, 20);

        /* create canvas for redrawing image */
        $canvas = @imagecreatetruecolor($this->width + $width_extra,
            $this->height + $width_extra);

        $dstX = 0;
        $dstY = 0;
        $dstW = $width_extra;
        $dstH = $this->width;
        $srcX = 0;
        $srcY = 0;
        $srcW = $width_extra;
        $srcH = $this->width-2 * $width_extra;
        $h = rand(4, 9);
        /* waving */
        for($i = 0; $i < $this->width; $i++) {
            imagecopyresized($canvas, $this->image,
                $dstX + $i, $dstY,
                $srcX + $i, $srcY,
                $dstW + $i, $dstH + $width_extra * (sin(deg2rad(1.5 * $i * $h)) + sin(deg2rad($i * $h))),
                $srcW + $i, $srcH);
        }

        $this->filtered = $canvas;
    }
}

class Bubbly extends Filter {
    var $filtered;

    function run()
    {
        /* bubbling image */
        $black = imagecolorallocate ($this->image, 0, 0, 0);
        $white = imagecolorallocate ($this->image, 255, 255, 255);

        for($i = 0; $i < $this->width; $i = $i + 10) {
            srand((double)microtime() * 1000000);
            $w = rand(6, 10);
            $y = rand(0, $this->height);
            imagefilledellipse ($this->image, $i, $y, $w, $w, $black);
            imagefilledellipse ($this->image, $i, $y, $w, $w, $white);
            $w = rand(6, 10);
            imagefilledellipse ($this->image, $i, rand(0, $this->height), $w, $w, $black);
            $w = rand(4, 10);
            imageellipse ($this->image, $i, rand(0, $this->height), $w, $w, $black);
        }

        $this->filtered = $this->image;
    }
}

class BreakType extends Filter {
    var $filtered;

    function run()
    {
        /* variable for waving*/
        srand((double)microtime() * 1000000);
        $width_extra = rand(10, 20);

        /* create canvas for redrawing image */
        $canvas1 = @imagecreatetruecolor($this->height, $this->height);
        $canvas2 = @imagecreatetruecolor($this->width, $this->height);

        imagecopyresized($canvas1, $this->image,
            0, 0,
            0, 0,
            $this->height, $this->height,
            $this->width, $this->height);

        imagecopyresized($canvas2, $canvas1,
            0, 0,
            0, 0,
            $this->width, $this->height,
            $this->height, $this->height);

        $this->filtered = $canvas2;
    }
}

?>
