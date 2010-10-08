/* 
 * Default CKEditor preset and connector
 */

// Full editor
var ckeditorConfig = {
	toolbar: 'Full'
};

// Mini editor
var ckeditorMiniConfig = {
	toolbar: 'Basic'
};

function ckeditorReplace() {
	var textareas = document.getElementsByTagName('textarea');
	for (var i = 0; i < textareas.length; i++) {
		if (textareas[i].getAttribute('class') == 'editor') {
			CKEDITOR.replace(textareas[i], ckeditorConfig);
		} else if (textareas[i].getAttribute('class') == 'minieditor') {
			CKEDITOR.replace(textareas[i], ckeditorMiniConfig);
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
	$(document).ready(function () {
		$('textarea.editor').ckeditor(ckeditorConfig);
		$('textarea.minieditor').ckeditor(ckeditorMiniConfig);
	});
}