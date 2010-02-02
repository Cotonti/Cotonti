function popup(code, w, h){
    window.open('plug.php?o=' + code, '', 'toolbar=0,location=0,directories=0,menuBar=0,resizable=0,scrollbars=yes,width=' + w + ',height=' + h + ',left=32,top=16');
}

function pfs(id, c1, c2){
    window.open('pfs.php?userid=' + id + '&c1=' + c1 + '&c2=' + c2, 'PFS', 'status=1, toolbar=0,location=0,directories=0,menuBar=0,resizable=1,scrollbars=yes,width=754,height=512,left=32,top=16');
}

function help(rcode, c1, c2){
    // deprecated
}

function picture(url, sx, sy){
    window.open('pfs.php?m=view&v=' + url, 'Picture', 'toolbar=0,location=0,directories=0,menuBar=0,resizable=1,scrollbars=yes,width=' + sx + ',height=' + sy + ',left=0,top=0');
}

function redirect(url){
    location.href = url.options[url.selectedIndex].value;
}

function toggleblock(id){
    var bl = document.getElementById(id);
    if (bl.style.display == 'none') {
        bl.style.display = '';
    }
    else {
        bl.style.display = 'none';
    }
}

// Inserts text into textarea at cursor position
function insertText(docObj, formName, fieldName, value) {
	var field = null;
	if(!docObj)
		docObj = document;
	// Find the field in the docObj
	for(var i = 0; i < docObj.forms.length; i++) {
		if(docObj.forms[i].name == formName) {
			for(var j = 0; j < docObj.forms[i].elements.length; j++) {
				if(docObj.forms[i].elements[j].name == fieldName) {
					field = docObj.forms[i].elements[j];
					break;
				}
			}
			break;
		}
	}
	if(!field)
		return false;
	// Insert the text
	if (docObj.selection) {
		// MSIE and Opera
		field.focus();
		var sel = docObj.selection.createRange();
		sel.text = value;
	} else if (field.selectionStart || field.selectionStart == 0) {
		// Mozilla
		var startPos = field.selectionStart;
		var endPos = field.selectionEnd;
		field.value = field.value.substring(0, startPos) + value + field.value.substring(endPos, field.value.length);
	} else {
		field.value += value;
	}
	return true;
}

// Array of ajax error handlers
// Example of use:
// ajaxErrorHandlers.push({divId: 'ajaxBlock', func: myErrorHandler});
var ajaxErrorHandlers = new Array();
// AJAX enablement defaults to false
var ajaxEnabled = false;
// Required to calculate paths
var ajaxCurrentBase = location.href.replace($('base').eq(0).attr('href'), '').replace(/\?.*$/, '').replace(/#.*$/, '');
// This flag indicates that AJAX+history has been used on this page
// It means that "#" or home link should be loaded via ajax too
var ajaxUsed = false;
// Global flag to let everybody know that AJAX has failed
var ajaxError = false;

// AJAX helper function
function ajaxSend(settings) {
	var method = settings.method || 'GET';
	var data = settings.data || '';
	var url = settings.url || $('#' + settings.formId).attr('action');
	if(method == 'POST') {
		data += '&' + $('#' + settings.formId).serialize();
	}
	$.ajax({
		type: method,
		url: url,
		data: data,
		beforeSend: function() {
			$('#' + settings.divId).append('<span style="position:relative;left:' + ($('#' + settings.divId).width()/2 - 16) + 'px;top:-' + ($('#' + settings.divId).height()/2 - 16) + 'px;" class="loading" id="loading"><img src="images/spinner_bigger.gif" alt="loading"/></span>');
		},
		success: function(msg) {
			$('#loading').remove();
			$('#' + settings.divId).html(msg).hide().stop().fadeIn('slow');
		},
		error: function(msg) {
			$('#loading').remove();
			if (ajaxErrorHandlers.length > 0) {
				for (var i = 0; i < ajaxErrorHandlers.length; i++) {
					if (ajaxErrorHandlers[i].divId == settings.divId)
						ajaxErrorHandlers[i].func(msg);
				}
			} else {
				alert('AJAX error: ' + msg);
				ajaxError = true;
			}
		}
	});
	return false;
}

// AJAX subpage loader with history support
function ajaxPageLoad(hash) {
	var m = hash.match(/^get(-.*?)?;(.*)$/);
	if (m) {
		// ajax bookmark
		var url = m[2].indexOf(';') > 0 ? m[2].replace(';', '?') : ajaxCurrentBase + '?' + m[2];
		ajaxUsed = true;
		return ajaxSend({
			method: 'GET',
			url: url,
			divId: m[1] ? m[1].substr(1) : 'ajaxBlock'
		});
	} else if (hash == '' && ajaxUsed) {
		// ajax home
		return ajaxSend ({
			url: location.href.replace(/#.*$/, ''),
			divId: 'ajaxBlock'
		});
	}
	return true;
}

// Constructs ajaxable hash string
function ajaxMakeHash(href, rel) {
	var hash = 'get';
	var hrefBase, params;
	var sep = '?';
	var m = rel.match(/get(-[^ ;]+)?(;\S*)?$/);
	if (m) {
		if (m[1]) {
			hash += m[1];
		}
		if (m[2]) {
			href = m[2].substr(1);
			sep  = ';';
		}
	}
	hash += ';'
	if (href.indexOf(sep) > 0) {
		hrefBase = href.substr(0, href.indexOf(sep));
		params = href.substr(href.indexOf(sep) + 1);
	} else {
		hrefBase = href;
		params = '';
	}
	hash += hrefBase == ajaxCurrentBase ? params : hrefBase + ';' + params;
	return hash;
}

// Standard event bindings
function bindHandlers() {
	if (location.hash == '#comments' || location.hash.match(/#c\d+/)) {
		$('.comments').css('display', '');
	}
	$('.comments_link').click(function() {
		if($('.comments').css('display') == 'none') {
			$('.comments').css('display', '');
		} else {
			$('.comments').css('display', 'none');
		}
	});

	if(location.href.match(/#comments/)) {
		$('.comments').css('display', '');
	}

	if (ajaxEnabled) {
		// AJAX auto-handling
		$('form.ajax').live('submit', function() {
			return ajaxSend({
				method: 'POST',
				formId: $(this).attr('id'),
				url: $(this).attr('action'),
				divId: $(this).attr('title') ? $(this).attr('title') : 'ajaxBlock'
			});
		});
		$('a.ajax').live('click', function() {
			$.historyLoad(ajaxMakeHash($(this).attr('href').replace(/#.*$/, ''), $(this).attr('rel')));
			return ajaxError;
		});
	}
}

$(document).ready(function() {
	bindHandlers();
	if (ajaxEnabled) {
		$.historyInit(ajaxPageLoad, location.hash);
	}
});

window.name = 'main';
