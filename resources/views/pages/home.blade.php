@extends('layouts.master')
@section('meta')
<meta name="description" content="Topfilm là website phát các chương trình truyền hình, phim, hoạt hình từ khắp nơi trên thế giới, với phụ đề vietsub và chất lượng hình ảnh fullhd, và các bộ phim mới được phát hành hàng ngày! - topfilm">
<meta name="keywords" content="topfilm, topphim, top film, top phim, phim vietsub, fullhd, full hd, phim moi nhat, phim hot, hen ho chon cong so, phim hay, top, film, hot phim, hot film, chieu rap, phim tam ly, devsne">
<meta name="robots" content="index, follow">
<meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta property="og:locale" content="vi_VN">
<meta property="og:type" content="website">
<meta property="og:title" content="TOPFILM - Xem phim FullHD Vietsub mới nhất">
<meta property="og:description" content="Topfilm là website phát các chương trình truyền hình, phim, hoạt hình từ khắp nơi trên thế giới, với phụ đề vietsub và chất lượng hình ảnh fullhd, và các bộ phim mới được phát hành hàng ngày! - topfilm">
<meta property="og:url" content="{{route('home')}}">
<meta property="og:site_name" content="TOPFILM - Xem phim FullHD Vietsub mới nhất">
<meta property="og:image" content="">
<title>TOPFILM - Xem phim FullHD Vietsub mới nhất</title>
@endsection
@section('content')
<div class="box homepage advanced" id="2">
	<?php
	$movieService = new \App\Services\MovieService();

	$url_movie = 'https://ga-mobile-api.loklok.tv/cms/app/homePage/getHome?page=0';
	$movie_home = $movieService->getData($url_movie);
	while ($movie_home == null) {
		$movie_home = $movieService->getData($url_movie);
	}

	$url_top = 'https://ga-mobile-api.loklok.tv/cms/app/search/v1/searchLeaderboard';
	$top_search = $movieService->getData($url_top);
	while ($top_search == null) {
		$top_search = $movieService->getData($url_top);
	}
	?>
	<div class="listfilm">
		@foreach($movie_home['recommendItems'] as $keyRecommendItems => $recommendItems)
		@if($recommendItems['homeSectionType'] == 'BANNER' && sizeof($recommendItems['recommendContentVOList']) > 1)
		<div class="listfilm__top">
			<div class="categorys">
				<a data="1" class="home__category">Phim hành động</a>
				<a data="19" class="home__category">Khoa học viễn tưởng</a>
				<a data="3" class="home__category">Hoạt hình</a>
				<a data="13" class="home__category">Kinh dị</a>
				<a data="5" class="home__category">Hài kịch</a>
				<a data="64" class="home__category">Thảm khốc</a>
				<a data="24" class="home__category">Chiến tranh</a>
			</div>
			<div class="swiper__slider">
				<div class="swiper mySwiper">
					<div class="swiper-wrapper">
						@foreach($recommendItems['recommendContentVOList'] as $key => $banner)
						<div class="swiper-slide rounded-xl">
							<img class="object-cover w-full" src="{{ $banner['imageUrl']}}" alt="image" />
						</div>
						@endforeach
					</div>
					<div class="swiper-button-next"></div>
					<div class="swiper-button-prev"></div>
					<div class="swiper-pagination"></div>
				</div>
			</div>
		</div>
		@endif
		@if($recommendItems['homeSectionType'] == 'SINGLE_ALBUM')
		<div class="recommend__items">
			<div class="recommend__items__title">
				<div class="recommend__items__name">
					<span>{{$recommendItems['homeSectionName']}}</span>
				</div>

				<a href="{{ route('moremovie', ['page' => 0, 'id' => $keyRecommendItems])}}" class="recommend__items__btn">
					Xem thêm
					<svg xmlns="http://www.w3.org/2000/svg" class="arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
					</svg>
				</a>
			</div>
			<div class="recommend__item">
				<?php $image = Session('image') ? Session::get('image') : [];
				$movie_list = Session('movie_list') ? Session::get('movie_list') : [];
				?>
				@foreach($recommendItems['recommendContentVOList'] as $key => $movie)
				@if($key < 6) <a href="<?php
										$movie_check = App\Models\Movie::where('id', $movie['id'])->where('category', $movie['category'])->first();
										echo $movie_check == null ? route('movie.detail', ['category' => $movie['category'], 'id' => $movie['id'], 'name' => $movie['title']]) : route('detail_name', $movie_check->slug);
										?>" class="card__film">
					<?php
					$urlImage = 'img/' . $movie['category'] . $movie['id'] . '.jpg';
					if (!file_exists($urlImage)) {
						$urlImage = $movie['imageUrl'];
						$image[$movie['category'] . $movie['id']] = $movie['imageUrl'];
					}
					$movie_check = App\Models\Movie::where('id', $movie['id'])->where('category', $movie['category'])->first();
					if ($movie_check == null) {
						$movie_list[$movie['category'] . $movie['id']] = ['id' => $movie['id'], 'category' => $movie['category'], 'name' => $movie['title']];
					}
					?>
					<img class="image" src="{{asset($urlImage)}}" alt="image" />
					<p class="film__name">{{$movie['title']}}</p>
					</a>
					@endif
					@endforeach
					<?php Session()->put('image', $image);
					Session()->put('movie_list', $movie_list);
					?>
			</div>
		</div>
		@endif
		@endforeach
		<div class="text-center">
			<div class="lds-facebook">
				<div></div>
				<div></div>
				<div></div>
			</div>
		</div>
	</div>
	<div class="top_search">
		<div class="top_search__title">Top tìm kiếm</div>
		<?php $image = Session('image') ? Session::get('image') : [];
		$movie_list = Session('movie_list') ? Session::get('movie_list') : [];
		?>
		@foreach($top_search['list'] as $movie)
		<a href="<?php
					$movie_check = App\Models\Movie::where('id', $movie['id'])->where('category', $movie['domainType'])->first();
					echo $movie_check == null ? route('movie.detail', ['category' => $movie['domainType'], 'id' => $movie['id'], 'name' => $movie['title']]) : route('detail_name', $movie_check->slug);
					?>" class="top_search__card">
			<?php
			$urlImage = 'img/' . $movie['domainType'] . $movie['id'] . 'top_search.jpg';
			if (!file_exists($urlImage)) {
				$urlImage = $movie['cover'];
				$image[$movie['domainType'] . $movie['id'] . 'top_search'] = $movie['cover'];
			}
			$movie_check = App\Models\Movie::where('id', $movie['id'])->where('category', $movie['domainType'])->first();
			if ($movie_check == null) {
				$movie_list[$movie['domainType'] . $movie['id']] = ['id' => $movie['id'], 'category' => $movie['domainType'], 'name' => $movie['title']];
			}
			?>
			<img src="{{asset($urlImage)}}" class="top_search__card__img">
			<div class="top_search__card__name">{{$movie['title']}}</div>
		</a>
		@endforeach
		<?php Session()->put('image', $image);
		Session()->put('movie_list', $movie_list);
		?>
	</div>
</div>
<script>
	$('.home__category').click(function() {
		array['category'] = $(this).attr('data');

		$('.box.advanced').html('');
		$('#preloader').show();

		let _token = $('input[name="_token"]').val();
		$.ajax({
			url: "{{ route('search_advanced') }}",
			type: "POST",
			dataType: 'json',
			data: {
				params: '',
				area: '',
				category: array['category'],
				year: '',
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
@endsection