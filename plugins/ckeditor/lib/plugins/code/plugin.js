/**
 * @file Code plugin.
 */

(function()
{
	var pluginName = 'code';

	// Registering plugin name
	CKEDITOR.plugins.add( pluginName,
	{
		lang : [ 'en', 'ru' ],
		init : function( editor )
		{
			// Adding button command
			editor.addCommand( pluginName,new CKEDITOR.dialogCommand( 'code' ));
			 // Path to dialog script
			CKEDITOR.dialog.add( pluginName, this.path + 'dialogs/code.js' );
			// Adding the button
			editor.ui.addButton( 'Code',
			{
				label : editor.lang.code.title,	// Title
				command : pluginName,
				icon : this.path + 'logo.gif'	// Icon path
			});
		}
	});
})();