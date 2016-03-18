/**
 *  CKEditor toolbar sets for Cotonti
 */

CKEDITOR.editorConfig = function( config )
{
	config.toolbar = 'Full';
	config.extraPlugins = 'more,oembed,syntaxhighlight,codemirror';

	config.allowedContent = true; // disable ACF

	config.toolbar_Full =
		[
			['Source'],
			['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
			['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
			['Image','oembed','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
			['Maximize', 'ShowBlocks','-','About'],
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
		['Image','Link','Unlink','Anchor','Smiley'],
		['TextColor','BGColor'],
		['Cut','Copy','Paste','PasteText','Scayt'],
		['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],['Source']
	];

	config.toolbar_Basic =
		[
			['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink','-','About']
		];
};
