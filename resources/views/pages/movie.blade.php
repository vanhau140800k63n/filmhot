@extends('layouts.master')
@section('content')
<section class="movie">
	<div class="container">
		<div class="movie__container">
			<div class="movie__media">
				<input id="media" id_media="{{$movie_detail['id']}}" category="{{$movie_detail['category']}}" id_episode="{{$episode_id}}" class="hidden">
				<video controls class="movie__screen" autoplay id="video_media" crossorigin playsinline draggable="true">
					@foreach($movie_detail['episodeVo'][$episode_id]['subtitlingList'] as $subtitle)
					<track kind="subtitles" label="{{$subtitle['language']}}" srclang="{{$subtitle['languageAbbr']}}" src="https://srt-to-vtt.vercel.app/?url={{$subtitle['subtitlingUrl']}}" >
					@endforeach
				</video>
				<select class="movie__quality">
					@foreach($definitionList as $definition)
					<option id="{{$definition['code']}}">{{$definition['description']}}</option>
					@endforeach
				</select>
				<div class="movie__cover">
					<i class="fa-solid fa-play movie__play"></i>
				</div>

			</div>
			<div class="movie__name">{{$movie_detail['name']}}</div>
			<div class="movie__episodes">
				@foreach($movie_detail['episodeVo'] as $key => $episode)
				<button class="episode" id="{{$key}}" >{{$key + 1}} </button>
				@endforeach
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
			<div>

			</div>
		</div>
	</div>
	<script>
		$('.movie__cover').height($('video').height());

		$('.episode').each(function() {
			if($(this).attr('id') == $('#media').attr('id_episode'))
				$(this).css('background-color', '#ed5829');
		})

		
		$('.movie__play').click(function() {
			let episode_id = Number($('#media').attr('id_episode'));
			let new_url = 'category='+ $('#media').attr('category') + '&id=' + $('#media').attr('id_media') + '&episode=' + (episode_id + 1);

			load(episode_id, new_url);
		})

		var restart_media = setInterval(function() {
			var video = document.getElementById("video_media");
			console.log(video.readyState);
			if(video.readyState == 0) {
				let episode_id = Number($('#media').attr('id_episode'));
				let new_url = 'category='+ $('#media').attr('category') + '&id=' + $('#media').attr('id_media') + '&episode=' + (episode_id + 1);

				load(episode_id, new_url);
			} else {
				clearInterval(restart_media);
			}
		},2000);

		function load(episode_id, new_url) {
			$('.movie__cover').css('display', 'none');

			let _token = $('input[name="_token"]').val();
			$.ajax({
				url: 'episode-ajax',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded'
				},
				type: "POST",
				dataType: 'json',
				data: {
					id: $('#media').attr('id_media'),
					category: $('#media').attr('category'),
					episode_id: episode_id,
					_token: _token
				}
			}).done(function (data) {
				$('#media').val(data['mediaUrl']);
				$('#media').attr('id_episode', episode_id);

				window.history.pushState({}, '', new_url);

				((source) => {
					if (typeof Hls == "undefined") return console.error("HLS Not Found");
					if (!document.querySelector("video")) return;
					var hls = new Hls();
					hls.loadSource(source);
					hls.attachMedia(document.querySelector("video"));
				})(data['mediaUrl']);

				let str = '';
				for(let i = 0; i < data['0'].length; ++i) {
					str += '<option id="'+ data['0'][i]['code'] +'">'+ data['0'][i]['description'] +'</option>';
				}
				$('.movie__quality').html(str);

				return true;
			}).fail(function (e) {
				return false;
			});
		}


		$('.episode').click(function() {
			let episode_id = Number($(this).attr('id'));
			let new_url = 'category='+ $('#media').attr('category') + '&id=' + $('#media').attr('id_media') + '&episode=' + (episode_id + 1);

			$('.episode').each(function() {
				$(this).css('background-color', '#27282e');
			})
			$(this).css('background', '#ed5829');

			load(episode_id, new_url);
		})
	</script>
</section>
@endsection