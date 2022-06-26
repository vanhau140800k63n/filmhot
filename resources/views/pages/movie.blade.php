@extends('layouts.master')
@section('meta')
<meta name="description" content="Xem phim {{$movie_detail->name}} FullHD Vietsub, {{$movie_detail->name}} tập 1, {{$movie_detail->name}} tập cuối - Xem phim ngay tại TopFilm.">
<meta name="keywords" content="topfilm, topphim, top film, top phim, phim vietsub, fullhd, full hd, phim moi nhat, phim hot, hen ho chon cong so, phim hay, top, film, hot phim, hot film, chieu rap, phim tam ly, devsne, {{$movie_detail->meta}}">
<meta name="robots" content="index, follow">
<meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta property="og:locale" content="vi_VN">
<meta property="og:type" content="website">
<meta property="og:title" content="{{$movie_detail->name}} - FullHD Vietsub + Thuyết Minh">
<meta property="og:description" content="Xem phim {{$movie_detail->name}} FullHD Vietsub, {{$movie_detail->name}} tập 1, {{$movie_detail->name}} tập cuối - Xem phim ngay tại TopFilm.">
<meta property="og:url" content="{{$url}}">
<meta property="og:site_name" content="{{$movie_detail->name}}">
<meta property="og:image" content="{{$movie_detail->image}}">
<title>{{$movie_detail->name}} - FullHD Vietsub + Thuyết Minh</title>
<style>
	.vjs-menu-item-text {
		text-transform: none;
	}
</style>
@endsection
@section('content')
<section class="movie">
	<div class="box advanced">
		<div class="movie__container">
			<div class="movie__media" id="movie__media">
				<input id="media" id_media="{{$movie_detail->id}}" category="{{$movie_detail->category}}" id_episode="{{$episode_id}}" class="hidden">
				<video class="movie__screen video-js" id="video_media" preload="auto" data-setup="{}" controls autoplay>
					<source src="movie" type="application/x-mpegURL">
					<track id="subtitles" kind="subtitles" label="Tiếng Việt" srclang="vi" src="{{$sub}}">
					<track id="subtitles" kind="subtitles" label="Tiếng Anh" srclang="en" src="{{$sub_en}}">
				</video>
				<div class="movie__load">
					<div id="loading_movie"></div>
				</div>
			</div>
			<h1 class="movie__name" id="{{$movie_detail['name']}}">{{$movie_detail->name}}</h1>
			<div class="movie__episodes">

			</div>
			<div class="movie__info">
				<div class="movie__score"> <i class="fa-solid fa-star"></i> {{$movie_detail->rate}}</div>
				<div class="movie__year"> <i class="fa-solid fa-calendar"></i> {{$movie_detail->year}}</div>
			</div>
			<div class="movie__tag">

			</div>
			<div class="movie__intro">{{$movie_detail->description}}</div>

			<div class="recommend__items__title">
				<div class="recommend__items__name" style="max-width: 100%; margin-top: 20px">
					<span>Phim ngẫu nhiên</span>
				</div>
			</div>
			<div class="recommend__item">
				@foreach($random_movies as $movie)
				<a href="{{route('detail_name', $movie->slug)}}" class="card__film">
					<?php 
					if($movie->image == '' || $movie->image == null) {
						$url_image = asset('img/'.$movie->category.$movie->id.'.jpg');
					} else {
						$url_image = $movie->image;
					}
					?>
					<img class="image" src="{{$url_image}}" alt="image" />
					<p class="film__name">{{$movie->name}}</p>
				</a>
				@endforeach
			</div>
		</div>
		<div class="movie__similar">
		</div>
	</div>

	<!-- <div class="box comments_hidden" style="display: none; margin-bottom: 20px">
		<div data-width="100%" class="fb-comments" data-href="{{$url}}" data-width="" data-numposts="5"></div>
	</div> -->
