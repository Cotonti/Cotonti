/**
 * MarkItUp! extended settings for Cotonti (BBcode)
 */

var mySettings = {
	previewParserVar: 'text',
	previewPosition: 'before',
	previewAutoRefresh: false,
	onEnter: {keepDefault: false, replaceWith: '\n'},
	markupSet: [
		{name: L.bold, className:'mBold', key:'B', openWith:'(!(<strong>|!|<b>)!)', closeWith:'(!(</strong>|!|</b>)!)'},
		{name: L.italic, className:'mItalic', key:'I', openWith:'(!(<em>|!|<i>)!)', closeWith:'(!(</em>|!|</i>)!)'},
		{name: L.underline, className: 'mUnderline', key:'U', openWith:'<span style="text-decoration:underline">', closeWith:'</span>'},
		{name: L.strike, className: 'mStrike', key:'S', openWith:'<span style="text-decoration:line-through">', closeWith:'</span>'},
		{separator:'---------------'},
		{name: L.paragraph, className: 'mParagraph', key:'P', multiline: true, openBlockWith:'<p>', closeBlockWith:'</p>'},
		{name: L.align, className: 'mAlign',
		dropMenu: [
			{name: L.center, className: 'mCenter', multiline: true, openBlockWith: '<div style="text-align:center">', closeBlockWith:'</div>'},
			{name: L.justify, className: 'mJustify', multiline: true, openBlockWith: '<div style="text-align:justify">', closeBlockWith:'</div>'},
			{name: L.left, className: 'mLeft', multiline: true, openBlockWith: '<div style="text-align:left">', closeBlockWith:'</div>'},
			{name: L.right, className: 'mRight', multiline: true, openBlockWith: '<div style="text-align:right">', closeBlockWith:'</div>'}
		]},
		{name: L.font, className: 'mFonts', key:'F',
		dropMenu :[
			{name: L.h1, className: 'mH1', openWith:'<h1>', closeWith:'</h1>'},
			{name: L.h2, className: 'mH2', openWith:'<h2>', closeWith:'</h2>'},
			{name: L.h3, className: 'mH3', openWith:'<h3>', closeWith:'</h3>'},
			{name: L.h4, className: 'mH4', openWith:'<h4>', closeWith:'</h4>'},
			{name: L.h5, className: 'mH5', openWith:'<h5>', closeWith:'</h5>'},
			{name: L.h6, className: 'mH6', openWith:'<h6>', closeWith:'</h6>'}
		]},
		{name: L.color, className:'palette', dropMenu: [
			{name: L.yellow,	openWith:'<span style="color:#FCE94F">', closeWith: '</span>',	className:"col1-1"},
			{name: L.yellow,	openWith:'<span style="color:#EDD400">', closeWith: '</span>', 	className:"col1-2"},
			{name: L.yellow, 	openWith:'<span style="color:#C4A000">', closeWith: '</span>', 	className:"col1-3"},

			{name: L.orange, 	openWith:'<span style="color:#FCAF3E">', closeWith: '</span>', 	className:"col2-1"},
			{name: L.orange, 	openWith:'<span style="color:#F57900">', closeWith: '</span>', 	className:"col2-2"},
			{name: L.orange,	openWith:'<span style="color:#CE5C00">', closeWith: '</span>', 	className:"col2-3"},

			{name: L.brown, 	openWith:'<span style="color:#E9B96E">', closeWith: '</span>', 	className:"col3-1"},
			{name: L.brown, 	openWith:'<span style="color:#C17D11">', closeWith: '</span>', 	className:"col3-2"},
			{name: L.brown,		openWith:'<span style="color:#8F5902">', closeWith: '</span>',	className:"col3-3"},

			{name: L.green, 	openWith:'<span style="color:#8AE234">', closeWith: '</span>', 	className:"col4-1"},
			{name: L.green, 	openWith:'<span style="color:#73D216">', closeWith: '</span>',	className:"col4-2"},
			{name: L.green,		openWith:'<span style="color:#4E9A06">', closeWith: '</span>',	className:"col4-3"},

			{name: L.blue, 		openWith:'<span style="color:#729FCF">', closeWith: '</span>',	className:"col5-1"},
			{name: L.blue, 		openWith:'<span style="color:#3465A4">', closeWith: '</span>',	className:"col5-2"},
			{name: L.blue,		openWith:'<span style="color:#204A87">', closeWith: '</span>',	className:"col5-3"},

			{name: L.purple, 	openWith:'<span style="color:#AD7FA8">', closeWith: '</span>',	className:"col6-1"},
			{name: L.purple, 	openWith:'<span style="color:#75507B">', closeWith: '</span>',	className:"col6-2"},
			{name: L.purple,	openWith:'<span style="color:#5C3566">', closeWith: '</span>',	className:"col6-3"},

			{name: L.red, 		openWith:'<span style="color:#EF2929">', closeWith: '</span>',	className:"col7-1"},
			{name: L.red, 		openWith:'<span style="color:#CC0000">', closeWith: '</span>',	className:"col7-2"},
			{name: L.red,		openWith:'<span style="color:#A40000">', closeWith: '</span>',	className:"col7-3"},

			{name: L.gray, 		openWith:'<span style="color:#FFFFFF">', closeWith: '</span>',	className:"col8-1"},
			{name: L.gray, 		openWith:'<span style="color:#D3D7CF">', closeWith: '</span>',	className:"col8-2"},
			{name: L.gray,		openWith:'<span style="color:#BABDB6">', closeWith: '</span>',	className:"col8-3"},

			{name: L.gray, 		openWith:'<span style="color:#888A85">', closeWith: '</span>',	className:"col9-1"},
			{name: L.gray, 		openWith:'<span style="color:#555753">', closeWith: '</span>',	className:"col9-2"},
			{name: L.gray,		openWith:'<span style="color:#000000">', closeWith: '</span>',	className:"col9-3"}
		]},
		{separator:'---------------'},
		{name: L.picture, className: 'mPicture', key:'P', replaceWith:'<img src="[![' + L.picture_url + ':!:http://]!]" />'},
		{name: L.link, className: 'mLink', key:'L', openWith:'<a href="[![URL:!:http://]!]">', closeWith:'</a>', placeHolder: L.link_text},
		{name: L.email, className: 'mEmail', openWith:'<a href="mailto:[![' + L.email_addr + ':!:john@doe.com]!]">', closeWith:'</a>', placeHolder: L.email_text},
		{separator:'---------------'},
		{name: L.ul, className: 'mUl', openWith:'<li>', closeWith:'</li>', multiline:true, openBlockWith:'<ul>\n', closeBlockWith:'\n</ul>'},
		{name: L.ol, className: 'mOl', openWith:'<li>', closeWith:'</li>', multiline:true, openBlockWith:'<ol>\n', closeBlockWith:'\n</ol>'},
		{name: L.li, className: 'mLi', openWith:'<li>', key: 'M', closeWith: '</li>'},
		{name: L.table, openBlockWith:'<table>\n', closeBlockWith:'\n</table>', placeHolder:"<tr><(!(td|!|th)!)></(!(td|!|th)!)></tr>", className:'mtable',multiline:true},
		{name: L.table_row, openWith:'<tr>', closeWith:'</tr>', placeHolder:"<(!(td|!|th)!)></(!(td|!|th)!)>", className:'mtable-row'},
		{name: L.table_cell, openWith:'<(!(td|!|th)!)>', closeWith:'</(!(td|!|th)!)>', className:'mtable-col'},
		{separator:'---------------'},
		{name: L.quote, className:'mQuote', dropMenu: [
			{name: L.quote, className: 'mQuote', multiline:true, openBlockWith:'<blockquote><strong>[![' + L.quote_from + ']!]: </strong>', closeBlockWith:'</blockquote>',
				afterInsert: function (h)
				{
					var str = $(h.textarea).val();
					if (str.indexOf('<blockquote><strong>: </strong>') >= 0)
					{
						$(h.textarea).val(str.replace('<blockquote><strong>: </strong>', '<blockquote>'));
					}
				}
			},
			{name: L.pre, className: 'mPre', multiline:true, openBlockWith:'<pre>', closeBlockWith:'</pre>'},
			{name: L.code, className: 'mCode', multiline:true, openBlockWith:'<code>', closeBlockWith:'</code>'}
		]},
		{name: L.smilies, className: "mSmilies", replaceWith: function(markitup) {showSmilies(markitup)}},
		{name: L.more, className: 'mMore', replaceWith: '<hr class="more" />'},
		{separator:'---------------'},
		{name: L.clean, className:"mClean", replaceWith:function(markitup) {return markitup.selection.replace(/\[(.*?)\]/g, "")}},
		{name: L.preview, className:"mPreview", call:'preview'}
	]
}

