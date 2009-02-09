/**
 * jQuery Simple Accordion
 * Original by Jan Järfalk (http://www.unwrongest.com/projects/accordion/)
 * Modified for use with Cotonti
 */
(function($){
	$.fn.extend({
		accordion: function() {
			return this.each(function() {
				if($(this).data('accordiated'))
					return false;

				$.each($(this).find('ul, li>div'), function(){
					$(this).data('accordiated', true);
					if(!$(this).hasClass('expanded'))
						$(this).hide();
				});
				$.each($(this).find('a'), function(){
					$(this).click(function(e){
						$(e.target).parent('li').toggleClass('active').siblings().removeClass('active').children('ul, div').slideUp('fast');
						$(e.target).siblings('ul, div').slideToggle('fast');
						return void(0);
					});
				});
			});
		}
	});
})(jQuery);