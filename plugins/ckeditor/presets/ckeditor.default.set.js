/**
 * Default CKEditor preset and connector
 */

var ckeditorClasses = {
	// Full editor
	'editor': 'Full',

	// Medium editor
	'medieditor': 'Medium',

	// Mini editor
	'minieditor': 'Basic',
};

function ckeditorReplace() {
	let textareas = document.getElementsByTagName('textarea');
	if (textareas === undefined || textareas.length === 0) {
		return
	}
	for (let textarea of textareas) {
		let classList = textarea.classList;
		if (classList === undefined || classList.length === 0) {
			continue;
		}
		for (let key of classList) {
			if (ckeditorClasses[key] !== undefined) {
				CKEDITOR.replace(textarea, {
					height: textarea.clientHeight, // or offsetHeight ?
					width:'100%',
					toolbar: ckeditorClasses[key]
				});
				break;
			}
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
