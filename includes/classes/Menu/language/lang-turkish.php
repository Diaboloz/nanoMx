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

define("_MX_MENUS", "Menü");
define("_MX_MENU_ADMIN", "Menülerinizi Yönetin");
define("_MX_MENU_INPUTREQUIRED_NOTE", "Gereken bilgileri gösterir");
define("_MX_MENU_INPUTREQUIRED", "<i>%s</i> gerekli");
define("_MX_MENU_PATH", "URL");
define("_MX_MENU_EDIT", "Düzenle");
define("_MX_MENU_DELETE", "Sil");
define("_MX_MENU_DELETE_AREYOUSURE", "'%s' menüsünün silinmesini istediğinize eminmisiniz? [ <a href='%s'>Evet</a> | <a href='" . adminUrl('menu') . "'>Hayır</a> ]");
define("_MX_MENU_DELETED", "'%s' menüsü silindi.");
define("_MX_MENU", "Menü");
define("_MX_MENU_ITEM", "Menü öğesi");
define("_MX_MENU_OPERATIONS", "İşlemler");
define("_MX_MENU_SHOWALL", "Tümünü göster");
define("_MX_MENU_SHOWALL_NO_MENUS", "Menü belirlenmemiş");
define("_MX_MENU_ADDMENU", "Menü ekle");
define("_MX_MENU_ADDMENU_INTRO", "Yeni menü için isim girin. Oluşturduktan sonra blok yönetiminden etkinleştirmeniz gereklidir.");
define("_MX_MENU_ADDMENU_EDIT", "Menü düzenle");
define("_MX_MENU_ADDMENU_NAME_DESCR", "Menünün ismi.");
define("_MX_MENU_ADDMENU_ADDED", "'%s' menüsü oluşturuldu.");
define("_MX_MENU_ADDMENU_UPDATED", "'%s' menüsü güncellendi.");
define("_MX_MENU_ADDMENU_EXISTEDALREADY", "'%s' menüsü zaten mevcut.");
define("_MX_MENU_ADDMENU_BLOCKEDIT", "<a href=\"%s\">Menü bloğunu yönet</a>");
define("_MX_MENU_ADDITEM", "Menü öğesi ekle");
define("_MX_MENU_ADDITEM_EDIT", "Menü öğesi düzenle");
define("_MX_MENU_ADDITEM_NOTDEF", "Menü öğeleri belirlenmemiş.");
define("_MX_MENU_ADDITEM_NAME_DESCR", "Menü öğesinin ismi.");
define("_MX_MENU_ADDITEM_TITLE_DESCR", "Fare ile bir menü öğesi üzerinden gidildiği takdirde gösterilen açıklama.");
define("_MX_MENU_ADDITEM_PATH_DESCR", "Bu menü öğesinin izlediği adrestir. Bu bir pragmaMx adresi veya site dışında, örneğin http://www.pragmamx.org gibi bir bağlantı olabilir.");
define("_MX_MENU_ADDITEM_ASSOCIATEDMENU", "Ana menü");
define("_MX_MENU_ADDITEM_WEIGHT", "Sıralama");
define("_MX_MENU_ADDITEM_ADDED", "'%s' menü öğesi eklendi.");
define("_MX_MENU_ADDITEM_DELETE_AREYOUSURE_1", "'%s' menüsünü silmek istediğinizden emin misiniz? [ <a href='%s'>Evet</a> | <a href='" . adminUrl('menu') . "'>Hayır</a> ]");
define("_MX_MENU_ADDITEM_DELETE_AREYOUSURE_2", "'%s' menüsünü silmek istediğinizden emin misiniz? [ <a href='%s'>Evet</a> | <a href='" . adminUrl('menu') . "'>Hayır</a> ]<br /><b>Not</b>: Mevcut alt öğeleri üstteki menü seviyesine yerleştirilir.");
define("_MX_MENU_ADDITEM_DELETED", "'%s' menü öğesi silindi.");
define("_MX_MENU_ADDITEM_UPDATED", "'%s' menü öğesi güncellendi.");
define("_MX_MENU_MODULE_IMPORT", "Ekle");
define("_MX_MENU_ITEM_EXP_OPEN", "açık");
define("_MX_MENU_ITEM_EXP_DESCR", "Eğer bu menü öğesinin alt öğeleri varsa bunlar sürekli açık olarak gösterilecektir.");
define("_MX_MENU_ITEM_ISDISABLED", "(pasifleştirilmiş)");
define("_MX_MENU_MODULE_ADMIN", "Modülleri yönet");
define("_MX_MENU_MODULE_NAME", "İsim");
define("_MX_MENU_MODULE_TITLE", "Başlık");
define("_MX_MENU_MODULE_BLOCK", "Blok dosyası");
define("_MX_MENU_MODULE_OUTYET", "Modül linkleri");
define("_MX_MENU_ITEM_FORBIDDEN", "%s gizli modül linkleri");
define("_MX_MENU_MENUOPTIONS", "Menü Ayarları");
define("_MX_MENU_SETTINGS_UPDATED", "Menü ayarları güncellendi.");
define("_MX_MENU_SETTINGS_DYN_EXP", "Menü öğelerinin dinamik şekilde açılıp ve kapatılmasına imkan tanı");
define("_MX_MENU_SETTINGS_DYN_EXP_DESCR", "Eğer bu seçenek aktifleştirilmişse, gizle/göster sembolüne tıklayarak kullanıcı mevcut olan alt menü öğelerini açabilir.");
define("_MX_MENU_ENABLED", "'%s' menüsü aktifleştirildi.");
define("_MX_MENU_DISABLED", "'%s' menüsü pasifleştirildi.");
define("_MX_MENU_MODULLINK", "Modül linki");
define("_MX_MENU_FURTHERSETTINGS", "ve diğer ayarlar");
define("_MX_MENU_TARGET", "Pencere");
define("_MX_MENU_TARGET2", "Hedef pencere");
define("_MX_MENU_ADDITEM_TARGET_DESCR", "Bağlantının açılacağı pencerenin adı. Aynı pencerede açmak için boş bırak.");
define("_MX_MENU_POS_BEFORE", "önce");
define("_MX_MENU_POS_BEGIN", "başına");
define("_MX_MENU_POS_LAST", "sonuna");
define("_MX_MENU_SAVEERROR", "Menü öğesi '%s' kaydedilemedi.");
define("_MX_MENU_NOTACTIVE", "Pasif");
define("_MX_MENU_BLOCKSADMIN", "Blok Yönetimi");

?>