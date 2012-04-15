/**
 * MarkItUp! extended settings for Cotonti (BBcode)
 */

var mySettings = {
	previewParserVar: 'text',
	previewPosition: 'before',
	previewAutoRefresh: false,
	onEnter: {keepDefault: false, replaceWith: '\n'},
	markupSet: [
		{name: L.bold, className:'mBold', key:'B', openWith:'[b]', closeWith:'[/b]'},
		{name: L.italic, className:'mItalic', key:'I', openWith:'[i]', closeWith:'[/i]'},
		{name: L.underline, className: 'mUnderline', key:'U', openWith:'[u]', closeWith:'[/u]'},
		{name: L.strike, className: 'mStrike', key:'S', openWith:'[s]', closeWith: '[/s]'},
		{separator:'---------------' },
		{name: L.align, className: 'mAlign',
		dropMenu: [
			{name: L.center, className: 'mCenter', multiline:true, openBlockWith: '[center]', closeBlockWith: '[/center]'},
			{name: L.justify, className: 'mJustify', multiline:true, openBlockWith: '[justify]', closeBlockWith: '[/justify]'},
			{name: L.left, className: 'mLeft', multiline:true, openBlockWith: '[left]', closeBlockWith: '[/left]'},
			{name: L.right, className: 'mRight', multiline:true, openBlockWith: '[right]', closeBlockWith: '[/right]'}
		]},
		{name: L.font, className: 'mFonts', key:'F',
		dropMenu :[
			{name: L.size, className: 'mFonts', openWith:'[size=[![' + L.size_pt +']!]]', closeWith:'[/size]' },
			{name: L.h1, className: 'mH1', openWith:'[h1]', closeWith:'[/h1]' },
			{name: L.h2, className: 'mH2', openWith:'[h2]', closeWith:'[/h2]' },
			{name: L.h3, className: 'mH3', openWith:'[h3]', closeWith:'[/h3]' },
			{name: L.h4, className: 'mH4', openWith:'[h4]', closeWith:'[/h4]' },
			{name: L.h5, className: 'mH5', openWith:'[h5]', closeWith:'[/h5]' },
			{name: L.h6, className: 'mH6', openWith:'[h6]', closeWith:'[/h6]' }
		]},
		{name: L.color, className:'palette', dropMenu: [
			{name: L.yellow,	openWith:'[color=#FCE94F]', closeWith: '[/color]',	className:"col1-1" },
			{name: L.yellow,	openWith:'[color=#EDD400]', closeWith: '[/color]', 	className:"col1-2" },
			{name: L.yellow, 	openWith:'[color=#C4A000]', closeWith: '[/color]', 	className:"col1-3" },

			{name: L.orange, 	openWith:'[color=#FCAF3E]', closeWith: '[/color]', 	className:"col2-1" },
			{name: L.orange, 	openWith:'[color=#F57900]', closeWith: '[/color]', 	className:"col2-2" },
			{name: L.orange,	openWith:'[color=#CE5C00]', closeWith: '[/color]', 	className:"col2-3" },

			{name: L.brown, 	openWith:'[color=#E9B96E]', closeWith: '[/color]', 	className:"col3-1" },
			{name: L.brown, 	openWith:'[color=#C17D11]', closeWith: '[/color]', 	className:"col3-2" },
			{name: L.brown,		openWith:'[color=#8F5902]',  closeWith: '[/color]',	className:"col3-3" },

			{name: L.green, 	openWith:'[color=#8AE234]', closeWith: '[/color]', 	className:"col4-1" },
			{name: L.green, 	openWith:'[color=#73D216]',  closeWith: '[/color]',	className:"col4-2" },
			{name: L.green,		openWith:'[color=#4E9A06]',  closeWith: '[/color]',	className:"col4-3" },

			{name: L.blue, 		openWith:'[color=#729FCF]',  closeWith: '[/color]',	className:"col5-1" },
			{name: L.blue, 		openWith:'[color=#3465A4]',  closeWith: '[/color]',	className:"col5-2" },
			{name: L.blue,		openWith:'[color=#204A87]',  closeWith: '[/color]',	className:"col5-3" },

			{name: L.purple, 	openWith:'[color=#AD7FA8]',  closeWith: '[/color]',	className:"col6-1" },
			{name: L.purple, 	openWith:'[color=#75507B]',  closeWith: '[/color]',	className:"col6-2" },
			{name: L.purple,	openWith:'[color=#5C3566]',  closeWith: '[/color]',	className:"col6-3" },

			{name: L.red, 		openWith:'[color=#EF2929]',  closeWith: '[/color]',	className:"col7-1" },
			{name: L.red, 		openWith:'[color=#CC0000]',  closeWith: '[/color]',	className:"col7-2" },
			{name: L.red,		openWith:'[color=#A40000]',  closeWith: '[/color]',	className:"col7-3" },

			{name: L.gray, 		openWith:'[color=#FFFFFF]',  closeWith: '[/color]',	className:"col8-1" },
			{name: L.gray, 		openWith:'[color=#D3D7CF]',  closeWith: '[/color]',	className:"col8-2" },
			{name: L.gray,		openWith:'[color=#BABDB6]',  closeWith: '[/color]',	className:"col8-3" },

			{name: L.gray, 		openWith:'[color=#888A85]',  closeWith: '[/color]',	className:"col9-1" },
			{name: L.gray, 		openWith:'[color=#555753]',  closeWith: '[/color]',	className:"col9-2" },
			{name: L.gray,		openWith:'[color=#000000]',  closeWith: '[/color]',	className:"col9-3" }
		]},
		{separator:'---------------' },
		{name: L.picture, className: 'mPicture', key:'P', replaceWith:'[img][![' + L.picture_url + ':!:http://]!][/img]'},
		{name: L.link, className: 'mLink', key:'L', openWith:'[url=[![URL:!:http://]!]]', closeWith:'[/url]', placeHolder: L.link_text},
		{name: L.email, className: 'mEmail', openWith:'[email=[![' + L.email_addr + ':!:john@doe.com]!]]', closeWith:'[/email]', placeHolder: L.email_text},
		{separator:'---------------' },
		{name: L.paragraph, className: 'mParagraph', openBlockWith: '[p]', closeBlockWith: '[/p]'},
		{name: L.ul, className: 'mUl', multiline:true, openWith:'[li]', closeWith:'[/li]', openBlockWith:'[list]\n', closeBlockWith:'\n[/list]'},
		{name: L.ol, className: 'mOl', multiline:true, openWith:'[li]', closeWith:'[/li]', openBlockWith:'[ol]\n', closeBlockWith:'\n[/ol]'},
		{name: L.li, className: 'mLi', openWith:'[li]', key: 'M', closeWith: '[/li]'},
		{name: L.table, multiline:true, openBlockWith:'[table]\n', closeBlockWith:'\n[/table]', placeHolder:"[tr][(!(td|!|th)!)][/(!(td|!|th)!)][/tr]", className:'mtable' },
		{name: L.table_row, openWith:'[tr]', closeWith:'[/tr]', placeHolder:"[(!(td|!|th)!)][/(!(td|!|th)!)]", className:'mtable-row' },
		{name: L.table_cell, openWith:'[(!(td|!|th)!)]', closeWith:'[/(!(td|!|th)!)]', className:'mtable-col' },
		{separator:'---------------' },
		{name: L.quote, className:'mQuote', dropMenu: [
			{name: L.quote, className: 'mQuote', multiline:true, openBlockWith:'[quote=[![' + L.quote_from + ']!]]', closeBlockWith:'[/quote]',
				afterInsert: function (h)
				{
					var str = $(h.textarea).val();
					if (str.indexOf('[quote=]') >= 0)
					{
						$(h.textarea).val(str.replace('[quote=]', '[quote]'));
					}
				}
			},
			{name: L.pre, className: 'mPre', multiline:true, openBlockWith:'[pre]', closeBlockWith:'[/pre]'},
			{name: L.spoiler, className: 'mSpoiler', multiline:true, openBlockWith:'[spoiler=[![' + L.spoiler_text + ']!]]', closeBlockWith:'[/spoiler]'}
		]},
		{name: L.code, className: 'mCode', multiline:true, openBlockWith:'[code]', closeBlockWith:'[/code]'},
		{name: L.hide, className: 'mHide', multiline:true, openBlockWith:'[hide]', closeBlockWith:'[/hide]'},
		{name: L.smilies, className: "mSmilies", replaceWith: function(markitup) { showSmilies(markitup) } },
		{name: L.more, className: 'mMore', replaceWith: '[more]'},
		{separator:'---------------' },
		{name: L.clean, className:"mClean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") } },
		{name: L.preview, className:"mPreview", call:'preview' }
	]
}

