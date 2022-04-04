var scroll = true;
$(window).scroll(function() {
	value =  $('header').height() + $(".homepage").height() - $(window).scrollTop() - $(window).height() - 50;
	if(value < 0 && scroll) {
		scroll = false;
		let _token = $('input[name="_token"]').val();
		$.ajax({
			url: 'home-ajax',
			type: "POST",
			dataType: 'json',
			data: {
				page: $('.homepage').attr('id'),
				width: $('.image').width(),
				_token: _token
			}
		}).done(function (data) {
			$('.lds-facebook').remove();
			$('.listfilm').html($('.listfilm').html() + data[0]);
			$('.homepage').attr('id', data[1]);

			setTimeout(function() {
				scroll = true;
			},2000);

			return true;
		}).fail(function (e) {
			return false;
		});
	}
});