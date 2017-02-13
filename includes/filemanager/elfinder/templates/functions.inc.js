
var elfinder2_cut_path = function(file) {
  //console.log(file);
  switch('<?php echo $this->getback ?>') {
    case 'name': return file.name;
    case 'url': return file.url;
    <?php /* elfinder2 gibt file.path falsch zurück, er setzt den Alias davor anstatt den korrekten Pfad. Deswegen hier dieser Umstand */ ?>
    case 'path':
    default: return file.url.replace(new RegExp('^(<?php echo PMX_BASE_PATH ?>)'), '')
  }
}

<?php /* pragmaMx CVS $Id: functions.inc.js 6 2015-07-08 07:07:06Z PragmaMx $ */ ?>
