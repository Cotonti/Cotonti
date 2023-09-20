/**
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */


/**
 * Base Cotonti class
 */
class CotontiApplication
{
	/**
	 * Load data from /index.php?a=get
	 * Can be useful, for example when it is needed to load some dynamic content to cached page
	 *
	 * Example:
	 * cot.loadData(['captcha', 'x']).then(result => {
     *    console.log(result);
     * });
	 *
	 * @param what Array or string. Data to load. For example, ['captcha', 'x'] (x - anti XSS parameter value)
	 * @returns {Promise<{}>}
	 */
	async loadData(what) {
		if (!what) {
			return {};
		}

		if (this.loadedData === undefined) {
			this.loadedData = {};
		}

		let dataToLoad = [];

		if (typeof what === 'string' || what instanceof String) {
			what = [what];
		}

		for (let item of what) {
			if (!(item in this.loadedData)) {
				dataToLoad.push(item);
			}
		}

		if (dataToLoad.length > 0) {
			// @todo change to system controller when it will be implemented
			let params = new URLSearchParams({a: 'get',});

			dataToLoad.forEach((item, index, array) => {
				params.append('data[' + index + ']', item);
			});
			params.append('_ajax', 1);
			try {
				let response = await fetch('index.php?' + params.toString());

				if (response.ok) {
					const responseData = await response.json();
					if (responseData.success) {
						for (const key in responseData.data) {
							this.loadedData[key] = responseData.data[key];
						}
					}

				} else {
					// HTTP error
				}
			} catch (error) {
				// Request error
			}
		}

		let result = {};
		for (let item of what) {
			if ((item in this.loadedData)) {
				result[item] = this.loadedData[item];
			}
		}

		return result;
	}

	/**
	 * Load captcha via ajax. Used on cached pages.
	 */
	loadCaptcha() {
		this.loadData(['captcha', 'x']).then(result => {
			let captchaElements = document.querySelectorAll('.captcha-place-holder');
			for (let element of captchaElements) {
				element.innerHTML = result.captcha;
				this.executeScriptElements(element);
				element.classList.remove('captcha-place-holder', 'loading');
				element.classList.add('captcha');

				const form = element.closest('form');
				if (form !== null) {
					let inputX = form.querySelector('input[type="hidden"][name="x"]');
					if (inputX !== null) {
						inputX.setAttribute('value', result.x);
					}
				}
			}
		});
	}

	/**
	 * If you append <script> tags to the elements of the finished DOM document, they will not be executed automatically.
	 * The method executes <script> scripts nested in the specified element
	 * @param containerElement Node
	 */
	executeScriptElements(containerElement) {
		const scriptElements = containerElement.querySelectorAll('script');

		Array.from(scriptElements).forEach((scriptElement) => {
			const clonedElement = document.createElement('script');
			Array.from(scriptElement.attributes).forEach((attribute) => {
				clonedElement.setAttribute(attribute.name, attribute.value);
			});
			clonedElement.text = scriptElement.text;
			scriptElement.parentNode.replaceChild(clonedElement, scriptElement);
		});
	}
}

let cot = new CotontiApplication();


function encodeURIfix(str) {
	// to prevent twice encoding
	// and fix '[',']' signs to follow RFC3986 (section-3.2.2)
	return encodeURI(decodeURI(str)).replace(/%5B/g, '[').replace(/%5D/g, ']');
}

function getBaseHref() {
	var href = document.getElementsByTagName('base')[0].href;
	if (href == null) {
		return '/';
	} else {
		return href;
	}
}

function popup(code, w, h){
    window.open(getBaseHref() + 'index.php?o=' + code, '', 'toolbar=0,location=0,directories=0,menuBar=0,resizable=0,scrollbars=yes,width=' + w + ',height=' + h + ',left=32,top=16');
}

function pfs(id, c1, c2, parser){
    window.open(getBaseHref() + 'index.php?e=pfs&userid=' + id + '&c1=' + c1 + '&c2=' + c2 + '&parser=' + parser, 'PFS', 'status=1, toolbar=0,location=0,directories=0,menuBar=0,resizable=1,scrollbars=yes,width=754,height=512,left=32,top=16');
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
	return false;
}

