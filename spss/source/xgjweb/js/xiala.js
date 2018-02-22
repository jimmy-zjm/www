

$(document).ready(function (){
	$('.selectbox').wrap('<div class="select_wrapper"></div>')
	$('.selectbox').parent().prepend('<span>'+$(this).find(':selected').text()+'</span>');
	$('.selectbox').parent().children('span').width($('.selectbox').width());	
	$('.selectbox').css('display', 'none');					
	$('.selectbox').parent().append('<ul class="select_inner"></ul>');
	$('.selectbox').children().each(function(){
		var opttext = $(this).text();
		var optval = $(this).val();
		$('.selectbox').parent().children('.select_inner').append('<li id="' + optval + '">' + opttext + '</li>');
	});

	$('.selectbox').parent().find('li').on('click', function (){
		var cur = $(this).attr('id');
		$('.selectbox').parent().children('span').text($(this).text());
		$('.selectbox').children().removeAttr('selected');
		$('.selectbox').children('[value="'+cur+'"]').attr('selected','selected');
		/*alert($('.selectbox').children('[value="'+cur+'"]').text());		*/		
		/*console.log($('.selectbox').children('[value="'+cur+'"]').text());*/
	});
	
	$('.selectbox').parent().on('click', function (){
		$(this).find('ul').slideToggle('fast');
	});
});


$(document).ready(function (){
	$('.selectbox1').wrap('<div class="select_wrapper"></div>')
	$('.selectbox1').parent().prepend('<span>'+'近三个月订单'+'</span>');
	$('.selectbox1').parent().children('span').width($('.selectbox').width());	
	$('.selectbox1').css('display', 'none');					
	$('.selectbox1').parent().append('<ul class="select_inner"></ul>');
	$('.selectbox1').children().each(function(){
		var opttext = $(this).text();
		var optval = $(this).val();
		$('.selectbox1').parent().children('.select_inner').append('<li id="' + optval + '">' + opttext + '</li>');
	});

	$('.selectbox1').parent().find('li').on('click', function (){
		var cur = $(this).attr('id');
		$('.selectbox1').parent().children('span').text($(this).text());
		$('.selectbox1').children().removeAttr('selected');
		$('.selectbox1').children('[value="'+cur+'"]').attr('selected','selected');
		/*alert($('.selectbox').children('[value="'+cur+'"]').text());		*/		
		/*console.log($('.selectbox').children('[value="'+cur+'"]').text());*/
	});
	
	$('.selectbox1').parent().on('click', function (){
		$(this).find('ul').slideToggle('fast');
	});
});

$(document).ready(function (){
	$('.selectbox2').wrap('<div class="select_wrapper"></div>')
	$('.selectbox2').parent().prepend('<span>'+'全部状态'+'</span>');
	$('.selectbox2').parent().children('span').width($('.selectbox').width());	
	$('.selectbox2').css('display', 'none');					
	$('.selectbox2').parent().append('<ul class="select_inner"></ul>');
	$('.selectbox2').children().each(function(){
		var opttext = $(this).text();
		var optval = $(this).val();
		$('.selectbox2').parent().children('.select_inner').append('<li id="' + optval + '">' + opttext + '</li>');
	});

	$('.selectbox2').parent().find('li').on('click', function (){
		var cur = $(this).attr('id');
		$('.selectbox2').parent().children('span').text($(this).text());
		$('.selectbox2').children().removeAttr('selected');
		$('.selectbox2').children('[value="'+cur+'"]').attr('selected','selected');
		/*alert($('.selectbox2').children('[value="'+cur+'"]').text());		*/		
		/*console.log($('.selectbox').children('[value="'+cur+'"]').text());*/
	});
	
	$('.selectbox2').parent().on('click', function (){
		$(this).find('ul').slideToggle('fast');
	});
});

$(document).ready(function (){
	$('.selectbox3').wrap('<div class="select_wrapper"></div>')
	$('.selectbox3').parent().prepend('<span>'+'近三个月订单'+'</span>');
	$('.selectbox3').parent().children('span').width($('.selectbox').width());	
	$('.selectbox3').css('display', 'none');					
	$('.selectbox3').parent().append('<ul class="select_inner"></ul>');
	$('.selectbox3').children().each(function(){
		var opttext = $(this).text();
		var optval = $(this).val();
		$('.selectbox3').parent().children('.select_inner').append('<li id="' + optval + '">' + opttext + '</li>');
	});

	$('.selectbox3').parent().find('li').on('click', function (){
		var cur = $(this).attr('id');
		$('.selectbox3').parent().children('span').text($(this).text());
		$('.selectbox3').children().removeAttr('selected');
		$('.selectbox3').children('[value="'+cur+'"]').attr('selected','selected');
		/*alert($('.selectbox2').children('[value="'+cur+'"]').text());		*/		
		/*console.log($('.selectbox').children('[value="'+cur+'"]').text());*/
	});
	
	$('.selectbox2').parent().on('click', function (){
		$(this).find('ul').slideToggle('fast');
	});
});

