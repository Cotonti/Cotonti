/*
 * ChiliToolbar v1.2
 *
 * Copyright (c) 2008 Orkan (orkans@gmail.com)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * Installation:
 * Put these two lines in <HEAD> section, after jquery.chili.js
 *   <script src="jquery.chili-toolbar.js" type="text/javascript"></script>
 *   <link href="jquery.chili-toolbar.css" type="text/css" rel="styleshee
 *
 * $Rev: 16 $
 * $Date:: 2008-09-03 #$
 *
 * Depends:
 *	jquery.chili.js
 *
 */

(function($) {

ChiliBook.Toolbar = {
	Version: "1.2",
	Chili: $.fn.chili,
	Command: {
		ViewSource: {
			Label: "view plain",
			Cmd: function(el){
				var wnd = window.open("", ChiliBook.Toolbar.Utils.PopUpTarget, "width=750, height=400, resizable=1, scrollbars=0");
				wnd.document.write('<textarea style="width:99%;height:99%">'+$.data(el, "chili.text").replace("&", "&amp;")+'</textarea>');
				wnd.document.close();
			}
		},
		CopyToClipboard: {
			Label: "copy to clipboard",
			Cmd: function(el){
				if(window.clipboardData) {
					window.clipboardData.setData("text", $.data(el, "chili.text"));
					ChiliBook.Toolbar.Lang.Clipboard_Y && alert(ChiliBook.Toolbar.Lang.Clipboard_Y);
				}
				else if(ChiliBook.Toolbar.Clipboard.Swf) {
					if(!ChiliBook.Toolbar.Clipboard.Div) ChiliBook.Toolbar.Clipboard.Div = $("<div/>").appendTo(document.body);
					ChiliBook.Toolbar.Clipboard.Div.html('<embed src="'+ChiliBook.Toolbar.Clipboard.Swf+'" FlashVars="clipboard='+encodeURIComponent($.data(el, "chili.text"))+'" width="0" height="0" type="application/x-shockwave-flash"></embed>');
					ChiliBook.Toolbar.Lang.Clipboard_Y && alert(ChiliBook.Toolbar.Lang.Clipboard_Y);
				}
				else ChiliBook.Toolbar.Lang.Clipboard_N && alert(ChiliBook.Toolbar.Lang.Clipboard_N);
			}
		},
		PrintSource: {
			Label: "print",
			Cmd: function(el){
				var ifr = $("<iframe/>").css({position:"absolute", width:0, height:0, left:0, top:0}).appendTo(document.body).get(0);
				var doc = ifr.contentWindow.document;
				var p = el.parentNode;

				ChiliBook.Toolbar.Utils.CopyStyles(doc);
				doc.write('<'+p.tagName+' class="'+p.className+'"><'+el.tagName+' class="'+el.className+'">'+el.innerHTML+'</'+el.tagName+'></'+p.tagName+'>');
				doc.close();

				ifr.contentWindow.focus();
				ifr.contentWindow.print();
				alert(ChiliBook.Toolbar.Lang.Printing);
				document.body.removeChild(ifr);
			}
		},
		About: {
			Label: "?",
			Cmd: function(el){
				var tpl = '<div class="colored" style="height:200px;text-align:center"><strong>ChiliToolbar v{V}</strong><div style="padding:1em 22px">Highlight your snippets with<br />jQuery&#039;s <a href="http://noteslog.com/chili/" target="{T}">Chili</a> code highlighter<br />and display it in a<br /><a href="http://www.dreamprojections.com/syntaxhighlighter/?ref=about" target="{T}">SyntaxHighlighter</a> way!<br /><br />&copy; 2008 <a href="mailto:orkans@gmail.com" target="{T}">Orkan</a><br /><br /><input type="button" value="OK" onClick="window.close()" style="padding:0 1em" /></div></div>';
				var wnd	= window.open("", "", "width=300, height=200, scrollbars=0");
				var doc	= wnd.document;

				ChiliBook.Toolbar.Utils.CopyStyles(doc);
				
				doc.write(tpl.replace('{V}', ChiliBook.Toolbar.Version).replace('{T}', ChiliBook.Toolbar.Utils.PopUpTarget));
				doc.close();
				wnd.focus();
			}
		}
	},
	Clipboard:  {
		Swf: "clipboard.swf",
		Div: null
	},
	Utils: {
		CopyStyles: function(doc) {
			for(var i = 0; i < document.styleSheets.length; i++)
			{
				var style = document.styleSheets[i];
				var owner = style.ownerNode ? style.ownerNode : style.owningElement;
				var media = typeof style.media == "string" ? style.media : style.media.mediaText;

				if(owner.tagName.toLowerCase() == 'link') doc.write('<link href="'+owner.href+'" rel="stylesheet" type="text/css" '+(media?'media="'+media+'"':"")+' />');
				else doc.write('<style type="text/css" '+(media ? 'media="'+media+'"' : "")+'>'+(style.cssText ? style.cssText : owner.textContent)+'</style>');
			}
			doc.write('<style type="text/css" media="all">\nhtml,body {margin:0;padding:0}\n</style>');
		},
		IEOverflowFix: "1.5em",
		PopUpTarget: "main"
	},
	Lang: {
		Clipboard_Y: "The code is in your clipboard now",
		Clipboard_N: "Sorry, the code cannot be copied to your clipboard",
		Printing: "Printing..."
	}
};


$.fn.chili = function(options) {

	// Prepare toolbar
	this.bind("chili.before_coloring", function(){
		var self	= this,
			$this	= $(this),
			$bar	= $("<div/>").addClass("bar"),
			$tools	= $("<div/>").addClass("tools");

		$.each(ChiliBook.Toolbar.Command, function(key, obj){ // TODO: need some improvements here
			$('<a href="javascript:;"><span>'+obj.Label+'</span></a>')
				.click(function(){ obj.Cmd(self); })
				.appendTo($tools);
		});
		
		$this.before($bar.append($tools));
		$this.data("chili.text", $this.text());
	});

	// Overflow IE bug fix 
	if($.browser.msie && ChiliBook.Toolbar.Utils.IEOverflowFix)
	{
		this.bind("chili.after_coloring", function(){
			if(this.scrollWidth > this.offsetWidth) {
				  $(this).css({"padding-bottom": ChiliBook.Toolbar.Utils.IEOverflowFix, "overflow-y": "hidden"});
			}
		});
	}

	// Apply Chili
	ChiliBook.Toolbar.Chili.apply(this, [options]);
};

}) (jQuery);