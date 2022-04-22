@extends('layouts.master')
@section('meta')
<meta name="description" content="{{$movie_detail['introduction']}}">
<meta name="keywords" content="<?php if (isset($movie)) echo $movie->meta; ?>{{$movie_detail['name']}} vietsub, {{$movie_detail['name']}} fullhd, {{$movie_detail['name']}} fullhd vietsub, {{$movie_detail['name']}}">
<meta name="robots" content="index, follow">
<meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta property="og:locale" content="vi_VN">
<meta property="og:type" content="website">
<meta property="og:title" content="{{$movie_detail['name']}} FullHD VietSub + Thuyết Minh">
<meta property="og:description" content="{{$movie_detail['introduction']}}">
<meta property="og:url" content="">
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
				<video class="movie__screen video-js" id="video_media" preload="auto" data-setup="{}" controls autoplay>
					<source src="{{$media['mediaUrl']}}" type="application/x-mpegURL">
					@foreach($movie_detail['episodeVo'][$episode_id]['subtitlingList'] as $subtitle)
					@if($subtitle['languageAbbr'] == 'vi')
					<track id="subtitles" kind="subtitles" label="{{$subtitle['language']}}" srclang="{{$subtitle['languageAbbr']}}" src="https://srt-to-vtt.vercel.app/?url={{$subtitle['subtitlingUrl']}}">
					@endif
					@endforeach
				</video>
				<div class="movie__load">
					<div id="loading_movie"></div>
				</div>
			</div>
			<div class="movie__name" id="{{$movie_detail['name']}}"><?php echo $movie_detail['episodeCount'] > 1 ? $movie_detail['name'].' - Tập '.($episode_id+ 1) :$movie_detail['name'] ?></div>
			<div class="movie__episodes">
				@if($movie_detail['episodeCount'] > 1)
				@foreach($movie_detail['episodeVo'] as $key => $episode)
				<a class="episode <?php echo intval($key) == intval($episode_id) ? 'active' : '' ?>" id="{{$key + 1}}" href="{{ route('detail_name_episode', ['name' => $movie->slug, 'episode_id' => $key + 1]) }}">{{$key + 1}} </a>
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
			<a class="similar__container" href="
				<?php
				$movie_check = App\Models\Movie::where('id', $movie['id'])->where('category', $movie['category'])->first();
				echo $movie_check == null ? route('movie.detail', ['category' => $movie['category'], 'id' => $movie['id'], 'name' => $movie['name']]) : route('detail_name', $movie_check->slug);
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
		$('.movie__media').height($('.movie__media').width() * 1080 / 1920);
		$('.movie__load').height($('.movie__media').height() + 5);

		video = videojs('video_media');
		getVideo = setInterval(restart, 1000);
		console.log(video);

		function restart() {
			if (video['cache_']['duration'] == 0 || !video['controls_'] || video['error_'] != null) {
				let episode_id = Number($('#media').attr('id_episode'));
				let definition = $('.movie__quality').children(":selected").attr("id");
				reload(episode_id, definition);
			} else {
				$('.movie__load').hide();
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
				video.src(data['mediaUrl']);
				return true;
			}).fail(function(e) {
				return false;
			});
		}




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