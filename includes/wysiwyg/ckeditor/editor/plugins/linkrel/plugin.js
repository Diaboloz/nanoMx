/**
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 * Lizenz: GPL
 *
 * $Revision: 6 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (Mi, 08. Jul 2015) $
 *
 * based on: lightbox-Plugin
 * Webutler V2.1 - www.webutler.de
 * Autor: Sven Zinke
 */

(function () {

  CKEDITOR.plugins.add('linkrel', {});

  function e(h) {
    try {
      var i = h.getSelection();
      if (i.getType() == CKEDITOR.SELECTION_ELEMENT) {
        var j = i.getSelectedElement();
        if (j.is('a')) return j;
      }
      var k = i.getRanges(true)[0];
      k.shrink(CKEDITOR.SHRINK_TEXT);
      var l = k.getCommonAncestor();
      return l.getAscendant('a', true);
    } catch (m) {
      return null;
    }
  };

  CKEDITOR.on('dialogDefinition', function (a) {
    var b = a.data.name,
      c = a.data.definition,
      d = a.editor;
    if (b == 'image') {
      var g = c.getContents('Link');
      g.add({
        type: 'hbox',
        widths: ['45%', '55%'],
        style: 'margin:1em 0;',
        children: [{
          type: 'text',
          id: 'advRel',
          label: d.lang.link.rel,
          'default': '',
          setup: function () {
            var h = e(d);
            if (h) {
              this.setValue(h.getAttribute('rel') || '');
            }
          },
          commit: function () {
            var h = this;
            h.linkElement = h.getDialog().linkElement;
            if (h.linkElement) {
              if (h.getValue() != '') {
                h.linkElement.setAttribute('rel', h.getValue());
              } else {
                h.linkElement.removeAttribute('rel');
              }
            }
          }
        },
        {
          type: 'html',
          html: '&nbsp;'
        }]
      }, '');
    }
  });
})();