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
 * $Revision: 165 $
 * $Author: PragmaMx $
 * $Date: 2016-06-09 10:27:55 +0200 (Do, 09. Jun 2016) $
 */

/**
 * Class: RSS_feed
 * Author: Dr. Timothy Sakach
 * Version 2.0
 * This script will parse a RSS/XML file that comes from a URL feed.
 * It will return an HTML unordered list.
 *
 * Getting a feed from another web site, should be quick and simple. Any
 * unneeded complications should be avoided. One feed processor from a
 * large Content Management System written in PHP nearly got me kicked
 * off my server. It ran wild with overly complex coding and included
 * opening hundreds of objects in a loop and used the XML-DOM (ugh!).
 *
 * This is far simpler, faster and more reliable.
 *
 * There are several versions of RSS/RDF. However, in practical use
 * most all feed implementations follow the same pattern: A publication link is
 * contained within <item> tags and consists of a <title>, <link>, and
 * a <description>. We tested feeds from many different sources using
 * all flavors and versions and this class was able to parse all of them.
 *
 * Granted there are many options with version 1.0 (RDF). However,
 * the full RDF syntax and vocabulary are not needed in a feed.
 * All of the implementations we found that included RDF tags
 * really added very little to the feed and  were safely ignored.
 *
 * Version 2.0 extends versions 0.9x by adding new tags. This presented
 * no problems and this class can be extended to process those tags. But
 * our goal was to provide a simple solution that can easily add syndicated
 * publications to any web site.
 *
 * Changes: by Dr. Timothy Sakach
 * 1. 8/31/03 Corrected bug because the description tag (and all other tags, for that
 * matter} can occur in any sequence within the container tags.
 * 2. 8/31/03 Corrected bug created by the way the XML parser returns parsed cdata.
 * 3. 8/31/03 Added Set_Limit property to control the number of links to show.
 * 4. 8/31/03 Added image control and Show_Image property.
 * 5. 8/31/03 Channel, Image, and Items now use arrays as buffers.
 *
 * more changes done by jubilee (www.marx-city.de)
 * 1. Fixed some undefined indexes in var declarations so that now
 * no notices on error_reporting0on are shown.
 * 2. clear some var's after script output so that you can run this class
 * width only one contructor call in loops (before this fixes all
 * input from all sources are mapped in the var and corrupted the output
 */

defined('mxMainFileLoaded') or die('access denied');

class RSS_feed {
    // The object of the parser is to determine the "State" of the RSS/XML
    // and set up the class to respond accordingly. The critical information
    // is in the _handle_character_data function as that is where the
    // feed information can be found.
    var $flag; // To control the state of the unordered list
    var $state; // To determine which element tag is being worked
    var $level; // Simple XML level control
    var $output; // Where the results will be stored
    var $showdesc; // A flag to indicate whether or not to show the description.
    var $showimage; // A flag to indicate whether or not to show the image.
    var $URL; // The location of the external feed.
    var $psr; // Our parser object.
    var $contents; // The RSS/XML from the feed
    var $rss_version; // Stores version "number"
    var $limit; // The maximum number of links we want
    var $channel; // Array of channel cdata
    var $image; // Array of image cdata
    var $item; // Array of item cdata
    var $channelclosed; // To indicate state if channel closes before image tags.
    var $encoding;
    // Define the functions required for handling the different pieces.
    function __construct()
    {
        // Constructor
        $this->output = "";
        $this->channel = array();
        $this->channel['title'] = "";
        $this->channel['link'] = "";
        $this->channel['desc'] = "";
        $this->image = array();
        $this->image['title'] = "";
        $this->image['link'] = "";
        $this->image['url'] = "";
        $this->item = array();
        $this->item['title'] = "";
        $this->item['link'] = "";
        $this->item['desc'] = "";
        $this->flag = false;
        $this->state = 0;
        $this->level = 0;
        $this->showdesc = false;
        $this->showimage = false;
        $this->channelclosed = false;
        $this->version = 9; // set 0.9x as default;
        $this->limit = 0; // use 0 as default == all
    }
    // METHODS AND PROPERTIES *****************************************
    function Show_Description($tf)
    {
        // By default the description is not included in the results
        // This public function allows for the description to be
        // included, if desired.
        if (!$tf === false) {
            $this->showdesc = true;
        } else {
            $this->showdesc = false;
        }
    }

