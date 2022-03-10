@extends('layouts.master')
@section('content')
<section class="movie">
	<div class="container">
		<div class="movie__container">
			<input type="" name="" id="media" value="{{$media['mediaUrl']}}" class="hidden">
			<video controls class="movie__screen"></video>
			<div class="movie__name">{{$movie_detail['name']}}</div>
			<div class="movie__episodes">
				@foreach($movie_detail['episodeVo'] as $key => $episode)
				<button class="episode">{{$key + 1}} </button>
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
		setTimeout(function() {
			((source) => {
				if (typeof Hls == "undefined") return console.error("HLS Not Found");
				if (!document.querySelector("video")) return;
				var hls = new Hls();
				hls.loadSource(source);
				hls.attachMedia(document.querySelector("video"));
			})(document.getElementById('media').value);
		}, 500);

		$('.episode').click(function() {
			alert(1);
		})
	</script>
</section>
@endsection