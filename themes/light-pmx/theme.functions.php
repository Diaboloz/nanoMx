<?php
/**
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Revision: 1.11 $
 * $Author: tora60 $
 * $Date: 2014-03-14 13:53:34 $
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * theme_define_placeholders()
 * Definition der jeweiligen Platzhalter und deren Ersetzungen
 *
 * @return
 */
function theme_define_placeholders()
{
    global $themesetting;
    // {LAYOUT_CLASS} wird in theme_header eingetragen, nicht hier !!
    $part[] = array("{PAGE_TITLE}", $themesetting['title']);
    $part[] = array("{CURRENTPATH}", theme_show_currentpath());
    $part[] = array("{LANGUAGEFLAGS}", theme_show_languageflags());
    $part[] = array("{LOGINFORM}", mx_theme_loginform());
    $part[] = array("{NAVBAR}", theme_get_mxmenu());
    $part[] = array("{DEBUGSERVICE}", theme_get_servicetext('debugservice'));
    $part[] = array("{SITESERVICE}", theme_get_servicetext('siteservice'));
    //$part[] = array("{PARTNERS}", theme_show_partners());
    ///$part[] = array("{FEATURED}", theme_show_featured());
    $part[] = array("{FOOTMESSAGE}", theme_show_footmsg());
    $part[] = array("{CURRENTYEAR}", date('Y'));

    $doctype_arr = mxDoctypeArray($GLOBALS['DOCTYPE']);
    list($type, $number) = explode(' ', $doctype_arr['name']);
    if (is_numeric($number)) {
        $type .= '&nbsp;' . $number;
    }

    $part[] = array("{XHTML}", $type);

    return $part;
}

/**
 * theme_define_blocks()
 * Definition der einzelnen Blockbereiche
 *
 * @return
 */
function theme_define_blocks()
{
    global $themesetting;
    static $var;
    if (isset($var)) return $var;
    // linke Blöcke
    $name = 'block_left';
    $var[$name]['container'] = 'blocks_left_loop';
    $var[$name]['function'] = 'themesidebox';
    $var[$name]['position'] = 'l';
    // obere Center-Blöcke
    $name = 'block_center_top';
    $var[$name]['container'] = 'blocks_center_top_loop';
    $var[$name]['function'] = 'thememiddlebox';
    $var[$name]['position'] = 'c';
    // untere Center-Blöcke
    $name = 'block_center_down';
    $var[$name]['container'] = 'blocks_center_down_loop';
    $var[$name]['function'] = 'thememiddlebox';
    $var[$name]['position'] = 'd';
	// rechte Bloecke
    $name = 'block_right';
    $var[$name]['container'] = 'blocks_right_loop';
    $var[$name]['function'] = 'themesidebox';
    $var[$name]['position'] = 'r';

    return $var;
}

/**
 * theme_define_content()
 * Definition des Contentbereiches inkl. der open/close-Table Funktionen
 *
 * @return
 */
function theme_define_content()
{
    global $themesetting;
    static $var;
    if (isset($var)) return $var;
    // die Funktion OpenTable()
    $var['opentabs']['OpenTable']['templatevar'] = 'opentable';
    $var['opentabs']['OpenTable']['innerreplace'] = '{OPENCLOSE_TABLE}'; // hier kann irgendwas stehen, es muss aber mit dem entsprechenden Text innerhalb der template-Datei übereinstimmen...
    // die Funktion OpenTable2()
    $var['opentabs']['OpenTable2']['templatevar'] = 'opentable2';
    $var['opentabs']['OpenTable2']['innerreplace'] = '{OPENCLOSE_TABLE_2}';
    // die Funktion OpenTableAl()
    $var['opentabs']['OpenTableAl']['templatevar'] = 'opentableal';
    $var['opentabs']['OpenTableAl']['innerreplace'] = '{OPENCLOSE_TABLE_AL}';
    // die Funktion themeindex() / News Modul
    $var['themeindex'] = 'themeindex';
    // die Funktion themearticle() / News Modul
    $var['themearticle'] = 'themearticle';
    // der Name (output_container) darf nicht verändert werden !!
    $var['output_container'] = 'script_output';
    // der Name (index_on_container) darf nicht verändert werden !!
    $var['index_on_container'] = 'blocks_right_container';
    $var['index_on_block_container'] = 'blocks_right_loop';
    // der Name (more_header) darf nicht verändert werden !!
    $var['add_header'] = 'more_header';

    return $var;
}

