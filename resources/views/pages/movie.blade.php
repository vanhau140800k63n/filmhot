@extends('layouts.master')
@section('content')
<section class="movie">
	<div class="container">
		<div class="movie__container">
			<div class="movie__media">
				<input type="" name="" id="media" value="{{$media['mediaUrl']}}" class="hidden">
				<video controls class="movie__screen" autoplay> </video>
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
				<button class="episode" id="{{$movie_detail['id']}}" category="{{$movie_detail['category']}}" id_episode="{{$key}}" >{{$key + 1}} </button>
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
		
		$('.movie__play').click(function() {
			((source) => {
				if (typeof Hls == "undefined") return console.error("HLS Not Found");
				if (!document.querySelector("video")) return;
				var hls = new Hls();
				hls.loadSource(source);
				hls.attachMedia(document.querySelector("video"));
			})(document.getElementById('media').value);
			$('.movie__cover').css('display', 'none');
		})

		setInterval(function() {
			console.log($('video').attr('src'));
		},1000);


		$('.episode').click(function() {

			let episode_id = Number($(this).attr('id_episode')) + 1;

			let new_url = 'category='+ $(this).attr('category') + '&id=' + $(this).attr('id') + '&episode=' + episode_id;

			$('.episode').each(function() {
				$(this).css('background-color', '#27282e');
			})
			$(this).css('background', '#ed5829');
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
					id: $(this).attr('id'),
					category: $(this).attr('category'),
					episode_id: $(this).attr('id_episode'),
					_token: _token
				}
			}).done(function (data) {
				console.log(data['mediaUrl']);
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

				window.history.pushState({}, '', new_url);

				return true;
			}).fail(function (e) {
				return false;
			});
		})
	</script>
</section>
@endsection