// Medium editor
var mediSettings = {
	previewParserVar: 'text',
	previewPosition: 'before',
	previewAutoRefresh: false,
	onEnter: {keepDefault: false, replaceWith: '\n'},
	markupSet: [
		{name: L.bold, className:'mBold', key:'B', openWith:'(!(<strong>|!|<b>)!)', closeWith:'(!(</strong>|!|</b>)!)'},
		{name: L.italic, className:'mItalic', key:'I', openWith:'(!(<em>|!|<i>)!)', closeWith:'(!(</em>|!|</i>)!)'},
		{name: L.underline, className: 'mUnderline', key:'U', openWith:'<span style="text-decoration:underline">', closeWith:'</span>'},
		{name: L.strike, className: 'mStrike', key:'S', openWith:'<span style="text-decoration:line-through">', closeWith:'</span>'},
		{separator:'---------------'},
		{name: L.paragraph, className: 'mParagraph', key:'P', multiline:true, openBlockWith:'<p>', closeBlockWith:'</p>'},
		{name: L.color, className:'palette', dropMenu: [
			{name: L.yellow,	openWith:'<span style="color:#FCE94F">', closeWith: '</span>',	className:"col1-1"},
			{name: L.yellow,	openWith:'<span style="color:#EDD400">', closeWith: '</span>', 	className:"col1-2"},
			{name: L.yellow, 	openWith:'<span style="color:#C4A000">', closeWith: '</span>', 	className:"col1-3"},

			{name: L.orange, 	openWith:'<span style="color:#FCAF3E">', closeWith: '</span>', 	className:"col2-1"},
			{name: L.orange, 	openWith:'<span style="color:#F57900">', closeWith: '</span>', 	className:"col2-2"},
			{name: L.orange,	openWith:'<span style="color:#CE5C00">', closeWith: '</span>', 	className:"col2-3"},

			{name: L.brown, 	openWith:'<span style="color:#E9B96E">', closeWith: '</span>', 	className:"col3-1"},
			{name: L.brown, 	openWith:'<span style="color:#C17D11">', closeWith: '</span>', 	className:"col3-2"},
			{name: L.brown,		openWith:'<span style="color:#8F5902">', closeWith: '</span>',	className:"col3-3"},

			{name: L.green, 	openWith:'<span style="color:#8AE234">', closeWith: '</span>', 	className:"col4-1"},
			{name: L.green, 	openWith:'<span style="color:#73D216">', closeWith: '</span>',	className:"col4-2"},
			{name: L.green,		openWith:'<span style="color:#4E9A06">', closeWith: '</span>',	className:"col4-3"},

			{name: L.blue, 		openWith:'<span style="color:#729FCF">', closeWith: '</span>',	className:"col5-1"},
			{name: L.blue, 		openWith:'<span style="color:#3465A4">', closeWith: '</span>',	className:"col5-2"},
			{name: L.blue,		openWith:'<span style="color:#204A87">', closeWith: '</span>',	className:"col5-3"},

			{name: L.purple, 	openWith:'<span style="color:#AD7FA8">', closeWith: '</span>',	className:"col6-1"},
			{name: L.purple, 	openWith:'<span style="color:#75507B">', closeWith: '</span>',	className:"col6-2"},
			{name: L.purple,	openWith:'<span style="color:#5C3566">', closeWith: '</span>',	className:"col6-3"},

			{name: L.red, 		openWith:'<span style="color:#EF2929">', closeWith: '</span>',	className:"col7-1"},
			{name: L.red, 		openWith:'<span style="color:#CC0000">', closeWith: '</span>',	className:"col7-2"},
			{name: L.red,		openWith:'<span style="color:#A40000">', closeWith: '</span>',	className:"col7-3"},

			{name: L.gray, 		openWith:'<span style="color:#FFFFFF">', closeWith: '</span>',	className:"col8-1"},
			{name: L.gray, 		openWith:'<span style="color:#D3D7CF">', closeWith: '</span>',	className:"col8-2"},
			{name: L.gray,		openWith:'<span style="color:#BABDB6">', closeWith: '</span>',	className:"col8-3"},

			{name: L.gray, 		openWith:'<span style="color:#888A85">', closeWith: '</span>',	className:"col9-1"},
			{name: L.gray, 		openWith:'<span style="color:#555753">', closeWith: '</span>',	className:"col9-2"},
			{name: L.gray,		openWith:'<span style="color:#000000">', closeWith: '</span>',	className:"col9-3"}
		]},
		{separator:'---------------'},
		{name: L.picture, className: 'mPicture', key:'P', replaceWith:'<img src="[![' + L.picture_url + ':!:http://]!]" />'},
		{name: L.link, className: 'mLink', key:'L', openWith:'<a href="[![URL:!:http://]!]">', closeWith:'</a>', placeHolder: L.link_text},
		{name: L.email, className: 'mEmail', openWith:'<a href="mailto:[![' + L.email_addr + ':!:john@doe.com]!]">', closeWith:'</a>', placeHolder: L.email_text},
		{separator:'---------------'},
		{name: L.ul, className: 'mUl', openWith:'<li>', closeWith:'</li>', multiline:true, openBlockWith:'<ul>\n', closeBlockWith:'\n</ul>'},
		{name: L.ol, className: 'mOl', openWith:'<li>', closeWith:'</li>', multiline:true, openBlockWith:'<ol>\n', closeBlockWith:'\n</ol>'},
		{name: L.li, className: 'mLi', openWith:'<li>', key: 'M', closeWith: '</li>'},
		{separator:'---------------'},
		{name: L.quote, className:'mQuote', dropMenu: [
			{name: L.quote, className: 'mQuote', multiline:true, openBlockWith:'<blockquote><strong>[![' + L.quote_from + ']!]: </strong>', closeBlockWith:'</blockquote>',
				afterInsert: function (h)
				{
					var str = $(h.textarea).val();
					if (str.indexOf('<blockquote><strong>: </strong>') >= 0)
					{
						$(h.textarea).val(str.replace('<blockquote><strong>: </strong>', '<blockquote>'));
					}
				}
			},
			{name: L.pre, className: 'mPre', multiline:true, openBlockWith:'<pre>', closeBlockWith:'</pre>'},
			{name: L.code, className: 'mCode', multiline:true, openBlockWith:'<pre class="code">', closeBlockWith:'</pre>'}
		]},
		{name: L.smilies, className: "mSmilies", replaceWith: function(markitup) {showSmilies(markitup)}},
		{separator:'---------------'},
		{name: L.clean, className:"mClean", replaceWith:function(markitup) {return markitup.selection.replace(/\[(.*?)\]/g, "")}},
		{name: L.preview, className:"mPreview", call:'preview'}
	]
}

