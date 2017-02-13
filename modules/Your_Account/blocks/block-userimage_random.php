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

global $prefix, $user_prefix;

$pici = load_class('Userpic', array()); // leeres Array übergeben, es wird nur die config gebraucht...

if (MX_IS_USER) {
    $userdata = mxGetUserData();
    $andnot = ' AND uid<>' . $userdata['uid'];
} else {
    $andnot = '';
}

$qry = "SELECT uid
        FROM `{$user_prefix}_users`
        WHERE NOT ISNULL(user_avatar) AND user_avatar like '" . mxAddSlashesForSQL($pici->path_upload) . "%'" . $andnot . "
        ORDER BY rand()
        LIMIT 1";
$result = sql_query($qry);
list($uid) = sql_fetch_row($result);
if ($uid) {
    $userdata = mxGetUserdataFromUid($uid);
    $pici = load_class('Userpic', $userdata);
    if ($pici->is_uploaded()) {
        $tplvars = array('photo' => $pici->getHtml('normal', array('shrink-width' => '150', 'class' => 'align-center')),
            'profilelink' => mxCreateUserprofileLink($userdata['uname']),
            );

        /* Templateausgabe erstellen */
        $tpl = load_class('Template');
        $tpl->init_path(__FILE__);
        $tpl->init_template(__FILE__);
        $tpl->assign($tplvars);
        $content = $tpl->fetch();
    }
}
$pici = null;

?>