</section>
<section class="home__products">
	<div class="box">
		<div class="home__products__all" id="all__products">
			@foreach($productAll as $key)
			<div class="home__product">
				<div class="home__product__card">
					<p class="ribbon__shop">Yêu Thích +</p>
					<div class="ribbon__sale">
						<p data__price="{{$key->price}}" data__price__sale="{{$key->sale}}">10% Giảm</p>
					</div>
					<img src="{{$key->image}}" alt="" class="home__product-img">
					<img class="home__product-label_bottom" src="https://devsne.vn/source/img/a/voucher.png">
					<div class="home__product-content">
						<h3 class="home__product-title">
							{{$key->name}}
						</h3>
						<div class="home__product-bottom">
							<div class="home__product_price">
								<div class="home__product_price-default"><span>₫</span>{{number_format($key->price)}}</div>
								<div class="home__product_price-sale"><span>₫</span>{{number_format($key->sale)}}</div>
							</div>
							<div class="home__product-rating">
								<div class="number-rated">{{$key->rated}}</div>
								<div class="star-rated">
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
								</div>
								<div class="sold-qty">| Đã bán {{$key->sold}}</div>
							</div>
						</div>
					</div>
				</div>
				<?php $type = substr($key->categorie, 0, 5) ?>
				<div class="home__product__search">
					<a href="">Sản phẩm tương tự</a>
				</div>
			</div>
			@endforeach
		</div>
	</div>
</section>
<script>
	$(document).ready(function() {
		$('.movie__media').height($('.movie__media').width() * 1080 / 1920);
		$('.movie__load').height($('.movie__media').height() + 5);

		video = videojs('video_media');
		getVideo = setInterval(restart, 1000);

		document.onkeydown = function(event) {
			switch (event.keyCode) {
				case 37:
					event.preventDefault();
					vid_currentTime = video.currentTime();
					video.currentTime(vid_currentTime - 5);
					break;
				case 39:
					event.preventDefault();
					vid_currentTime = video.currentTime();
					video.currentTime(vid_currentTime + 5);
					break;
			}
		};

		video.seekButtons({
			forward: 10,
			back: 10
		});

		let _token = $('input[name="_token"]').val();
		$.ajax({
			url: "{{ route('movie.get-view-movie-ajax')}}",
			type: "POST",
			dataType: 'json',
			data: {
				name: "{{ $name }}",
				episode_id: "{{ $episode_id }}",
				_token: _token
			}
		}).done(function(data) {
			if ($('.movie__name').html() == '' || !data[8]) {
				window.location.href = data[6];
			} else {
				if (data[7]) {
					$('.movie__name').html($('.movie__name').html() + " - Tập " + "{{ $episode_id + 1}}");
				}
			}
			$('.movie__similar').html(data[1]);
			$('.comments_hidden').show();
			$('.home__products').show();
			$('.movie__episodes').html(data[4]);
			$('.movie__tag').html(data[5]);

			$('.tag__name').click(function() {
				$('.comments_hidden').remove();
				$('.home__products').remove();
				array['category'] = $(this).attr('id_tag');

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

			return true;
		}).fail(function(e) {
			return false;
		});

		function restart() {
			if (video['cache_']['duration'] == 0 || !video['controls_'] || video['error_'] != null || isNaN(video['cache_']['duration'])) {
				let episode_id = Number($('#media').attr('id_episode'));
				let definition = $('.movie__quality').children(":selected").attr("id");
				reload(episode_id, definition);
			} else {
				$('.movie__load').hide();
				$('.movie__intro').html($('.movie__intro').html() + video['cache_']['duration']);
				video.textTracks()[0].mode = 'showing';
				clearInterval(getVideo);
			}
		}

		function reload(episode_id, definition) {
			let _token = $('input[name="_token"]').val();
			$.ajax({
				url: "{{ route('movie.episode-ajax')}}",
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded'
				},
				type: "POST",
				dataType: 'json',
				data: {
					id: $('#media').attr('id_media'),
					category: $('#media').attr('category'),
					episode_id: episode_id,
					definition: definition,
					_token: _token
				}
			}).done(function(data) {
				if (video['cache_']['duration'] == 0 || !video['controls_'] || video['error_'] != null || isNaN(video['cache_']['duration'])) {
					video.src(data['mediaUrl']);
				}
				return true;
			}).fail(function(e) {
				return false;
			});
		}
	})
</script>
@endsection