@extends('layouts.master')
@section('meta')
<title>{{$movie_detail['name']}}</title>
@endsection
@section('content')
<section class="movie">
	<div class="box">
		<div class="movie__container">
			<div class="movie__media" id="movie__media">
				<input id="media" id_media="{{$movie_detail['id']}}" category="{{$movie_detail['category']}}" id_episode="{{$episode_id}}" class="hidden">
				<video class="movie__screen" id="video_media" autoplay crossorigin controls>
					@foreach($movie_detail['episodeVo'][$episode_id]['subtitlingList'] as $subtitle)
					@if($subtitle['languageAbbr'] == 'vi')
					<track id="subtitles" kind="subtitles" label="{{$subtitle['language']}}" srclang="{{$subtitle['languageAbbr']}}" src="https://srt-to-vtt.vercel.app/?url={{$subtitle['subtitlingUrl']}}" >
					@endif
					@endforeach
				</video>
				<i class="fa-solid fa-play movie__play"></i>
				<div class="movie__load">
					<div class="lds-ripple"><div></div><div></div></div>
				</div>
			</div>
			<div class="movie__name" id="{{$movie_detail['name']}}">{{$movie_detail['name']}}</div>
			<div class="movie__episodes">
				@if($movie_detail['episodeCount'] > 1)
				@foreach($movie_detail['episodeVo'] as $key => $episode)
				<button class="episode" id="{{$key}}" >{{$key + 1}} </button>
				@endforeach
				@endif
			</div>
			<div class="movie__info">
				<div class="movie__score"> <ion-icon name="star-half"></ion-icon> {{$movie_detail['score']}}</div>
				<div class="movie__year"> <ion-icon name="calendar-outline"></ion-icon> {{$movie_detail['year']}}</div>
			</div>
			<div class="movie__tag">
				@foreach($movie_detail['tagNameList'] as $item)
				<div class="tag__name">{{$item}}</div>
				@endforeach
			</div>
			<div class="movie__intro">{{$movie_detail['introduction']}}</div>
		</div>
		<div class="movie__similar">
			<?php $image = Session('image')?Session::get('image'):[]; ?>
			@foreach($movie_detail['likeList'] as $movie) 
			<a class="similar__container" href="{{ route('movie.detail', ['category' => $movie['category'], 'id' => $movie['id']]) }}">
				<?php 
				$urlImage = 'img/'.$movie['category'].$movie['id'].'.jpg';
				if(!file_exists($urlImage)) {
					$urlImage = $movie['coverVerticalUrl'];
					$image[$movie['category'].$movie['id']] = $movie['coverVerticalUrl'];
				}
				?>
				<img src="{{asset($urlImage)}}">
				<div class="similar__name">{{$movie['name']}}</div>
			</a>
			@endforeach
			<?php Session()->put('image', $image); ?>
		</div>
	</div>
</section>
<script>
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
		if(video.readyState == 0) {
			let episode_id = Number($('#media').attr('id_episode'));
			let definition = $('.movie__quality').children(":selected").attr("id");
			reload(episode_id, definition);
		} else {
            if(video.textTracks.length == 1) {
            	video.textTracks[0].mode = 'hidden';
            }
            if(video.textTracks.length == 1) {
            	video.textTracks[0].mode = 'showing';
            }
			clearInterval(restart_media);
		}
	}

	
	$('.movie__similar img').css('max-height', $('.movie__similar img').width()*1.4);
	$('.movie__media').height($('.movie__media').width() * 1080 / 1920);
	$('.movie__load').height($('.movie__media').height() + 5);

	$('.episode').each(function() {
		if($(this).attr('id') == $('#media').attr('id_episode')) {
			$(this).css('background-color', '#ed5829');
		}
	})

	
	$('.movie__play').click(function() {
		$('.movie__play').css('display', 'none');

		load();
	})

	function load() {
        $('.movie__load').css('display','flex');

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
		}).done(function (data) {
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
            for(let i = 0; i < data['1'].length; ++i) {
            	if(data['1'][i]['languageAbbr'] == 'vi') {
            		subtitle = '<track kind="subtitles" label="' + data['1'][i]['language'] +'" srclang="' + data['1'][i]['languageAbbr'] +'" src="https://srt-to-vtt.vercel.app/?url='+ data['1'][i]['subtitlingUrl'] +'" >';
            	}
            }
            $('.movie__screen').html(subtitle);
            $('.movie__name').html($('.movie__name').attr('id') + data[3]);
            $('.movie__load').css('display','none');

            restart_media = setInterval(restart, 2000);

			return true;
		}).fail(function (e) {
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
		}).done(function (data) {
			var video = document.getElementById("video_media");
			if(video.readyState != 0) {
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
            for(let i = 0; i < data['1'].length; ++i) {
            	if(data['1'][i]['languageAbbr'] == 'vi') {
            		subtitle = '<track kind="subtitles" label="' + data['1'][i]['language'] +'" srclang="' + data['1'][i]['languageAbbr'] +'" src="https://srt-to-vtt.vercel.app/?url='+ data['1'][i]['subtitlingUrl'] +'" >';
            	}
            }
            $('.movie__screen').html(subtitle);

			return true;
		}).fail(function (e) {

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
		}).done(function (data) {
			$('#media').val(data['mediaUrl']);

			((source) => {
				if (typeof Hls == "undefined") return console.error("HLS Not Found");
				if (!document.querySelector("video")) return;
				var hls = new Hls();
				hls.loadSource(source);
				hls.attachMedia(document.querySelector("video"));
			})(data['mediaUrl']);

			let subtitle = '';
            for(let i = 0; i < data['1'].length; ++i) {
            	if(data['1'][i]['languageAbbr'] == 'vi') {
            		subtitle = '<track kind="subtitles" label="' + data['1'][i]['language'] +'" srclang="' + data['1'][i]['languageAbbr'] +'" src="https://srt-to-vtt.vercel.app/?url='+ data['1'][i]['subtitlingUrl'] +'" >';
            	}
            }
            $('.movie__screen').html(subtitle);

			restart_media = setInterval(restart, 2000);

			return true;
		}).fail(function (e) {

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

		load();
	})

	$('.movie__quality').change(function() {
		let episode_id = Number($('#media').attr('id_episode'));
		let definition = $(this).children(":selected").attr("id");

		loadByDefinition(episode_id, definition);
	})
</script>
@endsection