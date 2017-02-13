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
 * $Revision: 194 $
 * $Author: PragmaMx $
 * $Date: 2016-07-25 15:10:19 +0200 (Mo, 25. Jul 2016) $
 *
 * turkish language file, translated by:
 * Sıtkı Özkurt from www.akcaabat-acisu.com
 */

defined('mxMainFileLoaded') or die('access denied');

/* Datestrings und locale Einstellungen: */
define("_CHARSET", "utf-8"); // Test:  äöüß
define("_LOCALE", "tr_TR");
$old_setlocale = setlocale(LC_TIME, 0);
$locale = array('tr_TR.UTF-8', 'tr_TR.UTF8', 'tr_TR', 'tr', 'trk', 'turkish', 'TR', 'TUR', '792', 'CTRY_TURKEY', 'tr_TR.ISO-8859-9');
define('_SETLOCALE', setlocale(LC_TIME, $locale));
setlocale(LC_TIME, $old_setlocale);
define('_SETTIMEZONE', 'Europe/Istanbul');
define('_DECIMAL_SEPARATOR', ',');
define('_THOUSANDS_SEPARATOR', '.');
define('_SPECIALCHARS', 'âÇçĞğİıîÖöŞşÜü');

$rewriteentities = array(/* ASCII-Wert => Ersetzung */
//    226 => 'a', // â  Kleines a mit Zirkumflex
//    199 => 'C', // Ç  Grosses C mit Cedilla
//    231 => 'c', // ç  Kleines c mit Cedilla
//    286 => 'G', // G  Grosses G mit Breve
//    287 => 'g', // g  Kleines g mit Breve
//    304 => 'I', // I  Grosses I mit Punkt obendrauf
//    305 => 'i', // i  Kleines i ohne Punkt
//    238 => 'i', // î  Kleines i mit Zirkumflex
    214 => 'O', // Ö  Grosses O Umlaut (Diaeresis)
    246 => 'o', // ö  Kleines o Umlaut (Diaeresis)
//    350 => 'S', // S  Grosses S mit Cedilla
//    351 => 's', // s  Kleines s mit Cedilla
    220 => 'U', // Ü  Grosses U Umlaut (Diaeresis)
    252 => 'u', // ü  Kleines u Umlaut (Diaeresis)
    );
