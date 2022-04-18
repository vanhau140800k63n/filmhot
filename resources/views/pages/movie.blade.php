@extends('layouts.master')
@section('meta')
<meta name="description" content="{{$movie_detail['introduction']}}">
<meta name="keywords" content="<?php if(isset($movie)) echo $movie->meta; ?>{{$movie_detail['name']}} vietsub, {{$movie_detail['name']}} fullhd, {{$movie_detail['name']}} fullhd vietsub, {{$movie_detail['name']}}">
<meta name="robots" content="index, follow">
<meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta property="og:locale" content="vi_VN">
<meta property="og:type" content="website">
<meta property="og:title" content="{{$movie_detail['name']}} FullHD VietSub + Thuyết Minh">
<meta property="og:description" content="{{$movie_detail['introduction']}}">
<meta property="og:url" content="{{ route('movie.detail', ['category' => $movie_detail['category'], 'id' => $movie_detail['id']]) }}">
<meta property="og:site_name" content="{{$movie_detail['name']}} FullHD VietSub + Thuyết Minh">
<meta property="og:image" content="{{asset('img/'.$movie_detail['category'].$movie_detail['id'].'.jpg')}}">
<title>{{$movie_detail['name']}} FullHD VietSub + Thuyết Minh</title>
@endsection
@section('content')
<section class="movie">
	<div class="box advanced">
		<div class="movie__container">
			<div class="movie__media" id="movie__media">
				<input id="media" id_media="{{$movie_detail['id']}}" category="{{$movie_detail['category']}}" id_episode="{{$episode_id}}" class="hidden">
				<video class="movie__screen" id="video_media" autoplay crossorigin controls>
					@foreach($movie_detail['episodeVo'][$episode_id]['subtitlingList'] as $subtitle)
					@if($subtitle['languageAbbr'] == 'vi')
					<track id="subtitles" kind="subtitles" label="{{$subtitle['language']}}" srclang="{{$subtitle['languageAbbr']}}" src="https://srt-to-vtt.vercel.app/?url={{$subtitle['subtitlingUrl']}}">
					@endif
					@endforeach
				</video>
				<div class="movie__load">
					<i class="fa-solid fa-play movie__play"></i>
					<div id="loading_movie"></div>
				</div>
			</div>
			<div class="movie__name" id="{{$movie_detail['name']}}">{{$movie_detail['name']}}</div>
			<div class="movie__episodes">
				@if($movie_detail['episodeCount'] > 1)
				@foreach($movie_detail['episodeVo'] as $key => $episode)
				<button class="episode" id="{{$key}}">{{$key + 1}} </button>
				@endforeach
				@endif
			</div>
			<div class="movie__info">
				<div class="movie__score"> <i class="fa-solid fa-star"></i> {{$movie_detail['score']}}</div>
				<div class="movie__year"> <i class="fa-solid fa-calendar"></i> {{$movie_detail['year']}}</div>
			</div>
			<div class="movie__tag">
				@foreach($movie_detail['tagList'] as $item)
				<div class="tag__name" id_tag="{{$item['id']}}">
					@if (trans()->has('search_advanced.detail.'. $item['name']))
					{{ __('search_advanced.detail.'. $item['name'])}}
					@else
					{{$item['name']}}
					@endif
				</div>
				@endforeach
			</div>
			<div class="movie__intro">{{$movie_detail['introduction']}}</div>
		</div>
		<div class="movie__similar">
			<?php $image = Session('image') ? Session::get('image') : [];
			$movie_list = Session('movie_list') ? Session::get('movie_list') : []; ?>
			@foreach($movie_detail['likeList'] as $movie)
			<a class="similar__container" href="<?php
												$movie_check = App\Models\Movie::where('id', $movie['id'])->where('category', $movie['category'])->first();
												echo $movie_check == null ? route('movie.detail', ['category' => $movie['category'], 'id' => $movie['id']]) : route('detail_name', $movie_check->slug);
												?>">
				<?php
				$urlImage = 'img/' . $movie['category'] . $movie['id'] . '.jpg';
				if (!file_exists($urlImage)) {
					$urlImage = $movie['coverVerticalUrl'];
					$image[$movie['category'] . $movie['id']] = $movie['coverVerticalUrl'];
				}
				$movie_check = App\Models\Movie::where('id', $movie['id'])->where('category', $movie['category'])->first();
				if ($movie_check == null) {
					$movie_list[$movie['category'] . $movie['id']] = ['id' => $movie['id'], 'category' => $movie['category'], 'name' => $movie['name']];
				}
				?>
				<img src="{{asset($urlImage)}}">
				<div class="similar__name">{{$movie['name']}}</div>
			</a>
			@endforeach
			<?php Session()->put('image', $image);
			Session()->put('movie_list', $movie_list); ?>
		</div>
	</div>
