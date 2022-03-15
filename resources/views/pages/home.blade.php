@extends('layouts.master')
@section('content')
<section class="home">
	<div class="container"></div>
</section>
<div class="container test" id="1"> 
	<div class="w-full border-l border-l-gray-200 pb-[10px]">
		<div class="mt-6 mx-[5%] rounded-md listfilm">
			@foreach($convert['data']['recommendItems'] as $key => $recommendItems)
			@if($recommendItems['homeSectionType'] == 'SINGLE_ALBUM')
			<div class="mt-8">
				<div class="flex items-center mt-6 mb-2 justify-between">
					<div class="flex items-center gap-2 text-[24px] font-semibold">
						<div class="fade-loading"></div>
						<span>{{$recommendItems['homeSectionName']}}</span>
					</div>
					<div class="">
						<a href="{{ url ('comming-soon') }}">
							<button class="flex items-center gap-1 text-[16px] font-medium bg-pink-400 hover:bg-pink-300 text-white rounded-full px-2 py-1">
								<h1>Xem thêm</h1>
								<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
								</svg>
							</button>
						</a>
					</div>
				</div>
				<div class="grid grid-cols-5 gap-4">
					@foreach($recommendItems['recommendContentVOList'] as $key => $movie)
					<a href="movies/category={{$movie['category']}}&id={{$movie['id']}}" class="bg-[#27282d] rounded-xl"> 
						<?php 
						    $urlImage = 'img/'.$movie['category'].$movie['id'].'.jpg';
						    if(!file_exists($urlImage)) {
						    	$urlImage = $movie['imageUrl'];
						    }
						?>
						<img class="object-cover w-full h-[230px] rounded-t-xl"
						src="{{$urlImage}}" alt="image" />
						<div class="max-h-[40px] mx-4  text-ellipsis overflow-hidden">
							<h2 class="text-gray-100 py-1 text-[16px] whitespace-nowrap">{{$movie['title']}}</h2>
						</div>
					</a>
					@endforeach
				</div>
			</div>
			@endif	
			@endforeach
		</div>
	</div>
</div>

<script type="text/javascript">
	$(window).scroll(function() {
		value =  $('header').height() + $(".test").height() - $(window).scrollTop() - $(window).height();
		// console.log(value);
		if(value < 0) {
			let _token = $('input[name="_token"]').val();
			$.ajax({
				url: 'home-ajax',
				type: "POST",
				dataType: 'json',
				data: {
					page: $('.test').attr('id'),
					_token: _token
				}
			}).done(function (data) {
				$('.listfilm').html($('.listfilm').html() + data[0]);
				$('.test').attr('id', data[1]);

				return true;
			}).fail(function (e) {
				return false;
			});
		}

	});
</script>
@endsection