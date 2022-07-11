var scroll = true;
$(window).scroll(function () {
	if ($('.box').hasClass('homepage')) {
		value = $('header').height() + $(".homepage").height() - $(window).scrollTop() - $(window).height() - 50;
		if (value < 0 && scroll) {
			scroll = false;
			let _token = $('input[name="_token"]').val();
			$.ajax({
				url: "{{route('home-ajax')}}",
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

				setTimeout(function () {
					scroll = true;
				}, 1500);

				return true;
			}).fail(function (e) {
				return false;
			});
		}
	}
	if ($('.box').hasClass('search_advanced_film')) {
		value = $('header').height() + $(".search_advanced_film").height() - $(window).scrollTop() - $(window).height() - 50;
		if (value < 0 && scroll) {
			alert(1);
			scroll = false;
			let _token = $('input[name="_token"]').val();
			$.ajax({
				url: "{{ route('search_advanced') }}",
				type: "POST",
				dataType: 'json',
				data: {
					params: $('#as_container' + array['as']).attr('params'),
					area: array['area'],
					category: array['category'],
					year: array['year'],
					sort: array['sort'],
					_token: _token
				}
			}).done(function (data) {
				$('.lds-facebook').remove();
				$('.listfilm').html($('.listfilm').html() + data[0]);
				$('.homepage').attr('id', data[1]);

				setTimeout(function () {
					scroll = true;
				}, 1500);

				return true;
			}).fail(function (e) {
				return false;
			});
		}
	}
});