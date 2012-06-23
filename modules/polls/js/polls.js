var ansCount = 1;
var ansMax = 100;
$(".deloption").live("click",function () {
	$(this).parent().children('.tbox').attr('value', '');
	if (ansCount>2)
	{
		ansCount--;
		$(this).parent().remove();
	}
	if (ansCount<=ansMax)
	{
		$("#addoption").removeAttr('disabled');
	}
	return false;
});
$("#addoption").live("click",function () {
	if (ansCount<ansMax)
	{			
		$('.polloptiondiv').last().clone().attr("id", '').insertAfter($('.polloptiondiv').last()).show().children('.tbox').attr('value', '');
		ansCount++;
	}
	if (ansCount>=ansMax)
	{
		$("#addoption").attr('disabled', 'disabled');
	}
	return false;
});
$(document).ready(function(){
	ansCount = $('.polloptiondiv').length;
	$('#addoption').show();
	$('.deloption').show();
});