/**
 * theme_get_layout_class()
 * Die verschiedenen Layout-Spalten definieren
 * das Ganze wird in Funktion theme_header() angewendet
 *
 * @return
 */
function theme_get_layout_class()
{
    global $themesetting;

    static $class = '';
    if ($class) {
        return $class;
    }

    $class = ' bodymain';

    $page = 100;
    $right = intval($themesetting['layoutwidth']['right']);

    /*
    switch ($themesetting['layouttype']) {
        case 'fluid':
            $class .= ' fluid';
            $page = 100;
            $right = intval($themesetting['layoutwidth']['fluid']['right']);
            break;
        case 'fixed':
        default:
            $class .= ' fixed';
            $page = intval($themesetting['layoutwidth']['fixed']['page']);
            $right = intval($themesetting['layoutwidth']['fixed']['right']);
    }*/

    $bottom = false;

    switch (true) {
        case theme_hidesideblocks():
        case theme_hiderightblocks() && theme_hideleftblocks():
            // einspaltiges Layout
            $class .= ' full-width';
            break;

        case theme_hiderightblocks():
            // case isset($_GET['about']) && $_GET['about'] === basename(_THISTHEME_):
             // zweispaltiges Layout
            $class .= ' col-2-left';
            switch ($themesetting['layoutcols']) {
                case 'right':
                    $class .= ' col-to-right';
                    break;
                default:

            }
            break;

       case theme_hideleftblocks():
            // zweispaltiges Layout, rechts ist aber links!!
            $class .= ' col-2-right';
            switch ($themesetting['layoutcols']) {
                case 'left':
                    $class .= ' col-to-left';
                    break;
                default:

            }
            break;

        default:
            // dreispaltiges Layout > Standard
            switch ($themesetting['layoutcols']) {
                case 'left':
                    $class .= ' col-3-left';
                    break;
                case 'right':
                    $class .= ' col-3-right';
                    break;
                default:
                    $class .= ' col-3';
            }
            break;
    }

 
    return trim($class);
}

/**
 * theme_replace_start()
 * ersetzen von eigenen Theme-Elementen, kann verändert und ergänzt werden
 * diese Teile werden gleich zu Beginn des scriptes, beim einlesen des templates, ersetzt
 * Vorsicht, wenn das Theme gecached werden soll!!!
 * Dann dürfen hier keine dynamischen Elemente eingesetzt werden.
 *
 * @param mixed $template
 * @return
 */
function theme_replace_start($template)
{
    global $themesetting;

    /**
     * bestimmte Texte, vor allem Image-Pfade, die ersetzt werden sollen, definieren (suche/ersetze)
     */
    $part[] = array('"style/', '"' . MX_THEME_DIR . '/style/'); // die Stylesheets
    $part[] = array('"images/', '"' . MX_THEME_DIR . '/images/'); // normale images im theme
    /* Siteservicebereich entfernen, wenn Siteservice abgeschaltet oder leer ist 
    if (!theme_get_servicetext('siteservice')) {
        theme_extract_part($template, 'siteservice', '');
    }

    /* Debugbereich entfernen, wenn Debugmodus abgeschaltet oder leer ist 
    if (!theme_get_servicetext('debugservice')) {
        theme_extract_part($template, 'debugservice', '');
    }
/*
    if (!$themesetting['footmenu']['backend'] || empty($GLOBALS['backend_active'])) {
        theme_extract_part($template, 'footmenurss');
    }

    if (!$themesetting['footmenu']['impres'] || !mxModuleAllowed('Impressum')) {
        theme_extract_part($template, 'footmenuimpres');
    }*/

    /* ersten Trennstrich im Topmenü entfernen */
    if (preg_match('#(<li\s+class="first">[^>]+>\s*(?:<![^>]+>\s*)?)<li>\|</li>#', $template, $matches)) {
        $part[] = array($matches[0] , $matches[1]);
    }

    /* das CSS-Menü durch die tatsächlichen Daten ersetzen */
    theme_extract_part($template, 'headmenue', '{NAVBAR}');
    $part[] = array('{CLASS}' , theme_get_layout_class());


    return theme_replace_parts($template, $part);
}

