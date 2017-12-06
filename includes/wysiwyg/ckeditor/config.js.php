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
 * $Revision: 373 $
 * $Author: PragmaMx $
 * $Date: 2017-09-21 09:11:04 +0200 (Do, 21. Sep 2017) $
 */

if (!defined('mxMainFileLoaded')) {
    if (!($mainfile = realpath(__DIR__ . '/../../../mainfile.php'))) {
        die('// mainfile not found...');
    }
    include_once($mainfile);
}

load_class('Textarea', false);
$wyscnf = pmxTextarea::get_config();

$tmp = sprintf('a%u', crc32(DS . $wyscnf['globals']['area_foreground'] . DS . $wyscnf['globals']['area_background'] . DS));
$editor = basename(__DIR__);
$contentscss = PMX_BASE_PATH . PMX_SYSTEM_PATH . 'wysiwyg/' . $editor . '/htmlarea.css.php?t=' . MX_THEME . '&' . $tmp;

$fb = load_class('Filebrowse');
if ($fb->is_active()) {
    $filebrowser = $fb->getBrowseUrl(array('editor' => $editor));
}

header('Content-Type: text/javascript; charset=UTF-8');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600 * 24) . ' GMT'); // 1 day
header('X-Powered-By: pragmaMx-cms');

?>
CKEDITOR.editorConfig = function( config )
{

<?php /* The base href URL used to resolve relative and absolute URLs in the editor content. */ ?>
  config.baseHref = '<?php echo PMX_HOME_URL ?>/';
  config.skin = 'moono';
  
  config.extraPlugins = 'image2';
  config.extraPlugins = 'linkrel';			
  
  config.toolbar_reduced =
  [
  	['Source','Undo','Redo','RemoveFormat','-','Bold','Italic','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','NumberedList','BulletedList','-','Link','Unlink','Image','Smiley','-','About']
  ];

  config.toolbar_normal =
  [
    { name: 'document', items : [ 'Source','Preview' ] },
    { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo','-','RemoveFormat' ] },
    { name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker','Scayt' ] },
    { name: 'tools', items : [ 'Maximize','ShowBlocks','-','About' ] },
    '/',
    { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript' ] },
    { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },
    { name: 'links', items : [ 'Link','Unlink' ] },
    '/',
    { name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
    { name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
    { name: 'colors', items : [ 'TextColor','BGColor' ] }
  ];

  config.toolbar_full =
  [
    { name: 'document', items : [ 'Source','-','Preview','Templates' ] },
    { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo','-','RemoveFormat' ] },
    { name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker','Scayt' ] },
    { name: 'tools', items : [ 'Maximize','ShowBlocks','-','About' ] },
    '/',
    { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript' ] },
    { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
    { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
    '/',
    { name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
    { name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
    { name: 'colors', items : [ 'TextColor','BGColor' ] }
  ];

<?php /* Styles configuration. */ ?>
  config.bodyClass = 'body-allone';
  config.bodyId = 'htmlarea';
  config.stylesSet = [ <?php include(__DIR__ . '/styles.php') ?> ];
  config.format_tags = 'p;h4;h5;h6;pre;address;div'
  config.contentsCss = '<?php echo $contentscss ?>';

<?php /* Rechtschreibprüfung */ ?>
<?php if (defined('_LOCALE') && in_array(_LOCALE, array('en_US', 'en_GB', 'pt_BR', 'da_DK', 'nl_NL', 'en_CA', 'fi_FI', 'fr_FR', 'fr_CA', 'de_DE', 'el_GR', 'it_IT', 'nb_NO', 'pt_PT', 'es_ES', 'sv_SE'))): ?>
  config.scayt_sLang = '<?php echo _LOCALE ?>';
<?php endif ?>

<?php /* der Dateibrowser */ ?>
<?php if($fb->is_active()){ ?>
  config.filebrowserBrowseUrl      = '<?php echo $filebrowser ?>&type=files';
  config.filebrowserImageBrowseUrl = '<?php echo $filebrowser ?>&type=images';
  config.filebrowserFlashBrowseUrl = '<?php echo $filebrowser ?>&type=flash';
<?php } //endif ?>

};

