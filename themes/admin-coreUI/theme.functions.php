<?php
/**
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Revision: 1.17 $
 * $Author: tora60 $
 * $Date: 2014-03-18 19:56:09 $
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
    $part[] = array("{PAGE_TITLE}", $GLOBALS['sitename']);
    $part[] = array("{ADMINMENUHEAD}", theme_get_mxmenu());
    //$part[] = array("{ADMINMENUBLOCK}", theme_get_blockmenu());
    $part[] = array("{USERPIC}", theme_get_userpic());
    $part[] = array("{CURRENTPATH}", theme_show_currentpath());
    $part[] = array("{CURRENT_MOD_LINK}", theme_get_currentmodlink());
    $part[] = array("{CURRENTYEAR}", date('Y'));
    $part[] = array("{PMX_VERSION}", PMX_VERSION_DATE);
    $part[] = array("{VIEWBENCH}", mxViewBench());

    $admindata = mxGetAdminData();
    if (MX_IS_USER) {
        $userdata = mxGetUserData();
        $uname = ($userdata['uname'] == $admindata['aid']) ? $userdata['uname'] : $admindata['aid'] . ' / ' . $userdata['uname'];
    } else {
        $uname = $admindata['aid'];
    }
    $part[] = array("{USERNAME}", $uname);

    $part[] = array("{THEME_TRAYITEMS}", theme_headitems());

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
    /* global $themesetting; */
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
    // rechte Blöcke
    $name = 'block_right';
    $var[$name]['container'] = 'blocks_right_loop';
    $var[$name]['function'] = 'themesidebox';
    $var[$name]['position'] = 'r';

    return (array)$var;
}

/**
 * theme_define_content()
 * Definition des Contentbereiches inkl. der open/close-Table Funktionen
 *
 * @return
 */
function theme_define_content()
{
    /* global $themesetting; */
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
    // rechte Blöcke
    $var['index_on_container'] = 'blocks_right_container';
    $var['index_on_block_container'] = 'blocks_right_loop';
    // der Name (more_header) darf nicht verändert werden !!
    $var['add_header'] = 'more_header';

    return $var;
}

/**
 * Die verschiedenen Layout-Spalten definieren
 * das Ganze wird in Funktion theme_header() angewendet
 */
function theme_get_layout_class()
{
    global $themesetting;

    $class = $themesetting['layouttype'];
    return trim($class);
}

/**
 * Die verschiedenen Layout-Spalten definieren
 */
function theme_get_userpic()
{
    $content = '';
    $pici = load_class('Userpic');
    $content = '<img src="' . $pici->exist() . '" class="img-avatar" />';   
    return $content;
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
    $part[] = array('"js/', '"' . MX_THEME_DIR . '/js/'); // die Javascripte
    $part[] = array('"images/', '"' . MX_THEME_DIR . '/images/'); // normale images im theme
    $part[] = array('url(images/', 'url(' . MX_THEME_DIR . '/images/'); // Hintergrundbilder im theme

    // Debugbereich entfernen, wenn Debugmodus abgeschaltet ist
    switch (pmxDebug::is_debugmode()) {
        case VIEW_ADMIN:
            theme_extract_part($template, 'hddebug2', '');
            break;
        case VIEW_ALL:
            theme_extract_part($template, 'hddebug1', '');
            break;
        default:
            theme_extract_part($template, 'hddebug1', '');
            theme_extract_part($template, 'hddebug2', '');
    }
    // das Kopfmenü durch die tatsächlichen Daten ersetzen
    theme_extract_part($template, 'headmenue', '{ADMINMENUHEAD}');
    // das Block-Adminmenü durch die tatsächlichen Daten ersetzen
    //theme_extract_part($template, 'admin_block_menu', '{ADMINMENUBLOCK}');
    //$part[] = array('{CLASS}' , theme_get_layout_class());

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
    return $newheader;
}

/**
 * ersetzen in jedem einzelnen Block
 */
