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
			@foreach($search_advanced_list as $as_key => $as_container)
			<div class="as_name" id_key="{{$as_key}}">{{$as_container['name']}} <i class="fa-solid fa-caret-down"></i></div>

			<div class="as_container" id="as_container{{$as_key}}" params="{{$as_container['params']}}">
				@foreach($as_container['screeningItems'] as $key_screening_items => $screening_items)
				<div class="as_items" index="as_{{$screening_items['id']}}">
				@if($key_screening_items < 3) 
				<div class="as_items_name"> {{ __('search_advanced.'. $screening_items['name'])}}</div>
				@foreach($screening_items['items'] as $key_as_items=> $as_item)
				<div class="as_item" value="{{$as_item['params']}}" screening_type="{{$as_item['screeningType']}}" check="{{$as_key.'.'.$as_item['screeningType'].'#'.$as_item['params']}}">
				@if (trans()->has('search_advanced.detail.'.$as_item['name']))
				    {{ __('search_advanced.detail.'.$as_item['name'])}}
				@else
                    {{$as_item['name']}}
				@endif
				</div>
				@endforeach
				@endif
			</div>
			@endforeach
			<div class="close_search_advanced"> 
				<button class="close_search_advanced_btn">Đóng</button>
			</div>
		</div>
		@endforeach
	</div>
	</div>
	<div id="preloader">
		<div id="loader"></div>
	</div>
</header>
<script>
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
</script>