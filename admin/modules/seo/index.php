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
 * $Revision: 158 $
 * $Author: PragmaMx $
 * $Date: 2016-05-14 19:07:02 +0200 (Sa, 14. Mai 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * pmxSeo_admin
 *
 * @package pragmaMx core
 * @author tora60
 * @copyright Copyright (c) 2012
 * @version $Id: index.php 158 2016-05-14 17:07:02Z PragmaMx $
 * @access public
 */
class pmxSeo_admin {
    private $errors = array();
    private $form = null;

    private $config = null;

    /**
     * pmxSeo_admin::__construct()
     */
    public function __construct()
    {
        if (!mxGetAdminPref('radminsuper')) {
            return mxRedirect(adminUrl(), _ACCESSDENIED);
        }

        mxGetLangfile(__DIR__);

        load_class('Modrewrite', false);

        $this->config = load_class('Config', 'pmx.seo');
        if (!$this->config->get()) {
            $def = $this->config->get_defaults();
            $this->config->set($def);
        }

        $this->form = load_class('AdminForm', PMX_MODULE);

        $returnflag = false;
        switch ($this->form->CheckButton()) {
            case 'accept':
                $returnflag = true;
            case 'save':
                return $this->settings_save($returnflag);
            default:
                return $this->settings();
        }
    }

    /**
     * pmxSeo_admin::settings()
     *
     * @param array $data
     * @return
     */
    function settings($data = array())
    {
        if ($data) {
            $data = array_merge($this->config->get(), $data);
        } else {
            $data = $this->config->get();
        }

        $this->_arrays2strings($data);

        $rewrite_warning = false;
        $can_rewrite = pmxModrewrite::can_rewrite($rewrite_warning);
        $can_extend = ($can_rewrite) ? pmxModrewrite::can_extend() : false;

        /* Form Einstellungen */
        $this->form->tb_pic_heigth = 25;
        //$this->form->infobutton = true;
        $this->form->acceptbutton = false;
		$this->form->fieldhomebutton=false;
        $this->form->title = _SEO;
		$this->form->cssform = "a304030";
        /* Toolbar zusammenstellen */
        $this->form->addToolbar('accept');
        $this->form->addToolbar('save');
        $this->form->addToolbarLink('cancel', adminUrl(PMX_MODULE));
        // $this->form->addToolbarLink('cpanel', adminUrl(PMX_MODULE));
        /* allgemeine Elemente */
        $this->form->addFieldSet("global", _SEOGLOBALSET, null, true);
        $this->form->add("global", "textbox", "metakeywords", $data['metakeywords'], _KEYWORD_TXT, _CUTWITHCOMMATA, 50, false);

        /* Mod-Rewrite Elemente */
        $this->form->addFieldSet("modrewrite", _MODREWRITE, null, true, array('class' => 'g-50-30-20'));
        if ($can_rewrite) {
            $this->form->add("modrewrite", "checkbox", "modrewrite[anony]", $data['modrewrite']['anony'], _PROMODREWANON);
            $this->form->add("modrewrite", "checkbox", "modrewrite[users]", $data['modrewrite']['users'], _PROMODREWUSERS);
            $this->form->add("modrewrite", "checkbox", "modrewrite[admin]", $data['modrewrite']['admin'], _PROMODREWADMIN);
        } else {
            $this->form->add("modrewrite", "output", '<div class="important">' . _PROMODREWERROR . '<br />' . $rewrite_warning . '</div>');
        }
        if ($can_extend) {
            $this->form->add("modrewrite", "yesno", "modrewriteextend", $data['modrewriteextend'], _USEMODREWRITEEXTEND);
        }

        /* Sitemap Elemente */
        $this->form->addFieldSet("sitemap", _SEOSITEMAP, null, true);
        $this->form->add("sitemap", "yesno", "sitemap", $data['sitemap'], _SEOSITEMAPACTIVE);
        $this->form->add("sitemap", "input", "sitemapcache", $data['sitemapcache'], _SEOSITEMAPCACHE, _SEOSITEMAPCACHEDESC, 3);
        $this->form->add("sitemap", "input", "sitemaplimit", $data['sitemaplimit'], _SEOSITEMAPLIMIT, _SEOSITEMAPLIMITDESC, 3);
        $this->form->add("sitemap", "textbox", "sitemapkeywords", $data['sitemapkeywords'], _SEOSITEMAPKEYS, _SEOSITEMAPKEYSDESC, 50, false);
        $this->form->add("sitemap", "textbox", "sitemapexmod", $data['sitemapexmod'], _SEOSITEMAPEXMOD, _SEOSITEMAPEXMODDESC, 50, false);

        /* Form abrufen */
        $form = $this->form->Show();

        /* Form ausgeben */
        include_once('header.php');
        echo $form;
        include_once('footer.php');
    }

    /**
     * pmxSeo_admin::settings_save()
     *
     * @param mixed $returnflag
     * @return
     */
    function settings_save($returnflag = false)
    {
        $defaults = $this->config->get();
        /* Standardwert auf false setzen */
        $defaults['modrewrite'] = array_fill_keys(array_keys($defaults['modrewrite']), false);

        $data = array_merge($defaults, $_POST);
        $data = array_intersect_key($data, $defaults);

        $can_rewrite = pmxModrewrite::can_rewrite($rewrite_warning);
        if (!$can_rewrite) {
            $data['modrewrite'] = $defaults['modrewrite'];
        }
        $can_extend = ($can_rewrite) ? pmxModrewrite::can_extend() : false;
        if (!$can_extend) {
            $data['modrewriteextend'] = false;
        }

        $this->_strings2arrays($data);

        $this->config->set($data, true);

        if ($returnflag) {
            return $this->settings();
        } else {
            return mxRedirect(adminUrl(PMX_MODULE), _CHANGESAREOK);
        }
    }

    /**
     * pmxSeo_admin::_arrays2strings()
     *
     * @param mixed $data
     * @return
     */
    private function _arrays2strings(&$data)
    {
        if (is_array($data['metakeywords'])) {
            $data['metakeywords'] = implode(', ', $data['metakeywords']);
        }
        if (is_array($data['sitemapkeywords'])) {
            $data['sitemapkeywords'] = implode(', ', $data['sitemapkeywords']);
        }
        if (is_array($data['sitemapexmod'])) {
            $data['sitemapexmod'] = implode(', ', $data['sitemapexmod']);
        }
    }

    /**
     * pmxSeo_admin::_strings2arrays()
     *
     * @param mixed $data
     * @return
     */
    private function _strings2arrays(&$data)
    {
        if (!is_array($data['metakeywords'])) {
            $data['metakeywords'] = preg_split('#\s*,\s*#', $data['metakeywords']);
        }
        if (!is_array($data['sitemapkeywords'])) {
            $data['sitemapkeywords'] = preg_split('#\s*,\s*#', $data['sitemapkeywords']);
        }
        if (!is_array($data['sitemapexmod'])) {
            $data['sitemapexmod'] = preg_split('#\s*,\s*#', $data['sitemapexmod']);
        }
    }
}

$tmp = new pmxSeo_admin();
$tmp = null;

?>