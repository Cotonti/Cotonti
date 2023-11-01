/**
 * @license Copyright (c) 2003-2022, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// https://ckeditor.com/docs/ckeditor4/latest/api/CKEDITOR_config.html

	config.toolbar = 'Full';
	config.extraPlugins = 'more,oembed,syntaxhighlight,codemirror';

	// It has a bug in FireFox
	config.removePlugins = 'emoji';

	config.allowedContent = true; // disable ACF

	// CKEditor toolbar sets for Cotonti
	config.toolbar_Full = [
		['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
		['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
		['Image','oembed','Table','HorizontalRule','Smiley','EmojiPanel','SpecialChar','PageBreak'],
		['Maximize', 'ShowBlocks','-', 'Source', '-', 'About'],
		'/',
		['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
		['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
		['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		['Link','Unlink','Anchor','More','Syntaxhighlight'],
		['Styles','Format'],
		['TextColor','BGColor']
	];

	config.toolbar_Medium = [
		['Bold','Italic','Underline','Strike'],
		['NumberedList','BulletedList','-','Blockquote','Syntaxhighlight'],
		['Image','Link','Unlink','Anchor','Smiley','EmojiPanel'],
		['TextColor','BGColor'],
		['Cut','Copy','Paste','PasteText','Scayt'],
		['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],['Source']
	];

	config.toolbar_Basic = [
		['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink']
	];
	// /CKEditor toolbar sets for Cotonti

	// The toolbar groups arrangement, optimized for two toolbar rows.
	// Example
	// config.toolbarGroups = [
	// 	{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
	// 	{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
	// 	{ name: 'links' },
	// 	{ name: 'insert' },
	// 	{ name: 'forms' },
	// 	{ name: 'tools' },
	// 	{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
	// 	{ name: 'others' },
	// 	'/',
	// 	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
	// 	{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
	// 	{ name: 'styles' },
	// 	{ name: 'colors' },
	// 	{ name: 'about' }
	// ];

	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	//config.removeButtons = 'Underline,Subscript,Superscript';

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;h4;pre';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced';
};