    function Show_Image($tf)
    {
        // By default the image is not included in the results
        // This allows for the image to be included, if desired.
        if (!$tf === false) {
            $this->showimage = true;
        } else {
            $this->showimage = false;
        }
    }

    function Set_URL($url)
    {
        // This is the URL to the feed. The class expects that RSS/XML will
        // be returned.
        $this->URL = $url;
        // Knowing this, we can get the feed contents now.
        // Get the RSS/XML from the feed URL
        $this->_load_file();
        // Check the version of the XML and set the version state
        $this->_get_rss_version();
    }

    function Set_Limit($cnt)
    {
        // This property sets the limit of links to return
        // if $cnt is not numeric, 0 is returned! You get the entire list!
        $i = intval($cnt);
        if ($i > 0) $this->limit = $i;
    }

    function Set_Encoding($charset)
    {
        // This property sets the charset to return
        if ($charset) {
            $this->encoding = $charset;
        }
    }

    function Get_Results()
    {
        // When the properties have been set, then this function should
        // be called. It will return the HTML unordered list.
        $c = $this->contents;
        $this->output = "";
        // Create the parser and set handlers.
        if ((strtoupper($this->encoding) !== 'utf-8') && (strtoupper($this->encoding) !== 'US-ASCII') && (strtoupper($this->encoding) !== 'utf-8')) {
            $this->encoding = 'utf-8';
        } else {
            $this->encoding = (strtoupper($this->encoding));
        }
        $this->psr = xml_parser_create($this->encoding);
        xml_parser_set_option($this->psr, XML_OPTION_TARGET_ENCODING, 'utf-8');

        xml_set_object($this->psr, $this);
        xml_parser_set_option($this->psr, XML_OPTION_CASE_FOLDING, 1);
        // Set the parser element handlers based upon the version.
        switch ($this->version) {
            case 9:
            case 2;
                xml_set_element_handler($this->psr, '_handle_open_element', '_handle_close_element');
                break;
            case 1:
                xml_set_element_handler($this->psr, '_rdf_handle_open_element', '_handle_close_element');
                break;
        }
        // Set the handler for the cdata
        xml_set_character_data_handler($this->psr, "_handle_character_data");
        // Parse it.
        if (!xml_parse ($this->psr, $c)) {
            // This returns an error message if the RSS/XML cannot be parsed.
            // Too bad.
            // This indicates a bad or malformed feed!
            $ln = xml_get_current_line_number($this->psr);
            $msg = xml_error_string(xml_get_error_code($this->psr));
            if (defined('_XMLERROROCCURED')) {
                return _XMLERROROCCURED . " $ln: $msg";
            } else {
                return "An XML error occurred in line $ln: $msg";
            }
        }
        // Free up the parser and clear memory
        xml_parser_free($this->psr);
        $this->contents = "";
        // Close the list and return the results
        $this->output .= "</ul>";
        return $this->output;
    }
    // **************************************************************
    // HANDLER FUNCTIONS
    function _handle_open_element (&$p, &$element, &$attributes)
    {
        // parser for rss version 0.9x and 2.0
        // Set the state of the class for the benefit of the cdata handler.
        $element = strtolower($element);

        switch ($element) {
            case 'rss':
                // data at this level may not be needed
                $this->level = 0;
                $this->state = 0;
                break;
            case 'channel':
                // The channel may have a title and a link and a description
                // This data will not be part of the list, but will precede it.
                $this->flag = true;
                $this->level = 1;
                $this->state = 1;
                break;
            case 'item':
                // We have an item to process
                $this->level = 3;
                $this->state = 3;
                break;
            // some tags that will appear under 'channel'
            // for now, ignore these tags.
            case 'pubdate':
            case 'managingeditor':
            case 'webmaster':
            case 'width':
            case 'height':
            case 'language':
                $this->state = 99;
                break;
            case 'image':
                // This assumes the image is a container element, as this is most common
                $this->level = 2;
                break;
            case 'title':
                $this->state = 4;
                break;
            case 'link':
                $this->state = 5;
                break;
            case 'description':
                $this->state = 6;
                break;
            case 'url':
                $this->state = 7;
                break;
            default:
                // ignore any undefined tags
                $this->state = 99;
                break;
        }
    }

