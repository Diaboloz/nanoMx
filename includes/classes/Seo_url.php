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

 class pmxSeo_url {
    /**
     * durch was sollen bei automatischer Ersetzung, die erwuenschten
     * Bindestriche in der URL ersetzt werden?
     */
    public static $alternate = '~';

    /* Trennzeichen zwischen Modulnamen und restlichem Querystring */
    private static $_separator = '/';

    private static $_maxlen_url = 255;

    private static $_maxlen_suhosin = null;

    private static $_supported = false;


     /**
     * pmxpmxSeo_url::__construct()
     *
     * @param mixed $params
     */
    protected function __construct($params = null)
    {
    }
    /**
     * pmxpmxSeo_url::check_htaccess()
     * .htaccess auf Gueltigkeit pruefen
     *
     * @return
     */
    public static function check_htaccess()
    {
        /* PMXMODREWRITE wird in der index.php definiert */
        if (is_file(PMX_REAL_BASE_DIR . DS . '.htaccess') || defined('PMXMODREWRITE')) {
            // TODO: Inhalt pruefen
            return true;
        }
        return false;
    }
 }
 ?>