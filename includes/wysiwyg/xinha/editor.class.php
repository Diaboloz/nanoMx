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
* globales Array, welches die Editorteile fuer den Headbereich der Seite enthaelt.
* wird in der Funktion addEditor() der Klasse pmx_xinha gefuellt und
* gleichzeitig an den Headbereich weitergeleitet
*/
$XINHA_COLLECTION = array();

/**
 * xinha Klasse
 *
 * Konfiguration, siehe hier:
 * - http://xinha.webfactional.com/wiki/Documentation/ConfigVariablesList
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2011
 * @version $Id: editor.class.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmx_editor_xinha {
    /* Standardwerte initialisieren */

    protected $name, $height, $width, $lang, $stylesheet, $mode, $value, $pagebreak, $infotext, $langdirection;

    protected $editor_path, $templatedir;

    /**
     * pmx_editor_xinha::__construct()
     * Der Constructor der Klasse
     *
     * @param mixed $parent_area
     */
    public function __construct($parent_area)
    {
        $class_vars = get_class_vars(__CLASS__);

        $intersect = array_intersect_key($parent_area, $class_vars);
        foreach ($intersect as $key => $value) {
            $this->$key = $value;
        }

        $this->editor_path = PMX_BASE_PATH . PMX_SYSTEM_PATH . 'wysiwyg/' . basename(dirname(__FILE__)) . '/editor/';
        $this->templatedir = dirname(__FILE__) . DS . 'templates';

        /* TODO: Fallback fuer fehlende Sprachdatei !!! */
        $this->lang = _DOC_LANGUAGE;
        $this->langdirection = _DOC_DIRECTION;
    }

    /**
     * pmx_editor_xinha::getHtml()
     * Der komplett generierte HTML-Output dieser Klasse
     * Es wird nicht nur die eigentliche Textarea erstellt, sondern auch die
     * zugehoerigen Einstellungen und Erweiterungen im HTML-Headbereich der
     * Seite erzeugt.
     *
     * @return string
     */
    public function getHtml()
    {
        global $XINHA_COLLECTION;
        // $this->setMode($this->mode);
        $this->setWidth($this->width);
        $this->setHeight($this->height);

        $editor_path = PMX_BASE_PATH . PMX_SYSTEM_PATH . 'wysiwyg/xinha/editor/';
        $editor_lang = _DOC_LANGUAGE;

        $dimensions = '';
        $dimensions .= ' height: ' . $this->height . ';';
        $dimensions .= ' width: ' . $this->width . ';';
        // <link type="text/css" rel="stylesheet" title="pmx" href="< ? php echo $editor_path; ? >skins/pmx/skin.css" />
        // style="overflow: auto; display: inline;' . $dimensions . '"
        // TODO: Cols und Rows noch berechnen?
        $out = "\n";
        $out .= '<textarea cols="65" rows="10" name="' . $this->name . '" id="' . $this->name . '" style="overflow: auto; display: inline;' . $dimensions . '">';
        $out .= htmlspecialchars($this->value);
        $out .= '</textarea>';
        $out .= "\n";

        /**
         * ab hier die Konfiguration der einzelnen Editoren
         */
        /**
         * die Hoehe des Editors
         * Allowed values are 'auto', 'toolbar' or a numeric value followed by px.
         * - auto : let Xinha choose the width to use.
         * - toolbar : compute the width size from the toolbar width.
         * - numeric value : forced width in pixels ('600px').
         * - default value : 'toolbar'
         */
        $value = $this->width;
        if ($value === '100%') {
            $value = 'auto';
        } else if (substr($value, -1) === '%') {
            $value = 'toolbar';
        } else {
            $value = intval($value) . 'px';
        }
        $config_array['width'] = $value;

        /**
         * die Breite des Editors
         * - Allowed values are 'auto' or a numeric value followed by px.
         * - auto : let Xinha choose the height to use.
         * - numeric value : forced height in pixels ('200px').
         * - default value : 'auto'
         */
        $value = $this->height;
        if (substr($value, -1) === '%') {
            $value = 'auto';
        } else {
            $value = intval($value) . 'px';
        }
        $config_array['height'] = $value;

        /**
         * die Konfiguration und Initialisierung dieses Editors an das
         * globale Array anfuegen
         * das Ganze ist etwas tricky...
         * weil jeder zusaetzliche Editor ein komplett neues Javascript fuer
         * den Headbereich der Seite generieren muss
         */
        $XINHA_COLLECTION[$this->name] = $config_array;
        foreach ($XINHA_COLLECTION as $name => $arr) {
            $editors[] = $name;
            foreach ($arr as $key => $value) {
                if ($key !== 'out') {
                    $editors_config[] = '  xinha_editors.' . $name . '.config.' . $key . ' = "' . $value . '";';
                }
            }
        }

        /**
         * hier den kompletten Eintrag fuer den Headbereich generieren
         */
        ob_start();

        ?>
<!-- Beginn Xinha Integration -->
<script type="text/javascript">
    /*<![CDATA[*/
    var _editor_url  = '<?php echo $editor_path ?>';
    var _editor_lang = '<?php echo $editor_lang ?>';
    /*]]>*/
</script>

<script type="text/javascript" src="<?php echo $editor_path ?>XinhaCore.js"></script>

<script type="text/javascript">
/*<![CDATA[*/
var xinha_plugins =
[
 'CharacterMap',
 'ContextMenu',
 'ListType',
 'SpellChecker',
 'Stylist',
 'SuperClean',
 'TableOperations'
];

var xinha_editors = ['<?php echo implode("','", $editors) ?>'];

function xinha_init()
{
  if(!Xinha.loadPlugins(xinha_plugins, xinha_init)) return;
  var xinha_config = new Xinha.Config();
  xinha_editors = Xinha.makeEditors(xinha_editors, xinha_config, xinha_plugins);
<?php echo implode("\n", $editors_config) . "\n" ?>
  Xinha.startEditors(xinha_editors);
}
window.onload = xinha_init;
/*]]>*/
</script>
<!-- Ende Xinha Integration -->

<?php
        $head1 = ob_get_clean();
        /**
         * hier den kompletten Eintrag dem Headbereich uebergeben
         */
        pmxHeader::add($head1, 'xinha_head');
        // #################
        // /* TODO: Toolbar einstellen !! setMode() */
        // $template = $this->mode . '.template.html';
        // $templatevars = array();
        // foreach ($this as $key => $value) {
        // $templatevars[$key] = $value;
        // }
        // /* TODO: Cols und Rows noch berechnen? */
        // /**
        // * hier den kompletten Eintrag fuer den Headbereich generieren
        // */
        // /* Load jQuery */
        // // pmxHeader::add_jquery();
        // /* Load jQuery build */
        // #pmxHeader::add_script($this->editor_path . 'tiny_mce.js');
        // // pmxHeader::add_script($this->editor_path . 'tiny_mce_gzip.js');
        // /* Template initialisieren */
        // $template_object = load_class('Template');
        // $template_object->set_path($this->templatedir);
        // $template_object->assign($templatevars);
        // $out = $template_object->fetch($template);
        // ###################
        /**
         * nur das erzeugte Textfeld zurueckgeben, damit dieses als
         * eigentlichen Editor an der aufgerufenen Stelle erscheint.
         * der Rest aus dieser Funktion landet ja im Headbereich der Seite
         */
        return $out;
    }

    /**
     * pmx_editor_xinha::setHeight()
     * This property controls the height of the editor.
     *
     * @param mixed $value
     * @return
     */
    protected function setHeight($value)
    {
        /**
         * This property controls the height of the editor.
         * Allowed values are 'auto' or a numeric value followed by px.
         * - auto : let Xinha choose the height to use.
         * - numeric value : forced height in pixels ('200px').
         * - default value : 'auto'
         */
        if (substr($value, -1) === '%') {
            $this->height = intval($value) . '%';
        } else {
            $this->height = intval($value) . 'px';
        }
    }

    /**
     * pmx_editor_xinha::setWidth()
     * This property controls the width of the editor.
     *
     * @param mixed $value
     * @return
     */
    protected function setWidth($value)
    {
        /**
         * This property controls the width of the editor.
         * Allowed values are 'auto', 'toolbar' or a numeric value followed by px.
         * - auto : let Xinha choose the width to use.
         * - toolbar : compute the width size from the toolbar width.
         * - numeric value : forced width in pixels ('600px').
         * - default value : 'toolbar'
         */
        if (substr($value, -1) === '%') {
            $this->width = intval($value) . '%';
        } else {
            $this->width = intval($value) . 'px';
        }
    }
}

?>