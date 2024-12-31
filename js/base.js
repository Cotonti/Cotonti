/**
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
(()=>{"use strict";class e{eventsUrl=null;#e=null;onEvent=null;constructor(e,t=null){this.eventsUrl=e,null!==t&&(this.mode=t),this.init()}init(){null===this.#e&&("production"!==this.mode&&console.log("init ServerSentEventsClient"),this.#e=new EventSource(this.eventsUrl),"production"!==this.mode&&(this.#e.onopen=function(e){console.log("ServerSentEventsClient connection is open")}),this.#e.onmessage=e=>{const t=JSON.parse(e.data);void 0!==t.data&&"string"==typeof t.data&&""!==t.data&&(t.data=JSON.parse(t.data)),t.id=e.lastEventId,this.#t(t)},"production"!==this.mode&&(this.#e.onerror=function(e){console.log("ServerSentEventsClient connection error")}))}#t(e){"production"!==this.mode&&console.log("ServerSentEventsClient: Server triggered event",e),"function"!=typeof this.onEvent&&console.error(this.constructor.name+".onEvent is not defined"),this.onEvent(e)}}class t{eventsUrl=null;#n=null;onEvent=null;mode="production";constructor(e,t=null){this.eventsUrl=e,null!==t&&(this.mode=t),this.init()}init(){null===this.#n&&("production"!==this.mode&&console.log("init ServerSentEvents driver"),this.#n=new e(this.eventsUrl,this.mode),this.#n.onEvent=e=>{this.onEventTriggered(e)})}onEventTriggered(e){"function"!=typeof this.onEvent&&console.error(this.constructor.name+".onEvent is not defined"),this.onEvent(e)}}class n extends t{init(){"production"!==this.mode&&console.log("init ServerSentEventsSharedWorker driver");try{const e=new SharedWorker("/js/sharedWorkerSSE.min.js").port;e.postMessage({config:{url:this.eventsUrl,mode:this.mode}}),e.onmessage=e=>{"production"!==this.mode&&console.log("ServerSentEventsSharedWorker event",e.data),this.onEventTriggered(e.data)}}catch(e){console.error(e)}}}class r{#r=null;#o=null;mode="production";constructor(){this.#o=new Map}addObserver(e,t,n){this.#o.set(e,{name:e,event:t,onEvent:n}),null===this.#r&&this.#s()}#s(){const e=getBaseHref()+"index.php?n=server-events";void 0!==typeof SharedWorker?("production"!==this.mode&&console.log("creating ServerSentEventsSharedWorker driver"),this.#r=new n(e,this.mode)):("production"!==this.mode&&console.log("creating ServerSentEvents driver"),this.#r=new t(e,this.mode)),this.#r.onEvent=e=>{this.#i(e)},this.#r.mode=this.mode}#i(e){"production"!==this.mode&&console.log("Server triggered event",e),this.#o.forEach((t=>{t.event===e.event&&"function"==typeof t.onEvent&&t.onEvent(e.data)}))}}window.cot=new class{#l=null;getServerEvents(){return null===this.#l&&(this.#l=new r),this.#l}async loadData(e){if(!e)return{};void 0===this.loadedData&&(this.loadedData={});let t=[];("string"==typeof e||e instanceof String)&&(e=[e]);for(let n of e)n in this.loadedData||t.push(n);if(t.length>0){let e=new URLSearchParams({n:"main",a:"get"});t.forEach(((t,n,r)=>{e.append("data["+n+"]",t)})),e.append("_ajax",1);try{let t=await fetch("index.php?"+e.toString());if(t.ok){const e=await t.json();if(e.success)for(const t in e.data)this.loadedData[t]=e.data[t]}}catch(e){}}let n={};for(let t of e)t in this.loadedData&&(n[t]=this.loadedData[t]);return n}loadCaptcha(){this.loadData(["captcha","x"]).then((e=>{let t=document.querySelectorAll(".captcha-place-holder");for(let n of t){n.innerHTML=e.captcha,this.executeScriptElements(n),n.classList.remove("captcha-place-holder","loading"),n.classList.add("captcha");const t=n.closest("form");if(null!==t){let n=t.querySelector('input[type="hidden"][name="x"]');null!==n&&n.setAttribute("value",e.x)}}}))}executeScriptElements(e){const t=e.querySelectorAll("script");Array.from(t).forEach((e=>{const t=document.createElement("script");Array.from(e.attributes).forEach((e=>{t.setAttribute(e.name,e.value)})),t.text=e.text,e.parentNode.replaceChild(t,e)}))}}})();

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

window.name = 'main';//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIl9oZWFkZXIuanMiLCJ3ZWJwYWNrOi8vY290b250aS9zcmMvc2VydmVyRXZlbnRzL1NlcnZlclNlbnRFdmVudHNDbGllbnQuanMiLCJ3ZWJwYWNrOi8vY290b250aS9zcmMvc2VydmVyRXZlbnRzL2RyaXZlci9TZXJ2ZXJTZW50RXZlbnRzLmpzIiwid2VicGFjazovL2NvdG9udGkvc3JjL3NlcnZlckV2ZW50cy9kcml2ZXIvU2VydmVyU2VudEV2ZW50c1NoYXJlZFdvcmtlci5qcyIsIndlYnBhY2s6Ly9jb3RvbnRpL3NyYy9zZXJ2ZXJFdmVudHMvU2VydmVyRXZlbnRzLmpzIiwid2VicGFjazovL2NvdG9udGkvc3JjL0NvdG9udGlBcHBsaWNhdGlvbi5qcyIsImJhc2UuanMiXSwibmFtZXMiOlsiU2VydmVyU2VudEV2ZW50c0NsaWVudCIsImV2ZW50c1VybCIsIm9uRXZlbnQiLCJjb25zdHJ1Y3RvciIsIm1vZGUiLCJ0aGlzIiwiaW5pdCIsImNvbnNvbGUiLCJsb2ciLCJFdmVudFNvdXJjZSIsIm9ub3BlbiIsImV2ZW50Iiwib25tZXNzYWdlIiwiZXZlbnREYXRhIiwiSlNPTiIsInBhcnNlIiwiZGF0YSIsInVuZGVmaW5lZCIsImlkIiwibGFzdEV2ZW50SWQiLCJvbmVycm9yIiwiZXJyb3IiLCJuYW1lIiwiU2VydmVyU2VudEV2ZW50cyIsIm9uRXZlbnRUcmlnZ2VyZWQiLCJTZXJ2ZXJTZW50RXZlbnRzU2hhcmVkV29ya2VyIiwicG9ydCIsIlNoYXJlZFdvcmtlciIsInBvc3RNZXNzYWdlIiwiY29uZmlnIiwidXJsIiwiZSIsIlNlcnZlckV2ZW50cyIsIk1hcCIsImFkZE9ic2VydmVyIiwiY2FsbGJhY2siLCJzZXQiLCJnZXRCYXNlSHJlZiIsImZvckVhY2giLCJvYnNlcnZlciIsIndpbmRvdyIsImNvdCIsImdldFNlcnZlckV2ZW50cyIsImxvYWREYXRhIiwid2hhdCIsImxvYWRlZERhdGEiLCJkYXRhVG9Mb2FkIiwiU3RyaW5nIiwiaXRlbSIsInB1c2giLCJsZW5ndGgiLCJwYXJhbXMiLCJVUkxTZWFyY2hQYXJhbXMiLCJuIiwiYSIsImluZGV4IiwiYXJyYXkiLCJhcHBlbmQiLCJyZXNwb25zZSIsImZldGNoIiwidG9TdHJpbmciLCJvayIsInJlc3BvbnNlRGF0YSIsImpzb24iLCJzdWNjZXNzIiwia2V5IiwicmVzdWx0IiwibG9hZENhcHRjaGEiLCJ0aGVuIiwiY2FwdGNoYUVsZW1lbnRzIiwiZG9jdW1lbnQiLCJxdWVyeVNlbGVjdG9yQWxsIiwiZWxlbWVudCIsImlubmVySFRNTCIsImNhcHRjaGEiLCJleGVjdXRlU2NyaXB0RWxlbWVudHMiLCJjbGFzc0xpc3QiLCJyZW1vdmUiLCJhZGQiLCJmb3JtIiwiY2xvc2VzdCIsImlucHV0WCIsInF1ZXJ5U2VsZWN0b3IiLCJzZXRBdHRyaWJ1dGUiLCJ4IiwiY29udGFpbmVyRWxlbWVudCIsInNjcmlwdEVsZW1lbnRzIiwiQXJyYXkiLCJmcm9tIiwic2NyaXB0RWxlbWVudCIsImNsb25lZEVsZW1lbnQiLCJjcmVhdGVFbGVtZW50IiwiYXR0cmlidXRlcyIsImF0dHJpYnV0ZSIsInZhbHVlIiwidGV4dCIsInBhcmVudE5vZGUiLCJyZXBsYWNlQ2hpbGQiXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7bUJDQU8sTUFBTUEsRUFJVEMsVUFBWSxLQUtaLEdBQWUsS0FLZkMsUUFBVSxLQUVWLFdBQUFDLENBQVlGLEVBQVdHLEVBQU8sTUFDMUJDLEtBQUtKLFVBQVlBLEVBQ0osT0FBVEcsSUFDQUMsS0FBS0QsS0FBT0EsR0FFaEJDLEtBQUtDLE1BQ1QsQ0FFQSxJQUFBQSxHQUM4QixPQUF0QkQsTUFBSyxJQUlTLGVBQWRBLEtBQUtELE1BQ0xHLFFBQVFDLElBQUksK0JBR2hCSCxNQUFLLEVBQWUsSUFBSUksWUFBWUosS0FBS0osV0FFdkIsZUFBZEksS0FBS0QsT0FDTEMsTUFBSyxFQUFhSyxPQUFTLFNBQVVDLEdBQ2pDSixRQUFRQyxJQUFJLDRDQUNoQixHQUdKSCxNQUFLLEVBQWFPLFVBQWFE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QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSIsImZpbGUiOiJiYXNlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyoqXHJcbiAqIEBwYWNrYWdlIENvdG9udGlcclxuICogQGNvcHlyaWdodCAoYykgQ290b250aSBUZWFtXHJcbiAqIEBsaWNlbnNlIGh0dHBzOi8vZ2l0aHViLmNvbS9Db3RvbnRpL0NvdG9udGkvYmxvYi9tYXN0ZXIvTGljZW5zZS50eHRcclxuICovIiwiLyoqXHJcbiAqIFNlcnZlciBTZW50IEV2ZW50cyBjbGllbnRcclxuICogQGxpbmsgaHR0cHM6Ly9kZXZlbG9wZXIubW96aWxsYS5vcmcvZW4tVVMvZG9jcy9XZWIvQVBJL1NlcnZlci1zZW50X2V2ZW50cy9Vc2luZ19zZXJ2ZXItc2VudF9ldmVudHNcclxuICovXHJcbmV4cG9ydCBjbGFzcyBTZXJ2ZXJTZW50RXZlbnRzQ2xpZW50IHtcclxuICAgIC8qKlxyXG4gICAgICogQHR5cGUge3N0cmluZ3xudWxsfVxyXG4gICAgICovXHJcbiAgICBldmVudHNVcmwgPSBudWxsO1xyXG5cclxuICAgIC8qKlxyXG4gICAgICogQHR5cGUge0V2ZW50U291cmNlfG51bGx9XHJcbiAgICAgKi9cclxuICAgICNldmVudFNvdXJjZSA9IG51bGw7XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBAdHlwZSB7ZnVuY3Rpb258bnVsbH1cclxuICAgICAqL1xyXG4gICAgb25FdmVudCA9IG51bGw7XHJcblxyXG4gICAgY29uc3RydWN0b3IoZXZlbnRzVXJsLCBtb2RlID0gbnVsbCkge1xyXG4gICAgICAgIHRoaXMuZXZlbnRzVXJsID0gZXZlbnRzVXJsO1xyXG4gICAgICAgIGlmIChtb2RlICE9PSBudWxsKSB7XHJcbiAgICAgICAgICAgIHRoaXMubW9kZSA9IG1vZGU7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHRoaXMuaW5pdCgpO1xyXG4gICAgfVxyXG5cclxuICAgIGluaXQoKSB7XHJcbiAgICAgICAgaWYgKHRoaXMuI2V2ZW50U291cmNlICE9PSBudWxsKSB7XHJcbiAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGlmICh0aGlzLm1vZGUgIT09ICdwcm9kdWN0aW9uJykge1xyXG4gICAgICAgICAgICBjb25zb2xlLmxvZygnaW5pdCBTZXJ2ZXJTZW50RXZlbnRzQ2xpZW50Jyk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICB0aGlzLiNldmVudFNvdXJjZSA9IG5ldyBFdmVudFNvdXJjZSh0aGlzLmV2ZW50c1VybCk7XHJcblxyXG4gICAgICAgIGlmICh0aGlzLm1vZGUgIT09ICdwcm9kdWN0aW9uJykge1xyXG4gICAgICAgICAgICB0aGlzLiNldmVudFNvdXJjZS5vbm9wZW4gPSBmdW5jdGlvbiAoZXZlbnQpIHtcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdTZXJ2ZXJTZW50RXZlbnRzQ2xpZW50IGNvbm5lY3Rpb24gaXMgb3BlbicpO1xyXG4gICAgICAgICAgICB9O1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgdGhpcy4jZXZlbnRTb3VyY2Uub25tZXNzYWdlID0gKGV2ZW50KSA9PiB7XHJcbiAgICAgICAgICAgIGNvbnN0IGV2ZW50RGF0YSA9IEpTT04ucGFyc2UoZXZlbnQuZGF0YSk7XHJcbiAgICAgICAgICAgIGlmIChldmVudERhdGEuZGF0YSAhPT0gdW5kZWZpbmVkICYmICh0eXBlb2YgZXZlbnREYXRhLmRhdGEgPT09ICdzdHJpbmcnKSAmJiBldmVudERhdGEuZGF0YSAhPT0gJycpIHtcclxuICAgICAgICAgICAgICAgIGV2ZW50RGF0YS5kYXRhID0gSlNPTi5wYXJzZShldmVudERhdGEuZGF0YSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgZXZlbnREYXRhLmlkID0gZXZlbnQubGFzdEV2ZW50SWQ7XHJcbiAgICAgICAgICAgIHRoaXMuI29uRXZlbnRUcmlnZ2VyZWQoZXZlbnREYXRhKTtcclxuICAgICAgICB9O1xyXG5cclxuICAgICAgICBpZiAodGhpcy5tb2RlICE9PSAncHJvZHVjdGlvbicpIHtcclxuICAgICAgICAgICAgdGhpcy4jZXZlbnRTb3VyY2Uub25lcnJvciA9IGZ1bmN0aW9uIChldmVudCkge1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ1NlcnZlclNlbnRFdmVudHNDbGllbnQgY29ubmVjdGlvbiBlcnJvcicpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG5cclxuICAgICNvbkV2ZW50VHJpZ2dlcmVkKGV2ZW50RGF0YSkge1xyXG4gICAgICAgIGlmICh0aGlzLm1vZGUgIT09ICdwcm9kdWN0aW9uJykge1xyXG4gICAgICAgICAgICBjb25zb2xlLmxvZygnU2VydmVyU2VudEV2ZW50c0NsaWVudDogU2VydmVyIHRyaWdnZXJlZCBldmVudCcsIGV2ZW50RGF0YSk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBpZiAodHlwZW9mIHRoaXMub25FdmVudCAhPT0gJ2Z1bmN0aW9uJykge1xyXG4gICAgICAgICAgICBjb25zb2xlLmVycm9yKHRoaXMuY29uc3RydWN0b3IubmFtZSArICcub25FdmVudCBpcyBub3QgZGVmaW5lZCcpO1xyXG4gICAgICAgIH1cclxuICAgICAgICB0aGlzLm9uRXZlbnQoZXZlbnREYXRhKTtcclxuICAgIH1cclxufVxyXG4iLCJpbXBvcnQge1NlcnZlclNlbnRFdmVudHNDbGllbnR9IGZyb20gXCIuLi9TZXJ2ZXJTZW50RXZlbnRzQ2xpZW50XCI7XHJcblxyXG4vKipcclxuICogU2VydmVyIFNlbnQgRXZlbnRzIGRyaXZlciBpbXBsZW1lbnRhdGlvbiBmb3IgYnJvd3NlcnMgdGhhdCBkbyBub3Qgc3VwcG9ydCBTaGFyZWQgV29ya2VyLlxyXG4gKiBUaGVzZSBhcmUgQW5kcm9pZCBtb2JpbGUgZGV2aWNlIGJyb3dzZXJzLlxyXG4gKiBAbGluayBodHRwczovL2RldmVsb3Blci5tb3ppbGxhLm9yZy9lbi1VUy9kb2NzL1dlYi9BUEkvU2VydmVyLXNlbnRfZXZlbnRzL1VzaW5nX3NlcnZlci1zZW50X2V2ZW50c1xyXG4gKi9cclxuZXhwb3J0IGNsYXNzIFNlcnZlclNlbnRFdmVudHMge1xyXG4gICAgLyoqXHJcbiAgICAgKiBAdHlwZSB7c3RyaW5nfG51bGx9XHJcbiAgICAgKi9cclxuICAgIGV2ZW50c1VybCA9IG51bGw7XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBAdHlwZSB7U2VydmVyU2VudEV2ZW50c0NsaWVudHxudWxsfVxyXG4gICAgICovXHJcbiAgICAjY2xpZW50ID0gbnVsbDtcclxuXHJcbiAgICAvKipcclxuICAgICAqIEB0eXBlIHtmdW5jdGlvbnxudWxsfVxyXG4gICAgICovXHJcbiAgICBvbkV2ZW50ID0gbnVsbDtcclxuXHJcbiAgICAvKipcclxuICAgICAqIEB0eXBlIHtzdHJpbmd8bnVsbH1cclxuICAgICAqL1xyXG4gICAgbW9kZSA9ICdwcm9kdWN0aW9uJztcclxuXHJcbiAgICBjb25zdHJ1Y3RvcihldmVudHNVcmwsIG1vZGUgPSBudWxsKSB7XHJcbiAgICAgICAgdGhpcy5ldmVudHNVcmwgPSBldmVudHNVcmw7XHJcbiAgICAgICAgaWYgKG1vZGUgIT09IG51bGwpIHtcclxuICAgICAgICAgICAgdGhpcy5tb2RlID0gbW9kZTtcclxuICAgICAgICB9XHJcbiAgICAgICAgdGhpcy5pbml0KCk7XHJcbiAgICB9XHJcblxyXG4gICAgaW5pdCgpIHtcclxuICAgICAgICBpZiAodGhpcy4jY2xpZW50ICE9PSBudWxsKSB7XHJcbiAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGlmICh0aGlzLm1vZGUgIT09ICdwcm9kdWN0aW9uJykge1xyXG4gICAgICAgICAgICBjb25zb2xlLmxvZygnaW5pdCBTZXJ2ZXJTZW50RXZlbnRzIGRyaXZlcicpO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgdGhpcy4jY2xpZW50ID0gbmV3IFNlcnZlclNlbnRFdmVudHNDbGllbnQodGhpcy5ldmVudHNVcmwsIHRoaXMubW9kZSk7XHJcbiAgICAgICAgdGhpcy4jY2xpZW50Lm9uRXZlbnQgPSAoZXZlbnREYXRhKSA9PiB7XHJcbiAgICAgICAgICAgIHRoaXMub25FdmVudFRyaWdnZXJlZChldmVudERhdGEpO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuXHJcbiAgICBvbkV2ZW50VHJpZ2dlcmVkKGV2ZW50RGF0YSkge1xyXG4gICAgICAgIGlmICh0eXBlb2YgdGhpcy5vbkV2ZW50ICE9PSAnZnVuY3Rpb24nKSB7XHJcbiAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IodGhpcy5jb25zdHJ1Y3Rvci5uYW1lICsgJy5vbkV2ZW50IGlzIG5vdCBkZWZpbmVkJyk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHRoaXMub25FdmVudChldmVudERhdGEpO1xyXG4gICAgfVxyXG59IiwiaW1wb3J0IHtTZXJ2ZXJTZW50RXZlbnRzfSBmcm9tICcuL1NlcnZlclNlbnRFdmVudHMnO1xyXG5cclxuLyoqXHJcbiAqIFNlcnZlciBTZW50IEV2ZW50cyBkcml2ZXIgaW1wbGVtZW50YXRpb24gdmlhIFNoYXJlZFdvcmtlci5cclxuICpcclxuICogQGxpbmsgaHR0cHM6Ly9kZXZlbG9wZXIubW96aWxsYS5vcmcvZW4tVVMvZG9jcy9XZWIvQVBJL1NlcnZlci1zZW50X2V2ZW50cy9Vc2luZ19zZXJ2ZXItc2VudF9ldmVudHNcclxuICogQGxpbmsgaHR0cHM6Ly9kZXZlbG9wZXIubW96aWxsYS5vcmcvZW4tVVMvZG9jcy9XZWIvQVBJL1NoYXJlZFdvcmtlclxyXG4gKi9cclxuZXhwb3J0IGNsYXNzIFNlcnZlclNlbnRFdmVudHNTaGFyZWRXb3JrZXIgZXh0ZW5kcyBTZXJ2ZXJTZW50RXZlbnRzIHtcclxuICAgIGluaXQoKSB7XHJcbiAgICAgICAgaWYgKHRoaXMubW9kZSAhPT0gJ3Byb2R1Y3Rpb24nKSB7XHJcbiAgICAgICAgICAgIGNvbnNvbGUubG9nKCdpbml0IFNlcnZlclNlbnRFdmVudHNTaGFyZWRXb3JrZXIgZHJpdmVyJyk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICB0cnkge1xyXG4gICAgICAgICAgICBjb25zdCB3b3JrZXIgPSBuZXcgU2hhcmVkV29ya2VyKCcvanMvc2hhcmVkV29ya2VyU1NFLm1pbi5qcycpO1xyXG4gICAgICAgICAgICBjb25zdCBwb3J0ID0gd29ya2VyLnBvcnQ7XHJcblxyXG4gICAgICAgICAgICBwb3J0LnBvc3RNZXNzYWdlKHtjb25maWc6IHt1cmw6IHRoaXMuZXZlbnRzVXJsLCBtb2RlOiB0aGlzLm1vZGV9fSk7XHJcblxyXG4gICAgICAgICAgICBwb3J0Lm9ubWVzc2FnZSA9IChldmVudCkgPT4ge1xyXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMubW9kZSAhPT0gJ3Byb2R1Y3Rpb24nKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ1NlcnZlclNlbnRFdmVudHNTaGFyZWRXb3JrZXIgZXZlbnQnLCBldmVudC5kYXRhKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIHRoaXMub25FdmVudFRyaWdnZXJlZChldmVudC5kYXRhKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0gY2F0Y2ggKGUpIHtcclxuICAgICAgICAgICAgY29uc29sZS5lcnJvcihlKTtcclxuICAgICAgICB9XHJcbiAgICB9XHJcbn0iLCJpbXBvcnQge1NlcnZlclNlbnRFdmVudHNTaGFyZWRXb3JrZXJ9IGZyb20gXCIuL2RyaXZlci9TZXJ2ZXJTZW50RXZlbnRzU2hhcmVkV29ya2VyXCI7XHJcbmltcG9ydCB7U2VydmVyU2VudEV2ZW50c30gZnJvbSBcIi4vZHJpdmVyL1NlcnZlclNlbnRFdmVudHNcIjtcclxuXHJcbi8qKlxyXG4gKiBTZXJ2ZXIgZXZlbnRzXHJcbiAqIEBwYWNrYWdlIENvdG9udGlcclxuICogQGNvcHlyaWdodCAoYykgQ290b250aSBUZWFtXHJcbiAqL1xyXG5leHBvcnQgY2xhc3MgU2VydmVyRXZlbnRzIHtcclxuICAgIC8qKlxyXG4gICAgICogQHR5cGUge251bGx8U2VydmVyU2VudEV2ZW50c1NoYXJlZFdvcmtlcnxTZXJ2ZXJTZW50RXZlbnRzfVxyXG4gICAgICovXHJcbiAgICAjZHJpdmVyID0gbnVsbDtcclxuXHJcbiAgICAjb2JzZXJ2ZXJzUmVnaXN0cnkgPSBudWxsO1xyXG5cclxuICAgIG1vZGUgPSAncHJvZHVjdGlvbic7XHJcblxyXG4gICAgY29uc3RydWN0b3IoKSB7XHJcbiAgICAgICAgdGhpcy4jb2JzZXJ2ZXJzUmVnaXN0cnkgPSBuZXcgTWFwKCk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBAcGFyYW0ge3N0cmluZ30gbmFtZVxyXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IGV2ZW50XHJcbiAgICAgKiBAcGFyYW0ge2Z1bmN0aW9ufSBjYWxsYmFja1xyXG4gICAgICovXHJcbiAgICBhZGRPYnNlcnZlcihuYW1lLCBldmVudCwgY2FsbGJhY2spIHtcclxuICAgICAgICB0aGlzLiNvYnNlcnZlcnNSZWdpc3RyeS5zZXQobmFtZSwge1xyXG4gICAgICAgICAgICBuYW1lOiBuYW1lLFxyXG4gICAgICAgICAgICBldmVudDogZXZlbnQsXHJcbiAgICAgICAgICAgIG9uRXZlbnQ6IGNhbGxiYWNrXHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIGlmICh0aGlzLiNkcml2ZXIgPT09IG51bGwpIHtcclxuICAgICAgICAgICAgdGhpcy4jaW5pdERyaXZlcigpO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuXHJcbiAgICAjaW5pdERyaXZlcigpIHtcclxuICAgICAgICBjb25zdCB1cmwgPSBnZXRCYXNlSHJlZigpICsgJ2luZGV4LnBocD9uPXNlcnZlci1ldmVudHMnO1xyXG4gICAgICAgIGlmICh0eXBlb2YgU2hhcmVkV29ya2VyICE9PSB1bmRlZmluZWQpIHtcclxuICAgICAgICAgICAgaWYgKHRoaXMubW9kZSAhPT0gJ3Byb2R1Y3Rpb24nKSB7XHJcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnY3JlYXRpbmcgU2VydmVyU2VudEV2ZW50c1NoYXJlZFdvcmtlciBkcml2ZXInKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB0aGlzLiNkcml2ZXIgPSBuZXcgU2VydmVyU2VudEV2ZW50c1NoYXJlZFdvcmtlcih1cmwsIHRoaXMubW9kZSk7XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgaWYgKHRoaXMubW9kZSAhPT0gJ3Byb2R1Y3Rpb24nKSB7XHJcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnY3JlYXRpbmcgU2VydmVyU2VudEV2ZW50cyBkcml2ZXInKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB0aGlzLiNkcml2ZXIgPSBuZXcgU2VydmVyU2VudEV2ZW50cyh1cmwsIHRoaXMubW9kZSk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICB0aGlzLiNkcml2ZXIub25FdmVudCA9IChldmVudERhdGEpID0+IHtcclxuICAgICAgICAgICAgdGhpcy4jZXZlbnRUcmlnZ2VyZWQoZXZlbnREYXRhKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgdGhpcy4jZHJpdmVyLm1vZGUgPSB0aGlzLm1vZGU7XHJcbiAgICB9XHJcblxyXG4gICAgI2V2ZW50VHJpZ2dlcmVkKGV2ZW50RGF0YSkge1xyXG4gICAgICAgIGlmICh0aGlzLm1vZGUgIT09ICdwcm9kdWN0aW9uJykge1xyXG4gICAgICAgICAgICBjb25zb2xlLmxvZygnU2VydmVyIHRyaWdnZXJlZCBldmVudCcsIGV2ZW50RGF0YSk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICB0aGlzLiNvYnNlcnZlcnNSZWdpc3RyeS5mb3JFYWNoKChvYnNlcnZlcikgPT4ge1xyXG4gICAgICAgICAgICBpZiAoXHJcbiAgICAgICAgICAgICAgICBvYnNlcnZlci5ldmVudCA9PT0gZXZlbnREYXRhLmV2ZW50XHJcbiAgICAgICAgICAgICAgICAmJiB0eXBlb2Ygb2JzZXJ2ZXIub25FdmVudCA9PT0gJ2Z1bmN0aW9uJ1xyXG4gICAgICAgICAgICApIHtcclxuICAgICAgICAgICAgICAgIG9ic2VydmVyLm9uRXZlbnQoZXZlbnREYXRhLmRhdGEpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcbn0iLCJpbXBvcnQge1NlcnZlckV2ZW50c30gZnJvbSAnLi9zZXJ2ZXJFdmVudHMvU2VydmVyRXZlbnRzLmpzJztcclxuXHJcbi8qKlxyXG4gKiBCYXNlIENvdG9udGkgY2xhc3NcclxuICovXHJcbmNsYXNzIENvdG9udGlBcHBsaWNhdGlvblxyXG57XHJcbiAgICAjc2V2ZXJFdmVudHMgPSBudWxsO1xyXG5cclxuICAgIC8qKlxyXG4gICAgICogR2V0IFNlcnZlciBFdmVudHMgb2JqZWN0XHJcbiAgICAgKiBAcmV0dXJucyB7U2VydmVyRXZlbnRzfVxyXG4gICAgICovXHJcbiAgICBnZXRTZXJ2ZXJFdmVudHMoKSB7XHJcbiAgICAgICAgaWYgKHRoaXMuI3NldmVyRXZlbnRzID09PSBudWxsKSB7XHJcbiAgICAgICAgICAgIHRoaXMuI3NldmVyRXZlbnRzID0gbmV3IFNlcnZlckV2ZW50cygpO1xyXG4gICAgICAgIH1cclxuICAgICAgICByZXR1cm4gdGhpcy4jc2V2ZXJFdmVudHM7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBMb2FkIGRhdGEgZnJvbSAvaW5kZXgucGhwP249bWFpbiZhPWdldFxyXG4gICAgICogQ2FuIGJlIHVzZWZ1bCwgZm9yIGV4YW1wbGUgd2hlbiBpdCBpcyBuZWVkZWQgdG8gbG9hZCBzb21lIGR5bmFtaWMgY29udGVudCB0byBjYWNoZWQgcGFnZVxyXG4gICAgICpcclxuICAgICAqIEV4YW1wbGU6XHJcbiAgICAgKiBjb3QubG9hZERhdGEoWydjYXB0Y2hhJywgJ3gnXSkudGhlbihyZXN1bHQgPT4ge1xyXG4gICAgICogICAgY29uc29sZS5sb2cocmVzdWx0KTtcclxuICAgICAqIH0pO1xyXG4gICAgICpcclxuICAgICAqIEBwYXJhbSB3aGF0IEFycmF5IG9yIHN0cmluZy4gRGF0YSB0byBsb2FkLiBGb3IgZXhhbXBsZSwgWydjYXB0Y2hhJywgJ3gnXSAoeCAtIGFudGkgWFNTIHBhcmFtZXRlciB2YWx1ZSlcclxuICAgICAqIEByZXR1cm5zIHtQcm9taXNlPHt9Pn1cclxuICAgICAqL1xyXG4gICAgYXN5bmMgbG9hZERhdGEod2hhdCkge1xyXG4gICAgICAgIGlmICghd2hhdCkge1xyXG4gICAgICAgICAgICByZXR1cm4ge307XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBpZiAodGhpcy5sb2FkZWREYXRhID09PSB1bmRlZmluZWQpIHtcclxuICAgICAgICAgICAgdGhpcy5sb2FkZWREYXRhID0ge307XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBsZXQgZGF0YVRvTG9hZCA9IFtdO1xyXG5cclxuICAgICAgICBpZiAodHlwZW9mIHdoYXQgPT09ICdzdHJpbmcnIHx8IHdoYXQgaW5zdGFuY2VvZiBTdHJpbmcpIHtcclxuICAgICAgICAgICAgd2hhdCA9IFt3aGF0XTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGZvciAobGV0IGl0ZW0gb2Ygd2hhdCkge1xyXG4gICAgICAgICAgICBpZiAoIShpdGVtIGluIHRoaXMubG9hZGVkRGF0YSkpIHtcclxuICAgICAgICAgICAgICAgIGRhdGFUb0xvYWQucHVzaChpdGVtKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgaWYgKGRhdGFUb0xvYWQubGVuZ3RoID4gMCkge1xyXG4gICAgICAgICAgICAvLyBAdG9kbyBjaGFuZ2UgdG8gc3lzdGVtIGNvbnRyb2xsZXIgd2hlbiBpdCB3aWxsIGJlIGltcGxlbWVudGVkXHJcbiAgICAgICAgICAgIGxldCBwYXJhbXMgPSBuZXcgVVJMU2VhcmNoUGFyYW1zKHtuOiAnbWFpbicsIGE6ICdnZXQnfSk7XHJcblxyXG4gICAgICAgICAgICBkYXRhVG9Mb2FkLmZvckVhY2goKGl0ZW0sIGluZGV4LCBhcnJheSkgPT4ge1xyXG4gICAgICAgICAgICAgICAgcGFyYW1zLmFwcGVuZCgnZGF0YVsnICsgaW5kZXggKyAnXScsIGl0ZW0pO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgcGFyYW1zLmFwcGVuZCgnX2FqYXgnLCAxKTtcclxuICAgICAgICAgICAgdHJ5IHtcclxuICAgICAgICAgICAgICAgIGxldCByZXNwb25zZSA9IGF3YWl0IGZldGNoKCdpbmRleC5waHA/JyArIHBhcmFtcy50b1N0cmluZygpKTtcclxuXHJcbiAgICAgICAgICAgICAgICBpZiAocmVzcG9uc2Uub2spIHtcclxuICAgICAgICAgICAgICAgICAgICBjb25zdCByZXNwb25zZURhdGEgPSBhd2FpdCByZXNwb25zZS5qc29uKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYgKHJlc3BvbnNlRGF0YS5zdWNjZXNzKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGZvciAoY29uc3Qga2V5IGluIHJlc3BvbnNlRGF0YS5kYXRhKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGlzLmxvYWRlZERhdGFba2V5XSA9IHJlc3BvbnNlRGF0YS5kYXRhW2tleV07XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICAvLyBIVFRQIGVycm9yXHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0gY2F0Y2ggKGVycm9yKSB7XHJcbiAgICAgICAgICAgICAgICAvLyBSZXF1ZXN0IGVycm9yXHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGxldCByZXN1bHQgPSB7fTtcclxuICAgICAgICBmb3IgKGxldCBpdGVtIG9mIHdoYXQpIHtcclxuICAgICAgICAgICAgaWYgKChpdGVtIGluIHRoaXMubG9hZGVkRGF0YSkpIHtcclxuICAgICAgICAgICAgICAgIHJlc3VsdFtpdGVtXSA9IHRoaXMubG9hZGVkRGF0YVtpdGVtXTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgcmV0dXJuIHJlc3VsdDtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIExvYWQgY2FwdGNoYSB2aWEgYWpheC4gVXNlZCBvbiBjYWNoZWQgcGFnZXMuXHJcbiAgICAgKi9cclxuICAgIGxvYWRDYXB0Y2hhKCkge1xyXG4gICAgICAgIHRoaXMubG9hZERhdGEoWydjYXB0Y2hhJywgJ3gnXSkudGhlbihyZXN1bHQgPT4ge1xyXG4gICAgICAgICAgICBsZXQgY2FwdGNoYUVsZW1lbnRzID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnLmNhcHRjaGEtcGxhY2UtaG9sZGVyJyk7XHJcbiAgICAgICAgICAgIGZvciAobGV0IGVsZW1lbnQgb2YgY2FwdGNoYUVsZW1lbnRzKSB7XHJcbiAgICAgICAgICAgICAgICBlbGVtZW50LmlubmVySFRNTCA9IHJlc3VsdC5jYXB0Y2hhO1xyXG4gICAgICAgICAgICAgICAgdGhpcy5leGVjdXRlU2NyaXB0RWxlbWVudHMoZWxlbWVudCk7XHJcbiAgICAgICAgICAgICAgICBlbGVtZW50LmNsYXNzTGlzdC5yZW1vdmUoJ2NhcHRjaGEtcGxhY2UtaG9sZGVyJywgJ2xvYWRpbmcnKTtcclxuICAgICAgICAgICAgICAgIGVsZW1lbnQuY2xhc3NMaXN0LmFkZCgnY2FwdGNoYScpO1xyXG5cclxuICAgICAgICAgICAgICAgIGNvbnN0IGZvcm0gPSBlbGVtZW50LmNsb3Nlc3QoJ2Zvcm0nKTtcclxuICAgICAgICAgICAgICAgIGlmIChmb3JtICE9PSBudWxsKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgbGV0IGlucHV0WCA9IGZvcm0ucXVlcnlTZWxlY3RvcignaW5wdXRbdHlwZT1cImhpZGRlblwiXVtuYW1lPVwieFwiXScpO1xyXG4gICAgICAgICAgICAgICAgICAgIGlmIChpbnB1dFggIT09IG51bGwpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgaW5wdXRYLnNldEF0dHJpYnV0ZSgndmFsdWUnLCByZXN1bHQueCk7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBJZiB5b3UgYXBwZW5kIDxzY3JpcHQ+IHRhZ3MgdG8gdGhlIGVsZW1lbnRzIG9mIHRoZSBmaW5pc2hlZCBET00gZG9jdW1lbnQsIHRoZXkgd2lsbCBub3QgYmUgZXhlY3V0ZWQgYXV0b21hdGljYWxseS5cclxuICAgICAqIFRoZSBtZXRob2QgZXhlY3V0ZXMgPHNjcmlwdD4gc2NyaXB0cyBuZXN0ZWQgaW4gdGhlIHNwZWNpZmllZCBlbGVtZW50XHJcbiAgICAgKiBAcGFyYW0gY29udGFpbmVyRWxlbWVudCBOb2RlXHJcbiAgICAgKi9cclxuICAgIGV4ZWN1dGVTY3JpcHRFbGVtZW50cyhjb250YWluZXJFbGVtZW50KSB7XHJcbiAgICAgICAgY29uc3Qgc2NyaXB0RWxlbWVudHMgPSBjb250YWluZXJFbGVtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJ3NjcmlwdCcpO1xyXG5cclxuICAgICAgICBBcnJheS5mcm9tKHNjcmlwdEVsZW1lbnRzKS5mb3JFYWNoKChzY3JpcHRFbGVtZW50KSA9PiB7XHJcbiAgICAgICAgICAgIGNvbnN0IGNsb25lZEVsZW1lbnQgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdzY3JpcHQnKTtcclxuICAgICAgICAgICAgQXJyYXkuZnJvbShzY3JpcHRFbGVtZW50LmF0dHJpYnV0ZXMpLmZvckVhY2goKGF0dHJpYnV0ZSkgPT4ge1xyXG4gICAgICAgICAgICAgICAgY2xvbmVkRWxlbWVudC5zZXRBdHRyaWJ1dGUoYXR0cmlidXRlLm5hbWUsIGF0dHJpYnV0ZS52YWx1ZSk7XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICBjbG9uZWRFbGVtZW50LnRleHQgPSBzY3JpcHRFbGVtZW50LnRleHQ7XHJcbiAgICAgICAgICAgIHNjcmlwdEVsZW1lbnQucGFyZW50Tm9kZS5yZXBsYWNlQ2hpbGQoY2xvbmVkRWxlbWVudCwgc2NyaXB0RWxlbWVudCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcbn1cclxuXHJcbndpbmRvdy5jb3QgPSBuZXcgQ290b250aUFwcGxpY2F0aW9uKCk7XHJcbiIsImZ1bmN0aW9uIGVuY29kZVVSSWZpeChzdHIpIHtcclxuXHQvLyB0byBwcmV2ZW50IHR3aWNlIGVuY29kaW5nXHJcblx0Ly8gYW5kIGZpeCAnWycsJ10nIHNpZ25zIHRvIGZvbGxvdyBSRkMzOTg2IChzZWN0aW9uLTMuMi4yKVxyXG5cdHJldHVybiBlbmNvZGVVUkkoZGVjb2RlVVJJKHN0cikpLnJlcGxhY2UoLyU1Qi9nLCAnWycpLnJlcGxhY2UoLyU1RC9nLCAnXScpO1xyXG59XHJcblxyXG5mdW5jdGlvbiBnZXRCYXNlSHJlZigpIHtcclxuXHR2YXIgaHJlZiA9IGRvY3VtZW50LmdldEVsZW1lbnRzQnlUYWdOYW1lKCdiYXNlJylbMF0uaHJlZjtcclxuXHRpZiAoaHJlZiA9PSBudWxsKSB7XHJcblx0XHRyZXR1cm4gJy8nO1xyXG5cdH0gZWxzZSB7XHJcblx0XHRyZXR1cm4gaHJlZjtcclxuXHR9XHJcbn1cclxuXHJcbmZ1bmN0aW9uIHBvcHVwKGNvZGUsIHcsIGgpe1xyXG4gICAgd2luZG93Lm9wZW4oZ2V0QmFzZUhyZWYoKSArICdpbmRleC5waHA/bz0nICsgY29kZSwgJycsICd0b29sYmFyPTAsbG9jYXRpb249MCxkaXJlY3Rvcmllcz0wLG1lbnVCYXI9MCxyZXNpemFibGU9MCxzY3JvbGxiYXJzPXllcyx3aWR0aD0nICsgdyArICcsaGVpZ2h0PScgKyBoICsgJyxsZWZ0PTMyLHRvcD0xNicpO1xyXG59XHJcblxyXG4vKipcclxuICogQHRvZG8gbW92ZSB0byBwZnMgbW9kdWxlXHJcbiAqL1xyXG5mdW5jdGlvbiBwZnMoaWQsIGMxLCBjMiwgcGFyc2VyKXtcclxuICAgIHdpbmRvdy5vcGVuKFxyXG5cdFx0Z2V0QmFzZUhyZWYoKSArICdpbmRleC5waHA/ZT1wZnMmdXNlcmlkPScgKyBpZCArICcmYzE9JyArIGMxICsgJyZjMj0nICsgYzIgKyAnJnBhcnNlcj0nICsgcGFyc2VyLFxyXG5cdFx0J1BGUycsXHJcblx0XHQnc3RhdHVzPTEsIHRvb2xiYXI9MCxsb2NhdGlvbj0wLGRpcmVjdG9yaWVzPTAsbWVudUJhcj0wLHJlc2l6YWJsZT0xLHNjcm9sbGJhcnM9eWVzLHdpZHRoPTc1NCxoZWlnaHQ9NTEyLGxlZnQ9MzIsdG9wPTE2J1xyXG5cdCk7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIHJlZGlyZWN0KHVybCl7XHJcbiAgICBsb2NhdGlvbi5ocmVmID0gdXJsLm9wdGlvbnNbdXJsLnNlbGVjdGVkSW5kZXhdLnZhbHVlO1xyXG59XHJcblxyXG5mdW5jdGlvbiB0b2dnbGVibG9jayhpZCl7XHJcbiAgICB2YXIgYmwgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChpZCk7XHJcbiAgICBpZiAoYmwuc3R5bGUuZGlzcGxheSA9PSAnbm9uZScpIHtcclxuICAgICAgICBibC5zdHlsZS5kaXNwbGF5ID0gJyc7XHJcbiAgICB9XHJcbiAgICBlbHNlIHtcclxuICAgICAgICBibC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xyXG4gICAgfVxyXG5cdHJldHVybiBmYWxzZTtcclxufVxyXG5cclxuZnVuY3Rpb24gdG9nZ2xlQWxsKGFjdGlvbikge1xyXG5cdHZhciBibGtzID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnW2lkXj1cImJsa19cIl0nKTtcclxuXHRmb3IgKGkgPSAwOyBpIDwgYmxrcy5sZW5ndGg7IGkrKykge1xyXG5cdFx0aWYgKGFjdGlvbiA9PSAnaGlkZScpIHtcclxuXHRcdFx0Ymxrc1tpXS5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xyXG5cdFx0fSBlbHNlIGlmIChhY3Rpb24gPT0gJ3Nob3cnKSB7XHJcblx0XHRcdGJsa3NbaV0uc3R5bGUuZGlzcGxheSA9ICd0YWJsZS1yb3ctZ3JvdXAnO1xyXG5cdFx0fVxyXG5cdH1cclxuXHRyZXR1cm4gZmFsc2U7XHJcbn1cclxuXHJcbi8vIEluc2VydHMgdGV4dCBpbnRvIHRleHRhcmVhIGF0IGN1cnNvciBwb3NpdGlvblxyXG5mdW5jdGlvbiBpbnNlcnRUZXh0KGRvY09iaiwgZmllbGROYW1lLCB2YWx1ZSkge1xyXG5cdHZhciBmaWVsZCA9IG51bGw7XHJcblx0aWYgKCFkb2NPYmopIHtcclxuXHRcdGRvY09iaiA9IGRvY3VtZW50O1xyXG5cdH1cclxuXHQvLyBGaW5kIHRoZSBmaWVsZCBpbiB0aGUgZG9jT2JqXHJcblx0ZmluZEZpZWxkOlxyXG5cdGZvciAodmFyIGkgPSAwOyBpIDwgZG9jT2JqLmZvcm1zLmxlbmd0aDsgaSsrKSB7XHJcblx0XHRmb3IgKHZhciBqID0gMDsgaiA8IGRvY09iai5mb3Jtc1tpXS5lbGVtZW50cy5sZW5ndGg7IGorKykge1xyXG5cdFx0XHRpZiAoZG9jT2JqLmZvcm1zW2ldLmVsZW1lbnRzW2pdLm5hbWUgPT0gZmllbGROYW1lKSB7XHJcblx0XHRcdFx0ZmllbGQgPSBkb2NPYmouZm9ybXNbaV0uZWxlbWVudHNbal07XHJcblx0XHRcdFx0YnJlYWsgZmluZEZpZWxkO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblx0fVxyXG5cdGlmICghZmllbGQpIHtcclxuXHRcdHJldHVybiBmYWxzZTtcclxuXHR9XHJcblxyXG5cdC8vIEluc2VydCB0aGUgdGV4dFxyXG5cdGlmIChkb2NPYmouc2VsZWN0aW9uKSB7XHJcblx0XHQvLyBNU0lFIGFuZCBPcGVyYVxyXG5cdFx0ZmllbGQuZm9jdXMoKTtcclxuXHRcdHZhciBzZWwgPSBkb2NPYmouc2VsZWN0aW9uLmNyZWF0ZVJhbmdlKCk7XHJcblx0XHRzZWwudGV4dCA9IHZhbHVlO1xyXG5cdH0gZWxzZSBpZiAoZmllbGQuc2VsZWN0aW9uU3RhcnQgfHwgZmllbGQuc2VsZWN0aW9uU3RhcnQgPT0gMCkge1xyXG5cdFx0Ly8gTW96aWxsYVxyXG5cdFx0dmFyIHN0YXJ0UG9zID0gZmllbGQuc2VsZWN0aW9uU3RhcnQ7XHJcblx0XHR2YXIgZW5kUG9zID0gZmllbGQuc2VsZWN0aW9uRW5kO1xyXG5cdFx0ZmllbGQudmFsdWUgPSBmaWVsZC52YWx1ZS5zdWJzdHJpbmcoMCwgc3RhcnRQb3MpICsgdmFsdWUgKyBmaWVsZC52YWx1ZS5zdWJzdHJpbmcoZW5kUG9zLCBmaWVsZC52YWx1ZS5sZW5ndGgpO1xyXG5cdH0gZWxzZSB7XHJcblx0XHRmaWVsZC52YWx1ZSArPSB2YWx1ZTtcclxuXHR9XHJcblx0cmV0dXJuIHRydWU7XHJcbn1cclxuXHJcbi8vIEFycmF5IG9mIGFqYXggZXJyb3IgaGFuZGxlcnNcclxuLy8gRXhhbXBsZSBvZiB1c2U6XHJcbi8vIGFqYXhFcnJvckhhbmRsZXJzLnB1c2goe2Z1bmM6IG15RXJyb3JIYW5kbGVyfSk7XHJcbi8vIGFqYXhTdWNjZXNzSGFuZGxlcnMucHVzaCh7ZnVuYzogbXlTdWNjZXNzSGFuZGxlcn0pO1xyXG52YXIgYWpheEVycm9ySGFuZGxlcnMgPSBuZXcgQXJyYXkoKTtcclxudmFyIGFqYXhTdWNjZXNzSGFuZGxlcnMgPSBuZXcgQXJyYXkoKTtcclxuLy8gQUpBWCBlbmFibGVtZW50IGRlZmF1bHRzIHRvIGZhbHNlXHJcbnZhciBhamF4RW5hYmxlZCA9IGZhbHNlO1xyXG4vLyBSZXF1aXJlZCB0byBjYWxjdWxhdGUgcGF0aHNcclxuaWYgKHR5cGVvZiBqUXVlcnkgIT0gJ3VuZGVmaW5lZCcpIHtcclxuXHR2YXIgYWpheEN1cnJlbnRCYXNlID0gbG9jYXRpb24uaHJlZi5yZXBsYWNlKCQoJ2Jhc2UnKS5lcSgwKS5hdHRyKCdocmVmJyksICcnKS5yZXBsYWNlKC9cXD8uKiQvLCAnJykucmVwbGFjZSgvIy4qJC8sICcnKTtcclxufVxyXG4vLyBUaGlzIGZsYWcgaW5kaWNhdGVzIHRoYXQgQUpBWCtoaXN0b3J5IGhhcyBiZWVuIHVzZWQgb24gdGhpcyBwYWdlXHJcbi8vIEl0IG1lYW5zIHRoYXQgXCIjXCIgb3IgaG9tZSBsaW5rIHNob3VsZCBiZSBsb2FkZWQgdmlhIGFqYXggdG9vXHJcbnZhciBhamF4VXNlZCA9IGZhbHNlO1xyXG4vLyBHbG9iYWwgZmxhZyB0byBsZXQgZXZlcnlib2R5IGtub3cgdGhhdCBBSkFYIGhhcyBmYWlsZWRcclxudmFyIGFqYXhFcnJvciA9IGZhbHNlO1xyXG5cclxuLyoqXHJcbiAqIEFKQVggaGVscGVyIGZ1bmN0aW9uXHJcbiAqIEBwYXJhbSB7aGFzaH0gc2V0dGluZ3MgQSBoYXNodGFibGUgd2l0aCBzZXR0aW5nc1xyXG4gKiBAcmV0dXJuIEZBTFNFIG9uIHN1Y2Nlc3NmdWwgQUpBWCBjYWxsLCBUUlVFIG9uIGVycm9yIHRvIGNvbnRpbnVlIGluXHJcbiAqIHN5bmNocm9ub3VzIG1vZGVcclxuICogQHR5cGUgYm9vbFxyXG4gKi9cclxuZnVuY3Rpb24gYWpheFNlbmQoc2V0dGluZ3MpIHtcclxuXHR2YXIgbWV0aG9kID0gc2V0dGluZ3MubWV0aG9kID8gc2V0dGluZ3MubWV0aG9kLnRvVXBwZXJDYXNlKCkgOiAnR0VUJztcclxuXHR2YXIgZGF0YSA9IHNldHRpbmdzLmRhdGEgfHwgJyc7XHJcblx0dmFyIHVybCA9IHNldHRpbmdzLnVybCB8fCAkKCcjJyArIHNldHRpbmdzLmZvcm1JZCkuYXR0cignYWN0aW9uJyk7XHJcblx0aWYgKG1ldGhvZCA9PT0gJ1BPU1QnKSB7XHJcblx0XHRkYXRhICs9ICcmJyArICQoJyMnICsgc2V0dGluZ3MuZm9ybUlkKS5zZXJpYWxpemUoKTtcclxuXHR9IGVsc2UgaWYgKHNldHRpbmdzLmZvcm1JZCkge1xyXG5cdFx0dmFyIHNlcCA9IHVybC5pbmRleE9mKCc/JykgPiAwID8gJyYnIDogJz8nO1xyXG5cdFx0dXJsICs9IHNlcCArICQoJyMnICsgc2V0dGluZ3MuZm9ybUlkKS5zZXJpYWxpemUoKTtcclxuXHR9XHJcblx0JC5hamF4KHtcclxuXHRcdHR5cGU6IG1ldGhvZCxcclxuXHRcdHVybDogZW5jb2RlVVJJZml4KHVybCksXHJcblx0XHRkYXRhOiBkYXRhLFxyXG5cdFx0YmVmb3JlU2VuZDogZnVuY3Rpb24oKSB7XHJcblx0XHRcdGlmICghc2V0dGluZ3Mubm9uc2hvd2xvYWRpbmcpIHtcclxuXHRcdFx0XHQkKCcjJyArIHNldHRpbmdzLmRpdklkKVxyXG5cdFx0XHRcdFx0LmFwcGVuZCgnPHNwYW4gc3R5bGU9XCJwb3NpdGlvbjphYnNvbHV0ZTsgbGVmdDonICsgKCQoJyMnICsgc2V0dGluZ3MuZGl2SWQpLndpZHRoKCkvMiAtIDExMCkgKyAncHg7dG9wOicgKyAoJCgnIycgKyBzZXR0aW5ncy5kaXZJZCkuaGVpZ2h0KCkvMiAtIDkpICsgJ3B4O1wiIGNsYXNzPVwibG9hZGluZ1wiIGlkPVwibG9hZGluZ1wiPjxpbWcgc3JjPVwiLi9pbWFnZXMvc3Bpbm5lci5naWZcIiBhbHQ9XCJsb2FkaW5nXCIvPjwvc3Bhbj4nKS5jc3MoJ3Bvc2l0aW9uJywgJ3JlbGF0aXZlJyk7XHJcblx0XHRcdH1cclxuXHRcdH0sXHJcblx0XHRzdWNjZXNzOiBmdW5jdGlvbihtc2cpIHtcclxuXHRcdFx0aWYgKCFzZXR0aW5ncy5ub25zaG93bG9hZGluZykge1xyXG5cdFx0XHRcdCQoJyNsb2FkaW5nJykucmVtb3ZlKCk7XHJcblx0XHRcdH1cclxuXHRcdFx0aWYgKCFzZXR0aW5ncy5ub25zaG93ZmFkZWluKSB7XHJcblx0XHRcdFx0JCgnIycgKyBzZXR0aW5ncy5kaXZJZCkuaGlkZSgpLmh0bWwobXNnKS5mYWRlSW4oNTAwKTtcclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHQkKCcjJyArIHNldHRpbmdzLmRpdklkKS5odG1sKG1zZyk7XHJcblx0XHRcdH1cclxuXHRcdFx0Zm9yICh2YXIgaSA9IDA7IGkgPCBhamF4U3VjY2Vzc0hhbmRsZXJzLmxlbmd0aDsgaSsrKSB7XHJcblx0XHRcdFx0aWYoYWpheFN1Y2Nlc3NIYW5kbGVyc1tpXS5mdW5jKVxyXG5cdFx0XHRcdFx0YWpheFN1Y2Nlc3NIYW5kbGVyc1tpXS5mdW5jKG1zZyk7XHJcblx0XHRcdFx0ZWxzZVxyXG5cdFx0XHRcdFx0YWpheFN1Y2Nlc3NIYW5kbGVyc1tpXShtc2cpO1xyXG5cdFx0XHR9XHJcblx0XHR9LFxyXG5cdFx0ZXJyb3I6IGZ1bmN0aW9uKG1zZykge1xyXG5cdFx0XHRpZiAoIXNldHRpbmdzLm5vbnNob3dsb2FkaW5nKSB7XHJcblx0XHRcdFx0JCgnI2xvYWRpbmcnKS5yZW1vdmUoKTtcclxuXHRcdFx0fVxyXG5cdFx0XHRpZiAoIXNldHRpbmdzLm5vbnNob3dmYWRlaW4pIHtcclxuXHRcdFx0XHQkKCcjJyArIHNldHRpbmdzLmRpdklkKS5oaWRlKCkuaHRtbChtc2cpLmZhZGVJbig1MDApO1xyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdCQoJyMnICsgc2V0dGluZ3MuZGl2SWQpLmh0bWwobXNnKTtcclxuXHRcdFx0fVxyXG5cdFx0XHRpZiAoYWpheEVycm9ySGFuZGxlcnMubGVuZ3RoID4gMCkge1xyXG5cdFx0XHRcdGZvciAodmFyIGkgPSAwOyBpIDwgYWpheEVycm9ySGFuZGxlcnMubGVuZ3RoOyBpKyspIHtcclxuXHRcdFx0XHRcdGlmIChhamF4RXJyb3JIYW5kbGVyc1tpXS5mdW5jKSB7XHJcblx0XHRcdFx0XHRcdGFqYXhFcnJvckhhbmRsZXJzW2ldLmZ1bmMobXNnKTtcclxuXHRcdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRcdGFqYXhFcnJvckhhbmRsZXJzW2ldKG1zZyk7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdGFsZXJ0KCdBSkFYIGVycm9yOiAnICsgbXNnKTtcclxuXHRcdFx0XHRhamF4RXJyb3IgPSB0cnVlO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblx0fSk7XHJcblx0cmV0dXJuIGZhbHNlO1xyXG59XHJcblxyXG4vKipcclxuICogQUpBWCBzdWJwYWdlIGxvYWRlciB3aXRoIGhpc3Rvcnkgc3VwcG9ydFxyXG4gKiBAcGFyYW0ge3N0cmluZ30gaGFzaCBBIGhhc2gtYWRkcmVzcyBzdHJpbmdcclxuICogQHJldHVybiBGQUxTRSBvbiBzdWNjZXNzZnVsIEFKQVggY2FsbCwgVFJVRSBvbiBlcnJvciB0byBjb250aW51ZSBpblxyXG4gKiBzeW5jaHJvbm91cyBtb2RlXHJcbiAqIEB0eXBlIGJvb2xcclxuICovXHJcbmZ1bmN0aW9uIGFqYXhQYWdlTG9hZChoYXNoKSB7XHJcbiAgICBpZiAoaGFzaCAhPT0gJycpIHtcclxuXHRcdGhhc2gucmVwbGFjZSgvXiMvLCAnJyk7XHJcblx0fVxyXG5cdHZhciBtID0gaGFzaC5tYXRjaCgvXmdldCgtLio/KT87KC4qKSQvKTtcclxuXHRpZiAobSkge1xyXG5cdFx0Ly8gYWpheCBib29rbWFya1xyXG4gICAgICAgIHZhciB1cmwgPSBtWzJdLmluZGV4T2YoJzsnKSA+IDAgPyBtWzJdLnJlcGxhY2UoJzsnLCAnPycpIDogYWpheEN1cnJlbnRCYXNlICsgJz8nICsgZGVjb2RlVVJJQ29tcG9uZW50KG1bMl0pO1xyXG5cdFx0YWpheFVzZWQgPSB0cnVlO1xyXG5cdFx0cmV0dXJuIGFqYXhTZW5kKHtcclxuXHRcdFx0bWV0aG9kOiAnR0VUJyxcclxuXHRcdFx0dXJsOiB1cmwsXHJcblx0XHRcdGRpdklkOiBtWzFdID8gbVsxXS5zdWJzdHIoMSkgOiAnYWpheEJsb2NrJ1xyXG5cdFx0fSk7XHJcblx0fSBlbHNlIGlmIChoYXNoID09PSAnJyAmJiBhamF4VXNlZCkge1xyXG5cdFx0Ly8gYWpheCBob21lXHJcblx0XHRyZXR1cm4gYWpheFNlbmQgKHtcclxuXHRcdFx0dXJsOiBsb2NhdGlvbi5ocmVmLnJlcGxhY2UoLyMuKiQvLCAnJyksXHJcblx0XHRcdGRpdklkOiAnYWpheEJsb2NrJ1xyXG5cdFx0fSk7XHJcblx0fVxyXG5cdHJldHVybiB0cnVlO1xyXG59XHJcblxyXG4vKipcclxuICogQUpBWCBzdWJmb3JtIGxvYWRlciB3aXRob3V0IGhpc3RvcnkgdHJhY2tpbmdcclxuICogQHBhcmFtIHtzdHJpbmd9IGhhc2ggQSBoYXNoLWFkZHJlc3Mgc3RyaW5nXHJcbiAqIEBwYXJhbSB7c3RyaW5nfSBmb3JtSWQgVGFyZ2V0IGZvcm0gaWQgYXR0cmlidXRlXHJcbiAqIEByZXR1cm4gRkFMU0Ugb24gc3VjY2Vzc2Z1bCBBSkFYIGNhbGwsIFRSVUUgb24gZXJyb3IgdG8gY29udGludWUgaW5cclxuICogc3luY2hyb25vdXMgbW9kZVxyXG4gKiBAdHlwZSBib29sXHJcbiAqL1xyXG5mdW5jdGlvbiBhamF4Rm9ybUxvYWQoaGFzaCwgZm9ybUlkKSB7XHJcblx0dmFyIG0gPSBoYXNoLm1hdGNoKC9eKGdldHxwb3N0KSgtLio/KT87KC4qKSQvKTtcclxuXHRpZiAobSkge1xyXG5cdFx0Ly8gYWpheCBib29rbWFya1xyXG5cdFx0dmFyIHVybCA9IG1bM10uaW5kZXhPZignOycpID4gMCA/IG1bM10ucmVwbGFjZSgnOycsICc/JykgOiBhamF4Q3VycmVudEJhc2UgKyAnPycgKyBtWzNdO1xyXG5cdFx0YWpheFVzZWQgPSB0cnVlO1xyXG5cdFx0cmV0dXJuIGFqYXhTZW5kKHtcclxuXHRcdFx0bWV0aG9kOiBtWzFdLnRvVXBwZXJDYXNlKCksXHJcblx0XHRcdHVybDogdXJsLFxyXG5cdFx0XHRkaXZJZDogbVsyXSA/IG1bMl0uc3Vic3RyKDEpIDogJ2FqYXhCbG9jaycsXHJcblx0XHRcdGZvcm1JZDogZm9ybUlkXHJcblx0XHR9KTtcclxuXHR9XHJcblx0cmV0dXJuIHRydWU7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBDb25zdHJ1Y3RzIGFqYXhhYmxlIGhhc2ggc3RyaW5nXHJcbiAqIEBwYXJhbSB7c3RyaW5nfSBocmVmIExpbmsgaHJlZiBvciBmb3JtIGFjdGlvbiBhdHRyaWJ1dGVcclxuICogQHBhcmFtIHtzdHJpbmd9IHJlbCBBbiBhdHRyaWJ1dGUgdmFsdWUgcG9zc2libHkgY29udGFpbmluZyBhIGhhc2ggYWRkcmVzc1xyXG4gKiBAcGFyYW0ge3N0cmluZ30gZm9ybURhdGEgSXMgcGFzc2VkIGZvciBmb3JtcyBvbmx5LCBpcyAncG9zdCcgZm9yIFBPU1QgZm9ybXNcclxuICogb3Igc2VyaWFsaXplZCBmb3JtIGRhdGEgZm9yIEdFVCBmb3Jtc1xyXG4gKiBAcmV0dXJuIEEgdmFsaWQgaGFzaC1hZGRyZXNzIHN0cmluZ1xyXG4gKiBAdHlwZSBzdHJpbmdcclxuICovXHJcbmZ1bmN0aW9uIGFqYXhNYWtlSGFzaChocmVmLCByZWwsIGZvcm1EYXRhKSB7XHJcblx0dmFyIGhhc2ggPSAoZm9ybURhdGEgPT0gJ3Bvc3QnKSA/ICdwb3N0JyA6ICdnZXQnO1xyXG5cdHZhciBocmVmQmFzZSwgcGFyYW1zO1xyXG5cdHZhciBzZXAgPSAnPyc7XHJcblx0dmFyIG0gPSByZWwgPyByZWwubWF0Y2goLyhnZXR8cG9zdCkoLVteIDtdKyk/KDtcXFMqKT8vKSA6IGZhbHNlO1xyXG5cdGlmIChtKSB7XHJcblx0XHRoYXNoID0gbVsxXTtcclxuXHRcdGlmIChtWzJdKSB7XHJcblx0XHRcdGhhc2ggKz0gbVsyXTtcclxuXHRcdH1cclxuXHRcdGlmIChtWzNdKSB7XHJcblx0XHRcdGhyZWYgPSBtWzNdLnN1YnN0cigxKTtcclxuXHRcdFx0c2VwICA9ICc7JztcclxuXHRcdH1cclxuXHR9XHJcblx0aGFzaCArPSAnOydcclxuXHRpZiAoaHJlZi5pbmRleE9mKHNlcCkgPiAwKSB7XHJcblx0XHRocmVmQmFzZSA9IGhyZWYuc3Vic3RyKDAsIGhyZWYuaW5kZXhPZihzZXApKTtcclxuXHRcdHBhcmFtcyA9IGhyZWYuc3Vic3RyKGhyZWYuaW5kZXhPZihzZXApICsgMSk7XHJcblx0XHRpZiAoZm9ybURhdGEgJiYgZm9ybURhdGEgIT0gJ3Bvc3QnKSB7XHJcblx0XHRcdHBhcmFtcyArPSAnJicgKyBmb3JtRGF0YTtcclxuXHRcdH1cclxuXHR9IGVsc2Uge1xyXG5cdFx0aHJlZkJhc2UgPSBocmVmO1xyXG5cdFx0cGFyYW1zID0gJyc7XHJcblx0XHRpZiAoZm9ybURhdGEgJiYgZm9ybURhdGEgIT0gJ3Bvc3QnKSB7XHJcblx0XHRcdHBhcmFtcyArPSBzZXAgKyBmb3JtRGF0YTtcclxuXHRcdH1cclxuXHR9XHJcblx0aGFzaCArPSBocmVmQmFzZSA9PSBhamF4Q3VycmVudEJhc2UgPyBwYXJhbXMgOiBocmVmQmFzZSArICc7JyArIHBhcmFtcztcclxuXHRyZXR1cm4gaGFzaDtcclxufVxyXG5cclxuLyoqXHJcbiAqIFN0YW5kYXJkIGV2ZW50IGJpbmRpbmdzXHJcbiAqL1xyXG5mdW5jdGlvbiBiaW5kSGFuZGxlcnMoKSB7XHJcblx0aWYgKGxvY2F0aW9uLmhhc2ggPT0gJyNjb21tZW50cycgfHwgbG9jYXRpb24uaGFzaC5tYXRjaCgvI2NcXGQrLykpIHtcclxuXHRcdCQoJy5jb21tZW50cycpLmNzcygnZGlzcGxheScsICcnKTtcclxuXHR9XHJcblx0JCgnLmNvbW1lbnRzX2xpbmsnKS5jbGljayhmdW5jdGlvbigpIHtcclxuXHRcdGlmKCQoJy5jb21tZW50cycpLmNzcygnZGlzcGxheScpID09ICdub25lJykge1xyXG5cdFx0XHQkKCcuY29tbWVudHMnKS5jc3MoJ2Rpc3BsYXknLCAnJyk7XHJcblx0XHR9IGVsc2Uge1xyXG5cdFx0XHQkKCcuY29tbWVudHMnKS5jc3MoJ2Rpc3BsYXknLCAnbm9uZScpO1xyXG5cdFx0fVxyXG5cdH0pO1xyXG5cclxuXHRpZihsb2NhdGlvbi5ocmVmLm1hdGNoKC8jY29tbWVudHMvKSkge1xyXG5cdFx0JCgnLmNvbW1lbnRzJykuY3NzKCdkaXNwbGF5JywgJycpO1xyXG5cdH1cclxuXHJcblx0aWYgKGFqYXhFbmFibGVkKSB7XHJcblx0XHQvLyBBSkFYIGF1dG8taGFuZGxpbmdcclxuXHRcdCQoJ2JvZHknKS5vbignc3VibWl0JywgJ2Zvcm0uYWpheCcsIGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRpZiAoJCh0aGlzKS5hdHRyKCdtZXRob2QnKS50b1VwcGVyQ2FzZSgpID09ICdQT1NUJykge1xyXG5cdFx0XHRcdGFqYXhGb3JtTG9hZChhamF4TWFrZUhhc2goJCh0aGlzKS5hdHRyKCdhY3Rpb24nKS5yZXBsYWNlKC8jLiokLywgJycpLCAkKHRoaXMpLmF0dHIoJ2NsYXNzJyksICdwb3N0JyksICQodGhpcykuYXR0cignaWQnKSk7XHJcblx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0d2luZG93LmxvY2F0aW9uLmhhc2ggPSBhamF4TWFrZUhhc2goJCh0aGlzKS5hdHRyKCdhY3Rpb24nKS5yZXBsYWNlKC8jLiokLywgJycpLCAkKHRoaXMpLmF0dHIoJ2NsYXNzJyksICQodGhpcykuc2VyaWFsaXplKCkpO1xyXG5cdFx0XHR9XHJcblx0XHRcdHJldHVybiBhamF4RXJyb3I7XHJcblx0XHR9KTtcclxuXHRcdCQoJ2JvZHknKS5vbignY2xpY2snLCAnYS5hamF4JywgZnVuY3Rpb24oKSB7XHJcblx0XHRcdHdpbmRvdy5sb2NhdGlvbi5oYXNoID0gYWpheE1ha2VIYXNoKCQodGhpcykuYXR0cignaHJlZicpLnJlcGxhY2UoLyMuKiQvLCAnJyksICQodGhpcykuYXR0cigncmVsJykpO1xyXG5cdFx0XHRyZXR1cm4gYWpheEVycm9yO1xyXG5cdFx0fSk7XHJcblxyXG5cdFx0Ly8gQUpBWCBhY3Rpb24gY29uZmlybWF0aW9uc1xyXG5cdFx0JCgnYm9keScpLm9uKCdjbGljaycsICdhLmNvbmZpcm1MaW5rJywgZnVuY3Rpb24oKSB7XHJcblx0XHRcdGlmICgkKHRoaXMpLmF0dHIoJ2hyZWYnKS5tYXRjaCgvbWVzc2FnZS4rOTIwL2kpKSB7XHJcblx0XHRcdFx0aWYgKCQoJyNjb25maXJtQm94JykpIHtcclxuXHRcdFx0XHRcdCQoJyNjb25maXJtQm94JykucmVtb3ZlKCk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdCQoJ2JvZHknKS5wcmVwZW5kKCc8ZGl2IGlkPVwiY29uZmlybUJveFwiIGNsYXNzPVwianFtV2luZG93XCI+PC9kaXY+Jyk7XHJcblx0XHRcdFx0JCgnI2NvbmZpcm1Cb3gnKS5qcW0oe2FqYXg6JCh0aGlzKS5hdHRyKCdocmVmJyksbW9kYWw6dHJ1ZSxvbkxvYWQ6ZnVuY3Rpb24oKXtcclxuXHRcdFx0XHRcdCQoJyNjb25maXJtQm94JykuY3NzKCdtYXJnaW4tbGVmdCcsICctJysoJCgnI2NvbmZpcm1Cb3gnKS53aWR0aCgpLzIpKydweCcpO1xyXG5cdFx0XHRcdFx0JCgnI2NvbmZpcm1Cb3gnKS5jc3MoJ21hcmdpbi10b3AnLCAnLScrKCQoJyNjb25maXJtQm94JykuaGVpZ2h0KCkvMikrJ3B4Jyk7XHJcblx0XHRcdFx0fX0pO1xyXG5cdFx0XHRcdCQoJyNjb25maXJtQm94JykuanFtU2hvdygpO1xyXG5cdFx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRyZXR1cm4gdHJ1ZTtcclxuXHRcdFx0fVxyXG5cdFx0fSk7XHJcblxyXG5cdFx0Ly8gTGlzdGVuIHRvIGhhc2ggY2hhbmdlIGV2ZW50c1xyXG5cdFx0JCh3aW5kb3cpLm9uKCdoYXNoY2hhbmdlJywgZnVuY3Rpb24oKSB7XHJcblx0XHRcdGFqYXhQYWdlTG9hZCh3aW5kb3cubG9jYXRpb24uaGFzaC5yZXBsYWNlKC9eIy8sICcnKSk7XHJcblx0XHR9KTtcclxuXHJcblx0XHQkKCdib2R5Jykub24oJ2NsaWNrJywgJ2EjY29uZmlybU5vJywgZnVuY3Rpb24oKSB7XHJcblx0XHRcdGlmICgkKFwiI2NvbmZpcm1Cb3hcIikuaXMoXCIuanFtV2luZG93XCIpKVxyXG5cdFx0XHR7XHJcblx0XHRcdFx0JCgnI2NvbmZpcm1Cb3gnKS5qcW1IaWRlKCk7XHJcblx0XHRcdFx0JCgnI2NvbmZpcm1Cb3gnKS5yZW1vdmUoKTtcclxuXHRcdFx0XHRyZXR1cm4gZmFsc2U7XHJcblx0XHRcdH1cclxuXHRcdFx0ZWxzZVxyXG5cdFx0XHR7XHJcblx0XHRcdFx0cmV0dXJuIHRydWU7XHJcblx0XHRcdH1cclxuXHRcdH0pO1xyXG5cdH1cclxufVxyXG5cclxuaWYgKHR5cGVvZiBqUXVlcnkgIT0gJ3VuZGVmaW5lZCcpIHtcclxuICAgICQoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uKCkge1xyXG4gICAgICAgIC8vIElmIHBhZ2Ugd2FzIGxvYWRlZCB3aXRoIGhhc2hcclxuICAgICAgICBpZiAoYWpheEVuYWJsZWQpIHtcclxuICAgICAgICAgICAgaWYod2luZG93LmxvY2F0aW9uLmhhc2ggIT0gJycpIHtcclxuICAgICAgICAgICAgICAgIGFqYXhQYWdlTG9hZCh3aW5kb3cubG9jYXRpb24uaGFzaC5yZXBsYWNlKC9eIy8sICcnKSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGJpbmRIYW5kbGVycygpO1xyXG4gICAgfSk7XHJcbn1cclxuXHJcbndpbmRvdy5uYW1lID0gJ21haW4nOyJdfQ==
