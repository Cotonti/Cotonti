/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @file Code plugin.
 */

(function() {
	var moreCmd = {
		exec: function(editor) {
			editor.insertHtml('<hr class="more" />');
		}
	};

	CKEDITOR.plugins.add('more', {
		lang: [ 'en', 'ru' ],
		init: function(editor) {
			editor.addCommand('more', moreCmd);
			editor.ui.addButton('More', {
				label: 'Insert &quot;Read more&quot; page cut',
				command: 'more',
				icon: this.path + 'more.gif'
			});
		}
	});
})();