    function _rdf_handle_open_element(&$p, &$element, &$attributes)
    {
        // RDF mixes things up a bit so we need to pay attention
        // However, when you get right down to it. There may be
        // no difference in the RDF feed other than more stuff to
        // ignore.
        $element = strtolower($element);
        // Include Dublin Core tags and full RDF specs?
        // Nah!
        switch ($element) {
            case "rdf:rdf":
                // The parser takes care of the Namespace, so we can ignore this.
                $this->level = 0;
                $this->state = 0;
                break;
            case "channel":
                // Same as above.
                $this->flag = true;
                $this->level = 1;
                $this->state = 1;
                break;
            case "image":
                // Assumes <image> is a container element. This is most common.
                $this->level = 2;
                break;
            case "items":
                // channel parameters stop and items begin. We can ignore this.
                $this->state = 99;
                break;
            case "item":
                // We have an item to process.
                $this->level = 3;
                $this->state = 3;
                break;
            case "title":
                $this->state = 4;
                break;
            case "link":
                $this->state = 5;
                break;
            case "description":
                $this->state = 6;
                break;
            case "url":
                $this->state = 7;
                break;
            // These next two are somewhat redundant, unless strict RDF format is followed.
            // Ho hum ...
            // We will ignore them.
            case "rdf:seq":
            case "rdf:li":
            default:
                // ignore tags
                $this->state = 99;
                break;
        }
    }

    function _handle_character_data(&$p, &$cdata)
    {
        /*
     This function is trivialized in many examples. However, this is
     where the real action lies. We have set the state of the class in order
     to determine what we should do with cdata.

     Changes had to be made here because the PHP parser, under special conditions
     would parse the data in the elements and return it in pieces. This showed
     up when the <title> contained &apos; or &quot;

     This function only accumulates $cdata text in arrays.
    */
        // Ignore $cdata filled with blanks or nothing
        $s = trim($cdata);
        if (strlen($s)) {
            // We really only need these things now: Title, Links and, if desired, Description and Image URL
            switch ($this->state) {
                case 4: // title
                    switch ($this->level) {
                        // This are the only levels that are important here.
                        case 1: // Channel
                            $this->channel['title'] .= $cdata;
                            break;
                        case 2; // Image
                            $this->image["title"] .= $cdata;
                            break;
                        case 3: // Item
                            $this->item["title"] .= $cdata;
                            break;
                    }
                    break;
                case 5: // link
                    switch ($this->level) {
                        case 1: // Channel
                            // Make the link for the channel and change to item level. We're done here.
                            $this->channel["link"] .= $cdata;
                            break;
                        case 2: // Image
                            $this->image["link"] .= $cdata;
                            break;
                        case 3: // Item
                            // Make the link for the item. Reset the flag and initialize the unordered list.
                            // Add the link for the item
                            $this->item["link"] .= $cdata;
                            break;
                    }
                    break;
                case 6: // description
                    // If the description is desired, add it now.
                    if ($this->showdesc) {
                        switch ($this->level) {
                            case 1: // Channel
                                $this->channel["desc"] .= $cdata;
                                break;
                            case 3: // Item
                                $this->item["desc"] .= $cdata;
                                break;
                        }
                    }
                    break;
                case 7: // Image url
                    if ($this->showimage) {
                        switch ($this->level) {
                            case 2: // Image
                                $this->image["url"] .= $cdata;
                                break;
                        }
                    }
                    break;
            }
        }
    }

