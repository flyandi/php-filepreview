<?php

/**
 * flyandi:php-filepreview
 *
 * Generates previews for various file formats and fallbacks
 *
 * @version: v1.0.0
 * @author: Andy Schwarz
 *
 * Created by Andy Schwarz. Please report any bug at http://github.com/flyandi/php-mime
 *
 * Copyright (c) 2016 Andy Schwarz http://github.com/flyandi
 *
 * The MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */

/**
 * (Constants)
 */



/**
 * (class) FilePreview
 */
class FilePreview {


    /**
     * (constants) colors
     * @var string
     */
    const COLOR_WHITE = "255,255,255";
    const COLOR_RED = "219,40,40";
    const COLOR_ORANGE = "242,113,28";
    const COLOR_YELLOW = "251,189,8";
    const COLOR_OLIVE = "181,204,24";
    const COLOR_GREEN = "33,186,69";
    const COLOR_TEAL = "0,181,173";
    const COLOR_BLUE = "33,133,208";
    const COLOR_VIOLET = "100,53,201";
    const COLOR_PURPLE = "163,51,200";
    const COLOR_PINK = "224,57,151";
    const COLOR_BROWN = "165,103,63";
    const COLOR_GREY = "118,118,118";
    const COLOR_BLACK = "27,28,29";

    /**
     * (constants) output formats
     * @var string
     */
    const OUTPUT_PNG = "png";

    /**
     * (constants) parameters
     */
    const DEFAULT_WIDTH = 320;
    const DEFAULT_HEIGHT = 240;
    const DEFAULT_TEXTSIZE_CAPTION = 40;
    const DEFAULT_TEXTSIZE_DESCRIPTION = 15;
    const DEFAULT_BAR_HEIGHT = 75;
    const DEFAULT_BAR_BOTTOM = 30;
    const DEFAULT_BAR_PADDING = 10;


    /**
     * [create description]
     * @param  [type] $source      [description]
     * @param  [type] $destination [description]
     * @param  [type] $width       [description]
     * @param  [type] $height      [description]
     * @return [type]              [description]
     */
    public static function create($source, $destination, $width, $height) {

        $instance = new self();

        $image = $instance->renderPreview($source, $width, $height);

        return $instance->output($image, $destination);
    }


    /**
     * [output description]
     * @param  [type] $image  [description]
     * @param  [type] $format [description]
     * @return [type]         [description]
     */
    public function output($image, $filename = false, $format = self::OUTPUT_PNG) {

        if(!$image) return false;

        switch($format) {

            default:
                imagepng($image, $filename);
                break;
        }

        return true;
    }


    /**
     * [renderPreview description]
     * @param  [type] $filename [description]
     * @param  [type] $width    [description]
     * @param  [type] $height   [description]
     * @return [type]           [description]
     */
    public function renderPreview($filename, $width, $height) {

        // get mime
        $mime = MimeTypeByFileExtension($filename); 

        if(!$mime) $mime = MimeTypeByFilename($filename);

        if(!$mime) return false;

        // detect if this is an image
        if($image = @getimagesize($filename)) {

            // pass to resize handler
            return $this->__autoImageResize($filename, $width, $height);
        }

        // pass to mime handler
        $params = $this->__mime($mime);

        // render generic
        return $this->renderGeneric($width, $height, $params->caption, $params->description, $params->backgroundColor, $params->barColor);
    }

    /**
     * [renderPreviewWithCaption description]
     * @param  [type] $filename        [description]
     * @param  [type] $width           [description]
     * @param  [type] $height          [description]
     * @param  [type] $caption         [description]
     * @param  [type] $description     [description]
     * @param  [type] $backgroundColor [description]
     * @param  [type] $barColor        [description]
     * @return [type]                  [description]
     */
    public function renderPreviewWithCaption($filename, $width, $height, $caption, $description, $backgroundColor, $barColor = self::COLOR_WHITE) {

        // get image
        $image = $this->renderPreview($filename, $width, $height);

        return $this->renderGeneric($width, $height, $caption, $description, $backgroundColor, $barColor, $image);
    }
    