// Mini editor
var miniSettings = {
		previewParserVar: 'text',
		previewPosition: 'before',
		previewAutoRefresh: false,
		onEnter: {keepDefault: false, replaceWith: '\n'},
		markupSet: [
			{name: L.bold, className:'mBold', key:'B', openWith:'(!(<strong>|!|<b>)!)', closeWith:'(!(</strong>|!|</b>)!)'},
			{name: L.italic, className:'mItalic', key:'I', openWith:'(!(<em>|!|<i>)!)', closeWith:'(!(</em>|!|</i>)!)'},
			{name: L.link, className: 'mLink', key:'L', openWith:'<a href="[![URL:!:http://]!]">', closeWith:'</a>', placeHolder: L.link_text},
			{name: L.picture, className: 'mPicture', key:'P', replaceWith:'<img src="[![' + L.picture_url + ':!:http://]!]" />'},
			{name: L.quote, className: 'mQuote', multiline:true, openBlockWith:'<blockquote><strong>[![' + L.quote_from + ']!]: </strong>', closeBlockWith:'</blockquote>',
				afterInsert: function (h)
				{
					var str = $(h.textarea).val();
					if (str.indexOf('<blockquote><strong>: </strong>') >= 0)
					{
						$(h.textarea).val(str.replace('<blockquote><strong>: </strong>', '<blockquote>'));
					}
				}
			},
			{name: L.smilies, className: "mSmilies", replaceWith: function(markitup) {showSmilies(markitup)}},
			{name: L.preview, className:"mPreview", call:'preview'}
		]
}

