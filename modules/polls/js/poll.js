$(".deloption").live("click",function () {
	$(this).parent().children('.tbox').attr('value', '');
	if (ansCount>2)
	{
		ansCount--;
		$(this).parent().remove();
	}
	if (ansCount<=ansMax)
	{
		$("#addoption").attr('disabled', '');
	}
	return false;
});

$(document).ready(function(){
	$("#addoption").click(function () {
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
	$('#addoption').show();
	$('.deloption').show();
});