    /**
     * [renderGeneric description]
     * @param  [type]  $width              [description]
     * @param  [type]  $height             [description]
     * @param  [type]  $caption            [description]
     * @param  [type]  $description        [description]
     * @param  [type]  $backgroundColor    [description]
     * @param  [type]  $barColor           [description]
     * @param  boolean $useBackgroundImage [description]
     * @return [type]                      [description]
     */
    public function renderGeneric($width, $height, $caption, $description, $backgroundColor, $barColor = self::COLOR_WHITE, $backgroundImage = false, $useDefault = true) {

        // prepare
        $caption = strtoupper($caption);
        $description = strtoupper($description);
        $font = dirname(__FILE__) . "/font/font.ttf";

        // set reference size
        $referenceSize = $width;
        $referenceDefault = self::DEFAULT_WIDTH;

        if($height < $width && $useDefault) {
            $referenceSize = $height;
            $referenceDefault = self::DEFAULT_HEIGHT;
        }

        // create image
        $image = imagecreatetruecolor($width, $height);

        // prepare colors
        $backgroundColor = $this->__color($image, $backgroundColor);
        $barColor = $this->__color($image, $barColor);

        // set background color    
        imagefill($image, 0, 0, $backgroundColor);

        // set background image
        if($backgroundImage) {
            imagecopy($image, $backgroundImage, 0, 0, 0, 0, $width, $height);
        }

        // calculate sizes
        $captionSize = $this->__calcSize($referenceSize, self::DEFAULT_TEXTSIZE_CAPTION, $referenceDefault);
        $descriptionSize = $this->__calcSize($referenceSize, self::DEFAULT_TEXTSIZE_DESCRIPTION, $referenceDefault);
        $captionBox = imagettfbbox($captionSize, 0, $font, $caption);
        $descriptionBox = imagettfbbox($descriptionSize, 0, $font, $description);

        // calculate bar paddings
        $barPadding = $this->__calcSize($referenceSize, self::DEFAULT_BAR_PADDING, $referenceDefault);
        $barHeight = $this->__calcSize($referenceSize, self::DEFAULT_BAR_HEIGHT, $referenceDefault);
        $barBottom = $this->__calcSize($referenceSize, self::DEFAULT_BAR_BOTTOM, $referenceDefault);
        $barWidth = ($descriptionBox[2] - $descriptionBox[0]) + (2 * $barPadding);

        // create bar
        imagefilledrectangle($image,
            $width - $barWidth,
            $height - $barHeight - $barBottom,
            $width,
            $height - $barBottom,
            $barColor
        );

        // draw caption
        $x = $width - $barWidth + $barPadding;
        $y = $height - $barHeight - $barBottom + ($captionSize) + $barPadding;

        // draw caption
        imagettftext($image, $captionSize, 0, $x, $y, $backgroundColor, $font, $caption);

        // draw description
        imagettftext($image, $descriptionSize, 0, $x, $y + $descriptionSize + ($barPadding/2), $backgroundColor, $font, $description);

        // return image
        return $image;
    }


    /**
     * [__color description]
     * @param  [type] $image [description]
     * @param  [type] $rgb   [description]
     * @return [type]        [description]
     */
    private function __color($image, $rgb) {

        if(is_string($rgb)) $rgb = explode(",", $rgb);

        if(count($rgb) != 3) return false;

        return imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);

    }

    /**
     * [__calcTextSize description]
     * @param  [type] $width [description]
     * @param  [type] $size  [description]
     * @return [type]        [description]
     */
    private function __calcSize($value, $defaultSize, $defaultValue = self::DEFAULT_WIDTH) {

        return round(($defaultSize * $value) / $defaultValue);
    }


    /**
     * [__autoImageResize description]
     * @param  [type] $filename [description]
     * @param  [type] $width    [description]
     * @param  [type] $height   [description]
     * @return [type]           [description]
     */
    private function __autoImageResize($filename, $width, $height) {

        // create image
        $image = imagecreatefromstring(file_get_contents($filename));
        $composed = imagecreatetruecolor($width, $height);

        // get original sizes
        $originalWidth = ImageSX($image);
        $originalHeight = ImageSY($image);

        // calculate ratio
        $ratioComposed = $width / $height;
        $ratioOriginal = $originalWidth / $originalHeight;

        // calcualte ratio
        if($ratioOriginal >= $ratioComposed) {
            $by = $originalHeight;
            $bx = ceil(($by * $width) / $height);
            $ax = ceil(($originalWidth - $bx) / 2);
            $ay = 0;
        
        } else {
            $bx = (integer) $originalWidth;
            $by = (integer) ceil(($bx * $height) / $width);
            $ay = (integer) ceil(($originalHeight - $height) / 2);
            $ax = 0;
        }

        imagecopyresampled($composed, $image, 0, 0, $ax, $ay, $width, $height, $bx, $by);

        // completed
        return $composed;
    }

    /**
     * [__mime description]
     * @param  [type] $mime [description]
     * @return [type]       [description]
     */
    private function __mime($mime) {

        // load mime database
        $database = file(dirname(__FILE__) . "/filepreview.db");

        foreach($database as $item) {

            $item = explode("|", $item);

            if(strtolower($item[0]) == $mime->mime) {
                
                return (object) [
                    "backgroundColor" => $item[1],
                    "barColor" => $item[2],
                    "caption" => $item[3],
                    "description" => $item[4]
                ];
            }
        } 

        // set colors
        $colors = [self::COLOR_RED, self::COLOR_ORANGE, self::COLOR_YELLOW, self::COLOR_OLIVE, self::COLOR_GREEN, self::COLOR_TEAL, self::COLOR_BLUE, self::COLOR_VIOLET, self::COLOR_PURPLE, self::COLOR_PINK, self::COLOR_BROWN, self::COLOR_GREY, self::COLOR_BLACK];

        // get first letter of description
        $index = floor((ord(strtoupper(substr($mime->description, 0, 1))) - 65) / 2);

        return (object) [
            "backgroundColor" => isset($colors[$index]) ? $colors[$index] : self::COLOR_GREY,
            "barColor" => self::COLOR_WHITE,
            "caption" => strtoupper(@$mime->extensions[0]),
            "description" => $mime->description,
        ];

    }
}

/* eof */