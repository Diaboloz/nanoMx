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
 * $Revision: 237 $
 * $Author: PragmaMx $
 * $Date: 2016-09-29 15:13:55 +0200 (Do, 29. Sep 2016) $
 */
/*
    Dateien
        - includes/classes/
            AdminForm.php    ->diese Datei
        - includes/classes/AdminForm/
            alle Sprachdateien
        - includes/javascript/
            mx_checklist.js
            mx_fieldsets.js
            jquery/qtip/jquery.qtip.css
        - layout/style/
            default.adminform.css
        - images/adminform/
            *.png alle Bilder für die Button
        -  jQuery
            qtip/jquery.qtip.js
            jquery/jquery.accordion.js
*/
defined('mxMainFileLoaded') or die('access denied');
/**
 * pmxAdminForm
 *
 * Beschreibung:
 * AdminForm erstellt ein strukturiertes Formular
 * die Funktionen können sowohl Public aufegrufen werden, werden aber auch intern verwendet
 *
 * @package pragmaMx
 * @author terraproject
 * @copyright Copyright (c) 2012
 * @version $Id: AdminForm.php 237 2016-09-29 13:13:55Z PragmaMx $
 * @access public
 */
class pmxAdminForm {
    /* Speichert die Instanz der Klasse */
    //private static $_initialized = false;
    /* Configuration */
    private $__set = array(); // Konfigurtion
    private $toolbar = array(); // speichert die Toolbar
  private $config = array(); // Konfigurationseinstellungen
    private $fieldset_array = array(); // nimmt die Feldnamen auf für JS
    private $formset = array();
    private $noformset = array();
    // dies sind keine Konfigurationseinstellungen, die von aussen gesetzt werden können
    /* Status */
    private $checklistflag = false; // gesetzt wenn eine checkliste erwartet wird
    private $collapsibleflag = false; // gesetzt, wenn die Fieldsets klappbar sein sollen
    private $formopenflag = false; // gesetzt, wenn form geöffnet wurde
    private $infobuttonflag = false; // gesetzt, wenn Infobutton erwartet wird
    private $tipcorners = array('bottomLeft', 'bottomRight', 'bottomMiddle',
        'topRight', 'topLeft', 'topMiddle',
        'leftMiddle', 'leftTop', 'leftBottom',
        'rightMiddle', 'rightBottom', 'rightTop'
        );
    private $tipopposites = array('topRight', 'topLeft', 'topMiddle',
        'bottomLeft', 'bottomRight', 'bottomMiddle',
        'rightMiddle', 'rightBottom', 'rightTop',
        'leftMiddle', 'leftTop', 'leftBottom'
        );
  private static $checkselector = "cid"; // Name der Checkbox, die für "alle selektieren" verantworlich ist
    /**
     * pmxAdminForm::__construct()
     *
     * @param string $formname Formularname kann übergeben werden
     * @return nothing
     */
    public function __construct($formname = 'aForm')
    {
        //if (self::$_initialized) {
        //    return;
        //}
        //self::$_initialized = true;
        $this->config = array(/* Standardwerte */
            'tb_pic_heigth' => 30, // höhe Bilder der Toolbar, standard 30px
            'tb_pic_text' => true, // Anzeige Text unter den Toolbarimages true/false
            'tb_pic_path' => "images/adminform/", // standardpfad zu den Bildern -> pfad zur pmxRoot
            'tb_pic_blank_pic' => "images/adminform/blank.png", // Standard-Bilddatei bei keiner Angabe
            'tb_pic_alternate_path' => "images/adminform/", // Alternativpfad zu den Bildern -> pfad zur pmxRoot
            'tb_direction' => "right", // standartdausrichtung right/left
            'tb_text' => "", // text seitlich der Toolbar
            'tb_show' => 'top', // anzeige der Toolaber top=oben, bottom=unten, both = oben und unten
            'formname' => preg_replace('#[^a-zA-Z0-9_]#', '', $formname), // Name des Formulars
            'buttontext' => false, // Buttons für alle öffnen/schliessen der Fieldsets als Text zeigen
      'collapsibleshowbutton'=>'both', // einblenden der Klappbutton (both=oben und unten, top=nur oben, bottom=nur unten, none=nichst)
            'acceptbutton' => true, // Anzeige von Accept-Button unterhalb der Fieldsets
            'target_url' => $_SERVER['REQUEST_URI'], // Target-Url des Formulars -> standard = self
            'enctype' => '', // encrypt-Type für das Formular
            'title' => '', // <h3>-Titel über dem Formular
            'description' => '', // Text unter dem Titel
            'csstoolbar' => 'toolbar1', // css-Klasse für die Toolbar ... standard 'toolbar1'
      'toolbarfixed'=>false, // true= gleiche Buttonbreite, false = Buttonbreite richtet sich nach dem Text
            'cssform' => 'adminForm', // css-Klasse für das Formular ... standard 'adminForm'
            'infobutton' => false, // Infobutton (true) oder Infotext (false)
            'fieldhomebutton' => false, // blendet einen Homebutton im unteren Rand des Fieldsets ein
      'fieldimagesize'=>"150px", // größe von anzuzeigenden Bildern im Formular. Kann in den betroffenen Inputfeldern einzeln auch eingestellt werden
            // TODO: dies sollte in die globale pragmaMx-Umgebung ausgelaget werden, so
            // dass alle tooltips auf der Seite gleich dargestellt werden
            // - die css-Datei "default.tooltip.css" ist bereits angepasst
            'tooltiptheme' => 'dark', // plain, dark, light, blue, red, green
            'tooltipstyle' => 'jtools', // shadow, rounded, youtube, jtools, cluetip, tipped, tipsy
            'tooltipdirection' => 'bottom right', // Tooltipdirection : Position der Spitze des Tooltips 'bootom left, bottom right, bottom center, top left, top right, top center'
            'checkselector' => self::$checkselector, // Name der Checkbox, die für "alle selektieren" verantworlich ist
            'homelink' => true, // wenn true wird unter der Form ein Link zum Formanfang gesetzt
      'mainform' => true, // auf false setzen, wenn 2.Form auf der Seite
      
            );
        $this->checklisJS = "<script type=\"text/javascript\">
                    /*<![CDATA[*/
                      $(document).ready(function () {
                      $(\"input[name='check_all']\").click(function () {
                        var checker = $(this);
                        if (checker.attr('checked')) {
                          checker.parents('table').find(':checkbox[name^=\"" . $this->config['checkselector'] . "\"]').attr('checked', true).click(function () {
                            checker.attr('checked', false);
                          });
                        }
                        else {
                          checker.parents('table').find(':checkbox[name^=\"" . $this->config['checkselector'] . "\"]').attr('checked', false);
                        }
                      });
                      });
                      /*]]>*/
                      </script> ";
            
    $this->fieldsetsJS = "jQuery(document).ready(function(){
          jQuery('." . $this->cssform . "_fieldset_collapsed').hide();
          /* jQuery('." . $this->cssform . "_fieldset ." . $this->cssform . "_fieldset_collapsed:first').slideDown();
          jQuery('." . $this->cssform . "_fieldset_title:first').toggleClass('" . $this->cssform . "_fieldset_title');*/
          
