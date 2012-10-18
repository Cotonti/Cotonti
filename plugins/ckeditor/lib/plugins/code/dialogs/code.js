function escapeHtml(unsafe) {
  return unsafe
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
}

CKEDITOR.dialog.add( 'code', function( editor )
{
	return {
		title : 'Code',
		minWidth : 400,
		minHeight : 200,
		contents : [
		{
			id : 'tab1',
			label : 'First Tab',
			title : 'First Tab',
			elements :
			[ {
				type : 'html',
				id : 'content',
				html :'<select size="1" name="chili">'+
				'<option value="code">Code</option>'+
				'<option value="php">PHP</option>'+
				'<option value="php-f">PHP + HTML</option>'+
				'<option value="mysql">MySQL</option>'+
				'<option value="lotusscript">LotusScript</option>'+
				'<option value="js">JavaScript</option>'+
				'<option value="java">Java</option>'+
				'<option value="html">HTML</option>'+
				'<option value="delphi">Delphi</option>'+
				'<option value="css">CSS</option>'+
				'<option value="csharp">C#</option>'+
				'<option value="cplusplus">C++</option>'+
				'</select>',
				validate : function(){
					CKEDITOR.config.chili_val= this.getValue();
				}
			},{
			id : 'input1',
			type : 'textarea',
			label : '',
			validate : function()
			{
				if ( !this.getValue() )
				{
					alert( 'Empty code' );
					return false;
				}
				var txt = '';
				if(CKEDITOR.config.chili_val=='code'){
					txt = '<div class="code"><pre>'+ escapeHtml(this.getValue()) +'</pre></div>';
				}else{
					txt = '<div class="highlight"><pre class="'+ CKEDITOR.config.chili_val + '">'+ escapeHtml(this.getValue()) +'</pre></div>';
				}
				//editor.insertElement( element );
				editor.insertHtml(txt);
				//CKEDITOR.ENTER_BR;
				return true;
			}
		}]
	}]
};
} );
