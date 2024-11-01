;(function($){
    $('.star').mouseenter(function(){
        $(this)
			.addClass('tmp_fs');
			//.prevAll().addClass('tmp_fs');
			//$(this).nextAll().addClass('tmp_es');
	});
	$('.star').mouseleave(function(){
        $(this)
			.removeClass('tmp_fs');
			//.prevAll().removeClass('tmp_fs');
		//$(this).nextAll().removeClass('tmp_es');
	});
	$('.star').click(function(){
        $(this)
			.addClass('fullStar');
			//.prevAll().addClass('fullStar');
			$(this).nextAll().removeClass('fullStar');
			$(this).prevAll().removeClass('fullStar');
		var rate = $(this).attr('title');
		$('#rateval').val(rate);
		
	});
    
	$('.rifancybox1').click(function(){
		if($(this).html()=='Login'){ $(this).html('X'); }else{ $(this).html('Login');  }
		$('#loginform_rate1').toggleClass('show');
		return false;
	});
           
   
    
})(jQuery);