define('_REWRITEENTITIES', serialize($rewriteentities));
define("_SPECIALCHARS_ONLY", false); // Schrift besteht nur aus Nicht-ASCII Zeichen
define("_DOC_LANGUAGE", "tr");
define("_DOC_DIRECTION", "ltr");
define("_DATESTRING", "%A, %d. %B %Y");
define("_DATESTRING2", "%A, %d. %B");
define("_XDATESTRING", "%d.%m.%Y. %H:%M");
define("_SHORTDATESTRING", "%d.%m.%Y");
define("_XDATESTRING2", "%A, %B %d");
define("_DATEPICKER", _SHORTDATESTRING);
define("_TIMEFORMAT", "%H:%Mh");
define("_DATETIME_FORMAT","%d.%m.%Y %H:%M");
define("_SYS_INTERNATIONALDATES", 1); //0 = mm/dd/yyyy, 1 = dd/mm/yyyy
define("_SYS_TIME24HOUR", 1); // 1 = 24 hour time... 0 = AM/PM time
define("_SYS_WEEKBEGINN", 1); # the First Day in the Week: 0 = Sunday, 1 = Monday
define("_Z1", "Bu sitedeki tüm logo ve markalar sahiplerinin malıdır. Diğer detayları <a href=\"modules.php?name=Impressum\">Künye</a> bölümünde bulabilirsiniz .");
define("_Z2", "Yorumlar yazarların sorumluluğu altındadır,<br />geri kalan her şey © by <a href=\"" . PMX_HOME_URL . "\">" . $GLOBALS['sitename'] . "</a>");
define("_Z3", "Bu site pragmaMx " . PMX_VERSION . " tabanlıdır.");
define("_Z4", "Haberlerimizi <a href=\"modules.php?name=rss\">RSS</a> kullanarak yayınlayabilirsiniz.");
define("_YES", "Evet");
define("_NO", "Hayır");
define("_EMAIL", "E-posta");
define("_SEND", "Gönder");
define("_SEARCH", "Ara");
define("_LOGIN", "Giriş");
define("_WRITES", "yazdı");
define("_POSTEDON", "Tarih:");
define("_NICKNAME", "Kullanıcı adı");
define("_PASSWORD", "Şifre");
define("_WELCOMETO", "Hoşgeldiniz:");
define("_EDIT", "Değiştir");
define("_DELETE", "Sil");
define("_POSTEDBY", "Gönderen");
define("_GOBACK", "[&nbsp;<a href=\"javascript:history.go(-1)\">Geri Dön</a>&nbsp;]");
define("_COMMENTS", "yorum");
define("_BY", "Gönderen");
define("_ON", "Tarih:");
define("_LOGOUT", "Çıkış");
define("_HREADMORE", "devamı...");
define("_YOUAREANON", "Kayıtlı değilsiniz. <a href=\"modules.php?name=Your_Account&op=new_user\">buraya</a> tıklayarak ücretsiz kayıt olabilirsiniz.");
define("_NOTE", "Not:");
define("_ADMIN", "Yönetici:");
define("_TOPIC", "Konu");
define("_MVIEWADMIN", "Görünüm: Sadece yöneticiler");
define("_MVIEWUSERS", "Görünüm: Sadece kayıtlı kullanıcılar");
define("_MVIEWANON", "Görünüm: Sadece anonim kullanıcılar");
define("_MVIEWALL", "Görünüm: Tüm tiyaretçiler");
define("_EXPIRELESSHOUR", "İmha: 1 saat içinde");
define("_EXPIREIN", "İmha:");
define("_UNLIMITED", "Limitsiz");
define("_HOURS", "Saat");
define("_RSSPROBLEM", "Şu an bu sitenin başlıklarında problem var");
define("_SELECTLANGUAGE", "Dil seçin");
define("_SELECTGUILANG", "Arabirim dilini seçin:");
define("_BLOCKPROBLEM", "Şu an bu blokta bir sorun var.");
define("_BLOCKPROBLEM2", "Şu an bu bloğun içeriği yok.");
define("_MODULENOTACTIVE", "Üzgünüz, bu modül aktif değil!");
define("_NOACTIVEMODULES", "Pasif modüller");
define("_NOVIEWEDMODULES", "Saklı modüller");
define("_FORADMINTESTS", "(Yönetici testleri için)");
define("_ACCESSDENIED", "Erişim engellendi");
define("_RESTRICTEDAREA", "Kısıtlı bir alana ulaşmaya çalışıyorsunuz.");
define("_MODULEUSERS", "Üzgünüz, sitemizin bu bölümü <i>sadece kayıtlı kullanıcılar</i> içindir.<br /><br /><a href=\"modules.php?name=Your_Account&op=new_user\">Buraya</a> tıklayarak ücretsiz kayıt olabilir, daha sonra<br />bu bölüme kısıtlamalarla karşılaşmadan erişebilirsiniz. Teşekkürler.");
define("_MODULESADMINS", "Üzgünüz, sitemizin bu bölümü <i>sadece yöneticiler</i> içindir.");
define("_HOME", "Ana sayfa");
define("_HOMEPROBLEM", "Önemli bir sorunumuz var: Ana sayfa yok!!!");
define("_ADDAHOME", "Ana sayfaya bir modül ekle");
define("_HOMEPROBLEMUSER", "Şu an ana sayfada bir sorun var. Lütfen daha sonra tekrar deneyin.");
define("_DATE", "Tarih");
define("_HOUR", "Saat");
define("_UMONTH", "Ay");
define("_YEAR", "Yıl");
define("_YEARS", "Yıl");
define("_JANUARY", "Ocak");
define("_FEBRUARY", "Şubat");
define("_MARCH", "Mart");
define("_APRIL", "Nisan");
define("_MAY", "May");
define("_JUNE", "Haziran");
define("_JULY", "Temmuz");
define("_AUGUST", "Ağustos");
define("_SEPTEMBER", "Eylül");
define("_OCTOBER", "Ekim");
define("_NOVEMBER", "Kasım");
define("_DECEMBER", "Aralık");
define("_WEEKFIRSTDAY", "Pazar");
define("_WEEKSECONDDAY", "Pazartesi");
define("_WEEKTHIRDDAY", "Salı");
define("_WEEKFOURTHDAY", "Çarşamba");
define("_WEEKFIFTHDAY", "Perşembe");
define("_WEEKSIXTHDAY", "Cuma");
define("_WEEKSEVENTHDAY", "Cumartesi");
define("_MAIN", "Başlangıç");
define("_TERMS", "İsimler");
define("_TOP", "başa dön");
define("_SITECHANGE", "yukarı numaraya:");
define("_BANNED", "Üzgünüz bir süre için siteden uzaklaştırıldınız!<br /><br />Nedenini öğrenmek için yetkili kişilerle iletişime geçin");
define("_VKPBENCH1", "Bu sayfa ");
define("_VKPBENCH2", " saniyede, ");
define("_VKPBENCH3", " veritabanı sorgusuyla üretilmiştir");
define("_ERRNOTOPIC", "Bir konu seçmelisiniz.");
define("_ERRNOTITLE", "Makale için bir başlık belirtmeniz lazım.");
define("_ERRNOTEXT", "Bir metin yazmalısınız.");
define("_ERRNOSAVED", "Üzgünüz, veriler kaydedilemedi.");
define("_RETURNACCOUNT", "Bilgilerim Sayfasına Geri Dön");
define("_FORADMINGROUPS", "(gruplar göremez)");
define("_GROUPRESTRICTEDAREA", "Üzgünüz, bu bölümüne erişim yetkiniz yok.");
define("_NOGROUPMODULES", "Grupsuz modüller");
define("_AB_LOGOUT", "Çıkış");
define("_AB_SETTINGS", "Ayarlar");
define("_AB_MESSAGE", "Yönetici mesajları");
define("_AB_TITLEBAR", "Yönetici menüsü");
define("_AB_NOWAITINGCONT", "Yeni birşey yok");
define("_AB_RESETBCACHE", "Blok önbelleğini boşalt");
define("_ERR_YOUBAD", "Geçersiz operasyon yapmayı denediniz!");
define("_REMEMBERLOGIN", "Girişi hatırla");
define("_ADMINMENUEBL", "Yönetim");
define("_MXSITEBASEDON", "Bu web sitesi buraya dayalı");
define("_WEBMAIL", "E-Posta gönder");
define("_CONTRIBUTEDBY", "Hazırlayan");
define("_BBFORUMS", "Forumlar");
define("_BLK_MINIMIZE", "simge durumuna küçült");
define("_BLK_MAXIMIZE", "komple göster");
define("_BLK_HIDE", "gizle");
define("_BLK_MESSAGE", "Mesaj");
define("_BLK_MYBLOCKS", "Blok ayarları");
define("_BLK_EDITADMIN", "değiştir (Yönetici)");
define("_BLK_OPTIONS", "Blok seçenekleri");
define("_BLK_OPTIONSCLICK", "Blok seçeneklerini ayarlamak için tıklayın.");
define("_ADM_MESS_DATEEXPIRE", "Tarih");
define("_ADM_MESS_TIMES", "Zaman");
define("_ADM_MESS_DATESTART", "Başlangıç tarihi");
define("_ADM_MESS_TODAY", "Bugün");
define("_DEFAULTGROUP", "Varsayılan grup");
define("_YOURELOGGEDIN", 'Giriş yaptığınız için teşekkürler.');
define("_YOUARELOGGEDOUT", "Şimdi çıkış yaptınız.");
define('_CHANGESAREOK', 'Değişiklikler kaydedildi.');
define('_CHANGESNOTOK', 'Değişiklikler kaydedilemedi.');
define('_DELETEAREOK', 'Bilgiler silindi.');
define('_DELETENOTOK', 'Bilgiler silinemedi.');
define("_RETYPEPASSWD", "Şifreyi tekrarla");
define('_USERNAMENOTALLOWED', '&quot;%s&quot; kullanıcı adını kullanamazsınız.'); // %s = sprintf()
define('_SYSINFOMODULES', 'Yüklenmiş modüller ile ilgili bilgi');
define('_SYSINFOTHEMES', 'Yüklenmiş dizaynlar ile ilgili bilgi');
define("_ACCOUNT", "Kullanıcı hesabınız");
define('_MAXIMALCHAR', 'en fazla');
define("_SELECTPART", "Seçenek");
define("_CAPTCHAWRONG", "Yanlış Captcha sonucu");
define("_CAPTCHARELOAD", "Kontrol resmini güncelleştir");
define("_CAPTCHAINSERT", "Kontrol sonucu yukarıdaki resimden yükle:");
define("_ERROROCCURS", "Hatalar ortaya çıktı:");
define("_VISIT", "Ziyaret et");
define("_NEWMEMBERON", "Yeni kullanıcı kaydı");
define("_NEWMEMBERINFO", "Kullanıcı bilgisi");
define("_SUBMIT", "Gönder");
define("_GONEXT", "sonraki");
define("_GOPREV", "önceki");
define("_USERSADMINS", "Yöneticiler");
define("_USERSGROUPS", "Kullanıcı grupları");
define("_USERSMEMBERS", "Kayıtlı kullanıcılar");
define("_USERSOTHERS", "Diğerleri");
define("_FILES", "Dosyalar");
define("_ACCOUNTACTIVATIONLINK", "Kullanıcı hesabı aktivasyon linki");
define("_YSACCOUNT", "Bilgilerim");
define("_NEWSSHORT", "Haberler");
define("_RESETPMXCACHE", "Önbelleği boşalt");
define("_MSGDEBUGMODE", "Hata ayıklama modu aktifleştirildi!");
define("_ATTENTION", "Dikkat");
define("_SETUPWARNING1", "Lütfen kurulum klasörünü yeniden adlandırın veya silin!");
define("_SETUPWARNING2", "'setup/index.php' dosyasını yeniden adlandırmak için lütfen <a href='index.php?%s'>buraya tıklayın</a>");
define("_AB_EVENT", "Yeni etkinlik");
define("_EXPAND2COLLAPSE_TITLE", "Gizle veya Göster");
define("_EXPAND2COLLAPSE_TITLE_E", "Göster");
define("_EXPAND2COLLAPSE_TITLE_C", "Gizle");
define("_TEXTQUOTE", "Alıntı");
define('_BBBOLD', 'kalın');
define('_BBITALIC', 'İtalik');
define('_BBUNDERLINE', 'altı çizilmiş');
define('_BBXCODE', 'Kod');
define('_BBEMAIL', 'E-posta');
define('_BBQUOTE', 'Alıntı');
define('_BBURL', 'Bağlantı ');
define('_BBIMG', 'Resim');
define('_BBLIST', 'Liste');
define('_BBLINE', 'Ayırma çizgisi');
define('_BBNUMLIST', 'numaralı liste');
define('_BBCHARLIST', 'harf listesi');
define('_BBCENTER', 'ortala');
define('_BBXPHPCODE', 'PHP kodu');
define("_ALLOWEDHTML", "İzin verilen HTML:");
define("_EXTRANS", "HTML etiketleri metne");
define("_HTMLFORMATED", "HTML biçimli");
define("_PLAINTEXT", "Metin biçimli");
define("_OK", "Tamam!");
define("_SAVE", "Kaydet");
define("_PREVIEW", "Önizleme");
define("_FORMCANCEL", "Gönderme iptali");
define("_FORMRESET", "Temizle");
define("_FORMSUBMIT", "Gönder");
define("_NEWUSER", "Yeni kullanıcı");
define("_PRINTER", "Yazdırılabilir sayfa");
define("_FRIEND", "Haberi paylaş");
define("_YOURNAME", "İsminiz");
define("_HITS", "İzlenme");
define("_LANGUAGE", "Dil");
define("_SCORE", "Puan");
define("_NOSUBJECT", "Başlık yok");
define("_SUBJECT", "Konu");
define("_LANGDANISH", "Danimarkaca");
define("_LANGENGLISH", "İngilizce");
define("_LANGFRENCH", "Fransızca");
define("_LANGGERMAN", "Almanca");
define("_LANGSPANISH", "İspanyolca");
define("_LANGTURKISH", "Türkçe");
define("_LANGUAGES", "Mevcut diller");
define("_PREFEREDLANG", "Tercih edilen dil");
define("_LEGAL", "Kullanım koşulları");
// page
define("_PAGE", "Sayfa");
define("_PAGES", "Sayfa");
define("_OFPAGES", "/");
define("_PAGEOFPAGES", "Sayfa %d / %d");
define("_GOTOPAGEPREVIOUS", 'Önceki sayfa');
define("_GOTOPAGENEXT", 'Sonraki sayfa');
define("_GOTOPAGE", "Sayfa");
define("_GOTOPAGEFIRST", "ilk sayfa");
define("_GOTOPAGELAST", "son sayfa");
define("_BLK_NOYETCONTENT", "Henüz içerik yok");
define("_BLK_ADMINLINK", "Yönetici modülü");
define("_BLK_MODULENOTACTIVE", "Bu blok için modül '<i>%s</i>' aktif değil!");
define("_MODULEFILENOTFOUND", "Aradığınız Resim bulunamadı");
define("_DEBUG_DIE_1", "Sayfa işlenirken bir hata oluştu.");
define("_DEBUG_DIE_2", "Lütfen aşağıdaki hatayı site yöneticisine rapor edin.");
define("_DEBUG_INFO", "Hata temizleme bilgisi");
define("_DEBUG_QUERIES", "SQL Sorguları");
define("_DEBUG_REQUEST", "İstek");
define("_DEBUG_NOTICES", "Hatalar ve Uyarılar");
define("_COMMENTSNOTIFY", "\"%s\" de yeni yorum yazıldı."); // %s = sprintf $sitename
define("_REDIRECTMESS1", "Biraz bekleyin, %d saniye sonra yönlendirileceksiniz."); // %d = sprintf()
define("_REDIRECTMESS1A", "{Biraz bekleyin, }s{ saniye sonra yönlendirileceksiniz.}"); // {xx}s{xx} formated: http://eric.garside.name/docs.html?p=epiclock#ec-formatting-options
define("_REDIRECTMESS2", "Beklemek istemiyorsanız buraya tıklayın.");
define("_REDIRECTMESS3", "Lütfen bekleyin...");
define("_DEACTIVATE", "Pasifleştir");
define("_INACTIVE", "Pasif");
define("_ACTIVATE", "Etkinleştir");
define("_XMLERROROCCURED", "Satırda bir XML hatası oluştu");
// define("_ERRDEMOMODE", "Üzgünüz, demo modunda bu işlemi gerçekleştiremezsiniz.!");
define("_JSSHOULDBEACTIVE", "Bu özelliği kullanmak için JavaScript etkin olmalı.");
define("_CLICKFORFULLSIZE", "Tam boy görünüm için tıklayın...");
define("_REQUIRED", "(gerekli)");
define("_SAVECHANGES", "Değişiklikleri kaydet");
define("_MODULESSYSADMINS", "Maalesef bu bölüm bizim <i>Süper Yöneticiler</i> için ayrılmıştır");
define("_DATEREGISTERED", "Kayıt tarihi");
define("_RESET", "Sıfırlama");
define("_PAGEBREAK", "Birden fazla sayfa kullanmak için kesmek istediğiniz yere <strong class=\"nowrap\">" . htmlspecialchars(PMX_PAGE_DELIMITER) . "</strong> yazabilirsiniz.");
define("_READMORE", "Devamı...");
define("_AND", "ve");
define("_HELLO", "Merhaba");
define("_FUNCTIONS", "Fonksiyonlar");
define("_DAY", "Gün");
define("_TITLE", "Başlık");
define("_FROM", "Kimden");
define("_TO", "Kime");
define("_WEEK", "hafta");
define("_WEEKS", "hafta");
define("_MONTH", "ay");
define("_MONTHS", "ay");
define("_HELP", "Yardım");
define("_COPY", "Kopyalama");
define("_CLONE", "Çoğaltma");
define("_MOVE", "Kaydırma");
define("_DAYS", "gün");
define("_IN", "içinde:");
define("_DESCRIPTION", "Açıklama");
define("_HOMEPAGE", "Ana Sayfa");
define("_TOPICNAME", "Konunun adı");
define("_GOTOADMIN", "Yönetici bölümüne git");
define("_SCROLLTOTHETOP", "Sayfa başına kaydır");
define("_NOTIFYSUBJECT", "Bildirim");
define("_NOTIFYMESSAGE", "Merhaba, sitenize yeni gönderimler vardır.");
define("_NOTITLE", "başlıksız");
define("_ALL", "Tümü");
define("_NONE", "Yok");
define("_BROWSE", "Gez");
define("_FILESECURERISK1", "GÜVENLİK TEHLİKESİ:");
define("_FILESECURERISK2", "Silmeniz gereken dosya:");
define("_CANCEL", "İptal");
// Konstanten zur Passwortstärke
define("_PWD_STRENGTH", "Şifre gücü:");
define("_PWD_TOOSHORT", "Çok kısa");
define("_PWD_VERYWEAK", "Çok zayıf ");
define("_PWD_WEAK", "Zayıf");
define("_PWD_GOOD", "İyi");
define("_PWD_STRONG", "Mükemmel");

define("_LEGALPP", "Gizlilik Politikası");
define("_MAILISBLOCKED", "Bu e-posta adresi (veya bir kısmı) izinli değil.");
/* since 2.2.5*/
define("_COOKIEINFO","Web sitemizi kullanarak, deneyiminizi geliştirmek için çerezler kullanmayı kabul edersiniz.");
define("_MOREINFO","daha fazla bilgi");

?>