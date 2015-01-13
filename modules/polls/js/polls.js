/**
 * Polls
 *
 * @package Polls
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
var ansCount = 1;
var ansMax = 100;
$( document ).on( "click", ".deloption", function(e) {
	e.preventDefault();

	$(this).parent().children('.tbox').attr('value', '');
	var optCount = $('.polloptiondiv').length;
	if (optCount > 1) {
		ansCount--;
		$(this).parents('.polloptiondiv').remove();
	}
	if (optCount <= ansMax) {
		$("#addoption").removeAttr('disabled');
	}
	return false;
});

$( document ).on( "click", "#addoption", function(e) {
	e.preventDefault();

	var optCount = $('.polloptiondiv').length;
	if (optCount <= ansMax) {
		var newOption = $('.polloptiondiv').last().clone().attr("id", '').insertAfter($('.polloptiondiv').last()).show();
		newOption.find('.tbox').attr('value', '');
		newOption.find('input[type="text"]').val('');
		ansCount++;
	}
	if (optCount >= ansMax) {
		$("#addoption").attr('disabled', 'disabled');
	}

	ansCount = $('.polloptiondiv').length;
	return false;
});

$(document).ready(function(){
	ansCount = $('.polloptiondiv').length;
	$('#addoption').show();
	$('.deloption').show();
});