// Medium editor
var mediSettings = {
	previewParserVar: 'text',
	previewPosition: 'before',
	previewAutoRefresh: false,
	onEnter: {keepDefault: false, replaceWith: '\n'},
	markupSet: [
		{name: L.bold, className:'mBold', key:'B', openWith:'[b]', closeWith:'[/b]'},
		{name: L.italic, className:'mItalic', key:'I', openWith:'[i]', closeWith:'[/i]'},
		{name: L.underline, className: 'mUnderline', key:'U', openWith:'[u]', closeWith:'[/u]'},
		{name: L.strike, className: 'mStrike', key:'S', openWith:'[s]', closeWith: '[/s]'},
		{separator:'---------------' },
		{name: L.color, className:'palette', dropMenu: [
			{name: L.yellow,	openWith:'[color=#FCE94F]', closeWith: '[/color]',	className:"col1-1" },
			{name: L.yellow,	openWith:'[color=#EDD400]', closeWith: '[/color]', 	className:"col1-2" },
			{name: L.yellow, 	openWith:'[color=#C4A000]', closeWith: '[/color]', 	className:"col1-3" },

			{name: L.orange, 	openWith:'[color=#FCAF3E]', closeWith: '[/color]', 	className:"col2-1" },
			{name: L.orange, 	openWith:'[color=#F57900]', closeWith: '[/color]', 	className:"col2-2" },
			{name: L.orange,	openWith:'[color=#CE5C00]', closeWith: '[/color]', 	className:"col2-3" },

			{name: L.brown, 	openWith:'[color=#E9B96E]', closeWith: '[/color]', 	className:"col3-1" },
			{name: L.brown, 	openWith:'[color=#C17D11]', closeWith: '[/color]', 	className:"col3-2" },
			{name: L.brown,		openWith:'[color=#8F5902]',  closeWith: '[/color]',	className:"col3-3" },

			{name: L.green, 	openWith:'[color=#8AE234]', closeWith: '[/color]', 	className:"col4-1" },
			{name: L.green, 	openWith:'[color=#73D216]',  closeWith: '[/color]',	className:"col4-2" },
			{name: L.green,		openWith:'[color=#4E9A06]',  closeWith: '[/color]',	className:"col4-3" },

			{name: L.blue, 		openWith:'[color=#729FCF]',  closeWith: '[/color]',	className:"col5-1" },
			{name: L.blue, 		openWith:'[color=#3465A4]',  closeWith: '[/color]',	className:"col5-2" },
			{name: L.blue,		openWith:'[color=#204A87]',  closeWith: '[/color]',	className:"col5-3" },

			{name: L.purple, 	openWith:'[color=#AD7FA8]',  closeWith: '[/color]',	className:"col6-1" },
			{name: L.purple, 	openWith:'[color=#75507B]',  closeWith: '[/color]',	className:"col6-2" },
			{name: L.purple,	openWith:'[color=#5C3566]',  closeWith: '[/color]',	className:"col6-3" },

			{name: L.red, 		openWith:'[color=#EF2929]',  closeWith: '[/color]',	className:"col7-1" },
			{name: L.red, 		openWith:'[color=#CC0000]',  closeWith: '[/color]',	className:"col7-2" },
			{name: L.red,		openWith:'[color=#A40000]',  closeWith: '[/color]',	className:"col7-3" },

			{name: L.gray, 		openWith:'[color=#FFFFFF]',  closeWith: '[/color]',	className:"col8-1" },
			{name: L.gray, 		openWith:'[color=#D3D7CF]',  closeWith: '[/color]',	className:"col8-2" },
			{name: L.gray,		openWith:'[color=#BABDB6]',  closeWith: '[/color]',	className:"col8-3" },

			{name: L.gray, 		openWith:'[color=#888A85]',  closeWith: '[/color]',	className:"col9-1" },
			{name: L.gray, 		openWith:'[color=#555753]',  closeWith: '[/color]',	className:"col9-2" },
			{name: L.gray,		openWith:'[color=#000000]',  closeWith: '[/color]',	className:"col9-3" }
		]},
		{separator:'---------------' },
		{name: L.picture, className: 'mPicture', key:'P', replaceWith:'[img][![' + L.picture_url + ':!:http://]!][/img]'},
		{name: L.link, className: 'mLink', key:'L', openWith:'[url=[![URL:!:http://]!]]', closeWith:'[/url]', placeHolder: L.link_text},
		{name: L.email, className: 'mEmail', openWith:'[email=[![' + L.email_addr + ':!:john@doe.com]!]]', closeWith:'[/email]', placeHolder: L.email_text},
		{separator:'---------------' },
		{name: L.ul, className: 'mUl', multiline:true, openWith:'[li]', closeWith:'[/li]', openBlockWith:'[list]\n', closeBlockWith:'\n[/list]'},
		{name: L.ol, className: 'mOl', multiline:true, openWith:'[li]', closeWith:'[/li]', openBlockWith:'[ol]\n', closeBlockWith:'\n[/ol]'},
		{name: L.li, className: 'mLi', openWith:'[li]', key: 'M', closeWith: '[/li]'},
		{separator:'---------------' },
		{name: L.quote, className:'mQuote', dropMenu: [
			{name: L.quote, className: 'mQuote', multiline:true, openBlockWith:'[quote=[![' + L.quote_from + ']!]]', closeBlockWith:'[/quote]',
				afterInsert: function (h)
				{
					var str = $(h.textarea).val();
					if (str.indexOf('[quote=]') >= 0)
					{
						$(h.textarea).val(str.replace('[quote=]', '[quote]'));
					}
				}
			},
			{name: L.pre, className: 'mPre', multiline:true, openBlockWith:'[pre]', closeBlockWith:'[/pre]'},
		]},
		{name: L.code, className: 'mCode', multiline:true, openBlockWith:'[code]', closeBlockWith:'[/code]'},
		{name: L.hide, className: 'mHide', multiline:true, openBlockWith:'[hide]', closeBlockWith:'[/hide]'},
		{name: L.smilies, className: "mSmilies", replaceWith: function(markitup) { showSmilies(markitup) } },
		{separator:'---------------' },
		{name: L.clean, className:"mClean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") } },
		{name: L.preview, className:"mPreview", call:'preview' }
	]
}

