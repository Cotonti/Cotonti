/**
 * @file Code plugin.
 */

(function()
{
	var pluginName = 'code';
	
	// Регистрируем имя плагина .
	CKEDITOR.plugins.add( pluginName,
	{
		lang : [ 'en', 'ru' ],
		init : function( editor )
		{	
			//Добавляем команду на нажатие кнопки
			editor.addCommand( pluginName,new CKEDITOR.dialogCommand( 'code' ));
			 //Указываем где скрипт окна диалога.
			CKEDITOR.dialog.add( pluginName, this.path + 'dialogs/code.js' );
			// Добавляем кнопочку
			editor.ui.addButton( 'Code',
			{
				label : editor.lang.code.title,	//Title кнопки
				command : pluginName,
				icon : this.path + 'logo.gif'	//Путь к иконке
			});
		}
	});
})();