/**
 * theme_replace_header()
 * ersetzen von eigenen Theme-Elementen, kann verändert und ergänzt werden
 * diese Teile werden vor der Ausgabe des headers im Headbereich ersetzt
 *
 * @param mixed $newheader
 * @return
 */
function theme_replace_header($newheader)
{
    /* bei Bedarf den XHTML-Dokumenttyp überprüfen und Fehlermeldung ausgeben */
    if (MX_IS_ADMIN && !theme_check_xhtmldoctype()) {
        $newheader .= '<div class="warning"><h2 class="align-center">' . _THEME_FALSEDOCTYPE . '</h2></div>';
    }

    return $newheader;
}

/**
 * theme_replace_blocks()
 * ersetzen in jedem einzelnen Block
 *
 * @param mixed $template
 * @param mixed $block
 * @return
 */
function theme_replace_blocks($template, $block)
{
    global $themesetting;

    /* Teil für alle Blöcke */
    if (empty($block['title']) || $block['title'] == 'NOTITLE') {
        $part[] = array('__BLOCK_ID__' , '__BLOCK_ID__ block-hide-caption');
        $part[] = array('<h3 class="block-title"></h3>' , '');
        $part[] = array('<div class="block-title"></div>' , '');
    }

    $part[] = array('__BLOCK_ID__' , 'block-' . $block['position'] . '-' . $block['order'] . '-bid-' . $block['bid'] . '');

    return theme_replace_parts($template, $part);
}

/**
 * theme_replace_end()
 * ersetzen von eigenen Theme-Elementen, kann verändert und ergänzt werden
 * diese Teile werden am Ende des scriptes, in der Funktion themefooter() ersetzt
 *
 * @param mixed $template
 * @return
 */
function theme_replace_end($template)
{
    global $themesetting;

    /* die linken Blöcke bei bestimmten Seiten entfernen */
    if (theme_hideleftblocks()) {
        theme_extract_part($template, 'blocks_left_container');
    }


    /* leeres Footmenü entfernen */
    if (preg_match('#<ul\s*id="footmenu">\s*<li[^<]+</li>\s*<li[^<]+</li>\s*</ul>\s*<div[^<]+</div>#', $template, $matches)) {
        theme_extract_part($template, 'footmenu');
    }

    /* Leere Elemente entfernen */
    $part[] = array(' class=""', '');

    return theme_replace_parts($template, $part);
}

/**
 * theme_hideleftblocks()
 * prüfen ob linke Blöcke ausgeblendet werden sollen
 *
 * @return
 */
function theme_hideleftblocks()
{
    global $themesetting;

    switch (true) {
        case (isset($themesetting['hide-left']) && in_array(1, $themesetting['hide-left'])):
        case theme_hidesideblocks():
            return true;
    }

    $blocks = mxGetAllBlocks('l');
    if (!$blocks) {
        // für nächsten Aufruf vereinfachen
        $themesetting['hide-left'][] = 1;
        return true;
    }
    return false;
}

/**
 * theme_hiderightblocks()
 * prüfen ob linke Blöcke ausgeblendet werden sollen
 *
 * @return
 */
function theme_hiderightblocks()
{
    global $themesetting;

    switch (true) {
        case empty($GLOBALS['index']):
        case theme_hidesideblocks():
            return true;
    }

    $blocks = mxGetAllBlocks('r');
    if (!$blocks) {
        // für nächsten Aufruf vereinfachen
        $GLOBALS['index'] = false;
        return true;
    }
    return false;
}

/**
 * theme_hidesideblocks()
 * prüfen ob linke Blöcke ausgeblendet werden sollen
 *
 * @return
 */
