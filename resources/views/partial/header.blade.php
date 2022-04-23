<header>
	<div class="box">
		<div class="logo"><a href="{{route('home')}}">TOPFILM</a></div>
		<div class="search">
			<form action="{{route('key-search')}}" method="post">
				@csrf
				<input type="text" name="keyword" placeholder="Tên phim ..." class="search__input">
				<button class="search__btn">Tìm kiếm</button>
			</form>
		</div>
		<?php
		$movieService = new \App\Services\MovieService();
		$search_advanced_list = $movieService->searchAdvancedList();
		while ($search_advanced_list == null) {
			$search_advanced_list = $movieService->searchAdvancedList();
		}
		?>
		<div class="advanced_search">

		</div>
	</div>
	<div id="preloader">
		<div id="loader"></div>
	</div>
</header>
<script>
	$.ajax({
		url: "{{ route('header-ajax') }}",
		type: "GET",
		dataType: 'json',
	}).done(function(data) {
		$('.advanced_search').html(data);
		array = {
			'as': '',
			'area': '',
			'category': '',
			'year': ''
		};

		$('.as_name').hover(function() {
			$('.as_container').each(function() {
				$(this).hide();
			})
			$('.as_name').each(function() {
				$(this).css('color', '#fff');
				$(this).css('background', 'none');
			})
			$('#as_container' + $(this).attr('id_key')).show();
			$(this).css('color', '#000');
			$(this).css('background', '#fff');
		}, function() {
			if ($('#as_container' + $(this).attr('id_key') + ':hover').length == 0) {
				$('#as_container' + $(this).attr('id_key')).hide();
				$(this).css('color', '#fff');
				$(this).css('background', 'none');
			}
		})
		$('.as_container').mouseout(function() {
			if ($('.as_container div:hover').length == 0) {
				$(this).hide();
				$('.as_name').each(function() {
					$(this).css('color', '#fff');
					$(this).css('background', 'none');
				})
			}
		})

		$('.close_search_advanced').click(function() {
			$(this).parent().hide();
			$('.as_name').each(function() {
				$(this).css('color', '#fff');
				$(this).css('background', 'none');
			})
		})

		$('.as_item').click(function() {
			value = $(this).attr('check');
			as_new = value.slice(0, value.indexOf('.'));
			as_name = value.slice(value.indexOf('.') + 1, value.indexOf('#'));
			as_id = value.slice(value.indexOf('#') + 1, value.length);

			if (as_new != array['as']) {
				$.each(array, function(key, value) {
					if (key != 'as') {
						$("[check='" + array['as'] + "." + key + "#" + value + "']").removeClass('active');
					}
				});
				array = {
					'as': '',
					'area': '',
					'category': '',
					'year': ''
				};
				array['as'] = as_new;
				array[as_name] = as_id;
			} else {
				$("[check='" + array['as'] + "." + as_name + "#" + array[as_name] + "']").removeClass('active');
				array[as_name] = as_id;
			}
			$(this).addClass('active');

			$('.box.advanced').html('');
			$('#preloader').show();

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
					_token: _token
				}
			}).done(function(data) {
				$('.box.advanced').removeClass('homepage').addClass('search_advanced_film');
				$('.box.search_advanced_film').html(data[0]);
				if (data[1] < 18) {
					$('.lds-facebook').remove();
				}
				$('#preloader').hide();
				return true;
			}).fail(function(e) {
				$('.box.advanced').removeClass('homepage').addClass('search_advanced_film');
				$('.box.search_advanced_film').html('<div style="padding-top: 30px; font-weight: 600; font-size: 20px">Không tìm thấy phim</div>');
				$('#preloader').hide();
				return false;
			});
		})
		return true;
	}).fail(function(e) {
		return false;
	});
</script>