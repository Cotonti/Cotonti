function plugin_ratings_send(form,rate_name,value,this_rate){   
        $.ajax({
                type: 'POST',
                url: $('#' + form).attr('action'),
                data: rate_name + '=' + value + '&' + $('#' + form).serialize(),
                success: function(msg) {

                        var index = Number(msg) > 0 ? Number(msg) - 1 : 0;

                        $('#' + rate_name).rating('select', index);
                        if(this_rate){
                            this_rate.rating('disable');
                        }
                },
                error: function(msg) {
                        alert('AJAX error: ' + msg);
                }
        });
}


$(document).on('ready ajaxSuccess', function() {
    
	$('.rstar').rating({
		callback: function(value, link) {
			var rate_name = $(this).attr('name');
                        var form = $(this).closest('form').attr('id');

                        if(value !== undefined && value !== 'undefined'){
                            plugin_ratings_send(form,rate_name,value,$(this));
                        }
			return false;
		}
	});
        
        $('.rating-cancel a').off('click');
        $('.rating-cancel a').on('click',function(){
            
            var form = $(this).closest('form').attr('id');
            var rate_code = form.replace('form_', '');
            $('input[type="radio"][name="rate_'+rate_code+'"').removeAttr('checked');
            plugin_ratings_send(form,'rate_'+rate_code,0);
          
        });
        
});