function theme_hidesideblocks()
{
    global $themesetting;
    return (isset($themesetting['hide-both']) && in_array(1, $themesetting['hide-both']));
}

/**
 * theme_header()
 * in $newheader ist der gesamte angepasste header enthalten
 *
 * @param mixed $newheader
 * @param mixed $siteservice
 * @param mixed $debugservice
 * @return
 */
function theme_header($newheader, &$siteservice, &$debugservice)
{
    global $theme_template, $themesetting;
    /* das Theme verwendet eigene Servicebereiche, deswegen hier die beiden Variablen killen */
    //$siteservice = null;
    //$debugservice = null;

    /* Layout im body-Tag definieren */
    $class = theme_get_layout_class();
    $theme_template['body_tag'] = preg_replace('#(class\s*=\s*["\'])(.*)(["\'])#', '$1' . trim($class) . '$3', $theme_template['body_tag']);

    /* den body-Tag hinter head-Ende setzen >> nicht verändern !! */
    $newheader .= "\n</head>\n\n" . $theme_template['body_tag'] . "\n\n";
    // ersetzen von eigenen Theme-Elementen, kann verändert und ergänzt werden
    // diese Teile werden vor der Ausgabe des headers im Headbereich ersetzt
    $newheader = theme_replace_header($newheader);

    /* Grid */
    pmxHeader::add_style(MX_THEME_DIR . '/style/layout.fluid.css.php');
    return $newheader;
}

/**
 * themesidebox()
 * parsen der Seiten-Blöcke
 *
 * @param mixed $title
 * @param mixed $content
 * @param array $block
 * @param integer $noecho
 * @return
 */
function themesidebox($title, $content, $block = array(), $noecho = 0)
{
    global $theme_template, $themesetting;

    switch (true) {
        case empty($block):
        case empty($block['position']):
        case $block['position'] === 'l':
            $out = $theme_template['block_left'];
            break;
        case $block['position'] === 'r':
                $out = $theme_template['block_right'];       
            break;
    }
    $part[] = array('{BLOCK_CONTENT}' , $content);
    $part[] = array('{BLOCK_TITLE}' , $title);
    $out = theme_replace_parts($out, $part);

    if ($noecho) {
        return $out;
    } else {
        echo $out;
    }
}

/**
 * thememiddlebox()
 * parsen der Center-Blöcke
 *
 * @param mixed $title
 * @param mixed $content
 * @param array $block
 * @param integer $noecho
 * @return
 */
function thememiddlebox($title, $content, $block = array(), $noecho = 0)
{
    global $theme_template, $themesetting;

    $out = '';
    switch (true) {
        case empty($block):
        case empty($block['position']):
        case $block['position'] === 'c':
            $block['position'] = 'c';
            $out = $theme_template['block_center_top'];
            break;
        case $block['position'] === 'd':
            $out = $theme_template['block_center_down'];
            break;
    }

    $part[] = array('{BLOCK_CONTENT}' , $content);
    $part[] = array('{BLOCK_TITLE}' , $title);
    $out = theme_replace_parts($out, $part);

    if ($noecho) {
        return $out;
    } else {
        echo $out;
    }
}

/**
 * mx_theme_loginform()
 * Benutzerspezifische Anzeigen im Kopfbereich
 *
 * @return
 */
function mx_theme_loginform()
{
    global $JPCACHE_ON;

    if ($JPCACHE_ON) {
        return;
    }

    $tpl = load_class('Template');
    $tpl->set_path(dirname(__FILE__) . DS . 'templates');

    if (!MX_IS_USER) {
        defined('mxloginblockviewed') OR define('mxloginblockviewed', true);
        return $tpl->fetch('loginform.html');
    }

     if (MX_IS_USER) {
        $hookitems = pmx_get_usernews();
        $userentries = array();
        foreach ($hookitems as $entry) {
            $userentries[$entry['link']] = $entry['text'];
        }
        $username = mxSessionGetVar('user_uname');
        $tpl = load_class('Template');
        $tpl->set_path(dirname(__FILE__) . DS . 'templates');
        $tpl->assign(compact('msg', 'userentries', 'username'));

        return $tpl->fetch('hellouser.html');
    }
}