    function _handle_close_element(&$p, &$element)
    {
        if (!isset($outbuffer)) {
            $outbuffer = "";
        } ;
        // Closing elements for all versions.
        // Because the elements can appear in orders differing from each other
        // the output is now created at the close of each of the critical elements.
        $element = strtolower($element);
        static $cnt;
        switch ($element) {
            case 'channel': // major elements -- define closing event
                // put channel information on the top. This should work even if the channel close element
                // occurs before the item tags
                if ($this->showimage && $this->image['link']) {
                    $outbuffer .= '<p><a href="' . $this->image["link"] . '" target="_blank">' . mxCreateImage($this->image['url'], $this->image['title']) . '</a></p>';
                }
                $outbuffer .= '<p><a href="' . $this->channel["link"] . '" target="_blank">' . $this->channel["title"] . '</a></p>';
                if ($this->showdesc)
                    $outbuffer .= $this->channel["desc"];
                $outbuffer .= $this->output;
                $this->output = $outbuffer;
                $this->channel["link"] = "";
                $this->channel["title"] = "";
                $this->channel["desc"] = "";
                $this->channelclosed = true;
                $outbuffer = "";
                break;
            case 'image': // Image tags closed
                // A little tricky here as image is often part of the channel container tags
                // Some feeds close the channel element and then provide the image.
                // Image has its own close element, unless the values are provided by Attributes!
                if ($this->channelclosed) {
                    // Put the image on the top
                    if ($this->showimage && $this->image["link"]) {
                        $outbuffer .= '<p><a href="' . $this->image["link"] . '" target="_blank">' . mxCreateImage($this->image['url'], $this->image['title']) . '</a></p>';
                        $this->output = $outbuffer . $this->output;
                        $outbuffer = "";
                    }
                }
                break;
            case 'item':
                // Each item has its own close element
                if ($this->flag) {
                    // Initialize item list
                    $this->output .= "<ul>";
                    $this->flag = false;
                    $cnt = 0;
                }
                if ($this->limit > $cnt || !$this->limit) {
                    $this->output .= '<li><a href="' . $this->item["link"] . '" target="_blank">' . $this->item["title"] . '</a></li>';
                    if ($this->showdesc)
                        $this->output .= $this->item["desc"];
                    if ($this->limit > 0) $cnt++;
                }
                $this->item["link"] = "";
                $this->item["title"] = "";
                $this->item["desc"] = "";
                break;
            default: // ignore all other close elements
                break;
        }
    }

    function _load_file()
    {
        // Get the raw feed from the URL. Because this uses a URL as the feed source
        // it can be used to process an RSS/XML feed from any web site, including local.
        $this->contents = '';

        switch (true) {
            case function_exists('curl_init'):
                // wenn möglich die curl-Biblithek verwenden
                $ch = curl_init();
                $timeout = 20; // set to zero for no timeout
                curl_setopt ($ch, CURLOPT_URL, $this->URL);
                curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                $file_contents = curl_exec($ch);
                curl_close($ch);
                $this->encoding = $this->_get_encoding($file_contents);
                $this->contents = $file_contents;
                break;

            case mxIniGet('allow_url_fopen') && $data = @file($this->URL):
                $this->contents = implode('', $data); // Put data into an array
                $this->encoding = $this->_get_encoding($this->contents);
                break;

            case function_exists('fsockopen'):
                // ansonsten fsockopen() verwenden
                $rdf = parse_url($this->URL);
                $rdf['host'] = strtolower($rdf['host']);
                $errno = 0;
                $errstr = '';
                $fp = fsockopen($rdf['host'], 80, $errno, $errstr, 15);
                if (!$fp) {
                    return;
                }
                if (!isset($rdf['query'])) {
                    $rdf['query'] = "";
                }
                fputs($fp, "GET " . $rdf['path'] . "?" . $rdf['query'] . " HTTP/1.0\r\n");
                fputs($fp, "HOST: " . $rdf['host'] . "\r\n\r\n");
                $string = "";
                while (!feof($fp)) {
                    $pagetext = fgets($fp, 300);
                    $string .= trim($pagetext);
                }
                fputs($fp, "Connection: close\r\n\r\n");
                fclose($fp);
                $this->encoding = $this->_get_encoding($string);
                if (preg_match('#<.*>#s', $string, $matches)) {
                    $this->contents = $matches[0];
                }
                break;

            default:
                trigger_error('no url-wrapper found for: ' . $this->URL, E_USER_NOTICE);
        }
    }

    function _get_rss_version()
    {
        // Set the version state
        if (strpos($this->contents, 'version="0.9')) $this->rss_version = 9;
        elseif (strpos($this->contents, "rdf:")) $this->rss_version = 1;
        elseif (strpos($this->contents, 'version="2.0"')) $this->rss_version = 2;
    }

    function _get_encoding($data)
    {
        preg_match('|encoding=\"(.+)\".{0,1}\?>|', $data, $encoding);
        $encoding[1] = (empty($encoding[1])) ? 'utf-8' :$encoding[1];
        return $encoding[1];
    }
} // end of class

?>