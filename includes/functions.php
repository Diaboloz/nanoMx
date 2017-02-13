<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: PragmaMx $
 * $Revision: 171 $
 * $Date: 2016-06-29 13:59:03 +0200 (mer. 29 juin 2016) $
 *
 * based on eBoard v1.1, rewrite and modified by
 * vkpMx-Developer-Team (http://www.maax-design.de)
 * Original source-code made by the XMB-team
 * (XMB-Forum, http://www.xmbforum.com), modified for nukestyle-systems
 * by Trollix (XForum, http://www.trollix.com).
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * mxb_outputhandler()
 *
 * @param mixed $mxboard_output
 * @return
 */
function mxb_outputhandler($mxboard_output)
{
    global $ModName, $eBoardUser, $userinfo, $file, $action, $mxb_template, $title, $mxbStartTime, $mxQueryCount, $mxbQueryDiff, $showtotaltime, $color1, $color2;
    // inline-Styles in <head> verlagern
    if (preg_match('#<style[^>]*>[^>]+</style>#siU', $mxboard_output, $matches)) {
        $mxboard_output = str_replace($matches[0], '', $mxboard_output);
        pmxHeader::add($matches[0]);
    }
    // inline-Javascripte in <head> verlagern
    if (preg_match_all('#<script[^>]*\ssrc=[^>]*>.*</script>#siU', $mxboard_output, $matches)) {
        foreach($matches[0] as $eScript) {
            $mxboard_output = str_replace($eScript, '', $mxboard_output);
            pmxHeader::add($eScript);
        }
    }
    // inline-Javascripte in <head> verlagern
    if (preg_match_all('#<meta[^>]*>#i', $mxboard_output, $matches)) {
        foreach($matches[0] as $eScript) {
            $mxboard_output = str_replace($eScript, '', $mxboard_output);
            pmxHeader::add($eScript);
        }
    }
    // Javascripte und styles aus dem mxBoard Template in <head> verlagern
    if (!empty($mxb_template['head'])) {
        pmxHeader::add($mxb_template['head']);
    }
    // Standard Javascript
    pmxHeader::add_script(MXB_BASEMODJS . '/common.js');
    // die prioritäten-Farben, von <font> nach <span style> ändern
    if ($color1 && $color2 && preg_match_all('#(<font color=[\'"](' . str_replace('#', '\#', preg_quote($color1) . '|' . preg_quote($color2)) . ')[\'"]>)(.*)(</font>)#isU', $mxboard_output, $matches)) {
        foreach ($matches[0] as $i => $search) {
            $repl = '<span style="color: ' . $matches[2][$i] . ';">' . $matches[3][$i] . '</span>';
            $mxboard_output = str_replace($search, $repl, $mxboard_output);
        }
    }
    // Ausgabe neu zusammensetzen
    switch (true) {
        case defined('MXB_IS_USERPAGE');
            include_once(PMX_SYSTEM_DIR . DS . 'mx_userfunctions.php');
            $view = new pmxUserPage(MXB_IS_USERPAGE);
            $view->subtitle = $title;
            $view->tabname = 'mxb' . $ModName;

            /* Tabs */
            $tabitems['home'] = array('link' => MXB_BM_INDEX0, 'caption' => _TEXTINDEX, 'title' => _TEXTINDEX);
            $tabitems['viewpro'] = array('link' => MXB_BM_MEMBER1 . 'action=viewpro&amp;member=' . MXB_IS_USERPAGE, 'caption' => _TEXTVIEWPRO, 'title' => _TEXTVIEWPRO);
            if (MXB_IS_USERPAGE == $eBoardUser['username'] || $eBoardUser['isadmin'] || $eBoardUser['superuser']) {
                $tabitems['editpro'] = array('link' => MXB_BM_MEMBER1 . 'action=editpro&amp;member=' . MXB_IS_USERPAGE, 'caption' => _TEXTEDITPRO, 'title' => _TEXTEDITPRO);
            }
            if ($file == 'search' || (is_object($userinfo) && $userinfo->username == MXB_IS_USERPAGE && $userinfo->postnum > 0)) {
                $tabitems['search'] = array('link' => MXB_BM_SEARCH1 . 'member=' . MXB_IS_USERPAGE, 'caption' => _MEMPOSTS . '(' . _TEXTSEARCH . ')', 'title' => _SEARCHUSERMSG);
            }

            /* Template initialisieren */
            $template = load_class('Template');
            $template->init_path(__FILE__);
            $template->assign('action', $action);
            $template->assign('content', $mxboard_output);
            $template->assign('tabitems', $tabitems);

            $mxboard_output = $template->fetch('userpage.html');

            $mxboard_output = $view->fetch($mxboard_output);
            break;

        default:
            $mxboard_output = $mxb_template['top'] . $mxboard_output . $mxb_template['bottom'];
            if ($showtotaltime != 'off' && defined('_TEXTSECONDS')) {
                $totaltime = _VKPBENCH1 . number_format(round((microtime(true) - $mxbStartTime), 4), 3, ',', '') . _VKPBENCH2 . ($mxQueryCount - $mxbQueryDiff) . _VKPBENCH3;
                // dummy ersetzen ;)
                $mxboard_output = str_replace('<!-- mxBoard-totaltime-' . $mxbStartTime . ' -->', $totaltime, $mxboard_output);
            }
    }

    return $mxboard_output;
}

function mxbShowIconesBB()
{
    pmxHeader::add_style(MXB_BASEMODIMG . '/bbcode/bbcode_editor.css');
    $str = '
            <div class="bbcBar" style="width: 370px;">
                <ul>
                    <li><a onclick="UnbEditorDoCmd(event, \'bold\'); return false;" title="' . _BBBOLD . '" class="bold"></a></li>
                    <li><a onclick="UnbEditorDoCmd(event, \'italic\'); return false;" title="' . _BBITALIC . '" class="italic"></a></li>
                    <li><a onclick="UnbEditorDoCmd(event, \'underline\'); return false;" title="' . _BBUNDERLINE . '" class="underline"></a></li>          
                </ul>
                <ul>
                    <li><a onclick="UnbEditorDoCmd(event, \'list_1\'); return false;" title="' . _BBNUMLIST . '" class="orderedlist"></a></li>
                    <li><a onclick="UnbEditorDoCmd(event, \'list\'); return false;" title="' . _BBLIST . '" class="unorderedlist"></a></li>
                    <li class="separator"><!-- --></li> 
                    <li><a onclick="UnbEditorDoCmd(event, \'line\'); return false;" title="' . _BBLINE . '" class="horizontalrule"></a></li>
                </ul>
                <ul>
                    <li><a onclick="UnbEditorDoCmd(event, \'center\'); return false;" title="' . _BBCENTER . '" class="justifycenter"></a></li>
                    <li><a onclick="UnbEditorDoCmd(event, \'list_A\'); return false;" title="' . _BBCHARLIST . '" class="justifyright"></a></li>
                </ul>
                <ul>
                    
                    <li><a onclick="UnbEditorDoCmd(event, \'url\'); return false;" title="' . _BBURL . '" class="link"></a></li>
                    <li><a onclick="UnbEditorDoCmd(event, \'img\'); return false;" title="' . _BBIMG . '" class="image"></a></li>
                    <li><a onclick="UnbEditorDoCmd(event, \'email\'); return false;" title="' . _BBEMAIL . '" class="email"></a></li>
                </ul>
                <ul>
                    <li><a onclick="UnbEditorDoCmd(event, \'quote\'); return false;" title="' . _BBQUOTE . '" class="bbcQuote"></a></li>
                    <li><a onclick="UnbEditorDoCmd(event, \'code\'); return false;" title="' . _BBXCODE . '" class="bbcCode"></a></li>
                </ul>        
                <br class="clear" />
        </div>
    ';
    return $str;
}
// -----------------------------------------------------------------------------
// Display smilley table on posts
// -----------------------------------------------------------------------------
function mxbShowTableSmilies()
{
    global $table_smilies, $smiliesrownumber, $smilieslinenumber;
    $str = '';
    $totalsmilies = intval($smilieslinenumber * $smiliesrownumber);
    $querysmilie = sql_query("SELECT * FROM $table_smilies WHERE type='smiley' ORDER BY id limit 0, $totalsmilies");

    $l = 'on';
    $count = 1;

    while ($smilie = sql_fetch_object($querysmilie)) {
        $pic = '<td>
            <img src="' . MXB_BASEMODIMG . '/' . $smilie->url . '" onclick="UnbInsertText(\' ' . $smilie->code . ' \'); return false;" alt="' . $smilie->code . '" title="' . $smilie->code . '" border="0" class="mxb-input-image"/>
            </td>';

        if ($l == 'on') {
            if ($count == 1) {
                $str .= '<tr align="center">';
            }
            $count++;
            $str .= $pic;
        } else {
            $str .= $pic . "</tr>";
        }

        if (($l == 'on' && $count < $smiliesrownumber) || $count == 1) {
            $l = 'on';
        } else {
            $l = 'off';
            $count = 1;
        }
    }

    if ($l != 'on') {
        $str .= "<td>&nbsp;</td></tr>";
    }

    if ($str) {
        $strmore = '<br/>[<a href=\"javascript:UnbPopup(\'modules.php?name=' . MXB_MODNAME . '&file=popsmilies&theme=' . MX_THEME . '\',\'smilies\',750,430);\">' . _MORE_SMILIES . '<\/a>]';
        $str = '
            <table style="border:none;margin:1;padding:1;text-align:center" >' . $str . '</table>
            <script type="text/javascript">
            /* <![CDATA[ */
            document.write("' . trim(preg_replace('#\s+#', ' ', $strmore)) . '")
            /* ]]> */
            </script>
            ';
    }
    return $str;
}

function mxbFixBbCodeQuote($message)
{
    // falsche führende und schliessende Quotes entfernen.
    while (substr($message, -7) == '[quote]') {
        $message = substr($message, 0, -7);
    } while (substr($message, 0, 8) == '[/quote]') {
        $message = substr($message, 8);
    }
    // prüfen ob alle Quotes geöffnet/geschlossen
    $quote_open = preg_match_all('#(\[quote[^\]]*\])#is', $message, $dummy);
    $quote_close = preg_match_all('~(\[/quote\])~is', $message, $dummy);
    // alle Quotes korrekt schliessen/öffnen
    if ($quote_open > $quote_close) {
        $message .= str_repeat('[/quote]', $quote_open - $quote_close);
    } elseif ($quote_close > $quote_open) {
        $message = str_repeat('[quote]', $quote_close - $quote_open) . $message;
    }
    $message = str_replace('[quote][/quote]', '', $message);
    // falsche führende und schliessende codes entfernen.
    while (substr($message, -6) == '[code]') {
        $message = substr($message, 0, -6);
    } while (substr($message, 0, 7) == '[/code]') {
        $message = substr($message, 7);
    }
    // prüfen ob alle codes geöffnet/geschlossen
    $code_open = preg_match_all('~(\[code(?:=[^\]]+)?\])~is', $message, $dummy);
    $code_close = preg_match_all('~(\[/code\])~is', $message, $dummy);
    // alle codes "entwerten", wenn ungültige enthalten
    if ($code_open != $code_close)
        $message = str_replace('code]', 'code&#' . ord(']') . ';', $message);
    // falsche führende und schliessende phps entfernen.
    while (substr($message, -5) == '[php]') {
        $message = substr($message, 0, -5);
    } while (substr($message, 0, 6) == '[/php]') {
        $message = substr($message, 6);
    }
    // prüfen ob alle phps geöffnet/geschlossen
    $php_open = preg_match_all('~(\[php(?:=[^\]]+)?\])~is', $message, $dummy);
    $php_close = preg_match_all('~(\[/php\])~is', $message, $dummy);
    // alle phps "entwerten", wenn ungültige enthalten
    if ($php_open != $php_close) {
        $message = str_replace('php]', 'php&#' . ord(']') . ';', $message);
    }

    return $message;
}

/**
 * mxbPostifyGetsize()
 * nur Hilfsfunktion zu mxbPostify()
 *
 * @param array $args
 * @return string
 */
function mxbPostifyGetsize($args)
{
    list($tmp, $size, $text) = $args;

    $sizes = array(1 => 'xx-small', // winzig
        2 => 'x-small', // sehr klein
        3 => 'small', // klein
        4 => 'medium', // mittel
        5 => 'large', // groß
        6 => 'x-large', // sehr groß
        7 => 'xx-large', // riesig
        );
    if (!$text || !$size || !isset($sizes[$size])) {
        return '';
    }
    return '<span style="font-size:' . $sizes[$size] . '">' . $text . '</span>';
}

function mxbPostify($message, $allowhtml, $allowsmilies, $allowbbcode, $allowimgcode, $smileyoff = '', $bbcodeoff = '')
{
    global $table_smilies, $dateformat, $timecode, $timeoffset, $max_w, $max_h, $showeditedby;

    $wwrap = 75;

    $editmessage = '';
    // Editierhinweis immer behandeln
    if (preg_match_all('#\[\[editby=([^=]+)=([0-9]+)\]\]#i', $message, $matches)) {
        foreach($matches[0] as $i => $match) {
            $message = trim(str_replace($match, '', $message));
            if ($matches[2][$i] <= time()) {
                $edits[$matches[2][$i]] = $matches[1][$i];
            }
        }
        // wenn edits vorhanden und diese auch angezeigt werden sollen
        if (isset($edits) && !empty($showeditedby)) {
            krsort($edits);
            $edname = urldecode(current($edits));
            $edtime = current(array_keys($edits));
            $ydate = @gmdate($dateformat, (int)$edtime + ($timeoffset * 3600));
            $ytime = @gmdate($timecode, (int)$edtime + ($timeoffset * 3600));
            $editmessage = '<div class="notice">[' . _TEXTEDITON . ' ' . $ydate . ' ' . _TEXTAT . ' ' . $ytime . ' ' . _TEXTBY . ' ' . $edname . ']</div>';
        }
    }

    if (empty($message)) {
        return $editmessage;
    }
    // dieses bescheuerte yes/on & no/off Problem fixen, wie kann man so nen Scheiss coden..
    $allowhtml = ($allowhtml == 'yes' || $allowhtml == 'on') ? true : false;
    $allowsmilies = ($allowsmilies == 'yes' || $allowsmilies == 'on') ? true : false;
    $allowbbcode = ($allowbbcode == 'yes' || $allowbbcode == 'on') ? true : false;
    $allowimgcode = ($allowimgcode == 'yes' || $allowimgcode == 'on') ? true : false;
    $smileyoff = ($smileyoff == 'yes' || $smileyoff == 'on') ? true : false;
    $bbcodeoff = ($bbcodeoff == 'yes' || $bbcodeoff == 'on') ? true : false;
    $message = str_replace("\r", '', $message);
    // remove any lower ASCII control character except HT (0x09) and LF (0x0A)
    $message = preg_replace('_[\x00-\x08\x0B-\x1F]_', "\xEF\xBF\xBD", $message);
    // add a new-line at the beginning and ensure there's one at the end
    // some reg-exps need this to match the first character
    $message = "\r" . $message . "\r";

    if (!$allowhtml) {
        if ($bbcodeoff || !$allowbbcode) {
            $message = str_replace('&', '&amp;', $message);
        }
        $message = str_replace('<', '&lt;', $message);
        $message = str_replace('>', '&gt;', $message);
    }
    // bereits hier, wegen smilieproblem und [code] [quote] [php] Problemen (AE)
    $message = mxbSecureHtml($message);
    if (!$smileyoff && $allowsmilies) {
        $message = str_replace('&#41;', ')', $message);
        // / nur wenn nicht bereits statisch vorhanden AE
        static $allsmiles;
        if (!isset($allsmiles)) {
            $allsmiles = array();
            // Abfrage nach länge des Codes absteigend sortieren, damit kuze Codes, z.B. :o erst später ersetzt werden
            $querysmilie = sql_query("SELECT code, url FROM $table_smilies WHERE type='smiley' ORDER BY LENGTH(code) DESC");
            while ($smilie = sql_fetch_object($querysmilie)) {
                // den code im alt attribut mit ord() behandeln, damit das nicht auch geparst wird

				// TODO: e-modifier 
				/* e-modifyer */
               // $img = '<img src="' . MXB_BASEMODIMG . '/' . $smilie->url . '" alt="' . preg_replace('#[[:punct:]]#e', '"&#".ord("$0").";"', $smilie->code) . '" />';
                $img = '<img src="' . MXB_BASEMODIMG . '/' . $smilie->url . '" alt="' . preg_replace_callback('#[[:punct:]]#', function($m){ return "&#".ord($m[0]);}, $smilie->code) . '" />';
                $allsmiles[$smilie->code] = $img;
            }
        }
        if (!empty($allsmiles)) {
            $message = str_replace(array_keys($allsmiles), array_values($allsmiles), $message);
        }
    }

    if (!$bbcodeoff && $allowbbcode) {
        $message = mxbFixBbCodeQuote($message);

        preg_match_all("!(\[php\]|<\?)(.*?)(\[/php\]|\?>)!is", $message, $phpmatches);
        if (isset($phpmatches[2])) {
            foreach($phpmatches[2] as $i => $phpmatch) {
                $message = str_replace($phpmatch, '##phpmatch_' . $i . '##', $message);
            }
        }

        preg_match_all("!(\[code\]|<\?)(.*?)(\[/code\]|\?>)!is", $message, $codematches);
        if (isset($codematches[2])) {
            foreach($codematches[2] as $i => $codematch) {
                $message = str_replace($codematch, '##codematch_' . $i . '##', $message);
            }
        }
        // eventuelle Zeilenumbrüche bei bestimmten bbCode-Tags entfernen, damit nl2br keine zusätzlichen anfügt...
        $message = preg_replace('#[[:cntrl:]]?(\[/?(?:p|li|center|blink|strike|h[3-7]|marquee|quote|list(?:=[1Aa])?)\])[[:cntrl:]]?#is', '\1', $message);

        $message = preg_replace_callback('#\[size=([1-7])\](.*)(\[\/size\]|$)#isU', 'mxbPostifyGetsize', $message);
        $message = preg_replace("/\[color=([^\[]*)\](.*)(\[\/color\]|$)/isU", '<span style="color:\1;background-color:transparent;">\2</span>', $message);
        $message = preg_replace("/\[font=([^\[]*)\](.*)(\[\/font\]|$)/isU", '<span style="font-family:\1">\2</span>', $message);
        $message = preg_replace("/\[align=([^\[]*)\](.*)(\[\/align\]|$)/isU", '<p align="\1">\2</p>', $message);
        // unterstrichen extra behandeln
        $message = preg_replace('#\[([u])\]#is', '<u style="text-decoration: underline;">', $message);
        // Formatierungen, fett, unterstrichen, kursiv, absatz
        $message = preg_replace('#\[(/?[abiup])\]#is', '<\1>', $message);
        $message = preg_replace('#\[(/?(?:center|blink|strike|li|strong|h[1-7]|sup|marquee))\]#is', '<\1>', $message);
        $message = preg_replace("/(^|[>[:space:]\n])([[:alnum:]]+):\/\/([^[:space:]]*)([[:alnum:]#?\/&=])([<[:space:]\n]|$)/mi", "\\1<a rel=\"nofollow\" href=\"\\2://\\3\\4\" target=\"_blank\">\\2://\\3\\4</a>\\5", $message);
        $message = str_replace("[list]", "<ul type=\"square\">", $message);
        $message = str_replace("[/list]", "</ul>", $message);
        $message = preg_replace('#\[list=([1Aa])\]#i', '<ol type="\1">', $message);
        $message = preg_replace('#\[/?list=([1Aa])\]#i', '</ol>', $message);
        $message = str_replace("[*]", "<li>", $message);
        $message = str_replace("[line]", "<hr width=\"95%\" size=\"1\" noshade=\"noshade\"/>", $message);
        $message = str_replace("[hr]", "<hr width=\"95%\" size=\"1\"  noshade=\"noshade\"/>", $message);
        // [quote author=Andi link=topic=16670.msg115098#msg115098 date=1147701559]
        $message = str_replace("[quote]", '<br/><table border="0" cellspacing="1" cellpadding="3" class="bordercolor full"><tr><td class="bgcolor1"><span class="tiny">' . _TEXTQUOTE . ':</span></td></tr><tr><td class="bgcolor2"><div style="width: 100%; overflow: auto; " class="text">', $message);
        $message = str_replace("[/quote]", '</div></td></tr></table><br/>', $message);
        // [quote author=Andi link=topic=16670.msg115098#msg115098 date=1147701559]
        preg_match_all('#\[quote\s+([^\]]+)\]#is', $message, $matches);
        foreach($matches[0] as $quote) {
            if (preg_match('#author=([^\s\]]+)#i', $quote, $parts)) {
                $view = _TEXTQUOTE . ' ' . _TEXTBY . ' ' . urldecode(trim(str_replace('&nbsp;', '', $parts[1])));
                if (preg_match('#date=([^\s\]]+)#i', $quote, $parts)) {
                    $ydate = gmdate($dateformat, (int)$parts[1] + ($timeoffset * 3600));
                    $ytime = gmdate($timecode, (int)$parts[1] + ($timeoffset * 3600));
                    $view .= ', ' . _LASTREPLY1 . ' ' . $ydate . ' ' . _TEXTAT . ' ' . $ytime;
                }
                if (preg_match('#topic=([0-9]+)[^0-9\s]*([0-9]*)#i', $quote, $parts)) {
                    global $tid;
                    $target = ($parts[1] == $tid && !isset($_POST['previewpost'])) ? '_self' : '_blank';
                    $ypid = (empty($parts[2])) ? '0' : $parts[2];
                    $view = '<a href="' . MXB_BM_VIEWTHREAD1 . 'tid=' . $parts[1] . '#pid' . $ypid . '" target="' . $target . '">' . $view . '</a>';
                }
            } else {
                $view = _TEXTQUOTE;
            }
            $message = str_replace($quote, '<h4>' . $view . '</h4><blockquote><div><cite>' . _TEXTQUOTE . ':</cite>', $message);
        }

        $patterns = array();
        $replacements = array();
        $patterns[0] = "/\[url\]www.([^\[]*)\[\/url\]/i";
        $replacements[0] = "<a href=\"http://www.\\1\" target=\"_blank\">\\1</a>";
        $patterns[1] = "/\[url\]([^\[]*)\[\/url\]/i";
        $replacements[1] = "<a href=\"\\1\" target=\"_blank\">\\1</a>";
        $patterns[2] = "/\[url=([^\[]*)\]([^\[]*)\[\/url\]/i";
        $replacements[2] = "<a href=\"\\1\" target=\"_blank\">\\2</a>";
        $patterns[3] = "/\[email\]([^\[]*)\[\/email\]/i";
        $replacements[3] = "<a href=\"mailto:\\1\">\\1</a>";
        $patterns[4] = "/\[email=([^\[]*)\]([^\[]*)\[\/email\]/i";
        $replacements[4] = "<a href=\"mailto:\\1\">\\2</a>";
        if ($allowimgcode) {
            $patterns[5] = "/\[img\]([^\[]*)\[\/img\]/i";
            $replacements[5] = "<img src=\"\\1\" alt=\"\\1\"/>";
            $patterns[6] = "/\[img=([^\[]*)\]([^\[]*)\[\/img\]/i";
            $replacements[6] = "<img src=\"\\1\" alt=\"\\2\"/>";
        }

        $message = preg_replace($patterns, $replacements, $message);
    }
    $message = mxbCensorMessage($message);
    $message = nl2br(trim($message));
    // besseres Wordwrap, aus MyBB 1.4
    if (!($new_message = @preg_replace("#(?>[^\s&/<>\"\\-\.\[\]]{{$wwrap}})#u", "$0&#8203;", $message))) {
        $new_message = preg_replace("#(?>[^\s&/<>\"\\-\.\[\]]{{$wwrap}})#", "$0&#8203;", $message);
    }
    $message = $new_message;

    if (isset($phpmatches[2])) {
        foreach($phpmatches[2] as $i => $phpmatch) {
            if (!empty($allsmiles)) {
                // falsch gewandelte Smilies zurückwandeln
                $phpmatch = str_replace(array_values($allsmiles), array_keys($allsmiles), $phpmatch);
            }
            $message = str_replace('##phpmatch_' . $i . '##', $phpmatch, $message);
        }

// TODO: e-modifier 

        //$message = preg_replace("!(\[php\]|<\?)(.*?)(\[/php\]|\? >)!ise", "mxbPhpStr('\\2','" . $allowhtml . "')", $message); //<?php
        $message = preg_replace_callback("!(\[php\]|<\?)(.*?)(\[/php\]|\? >)!is", function($m){ return mxbPhpStr($m[1], $allowhtml );}, $message); //<?php
}

    if (isset($codematches[2])) {
        foreach($codematches[2] as $i => $codematch) {
            if (!empty($allsmiles)) {
                // falsch gewandelte Smilies zurückwandeln
                $codematch = str_replace(array_values($allsmiles), array_keys($allsmiles), $codematch);
            }
            $message = str_replace('##codematch_' . $i . '##', $codematch, $message);
        }

// TODO: e-modifier 

        $message = preg_replace("!(\[code\]|<\?)(.*?)(\[/code\]|\?>)!ise", "mxbCodeStr('\\2','" . $allowhtml . "')", $message); //<?php*/
        //$message = preg_replace_callback("!(\[code\]|<\?)(.*?)(\[/code\]|\?>)!is", function ($m) { return xbCodeStr($m[2],$allowhtml);}, $message); //<?php
    }
    // Die Editiernachricht anhängen, falls vorhanden...
    $message .= $editmessage;

    return $message;
}
// some parts of php code highlithting from ThWboard,
// Paul Baecher <paul@thewall.de>, Felix Gonschorek <funner@thewall.de>
// the rest added by AE
function mxbPhpStr($string, $allowhtml)
{
    // $string = trim($string);
    $string = str_replace('\"', '"', $string);

    $string = preg_replace('#<br\s*/?>#i', "\n", $string);
    $string = str_replace('&lt;', '<', $string);
    $string = str_replace('&gt;', '>', $string);
    // ttt: automatically insert < ?php if necessary
    if (!preg_match('/^\<\?(php)?/si', $string)) {
        $string = "<?php\n" . $string;
    }
    if (!preg_match('/\?\>$/s', $string)) {
        $string .= "\n?>";
    }
    $string = highlight_string($string, true);
    if (preg_match('#(&lt;\?(?:php)?<br /></font>)(.*)(\?&gt;</font>)#is', $string, $matches)) {
        $string = $matches[2] . '</font>';
    }
    $string = preg_replace('/(<font.+#)([[:alnum:]]{6})(">)/isU', "<font style=\"color: \\2;\">", $string);
    return '
    	<dl class="codebox">
    		<dt>PHP-' . _BBXCODE . ':</dt>
     		<dd>
     			<div style="width: 100%; overflow: auto; padding-left: 3px;">
     				<pre class="code">' . trim($string) . '</pre>
     			</div>
     		</dd>
     	</dl>';
}

function mxbCodeStr($string, $allowhtml)
{
    // $string = trim($string);
    $string = str_replace('\"', '"', $string);
    if ($allowhtml) {
        $string = htmlspecialchars($string, ENT_NOQUOTES);
    }
    return '
    	<dl class="codebox">
    		<dt>' . _BBXCODE . ':</dt>
     		<dd>
     			<code>' . trim($string) . '</code>
     		</dd>
     	</dl>';
}

function mxbSecureHtml($source)
{
    if (! empty($source)) {
        $search = array('|</?\s*SCRIPT\s+.*?>|si',
            '|<img.*?SCRIPT.*?>|si',
            '|</?\s*FRAME\s+.*?>|si',
            '|</?\s*OBJECT\s+.*?>|si',
            '|</?\s*META\s+.*?>|si',
            '|</?\s*APPLET\s+.*?>|si',
            '|</?\s*LINK\s+.*?>|si',
            '|</?\s*IFRAME.*?>|si',
            '|STYLE\s*=\s*"[^"]*"|si'); #<?php
        $replace = array('');
        $secure = preg_replace($search, $replace, $source);
    }
    return($source);
}

function mxbCensorMessage($message)
{
    static $tabbadwords, $replace;
    if (!isset($tabbadwords) && !isset($replace)) {
        global $table_words;
        $tabbadwords = array();
        $replace = array();
        $querycensor = sql_query("SELECT find, replace1 FROM $table_words");
        while ($censor = sql_fetch_object($querycensor)) {
            $tabbadwords[] = '|' . $censor->find . '|si';
            $tabbadwords[] = '| ' . $censor->find . '|si';
            $replace[] = '' . $censor->replace1;
            $replace[] = ' ' . $censor->replace1 . ' ';
        }
    }
    if (count($tabbadwords) && count($replace)) {
        $message = preg_replace($tabbadwords, $replace, $message);
    }
    return $message;
}

function mxbIsPostOwner($author, $thisuser)
{
    if (mxbIsAnonymous()) {
        return false;
    }
    if ($author == $thisuser) {
        return true;
    } else {
        return false;
    }
}

function mxbIsModeratorInForum($forums)
{
    global $eBoardUser, $table_forums;
    // ungültige Daten, oder kein angem. Benutzer
    if (empty($forums) || empty($eBoardUser['username'])) {
        return false;
    }
    // Admins sind immer auch Moderatoren
    if ($eBoardUser['superuser'] || $eBoardUser['isadmin']) {
        return true;
    }
    // wenn $forums ein Foren-ID ist...
    if (!is_array($forums) && is_numeric($forums)) {
        // $forums als integer $fid verwenden und die Moderatorenliste ermitteln
        $query = sql_query("SELECT fid, moderator FROM $table_forums WHERE moderator LIKE '%" . substr($eBoardUser['username'], 0, 25) . "%' AND fid=" . intval($forums));
        $forums = sql_fetch_object($query);
        // wenn keine Moderatorenliste, aber kein Super-Mod
        if (empty($forums->moderator) && $eBoardUser['status'] != "Super Moderator") {
            return false;
        }
    }
    // wenn nur für Admins das Schreiben erlaubt ist, dann kann der Moderator auch nicht moderieren
    $pperm = explode('|', $forums->postperm);

    if ($pperm[0] == '2' || $pperm[1] == '2') {
        return false;
    }
    // in Moderatorenliste, oder Super-Mod
    if (mxbIsInModeratorList($forums) || $eBoardUser['status'] == "Super Moderator") {
        return true;
    }

    return false;
}

function isOnStaff($status)
{
    return ($status == 'Administrator' || $status == 'Super Moderator' || $status == 'Moderator');
}

function isTrusted($forum)
{
    global $eBoardUser;

    if ($eBoardUser['isadmin']) {
        return true;
    }
    if (empty($forum->private) && empty($forum->userlist)) {
        return true;
    }
    if ($forum->private == "user" && empty($forum->userlist) && !mxbIsAnonymous()) {
        return true;
    }
    if (isInUserList($forum)) {
        return true;
    }
    if (mxbIsInModeratorList($forum)) {
        return true;
    }
    if ($forum->private == "staff" && isOnStaff($eBoardUser['status']) && empty($forum->userlist) && !mxbIsAnonymous()) {
        return true;
    }

    return false;
}

function isInUserList($forum)
{
    global $eBoardUser;
    $forum->userlist = trim($forum->userlist);
    if (empty($forum->userlist) || mxbIsAnonymous()) {
        return false;
    }
    $users = preg_split('#\s*,\s*#', trim($forum->userlist, ', '));
    return in_array($eBoardUser['username'], $users);
}

function mxbIsInModeratorList($forum)
{
    global $eBoardUser, $table_members;
    $forum->moderator = trim($forum->moderator);
    if (mxbIsAnonymous() || empty($forum->moderator)) {
        return false;
    }
    $forum_moderator = preg_split('#\s*,\s*#', trim($forum->moderator, ', '));
    $return = in_array($eBoardUser['username'], $forum_moderator);
    if ($return && !isOnStaff($eBoardUser['status'])) {
        // in Liste und kein Admin etc., aber mod-Status nicht in Membertabelle eingetragen >> ändern
        sql_query("UPDATE $table_members SET status='Moderator' WHERE username='" . substr($eBoardUser['username'], 0, 25) . "'");
        $eBoardUser['status'] = 'Moderator';
    }
    return $return;
}

function mxbPrivateCheck($forum)
{
    global $eBoardUser;
    if ($eBoardUser['isadmin'] || $eBoardUser['status'] == "Super Moderator") {
        return true;
    }
    if (empty($forum->private) && empty($forum->userlist)) {
        return true;
    }
    if ($forum->private == "user" && empty($forum->userlist) && !mxbIsAnonymous()) {
        return true;
    }
    if (!mxbIsAnonymous() && mxbIsInModeratorList($forum)) {
        return true;
    }
    if (!mxbIsAnonymous() && isInUserList($forum)) {
        return true;
    }

    return false;
}

function mxbListForum($forum, $tid, $lastthreadsubject)
{
    global $table_threads, $lastpostsubj, $lastpostsubjchars, $timeoffset, $lastvisitdate, $hideprivate, $bgcolor1, $bgcolor2, $timecode, $dateformat;
    global $eBoardUser, $tablespace, $borderwith;

    if ($forum->lastpost_at) {
        if ($lastpostsubj == 'on') {
            $subject = strip_tags(stripslashes($lastthreadsubject));
            $shortsubject = (strlen($subject) > $lastpostsubjchars) ? substr($subject, 0, $lastpostsubjchars) . '...' : $subject;
            $shortsubject = '<a href="' . MXB_BM_VIEWTHREAD1 . 'tid=' . $tid . '">' . $shortsubject . '</a><br/>';
            $subjectwidth = "190px";
        } else {
            $shortsubject = '';
            $subjectwidth = "100%";
        }

        $lastpostdate = gmdate($dateformat, $forum->lastpost_at + ($timeoffset * 3600));
        $lastposttime = gmdate($timecode, $forum->lastpost_at + ($timeoffset * 3600));

        $lastpost = '';
        $lastposticon = '';
        if ($lastpostsubj != 'on') {
            $lastposticon = '<a href="' . MXB_BM_VIEWTHREAD1 . 'tid=' . $tid . '"><i class="fa fa-share-square-o"></i></a>';
        }
        $lastpost .= _TEXTLE . '&nbsp;' . $lastpostdate . ' ' . _TEXTAT . '&nbsp;' . $lastposttime . '<br />' . _TEXTBY . '&nbsp;' . mxb_link2profile($forum->lastpost_by_true, $forum->lastpost_by) . $shortsubject . '&nbsp;' . $lastposticon . '';
    } else {
        $lastpost = _TEXTNEVER;
    }

    if (!empty($forum->lastpost_at) && $lastvisitdate < $forum->lastpost_at) {
        $folder = mxbGetImage('forum_unread.png', false, false, true);
    } else {
        $folder = mxbGetImage('forum_read.png', false, false, true);
    }

    if (!empty($forum->moderator)) {
        $modz = preg_split('#\s*,\s*#', trim($forum->moderator, ', '));
        $forum->moderator = '';
        for($num = 0; $num < count($modz); $num++) {
            $thismod = mxb_link2profile($modz[$num]);
            if ($num == count($modz) - 1) {
                $forum->moderator .= $thismod;
            } else {
                $forum->moderator .= "$thismod, ";
            }
        }
        $forum->moderator = '<br />(' . _TEXTMODBY . '<em>' . $forum->moderator . '</em>)';
    } else {
        $forum->moderator = '';
    }

    if (!empty($forum->description)) {
        $forum->description = '<br />' . $forum->description;
    } else {
        $forum->description = '';
    }

    $str = '
    <ul class="topiclist">
    	<li>
				<div class="forumbar">
					<div class="inner">
						<dl class="icon" style="background-image: url(' . $folder . '); background-repeat: no-repeat;">
							<dt title="' . _TEXTMESSSLV . '">
								<a href="' . MXB_BM_FORUMDISPLAY1 . 'fid=' . $forum->fid . '" class="forumtitle">' . $forum->name . '</a>
								' . $forum->description . '
								' . $forum->moderator . '
							</dt>
							<dd class="lastpost">
								<span>
									<dfn>' . _TEXTLASTPOST . '</dfn>
									' . $lastpost . '
								</span>
							</dd>
							<dd class="topics">' . $forum->threads . '<dfn>' . _TEXTTOPICS . '</dfn></dd>
							<dd class="posts">' . $forum->posts . '<dfn>' . _TEXTPOSTS . '</dfn></dd>
						</dl>
					</div>
				</div>
    	</li>
    </ul>';

    $fmods = '';
    return $str;
}

function mxbUserTracking($trackingfid, $trackingtime, $fid)
{
    global $table_forums;

    if ($trackingfid != $fid) {
        if ($trackingtime < (time() - 3600)) {
            $timeinforum = 0;
        } else {
            $timeinforum = time() - $trackingtime;
        }

        if ($trackingfid != 0) {
            sql_query("UPDATE $table_forums SET totaltime=totaltime+'$timeinforum' WHERE fid='" . intval($trackingfid) . "'");
        }
        if (empty($fid)) {
            $fid = 0;
        }
        $trackingtime = time();
        $trackingfid = $fid;
    } else {
        $timeinforum = 0;
    }
    $out['trackingfid'] = $trackingfid;
    $out['trackingtime'] = $trackingtime;
    $out['timeinforum'] = $timeinforum;

    return $out;
}

function mxbIsAnonymous()
{
    global $eBoardUser;
    return ($eBoardUser['username'] == MXB_ANONYMOUS || (empty($eBoardUser['username'])));
}

function mxbPostingAllowed($forum, $what)
{
    global $eBoardUser, $noreg;

    $permissions = explode('|', $forum->postperm);

    $what = ($what == 'newpost') ? 0 : 1;

    if (mxbIsAnonymous()) {
        return($forum->guestposting == 'yes' &&
            // $noreg == 'on' &&
            $permissions[$what] == '1' &&
            empty($forum->private) &&
            empty($forum->userlist)
            );
    }
    // ("1", "Normal");
    // ("2", "Admins");
    // ("3", "Admins/Mods");
    // ("4", "Kein Posten");
    $allow = false;
    if (empty($permissions[$what]) || $permissions[$what] == '1') {
        $allow = true;
    } elseif ($permissions[$what] == '2' && $eBoardUser['isadmin']) {
        $allow = true;
    } elseif ($permissions[$what] == '3' && mxbIsModeratorInForum($forum)) {
        $allow = true;
    } elseif ($permissions[$what] == '4') {
        $allow = false;
    }

    if (!empty($forum->private) || !empty($forum->userlist)) {
        if (isTrusted($forum)) {
            $private = true;
        } else {
            $private = false;
        }
    } else {
        $private = true;
    }

    return ($allow && $private);
}
// aktualisiert die Anzahl der geshriebenen Posts eines Users
function mxbRepairUserPostNum($username)
{
    global $table_members, $table_posts, $table_threads;
    $query = sql_query("SELECT COUNT(tid) AS c FROM $table_threads WHERE author='" . substr($username, 0, 25) . "'");
    $c_threads = sql_fetch_assoc($query);
    $query = sql_query("SELECT COUNT(pid) AS c FROM $table_posts WHERE author='" . substr($username, 0, 25) . "'");
    $c_posts = sql_fetch_assoc($query);
    $all = intval($c_threads['c']) + intval($c_posts['c']);
    sql_query("UPDATE $table_members SET postnum=" . intval($all) . " WHERE username='" . substr($username, 0, 25) . "'");
    return $all;;
}
// einen bestimmten User aus Moderatoren und Useraccessliste entfernen
function mxbCleanBoardAccess($thisuser)
{
    global $table_forums;
    $query = sql_query("SELECT fid, moderator, userlist
                              FROM $table_forums
                              WHERE moderator LIKE '%" . $thisuser . "%'
                              OR userlist LIKE '%" . $thisuser . "%' ");
    while ($forum = sql_fetch_object($query)) {
        $arr = preg_split('#\s*,\s*#', trim($forum->moderator, ', '));
        if (in_array($thisuser, $arr)) {
            $arr = array_flip($arr);
            unset($arr[$thisuser]);
            $arr = trim(implode(',', array_keys($arr)));
            sql_query("UPDATE $table_forums SET moderator='" . $arr . "' WHERE fid=" . intval($forum->fid));
        }
        $arr = preg_split('#\s*,\s*#', trim($forum->userlist, ', '));
        if (in_array($thisuser, $arr)) {
            $arr = array_flip($arr);
            unset($arr[$thisuser]);
            $arr = implode(',', array_keys($arr));
            $ex = '';
            if (empty($arr)) {
                // Achtung, falls Accessliste leer ist, wird Forum für alle sichtbar,
                // deswegen private auf Staff-Only stellen
                $ex = ", private='staff'";
            }
            sql_query("UPDATE $table_forums SET userlist='" . $arr . "'" . $ex . " WHERE fid=" . intval($forum->fid));
        }
    }
}
// prüfen, ob ein bestimmter User in einem beliebigen Forum als Moderator eingetragen ist
function mxbIsModerator($thisuser)
{
    global $table_forums;
    static $cache;
    if (isset($cache[$thisuser])) {
        return $cache[$thisuser];
    }
    $query = sql_query("SELECT moderator
                              FROM $table_forums
                              WHERE moderator LIKE '%" . addslashes(substr($thisuser, 0, 25)) . "%'");
    while ($forum = sql_fetch_object($query)) {
        $mods = preg_split('#\s*,\s*#', trim($forum->moderator, ', '));
        if (in_array($thisuser, $mods)) {
            $cache[$thisuser] = true;
            return true;
        }
    }
    $cache[$thisuser] = false;
    return false;
}

function mxbGetRepairedStatus($userinfo)
{
    global $table_members, $prefix;

    switch (true) {
        case is_object($userinfo):
            $uname = $userinfo->username;
            $status = $userinfo->status;
            break;
        case is_array($userinfo):
            $uname = $userinfo['username'];
            $status = $userinfo['status'];
            break;
        default:
            $uname = '';
            $status = 'Guest';
    }

    static $cache;
    if (isset($cache[$uname])) {
        return $cache[$uname];
    }

    if ($status == 'Administrator') {
        $query = sql_query("SELECT radminforum, radminsuper FROM " . $prefix . "_authors WHERE aid ='" . addslashes(substr(trim($uname), 0, 25)) . "'");
        $temp = sql_fetch_assoc($query);
        if ((empty($temp['radminforum']) && empty($temp['radminsuper']))) {
            // wenn keine Adminrechte gefunden, dann ist nix mit Admin
            sql_query("UPDATE $table_members SET status='Moderator' WHERE username='" . substr($uname, 0, 25) . "'");
            // auf Moderator setzen, weil als nächster Schritt, dies nochmal geprüft wird
            $status = 'Moderator';
        }
    }

    if ($status == 'Moderator' && !mxbIsModerator($uname)) {
        sql_query("UPDATE $table_members SET status='Member' WHERE username='" . substr($uname, 0, 25) . "'");
        $status = 'Member';
    }
    $cache[$uname] = $status;
    return $status;
}

function mxbCleanUserdata($username)
{
    global $table_members, $table_whosonline, $table_threads, $table_posts;

    sql_query("DELETE FROM $table_members WHERE username='" . mxAddSlashesForSQL($username) . "'");
    sql_query("DELETE FROM $table_whosonline WHERE username='" . mxAddSlashesForSQL($username) . "'");
}

function mxbUseCaptcha($userinfo)
{
    global $captcha, $captchauser;

    switch (true) {
        case $userinfo['isadmin']:
        case $userinfo['superuser']:
        case mxbIsAnonymous() && $captcha != 'on':
        case !mxbIsAnonymous() && $captchauser != 'on':
        case mxbIsModerator($userinfo['username']):
            return false;
            // break;
            // case mxbIsAnonymous() && $captcha == 'on':
            // case !mxbIsAnonymous() && $captchauser == 'on':
            // default:
            // return true;
    }
    return true;
}

function mxbPrintCaptcha()
{
    $captcha_object = load_class('Captcha');
    /* aktivieren, damit globales 'captchauseron' nicht beachtet wird, sondern die mxBoard-Einstellung */
    $captcha_object->set_active();

    ob_start();

    ?>
   	<div class="panel bgcolor2">
			<div class="inner">
        <?php echo $captcha_object->show_complete() ?>
			</div>
		</div>
<?php
    return ob_get_clean();
}

function mxbCheckCaptcha($eBoardUser)
{
    if (!mxbUseCaptcha($eBoardUser)) {
        return true;
    }

    $captcha_object = load_class('Captcha');

    /* aktivieren, damit globales 'captchauseron' nicht beachtet wird, sondern die mxBoard-Einstellung */
    $captcha_object->set_active();

    if ($captcha_object->check($_POST)) {
        return true;
    }
    return _CAPTCHAWRONG;
}

function mxbAdminMenu()
{
    /* Template initialisieren */
    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->display('admin.menu.html');
}

/**
 * ermittelt die verfuegbaren Sprachen
 */
function mxbGetAvailableLanguages($type = 'lang')
{
    $type = ($type == 'lang') ? $type : 'install';
    $langlist = array();
    foreach (glob(MXB_ROOTMOD . 'language' . DS . $type . '-*.php', GLOB_NOSORT) as $filename) {
        preg_match('#^' . $type . '-(.+)\.php$#', basename($filename), $matches);
        $key = mxGetLanguageString($matches[1]);
        $langlist[$key] = $matches[1];
    }
    ksort($langlist);
    return $langlist;
}

function mxb_link2profile($username, $text = '', $additions = '')
{
    switch (true) {
        case !$username && !$text:
            return;
        case !$username && $additions:
            return '<span ' . $additions . '>' . $text . '</span>';
        case !$username:
            return $text;
        case MXB_ANONYMOUS == $username;
            $text = (!$text) ? $username : $text;
            return '<span ' . $additions . '>' . $text . '</span>';
        default:
            $text = (!$text) ? $username : $text;
            return '<a href="' . MXB_BM_MEMBER1 . 'action=viewpro&amp;member=' . $username . '" ' . $additions . ' title="' . _TEXTPROFOR . ' ' . $username . '">' . $text . '</a>';
    }
}

function mxb_insert_user($thisuser, $new_status = 'Member')
{
    if (!include(MXB_SETTINGSFILE)) {
        return false;
    }
    // wenn Datensatz noch nicht vorhanden >> einfügen
    // vorher aber prüfen, ob bereits in userliste oder moderatorenliste eingetragen,
    // wenn Ja, dann diese Einträge entfernen, nicht dass alte Karteileichen weiterverwendet werden...
    mxbCleanBoardAccess($thisuser);
    // Tabellenfelder und Werte zu einfügen
    $fields = "
        `username` = '" . mxAddSlashesForSQL($thisuser) . "',
        `postnum` = '0',
        `status` = '" . $new_status . "',
        `timeoffset` = '" . $globaltimeoffset . "',
        `customstatus` = '',
        `theme` = '',
        `langfile` = '',
        `tpp` = '" . $topicperpage . "',
        `ppp` = '" . $postperpage . "',
        `newsletter` = '" . $eb_defmembernews . "',
        `timeformat` = '" . $timeformat . "',
        `dateformat` = '" . $dateformat . "',
        `lastvisit` = '" . time() . "',
        `lastvisitstore` = '" . time() . "',
        `lastvisitdate` = '" . time() . "',
        `trackingfid` = '0',
        `trackingtime` = '0',
        `totaltime` = '0',
        `u2u` = 'no',
        `notifyme` = 'no',
        `notifythread` = 'yes',
        `notifypost` = 'no',
        `notifyedit` = 'no',
        `notifydelete` = 'yes',
        `keeplastvisit` = '" . $eb_defmessslv . "'
        ";
    // neuen User anfügen
    return sql_query("INSERT INTO $table_members SET $fields");
}

function mxbGetUserData($username) 
{
	global $table_members;
	$tusername=sql_real_escape_string($username);
	$result= sql_query("SELECT * FROM $table_members WHERE username='".$tusername."'");
	$user=sql_fetch_array($result);
	return $user;
}
?>