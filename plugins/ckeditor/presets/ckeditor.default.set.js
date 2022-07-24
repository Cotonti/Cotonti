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
	for (let i in textareas) {
		let classList = textareas[i].classList;
		if (classList !== undefined && classList.length > 0) {
			for (let key in classList) {
				let textareaClass = classList[key];
				if (ckeditorClasses[textareaClass] !== undefined) {
					let textareasStyle = getComputedStyle(textareas[i], null) || textareas[i].currentStyle;
					CKEDITOR.replace(textareas[i], {
						height: textareasStyle.height,
						width:'100%',
						toolbar: ckeditorClasses[textareaClass]
					});
				}
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