function theme_replace_blocks($template, $block)
{
    return $template;
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
    /* Leere Elemente entfernen */
    $part[] = array(' class=""', '');
    $part['preg'][] = array('#\s*<\!--\s*(?:START|END)\s*[^>]+\s*-->\s*#', '');

    return theme_replace_parts($template, $part);
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
    $siteservice = null;
    $debugservice = null;

    /* Layout im body-Tag definieren */
    $class = theme_get_layout_class();
    $theme_template['body_tag'] = preg_replace('#(class\s*=\s*["\'])(.*)(["\'])#', '$1' . trim($class) . '$3', $theme_template['body_tag']);

    /* den body-Tag hinter head-Ende setzen >> nicht verändern !! */
    $newheader .= "\n</head>\n\n" . $theme_template['body_tag'] . "\n\n";
    // ersetzen von eigenen Theme-Elementen, kann verändert und ergänzt werden
    // diese Teile werden vor der Ausgabe des headers im Headbereich ersetzt
    $newheader = theme_replace_header($newheader);

    /* Fixes für den IE 
    $browser = load_class('Browser');
    if ($browser->msie) {
        pmxHeader::add_style(MX_THEME_DIR . '/style/main-ie6.css', 'all', 'lte IE 6');
    }

    /* Layout-Type: fluid oder fixed 
    if ($themesetting['layouttype'] == 'fixed') {
        //pmxHeader::add_style(MX_THEME_DIR . '/style/layout.fixed.css.php');
    }*/
    
    pmxHeader::add_jquery();
    //pmxHeader::add_jquery('jquery.cookie.js');
   // pmxHeader::add_script_code('var cookiepath="' . PMX_BASE_PATH . '";');
    //pmxHeader::add_script_code('var frameSrc = "https://www.google.fr/";');
   // pmxHeader::add_script_code('$(document).ready(function() {$("#hddebug, #switcher, #currentmodlinks a, #logo").tooltip()});');
    // pmxHeader::add_style(MX_THEME_DIR . '/style/cssmenue.css');//
    //pmxHeader::add_script(MX_THEME_DIR . '/js/jquery.scrollUp.min.js');
   // pmxHeader::add_script(MX_THEME_DIR . '/js/libs/jquery.min.js');
    //pmxHeader::add_script(MX_THEME_DIR . '/js/libs/bootstrap.min.js');
    //pmxHeader::add_script(MX_THEME_DIR . '/js/libs/tether.min.js');
   //pmxHeader::add_script(MX_THEME_DIR . '/js/app.js');

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
    global $theme_template;

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
    global $theme_template;

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

    pmxHeader::add_style('themes/' . basename(dirname(__FILE__)) . '/style/news.css');
    // nur eine Spalte zulassen :-)
    $GLOBALS['storyhome_cols'] = 1;

    $story = theme_get_story($story);

    $titleattr = ($story['allmorelink']['bodycount']) ? ' title="' . $story['allmorelink']['bodycount'] . ' ' . _BYTESMORE . '"' : '';
    if (empty($story['allmorelink']['bodycount'])) {
        $story['readmore'] = '';
    } else {
        $story['readmore'] = '<a href="modules.php?name=News&amp;file=article&amp;sid=' . $story['sid'] . '" class="post-readmore"' . $titleattr . '>' . _READMORE . '</a>';
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
    pmxHeader::add_style('themes/' . basename(dirname(__FILE__)) . '/style/news.css');

    $story = theme_get_story($story);

    $story['readmore'] = '';
    $story['title'] = strip_tags($story['title']);
    $story['content'] .= "<br />\n" . $story['bodytext'];
    $story['notes'] = (empty($story['notes'])) ? '' : '<dl class="post-notes"><dt>' . _NOTE . '</dt><dd>' . $story['notes'] . '</dd></dl>';
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
    $story['infoline_article'] = _NEWSSUBMITED . ' ' . $authorinfo . ' ' . $story['allmorelink']['datetime'];

    $topicinfo = '<a href="modules.php?name=News&amp;topic=' . $story['topic'] . '">' . $story['topictext'] . '</a>';
    $story['infoline_index'] = sprintf(_THEME_INFOLINE1, $topicinfo, $authorinfo);

    $dat = strtotime($story['time']);
    $story['posted_month'] = strftime('%b', $dat);
    $story['posted_day'] = date('d', $dat);

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
    /* global $themesetting; */

    $menuparams = array(/* Standardwerte */
        // 'menuname' => $themesetting['head_css_menu'],
        // 'homelink' => './',
        // 'class_home' => 'home',
        'class_first' => 'nav-item first',
        // 'class_last' => 'last',
        'class_current' => 'nav-item active open',
        // 'class_disabled' => 'disabled',
        'class_nolink' => 'nav-item',
        //'class_sublevel' => 'open',
        'class_parent' => 'nav-dropdown',
        // 'class_additional' => 'fadethis',
        // 'stylesheet' => '', // in __construct
        'template_path' => MX_THEME_DIR . '/templates', // in __construct
        'template' => 'admin.menu.html', // in __construct
        'normal_events' => false, // normale Klappfunktion, also nicht im CSS-menü etc.
        'adminmenu' => true, // !!!!!!!!!!!!!!!!!!!!!!!
        );

    load_class('Menu', false);

    $menu = pmxMenu::get_menu_instance($menuparams);

    /* Verwendet dann pmxMenu_menu::_level() */
    $content = $menu->fetch();
    $menu = null;

    return $content;
}

/**
 * theme_get_blockmenu()
 * Ausgabe des Adminmenüs in einem Block
 *
 * @return
 */
function theme_get_blockmenu()
{
    $content = '';
    include(PMX_BLOCKS_DIR . DS . 'block-AdminMenu.php');
    return $content;
}

/**
 * themefooter()
 *
 * @return
 */
function themefooter()
{
    global $theme_template;
    $part = array();

    /* Im Puffer stehen hier die Ausgaben des Moduls */
    $theme_template['script_output'] = ob_get_clean();

    /* hier ist das Modul komplett durchgelaufen und die Variablen koennen jetzt im Template ersetzt werden */
    $theme_template['template'] = theme_replace_vars($theme_template['template']);
    $contentvars = theme_define_content();
    theme_extract_part($theme_template['template'], $contentvars['output_container'], $theme_template['script_output']);
    unset($theme_template['script_output']);

    unset($theme_template['blockcontainers']);

    /*  ersetzen von eigenen Theme-Elementen, kann verändert und ergänzt werden */
    $theme_template['template'] = theme_replace_end($theme_template['template']);

    /* sys_images ersetzen */
    $theme_template['template'] = theme_replace_sysimages($theme_template['template']);

    /**
     * hier wird der eindeutige String für die Platzhalter durch die
     * eigentlichen Platzhalter, bzw. dessen Werte ersetzt (ab 0.1.9)
     */
    if (function_exists('theme_define_placeholders')) {
        $parts = theme_define_placeholders();
        $key = '-:_' . md5($GLOBALS['mxSecureKey']) . '_:-';
        foreach ($parts as $uups) {
            $searches[] = $key . trim($uups[0], '{');
            $replaces[] = $uups[1];
        }
        $theme_template['template'] = str_replace($searches, $replaces, $theme_template['template']);
    }

    /* Debug-Info ausgeben */
    $theme_template['template'] = theme_get_debuginfo($theme_template['template']);

    /* Puffer wieder starten für evtl. mod_rewrite */
    if (!ob_get_level()) {
        // Falls keine Pufferebene mehr vorhanden, obwohl in mxBaseconfig explizit gestartet...
        ob_start();
    }

    /* Inhalt abschicken */
    echo $theme_template['template'];
}

/**
 * theme_get_currentmodlink()
 * gibt einen Link zum aktuellen Adminmodul aus
 *
 * @return
 */
function theme_get_currentmodlink()
{
    $adminmenu = load_class('Adminmenue');
    $current = $adminmenu->get_current();

    /* ermitteln ob es ein normales Modul ist, oder ein Systemmodul */
    $module = (defined('PMX_MODULE') && is_file(PMX_MODULES_DIR . DS . PMX_MODULE . DS . 'index.php')) ? 'modules.php?name=' . PMX_MODULE : '';

    $tpl = load_class('Template');
    $tpl->set_path(dirname(__FILE__) . DS . 'templates');
    $tpl->assign($current);
    $tpl->assign('module', $module);

    return $tpl->fetch('admin.breadcrumb.html');
}

/**
 * theme_headitems()
 *
 * @return
 */
function theme_headitems()
{
    /*
    $userentries = array();
    if (MX_IS_USER && ($newentries = pmx_get_usernews())) {
        $userentries['count'] = 0;
        $userentries['items'] = array();
        foreach ($newentries as $entry) {
            $userentries['count'] += $entry['count'];
            $userentries['items'][$entry['link']] = $entry['text'];
        }
        $msg = ($userentries['count'] === 1) ? _THEME_NEWMESSAGE : _THEME_NEWMESSAGES;
        $userentries['title'] = sprintf($msg, $userentries['count']);
    }
    */
    $adminentries = array();
    if ($newentries = pmx_get_adminnews()) {
        $adminentries['count'] = 0;
        $adminentries['items'] = array();
        foreach ($newentries as $entry) {
            $adminentries['count'] += $entry['count'];
            $adminentries['items'][$entry['link']] = $entry['count'] . ' ' . $entry['text'];
        }
        $msg = ($adminentries['count'] === 1) ? _THEME_NEWENTRY : _THEME_NEWENTRIES;
        $adminentries['title'] = sprintf($msg, $adminentries['count']);
    }

    $headentries['doku'] = _THEME_DOKU;
    $headentries['cache'] = _THEME_PURGE_CACHE;
    $headentries['version'] = _THEME_PMX_VERSION;

    $langentries = theme_languageflags();

    $tpl = load_class('Template');
    $tpl->set_path(dirname(__FILE__) . DS . 'templates');
    $tpl->assign(compact('adminentries', 'userentries', 'langentries', 'headentries'));

    return $tpl->fetch('header.links.html');
}

/**
 * theme_languageflags()
 *
 * @return
 */
function theme_languageflags()
{
    global $currentlang;

    $path = 'images/language';
    $extension = 'png';

    $query = $_SERVER['QUERY_STRING'];
    if (isset($_GET['newlang'])) {
        $query = preg_replace('#[&?]?newlang=[a-zA-Z_]*#', '', $query);
    }
    $to = basename($_SERVER['PHP_SELF']);
    // index.php ist auch php_self=modules.php, deswegen hier index.php verwenden, falls $name leer ist
    if ($query) {
        $to .= '?' . mx_urltohtml($query) . '&amp;newlang=';
    } else {
        $to .= '?newlang=';
    }

    $languages = array_flip(mxGetAvailableLanguages());

    $current = array();
    $langentries = array();
    $langentries['items'] = array();
    foreach ($languages as $language => $title) {
        $image = $path . '/flag-' . $language . '.' . $extension;
        if ($currentlang == $language) {
            $current = array('text' => $title,
                'link' => $to . $language,
                'title' => _SELECTGUILANG,
                'image' => $image,
                );
       
        }
        $langentries['items'][] = array('text' => $title,
            'link' => $to . $language,
            'title' => _SELECTGUILANG . ': ' . $title,
            'image' => $image,
            );
    }

    if (count($langentries['items']) < 2) {
        return false;
    }

    $langentries = array_merge($current, $langentries);

    return $langentries;
}

?>
