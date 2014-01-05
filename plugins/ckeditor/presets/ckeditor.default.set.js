/*
 * Default CKEditor preset and connector
 */

var ckeditorClasses = Array();
ckeditorClasses['editor'] = 'Full'; // Full editor
ckeditorClasses['medieditor'] = 'Medium'; // Medium editor
ckeditorClasses['minieditor'] = 'Basic'; // Mini editor

function ckeditorReplace() {
	var textareas = document.getElementsByTagName('textarea');
	for (var i = 0; i < textareas.length; i++) {
		if (ckeditorClasses[textareas[i].getAttribute('class')] !== undefined) {
			var textareasStyle = getComputedStyle(textareas[i], null) || textareas[i].currentStyle;
			CKEDITOR.replace(textareas[i], {height:textareasStyle.height, width:'100%', toolbar: ckeditorClasses[textareas[i].getAttribute('class')]});
		}
	}
}

if (typeof jQuery == 'undefined') {
	if (window.addEventListener) {
		window.addEventListener('load', ckeditorReplace, false);
	} else if (window.attachEvent) {
		window.attachEvent('onload', ckeditorReplace);
	} else {
		window.onload = ckeditorReplace;
	}
} else {
	$(document).ready(ckeditorReplace);
	ajaxSuccessHandlers.push(ckeditorReplace);
}
