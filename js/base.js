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

/**
 * @todo move to pfs module
 */
function pfs(id, c1, c2, parser){
    window.open(
		getBaseHref() + 'index.php?e=pfs&userid=' + id + '&c1=' + c1 + '&c2=' + c2 + '&parser=' + parser,
		'PFS',
		'status=1, toolbar=0,location=0,directories=0,menuBar=0,resizable=1,scrollbars=yes,width=754,height=512,left=32,top=16'
	);
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
	if (!docObj) {
		docObj = document;
	}
	// Find the field in the docObj
	findField:
	for (var i = 0; i < docObj.forms.length; i++) {
		for (var j = 0; j < docObj.forms[i].elements.length; j++) {
			if (docObj.forms[i].elements[j].name == fieldName) {
				field = docObj.forms[i].elements[j];
				break findField;
			}
		}
	}
	if (!field) {
		return false;
	}

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
	if (method === 'POST') {
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
			if (!settings.nonshowloading) {
				$('#' + settings.divId)
					.append('<span style="position:absolute; left:' + ($('#' + settings.divId).width()/2 - 110) + 'px;top:' + ($('#' + settings.divId).height()/2 - 9) + 'px;" class="loading" id="loading"><img src="./images/spinner.gif" alt="loading"/></span>').css('position', 'relative');
			}
		},
		success: function(msg) {
			if (!settings.nonshowloading) {
				$('#loading').remove();
			}
			if (!settings.nonshowfadein) {
				$('#' + settings.divId).hide().html(msg).fadeIn(500);
			} else {
				$('#' + settings.divId).html(msg);
			}
			for (var i = 0; i < ajaxSuccessHandlers.length; i++) {
				if(ajaxSuccessHandlers[i].func)
					ajaxSuccessHandlers[i].func(msg);
				else
					ajaxSuccessHandlers[i](msg);
			}
		},
		error: function(msg) {
			if (!settings.nonshowloading) {
				$('#loading').remove();
			}
			if (!settings.nonshowfadein) {
				$('#' + settings.divId).hide().html(msg).fadeIn(500);
			} else {
				$('#' + settings.divId).html(msg);
			}
			if (ajaxErrorHandlers.length > 0) {
				for (var i = 0; i < ajaxErrorHandlers.length; i++) {
					if (ajaxErrorHandlers[i].func) {
						ajaxErrorHandlers[i].func(msg);
					} else {
						ajaxErrorHandlers[i](msg);
					}
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
    if (hash !== '') {
		hash.replace(/^#/, '');
	}
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
	} else if (hash === '' && ajaxUsed) {
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIl9oZWFkZXIuanMiLCJDb3RvbnRpQXBwbGljYXRpb24uanMiLCJiYXNlLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ0xBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUN0SEE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJmaWxlIjoiYmFzZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qKlxyXG4gKiBAcGFja2FnZSBDb3RvbnRpXHJcbiAqIEBjb3B5cmlnaHQgKGMpIENvdG9udGkgVGVhbVxyXG4gKiBAbGljZW5zZSBodHRwczovL2dpdGh1Yi5jb20vQ290b250aS9Db3RvbnRpL2Jsb2IvbWFzdGVyL0xpY2Vuc2UudHh0XHJcbiAqL1xyXG4iLCIvKipcclxuICogQmFzZSBDb3RvbnRpIGNsYXNzXHJcbiAqL1xyXG5jbGFzcyBDb3RvbnRpQXBwbGljYXRpb25cclxue1xyXG4gICAgLyoqXHJcbiAgICAgKiBMb2FkIGRhdGEgZnJvbSAvaW5kZXgucGhwP2E9Z2V0XHJcbiAgICAgKiBDYW4gYmUgdXNlZnVsLCBmb3IgZXhhbXBsZSB3aGVuIGl0IGlzIG5lZWRlZCB0byBsb2FkIHNvbWUgZHluYW1pYyBjb250ZW50IHRvIGNhY2hlZCBwYWdlXHJcbiAgICAgKlxyXG4gICAgICogRXhhbXBsZTpcclxuICAgICAqIGNvdC5sb2FkRGF0YShbJ2NhcHRjaGEnLCAneCddKS50aGVuKHJlc3VsdCA9PiB7XHJcbiAgICAgKiAgICBjb25zb2xlLmxvZyhyZXN1bHQpO1xyXG4gICAgICogfSk7XHJcbiAgICAgKlxyXG4gICAgICogQHBhcmFtIHdoYXQgQXJyYXkgb3Igc3RyaW5nLiBEYXRhIHRvIGxvYWQuIEZvciBleGFtcGxlLCBbJ2NhcHRjaGEnLCAneCddICh4IC0gYW50aSBYU1MgcGFyYW1ldGVyIHZhbHVlKVxyXG4gICAgICogQHJldHVybnMge1Byb21pc2U8e30+fVxyXG4gICAgICovXHJcbiAgICBhc3luYyBsb2FkRGF0YSh3aGF0KSB7XHJcbiAgICAgICAgaWYgKCF3aGF0KSB7XHJcbiAgICAgICAgICAgIHJldHVybiB7fTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGlmICh0aGlzLmxvYWRlZERhdGEgPT09IHVuZGVmaW5lZCkge1xyXG4gICAgICAgICAgICB0aGlzLmxvYWRlZERhdGEgPSB7fTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGxldCBkYXRhVG9Mb2FkID0gW107XHJcblxyXG4gICAgICAgIGlmICh0eXBlb2Ygd2hhdCA9PT0gJ3N0cmluZycgfHwgd2hhdCBpbnN0YW5jZW9mIFN0cmluZykge1xyXG4gICAgICAgICAgICB3aGF0ID0gW3doYXRdO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgZm9yIChsZXQgaXRlbSBvZiB3aGF0KSB7XHJcbiAgICAgICAgICAgIGlmICghKGl0ZW0gaW4gdGhpcy5sb2FkZWREYXRhKSkge1xyXG4gICAgICAgICAgICAgICAgZGF0YVRvTG9hZC5wdXNoKGl0ZW0pO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBpZiAoZGF0YVRvTG9hZC5sZW5ndGggPiAwKSB7XHJcbiAgICAgICAgICAgIC8vIEB0b2RvIGNoYW5nZSB0byBzeXN0ZW0gY29udHJvbGxlciB3aGVuIGl0IHdpbGwgYmUgaW1wbGVtZW50ZWRcclxuICAgICAgICAgICAgbGV0IHBhcmFtcyA9IG5ldyBVUkxTZWFyY2hQYXJhbXMoe2E6ICdnZXQnLH0pO1xyXG5cclxuICAgICAgICAgICAgZGF0YVRvTG9hZC5mb3JFYWNoKChpdGVtLCBpbmRleCwgYXJyYXkpID0+IHtcclxuICAgICAgICAgICAgICAgIHBhcmFtcy5hcHBlbmQoJ2RhdGFbJyArIGluZGV4ICsgJ10nLCBpdGVtKTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgIHBhcmFtcy5hcHBlbmQoJ19hamF4JywgMSk7XHJcbiAgICAgICAgICAgIHRyeSB7XHJcbiAgICAgICAgICAgICAgICBsZXQgcmVzcG9uc2UgPSBhd2FpdCBmZXRjaCgnaW5kZXgucGhwPycgKyBwYXJhbXMudG9TdHJpbmcoKSk7XHJcblxyXG4gICAgICAgICAgICAgICAgaWYgKHJlc3BvbnNlLm9rKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgcmVzcG9uc2VEYXRhID0gYXdhaXQgcmVzcG9uc2UuanNvbigpO1xyXG4gICAgICAgICAgICAgICAgICAgIGlmIChyZXNwb25zZURhdGEuc3VjY2Vzcykge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBmb3IgKGNvbnN0IGtleSBpbiByZXNwb25zZURhdGEuZGF0YSkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5sb2FkZWREYXRhW2tleV0gPSByZXNwb25zZURhdGEuZGF0YVtrZXldO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICAgICAgLy8gSFRUUCBlcnJvclxyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9IGNhdGNoIChlcnJvcikge1xyXG4gICAgICAgICAgICAgICAgLy8gUmVxdWVzdCBlcnJvclxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBsZXQgcmVzdWx0ID0ge307XHJcbiAgICAgICAgZm9yIChsZXQgaXRlbSBvZiB3aGF0KSB7XHJcbiAgICAgICAgICAgIGlmICgoaXRlbSBpbiB0aGlzLmxvYWRlZERhdGEpKSB7XHJcbiAgICAgICAgICAgICAgICByZXN1bHRbaXRlbV0gPSB0aGlzLmxvYWRlZERhdGFbaXRlbV07XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIHJldHVybiByZXN1bHQ7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBMb2FkIGNhcHRjaGEgdmlhIGFqYXguIFVzZWQgb24gY2FjaGVkIHBhZ2VzLlxyXG4gICAgICovXHJcbiAgICBsb2FkQ2FwdGNoYSgpIHtcclxuICAgICAgICB0aGlzLmxvYWREYXRhKFsnY2FwdGNoYScsICd4J10pLnRoZW4ocmVzdWx0ID0+IHtcclxuICAgICAgICAgICAgbGV0IGNhcHRjaGFFbGVtZW50cyA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJy5jYXB0Y2hhLXBsYWNlLWhvbGRlcicpO1xyXG4gICAgICAgICAgICBmb3IgKGxldCBlbGVtZW50IG9mIGNhcHRjaGFFbGVtZW50cykge1xyXG4gICAgICAgICAgICAgICAgZWxlbWVudC5pbm5lckhUTUwgPSByZXN1bHQuY2FwdGNoYTtcclxuICAgICAgICAgICAgICAgIHRoaXMuZXhlY3V0ZVNjcmlwdEVsZW1lbnRzKGVsZW1lbnQpO1xyXG4gICAgICAgICAgICAgICAgZWxlbWVudC5jbGFzc0xpc3QucmVtb3ZlKCdjYXB0Y2hhLXBsYWNlLWhvbGRlcicsICdsb2FkaW5nJyk7XHJcbiAgICAgICAgICAgICAgICBlbGVtZW50LmNsYXNzTGlzdC5hZGQoJ2NhcHRjaGEnKTtcclxuXHJcbiAgICAgICAgICAgICAgICBjb25zdCBmb3JtID0gZWxlbWVudC5jbG9zZXN0KCdmb3JtJyk7XHJcbiAgICAgICAgICAgICAgICBpZiAoZm9ybSAhPT0gbnVsbCkge1xyXG4gICAgICAgICAgICAgICAgICAgIGxldCBpbnB1dFggPSBmb3JtLnF1ZXJ5U2VsZWN0b3IoJ2lucHV0W3R5cGU9XCJoaWRkZW5cIl1bbmFtZT1cInhcIl0nKTtcclxuICAgICAgICAgICAgICAgICAgICBpZiAoaW5wdXRYICE9PSBudWxsKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGlucHV0WC5zZXRBdHRyaWJ1dGUoJ3ZhbHVlJywgcmVzdWx0LngpO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogSWYgeW91IGFwcGVuZCA8c2NyaXB0PiB0YWdzIHRvIHRoZSBlbGVtZW50cyBvZiB0aGUgZmluaXNoZWQgRE9NIGRvY3VtZW50LCB0aGV5IHdpbGwgbm90IGJlIGV4ZWN1dGVkIGF1dG9tYXRpY2FsbHkuXHJcbiAgICAgKiBUaGUgbWV0aG9kIGV4ZWN1dGVzIDxzY3JpcHQ+IHNjcmlwdHMgbmVzdGVkIGluIHRoZSBzcGVjaWZpZWQgZWxlbWVudFxyXG4gICAgICogQHBhcmFtIGNvbnRhaW5lckVsZW1lbnQgTm9kZVxyXG4gICAgICovXHJcbiAgICBleGVjdXRlU2NyaXB0RWxlbWVudHMoY29udGFpbmVyRWxlbWVudCkge1xyXG4gICAgICAgIGNvbnN0IHNjcmlwdEVsZW1lbnRzID0gY29udGFpbmVyRWxlbWVudC5xdWVyeVNlbGVjdG9yQWxsKCdzY3JpcHQnKTtcclxuXHJcbiAgICAgICAgQXJyYXkuZnJvbShzY3JpcHRFbGVtZW50cykuZm9yRWFjaCgoc2NyaXB0RWxlbWVudCkgPT4ge1xyXG4gICAgICAgICAgICBjb25zdCBjbG9uZWRFbGVtZW50ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnc2NyaXB0Jyk7XHJcbiAgICAgICAgICAgIEFycmF5LmZyb20oc2NyaXB0RWxlbWVudC5hdHRyaWJ1dGVzKS5mb3JFYWNoKChhdHRyaWJ1dGUpID0+IHtcclxuICAgICAgICAgICAgICAgIGNsb25lZEVsZW1lbnQuc2V0QXR0cmlidXRlKGF0dHJpYnV0ZS5uYW1lLCBhdHRyaWJ1dGUudmFsdWUpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgY2xvbmVkRWxlbWVudC50ZXh0ID0gc2NyaXB0RWxlbWVudC50ZXh0O1xyXG4gICAgICAgICAgICBzY3JpcHRFbGVtZW50LnBhcmVudE5vZGUucmVwbGFjZUNoaWxkKGNsb25lZEVsZW1lbnQsIHNjcmlwdEVsZW1lbnQpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgfVxyXG59XHJcblxyXG5sZXQgY290ID0gbmV3IENvdG9udGlBcHBsaWNhdGlvbigpO1xyXG4iLCJmdW5jdGlvbiBlbmNvZGVVUklmaXgoc3RyKSB7XHJcblx0Ly8gdG8gcHJldmVudCB0d2ljZSBlbmNvZGluZ1xyXG5cdC8vIGFuZCBmaXggJ1snLCddJyBzaWducyB0byBmb2xsb3cgUkZDMzk4NiAoc2VjdGlvbi0zLjIuMilcclxuXHRyZXR1cm4gZW5jb2RlVVJJKGRlY29kZVVSSShzdHIpKS5yZXBsYWNlKC8lNUIvZywgJ1snKS5yZXBsYWNlKC8lNUQvZywgJ10nKTtcclxufVxyXG5cclxuZnVuY3Rpb24gZ2V0QmFzZUhyZWYoKSB7XHJcblx0dmFyIGhyZWYgPSBkb2N1bWVudC5nZXRFbGVtZW50c0J5VGFnTmFtZSgnYmFzZScpWzBdLmhyZWY7XHJcblx0aWYgKGhyZWYgPT0gbnVsbCkge1xyXG5cdFx0cmV0dXJuICcvJztcclxuXHR9IGVsc2Uge1xyXG5cdFx0cmV0dXJuIGhyZWY7XHJcblx0fVxyXG59XHJcblxyXG5mdW5jdGlvbiBwb3B1cChjb2RlLCB3LCBoKXtcclxuICAgIHdpbmRvdy5vcGVuKGdldEJhc2VIcmVmKCkgKyAnaW5kZXgucGhwP289JyArIGNvZGUsICcnLCAndG9vbGJhcj0wLGxvY2F0aW9uPTAsZGlyZWN0b3JpZXM9MCxtZW51QmFyPTAscmVzaXphYmxlPTAsc2Nyb2xsYmFycz15ZXMsd2lkdGg9JyArIHcgKyAnLGhlaWdodD0nICsgaCArICcsbGVmdD0zMix0b3A9MTYnKTtcclxufVxyXG5cclxuLyoqXHJcbiAqIEB0b2RvIG1vdmUgdG8gcGZzIG1vZHVsZVxyXG4gKi9cclxuZnVuY3Rpb24gcGZzKGlkLCBjMSwgYzIsIHBhcnNlcil7XHJcbiAgICB3aW5kb3cub3BlbihcclxuXHRcdGdldEJhc2VIcmVmKCkgKyAnaW5kZXgucGhwP2U9cGZzJnVzZXJpZD0nICsgaWQgKyAnJmMxPScgKyBjMSArICcmYzI9JyArIGMyICsgJyZwYXJzZXI9JyArIHBhcnNlcixcclxuXHRcdCdQRlMnLFxyXG5cdFx0J3N0YXR1cz0xLCB0b29sYmFyPTAsbG9jYXRpb249MCxkaXJlY3Rvcmllcz0wLG1lbnVCYXI9MCxyZXNpemFibGU9MSxzY3JvbGxiYXJzPXllcyx3aWR0aD03NTQsaGVpZ2h0PTUxMixsZWZ0PTMyLHRvcD0xNidcclxuXHQpO1xyXG59XHJcblxyXG5mdW5jdGlvbiByZWRpcmVjdCh1cmwpe1xyXG4gICAgbG9jYXRpb24uaHJlZiA9IHVybC5vcHRpb25zW3VybC5zZWxlY3RlZEluZGV4XS52YWx1ZTtcclxufVxyXG5cclxuZnVuY3Rpb24gdG9nZ2xlYmxvY2soaWQpe1xyXG4gICAgdmFyIGJsID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoaWQpO1xyXG4gICAgaWYgKGJsLnN0eWxlLmRpc3BsYXkgPT0gJ25vbmUnKSB7XHJcbiAgICAgICAgYmwuc3R5bGUuZGlzcGxheSA9ICcnO1xyXG4gICAgfVxyXG4gICAgZWxzZSB7XHJcbiAgICAgICAgYmwuc3R5bGUuZGlzcGxheSA9ICdub25lJztcclxuICAgIH1cclxuXHRyZXR1cm4gZmFsc2U7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIHRvZ2dsZUFsbChhY3Rpb24pIHtcclxuXHR2YXIgYmxrcyA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJ1tpZF49XCJibGtfXCJdJyk7XHJcblx0Zm9yIChpID0gMDsgaSA8IGJsa3MubGVuZ3RoOyBpKyspIHtcclxuXHRcdGlmIChhY3Rpb24gPT0gJ2hpZGUnKSB7XHJcblx0XHRcdGJsa3NbaV0uc3R5bGUuZGlzcGxheSA9ICdub25lJztcclxuXHRcdH0gZWxzZSBpZiAoYWN0aW9uID09ICdzaG93Jykge1xyXG5cdFx0XHRibGtzW2ldLnN0eWxlLmRpc3BsYXkgPSAndGFibGUtcm93LWdyb3VwJztcclxuXHRcdH1cclxuXHR9XHJcblx0cmV0dXJuIGZhbHNlO1xyXG59XHJcblxyXG4vLyBJbnNlcnRzIHRleHQgaW50byB0ZXh0YXJlYSBhdCBjdXJzb3IgcG9zaXRpb25cclxuZnVuY3Rpb24gaW5zZXJ0VGV4dChkb2NPYmosIGZpZWxkTmFtZSwgdmFsdWUpIHtcclxuXHR2YXIgZmllbGQgPSBudWxsO1xyXG5cdGlmICghZG9jT2JqKSB7XHJcblx0XHRkb2NPYmogPSBkb2N1bWVudDtcclxuXHR9XHJcblx0Ly8gRmluZCB0aGUgZmllbGQgaW4gdGhlIGRvY09ialxyXG5cdGZpbmRGaWVsZDpcclxuXHRmb3IgKHZhciBpID0gMDsgaSA8IGRvY09iai5mb3Jtcy5sZW5ndGg7IGkrKykge1xyXG5cdFx0Zm9yICh2YXIgaiA9IDA7IGogPCBkb2NPYmouZm9ybXNbaV0uZWxlbWVudHMubGVuZ3RoOyBqKyspIHtcclxuXHRcdFx0aWYgKGRvY09iai5mb3Jtc1tpXS5lbGVtZW50c1tqXS5uYW1lID09IGZpZWxkTmFtZSkge1xyXG5cdFx0XHRcdGZpZWxkID0gZG9jT2JqLmZvcm1zW2ldLmVsZW1lbnRzW2pdO1xyXG5cdFx0XHRcdGJyZWFrIGZpbmRGaWVsZDtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cdH1cclxuXHRpZiAoIWZpZWxkKSB7XHJcblx0XHRyZXR1cm4gZmFsc2U7XHJcblx0fVxyXG5cclxuXHQvLyBJbnNlcnQgdGhlIHRleHRcclxuXHRpZiAoZG9jT2JqLnNlbGVjdGlvbikge1xyXG5cdFx0Ly8gTVNJRSBhbmQgT3BlcmFcclxuXHRcdGZpZWxkLmZvY3VzKCk7XHJcblx0XHR2YXIgc2VsID0gZG9jT2JqLnNlbGVjdGlvbi5jcmVhdGVSYW5nZSgpO1xyXG5cdFx0c2VsLnRleHQgPSB2YWx1ZTtcclxuXHR9IGVsc2UgaWYgKGZpZWxkLnNlbGVjdGlvblN0YXJ0IHx8IGZpZWxkLnNlbGVjdGlvblN0YXJ0ID09IDApIHtcclxuXHRcdC8vIE1vemlsbGFcclxuXHRcdHZhciBzdGFydFBvcyA9IGZpZWxkLnNlbGVjdGlvblN0YXJ0O1xyXG5cdFx0dmFyIGVuZFBvcyA9IGZpZWxkLnNlbGVjdGlvbkVuZDtcclxuXHRcdGZpZWxkLnZhbHVlID0gZmllbGQudmFsdWUuc3Vic3RyaW5nKDAsIHN0YXJ0UG9zKSArIHZhbHVlICsgZmllbGQudmFsdWUuc3Vic3RyaW5nKGVuZFBvcywgZmllbGQudmFsdWUubGVuZ3RoKTtcclxuXHR9IGVsc2Uge1xyXG5cdFx0ZmllbGQudmFsdWUgKz0gdmFsdWU7XHJcblx0fVxyXG5cdHJldHVybiB0cnVlO1xyXG59XHJcblxyXG4vLyBBcnJheSBvZiBhamF4IGVycm9yIGhhbmRsZXJzXHJcbi8vIEV4YW1wbGUgb2YgdXNlOlxyXG4vLyBhamF4RXJyb3JIYW5kbGVycy5wdXNoKHtmdW5jOiBteUVycm9ySGFuZGxlcn0pO1xyXG4vLyBhamF4U3VjY2Vzc0hhbmRsZXJzLnB1c2goe2Z1bmM6IG15U3VjY2Vzc0hhbmRsZXJ9KTtcclxudmFyIGFqYXhFcnJvckhhbmRsZXJzID0gbmV3IEFycmF5KCk7XHJcbnZhciBhamF4U3VjY2Vzc0hhbmRsZXJzID0gbmV3IEFycmF5KCk7XHJcbi8vIEFKQVggZW5hYmxlbWVudCBkZWZhdWx0cyB0byBmYWxzZVxyXG52YXIgYWpheEVuYWJsZWQgPSBmYWxzZTtcclxuLy8gUmVxdWlyZWQgdG8gY2FsY3VsYXRlIHBhdGhzXHJcbmlmICh0eXBlb2YgalF1ZXJ5ICE9ICd1bmRlZmluZWQnKSB7XHJcblx0dmFyIGFqYXhDdXJyZW50QmFzZSA9IGxvY2F0aW9uLmhyZWYucmVwbGFjZSgkKCdiYXNlJykuZXEoMCkuYXR0cignaHJlZicpLCAnJykucmVwbGFjZSgvXFw/LiokLywgJycpLnJlcGxhY2UoLyMuKiQvLCAnJyk7XHJcbn1cclxuLy8gVGhpcyBmbGFnIGluZGljYXRlcyB0aGF0IEFKQVgraGlzdG9yeSBoYXMgYmVlbiB1c2VkIG9uIHRoaXMgcGFnZVxyXG4vLyBJdCBtZWFucyB0aGF0IFwiI1wiIG9yIGhvbWUgbGluayBzaG91bGQgYmUgbG9hZGVkIHZpYSBhamF4IHRvb1xyXG52YXIgYWpheFVzZWQgPSBmYWxzZTtcclxuLy8gR2xvYmFsIGZsYWcgdG8gbGV0IGV2ZXJ5Ym9keSBrbm93IHRoYXQgQUpBWCBoYXMgZmFpbGVkXHJcbnZhciBhamF4RXJyb3IgPSBmYWxzZTtcclxuXHJcbi8qKlxyXG4gKiBBSkFYIGhlbHBlciBmdW5jdGlvblxyXG4gKiBAcGFyYW0ge2hhc2h9IHNldHRpbmdzIEEgaGFzaHRhYmxlIHdpdGggc2V0dGluZ3NcclxuICogQHJldHVybiBGQUxTRSBvbiBzdWNjZXNzZnVsIEFKQVggY2FsbCwgVFJVRSBvbiBlcnJvciB0byBjb250aW51ZSBpblxyXG4gKiBzeW5jaHJvbm91cyBtb2RlXHJcbiAqIEB0eXBlIGJvb2xcclxuICovXHJcbmZ1bmN0aW9uIGFqYXhTZW5kKHNldHRpbmdzKSB7XHJcblx0dmFyIG1ldGhvZCA9IHNldHRpbmdzLm1ldGhvZCA/IHNldHRpbmdzLm1ldGhvZC50b1VwcGVyQ2FzZSgpIDogJ0dFVCc7XHJcblx0dmFyIGRhdGEgPSBzZXR0aW5ncy5kYXRhIHx8ICcnO1xyXG5cdHZhciB1cmwgPSBzZXR0aW5ncy51cmwgfHwgJCgnIycgKyBzZXR0aW5ncy5mb3JtSWQpLmF0dHIoJ2FjdGlvbicpO1xyXG5cdGlmIChtZXRob2QgPT09ICdQT1NUJykge1xyXG5cdFx0ZGF0YSArPSAnJicgKyAkKCcjJyArIHNldHRpbmdzLmZvcm1JZCkuc2VyaWFsaXplKCk7XHJcblx0fSBlbHNlIGlmIChzZXR0aW5ncy5mb3JtSWQpIHtcclxuXHRcdHZhciBzZXAgPSB1cmwuaW5kZXhPZignPycpID4gMCA/ICcmJyA6ICc/JztcclxuXHRcdHVybCArPSBzZXAgKyAkKCcjJyArIHNldHRpbmdzLmZvcm1JZCkuc2VyaWFsaXplKCk7XHJcblx0fVxyXG5cdCQuYWpheCh7XHJcblx0XHR0eXBlOiBtZXRob2QsXHJcblx0XHR1cmw6IGVuY29kZVVSSWZpeCh1cmwpLFxyXG5cdFx0ZGF0YTogZGF0YSxcclxuXHRcdGJlZm9yZVNlbmQ6IGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRpZiAoIXNldHRpbmdzLm5vbnNob3dsb2FkaW5nKSB7XHJcblx0XHRcdFx0JCgnIycgKyBzZXR0aW5ncy5kaXZJZClcclxuXHRcdFx0XHRcdC5hcHBlbmQoJzxzcGFuIHN0eWxlPVwicG9zaXRpb246YWJzb2x1dGU7IGxlZnQ6JyArICgkKCcjJyArIHNldHRpbmdzLmRpdklkKS53aWR0aCgpLzIgLSAxMTApICsgJ3B4O3RvcDonICsgKCQoJyMnICsgc2V0dGluZ3MuZGl2SWQpLmhlaWdodCgpLzIgLSA5KSArICdweDtcIiBjbGFzcz1cImxvYWRpbmdcIiBpZD1cImxvYWRpbmdcIj48aW1nIHNyYz1cIi4vaW1hZ2VzL3NwaW5uZXIuZ2lmXCIgYWx0PVwibG9hZGluZ1wiLz48L3NwYW4+JykuY3NzKCdwb3NpdGlvbicsICdyZWxhdGl2ZScpO1xyXG5cdFx0XHR9XHJcblx0XHR9LFxyXG5cdFx0c3VjY2VzczogZnVuY3Rpb24obXNnKSB7XHJcblx0XHRcdGlmICghc2V0dGluZ3Mubm9uc2hvd2xvYWRpbmcpIHtcclxuXHRcdFx0XHQkKCcjbG9hZGluZycpLnJlbW92ZSgpO1xyXG5cdFx0XHR9XHJcblx0XHRcdGlmICghc2V0dGluZ3Mubm9uc2hvd2ZhZGVpbikge1xyXG5cdFx0XHRcdCQoJyMnICsgc2V0dGluZ3MuZGl2SWQpLmhpZGUoKS5odG1sKG1zZykuZmFkZUluKDUwMCk7XHJcblx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0JCgnIycgKyBzZXR0aW5ncy5kaXZJZCkuaHRtbChtc2cpO1xyXG5cdFx0XHR9XHJcblx0XHRcdGZvciAodmFyIGkgPSAwOyBpIDwgYWpheFN1Y2Nlc3NIYW5kbGVycy5sZW5ndGg7IGkrKykge1xyXG5cdFx0XHRcdGlmKGFqYXhTdWNjZXNzSGFuZGxlcnNbaV0uZnVuYylcclxuXHRcdFx0XHRcdGFqYXhTdWNjZXNzSGFuZGxlcnNbaV0uZnVuYyhtc2cpO1xyXG5cdFx0XHRcdGVsc2VcclxuXHRcdFx0XHRcdGFqYXhTdWNjZXNzSGFuZGxlcnNbaV0obXNnKTtcclxuXHRcdFx0fVxyXG5cdFx0fSxcclxuXHRcdGVycm9yOiBmdW5jdGlvbihtc2cpIHtcclxuXHRcdFx0aWYgKCFzZXR0aW5ncy5ub25zaG93bG9hZGluZykge1xyXG5cdFx0XHRcdCQoJyNsb2FkaW5nJykucmVtb3ZlKCk7XHJcblx0XHRcdH1cclxuXHRcdFx0aWYgKCFzZXR0aW5ncy5ub25zaG93ZmFkZWluKSB7XHJcblx0XHRcdFx0JCgnIycgKyBzZXR0aW5ncy5kaXZJZCkuaGlkZSgpLmh0bWwobXNnKS5mYWRlSW4oNTAwKTtcclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHQkKCcjJyArIHNldHRpbmdzLmRpdklkKS5odG1sKG1zZyk7XHJcblx0XHRcdH1cclxuXHRcdFx0aWYgKGFqYXhFcnJvckhhbmRsZXJzLmxlbmd0aCA+IDApIHtcclxuXHRcdFx0XHRmb3IgKHZhciBpID0gMDsgaSA8IGFqYXhFcnJvckhhbmRsZXJzLmxlbmd0aDsgaSsrKSB7XHJcblx0XHRcdFx0XHRpZiAoYWpheEVycm9ySGFuZGxlcnNbaV0uZnVuYykge1xyXG5cdFx0XHRcdFx0XHRhamF4RXJyb3JIYW5kbGVyc1tpXS5mdW5jKG1zZyk7XHJcblx0XHRcdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdFx0XHRhamF4RXJyb3JIYW5kbGVyc1tpXShtc2cpO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH1cclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRhbGVydCgnQUpBWCBlcnJvcjogJyArIG1zZyk7XHJcblx0XHRcdFx0YWpheEVycm9yID0gdHJ1ZTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cdH0pO1xyXG5cdHJldHVybiBmYWxzZTtcclxufVxyXG5cclxuLyoqXHJcbiAqIEFKQVggc3VicGFnZSBsb2FkZXIgd2l0aCBoaXN0b3J5IHN1cHBvcnRcclxuICogQHBhcmFtIHtzdHJpbmd9IGhhc2ggQSBoYXNoLWFkZHJlc3Mgc3RyaW5nXHJcbiAqIEByZXR1cm4gRkFMU0Ugb24gc3VjY2Vzc2Z1bCBBSkFYIGNhbGwsIFRSVUUgb24gZXJyb3IgdG8gY29udGludWUgaW5cclxuICogc3luY2hyb25vdXMgbW9kZVxyXG4gKiBAdHlwZSBib29sXHJcbiAqL1xyXG5mdW5jdGlvbiBhamF4UGFnZUxvYWQoaGFzaCkge1xyXG4gICAgaWYgKGhhc2ggIT09ICcnKSB7XHJcblx0XHRoYXNoLnJlcGxhY2UoL14jLywgJycpO1xyXG5cdH1cclxuXHR2YXIgbSA9IGhhc2gubWF0Y2goL15nZXQoLS4qPyk/OyguKikkLyk7XHJcblx0aWYgKG0pIHtcclxuXHRcdC8vIGFqYXggYm9va21hcmtcclxuICAgICAgICB2YXIgdXJsID0gbVsyXS5pbmRleE9mKCc7JykgPiAwID8gbVsyXS5yZXBsYWNlKCc7JywgJz8nKSA6IGFqYXhDdXJyZW50QmFzZSArICc/JyArIGRlY29kZVVSSUNvbXBvbmVudChtWzJdKTtcclxuXHRcdGFqYXhVc2VkID0gdHJ1ZTtcclxuXHRcdHJldHVybiBhamF4U2VuZCh7XHJcblx0XHRcdG1ldGhvZDogJ0dFVCcsXHJcblx0XHRcdHVybDogdXJsLFxyXG5cdFx0XHRkaXZJZDogbVsxXSA/IG1bMV0uc3Vic3RyKDEpIDogJ2FqYXhCbG9jaydcclxuXHRcdH0pO1xyXG5cdH0gZWxzZSBpZiAoaGFzaCA9PT0gJycgJiYgYWpheFVzZWQpIHtcclxuXHRcdC8vIGFqYXggaG9tZVxyXG5cdFx0cmV0dXJuIGFqYXhTZW5kICh7XHJcblx0XHRcdHVybDogbG9jYXRpb24uaHJlZi5yZXBsYWNlKC8jLiokLywgJycpLFxyXG5cdFx0XHRkaXZJZDogJ2FqYXhCbG9jaydcclxuXHRcdH0pO1xyXG5cdH1cclxuXHRyZXR1cm4gdHJ1ZTtcclxufVxyXG5cclxuLyoqXHJcbiAqIEFKQVggc3ViZm9ybSBsb2FkZXIgd2l0aG91dCBoaXN0b3J5IHRyYWNraW5nXHJcbiAqIEBwYXJhbSB7c3RyaW5nfSBoYXNoIEEgaGFzaC1hZGRyZXNzIHN0cmluZ1xyXG4gKiBAcGFyYW0ge3N0cmluZ30gZm9ybUlkIFRhcmdldCBmb3JtIGlkIGF0dHJpYnV0ZVxyXG4gKiBAcmV0dXJuIEZBTFNFIG9uIHN1Y2Nlc3NmdWwgQUpBWCBjYWxsLCBUUlVFIG9uIGVycm9yIHRvIGNvbnRpbnVlIGluXHJcbiAqIHN5bmNocm9ub3VzIG1vZGVcclxuICogQHR5cGUgYm9vbFxyXG4gKi9cclxuZnVuY3Rpb24gYWpheEZvcm1Mb2FkKGhhc2gsIGZvcm1JZCkge1xyXG5cdHZhciBtID0gaGFzaC5tYXRjaCgvXihnZXR8cG9zdCkoLS4qPyk/OyguKikkLyk7XHJcblx0aWYgKG0pIHtcclxuXHRcdC8vIGFqYXggYm9va21hcmtcclxuXHRcdHZhciB1cmwgPSBtWzNdLmluZGV4T2YoJzsnKSA+IDAgPyBtWzNdLnJlcGxhY2UoJzsnLCAnPycpIDogYWpheEN1cnJlbnRCYXNlICsgJz8nICsgbVszXTtcclxuXHRcdGFqYXhVc2VkID0gdHJ1ZTtcclxuXHRcdHJldHVybiBhamF4U2VuZCh7XHJcblx0XHRcdG1ldGhvZDogbVsxXS50b1VwcGVyQ2FzZSgpLFxyXG5cdFx0XHR1cmw6IHVybCxcclxuXHRcdFx0ZGl2SWQ6IG1bMl0gPyBtWzJdLnN1YnN0cigxKSA6ICdhamF4QmxvY2snLFxyXG5cdFx0XHRmb3JtSWQ6IGZvcm1JZFxyXG5cdFx0fSk7XHJcblx0fVxyXG5cdHJldHVybiB0cnVlO1xyXG59XHJcblxyXG4vKipcclxuICogQ29uc3RydWN0cyBhamF4YWJsZSBoYXNoIHN0cmluZ1xyXG4gKiBAcGFyYW0ge3N0cmluZ30gaHJlZiBMaW5rIGhyZWYgb3IgZm9ybSBhY3Rpb24gYXR0cmlidXRlXHJcbiAqIEBwYXJhbSB7c3RyaW5nfSByZWwgQW4gYXR0cmlidXRlIHZhbHVlIHBvc3NpYmx5IGNvbnRhaW5pbmcgYSBoYXNoIGFkZHJlc3NcclxuICogQHBhcmFtIHtzdHJpbmd9IGZvcm1EYXRhIElzIHBhc3NlZCBmb3IgZm9ybXMgb25seSwgaXMgJ3Bvc3QnIGZvciBQT1NUIGZvcm1zXHJcbiAqIG9yIHNlcmlhbGl6ZWQgZm9ybSBkYXRhIGZvciBHRVQgZm9ybXNcclxuICogQHJldHVybiBBIHZhbGlkIGhhc2gtYWRkcmVzcyBzdHJpbmdcclxuICogQHR5cGUgc3RyaW5nXHJcbiAqL1xyXG5mdW5jdGlvbiBhamF4TWFrZUhhc2goaHJlZiwgcmVsLCBmb3JtRGF0YSkge1xyXG5cdHZhciBoYXNoID0gKGZvcm1EYXRhID09ICdwb3N0JykgPyAncG9zdCcgOiAnZ2V0JztcclxuXHR2YXIgaHJlZkJhc2UsIHBhcmFtcztcclxuXHR2YXIgc2VwID0gJz8nO1xyXG5cdHZhciBtID0gcmVsID8gcmVsLm1hdGNoKC8oZ2V0fHBvc3QpKC1bXiA7XSspPyg7XFxTKik/LykgOiBmYWxzZTtcclxuXHRpZiAobSkge1xyXG5cdFx0aGFzaCA9IG1bMV07XHJcblx0XHRpZiAobVsyXSkge1xyXG5cdFx0XHRoYXNoICs9IG1bMl07XHJcblx0XHR9XHJcblx0XHRpZiAobVszXSkge1xyXG5cdFx0XHRocmVmID0gbVszXS5zdWJzdHIoMSk7XHJcblx0XHRcdHNlcCAgPSAnOyc7XHJcblx0XHR9XHJcblx0fVxyXG5cdGhhc2ggKz0gJzsnXHJcblx0aWYgKGhyZWYuaW5kZXhPZihzZXApID4gMCkge1xyXG5cdFx0aHJlZkJhc2UgPSBocmVmLnN1YnN0cigwLCBocmVmLmluZGV4T2Yoc2VwKSk7XHJcblx0XHRwYXJhbXMgPSBocmVmLnN1YnN0cihocmVmLmluZGV4T2Yoc2VwKSArIDEpO1xyXG5cdFx0aWYgKGZvcm1EYXRhICYmIGZvcm1EYXRhICE9ICdwb3N0Jykge1xyXG5cdFx0XHRwYXJhbXMgKz0gJyYnICsgZm9ybURhdGE7XHJcblx0XHR9XHJcblx0fSBlbHNlIHtcclxuXHRcdGhyZWZCYXNlID0gaHJlZjtcclxuXHRcdHBhcmFtcyA9ICcnO1xyXG5cdFx0aWYgKGZvcm1EYXRhICYmIGZvcm1EYXRhICE9ICdwb3N0Jykge1xyXG5cdFx0XHRwYXJhbXMgKz0gc2VwICsgZm9ybURhdGE7XHJcblx0XHR9XHJcblx0fVxyXG5cdGhhc2ggKz0gaHJlZkJhc2UgPT0gYWpheEN1cnJlbnRCYXNlID8gcGFyYW1zIDogaHJlZkJhc2UgKyAnOycgKyBwYXJhbXM7XHJcblx0cmV0dXJuIGhhc2g7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBTdGFuZGFyZCBldmVudCBiaW5kaW5nc1xyXG4gKi9cclxuZnVuY3Rpb24gYmluZEhhbmRsZXJzKCkge1xyXG5cdGlmIChsb2NhdGlvbi5oYXNoID09ICcjY29tbWVudHMnIHx8IGxvY2F0aW9uLmhhc2gubWF0Y2goLyNjXFxkKy8pKSB7XHJcblx0XHQkKCcuY29tbWVudHMnKS5jc3MoJ2Rpc3BsYXknLCAnJyk7XHJcblx0fVxyXG5cdCQoJy5jb21tZW50c19saW5rJykuY2xpY2soZnVuY3Rpb24oKSB7XHJcblx0XHRpZigkKCcuY29tbWVudHMnKS5jc3MoJ2Rpc3BsYXknKSA9PSAnbm9uZScpIHtcclxuXHRcdFx0JCgnLmNvbW1lbnRzJykuY3NzKCdkaXNwbGF5JywgJycpO1xyXG5cdFx0fSBlbHNlIHtcclxuXHRcdFx0JCgnLmNvbW1lbnRzJykuY3NzKCdkaXNwbGF5JywgJ25vbmUnKTtcclxuXHRcdH1cclxuXHR9KTtcclxuXHJcblx0aWYobG9jYXRpb24uaHJlZi5tYXRjaCgvI2NvbW1lbnRzLykpIHtcclxuXHRcdCQoJy5jb21tZW50cycpLmNzcygnZGlzcGxheScsICcnKTtcclxuXHR9XHJcblxyXG5cdGlmIChhamF4RW5hYmxlZCkge1xyXG5cdFx0Ly8gQUpBWCBhdXRvLWhhbmRsaW5nXHJcblx0XHQkKCdib2R5Jykub24oJ3N1Ym1pdCcsICdmb3JtLmFqYXgnLCBmdW5jdGlvbigpIHtcclxuXHRcdFx0aWYgKCQodGhpcykuYXR0cignbWV0aG9kJykudG9VcHBlckNhc2UoKSA9PSAnUE9TVCcpIHtcclxuXHRcdFx0XHRhamF4Rm9ybUxvYWQoYWpheE1ha2VIYXNoKCQodGhpcykuYXR0cignYWN0aW9uJykucmVwbGFjZSgvIy4qJC8sICcnKSwgJCh0aGlzKS5hdHRyKCdjbGFzcycpLCAncG9zdCcpLCAkKHRoaXMpLmF0dHIoJ2lkJykpO1xyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdHdpbmRvdy5sb2NhdGlvbi5oYXNoID0gYWpheE1ha2VIYXNoKCQodGhpcykuYXR0cignYWN0aW9uJykucmVwbGFjZSgvIy4qJC8sICcnKSwgJCh0aGlzKS5hdHRyKCdjbGFzcycpLCAkKHRoaXMpLnNlcmlhbGl6ZSgpKTtcclxuXHRcdFx0fVxyXG5cdFx0XHRyZXR1cm4gYWpheEVycm9yO1xyXG5cdFx0fSk7XHJcblx0XHQkKCdib2R5Jykub24oJ2NsaWNrJywgJ2EuYWpheCcsIGZ1bmN0aW9uKCkge1xyXG5cdFx0XHR3aW5kb3cubG9jYXRpb24uaGFzaCA9IGFqYXhNYWtlSGFzaCgkKHRoaXMpLmF0dHIoJ2hyZWYnKS5yZXBsYWNlKC8jLiokLywgJycpLCAkKHRoaXMpLmF0dHIoJ3JlbCcpKTtcclxuXHRcdFx0cmV0dXJuIGFqYXhFcnJvcjtcclxuXHRcdH0pO1xyXG5cclxuXHRcdC8vIEFKQVggYWN0aW9uIGNvbmZpcm1hdGlvbnNcclxuXHRcdCQoJ2JvZHknKS5vbignY2xpY2snLCAnYS5jb25maXJtTGluaycsIGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRpZiAoJCh0aGlzKS5hdHRyKCdocmVmJykubWF0Y2goL21lc3NhZ2UuKzkyMC9pKSkge1xyXG5cdFx0XHRcdGlmICgkKCcjY29uZmlybUJveCcpKSB7XHJcblx0XHRcdFx0XHQkKCcjY29uZmlybUJveCcpLnJlbW92ZSgpO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHQkKCdib2R5JykucHJlcGVuZCgnPGRpdiBpZD1cImNvbmZpcm1Cb3hcIiBjbGFzcz1cImpxbVdpbmRvd1wiPjwvZGl2PicpO1xyXG5cdFx0XHRcdCQoJyNjb25maXJtQm94JykuanFtKHthamF4OiQodGhpcykuYXR0cignaHJlZicpLG1vZGFsOnRydWUsb25Mb2FkOmZ1bmN0aW9uKCl7XHJcblx0XHRcdFx0XHQkKCcjY29uZmlybUJveCcpLmNzcygnbWFyZ2luLWxlZnQnLCAnLScrKCQoJyNjb25maXJtQm94Jykud2lkdGgoKS8yKSsncHgnKTtcclxuXHRcdFx0XHRcdCQoJyNjb25maXJtQm94JykuY3NzKCdtYXJnaW4tdG9wJywgJy0nKygkKCcjY29uZmlybUJveCcpLmhlaWdodCgpLzIpKydweCcpO1xyXG5cdFx0XHRcdH19KTtcclxuXHRcdFx0XHQkKCcjY29uZmlybUJveCcpLmpxbVNob3coKTtcclxuXHRcdFx0XHRyZXR1cm4gZmFsc2U7XHJcblx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0cmV0dXJuIHRydWU7XHJcblx0XHRcdH1cclxuXHRcdH0pO1xyXG5cclxuXHRcdC8vIExpc3RlbiB0byBoYXNoIGNoYW5nZSBldmVudHNcclxuXHRcdCQod2luZG93KS5vbignaGFzaGNoYW5nZScsIGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRhamF4UGFnZUxvYWQod2luZG93LmxvY2F0aW9uLmhhc2gucmVwbGFjZSgvXiMvLCAnJykpO1xyXG5cdFx0fSk7XHJcblxyXG5cdFx0JCgnYm9keScpLm9uKCdjbGljaycsICdhI2NvbmZpcm1ObycsIGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRpZiAoJChcIiNjb25maXJtQm94XCIpLmlzKFwiLmpxbVdpbmRvd1wiKSlcclxuXHRcdFx0e1xyXG5cdFx0XHRcdCQoJyNjb25maXJtQm94JykuanFtSGlkZSgpO1xyXG5cdFx0XHRcdCQoJyNjb25maXJtQm94JykucmVtb3ZlKCk7XHJcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0XHR9XHJcblx0XHRcdGVsc2VcclxuXHRcdFx0e1xyXG5cdFx0XHRcdHJldHVybiB0cnVlO1xyXG5cdFx0XHR9XHJcblx0XHR9KTtcclxuXHR9XHJcbn1cclxuXHJcbmlmICh0eXBlb2YgalF1ZXJ5ICE9ICd1bmRlZmluZWQnKSB7XHJcbiAgICAkKGRvY3VtZW50KS5yZWFkeShmdW5jdGlvbigpIHtcclxuICAgICAgICAvLyBJZiBwYWdlIHdhcyBsb2FkZWQgd2l0aCBoYXNoXHJcbiAgICAgICAgaWYgKGFqYXhFbmFibGVkKSB7XHJcbiAgICAgICAgICAgIGlmKHdpbmRvdy5sb2NhdGlvbi5oYXNoICE9ICcnKSB7XHJcbiAgICAgICAgICAgICAgICBhamF4UGFnZUxvYWQod2luZG93LmxvY2F0aW9uLmhhc2gucmVwbGFjZSgvXiMvLCAnJykpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBiaW5kSGFuZGxlcnMoKTtcclxuICAgIH0pO1xyXG59XHJcblxyXG53aW5kb3cubmFtZSA9ICdtYWluJzsiXX0=