/**
 * themeindex()
 * News Modul Artikelliste (index.php)
 * $x bedeutet: nicht verwendet, nur zur nuke-Modulkompatibilität
 *
 * @param mixed $x
 * @param array $story
 * @return
 */
function themeindex($x1, $x2, $x3, $x4, $x5, $x6, $x7, $x8, $x9, $x10, $x11, $x12, $story = array())
{
    global $theme_template;

    // nur eine Spalte zulassen :-)
    $GLOBALS['storyhome_cols'] = 1;

    $story = theme_get_story($story);

    $titleattr = ($story['allmorelink']['bodycount']) ? ' title="' . $story['allmorelink']['bodycount'] . ' ' . _BYTESMORE . '"' : '';
    if (empty($story['allmorelink']['bodycount'])) {
        $story['readmore'] = '';
    } else {
        $story['readmore'] = '<a href="modules.php?name=News&amp;file=article&amp;sid=' . $story['sid'] . '" class="post-readmore mx-button mx-button-primary"' . $titleattr . '><i class="fa fa-arrow-circle-right"></i>  ' . _HREADMORE . '</a>';
    }

    $story['title'] = '<a href="modules.php?name=News&amp;file=article&amp;sid=' . $story['sid'] . '" rel="bookmark"' . $titleattr . '>' . strip_tags($story['title']) . '</a>';

    $story['notes'] = '';
    // die oben definierten Variablen in dem passenden templateteil ersetzen
    $artvars = theme_define_content();
    $out = theme_replace_vars($theme_template[$artvars['themeindex']], $story);

    echo $out;
    return;
}

/**
 * themearticle()
 * News Modul Artikelansicht (article.php)
 * $x bedeutet: nicht verwendet, nur zur nuke-Modulkompatibilität
 *
 * @param mixed $x
 * @param array $story
 * @return
 */
function themearticle($x1, $x2, $x3, $x4, $x5, $x6, $x7, $x8, $x9, $story = array())
{
    global $theme_template;
    $story = theme_get_story($story);

    $story['readmore'] = '';
    $story['title'] = strip_tags($story['title']);
    $story['content'] .= "<br />\n" . $story['bodytext'];
    $story['notes'] = (empty($story['notes'])) ? '' : '<dl class="article-notes mbs"><dt>' . _NOTE . '</dt><dd>' . $story['notes'] . '</dd></dl>';
    // die oben definierten Variablen in dem passenden templateteil ersetzen
    echo theme_replace_vars($theme_template['themearticle'], $story);
    return;
}

/**
 * theme_get_story()
 *
 * @param mixed $story
 * @return
 */
function theme_get_story($story)
{
    $story['title'] = strip_tags($story['title']);

    $story['content'] = $story['hometext'];

    if (is_file($GLOBALS['tipath'] . $story['topicimage'])) {
        $story['topicimage'] = $GLOBALS['tipath'] . $story['topicimage'];
    } else {
        $story['topicimage'] = $GLOBALS['tipath'] . 'AllTopics.gif';
    }
    $story['topictitle'] = _TOPIC . ': ' . $story['topictext'];

    $story['topicimage'] = mxCreateImage($story['topicimage'], $story['topictext'], array('title' => $story['topictitle'], 'align' => 'left', 'class' => 'post-topicimage'));
    $story['topicimage'] = '<a href="modules.php?name=News&amp;topic=' . $story['topic'] . '">' . $story['topicimage'] . '</a>';

    $authorinfo = (empty($story['informant']) || $story['informant'] == $GLOBALS['anonymous']) ? theme_adminname($story) : " " . $story['allmorelink']['informantlink'] . $story['informant'] . "</a>";
    $story['datetime'] = '<span class="story-date">' . $story['datetime'] . '</span>';
    $story['infoline_article'] = '<i class="fa fa-user"></i> ' . _NEWSSUBMITED . ' ' . $authorinfo;

    $topicinfo = '<a href="modules.php?name=News&amp;topic=' . $story['topic'] . '">' . $story['topictext'] . '</a>';
    $story['infoline_index'] = sprintf(_THEME_INFOLINE1, $authorinfo, $topicinfo);

    $dat = strtotime($story['time']);
    $story['posted_month'] = strftime('%b', $dat);
    $story['posted_day'] = date('d', $dat);
    $story['posted_year'] = date('Y', $dat);

    $tags = array('modules.php?name=Stories_Archive&amp;year=' . date('Y', $dat) => date('Y', $dat),
        'modules.php?name=News&amp;topic=' . $story['topic'] => $story['topictext'],
        'modules.php?name=News&amp;file=categories&amp;catid=' . $story['catid'] => $story['cattitle'],
        );

    $story['tags'] = ' ' . _THEME_TAGS . ': ';
    foreach ($tags as $key => $value) {
        if ($value) {
            $story['tags'] .= '<a href="' . $key . '">' . $value . '</a> ';
        }
    }
    if (!$story['acomm'] && $GLOBALS['articlecomm']) { // Achtung!!! acomm: 0 = Ja , 1 = Nein
        $story['comments'] = '<a href="modules.php?name=News&amp;file=article&amp;sid=' . $story['sid'] . '#comments" class="story-comments">' . _COMMENTS . ' (' . $story['comments'] . ')</a>';
    } else {
        $story['comments'] = '';
    }

    return $story;
}