$(document).ready(function (){
	$('.selectbox4').wrap('<div class="select_wrapper"></div>')
	$('.selectbox4').parent().prepend('<span>'+'全部状态'+'</span>');
	$('.selectbox4').parent().children('span').width($('.selectbox').width());	
	$('.selectbox4').css('display', 'none');					
	$('.selectbox4').parent().append('<ul class="select_inner"></ul>');
	$('.selectbox4').children().each(function(){
		var opttext = $(this).text();
		var optval = $(this).val();
		$('.selectbox4').parent().children('.select_inner').append('<li id="' + optval + '">' + opttext + '</li>');
	});

	$('.selectbox4').parent().find('li').on('click', function (){
		var cur = $(this).attr('id');
		$('.selectbox4').parent().children('span').text($(this).text());
		$('.selectbox4').children().removeAttr('selected');
		$('.selectbox4').children('[value="'+cur+'"]').attr('selected','selected');
		/*alert($('.selectbox2').children('[value="'+cur+'"]').text());		*/		
		/*console.log($('.selectbox').children('[value="'+cur+'"]').text());*/
	});
	
	$('.selectbox4').parent().on('click', function (){
		$(this).find('ul').slideToggle('fast');
	});
});

$(document).ready(function (){
	$('.selectbox5').wrap('<div class="select_wrapper"></div>')
	$('.selectbox5').parent().prepend('<span>'+'近三个月订单'+'</span>');
	$('.selectbox5').parent().children('span').width($('.selectbox').width());	
	$('.selectbox5').css('display', 'none');					
	$('.selectbox5').parent().append('<ul class="select_inner"></ul>');
	$('.selectbox5').children().each(function(){
		var opttext = $(this).text();
		var optval = $(this).val();
		$('.selectbox5').parent().children('.select_inner').append('<li id="' + optval + '">' + opttext + '</li>');
	});

	$('.selectbox5').parent().find('li').on('click', function (){
		var cur = $(this).attr('id');
		$('.selectbox5').parent().children('span').text($(this).text());
		$('.selectbox5').children().removeAttr('selected');
		$('.selectbox5').children('[value="'+cur+'"]').attr('selected','selected');
		/*alert($('.selectbox2').children('[value="'+cur+'"]').text());		*/		
		/*console.log($('.selectbox').children('[value="'+cur+'"]').text());*/
	});
	
	$('.selectbox5').parent().on('click', function (){
		$(this).find('ul').slideToggle('fast');
	});
});

$(document).ready(function (){
	$('.selectbox6').wrap('<div class="select_wrapper"></div>')
	$('.selectbox6').parent().prepend('<span>'+'全部状态'+'</span>');
	$('.selectbox6').parent().children('span').width($('.selectbox').width());	
	$('.selectbox6').css('display', 'none');					
	$('.selectbox6').parent().append('<ul class="select_inner"></ul>');
	$('.selectbox6').children().each(function(){
		var opttext = $(this).text();
		var optval = $(this).val();
		$('.selectbox6').parent().children('.select_inner').append('<li id="' + optval + '">' + opttext + '</li>');
	});

	$('.selectbox6').parent().find('li').on('click', function (){
		var cur = $(this).attr('id');
		$('.selectbox6').parent().children('span').text($(this).text());
		$('.selectbox6').children().removeAttr('selected');
		$('.selectbox6').children('[value="'+cur+'"]').attr('selected','selected');
		/*alert($('.selectbox2').children('[value="'+cur+'"]').text());		*/		
		/*console.log($('.selectbox').children('[value="'+cur+'"]').text());*/
	});
	
	$('.selectbox6').parent().on('click', function (){
		$(this).find('ul').slideToggle('fast');
	});
});

$(document).ready(function (){
	$('.selectbox7').wrap('<div class="select_wrapper"></div>')
	$('.selectbox7').parent().prepend('<span>'+'近三个月订单'+'</span>');
	$('.selectbox7').parent().children('span').width($('.selectbox').width());	
	$('.selectbox7').css('display', 'none');					
	$('.selectbox7').parent().append('<ul class="select_inner"></ul>');
	$('.selectbox7').children().each(function(){
		var opttext = $(this).text();
		var optval = $(this).val();
		$('.selectbox7').parent().children('.select_inner').append('<li id="' + optval + '">' + opttext + '</li>');
	});

	$('.selectbox7').parent().find('li').on('click', function (){
		var cur = $(this).attr('id');
		$('.selectbox7').parent().children('span').text($(this).text());
		$('.selectbox7').children().removeAttr('selected');
		$('.selectbox7').children('[value="'+cur+'"]').attr('selected','selected');
		/*alert($('.selectbox2').children('[value="'+cur+'"]').text());		*/		
		/*console.log($('.selectbox').children('[value="'+cur+'"]').text());*/
	});
	
	$('.selectbox7').parent().on('click', function (){
		$(this).find('ul').slideToggle('fast');
	});
});

$(document).ready(function (){
	$('.selectbox8').wrap('<div class="select_wrapper"></div>')
	$('.selectbox8').parent().prepend('<span>'+'全部状态'+'</span>');
	$('.selectbox8').parent().children('span').width($('.selectbox').width());	
	$('.selectbox8').css('display', 'none');					
	$('.selectbox8').parent().append('<ul class="select_inner"></ul>');
	$('.selectbox8').children().each(function(){
		var opttext = $(this).text();
		var optval = $(this).val();
		$('.selectbox8').parent().children('.select_inner').append('<li id="' + optval + '">' + opttext + '</li>');
	});

	$('.selectbox8').parent().find('li').on('click', function (){
		var cur = $(this).attr('id');
		$('.selectbox8').parent().children('span').text($(this).text());
		$('.selectbox8').children().removeAttr('selected');
		$('.selectbox8').children('[value="'+cur+'"]').attr('selected','selected');
		/*alert($('.selectbox2').children('[value="'+cur+'"]').text());		*/		
		/*console.log($('.selectbox').children('[value="'+cur+'"]').text());*/
	});
	
	$('.selectbox8').parent().on('click', function (){
		$(this).find('ul').slideToggle('fast');
	});
});