          jQuery('h3." . $this->cssform . "_fieldset_title').click(function(){
            if(jQuery(this).next().css('display')!='block'){
              /*jQuery('." . $this->cssform . "_fieldset_collapsed').slideUp(200);*/
              jQuery(this).next().slideDown(600);
              /*jQuery(this).next().css('display','block');*/
            } else {
              jQuery(this).next().slideUp(600);
              /*jQuery(this).next().css('display','none');*/
            }
            
            jQuery('." . $this->cssform . "_fieldset h3').removeClass();
            jQuery('." . $this->cssform . "_fieldset h3').addClass('" . $this->cssform . "_fieldset_title');
            
            jQuery(this).removeClass();
            jQuery(this).addClass('" . $this->cssform . "_fieldset_title');
          })
          jQuery('button.fieldset-expand_all').click(function(){
            jQuery('." . $this->cssform . "_fieldset_collapsed').slideDown(600);
          })
          jQuery('button.fieldset-collapse_all').click(function(){
            jQuery('." . $this->cssform . "_fieldset_collapsed').slideUp(600);
          })
  
          })";  
    
        mxGetLangfile(dirname(__FILE__) . DS . 'AdminForm' . DS . 'language');
    }
    /**
     * pmxAdminForm::__get()
     *
     * @param mixed $value_name
     * @return
     */
    public function __get($value_name)
    {
        if (isset($this->config[$value_name])) {
            return $this->config[$value_name];
        }
        return false;
    }
    /**
     * pmxAdminForm::__set()
     *
     * @param mixed $name
     * @param mixed $val
     * @return
     */
    public function __set($name, $val)
    {
        $this->config[$name] = $val ;
    }
    /**
     * pmxAdminForm::FormOpen()
     *
     * öffnet das Formular
     *
     * @param string $action
     * @param string $etype
     * @return string
     */
    public function FormOpen($action = "", $etype = "")
    {
        global $module_name;
        /* target-URL ermitteln */
        if (trim($action)) {
            $this->target_url = $action;
        }
        $taction = $this->target_url;
        $taction = str_replace("&amp;", "&", $taction);
        $taction = str_replace("&", "&amp;", $taction);
        /* enctype ermitteln */
        if ($etype == "") {
            $encrypt = "enctype=\"" . $this->enctype . "\"";
        } else {
            $encrypt = "enctype=\"" . $etype . "\"";
            $this->enctype = $etype;
        }
        $this->config['formname'] = ($this->formname == "adminForm")?$this->formname . rand(10, 99):$this->formname;
        /* jetzt Ausgabe zusammenstellen */
        pmxHeader::add_jquery();
        if ($this->config['mainform']) pmxHeader::add_script_code("var adminForm = '" . $this->config['formname'] . "';");
        $onsubmit = ($this->checklistflag) ? " onsubmit=\"return validateForm();\"" : "";
        $tdtext = "\n";
    $tdtext .= "<div class=\"adminForm " . $this->cssform . "\">\n" ;
        $tdtext .= "<a name=\"" . $this->formname . "-container\" ></a>";
        $tdtext .= "<div id=\"" . $this->formname . "-adminformcontainer\" >";
        $tdtext .= (trim($this->title)) ? "<h3>" . $this->title . "</h3>" : "";
        // $tdtext .= "<div id='slideToggle'>\n";
        $tdtext .= "<form class=\"form " . $this->cssform . "\" action=\"" . $taction . "\" method=\"post\" " . $encrypt . " name=\"" . $this->formname . "\" id=\"" . $this->formname . "\"" . $onsubmit . " accept-charset=\"utf-8\">";
        $tdtext .= (trim($this->description)) ? "<p>" . $this->description . "</p>" : "";
        $tdtext .= "<input type=\"hidden\" name=\"hidemainmenu\" value=\"0\"  />" ;
        
        $this->formopenflag = true;
        return $tdtext;
    }
    /**
     * pmxAdminForm::FormClose()
     *
     * schließt die Form
     *
     * @return string
     */
    public function FormClose()
    {
        $tdtext = "";
        if ($this->homelink) {
            $tdtext .= $this->_getHomebutton();
        }
    
        $tdtext .= "<input type=\"hidden\" name=\"boxchecked\" value=\"0\"  />" ;
        $tdtext .= "</div></form></div>\n";
        $this->formopenflag = false;
        if ($this->infobutton) {
            // pmxHeader::add_style('layout/style/default.tooltip.css');
            pmxHeader::add_jquery('ui/jquery.ui.tooltip.js');
            pmxHeader::add_script_code("
      $(document).ready(function()
      {
        $('." . $this->cssform . " img[title]').tooltip({
        position: {
        my: 'center bottom-20',
        at: 'center top'
        }
        })
      });");
        }
        if ($this->checklistflag) {
            pmxHeader::add_script(PMX_JAVASCRIPT_PATH . 'mx_checklist.js');
      $tdtext .= $this->checklisJS;
        }
        return $tdtext;
    }
    /**
     * pmxAdminForm::FieldSetOpen()
     *
     * öffnet ein Fieldset
     *
     * @param mixed $fieldname
     * @param mixed $legend
     * @param string $extendedtext
     * @param mixed $collapsible
     * @return string
     */
    public function FieldSetOpen($fieldname, $legend, $extendedtext = "", $collapsible = false, $attributes = array())
    {
        // zusätzlichen Parameter $attributes in Funktionsdefinition,
        // dieser wird dann aufgedröselt und dem fieldset-Tag zugewiesen
        $class = self::extract_class($attributes);
        $fstyle = "width:100%;";
        if (array_key_exists("style", $attributes)) {
            $fstyle = $attributes['style'];
            unset($attributes['style']);
        }
    
        $attributes = self::get_attributes_from_array($attributes);
        $tdtext = "<div style=\"display:inline-block;vertical-align:top;" . $fstyle . "\">";
        $class .= ' ' . $class . ' ' . $attributes;
        if ($class) {
            $class = trim($class);
        }
    //$tdtext .="<div class=\"fieldset\">";
        if ($collapsible) {
            $this->collapsibleflag = true;
            $tdtext .= "<div class=\"card\" ><legend class=\"" . $this->cssform . " card-header\">" . $legend . "</legend>";
            $tdtext .= "<div class=\"" . $this->cssform . "_fieldset_collapsed\">";
        } else {
            $tdtext .= "<div class=\"card\" ><legend class=\"" . $this->cssform . " card-header\">" . $legend . "</legend>";
            $tdtext .= "<div class=\"" . $this->cssform . "_fieldset_open\">";
        }
        $tdtext .= "<div class=\"card-block\">";
        $tdtext .= ($extendedtext) ? '<div class="fielddescription">' . $extendedtext . '</div>' : "";
        return $tdtext;
    }
    /**
     * pmxAdminForm::FieldSetClose()
     *
     * schließt fieldset
     *
     * @param mixed $button button=true dann wird am unteren Rand ein "Accept"-Button eingefügt
     * @return
     */
    public function FieldSetClose($button = false)
    {
        $tdtext = "";
        if ($button || $this->acceptbutton) {
            /*$tdtext .= "<hr />";*/
            $tdtext .= $this->_SetSubmitButton($button);
        }
        $tdtext .= "</div>";
        $tdtext .= "</div>";
        $tdtext .= "</div>";
        //$tdtext .= "</div>";//fieldset
        $tdtext .= "</div>";
        return $tdtext;
    }
    /**
     * pmxAdminForm::_SetSubmitButton()
     *
     * Zeigt einen "accept"-Buton an
     *
     * @param boolean $show_as_text
     * @return
     */
    private function _SetSubmitButton($show_as_text = false)
    {
        $tt = "";
        if ($this->checklistflag) {
            $tt = "onclick=\"javascript:if(document[adminForm].boxchecked.value==0){alert('" . _NOACTION . "');} else {onsubmitform();}\"";
        }     
        $img = $this->tb_pic_path . "accept.png";
        $img2 = $this->tb_pic_path . "up.png";
        $tdtext = "<div class=\"form-submit align-" . $this->tb_direction . "\">\n";
        $tdtext .= "<button class=\"button\" type=\"submit\" value=\"accept\" name=\"toolbarsubmit\" $tt title=\"" . _ACCEPT . "\"  >";
        if ($show_as_text) {
            $tdtext .= _ACCEPT;
        } else {
            $tdtext .= "<img  src=\"" . $img . "\" style=\"width:auto; height:16px;\" title='" . _ACCEPT . "' alt='" . _ACCEPT . "'  />";
        }
        $tdtext .= "</button>";
        if ($this->fieldhomebutton) {
            $tdtext .= $this->_getHomebutton();     
        }
        $tdtext .= "</div>";
        return $tdtext;
    }
  private function _getHomebutton ()
  {
            $img2 = $this->tb_pic_path . "up.png";
            $tdtext = "<div class=\"homebutton\">\n";
            $tdtext .= "<a href=\"#" . $this->formname . "-container\" >";
            $tdtext .= "<img src=\"$img2\" title='" . _HOME . "' alt='" . _HOME . "' />";
            $tdtext .= "<img src=\"$img2\" title='" . _HOME . "' alt='" . _HOME . "' />";
            $tdtext .= "</a></div>";
      return $tdtext;
  }
  
    /**
     * pmxAdminForm::FieldSetButton()
     *
     * Button "alle Öffnen" und "schließen" ausgebn
     *
     * @return
     */
    public function FieldSetButton()
    {
        $tdtext = "";
        if ($this->config['buttontext'] == true) {
            $tdtext .= "<div style=\"display:inline-block; width:100%;\">";
            $tdtext .= '<p class="align-right" style="margin-right:1em;"><button type="button" class="fieldset-expand_all">' . _EXPANDALL;
            $tdtext .= '</button>&nbsp;&nbsp;<button type="button" class="fieldset-collapse_all">' . _COLLAPSEALL . '</button></p>';
            $tdtext .= "</div>\n";
        } else {
            $img = $this->config['tb_pic_path'] . "collapsible_all.png";
            $img2 = $this->config['tb_pic_path'] . "expand_all.png";
            $tdtext .= "<div style=\"display:inline-block; width:100%;\">";
            $tdtext .= '<p class="align-right" style="margin-right:1em;"><button type="button" class="fieldset-expand_all" title="' . _EXPANDALL . '" ><img src="' . $img2 . '" title="' . _EXPANDALL . '"  style="height:16px; width:16px;"/>';
            $tdtext .= '</button><button type="button" class="fieldset-collapse_all" title="' . _COLLAPSEALL . '"><img src="' . $img . '" title="' . _COLLAPSEALL . '" style="height:16px; width:16px;" /></button></p>';
            $tdtext .= "</div>\n";
        }
        return $tdtext;
    }
    /**
     * pmxAdminForm::Show()
     *
     * gibt ein komplettes Formular aus
     *
     * @return
     */
    public function Show()
    {
        return $this->getAdminForm();
    }
    /**
     * pmxAdminForm::getAdminForm()
     *
     * stellt das Formular zusammen
     *
     * @return
     */
    public function getAdminForm()
    {
        $output = "";
        /* Form öffnen */
        if (!$this->formopenflag) $output .= $this->FormOpen();
        /* wenn Toolbar top/both angegeben, dann Toolbar ausgeben */
        if ($this->tb_flag && $this->tb_show != "bottom") $output .= $this->getToolbar();
        $output .= $this->getForm();
        /* wenn Toolbar bottom/both angegeben, dann Toolbar ausgeben */
        if ($this->tb_flag && $this->tb_show != "top") $output .= $this->getToolbar();
        /* Form schließen*/
        $output .= $this->FormClose();
        return $output;
    }
    /**
     * pmxAdminForm::getForm()
     *
     * @return
     */
    public function getForm()
    {
        $output = "";
        //pmxHeader::add_script(PMX_JAVASCRIPT_PATH . 'mx_fieldsets.js');
    $this->fieldsetsJS = "jQuery(document).ready(function(){
          jQuery('." . $this->cssform . "_fieldset_collapsed').hide();
          
          
          jQuery('legend." . $this->cssform . "_fieldset_title').click(function(){
            if(jQuery(this).next().css('display')!='block'){
              
              jQuery(this).next().slideDown(600);
              
            } else {
              jQuery(this).next().slideUp(600);
              
            }
            
            jQuery('." . $this->cssform . "_fieldset legend').removeClass();
            jQuery('." . $this->cssform . "_fieldset legend').addClass('" . $this->cssform . "_fieldset_title legend');
            
            jQuery(this).removeClass();
            jQuery(this).addClass('" . $this->cssform . "_fieldset_title legend');
          })
          jQuery('button.fieldset-expand_all').click(function(){
            jQuery('." . $this->cssform . "_fieldset_collapsed').slideDown(600);
          })
          jQuery('button.fieldset-collapse_all').click(function(){
            jQuery('." . $this->cssform . "_fieldset_collapsed').slideUp(600);
          })
  
          })";    
    pmxHeader::add_script_code($this->fieldsetsJS);
        /* wenn klappbare Fieldsets, dann button einblenden  */
    $this->collapsibleshowbutton=(in_array($this->collapsibleshowbutton,array("top","bottom","both","none")))?$this->collapsibleshowbutton:"both";
        if ($this->collapsibleflag && 
      ($this->collapsibleshowbutton=="both" OR $this->collapsibleshowbutton=="top"));
        /* jetzt Formularelemente ohne Fieldsets ausgeben */
        if (count($this->noformset) > 0) {
            $output .= "<div class=\"formcontainer\">\n";
            foreach ($this->noformset as $dummy => $field) {
                $output .= $field;
            }
            $output .= "\n</div>\n";
        }
        /* jetzt Fieldsets und Inhalte ausgeben */
        if (count($this->formset) > 0) {
            $output .= "<div class=\"formcontainer\">\n";
            foreach ($this->formset as $field) {
                $output .= $this->getFieldSet($field['name']);
            }
            $output .= "\n</div>\n";
        }
        /* wenn klappbare Fieldsets, dann button einblenden am Ende auch noch mal */
        if ($this->collapsibleflag && 
      ($this->collapsibleshowbutton=="both" OR $this->collapsibleshowbutton=="bottom"));
        return $output;
    }
    /**
     * pmxAdminForm::getFieldSet()
     *
     * gibt den HTML-Code des kompletten FieldSets zurück
     *
     * @param mixed $fname
     * @return
     */
    public function getFieldSet($fname)
    {
        $field = $this->formset[$fname];
        $output = "";
        
        $output .= $this->FieldSetOpen($field['name'], $field['title'], $field['legend'], $field['collapsible'], $field['attributes']);
        foreach ($field['child'] as $key) {
            $output .= $key;
        }
        $output .= $this->FieldSetClose($this->buttontext);
        
        return $output;
    }
    /**
     * pmxAdminForm::addFieldSet()
     *
     * erzeugt ein Fieldset
     *
     * @param mixed $fname
     * @param mixed $ftitle
     * @param string $flegend
     * @param boolean $collapsible
     * @param mixed $attributes
     * @return nothing
     */
    public function addFieldSet($fname, $ftitle, $flegend = "", $collapsible = false, $attributes = array())
    {
        $this->add("", "fieldset", $ftitle, $collapsible, $fname, $flegend, 0, $attributes);
    }
    /**
     * pmxAdminForm::add()
     *
     * ffieldset    = Name des Fieldset
     * fftype       = Type des Formularelementes
     * input        = Texteingabe
     * output       = allgemeine Textausgabe
     *
     * yesno       = ja/nein
     * select      = Auswahlliste $value= array( Anzige => wert )
     * checkbox    = CheckBox
     * password    = Passworteingabe
     * textarea    = Textbox
     * editor      = Wysiwyg Editor
     * radio       = Radiobuttons
     * html        = html-code allgemein
     * file        = Dateiupload
     * filebrowse  = Dateimanager für Texteingabe Feld
     * .....
     *
     * flegend      = Feldbeschreibung (vor dem Formelement)
     * ffieldname   = Name des Formularfeldes
     * fvalue       = voreingestellter Wert des Eingabeelementes
     * fdescription = zusatzbeschreibung
     * ffieldlen    = Länge des Eingabefeldes (bei input/Text z.Bsp.)
     * fextern      = sonstige angaben, bei verschiedenen Elementen sinnvoll
     *
     * @param mixed $ffieldset
     * @param mixed $ftype
     * @param mixed $ffieldname
     * @param string $fvalue
     * @param string $flegend
     * @param string $fdesc
     * @param integer $ffieldlen
     * @param string $fextern
     * @param mixed $frequired
     * @return
     */
    public function add($ffieldset, $ftype, $ffieldname, $fvalue = "", $flegend = "", $fdesc = "", $ffieldlen = 0, $fextern = "", $frequired = false)
    {
        $forminput = "";
        // $fdesc= strip_tags ($fdesc);
        if ($ftype != "fieldset") {
            $linestyle1 = "class=\"forminputtitle\"";
            $linestyle2 = "class=\"forminputfield\"";
            $linestyle3 = "class=\"forminputdesc\"";
            $fdescription = "";
            if ($this->infobutton && ($fdesc)) {
                $fdescription = "<img src=\"" . $this->tb_pic_path . "info.png\" title=\"" . htmlspecialchars(strip_tags($fdesc), ENT_COMPAT | ENT_HTML5, 'UTF-8', false) . "\" alt=\"\" style=\"height:16px;width:auto;\" />";
                $linestyle3 = "class=\"forminputinfo\"";
                $this->infobuttonflag = true;
            } else {
                $fdescription = $fdesc; //;
            }
            $ifrequired = ($frequired) ? ' required="required"' : "";
            if ($ffieldset) {
                $inputid = $this->formname . $this->formset[$ffieldset]['name'] . count($this->formset[$ffieldset]['child']);
            } else {
                $inputid = (is_array($ffieldname))?"table":$ffieldname;
            }
            $inputid = str_replace(array("[", "]", "(", ")", " ", ".", ",", ";"), "", $inputid);
            $inputlabel = "<label for=\"" . $inputid . "\" $linestyle1 title=\"" . htmlspecialchars(strip_tags($fdesc), ENT_COMPAT | ENT_HTML5, 'UTF-8', false) . "\" >" . $flegend . "</label>";
        }
        $fdesc = htmlspecialchars(strip_tags($fdesc), ENT_COMPAT | ENT_HTML5, 'UTF-8', false);
        switch ($ftype) {
            case "fieldset":
                $fieldname = str_replace(" ", "", $flegend);
                $this->formset[$fieldname]['name'] = $fieldname;
                $this->formset[$fieldname]['title'] = $ffieldname;
                $this->formset[$fieldname]['legend'] = $fdesc;
                $this->formset[$fieldname]['collapsible'] = $fvalue;
                $this->formset[$fieldname]['child'] = array();
                $this->formset[$fieldname]['attributes'] = $fextern;
                if ($fvalue) {
                    $this->collapsibleflag = true;
                    $this->fieldset_array[$fieldname] = $flegend;
                }
                return true;
            case "output":
                $forminput .= "<div class=\"formcontent\">";
                if (trim($flegend)) $forminput .= $flegend;
                $forminput .= $ffieldname . "</div>";
                break;
            case "info":
                $forminput .= "<div class=\"info\">";
                if (trim($flegend)) $forminput .= $flegend;
                $forminput .= $ffieldname . "</div>";
                break;
            case "note":
                $forminput .= "<div class=\"note\">";
                if (trim($flegend)) $forminput .= $flegend;
                $forminput .= $ffieldname . "</div>";
                break;
            case "error":
                $forminput .= "<div class=\"error\">";
                if (trim($flegend)) $forminput .= $flegend;
                $forminput .= $ffieldname . "</div>";
                break;
            case "warning":
                $forminput .= "<div class=\"warning\">";
                if (trim($flegend)) $forminput .= $flegend;
                $forminput .= $ffieldname . "</div>";
                break;
            case "highlight":
                $forminput .= "<div class=\"highlight\">";
                if (trim($flegend)) $forminput .= $flegend;
                $forminput .= $ffieldname . "</div>";
                break;
            case "html":
                $forminput = $ffieldname;
                break;
            case "special":
                // der per $value übergebene Inhalt wird in dem Bereich des
                // eigentlichen Formularfeldes angezeigt. So können beliebige
                // andere Feldkombinationen hier verwendet werden
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                $ffieldlen = (intval($ffieldlen) == 0) ? 30 : intval($ffieldlen);
                // $class
                $forminput = "<div class=\"form-group{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}><div id=\"" . $inputid . "\" title=\"" . $fdesc . "\" " . $fextern . ">" . $fvalue . "</div></div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "input":
            default:
                // falls im Attribut-Parameter die eine CSS Klasse angegeben wurde, diese
                // extrahieren und dem umgebenden <div> mit der Klasse forminputline zuweisen.
                // diese geschieht bei allen folgenden und relevanten Tags hier im Switch/case
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                $ffieldlen = (intval($ffieldlen) == 0) ? 30 : intval($ffieldlen);
                // $class
                $forminput = "<div class=\"form-group{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}><input class=\"form-control\" type=\"text\" name=\"" . $ffieldname . "\" value=\"" . $fvalue . "\" id=\"" . $inputid . "\" title=\"" . $fdesc . "\" size=\"{$ffieldlen}\" " . $fextern . $ifrequired . "/></div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "password":
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                $ffieldlen = (intval($ffieldlen) == 0) ? 20 : intval($ffieldlen);
                $forminput = "<div class=\"form-group{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}><input type=\"password\" name=\"" . $ffieldname . "\" value=\"" . $fvalue . "\" id=\"" . $inputid . "\" title=\"" . $fdesc . "\" size=\"{$ffieldlen}\" " . $fextern . $ifrequired . "/></div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "button":
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                $forminput = "<div class=\"form-group{$class}\">";
                $forminput .= "<label for=\"" . $inputid . "\" $linestyle1 title=\"\" >&nbsp;</label>";
                $forminput .= "<div {$linestyle2}><button type=\"button\" name=\"" . $ffieldname . "\" value=\"" . $fvalue . "\" id=\"" . $inputid . "\" title=\"" . $fdesc . "\" " . $fextern . ">";
                $forminput .= $flegend;
                $forminput .= "</button></div><div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "submitbutton":
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                $forminput = "<div class=\"form-group{$class}\">";
                $forminput .= $inputlabel;
                //$forminput .= "<label for=\"" . $inputid . "\" $linestyle1 title=\"\" >&nbsp;</label>";
                $forminput .= "<div {$linestyle2}><button type=\"submit\" name=\"toolbarsubmit\" value=\"" . $ffieldname . "\" id=\"" . $inputid . "\" title=\"" . $fvalue . "\" " . $fextern . ">";
                $forminput .= $fvalue;
                $forminput .= "</button></div><div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "hidden":
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                $forminput .= "<input type=\"hidden\" name=\"" . $ffieldname . "\" value=\"" . $fvalue . "\"" . $fextern . " class=\"hidden\" />";
                break;
            case "file":
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                $ffieldlen = (intval($ffieldlen) == 0) ? 30 : intval($ffieldlen);
                $forminput = "<div class=\"form-group{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}><input type=\"file\" name='" . $ffieldname . "[]' value='" . $fvalue . "' id=\"" . $inputid . "\" title=\"" . $fdesc . "\" size=\"{$ffieldlen}\" " . $fextern . $ifrequired . " /></div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                // multipart/form-data zwangsweise setzen
                $this->enctype = "multipart/form-data";
                break;
            case "yesno":
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                $forminput = "<div class=\"form-group{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}>";
                $forminput .= "<select class=\"form-control\" name=\"" . $ffieldname . "\" size=\"2\"  id=\"" . $inputid . "\" title=\"" . $fdesc . "\" " . $fextern . " >
                <option value=\"1\"" . (($fvalue == 1) ? ' selected="selected" class="current"' : '') . ">" . _YES . "</option>
                <option value=\"0\"" . (($fvalue != 1) ? ' selected="selected" class="current"' : '') . ">" . _NO . "</option>
                </select>";
                $forminput .= "</div><div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "yesnodefault":
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                $forminput = "<div class=\"form-group{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}>";
                $forminput .= "<select class=\"form-control\" name=\"" . $ffieldname . "\" size=\"1\"  id=\"" . $inputid . "\" title=\"" . $fdesc . "\" " . $fextern . " >
                <option value=\"-1\"" . (($fvalue == (-1)) ? ' selected="selected" class="current"' : '') . ">" . _DEFAULT . "&nbsp;</option>
                <option value=\"1\"" . (($fvalue == 1) ? ' selected="selected" class="current"' : '') . ">" . _YES . "</option>
                <option value=\"0\"" . (($fvalue == 0) ? ' selected="selected" class="current"' : '') . ">" . _NO . "</option>
                </select>";
                $forminput .= "</div><div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "checkbox":
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                /* wenn checkboxen in einem Array verwendet werden sollen, dürfen keine leeren Klammern bein Feldnamen stehen, immer arrayelement eindeutig referenzieren. */
                $fvalue = intval($fvalue);
                $fixe = "";
                if ($fvalue == "1") $fixe = "checked=\"checked\"";
                $forminput = "<div class=\"form-group{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}><input type=\"hidden\" name='" . $ffieldname . "' value='0' />";
                $forminput .= "<input type=\"checkbox\" name='" . $ffieldname . "' value='1' " . $fixe . "  id=\"" . $inputid . "\" title=\"" . $fdesc . "\"" . $ifrequired . $fextern . " /></div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "radio":
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                $fvalue = intval($fvalue);
                $fixe = "";
                if ($ffieldlen == "1") $fixe = "checked=\"checked\"";   /* hier rüber wird der aktivierte Butto bestimmt */
                $forminput = "<div class=\"form-group{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}>";
                $forminput .= "<input type=\"radio\" name='" . $ffieldname . "' value='". $fvalue . "' " . $fixe . " id=\"" . $inputid . "\" title=\"" . $fdesc . "\"" . $fextern . " /></div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "radiogroup":
                $class = self::extract_class($frequired);
                      
        /* über $fextern muss ein Array übergeben werden mit den Werten und den Bezeichnungen 
           Format = array("ausgabetext"=>"wert",....);
        */
        if (!is_array($fextern)) return;
                
        /* über $fvalue wird der "wert" übergeben, der ausgewählt werden soll   
        */
        
        /* ist ffieldlen > 0 dann wird die Liste horizontal ausgegeben, ansonsten vertikal */
        
        $ffieldlen=intval($ffieldlen);
        
        $forientation=($ffieldlen==0)?"<br />":"&nbsp";
        
                $forminput = "<div class=\"form-group{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}>";
          foreach ($fextern as $key=>$value) {
          $fixe =($fvalue==$value) ?"checked=\"checked\"":"";
          //$forminput .= "<div {$linestyle2}>";
          $forminput .= "<input type=\"radio\" name='" . $ffieldname . "' value='". $value . "' " . $fixe . "  title=\"" . $key . "\" />" . $key . $forientation;
          }
        $forminput .= "</div><div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "usergroup":
                /* nur im Adminbereich zulässig */
                if (defined('mxAdminFileLoaded')) {
                    $class = self::extract_class($fextern);
                    $fextern = self::get_attributes_from_array($frequired);
                    $usergroup = getAllAccessLevelSelectOptions($fvalue);
                    $arrlen = max(2, intval($ffieldlen));
                    $arrlen = min(5, $arrlen);
                    $forminput = "<div class=\"form-group{$class}\">";
                    $forminput .= $inputlabel;
                    $forminput .= "<div $linestyle2>";
                    $forminput .= "<select class=\"form-control\" name=\"" . $ffieldname . "\" size=\"" . $arrlen . "\"  id=\"" . $inputid . "\" title=\"" . $fdesc . "\"" . $fextern . ">";
                    $forminput .= "<option value=\"0\" " . ((0 == $fvalue) ? ' selected="selected" class="current"' : '') . " >" . _NONE . "</option>";
                    $forminput .= $usergroup . "</select></div>";
                    $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                    $forminput .= "</div>";
                }
                break;
            case "selectuser":
                $resultuser = sql_query("select uid,uname from " . $GLOBALS['user_prefix'] . "_users where user_stat>0 order by uname asc");
                while ($fuser = sql_fetch_assoc($resultuser)) {
                    $fextern[$fuser['uid']] = $fuser['uname'];
                }
                unset($fuser, $resultuser);
                $class = self::extract_class($frequired);
                $attributes = self::get_attributes_from_array($frequired);
                $arrlen = min(intval($ffieldlen), count($fextern));
                if (intval($ffieldlen) < 1) {
                    $arrlen = max(1, intval(count($fextern)));
                    $arrlen = (intval($arrlen) > 4) ? 4 : $arrlen;
                }
                $ausdruck = '<select class="form-control" name="' . $ffieldname . '" size="' . $arrlen . '"  id="' . $inputid . '" title="' . $fdesc . '"' . $attributes . '>';
                $ausdruck .= "<option value='0' " . ((0 == $fvalue) ? ' selected="selected" class="current"' : '') . " >" . _NONE . "&nbsp;&nbsp;</option>";
                /* foreach ($fextern as $key => $value) {
                    $sel = ($key == $fvalue) ? ' selected="selected" class="current"' : '';
                    $ausdruck .= "<option value='" . $key . "' " . $sel . " >" . $value . "&nbsp;&nbsp;</option>";
                } */
        foreach ($fextern as $key => $value) {
          if (is_array($fvalue) && in_array($key, $fvalue) && !in_array('0', $fvalue)){
          $sel = ' selected="selected" class="current"' ;
          } else {
          $sel = ($key == $fvalue) ? ' selected="selected" class="current"' : '';
          }
          $ausdruck .= "<option value='" . $key . "' " . $sel . " >" . $value . "&nbsp;&nbsp;</option>";
        }
                
                $ausdruck .= "</select>";
                $forminput = "<div class=\"form-group{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}>" . $ausdruck . "</div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "selectusergroup":
                $fextern = array();
                $resultusergroup = sql_query("select access_id,access_title from " . $GLOBALS['prefix'] . "_groups_access order by access_id");
                while ($fuser = sql_fetch_assoc($resultusergroup)) {
                    $fextern[$fuser['access_id']] = $fuser['access_title'];
                }
                unset($fuser, $resultusergroup);
                if (count($fextern) == 0) break;
                $class = self::extract_class($frequired);
                $attributes = self::get_attributes_from_array($frequired);
                $arrlen = max(2, intval($ffieldlen));
                $arrlen = min(4, $arrlen);
                // $arrlen = (intval($arrlen) > 4) ? 4 : $arrlen;
                $ausdruck = '<select class="form-control" name="' . $ffieldname . '" size="' . $arrlen . '"  id="' . $inputid . '" title="' . $fdesc . '"' . $attributes . '>';
                $ausdruck .= "<option value='-1' " . ((is_array($fvalue) && in_array('-1', $fvalue)) ? ' selected="selected" class="current"' : '') . " >" . _NONE . "&nbsp;&nbsp;</option>";
                // $ausdruck .= "<option value='0' " . ((is_array($fvalue) && in_array('0', $fvalue)) ? ' selected="selected" class="current"' : '') . " >" . _ALL . "&nbsp;&nbsp;</option>";
                foreach ($fextern as $key => $value) {
                    if (is_array($fvalue) && in_array($key, $fvalue) && !in_array('0', $fvalue)) {
                        $sel = ' selected="selected" class="current"' ;
                    } else {
                        $sel = ($key == $fvalue) ? ' selected="selected" class="current"' : '';
                    }
                    $ausdruck .= "<option value='" . $key . "' " . $sel . " >" . $value . "&nbsp;&nbsp;</option>";
                }
                $ausdruck .= "</select>";
                $forminput = "<div class=\"form-group{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}>" . $ausdruck . "</div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "select":
                if (!is_array($fextern)) {
                    /* nur was ausgeben, wenn auch eine Liste per $fextern übergeben wurde */
                    break;
                }
                $class = self::extract_class($frequired);
                $attributes = self::get_attributes_from_array($frequired);
                $arrlen = min(intval($ffieldlen), count($fextern));
                if (intval($ffieldlen) < 1) {
                    $arrlen = intval(count($fextern));
                    $arrlen = (intval($arrlen) > 4) ? 4 : $arrlen;
                }
        
        if (is_array($fvalue)) $attributes .=" multiple=\"multiple\"";
        
                $ausdruck = '<select class="form-control" name="' . $ffieldname . '" size="' . $arrlen . '"  id="' . $inputid . '" title="' . $fdesc . '"' . $attributes . '>';
                foreach ($fextern as $key => $value) {
          if (is_array($fvalue)) {
            $sel= (array_search($value,$fvalue)===FALSE)?"":' selected="selected" class="current"';
          } else {
            $sel = ($value == $fvalue) ? ' selected="selected" class="current"' : '';
          }
                    $ausdruck .= "<option value='" . $value . "' " . $sel . " >" . $key . "&nbsp;&nbsp;</option>";
                }
                $ausdruck .= "</select>";
                $forminput = "<div class=\"form-group{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}>" . $ausdruck . "</div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "selectlanguage":
                /* Sprachauswahl   */
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                // TODO: das ist nicht fertig, könnte auch über $fextern gesteuert werden
                $morepara = '';
                $withempty = false;
                $ausdruck = "";
                $languageslist = mxGetAvailableLanguages();
                if ($withempty) {
                    $languageslist[_ALL] = '';
                    ksort($languageslist);
                }
                $options = array();
                $options[] = '<option value="ALL"' . (("ALL" == $fvalue) ? ' selected="selected" class="current" ' : '') . ' >' . _ALL . '</option>';
                foreach($languageslist as $alt => $value) {
                    $options[] = '<option value="' . $value . '"' . (($value == $fvalue) ? ' selected="selected" class="current" ' : '') . ' >' . $alt . '</option>';
                }
                $ausdruck .= '<select class="form-control" name="' . $ffieldname . '" ' . $fextern . ' id="' . $inputid . '" title="' . $fdesc . '"' . $fextern . '>' . implode("\n", $options) . '</select>';
                $forminput = "<div class=\"form-group{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}>" . $ausdruck . "</div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "optiongroup":
                /* fextern enthält die komplette Auswahlliste mit allen <optiongroup> und <option> inkl. abschließender Tags */
                if (!is_array($fextern)) {
                    /* nur was ausgeben, wenn auch eine Liste per $fextern übergeben wurde */
                    break;
                }
                $class = self::extract_class($frequired);
                $attributes = self::get_attributes_from_array($frequired);
                $arrlen = min(intval($ffieldlen), count($fextern));
                if (intval($ffieldlen) < 1) {
                    $arrlen = intval(count($fextern));
                    $arrlen = (intval($arrlen) > 4) ? 4 : $arrlen;
                }
                $ausdruck = "<select class=\"form-control\" name=\"" . $ffieldname . "\" size='" . $arrlen . "'  id=\"" . $inputid . "\" title=\"" . $fdesc . "\"" . $attributes . ">";
                foreach ($fextern as $key => $value) {
                    $sel = ($value == $fvalue) ? ' selected="selected" class="current"' : '';
                    $ausdruck .= "<option value='$value' " . $sel . " >" . $key . "&nbsp;&nbsp;</option>";
                }
                $ausdruck .= "</select>";
                $forminput = "<div class=\"form-group{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}>" . $ausdruck . "</div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
      case "textbox":   // gibt eine Textarea eingepasst in die Zeile aus
                if (!isset($fextern['rows'])) {
                    $fextern['rows'] = intval($ffieldlen / 9);
                }
                $style = '';
                if (isset($fextern['style'])) {
                    $style = $fextern['style'];
                    unset($fextern['style']);
                }
                $attributes = self::get_attributes_from_array($fextern);
                $class = self::extract_class($fextern);
                // $intext = htmlspecialchars($fvalue, ENT_COMPAT | ENT_HTML5, 'UTF-8', false);
                $ausdruck = "<textarea class=\"form-control\" name=\"" . $ffieldname . "\" id=\"" . $inputid . "\" title=\"" . $fdesc . "\" style=\"width:90% " . $style . "\"" . $attributes . ">" . $fvalue . "</textarea>";
                $ausdruck .= "<input type=\"hidden\" name=\"spaw\" value=\"0\" />";
                $forminput = "<div class=\"form-group{$class}\" 
        >";
                 $forminput .= $inputlabel;
               
                $forminput .= "<div class=\"forminputfield{$class}\" >" . $ausdruck . "</div>";
                $forminput .= "<div  {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "textarea":  // gibt eine Textarea über die gesammte Breite aus
                if (!isset($fextern['rows'])) {
                    $fextern['rows'] = intval($ffieldlen / 9);
                }
                $style = '';
                if (isset($fextern['style'])) {
                    $style = $fextern['style'];
                    unset($fextern['style']);
                }
                $attributes = self::get_attributes_from_array($fextern);
                $class = self::extract_class($fextern);
                // $intext = htmlspecialchars($fvalue, ENT_COMPAT | ENT_HTML5, 'UTF-8', false);
                $ausdruck = "<textarea class=\"form-control\" name=\"" . $ffieldname . "\" id=\"" . $inputid . "\" title=\"" . $fdesc . "\" style=\"1em;width:95%;" . $style . "\"" . $attributes . ">" . $fvalue . "</textarea>";
                $ausdruck .= "<input type=\"hidden\" name=\"spaw\" value=\"0\" />";
                $forminput = "<div class=\"form-group{$class}\" >";
                //$forminput .= "<label for=\"" . $inputid . "\" class=\"forminputline{$class} title=\"" . htmlspecialchars(strip_tags($fdesc), ENT_COMPAT | ENT_HTML5, 'UTF-8', false) . "\" >" . $flegend . "</label>";
                $forminput .= "<div {$linestyle2}>" . $flegend . "&nbsp;</div>";                //$forminput .= "<div {$linestyle2}>" . $flegend . "&nbsp;</div>";
                $forminput .= "<div class=\"form-group{$class}\" style='width:100%'>" . $ausdruck . "</div>";
                $forminput .= "<div class=\"forminputdesc {$class}\" style='width:100%'>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
      
            /* Wysiwyg Editor */
            case "editor":
        /* übergabe der Parameter für den Editor in einem array über $fextern
        * mode= 'normal','full','mini'
        * z.Bsp. array('mode'=>'mini')
        *   siehe auch : \includes\classes\Textarea.php
        */
                switch (true) {
                    case $fextern === false:
                        $class = '';
                        /* Wenn $fextern false ist, wird kein Wysiwyg-Editor angezeigt */
                        $attr['wysiwyg'] = false;
                        break;
                    case is_array($fextern):
                        /* ansonsten muss $fextern ein Array sein, die Werte
                         * werden als Parameter für die Textarea-Klasse verwendet.
                         * Auch hiermit kann der Wysiwyg-Modus abgeschaltet werden.
                         */
                        $class = self::extract_class($fextern);
                        $attr = $fextern;
                        break;
                    default:
                        $class = '';
                        $attr = array();
                }
                /* diese Beiden Werte müssen immer über die add-Parameter kommen */
                $attr['name'] = $ffieldname;
                $attr['value'] = $fvalue;
                /* Wenn der add-Parameter $ffieldlen realistischen Wert hat und die Höhe nicht expliziet angegeben wurde, $ffieldlen als Höhe (in Pivel) verwenden */
                if (intval($ffieldlen) >= 50 && !isset($attr['height'])) {
                    $attr['height'] = $ffieldlen;
                }
                /* die gesammelten Attribute werden der Textarea-Klasse bereits im Konstruktor übergeben */
                $editor = load_class('Textarea', $attr);
                /* nochmal guggen ob wysiwyg überhaupt aktiviert */
                switch (true) {
                    case $fextern === false:
                    case isset($attr['wysiwyg']) && !$attr['wysiwyg']:
                        $editor->setWysiwyg(false);
                        $spaw = 0;
                        break;
                    default:
                        $spaw = intval($editor->getWysiwyg());
                        break;
                }
                /* HTML-Ausgabe der Textarea-Klasse abrufen */
                $ausdruck = $editor->getHtml();
                $ausdruck .= "<input type=\"hidden\" name=\"spaw\" value=\"" . $spaw . "\" id=\"" . $inputid . "\" />";
                $forminput = "<div class=\"form-group{$class} clearfix\">";
                $forminput .= "<div {$linestyle2}>" . $flegend . "&nbsp;</div>";
                $forminput .= "<div class=\"forminputline clear {$class}\" style='width:100%'>" . $ausdruck . "</div>";
                $forminput .= "<div class=\"forminputdesc {$class}\" style='width:100%'>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            /* HTML5 Formularelemente */
            case "number": // 
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                $ffieldlen = (intval($ffieldlen) == 0) ? 10 : intval($ffieldlen);
                $forminput = "<div class=\"form-group{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}><input class=\"form-control\" type=\"" . $ftype . "\" name=\"" . $ffieldname . "\" value=\"" . $fvalue . "\" id=\"" . $inputid . "\" title=\"" . $fdesc . "\" size=\"{$ffieldlen}\" " . $fextern . $ifrequired . " /></div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
      case "range": // über fextern kann min= und Max= step= festgelegt werden
                
        $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                $ffieldlen = (intval($ffieldlen) == 0) ? 30 : intval($ffieldlen);
                $forminput = "<div class=\"form-group{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}>";
        $forminput .= "<input class=\"inputrange\" type=\"" . $ftype . "\" name=\"" . $ffieldname . "\" value=\"" . $fvalue . "\" id=\"" . $inputid . "\" title=\"" . $fdesc . "\" size=\"{$ffieldlen}\" " . $fextern . $ifrequired . " oninput=\"" . $inputid . "x.value=parseInt(" . $inputid . ".value)\" />";
        $forminput .= "<output class=\"outputrange\" name=\"" . $inputid . "x\" for=\"" . $inputid . "\">".$fvalue."</output>";
        $forminput .= "</div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "date":
                /*todo JS-Datepicker einbauen*/
                pmxHeader::add_jquery('ui/jquery.ui.datepicker.js',
                    'ui/i18n/jquery.ui.datepicker-' . _DOC_LANGUAGE . '.js'
                    );
                $datepickeroptions = (trim($fextern)) ? ",{" . $fextern . "}" : "";
                pmxHeader::add_script_code('
                    $(function() {
                        $("#' . $inputid . '").datepicker($.datepicker.regional[ "' . _DOC_LANGUAGE . '" ]' . $datepickeroptions . ');
                    });');
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                $ffieldlen = (intval($ffieldlen) == 0) ? 30 : intval($ffieldlen);
                $forminput = "<div class=\"form-group{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}><input class=\"form-control\" type=\"text\" name=\"" . $ffieldname . "\" value=\"" . $fvalue . "\" id=\"" . $inputid . "\" title=\"" . $fdesc . "\" size=\"{$ffieldlen}\" " . $fextern . $ifrequired . "/></div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "datetime":
                /*todo JS-Datepicker einbauen*/
                pmxHeader::add_jquery('ui/jquery.ui.datepicker.js',
                    'ui/i18n/jquery.ui.datepicker-' . _DOC_LANGUAGE . '.js',
                    'ui/jquery.ui.slider.js'
                    );
                pmxHeader::add_jquery('jquery.timepicker.js');
                pmxHeader::add_style('layout/jquery/css/timepicker.css');
                $datepickeroptions = (trim($fextern)) ? "," . $fextern : "";
                pmxHeader::add_script_code('
                    $(function() {
                        $("#' . $inputid . '").datetimepicker( {
                            timeFormat: "HH:mm",
                            currentText: "' . _ADM_MESS_TODAY . '",
                            closeText: "' . _SAVE . '",
                            ' . $datepickeroptions . '
                        });
                    });');
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                $ffieldlen = (intval($ffieldlen) == 0) ? 30 : intval($ffieldlen);
                $forminput = "<div class=\"forminputline{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}><input class=\"form-control\" type=\"text\" name=\"" . $ffieldname . "\" value=\"" . $fvalue . "\" id=\"" . $inputid . "\" title=\"" . $fdesc . "\" size=\"{$ffieldlen}\" " . $fextern . $ifrequired . "/></div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "datetime-local":
            case "month":
            case "week":
            case "time":
                pmxHeader::add_jquery('ui/jquery.ui.datepicker.js',
                    'ui/i18n/jquery.ui.datepicker-' . _DOC_LANGUAGE . '.js',
                    'ui/jquery.ui.slider.js'
                    );
                pmxHeader::add_jquery('jquery.timepicker.js');
                pmxHeader::add_style('layout/jquery/css/timepicker.css');
                $datepickeroptions = (trim($fextern)) ? "," . $fextern : "";
                pmxHeader::add_script_code('
                    $(function() {
                        $("#' . $inputid . '").timepicker( {
                            timeFormat: "HH:mm",
                            currentText: "' . _ADM_MESS_TODAY . '",
                            closeText: "' . _SAVE . '",
                            timeOnlyTitle:"' . _SELECTTIME . '",
                            ' . $datepickeroptions . '
                        });
                    });');
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                $ffieldlen = (intval($ffieldlen) == 0) ? 30 : intval($ffieldlen);
                $forminput = "<div class=\"forminputline{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}><input type=\"text\" name=\"" . $ffieldname . "\" value=\"" . $fvalue . "\" id=\"" . $inputid . "\" title=\"" . $fdesc . "\" size=\"{$ffieldlen}\" " . $fextern . $ifrequired . "/></div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "tel":
            case "search":
            case "url":
            case "email":
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                $ffieldlen = (intval($ffieldlen) == 0) ? 30 : intval($ffieldlen);
                $forminput = "<div class=\"forminputline{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}><input class=\"form-control\" type=\"" . $ftype . "\" name=\"" . $ffieldname . "\" value=\"" . $fvalue . "\" id=\"" . $inputid . "\" title=\"" . $fdesc . "\" size=\"{$ffieldlen}\" " . $fextern . $ifrequired . "/></div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "color":
                pmxHeader::add_jquery('color/js/colpick.js');
                pmxHeader::add_style('includes/javascript/jquery/color/css/colpick.css');
                pmxHeader::add_script_code('
                $(function() {
                  $("#' . $inputid . '").colpick( {
                    colorScheme:"light",
                    onSubmit: function(hsb, hex, rgb, el) {
                      $(el).val(hex);
                      $(el).parent().addcss("border-right-color", "#"+hex);
                      $(el).colpickHide();
                    },
                    onBeforeShow: function () {
                      $(this).colpickSetColor(this.value);
                      return false;
                    }
                  });
                  $("#' . $inputid . '").parent().click(
                    function () {
                      $(this).children().click();
                    });
                });');
                $fvalue = strtolower(trim($fvalue, '# '));
                $defcol = ($fvalue) ? '#' . $fvalue : 'transparent';
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                $ffieldlen = (intval($ffieldlen) == 0) ? 8 : intval($ffieldlen);
                $forminput = "<div class=\"forminputline{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2}>";
                $forminput .= "<span class=\"colpick-color-box\" style=\"padding-right:1em;border-right-color:" . $defcol . "\">";
                $forminput .= "<input pattern=\"[a-fA-f0-9]{6}\"type=\"text\" name=\"" . $ffieldname . "\" value=\"" . $fvalue . "\" id=\"" . $inputid . "\" title=\"" . $fdesc . "\" size=\"{$ffieldlen}\" " . $fextern . $ifrequired . " />";
                $forminput .= "</span></div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
                break;
            case "captcha":
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                // $ffieldlen = (intval($ffieldlen) == 0) ? 30 : intval($ffieldlen);
                $captcha_object = load_class('Captcha', $ffieldname);
                $captcha_object->set_active($fvalue);
                if ($captcha_object->get_active($ffieldname)) {
                    // $captcha_object->set_active();
                    $ausgabe = $captcha_object->show_inputfield(array("required"=>"required","autocomplete"=>"off")) . "<br />" . $captcha_object->show_image() . "<br />" . $captcha_object->show_reloadbutton();
                    $forminput = "<div class=\"forminputline{$class}\">";
                    $forminput .= ($inputlabel == "") ? $captcha_object->show_caption() : $inputlabel;
                    $forminput .= "<div {$linestyle2}>" . $ausgabe . "</div>";
                    $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                    $forminput .= "</div>";
                }
                break;
            case "filebrowse":
                // Dateimanager für Texteingabe Feld
                // spezielle Parameter über $fextern:
                // title, type, getback, root, alias(nur zusammen mit root)
                $fb = load_class('Filebrowse');
                if ($fb->is_active()) {
                    if (is_array($fextern)) {
                        if (isset($fextern['title'])) {
                            $fb->set('title', $fextern['title']);
                        } else if ($inputlabel) {
                            $fb->set('title', $flegend);
                        }
                        if (isset($fextern['type'])) {
                            $fb->set_type($fextern['type']);
                        }
                        if (isset($fextern['getback'])) {
                            $fb->set_getback($fextern['getback']);
                        }
                        if (isset($fextern['root'])) {
                            $path = trim($fextern['root'], ' ;,:./\\') . '/';
                            if (isset($fextern['alias'])) {
                                $fb->set_root($path, $fextern['alias']);
                            } else {
                                $fb->set_root($path);
                            }
                        }
                        unset($fextern['title'], $fextern['type'], $fextern['getback'], $fextern['root'], $fextern['alias']);
                    }
                    $fb->dialog();
                }
                $class = self::extract_class($fextern);
        
                $ffextern = self::get_attributes_from_array($fextern);
                $ffieldlen = (intval($ffieldlen) == 0) ? 25 : intval($ffieldlen);
                $forminput = "
                <div class=\"forminputline{$class}\">
                {$inputlabel}
                <div style=\"white-space:nowrap\" {$linestyle2}>
                  <input class=\"form-control\" type=\"text\" name=\"{$ffieldname}\" value=\"{$fvalue}\" id=\"{$inputid}\" title=\"{$fdesc}\" size=\"{$ffieldlen}\" {$ffextern} {$ifrequired} />";
                if ($fb->is_active()) {
          $forminput .= "
           <button id=\"xx{$inputid}xx\">" . _BROWSE . "</button>
           <script type=\"text/javascript\">
           /*<![CDATA[*/
            var yy{$inputid}yy = '{$inputid}';
            $('#xx{$inputid}xx').click(function() {
            pmxfilemanager(yy{$inputid}yy, true);
            return false;
            });
           /*]]>*/
           </script>";
          
          
          // Soll das Image dargestellt werden?
          $showimage = false;
          if (array_key_exists('showimage',$fextern )) {
            $showimage = $fextern ['showimage'];
          }
          $widthimage=$this->fieldimagesize;
          
          if ($showimage){
            $widthimage=$this->fieldimagesize; // standard erst mal setzen
            if (array_key_exists('width',$fextern )) {
              $widthimage = $fextern ['width'];
          }
          // Image-ID
            $img_id = $inputid."_img";
            // Image-Tag incl. JavaScript:
            $img = "<img src=\"{$fvalue}\" id=\"{$img_id}\"/>";
            $img .= "<script type=\"text/javascript\">
            /*<![CDATA[*/
            $('#{$inputid}').change(function() {
            $('#{$img_id}').attr('src', this.value);
            $('#{$img_id}').attr('style', 'width:{$widthimage}');
            });
            /* ]]> */
            </script>";
          
           //Image-Tag mit eigener style-class hinzufuegen:
           $forminput .= "<div class=\"filebrowseimage{$class}\">".$img."</div>";
           }
        }  
          $forminput .= "
            </div>
            <div {$linestyle3}>{$fdescription}</div>
            </div>";
                break;
      case "path":
        /* Path prüft, ob das eingegebene Verzeichnis vorhanden und besschreibbar ist, wenn nicht weird das Feld rot angezeigt.
           die Pfadangabe bezieht sich immer auf das root-Verzeichnis der Installation
        */      
                $class = self::extract_class($fextern);
                $fextern = self::get_attributes_from_array($fextern);
                $ffieldlen = (intval($ffieldlen) == 0) ? 30 : intval($ffieldlen);
                // chek path is writable
        $inputcolor="";
        if (!empty(trim($fvalue))) {
          if(substr($fvalue,strlen($fvalue)-1,1)!="/") $fvalue .="/";
          //$inputcolor= "style='".((is_writable($fvalue))?"background:#429943;color:#000000;":"background:#EE0000;color:#f71b1b;")."'";
          $inputcolor= ((!is_writable($fvalue))?"class='inputerror'":"class='inputok'");
          $fdesc = (!is_writable($fvalue))?_NOWRITABLE:_WRITABLE;
        }
                $forminput = "<div class=\"forminputline{$class}\">";
                $forminput .= $inputlabel;
                $forminput .= "<div {$linestyle2} ><input class=\"form-control\" ".$inputcolor ." type=\"text\" name=\"" . $ffieldname . "\" value=\"" . $fvalue . "\" id=\"" . $inputid . "\" title=\"" . $fdesc . "\" size=\"{$ffieldlen}\" " . $fextern . $ifrequired . "/></div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";
        break;
      
      case "table":
                $class = self::extract_class($fextern);
                /* kniffelig:....
          wenn über die Klasse eine Checklist gefordert wird, ist das höchste Priorität
          ansonten gilt, wenn manuell über 'checklistflag' ein Wert gesetzt ist, wird dieser Wert genommen
          ansonsten wird der Wert aus der Klasse genommen
        */
        $fextern['checklistflag']= (array_key_exists('checklistflag',$fextern))?(($this->checklistflag==true)?true:$fextern['checklistflag']):$this->checklistflag;
        $this->checklistflag=($fextern['checklistflag'])?true:$this->checklistflag;
        $fextern['checkselektor']=$this->checkselektor;
        $fextern['adminform']=true;
                /* die ersten beiden Parameter sind nicht optional */
        $ausdruck= self::ListTable($ffieldname,$fvalue,$fextern);
          
                $forminput = "<div class=\"forminputline{$class}\">";
                $forminput .= "<div {$linestyle2}>" . $flegend . "&nbsp;</div>";
        $forminput .= "<div class=\"list\">" . $ausdruck . "</div>";
                $forminput .= "<div {$linestyle3}>" . $fdescription . "</div>";
                $forminput .= "</div>";       
        break;
        }
        /* leere title-Attribute entfernen */
        $formoutput = str_replace(' title=""', '', $forminput);
        if (strlen(trim($forminput)) > 0 and $ffieldset == "") {
            $this->noformset[] = $formoutput;
        } else {
            if (strlen(trim($forminput)) > 0 and array_key_exists($ffieldset, $this->formset)) {
                $this->formset[$ffieldset]['child'][] = $formoutput;
            }
        }
        return;
    }
    public static function checkCaptcha($ffieldname = 'captcha')
    {
        global $prefix, $module_name, $WSCFG;
        $captcha_object = load_class('Captcha', $ffieldname);
    return $captcha_object->check($_POST, 'captcha');
    
        if (!$captcha_object->check($_POST, 'captcha')) {
            return false;
        } else {
            return true;
        }
    }
    /* Toolbar Basisfunktionen */
    /**
     * addToolbarLink     fügt eine Schaltfläche der Toolbar hinzu, mit einem Link
     * addToolbar .      fügt eine Schaltfläche der Toolbar hinzu, welche über das Formular ausgewertet werden kann
     * $name             :     STRING    Funktionsname, wird über CheckButton zurückgegeben, wird an den Funktionsnamen ein x angehängt,
     * wird die Checkliste eingeschaltet, zurückgegeben über Checkbutton aber nur der Funktionsname ohne X
     * optional $text  :    STRING     Schaltflächentext, wenn leer wird eine Konstante mit constant ("_".$name) gesucht...
     * optional $pic   :    STRING     pfad zum image , wenn leer oder nicht vorhanden wird der funktionsname verwendet und das
     * entsprechende Bild aus der standard-toolbar-image-liste genommen
     * wenn gar nichts vorhanden ist, wird blank.png verwendet
     * optional $listfunc    : true/false   - wenn true wird geprüft, ob min ein Wert aus der Checklist angeklickt ist (standard = false)
     *
     *
     * Bsp:
     *
     * $tb->addToolbar("save")     -     Anzeige : 'save.png' aus Toolbar-Images
     * Buttonbeschriftung aus _SAVE
     * Rückgabe: 'save' ... Checklist inaktiv
     *
     * $tb->addToolbar("editx") -    Anzeige : 'edit.png' aus Toolbar-Images
     * Buttonbeschriftung aus _EDIT
     * Rückgabe: 'edit' ... Checklist aktiv
     *
     * $tb->addToolbar("wild",_GANZWILD,"noch/wilderer/pfad/zum/image.png",true)
     * -    Anzeige: noch/wilderer/pfad/zum/image.png
     * Buttonbeschriftung : _GANZWILD
     * Rückgabe: 'wild' ... Checklist aktiv
     *
     *
     * $tb->addToolbarLink("news","modules.php?name=News")
     * -     Anzeige : 'news.png' aus Toolbar-Images
     * Buttonbeschriftung aus _NEWS
     * Rückgabe: link direkt zur URL
     */
    /**
     * pmxAdminForm::addToolbarLink()
     *
     * @param mixed $name
     * @param string $targeturl
     * @param string $text
     * @param string $pic
     * @return
     */
    public function addToolbarLink($name, $targeturl = '', $text = '', $pic = '', $alttext = '')
    {
        $this->_addToolbarButton($name, $text, $pic, false, 'link', $targeturl, $alttext );
    }
    /**
     * pmxAdminForm::addToolbar()
     *
     * @param mixed $name
     * @param string $text
     * @param string $pic
     * @param mixed $listfunc
     * @return
     */
    public function addToolbar($name, $text = '', $pic = '', $alttext = '', $listfunc = false)
    {
        $this->_addToolbarButton($name, $text, $pic, $listfunc, 'button', '', $alttext);
    }
      /**
     * pmxAdminForm::addToolbarSpace()
     * add whitespace to Toolbar
     * @return
     */
    public function addToolbarSpace()
    {
        $this->_addToolbarButton('', '', '', false, 'space', '', '');
    }
    /**
     * pmxAdminForm::_addToolbarButton()
     *
     * @param mixed $name
     * @param string $text
     * @param string $pic
     * @param mixed $listfunc
     * @param string $type
     * @param string $target
     * @return
     */
    private function _addToolbarButton($name, $text = '', $pic = '', $listfunc = false, $type = 'button', $target = '', $alttext = '')
    {
        $textconst = $text;
        /* erst mal den Namen prüfen und konvertieren */
        $namelist = (strtolower(substr($name, -1)) == "x") ? (substr($name, 0, -1)) : $name;
        /* Testen ob Listfunction definiert */
        if (strtolower(substr($name, -1)) == "x") {
            $listfunc = true;
        }
        /* um die $listfunc-Funktion zu aktivieren wird der Feldname des Checkboxen-Array's der zugehörigen Liste benötigt */
        if ($listfunc !== false) {
            $this->checklistflag = $listfunc;
        }
        /* wenn $text leer dann nach Konstanten suchen */
        if (trim($text == '')) {
            if (!defined("_" . strtoupper($namelist))) {
                $textconst = $namelist;
            } else {
                $textconst = constant("_" . strtoupper($namelist));
            }
        }
        /* Bild testen und festlegen , wenn gar nix geht, dann blank.png*/
    
        if ($pic == '') {
      // nachsehen ob eventuell im alternativen Pfad das Bild ist?
            if (file_exists($this->tb_pic_alternate_path . $namelist . ".png")) {
        // bild im angegebenen Pfad vorhanden
                $pic = $this->tb_pic_alternate_path . $namelist . ".png";
            } else {
        // nachsehen ob eventuell bei der AdminKlasse das Bild schon ist?
        if (file_exists($this->tb_pic_path . $namelist . ".png")) {
          $pic = $this->tb_pic_path . $namelist . ".png";
        } else {
          // nein, keins da, also blank verwenden 
          $pic = $this->tb_pic_blank_pic;
        }
            }
        } else {
            if (!file_exists($pic)) {
        // nachsehen ob eventuell im alternativen Pfad das Bild ist?
        if (file_exists($this->tb_pic_alternate_path . $pic )) {
          // nachsehen ob eventuell im alternativen Pfad das Bild ist?
          $pic = $this->tb_pic_alternate_path . $pic ;
        
        } elseif (file_exists($this->tb_pic_alternate_path . $pic . ".png")) {
          // nachsehen ob eventuell im alternativen Pfad das Bild ist?
          $pic = $this->tb_pic_alternate_path . $pic . ".png";
        
        } elseif (file_exists($this->tb_pic_path . $pic )) {
          // nachsehen ob eventuell im orginal-Pfad das Bild ist?
          $pic = $this->tb_pic_path . $pic ;
        } elseif (file_exists($this->tb_pic_path . $pic . ".png")) {
          // nachsehen ob eventuell im orginal-Pfad das Bild ist?
          $pic = $this->tb_pic_path . $pic . ".png";        
        } else {
          // nein, keins da, also blank verwenden 
          $pic = $this->tb_pic_blank_pic;
          
        }
        
            } 
        }
        $alttext = strip_tags((!$alttext) ? $textconst : $alttext);
        $targeturl = $target;
        /* und nun endlich ins Array */
        $this->toolbar[] = array('name' => $namelist, 'text' => $textconst, 'picture' => $pic, 'list' => $listfunc, 'type' => $type, 'target' => $targeturl, 'alttext' => $alttext);
        $this->tb_flag = true;
        return;
    }
    /**
     * pmxAdminForm::clearToolbar()
     *
     * löscht das Toolbararray
     *
     * @return
     */
    public function clearToolbar()
    {
        // unset($this->toolbar);
        $this->toolbar = array();
        return;
    }
    /**
     * pmxAdminForm::getToolbar()
     *
     * gibt die Toolbar aus
     *
     * @return
     */
    public function getToolbar()
    {
        $this->toolbar2 = $this->toolbar;
        $output = "<div class=\"" . $this->csstoolbar . "\">";
    //es bibt nur links oder rechts, Standard=links
        if ($this->tb_direction != "right") $this->tb_direction = "left";
        if ($this->tb_direction != "left") $this->tb_direction = "right";
    if ($this->toolbarfixed) {
      $ww=($this->tb_pic_text)?intval(2.5 * $this->tb_pic_heigth):intval(2 * $this->tb_pic_heigth);
      pmxHeader::add_style_code("<style type=\"text/css\">/* <![CDATA[ */.".$this->csstoolbar." button{width:" . $ww . "px !important;height:" . $ww . "px !important;}/* ]]> */");
    }
        $output .= "<ul class=\"" . $this->csstoolbar . "\">";
        
    /* Ausgabe-Richtung ermitteln */
        if ($this->tb_direction == "right") {
            $this->toolbar2 = array_reverse($this->toolbar);
            if ($this->tb_text) $output .= "<li style=\"float:left;overflow:hidden;\" ><span>" . $this->tb_text . "</span></li>\n";
        }
        /* Button ausgeben */
        foreach ($this->toolbar2 as $value) {
      if ($value['type']=='space') {
        $output .= "<li style=\"float:" . $this->tb_direction . ";overflow:hidden;width:" . $this->tb_pic_heigth . "px;\" class=\"" . $this->csstoolbar . "\" >&nbsp;</li>\n";
                
      } else {
        $output .= "<li style=\"float:" . $this->tb_direction . ";overflow:hidden;\" class=\"" . $this->csstoolbar . "\" >" . $this->_Image($value['name'], $value['picture'], $value['text'], $value['alttext'], $value['list'], $value['type'], $value['target']) . "</li>\n";        
      }
        }
        if ($this->tb_direction == "left") {
            if ($this->tb_text) $output .= "<li style=\"float:right;overflow:hidden;\"><p>" . $this->tb_text . "</p></li>\n";
        }
        $output .= "</ul>\n<br/><input type='hidden' name='toolbarhide' value='1' /></div><br/>";
        unset ($this->toolbar2);
        return $output;
    }
    /**
     * pmxAdminForm::_Image()
     *
     * @param mixed $feldname
     * @param mixed $imgname
     * @param mixed $title
     * @param mixed $text
     * @param mixed $listerr
     * @param string $type
     * @param string $linkurl
     * @return
     */
    private function _Image($feldname, $imgname, $title, $text, $listerr = false, $type = 'button', $linkurl = '')
    {
        $tt = "";
        $img = $imgname;
        $buttontype = 'submit';
        $claas = '';
        if ($listerr) {
            $claas = ' frmchecklist';
        }
        if ($listerr) {
            $tt = "onclick=\"javascript:if(document[adminForm].boxchecked.value==0){alert('" . _NOACTION . "');} else {onsubmitform();}\"";
        } else {
            $tt = "onclick=\"javascript:hideMainMenu();\"";
        }
        if ($type == 'link' && ($linkurl)) {
            $linkurl = str_replace("&nbsp;", "&", $linkurl);
            $tt = " onclick=\"window.location.href='" . $linkurl . "';\"";
            $buttontype = 'button';
        }
        $tdtext = "";
        $tdtext .= "";
        $tdtext .= "<button class=\"" . $this->csstoolbar . $claas . " btn btn-success\" type='" . $buttontype . "' name='toolbarsubmit' value='" . $feldname . "' " . $tt . " title='" . $text . "' > ";
        if ($this->tb_pic_text == 1) $tdtext .= "<i class=\" fa fa-check\"></i> " . $title;
        $tdtext .= "</button>";
        return $tdtext;
    }
    /**
     * pmxAdminForm::CheckButton()
     *
     * Zentrale Rückgabefunktion aus dem Formular
     * gibt den Namen der ausgewählten Submit-Schaltfläche zurück
     *
     * @return
     */
    public static function CheckButton()
    {
        if (isset($_POST['toolbarhide']) and isset($_POST['toolbarsubmit'])) {
            return $_POST['toolbarsubmit'];
        } elseif (isset($_POST['submit'])) {
            return $_POST['submit'];
        } elseif (isset($_POST['formsubmit'])) {
            return $_POST['formsubmit'];
        } elseif (isset($_POST['hidemainmenu'])and isset($_POST['toolbarsubmit'])) {
            return $_POST['toolbarsubmit'];
        }
    }
    /**
     * pmxAdminForm::extract_class()
     * extrahiert eine angegebene css-Klassendefinition als String
     *
     * @param mixed $attrib_array
     * @return string
     */
    protected static function extract_class(&$attrib_array)
    {
        $class = '';
        if (is_array($attrib_array)) {
            if (isset($attrib_array['class'])) {
                $class = $attrib_array['class'];
                unset($attrib_array['class']);
                if (is_array($class)) {
                    $class = implode(' ', $class);
                }
                $class = ' ' . $class;
            }
            return $class;
        }
        if (is_string($attrib_array) && stripos($attrib_array, 'class') !== false) {
            if (preg_match('#class\s*=\s*([\'"])([^\1]+)\1#i', $attrib_array, $matches)) {
                $attrib_array = trim(str_replace($matches[0], '', $attrib_array));
                $class = ' ' . $matches[2];
            }
            return $class;
        }
    }
    /**
     * pmxAdminForm::get_attributes_from_array()
     *
     * Wandelt die als Array() übergebenen Parameter in HTML-Attribute um.
     * Bestimmte Attribute werden aber ausgefiltert, weil diese immer direkt
     * mit der add() Funktion angegeben und ausgewertet werden
     *
     * @param mixed $attrib_array
     * @return
     */
    protected static function get_attributes_from_array(&$attrib_array)
    {
        if (is_string($attrib_array)) {
            return ' ' . trim($attrib_array);
        }
        if (!is_array($attrib_array)) {
            // hmmmm... ?
            return $attrib_array;
        }
        $notallowed = array('name', 'value', 'type', 'id', 'title', 'size');
        $attributes = '';
        foreach ((array) $attrib_array as $key => $val) {
            $key = strtolower($key);
            switch (true) {
                case $val === null:
                case $val === false:
                case in_array($key, $notallowed):
                    unset($attrib_array[$key]);
                    continue 2;
                    break;
                case $key == 'class' && is_array($val):
                    $val = implode(' ', $val);
                    break;
                case $key == 'style' && is_array($val):
                    $val = implode(';', $val);
                    break;
            }
            $key = htmlspecialchars($key, ENT_COMPAT | ENT_HTML5, 'UTF-8', false);
            $val = htmlspecialchars($val, ENT_COMPAT | ENT_HTML5, 'UTF-8', false);
            $attributes .= " $key=\"$val\"";
        }
        return $attributes;
    }
  
  /**
   *  $function addTable
   *  
   *  gibt eine Listen Tabelle mit optionalen selector  zurück
   *  
   *  param 
   *  $header array : [name] alle Spaltenköpfe als Strings
   *            [class] css-Klasse
   *            [attr]  zusätzliche HTML-Attribute für die Header-SPalte
              [col_attr]  attribute für die Datenspalten
              
   *  $values multi array   : alle Listeneiträge mit alles Spalten   
   *            der index muss den Wert enthalten, der bei der check-Funktion übergeben werden soll.
   *  
   *  $attributes     : allgemeinen Einstellungen für die Tabelle
   *            u.a.
   *            [tableclass]  CSS-der Tabelle
   *            [checklistflag] wenn true dann wird eine Checkselektor-Spalte hinzugefügt
   *  
   *  
   */
  static function ListTable($header=array(),$values=array(),$attributes=array()) 
  {
    $table="";
    $alternate=true;
        $class = self::extract_class($attributes);
    $checklistflag = false;
    $adminform=false;
    $tableclass="";
    $colclass="listcol";
    $preselectall=false;
    $checkselector = self::$checkselector;
    
    if (array_key_exists('checklistflag',$attributes)){
      $checklistflag = $attributes['checklistflag'];
      unset($attributes['checklistflag']);
    }
    if (array_key_exists('adminform',$attributes)){
      $adminform = $attributes['adminform'];
      unset($attributes['adminform']);
    }   
    if (array_key_exists('checkselector',$attributes)){
      $checkselector = $attributes['checkselector'];
      unset($attributes['checkselector']);
    }
    if (array_key_exists('alternate',$attributes)){
      $alternate = $attributes['alternate'];
      unset($attributes['alternate']);
    }
    if (array_key_exists('preselectall',$attributes)){
      $preselectall = $attributes['preselectall'];
      unset($attributes['preselectall']);
      $checklistflag=true;
    }
    if (array_key_exists('tableclass',$attributes)){
      $tableclass = $attributes['tableclass'];
      unset($attributes['tableclass']);
    }   
    if (array_key_exists('colclass',$attributes)){
      $colclass = $attributes['colclass'];
      unset($attributes['colclass']);
      $colclass=(trim($colclass)=="")?"listcol":$colclass;
    }   
    $cHeader=(is_array($header) AND !empty($header))?count($header):0;
    if ($cHeader==0)return "";  /* wenn kein Header da, dann keine Ausgabe */ 
    $colCount=($checklistflag)?$cHeader+1:$cHeader;
    $rowCount=(!empty($values))?count($values):0;
    $selected=($preselectall)?"checked='checked'":""; 
    $checkflag=$checklistflag && $adminform;
    
        $fextern = self::get_attributes_from_array($attributes);
    $table.="<table class='list listtable $tableclass' ".$fextern."><thead><tr>";
    
    $table.=($checkflag)?'<th class="tablecheckbox"><input type="checkbox" name="toggle" value="" onclick="checkAll('.$rowCount.');" '.$selected.'/></th> ':(($checklistflag)?"<th>&nbsp;</th>":"");  
    foreach ($header as $col)
    {
      $col= array_merge(array("name"=>"","attr"=>"","class"=>""),$col);
      $table .="<th class='".$col['class']."' ".self::get_attributes_from_array($col['attr']) .">".$col['name']."</th>";
    }
    $table.="</tr></head><tbody>";
    
    if ($checkflag && $preselectall) {
      pmxHeader::add_script_code('
      window.onload = function () {
        checkAll('.$rowCount.');
      }
      ');
    }
    $i=0;
    $altC=0;
    $inlineJS=($checkflag)?' onclick="isChecked(this.checked);"':'';
    if ($rowCount > 0) {
      foreach ($values as $key => $value){
        $altC++;
        $altColor=($alternate)?((intval($altC/2)===$altC/2)?"alt0":"alt1"):"";
        $table .="<tr>";
        
        $table .=($checklistflag)?'<td  class="tablecheckbox '.$altColor.' '.$colclass.'_0"><input type="checkbox" id="cb'.$i.'" name="'.$checkselector.'[]" value="'.$key. '"'. $inlineJS. ' '.$selected.'/></td> ':'';
        $i++;
        $l=0;
        foreach($value as $v) {
          $l++;
          $table.="<td class=\"$altColor {$colclass}_{$l}\">".$v."</td>";
        }
        $table .="</tr>";
      }
    }
    $table.="</tbody></table>";
        
    return "<div class=\"block\">".$table."</div>";
    
  }
}
?>