</section>
<script>
	$(document).ready(function() {
		var video = document.getElementById('video_media');

		document.onkeydown = function(event) {
			switch (event.keyCode) {
				case 37:
					event.preventDefault();

					vid_currentTime = video.currentTime;
					video.currentTime = vid_currentTime - 5;
					break;

				case 39:
					event.preventDefault();

					vid_currentTime = video.currentTime;
					video.currentTime = vid_currentTime + 5;
					break;
			}
		};

		function restart() {
			console.log(video.readyState);
			if (video.readyState == 0) {
				let episode_id = Number($('#media').attr('id_episode'));
				let definition = $('.movie__quality').children(":selected").attr("id");
				reload(episode_id, definition);
			} else {
				if (video.textTracks.length == 1) {
					video.textTracks[0].mode = 'hidden';
				}
				if (video.textTracks.length == 1) {
					video.textTracks[0].mode = 'showing';
				}
				clearInterval(restart_media);
			}
		}


		$('.movie__similar img').css('max-height', $('.movie__similar img').width() * 1.4);
		$('.movie__media').height($('.movie__media').width() * 1080 / 1920);
		$('.movie__load').height($('.movie__media').height() + 5);

		$('.episode').each(function() {
			if ($(this).attr('id') == $('#media').attr('id_episode')) {
				$(this).css('background-color', '#ed5829');
			}
		})


		$('.movie__play').click(function() {
			$('.movie__play').css('display', 'none');
			$('#loading_movie').css('display', 'block');
			load();
		})

		function load() {
			$('.movie__load').css('display', 'block');

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
					episode_id: $('#media').attr('id_episode'),
					definition: null,
					_token: _token
				}
			}).done(function(data) {
				$('#media').val(data['mediaUrl']);
				window.history.pushState({}, '', data[2]);

				((source) => {
					if (typeof Hls == "undefined") return console.error("HLS Not Found");
					if (!document.querySelector("video")) return;
					var hls = new Hls();
					hls.loadSource(source);
					hls.attachMedia(document.querySelector("video"));
				})(data['mediaUrl']);

				// let movie__quality = '';
				// for(let i = 0; i < data['0'].length; ++i) {
				// 	movie__quality += '<option id="'+ data['0'][i]['code'] +'">'+ data['0'][i]['description'] +'</option>';
				// }
				// $('.movie__quality').html(movie__quality);

				let subtitle = '';
				for (let i = 0; i < data['1'].length; ++i) {
					if (data['1'][i]['languageAbbr'] == 'vi') {
						subtitle = '<track kind="subtitles" label="' + data['1'][i]['language'] + '" srclang="' + data['1'][i]['languageAbbr'] + '" src="https://srt-to-vtt.vercel.app/?url=' + data['1'][i]['subtitlingUrl'] + '" >';
					}
				}
				$('.movie__screen').html(subtitle);
				$('.movie__name').html($('.movie__name').attr('id') + data[3]);
				$('.movie__load').css('display', 'none');

				restart_media = setInterval(restart, 1000);

				return true;
			}).fail(function(e) {
				return false;
			});
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
				var video = document.getElementById("video_media");
				if (video.readyState != 0) {
					return true;
				}
				$('#media').val(data['mediaUrl']);

				((source) => {
					if (typeof Hls == "undefined") return console.error("HLS Not Found");
					if (!document.querySelector("video")) return;
					var hls = new Hls();
					hls.loadSource(source);
					hls.attachMedia(document.querySelector("video"));
				})(data['mediaUrl']);

				let subtitle = '';
				for (let i = 0; i < data['1'].length; ++i) {
					if (data['1'][i]['languageAbbr'] == 'vi') {
						subtitle = '<track kind="subtitles" label="' + data['1'][i]['language'] + '" srclang="' + data['1'][i]['languageAbbr'] + '" src="https://srt-to-vtt.vercel.app/?url=' + data['1'][i]['subtitlingUrl'] + '" >';
					}
				}
				$('.movie__screen').html(subtitle);

				return true;
			}).fail(function(e) {

				return false;
			});
		}

		function loadByDefinition(episode_id, definition) {

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
				$('#media').val(data['mediaUrl']);

				((source) => {
					if (typeof Hls == "undefined") return console.error("HLS Not Found");
					if (!document.querySelector("video")) return;
					var hls = new Hls();
					hls.loadSource(source);
					hls.attachMedia(document.querySelector("video"));
				})(data['mediaUrl']);

				let subtitle = '';
				for (let i = 0; i < data['1'].length; ++i) {
					if (data['1'][i]['languageAbbr'] == 'vi') {
						subtitle = '<track kind="subtitles" label="' + data['1'][i]['language'] + '" srclang="' + data['1'][i]['languageAbbr'] + '" src="https://srt-to-vtt.vercel.app/?url=' + data['1'][i]['subtitlingUrl'] + '" >';
					}
				}
				$('.movie__screen').html(subtitle);

				restart_media = setInterval(restart, 2000);

				return true;
			}).fail(function(e) {

				return false;
			});
		}


		$('.episode').click(function() {
			$('.episode').each(function() {
				$(this).css('background-color', '#27282e');
			})
			$(this).css('background', '#ed5829');
			$('#media').attr('id_episode', $(this).attr('id'));
			$('.movie__play').css('display', 'none');
			$('#loading_movie').css('display', 'block');

			load();
		})

		$('.movie__quality').change(function() {
			let episode_id = Number($('#media').attr('id_episode'));
			let definition = $(this).children(":selected").attr("id");

			loadByDefinition(episode_id, definition);
		})

		$('.tag__name').click(function() {
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
	})
</script>
@endsection