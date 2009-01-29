function popup(code, w, h){
    window.open('plug.php?o=' + code, '', 'toolbar=0,location=0,directories=0,menuBar=0,resizable=0,scrollbars=yes,width=' + w + ',height=' + h + ',left=32,top=16');
}

function pfs(id, c1, c2){
    window.open('pfs.php?userid=' + id + '&c1=' + c1 + '&c2=' + c2, 'PFS', 'status=1, toolbar=0,location=0,directories=0,menuBar=0,resizable=1,scrollbars=yes,width=754,height=512,left=32,top=16');
}

function help(rcode, c1, c2){
    window.open('plug.php?h=' + rcode + '&c1=' + c1 + '&c2=' + c2, 'Help', 'toolbar=0,location=0,directories=0,menuBar=0,resizable=0,scrollbars=yes,width=480,height=512,left=32,top=16');
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
			$('#' + settings.divId).append('<div style="position:relative;left:' + ($('#' + settings.divId).width()/2 - 16) + 'px;top:-' + ($('#' + settings.divId).height()/2 - 16) + 'px;" class="loading" id="loading"><img src="images/spinner_bigger.gif" alt="loading"/></div>');
		},
		success: function(msg) {
			$('#loading').remove();
			$('#' + settings.divId).html(msg).hide().stop().fadeIn('slow');
		},
		error: function(msg) {
			$('#loading').remove();
			alert(settings.errMsg);
		}
	});
	return false;
}

$(document).ready(function() {
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
});

window.name = 'main';
