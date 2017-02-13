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
 */

defined('mxMainFileLoaded') or die('access denied');

/*
 * elRTE configuration
 *
 * @param doctype         - doctype for editor iframe
 * @param cssClass        - css class for editor
 * @param cssFiles        - array of css files, witch will inlude in iframe
 * @param height          - not used now (may be deleted in future)
 * @param lang            - interface language (requires file in i18n dir)
 * @param toolbar         - name of toolbar to load
 * @param absoluteURLs    - convert files and images urls to absolute or not
 * @param allowSource     - is source editing allowing
 * @param stripWhiteSpace - strip ?????? whitespaces/tabs or not
 * @param styleWithCSS    - use style=... instead of strong etc.
 * @param fmAllow         - allow using file manger (elFinder)
 * @param fmOpen          - callback for open file manager
 * @param buttons         - object with pairs of buttons classes names and titles (when create new button, you have to add iys name here)
 * @param panels          - named groups of buttons
 * @param panelNames      - title of panels (required for one planned feature)
 * @param toolbars        - named redy to use toolbals (you may combine your own toolbar)
 *
 * @author:    Dmitry Levashov (dio) dio@std42.ru
 * Copyright: Studio 42, http://www.std42.ru
 */
// header('Content-Type: text/javascript; charset=UTF-8');
// header('X-Powered-By: pragmaMx-cms');
// header('Etag: a' . md5(filectime(__FILE__)));
switch (true) {
    case isset($GLOBALS['AllowableHTML']):
        $allowablehtml = (array)$GLOBALS['AllowableHTML'];
        break;
    default:
        $allowablehtml = array();
}

$allowed = array();
$denied = array('h1', 'applet', 'base', 'basefont', 'bgsound', 'blink', 'body', 'col', 'colgroup', 'isindex', 'frameset', 'html', 'head', 'meta', 'marquee', 'noframes', 'noembed', 'o:p', 'title', 'xml');
foreach($allowablehtml as $tag => $para) {
    switch (true) {
        case in_array($tag, $denied);
        break;
        case MX_IS_ADMIN:
        case $para == 1:
        case $para == 2:
            $allowed[] = $tag;
            break;
        case $para == 0:
            $denied[] = $tag;
            break;
    }
}

?>
<script type="text/javascript">

var dialog;

$().ready(function() {
var pmx_elrte_options = {

  doctype         : '<?php echo $this->doctype ?>',
  cssClass        : 'el-rte',
  cssfiles        : ['<?php echo $this->editor_path ?>css/elrte-inner.css'],
  resizable       : true,
  lang            : '<?php echo $this->lang ?>',
  absoluteURLs    : false,
  allowSource     : true,
  stripWhiteSpace : true,
  styleWithCSS    : false,
  fmAllow         : true,
<?php if($this->filebrowse_active){ ?>
  fmOpen          : elrte_filemanager_callback,
<?php } //endif ?>

<?php if (MX_IS_ADMIN) {
    ?>
  /* if set all other tag will be removed */
  allowTags : [],
  /* if set this tags will be removed */
  denyTags : ['<?php echo implode("','", $denied) ?>'],

<?php } else {
    ?>
  /* if set all other tag will be removed */
  allowTags : ['<?php echo implode("','", $allowed) ?>'],
  /* if set this tags will be removed */
  denyTags : [],

<?php } // endif
?>

  denyAttr : [],
  /* on paste event this attributes will removed from pasted html */
  pasteDenyAttr : ['id', 'name', 'class', 'style', 'language', 'onclick', 'ondblclick', 'onhover', 'onkeup', 'onkeydown', 'onkeypress'],
  /* If false - all text nodes will be wrapped by paragraph tag */
  allowTextNodes : true,
  /* allow browser specific styles like -moz|-webkit|-o */
  allowBrowsersSpecStyles : false,
  /* allow paste content into editor */
  allowPaste : true,
  /* if true - only text will be pasted (not in ie) */
  pasteOnlyText : false,
  /* user replacement rules */
  replace : [],
  /* user restore rules */
  restore : [],
  pagebreak : '<div style="page-break-after: always;"><\/div>',

  panels      : {
    eol        : [], // special panel, insert's a new line in toolbar
    copypaste  : ['copy', 'cut', 'paste', 'pastetext', 'pasteformattext', 'removeformat', 'docstructure'],
    undoredo   : ['undo', 'redo'],
    style      : ['bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript'],
    colors     : ['forecolor', 'hilitecolor'],
    alignment  : ['justifyleft', 'justifycenter', 'justifyright', 'justifyfull'],
    indent     : ['outdent', 'indent'],
    format     : ['formatblock', 'fontsize', 'fontname', 'css'],
    lists      : ['insertorderedlist', 'insertunorderedlist'],
    elements   : ['horizontalrule', 'blockquote', 'div', 'stopfloat', 'nbsp'],
    direction  : ['ltr', 'rtl'],
    links      : ['link', 'unlink', 'anchor'],
    images     : ['image'],
    media      : ['elfinder', 'image', 'flash', 'smiley'],
    tables     : ['table', 'tableprops', 'tablerm',  'tbrowbefore', 'tbrowafter', 'tbrowrm', 'tbcolbefore', 'tbcolafter', 'tbcolrm', 'tbcellprops', 'tbcellsmerge', 'tbcellsplit'],
    fullscreen : ['fullscreen', 'about'],

    elements2      : ['link', 'unlink', 'image', 'smiley', 'about'],
    style2      : ['bold', 'italic'],
  },
  toolbars   : {
    reduced  : ['undoredo', 'style2', 'alignment', 'lists', 'elements2'],
    normal   : ['copypaste', 'undoredo', 'media', 'elements', 'links', 'lists', 'indent', 'format', 'style', 'colors', 'alignment', 'fullscreen'],
    full     : ['copypaste', 'undoredo', 'media', 'elements', 'links', 'lists', 'indent', 'format', 'style', 'colors', 'alignment', 'tables', 'fullscreen'],
  }
  }

  /* Objekte zusammenfügen */
  $().extend(elRTE.prototype.options, pmx_elrte_options);

})



</script>