// Renders and displays smilies dialog
// Using jqModal, see http://dev.iceburg.net/jquery/jqModal/
function showSmilies(markitup) {
	var perRow = smileBox.perRow;
	if($('#smilies').length != 1) {
		var smileHtml = '<table class="cells" cellpadding="0">';
		var code;
		for(var i = 0; i < smileSet.length; i++) {
			if(i % perRow == 0) {
				if(i != 0) smileHtml += '</tr>';
				smileHtml += '<tr>';
			}
			code = smileSet[i].code;
			code = code.replace(/</g, '&lt;');
			code = code.replace(/>/g, '&gt;');
			code = code.replace('/"/g', '&quot;');
			smileHtml += '<td><a class="smlink" href="#" name="'+code+'" title="'+smileSet[i].lang+'"><img src="./images/smilies/'+smileSet[i].file+'" alt="'+code+'" /></a></td>';
		}
		if(i % perRow > 0) {
			for(var j = i % perRow; j < perRow; j++) {
				smileHtml += '<td>&nbsp;</td>';
			}
		}
		smileHtml += '</tr></table>';
		var style = 'margin-left:-'+(smileBox.width/2)+'px;margin-top:-'+(smileBox.height/2)+'px;width:'+smileBox.width+'px;height:'+smileBox.height+'px';
		$('body').append('<div id="smilies" class="jqmWindow" style="' + style + '"><h4>' + L.smilies + '</h4>' + smileHtml + '<p><a href="#" class="jqmClose">' + L.close + '</a></p></div>');
		$('#smilies a.smlink').click(function() {
			emoticon = $(this).html();
			$.markItUp( {replaceWith: ' ' + emoticon + ' '} );
			return false;
		});
		$('#smilies').jqm();
	}
	$('#smilies').jqmShow();
}