function toggleAll(action) {
	var blks = document.querySelectorAll('[id^="blk_"]');
	for (i = 0; i < blks.length; i++) {
		if (action == 'hide') {
			blks[i].style.display = 'none';
		} else if (action == 'show') {
			blks[i].style.display = 'table-row-group';
		}
	}
	return false;
}

// Inserts text into textarea at cursor position
function insertText(docObj, fieldName, value) {
	var field = null;
	if(!docObj)
		docObj = document;
	// Find the field in the docObj
	findField:
	for(var i = 0; i < docObj.forms.length; i++) {
		for(var j = 0; j < docObj.forms[i].elements.length; j++) {
			if(docObj.forms[i].elements[j].name == fieldName) {
				field = docObj.forms[i].elements[j];
				break findField;
			}
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
// ajaxErrorHandlers.push({func: myErrorHandler});
// ajaxSuccessHandlers.push({func: mySuccessHandler});
var ajaxErrorHandlers = new Array();
var ajaxSuccessHandlers = new Array();
// AJAX enablement defaults to false
var ajaxEnabled = false;
// Required to calculate paths
if (typeof jQuery != 'undefined') {
	var ajaxCurrentBase = location.href.replace($('base').eq(0).attr('href'), '').replace(/\?.*$/, '').replace(/#.*$/, '');
}
// This flag indicates that AJAX+history has been used on this page
// It means that "#" or home link should be loaded via ajax too
var ajaxUsed = false;
// Global flag to let everybody know that AJAX has failed
var ajaxError = false;

/**
 * AJAX helper function
 * @param {hash} settings A hashtable with settings
 * @return FALSE on successful AJAX call, TRUE on error to continue in
 * synchronous mode
 * @type bool
 */
function ajaxSend(settings) {
	var method = settings.method ? settings.method.toUpperCase() : 'GET';
	var data = settings.data || '';
	var url = settings.url || $('#' + settings.formId).attr('action');
	if (method == 'POST') {
		data += '&' + $('#' + settings.formId).serialize();
	} else if (settings.formId) {
		var sep = url.indexOf('?') > 0 ? '&' : '?';
		url += sep + $('#' + settings.formId).serialize();
	}
	$.ajax({
		type: method,
		url: encodeURIfix(url),
		data: data,
		beforeSend: function() {
			if (!settings.nonshowloading) $('#' + settings.divId).append('<span style="position:absolute; left:' + ($('#' + settings.divId).width()/2 - 110) + 'px;top:' + ($('#' + settings.divId).height()/2 - 9) + 'px;" class="loading" id="loading"><img src="./images/spinner.gif" alt="loading"/></span>').css('position', 'relative');
		},
		success: function(msg) {
			if (!settings.nonshowloading) $('#loading').remove();
			if (!settings.nonshowfadein)
				$('#' + settings.divId).hide().html(msg).fadeIn(500);
			else
				$('#' + settings.divId).html(msg);
			for (var i = 0; i < ajaxSuccessHandlers.length; i++) {
				if(ajaxSuccessHandlers[i].func)
					ajaxSuccessHandlers[i].func(msg);
				else
					ajaxSuccessHandlers[i](msg);
			}
		},
		error: function(msg) {
			if (!settings.nonshowloading) $('#loading').remove();
			if (!settings.nonshowfadein)
				$('#' + settings.divId).hide().html(msg).fadeIn(500);
			else
				$('#' + settings.divId).html(msg);
			if (ajaxErrorHandlers.length > 0) {
				for (var i = 0; i < ajaxErrorHandlers.length; i++) {
					if (ajaxErrorHandlers[i].func)
						ajaxErrorHandlers[i].func(msg);
					else
						ajaxErrorHandlers[i](msg);
				}
			} else {
				alert('AJAX error: ' + msg);
				ajaxError = true;
			}
		}
	});
	return false;
}

/**
 * AJAX subpage loader with history support
 * @param {string} hash A hash-address string
 * @return FALSE on successful AJAX call, TRUE on error to continue in
 * synchronous mode
 * @type bool
 */
function ajaxPageLoad(hash) {
    if(hash != '') hash.replace(/^#/, '');
	var m = hash.match(/^get(-.*?)?;(.*)$/);
	if (m) {
		// ajax bookmark
        var url = m[2].indexOf(';') > 0 ? m[2].replace(';', '?') : ajaxCurrentBase + '?' + decodeURIComponent(m[2]);
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

/**
 * AJAX subform loader without history tracking
 * @param {string} hash A hash-address string
 * @param {string} formId Target form id attribute
 * @return FALSE on successful AJAX call, TRUE on error to continue in
 * synchronous mode
 * @type bool
 */
function ajaxFormLoad(hash, formId) {
	var m = hash.match(/^(get|post)(-.*?)?;(.*)$/);
	if (m) {
		// ajax bookmark
		var url = m[3].indexOf(';') > 0 ? m[3].replace(';', '?') : ajaxCurrentBase + '?' + m[3];
		ajaxUsed = true;
		return ajaxSend({
			method: m[1].toUpperCase(),
			url: url,
			divId: m[2] ? m[2].substr(1) : 'ajaxBlock',
			formId: formId
		});
	}
	return true;
}

/**
 * Constructs ajaxable hash string
 * @param {string} href Link href or form action attribute
 * @param {string} rel An attribute value possibly containing a hash address
 * @param {string} formData Is passed for forms only, is 'post' for POST forms
 * or serialized form data for GET forms
 * @return A valid hash-address string
 * @type string
 */
function ajaxMakeHash(href, rel, formData) {
	var hash = (formData == 'post') ? 'post' : 'get';
	var hrefBase, params;
	var sep = '?';
	var m = rel ? rel.match(/(get|post)(-[^ ;]+)?(;\S*)?/) : false;
	if (m) {
		hash = m[1];
		if (m[2]) {
			hash += m[2];
		}
		if (m[3]) {
			href = m[3].substr(1);
			sep  = ';';
		}
	}
	hash += ';'
	if (href.indexOf(sep) > 0) {
		hrefBase = href.substr(0, href.indexOf(sep));
		params = href.substr(href.indexOf(sep) + 1);
		if (formData && formData != 'post') {
			params += '&' + formData;
		}
	} else {
		hrefBase = href;
		params = '';
		if (formData && formData != 'post') {
			params += sep + formData;
		}
	}
	hash += hrefBase == ajaxCurrentBase ? params : hrefBase + ';' + params;
	return hash;
}

/**
 * Standard event bindings
 */
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
		$('body').on('submit', 'form.ajax', function() {
			if ($(this).attr('method').toUpperCase() == 'POST') {
				ajaxFormLoad(ajaxMakeHash($(this).attr('action').replace(/#.*$/, ''), $(this).attr('class'), 'post'), $(this).attr('id'));
			} else {
				window.location.hash = ajaxMakeHash($(this).attr('action').replace(/#.*$/, ''), $(this).attr('class'), $(this).serialize());
			}
			return ajaxError;
		});
		$('body').on('click', 'a.ajax', function() {
			window.location.hash = ajaxMakeHash($(this).attr('href').replace(/#.*$/, ''), $(this).attr('rel'));
			return ajaxError;
		});

		// AJAX action confirmations
		$('body').on('click', 'a.confirmLink', function() {
			if ($(this).attr('href').match(/message.+920/i)) {
				if ($('#confirmBox')) {
					$('#confirmBox').remove();
				}
				$('body').prepend('<div id="confirmBox" class="jqmWindow"></div>');
				$('#confirmBox').jqm({ajax:$(this).attr('href'),modal:true,onLoad:function(){
					$('#confirmBox').css('margin-left', '-'+($('#confirmBox').width()/2)+'px');
					$('#confirmBox').css('margin-top', '-'+($('#confirmBox').height()/2)+'px');
				}});
				$('#confirmBox').jqmShow();
				return false;
			} else {
				return true;
			}
		});

		// Listen to hash change events
		$(window).on('hashchange', function() {
			ajaxPageLoad(window.location.hash.replace(/^#/, ''));
		});

		$('body').on('click', 'a#confirmNo', function() {
			if ($("#confirmBox").is(".jqmWindow"))
			{
				$('#confirmBox').jqmHide();
				$('#confirmBox').remove();
				return false;
			}
			else
			{
				return true;
			}
		});
	}
}

if (typeof jQuery != 'undefined') {
    $(document).ready(function() {
        // If page was loaded with hash
        if (ajaxEnabled) {
            if(window.location.hash != '') {
                ajaxPageLoad(window.location.hash.replace(/^#/, ''));
            }
        }

        bindHandlers();
    });
}

window.name = 'main';