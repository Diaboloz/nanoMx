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
 * $Revision: 227 $
 * $Author: PragmaMx $
 * $Date: 2016-09-28 15:44:45 +0200 (Mi, 28. Sep 2016) $
 *
 * turkish language file, translated by:
 * Sıtkı Özkurt from www.akcaabat-acisu.com
 */

defined('mxMainFileLoaded') or die('access denied');

/* Datestrings und locale Einstellungen: */
$old_setlocale = setlocale(LC_TIME, 0);
$locale = array('tr_TR.UTF-8', 'tr_TR.UTF8', 'tr_TR', 'tr', 'trk', 'turkish', 'TR', 'TUR', '792', 'CTRY_TURKEY', 'tr_TR.ISO-8859-9');
define("_SETLOCALE", setlocale(LC_TIME, $locale));
setlocale(LC_TIME, $old_setlocale);
define("_DOC_LANGUAGE", "tr");
define("_DOC_DIRECTION", "ltr");
define('_DATESTRING', '%d.%m.%Y');

/**
 * Setup Optionen zur Auswahl, siehe setup-settings.php
 */
// Neuinstallation
define('_SETUPOPTION_NEW', 'Yeni Kurulum');
define('_SETUPOPTION_NEW_DESC', 'Yeni PragmaMx kurulumu. Veritabanında olası mevcut veriler hiç değiştirilmeden kalır.');
// Update
define('_SETUPOPTION_UPDATE', 'Mevcut olan bir kurulumun güncellemesi (Update)');
define('_SETUPOPTION_UPDATE_DESC', 'Kurma betiği pragmaMx\'de zaten mevcut olan kurulumu güncellemeyi dener. Bu ayrıca PhpNuke, vkpMx ve başka çeşitli phpNuke tabanlı betik verilerinin dönüşümü için de uygundur.');
// Setupschritte
define('_STEP_SELECT', 'Yeni kurulum veya güncelleme seçeneği ');
define('_STEP_ISINCORRECT', 'Emniyet sorgusu');
define('_STEP_LICENSE', 'Lisans Koşulları');
define('_STEP_BACKUP', 'Veritabanı yedeklemesi');
define('_STEP_UPDATE', 'Veritabanı güncellemesi');
define('_STEP_DELFILES', 'gereksiz verilerin çıkartılması');
define('_STEP_FINISHEDINSTALL', 'Kurulum tamamlandı');
define('_STEP_DBSETTINGS', 'Veritabanı erişim ayarları');
define('_STEP_DBSETTINGSCREATE', 'Veritabanı erişim ayarları / Veritabanı oluşturulması');
define('_STEP_MORESETTINGS', 'Daha fazla Ayarlar');
define('_STEP_MORESETTINGSCHECK', 'Ayarların kontrol edilmesi');
define('_STEP_FINISHEDUPDATE', 'Güncelleme (Update) tamamlandı');
define('_STEP_CONFIGURATION', 'Ayarların güncelleştirilmesi');
define('_HELLOINSTALL', MX_SETUP_VERSION . ' Kurulumu ');
define('_HELLOINSTALL2', '' . preg_replace('#[[:space:]]#', '&nbsp;', MX_SETUP_VERSION) . '&#39;i kurmaya karar verdiğiniz için mutluyuz.<br /> Kurulumu başlatmadan önce mutlaka buradaki <a href="' . _MXDOKUSITE . '">Çevrimiçi Dökümantasyonu</a> okuyunuz.<br /> Eğer Dokümantasyona göz attıysanız şimdi kuruluma devam edebilirsiniz.');
define('_WHATWILLYOUDO', 'Değişik kurulum yöntemleri arasında seçme imkanına sahipsiniz. Kurma betiği tarafından önerilen yöntem zaten aktifleştirilmiştir. Eğer emin değilseniz başka bir yöntem seçiniz.<br /><br />Ne yapmak istiyorsunuz?');
define('_OLDVERSION_ERR1', 'Seçmiş olduğunuz kurulum seçeneği tespit edilen standart ile uyumlu değil,<br />bu durum sorunlara veya veri kaybına yol açabilir.');
define('_OLDVERSION_ERR2', '&quot;<em>' . @constant($GLOBALS['opt'][@$_REQUEST['setupoption']]['name']) . '</em>&quot;seçeneğini uygulamak istediğinize emin misiniz?');
define('_CONFIGSAVEMESS', 'Son olarak <em>' . basename(FILE_CONFIG_ROOT) . '</em> ayar dosyası güncelleştirilecektir.');
define('_CONFIG_OK_NEW', 'Ayar dosyası <em>' . basename(FILE_CONFIG_ROOT) . '</em> başarıyla oluşturuldu.');
define('_CONFIG_OK_OLD', 'Ayar dosyası <em>' . basename(FILE_CONFIG_ROOT) . '</em> zaten güncel ve düzgün.');
define('_CONFIG_ERR_1', '<em>' . basename(FILE_CONFIG_ROOT) . '</em> yazma korumalı!');
define('_CONFIG_ERR_2', '<em>' . basename(FILE_CONFIG_ROOT) . '</em> dosyasına yazım başarılı olmadı.');
define('_CONFIG_ERR_3', '<em>' . PMX_BASE_PATH . '</em> yazma korumalı!  haklarını lütfen ayarlayın!');
define('_CONFIG_ERR_4', '<em>' . basename(FILE_CONFIG_ROOT) . '</em> dosyası tanımlanamayan sebeplerden dolayı oluşturulamadı.');
define('_CONFIG_ERR_5', '<em>' . basename(FILE_CONFIG_ROOT) . '</em> dosyası mevcut, fakat okunamıyor.');
define('_CONFIG_ERR_6', '<em>' . basename(FILE_CONFIG_ROOT) . '</em> dosyası mevcut, fakat veriler doğru yazılmadı.');
define('_CONFIG_ERR_8', '<em>' . basename(FILE_CONFIG_ROOT) . '</em> ayar dosyası doğru oluşturulmamış olsa bile veritabanı bağlantısı tamamdır. Kurulum ile devam edebilirsiniz, fakat bundan sonra sistem ayarlarını yönetim menüsünde mutlaka kontrol edip gerekirse yeniden ayarlamalısınız.');
define('_CONFIG_BACK', 'Mevcut olan ayar dosyasının bir kopyası aşağıdaki isim ile başarıyla oluşturuldu:');
define('_CONFIG_CREATE', 'Lütfen gösterilen PHP kodları vasıtasıyla yeni bir betik dosyası oluşturunuz. Bu dosyayı <em>' . basename(FILE_CONFIG_ROOT) . '</em> olarak adlandırarak pragmaMx kurulum ana dizinine (' . dirname(basename(FILE_CONFIG_ROOT)) . ') olarak kopyalayın.<br />Bu dosyada kaynak metnin tamamının bire bir kopyalanmış olmasına dikkat ediniz.<br /><br />Bunu yaptıktan sonra, kuruluma devam edebilirsiniz.');
define('_CONFIG_BUTTONMAN', 'Ayar dosyasını el ile oluştur');
define('_CURRENTSTATUS', 'Şimdiye kadar ki kurulum durumu');
define('_THEREERROR', 'Hatalar oluştu');
define('_WILL_CREATE_TABLES', 'Bir sonraki adımda tablolar oluşturulup, güncelleştirilecektir. Bu bir müddet sürebilir!');
define('_WILL_CREATE_BACKUP', 'Eğer yedekleme seçeneğini kullanırsanız, tabloların oluşturulmasından önce seçilmiş olan veritabanın tamamının yedeklenmesi denenecektir.');
define('_CONTINUE_WITHOUTDBBACKUP', 'Yedeklemesiz devam et');
define('_CONTINUE_WITHDBBACKUP', 'Yedekleme yaparak devam et');
define('_DBFOLLOWERRORS', 'Aşağıdaki hata(lar) oluştu');
define('_NODBERRORS', 'Hata oluşmadı.');
define('_DBNOTEXIST', 'Belirtilen veritabanı sunucuda mevcut değil.');
define('_DBNOTSELECT', 'Sunucu belirtmediniz etmediniz.');
define('_DBNOACCESS', 'Sunucuya erişim engellendi.');
define('_DBOTHERERR', 'Sunucuya erişimde bir hata oluştu.');
define('_DBVERSIONFALSE', 'Üzgünüz, MYSQL versiyonunuz çok eski. Pragmamx kurulumunun devam edebilmesi için MYSQL sunucu versiyonun en az %s olması gereklidir.');
define('_NOT_CONNECT', 'Veritabanı sunucusuna bağlantı yok veya giriş verileri doğru değil.');
define('_NOT_CONNECTMORE', 'Lütfen önceki versiyonunuzun config.php dosyasının ve orada beyanbelirtilen veritabanı giriş verilerinin doğru olduğundan emin olunuz.');
define('_DB_CONNECTSUCCESS', '<em>%s</em> veritabanına bağlantı başarıyla yapıldı.');
define('_CORRECTION', 'Düzeltme');
define('_REMAKE', 'Tekrarla');
define('_IGNORE', 'Önemseme ve Devam Et');
define('_DONOTHING', 'Geç');
define('_DBARETABLES', '<li>Seçilmiş veritabanı boş değil.</li><li>Bir veritabanı yedeklemesi yapmanız önerilir.</li>');
define('_DBARENOTABLES', '<li>Seçilmiş veritabanın boş olduğu için yedekleme yapılmasına gerek yok.</li>');
define('_SUBMIT', 'Devam');
define('_OR', 'Veya');
define('_YES', 'Evet');
define('_NO', 'Hayır');
define('_GOBACK', 'Geri');
define('_CANCEL', 'İptal');
define('_FILE_NOT_FOUND', 'Dosya bulunamadı, veya okunamadı.');
define('_ACCEPT', 'Lisans koşullarını kabul ediyormusunuz?');
define('_START', 'Ana sayfa');
define('_INTRANETWARNING', 'İntranet seçeneğini sadece web sitesi internet adresi ile işlenir halde değilse seçin. Bu seçenek eğer web sitesi bir güvenlik duvarının arkasında bulunuyorsa veya bilgisayar İnternete bağlı değilse önerilebilir.');
define('_PRERR11', 'Her iki öneklerinde bir harf ile başlaması zorunludur, sadece rakamlar, harfler ve alt tireyi (_) içerebilir ve toplam ' . PREFIX_MAXLENGTH . ' karakter uzunluğunu da aşmaması gerekir.');
define('_PRERR12', 'Yeni önekin değeri yok.<br />Lütfen bir önek belirtin.');
define('_PRERR13', 'Yeni önek pragmaMx veya phpNuke standartlarına uyuyor. Lütfen güvenliğiniz için başka bir önek kullanın.');
define('_PRERR14', 'Önekte izinsiz karakterler bulunuyor. Sadece harfler, rakamlar ve alt tire (_) kullanabilirsiniz, ki önek bir rakamla da başlayamaz.<br />Lütfen başka bir önek kullanın.');
define('_PRERR15', 'Önek bir rakamla başlayamaz.<br />Lütfen başka bir önek kullanın.');
define('_PRERR16', 'Yeni önek çok uzun, önekler en fazla ' . PREFIX_MAXLENGTH . ' karakter uzunluğunda olabilir.<br />Lütfen kısa önekler kullanın.');
define('_PRERR17', 'Yeni öneki kullanan %d tablosu mevcut.<br />Lütfen başka bir önek kullanın.');
define('_PRERR18', 'Yeni kullanıcı önekinin değeri yok.<br />Lütfen bir kullanıcı öneki belirtin.');
define('_PRERR19', 'Kullanıcı önekinde izinsiz işaretler bulunuyor. Sadece harfler, rakamlar ve alt tire (_) kullanabilirsiniz, ki önek bir rakamla da başlayamaz.<br />Lütfen başka bir önek kullanın.');
define('_PRERR20', 'Kullanıcı öneki bir rakamla başlayamaz.<br />Lütfen başka bir kullanıcı öneki kullanın.');
define('_PRERR21', 'Yeni kullanıcı öneki çok uzun, önekler en fazla ' . PREFIX_MAXLENGTH . ' karakter uzunluğunda olabilir.<br />Lütfen kısa önekler kullanın.');
define('_PRERR22', 'Yeni kullanıcı önekinden zaten bir kullanıcı tablosu mevcut.<br />Lütfen başka bir kullanıcı öneki kullanın');
define('_SUPPORTINFO', 'Sisteminiz için buradan yardım alabilirsiniz: <a href="' . _MXSUPPORTSITE . '">' . _MXSUPPORTSITE . '</a>');
define('_DOKUINFO', 'Çevrimiçi Dokümantasyonu burada bulabilirsiniz: <a href="' . _MXDOKUSITE . '">' . _MXDOKUSITE . '</a>');
define('_NOBACKUPCREATED', 'Veritabanı yedeklemesi yapılmadı.');
define('_HAVE_CREATE_DBBACKUP', 'Veritabanı dosya olarak emniyet altına alındı:');
define('_HAVE_CREATE_BACKUPERR_1', 'Veritabanı yedeklemesi başarısız oldu.');
define('_HAVE_CREATE_BACKUPERR_2', 'Eğer bu veritabanı veriler içeriyorsa, devam etmeden ÖNCE sizde güncel bir yedek bulunduğundan emin olun!');
define('_SETUPHAPPY1', 'Tebrikler');
define('_SETUPHAPPY2', 'Sisteminiz şimdi tamamen kuruldu. Bir sonraki tıklamayla doğrudan yönetim menüsüne ulaşabilirsiniz.');
define('_SETUPHAPPY3', 'Öncelikle temel ayarlarınızı gözden geçirmeniz ve yeniden kaydetmeniz lazım.');
define('_DELETE_FILES', 'Eğer sisteminiz düzgün çalışıyorsa lütfen ana dizindeki (webroot)  &quot;<em>' . basename(dirname(__DIR__)) . '</em>&quot; klasörünü silin.<br /><strong>Bu güvenliğiniz için risk oluşturabilir!</strong>');
define('_GET_SQLHINTS', 'Dönüştürme/oluşturma esnasında çalıştırılan SQL Komutları');
define('_DATABASEISCURRENT', 'Veritabanı yapısı zaten güncel. Değişiklikler yapılmadı.');
define('_SEEALL', 'Tamamına bakın');
define('_DB_UPDATEREADY', 'Tablo dönüştürülmesi/oluşturulması tamamlandı.');
define('_DB_UPDATEFAIL', 'Tablo dönüştürülmesi/oluşturulması tamamen çalıştırılamadı.');
define('_DB_UPDATEFAIL2', 'Aşağıdaki önemli sistem tabloları eksik: ');
define('_BACKUPPLEASEDOIT', 'Güncellemeden önce veritabanını tamamen yedeklemeniz öneriliyor.');
define('_ERRMSG1A', 'Hata: Bir güncelleme dosyası eksik, aşağıdaki dosyanın varlığından emin olunuz:');
define('_YEAHREADY2', 'pragmaMxiniz en güncel haldedir.');
define('_SERVERMESSAGE', 'Sunucu raporu');
define('_ERRDBSYSFILENOFILES', 'Sistem tabloların hiçbiri kontrol edilemedi/oluşturulamadı, bu <em>' . PATH_SYSTABLES . '</em> dosyasında tanımlama dosyaları bulunmuyor.');
define('_ERRDBSYSFILEMISSFILES_1', 'Tüm sistem tabloları kontrol edilemiyor/oluşturulamıyor.');
define('_ERRDBSYSFILEMISSFILES_2', '<em>' . PATH_SYSTABLES . '</em> dosyasında aşağıdaki tanımlama dosyaları eksik');
define('_THESYSTABLES_1', 'Bu sistem tablo(ları) <strong>%s</strong> kontrol edilemedi/oluşturulamadı, çünkü ' . PATH_SYSTABLES . ' klasöründeki gerekli olan dosyalar yüklenemedi.');
define('_THESYSTABLES_2', 'Bu sistem tablo(ları) <strong>%s</strong> kontrol edilmedi/oluşturulmadı.');
define('_SYSTABLECREATED', '%d sistem tablosu kontrol edildi/oluşturuldu.');
define('_MODTABLESCREATED', '%d modül tablosu kontrol edildi/oluşturuldu.');
define('_NOMODTABLES', 'Modül tabloları kontrol edilmedi/oluşturulmadı.');
define('_STAT_THEREWAS', 'Toplam');
define('_STAT_TABLES_CREATED', 'tablo oluşturuldu');
define('_STAT_TABLES_RENAMED', 'tablo yeniden adlandırıldı');
define('_STAT_TABLES_CHANGED', 'tablo değiştirildi');
define('_STAT_DATAROWS_CREATED', 'veri eklendi/değiştirildi');
define('_STAT_DATAROWS_DELETED', 'veri silindi');
define('_MOREDEFFILEMISSING', '<em>' . @ADD_QUERIESFILE . '</em> dosyası ek SQL komutlarıyla eksik!');
define('_SETUPMODNOTFOUND1', 'Seçilmiş Kurulum Modülü <strong>%s</strong> mevcut değil!');
define('_ERROR', 'Hata');
define('_ERROR_FATAL', 'ciddi hata');
define('_SETUPCANCELED', 'Kurulum iptal edildi!');
define('_GOTOADMIN', 'Yönetim menüsüne git ');
define('_DBSETTINGS', 'Buraya veritabanı erişim verilerini girin. Kurulum sadece doğru ayarlanmış bir veritabanı bağlantısıyla devam ettirilebilir. Erişim verilerini web alanı sunan firmanızdan alabilirsiniz');
define('_DBNAME', 'Veritabanı Adı');
define('_DBPASS', 'Veritabanı Şifresi');
define('_DBSERVER', 'Veritabanı Sunucusu');
define('_DBTYP', 'Veritabanı Tipi');
define('_DBUSERNAME', 'Veritabanı Kullanıcısı');
define('_DBCREATEQUEST', '&quot;<em>' . @$_REQUEST['dbname'] . '</em>&quot; veritabanının oluşturulmasını denemek istiyormusunuz?');
define('_DBISCREATED', '&quot;<em>' . @$_REQUEST['dbname'] . '</em>&quot; veritabanı başarıyla oluşturuldu.');
define('_DBNOTCREATED', '&quot;<em>' . @$_REQUEST['dbname'] . '</em>&quot; veritabanının oluşturulması sırasında bir hata oluştu.');
define('_PREFIXSETTING', 'Eğer farklı pragmaMx sistemlerini bir veritabanında kullanmak istiyorsanız, önekler ilgili tabloların ayırt edilebilmesi için yararlıdır. Kullanıcı öneki kullanıcı verilerin farklı pragmaMxler içinde ortak kullanılmasını sağlar. Aksi halde kullanıcı önekinin normal öneke uyması gerekir.');
define('_PREFIX', 'Veritabanı Tablolarının Öneki');
define('_USERPREFIX', 'Kullanıcı Tablosunun Öneki');
define('_DEFAULTLANG', 'Standart Dil');
define('_INTRANETOPT', 'Intranet Ortamı');
define('_ADMINEMAIL', 'Yönetici E-posta Adresi');
define('_SITENAME', 'Sitenin İsmi');
define('_STARTDATE', 'Sitenin Başlangıç Tarihi');
define('_CHECKSETTINGS', 'Lütfen Ayarlarınızı kontrol edin!');
define('_PLEASECHECKSETTINGS', 'Lütfen şimdiye kadar yaptığınız ayarları kontrol edin.<br />Eğer tüm veriler doğruysa, kurulumu devam ettirebilirsiniz.<br />Yoksa verilerin düzeltilmesi için daha fırsatınız var.');
define('_HAVE_CREATE_TABLES', 'Tablolar oluşturuldu.');
define('_HAVE_CREATE_TABLES_7', 'Yalnız sistem için gerekli olan tablolar, hatasız oluşturuldu. Kurulum devam ettirilebilir, fakat sistemin değişik fonksiyonlarında hatalar oluşabilir.');
define('_HAVECREATE_TABLES_ERR', 'Veritabanı tamamen kurulamadı. Kurulum başarısız oldu.');
define('_CREATE_DB', 'Veritabanı oluştur');
define('_DELETESETUPDIR', 'Kurulum betiğini kullanılmaz hale getirmek için buraya tıklayınız. Bu sayede index.php yeniden adlandırılır ve dosya erişimi .htaccess vasıtasıyla engellenir. <em>(her sunucuda çalışmaz)</em>');
// add for fieldset
define('_PREFIXE', 'Önek');
define('_SITE__MORESETTINGS', 'Site Ayarları');
define('_SERVER', 'Sunucu Verileri');
define('_BACKUPBESHURE', 'Veritabanı tablolarının ayarlanmasından önce güncel bir veritabanı yedeklemesi yaptığınıza emin olun.');
define('_BACKUPBESHUREYES', 'Evet, güncel bir veritabanı yedeklemesi yaptım.');
define('_BACKUPBESHUREOK', 'Lütfen güncel bir veritabanı yedeklemesi yaptığınızı onaylayın,.');
// Modulbezeichnungen
define('Your_Account', 'Üye Hesabı');
define('News', 'Haberler');
define('blank_Home', 'Ana Sayfa');
define('Content', 'İçerikler');
define('Downloads', 'Dosyalar');
define('eBoard', 'Forum');
define('FAQ', 'S.S.S.');
define('Feedback', 'İletişim');
define('Guestbook', 'Ziyaretçi Defteri');
define('Impressum', 'Künye');
define('Kalender', 'Etkinlikler');
define('Statistics', 'İstatistik');
define('Members_List', 'Üye Listesi');
define('My_eGallery', 'Medya Galerisi');
define('Newsletter', 'Bülten');
define('Private_Messages', 'Özel Mesajlar');
define('Recommend_Us', 'Bizi Önerin');
define('Reviews', 'İncelemeler');
define('Search', 'Arama');
define('Sections', 'Konu Bölümleri');
define('Siteupdate', 'Site Yenilikleri');
define('Submit_News', 'Haber yaz');
define('Surveys', 'Anketler');
define('Top', 'En İyi On');
define('Topics', 'Konular');
define('UserGuest', 'Kişisel Ziyaretçi Defteri');
define('Web_Links', 'Bağlantılar');
define('Web_News', 'İnternet Haberleri');
define('LinkMe', 'Bize Link Verin');
define('Userinfo', 'Kimlik Bilgisi');
define('User_Registration', 'Kullanıcı Kaydı');
define('Gallery', 'Resim Galerisi');
define('Avatar', 'Avatar');
define('Banners', 'Reklamlar');
define('Encyclopedia', 'Ansiklopedi');
define('IcqList', 'Icq Listesi');
define('IrcChat', 'Sohbet');
define('Members_Web_Mail', 'Web Posta');
define('Stories_Archive', 'Haberler');
define('Themetest', 'Temalar');
define('User_Blocks', 'Bloklar');
define('User_Fotoalbum', 'Resim Albümüm');
define('legal', 'Kullanım Koşulları');
// die Nachricht für den Begrüssungsblock
define('_NEWINSTALLMESSAGEBLOCKTITLE', 'pragmaMx(' . MX_SETUP_VERSION_NUM . ')&#39;inize Hoşgeldiniz');
define('_NEWINSTALLMESSAGEBLOCK', trim(addslashes('
<p>Merhaba,</p>
<p>Bu mesajı okuyabiliyorsanız pragmaMx&#39;iniz hatasız kuruldu ve çalışıyor demektir. Tebrikler!.</p>
<p>PragmaMx&#39;i daha yakından görmeye karar verdiğiniz için size candan teşekkür etmek istiyoruz. Ümit ediyoruz ki bütün beklentilerinizi karşılayacaktır.</p>
<p>Sisteminiz için ek genişletmeleri sitemizden elde edebilirsiniz: <a href="http://www.pragmamx.org">http://pragmamx.org</a>.</p>
<p>Eğer kurulumdan sonra pragmaMx&#39;de doğrudan bir yönetici hesabı oluşturmamışsanız şimdi <a href="' . adminUrl() . '"><strong>buradan</strong></a> bir yönetici hesabı oluşturabilirsiniz.</p>
<p>İyi dileklerimizle sisteminizi keşfetmenizi diliyoruz. Yolunuzu daha kolay bulabilmeniz için yönetici bölümünde kısa bir dokümantasyon bıraktık, lütfen buna mutlaka göz atın.</p>
<p>pragmaMx Kodlama Ekibi</p>
')));
define('_DBUP_WAIT', 'Lütfen bekleyin');
define('_DBUP_MESSAGE', '
<p>Kurulum şimdi pragmaMx sisteminizi yapılandırıyor. </p>
<p>Veritabanı tabloların düzenlenmesi biraz zaman alabilir, süreç tamamlanana kadar lütfen bekleyin. Sayfadan çıkmayın veya yenilemeyin ve tarayıcıyı da kapatmayın.</p>
');

// Blockbeschriftungen:
define('_BLOCK_CAPTION_MAINMENU', 'Ana Menü');
define('_BLOCK_CAPTION_INTERNAL', 'Site İçi');
define('_BLOCK_CAPTION_COMMUNITY', 'Topluluk');
define('_BLOCK_CAPTION_OTHER', 'Diğerleri');
define('_BLOCK_CAPTION_1', 'Kurulum Uyarısı');
define('_BLOCK_CAPTION_2', 'Yönetim Menüsü');
define('_BLOCK_CAPTION_3', 'Dil');
define('_BLOCK_CAPTION_4', 'Giriş');
define('_BLOCK_CAPTION_5', 'Kullanıcı Menüsü');
define('_BLOCK_CAPTION_6', 'Kimler Çevrimiçi');
define('_BLOCK_CAPTION_7', 'SSS&#39;ler');
define('_BLOCK_CAPTION_8', 'Anket');
define('_BLOCK_CAPTION_9', 'pragmaMx Haberleri');
define('_BLOCK_CAPTION_5A', 'Kişisel Sayfanız');

/* Umgebungstest, äquivalent zu pmx_check.php */
define("_TITLE", " " . MX_SETUP_VERSION . "  ortamında test");
define("_ENVTEST", "Çevre testi");
define("_SELECTLANG", "Lütfen bir dil seçin");
define("_TEST_ISOK", "Tamam, bu sistem  " . MX_SETUP_VERSION . "  çalıstırabilir");
define("_TEST_ISNOTOK", "Bu sistem  " . MX_SETUP_VERSION . "  Sistem gereksinimlerini karşılamıyor");
define("_LEGEND", "Başlık");
define("_LEGEND_OK", "<span>Tamam</span> - Hepsi Tamam");
define("_LEGEND_WARN", "<span>Uyarı</span> - Bu özellik olmadan  " . MX_SETUP_VERSION . "  bazı işlevler kullanılabilir değil.");
define("_LEGEND_ERR", "<span>Hata</span> -  " . MX_SETUP_VERSION . "   bu özelliği gerektir ve o olmadan çalışamaz");
define("_ENVTEST_PHPFAIL", " " . MX_SETUP_VERSION . "  çalıstırmak için minimum PHP %s sürümü gereklidir. PHP sürümü: %s");
define("_ENVTEST_PHPOK", "PHP sürümü: %s");
define("_ENVTEST_MEMOK", "PHP Hafıza sınırı: %s");
define("_ENVTEST_MEMFAIL", "PHP Hafızanız  " . MX_SETUP_VERSION . "  Yüklemeyi tamamlamak için çok düşük. Minimal değer %s, ve buna ayarlanmış: %s");
define("_EXTTEST_REQFOUND", "Gerekli uzantı '%s' bulundu");
define("_EXTTEST_REQFAIL", " " . MX_SETUP_VERSION . "  çalıştırmak için uzantı '%s' gereklidir");
define("_EXTTEST_GD", "GD resim işleme için kullanılır. Onsuz, sistemin dosyaları küçük resim olarak, avatarlar, logolar ve proje simgeleri oluşturması veya yönetmesi mümkün değildir");
define("_EXTTEST_MB", "Baytlı dize Unicode ile çalışması için kullanılır. Onsuz, sistemin düzgün kelime ve dize bölme yapmayabilir ve örneğin yeni etkinlikler garip soru işareti karakter ile olabilir");
// define("_EXTTEST_ICONV", "Iconv karakter kümesi dönüstürme için kullanilir. Onsuz, sistem farkli karakter kümesi dönüstürme esnasinda biraz daha yavas");
define("_EXTTEST_IMAP", "IMAP, POP3 ve IMAP sunucularına bağlanmak için kullanılır. Onsuz, Gelen Posta modülü çalışmaz");
define("_EXTTEST_CURL", "Dış veri erişimi geliştirmek için CURL fonksiyonları.");
define("_EXTTEST_TIDY", "TIDY uzantısı etkin olduğunda, HTML çıktısı otomatik onaylanacak. Bu tarayıcıda sayfa düzenini hızlandırabilir ve web sitesini W3C uyumlu yapar.");
define("_EXTTEST_XML", "XML uzantısı RSS beslemeleri oluşturulması için diğerleri arasında gereklidir.");
define("_EXTTEST_RECFOUND", "Önerilen uzantı '%s' bulundu");
define("_EXTTEST_RECNOTFOUND", "Uzantı '%s' bulunamadı. <span class=\"details\">%s</span>");
define("_VERCHECK_DESCRIBE", "Burada listelenen dosya/klasörler eskimiş ve artık " . MX_SETUP_VERSION . " tarafından kullanılmaktadırlar. Onlar belli koşullar altında, hata ve güvenlik sorunlarına neden olabilir. Bu nedenle mutlaka silinmelidirler.");
define("_VERCHECK_DEL", "Dosya ve klasörleri sil");
define("_FILEDELNOTSURE", "Bu adımı şimdi atlayabilirsin ve daha sonra pragmaMx sisteminin sürüm yönetiminde tekrarlayabilirsin.");
define("_ERRMSG2", "Aşağıdaki dosya/klasörler otomatik olarak silinemedi. Bunu daha sonra pragmaMx sisteminin sürüm yönetiminde tekrarlayabilirsin.");
define("_PDOTEST_OK", "PDO veritabanı sürücüsü (%s) kullanılabilir");
define("_PDOTEST_FAIL", "Kullanılabilir bir PDO veritabanı sürücüsü (örneğin %s) bulunamadı");
define("_EXTTEST_PDO", "PDO eklentisi gelecekte pragmaMx için varsayılan veritabanı sürücüsü olacak. Eklenti kısa sürede mevcut olmalıdır.");
define("_EXTTEST_ZIP", "Zip işlevselliği bazı eklenti modülleri tarafından kullanılmaktadır ve mevcut olmalıdır.");

define("_DBCONNECT","veritabanı bağlantısı");
define("_EXTTEST_FILE_FAIL", "can not write : %s");
define("_EXTTEST_FILE_OK", "All file access available.");
?>