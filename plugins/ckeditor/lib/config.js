/* CKEditor toolbar sets for Cotonti */

CKEDITOR.editorConfig = function( config )
{
    config.toolbar = 'Full';
	config.extraPlugins = 'more';

	config.toolbar_Full =
	[
		['Source','-','Templates'],
		['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
		['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
		['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe'],
		['Maximize', 'ShowBlocks','-','About'],
		'/',
		['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
		['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
		['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		['Link','Unlink','Anchor','More'],
		['Styles','Format'],
		['TextColor','BGColor']
	];

	config.toolbar_Medium = [
		['Bold','Italic','Underline','Strike'],
		['NumberedList','BulletedList','-','Blockquote'],
		['Link','Unlink','Anchor','Smiley'],
		['TextColor','BGColor'],
		['Cut','Copy','Paste','PasteText','Scayt'],
		['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],['Source']
	];

	config.toolbar_Basic =
	[
		['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink','-','About']
	];
};