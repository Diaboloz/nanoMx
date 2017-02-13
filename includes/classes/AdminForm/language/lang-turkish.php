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
 * $Revision: 214 $
 * $Author: PragmaMx $
 * $Date: 2016-09-15 15:51:34 +0200 (Do, 15. Sep 2016) $
 *
 * @package pragmaMx
 */

defined('mxMainFileLoaded') or die('access denied');

$toolbarlangarray=array(
    '_NOACTION' => 'Lütfen önce bir seçim yapın !',
    '_EXPANDALL' => 'hepsini aç',
    '_COLLAPSEALL' => 'kapat',
    '_ADD' => 'Ekleyin',
    '_ACCEPT' => 'Uygulayın',
    '_BACK' => 'geri',
    '_CANCEL' => 'İptal',
    '_CATEGORYS' => 'Kategoriler',
    '_COLOR' => 'Renkler',
    '_COMMENTS' => 'Yorum',
    '_CONFIG' => 'Ayarlar',
    '_CONTENT' => 'İçerik',
    '_COPY' => 'kopyala',
    '_CPANEL' => 'Yönetici Menüsü',
    '_DELETE' => 'Sil',
    '_DOWN' => 'aşağıda',
    '_DOWNLOAD' => 'İndir',
    '_EDIT' => 'Değiştir',
    '_FOLDER' => 'Dosya',
    '_HELP' => 'Yardım',
    '_HOME' => 'Ana sayfa',
    '_IMAGE' => 'Resimler',
    '_LINK' => 'Bağlantı',
    '_MAIL' => 'Eposta',
    '_MOVE' => 'Taşıyın',
    '_NEW' => 'Yeni',
    '_NEWS' => 'Haber',
    '_NEXT' => 'devam',
    '_PLUS' => 'Ekle',
    '_PREVIEW' => 'Önizleme',
    '_PUBLISH' => 'yayınla',
    '_REDIRECT' => 'İletin',
    '_REFRESH' => 'Güncelleyin',
    '_SAVE' => 'Kaydedin',
    '_SETTINGS' => 'Ayarlar',
    '_TOOLS' => 'Seçenekler',
    '_TRASH' => 'Çöp kutusu',
    '_UNPUBLISH' => 'Kilitle',
    '_UP' => 'yukarı',
    '_UPLOAD' => 'Yükle',
    '_USER' => 'Kullanıcı',
    '_VOTE' => 'Oylama',
    '_ZOOM' => 'Zoom',
    '_SELECTTIME' => 'zaman seçin',
    '_DEFAULT' => 'standart',
	'_HTML_EDIT' => 'Edit HTML',
	'_CSS_EDIT' => 'Edit CSS', 
	'_WRITABLE' => 'writable',
	'_NOWRITABLE' =>'not writable',
	'_ARCHIVE'=>'Archiv',
	'_EXPORT'=>"Export",
	'_IMPORT'=>"Import",	
    );

foreach ($toolbarlangarray as $constant => $value) {
    defined($constant) OR define($constant,$value);
}

?>