// Mini editor
var miniSettings = {
		previewParserVar: 'text',
		previewPosition: 'before',
		previewAutoRefresh: false,
		onEnter: {keepDefault: false, replaceWith: '\n'},
		markupSet: [
			{name: L.bold, className:'mBold', key:'B', openWith:'[b]', closeWith:'[/b]'},
			{name: L.italic, className:'mItalic', key:'I', openWith:'[i]', closeWith:'[/i]'},
			{name: L.link, className: 'mLink', key:'L', openWith:'[url=[![URL:!:http://]!]]', closeWith:'[/url]', placeHolder: L.link_text},
			{name: L.picture, className: 'mPicture', key:'P', replaceWith:'[img][![' + L.picture_url + ':!:http://]!][/img]'},
			{name: L.quote, className: 'mQuote', multiline:true, openBlockWith:'[quote=[![' + L.quote_from + ']!]]', closeBlockWith:'[/quote]',
					afterInsert: function (h)
					{
						var str = $(h.textarea).val();
						if (str.indexOf('[quote=]') >= 0)
						{
							$(h.textarea).val(str.replace('[quote=]', '[quote]'));
						}
					}
			},
			{name: L.smilies, className: "mSmilies", replaceWith: function(markitup) { showSmilies(markitup) } },
			{name: L.preview, className:"mPreview", call:'preview' }
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
			emoticon = $(this).attr("name");
			$.markItUp( { replaceWith: ' ' + emoticon + ' ' } );
			return false;
		});
		$('#smilies').jqm();
	}
	$('#smilies').jqmShow();
}