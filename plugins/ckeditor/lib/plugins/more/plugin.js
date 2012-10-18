/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @file Code plugin.
 */

(function()
{
	var moreCmd =
	{
		exec : function( editor )
		{
			editor.insertHtml('<hr class="more" />');
		}
	};

	var pluginName = 'more';
// Регистрируем имя плагина .
CKEDITOR.plugins.add( pluginName,
{
lang : [ 'en', 'ru' ],
init : function( editor )
{//Добавляем команду на нажатие кнопки
editor.addCommand( pluginName,moreCmd);
// Добавляем кнопочку
editor.ui.addButton( 'More',
{
label : editor.lang.more.title ,//Title кнопки
command : pluginName,
icon : this.path + 'more.gif'//Путь к иконке
});
}
});
})();