/**
 * theme_get_mxmenu()
 * CSS-Menü aus dem Menümanager auslesen
 *
 * @return string
 */
function theme_get_mxmenu()
{
    global $themesetting;

    if (empty($themesetting['head_css_menu'])) {
        return;
    }

    load_class('Menu', false);

    $menu = pmxMenu::get_menu_instance($themesetting['head_css_menu']);
    $menu->template_path = MX_THEME_DIR . '/templates';
    $menu->template = 'mainnav.html';
    $menu->class_current = 'active';
    $menu->class_additional = 'mx-menu-item';
    if (MX_IS_ADMIN && !$menu->get_tree()) {
        return '<div class="warning">' . sprintf(_THEME_MENU_NOT_EXIST, $themesetting['head_css_menu']) . ' [<a href="admin.php?op=menu">' . _MX_MENU_ADDMENU_EDIT . '</a>]</div>';
    }

    $content = $menu->fetch();
    $menu = null;

    return $content;
}

/**
 * theme_show partners()
 */
function theme_show_partners()
{
    if (MX_MODULE == mxGetMainModuleName()){ 
        //pmxHeader::add_script(MX_THEME_DIR . '/js/jquery.carousel.min.js');
        $tpl = load_class('Template');
        $tpl->set_path(dirname(__FILE__) . DS . 'templates');
        return $tpl->fetch('slider.html');
    }
}

/**
 * theme_show featured()
 */
function theme_show_featured()
{
    if (MX_MODULE == mxGetMainModuleName() AND !MX_IS_USER && !MX_IS_ADMIN ){ 
        $tpl = load_class('Template');
        $tpl->set_path(dirname(__FILE__) . DS . 'templates');
        return $tpl->fetch('featured.html');
    }
}

/**
 * theme_preview_settings()
 * Theme-Einstellungen über Demo-Block steuern
 * (reserviert für pragmaMx Entwicklerteam)
 *
 * @param mixed $themesetting
 * @return
 */
function theme_preview_settings(&$themesetting)
{
    if ($file = realpath(_THISTHEME_ . '/help/theme.vars.ini')) {
        $requestname = 'thset_' . basename(_THISTHEME_);
        if (isset($_POST[$requestname])) {
            $newvals = $_POST[$requestname];
        } else {
            $newvals = mxSessionGetVar($requestname);
        }
        if ($newvals && is_array($newvals)) {
            $setting = parse_ini_file($file, true);
            $session = array();
            foreach ($newvals as $key => $value) {
                if (isset($setting[$key]) && in_array($value, $setting[$key])) {
                    $themesetting[$key] = $value;
                    $session[$key] = $value;
                }
            }
            mxSessionSetVar($requestname, $session);
        }
    }
}

?>
