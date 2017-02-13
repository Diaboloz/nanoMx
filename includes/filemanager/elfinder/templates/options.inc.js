
var elfinder_options = {
  url: '<?php echo $this->connector ?>',
  lang: '<?php echo $this->lang ?>',
  resizable: true,
  onlyMimes: [ <?php echo $this->onlymimes ?> ],
  rememberLastDir: <?php echo $this->rememberlastdir ?> ,
  ui : ['toolbar', 'places', 'tree', 'path', 'stat'],
  dateFormat : '<?php echo $this->dateformat ?>',
  title: '<?php echo $this->title ?>',
  uiOptions : {
    // toolbar configuration
    toolbar : [
      ['back', 'forward'],<?php /* ['reload'], ['home', 'up'],*/ ?>
      ['getfile', 'download', 'open', 'quicklook', 'info'],
      ['mkdir', 'mkfile', 'upload'],
      ['copy', 'cut', 'paste'],
      ['rm'],
      ['duplicate', 'rename', 'edit', 'resize'],
      ['extract', 'archive'],
      ['search'],
      ['view', 'sort'],
      ['help']
    ]},
    commandsOptions: {
      getfile: {
        onlyURL: false,
        multiple: false,
        folders: <?php echo $this->typefolders ?>,
        oncomplete: 'close'
      }
    },
};

<?php /* pragmaMx CVS $Id: options.inc.js 6 2015-07-08 07:07:06Z PragmaMx $ 

Info: http://elrte.org/redmine/boards/2/topics/204

*/ ?>