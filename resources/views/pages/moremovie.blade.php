@extends('layouts.master')
@section('meta')
<title>{{$result['homeSectionName']}}</title>
@endsection
@section('content')
<div class="box advanced"> 
	<div class="listfilm" style="width: 100%;">
		<div class="recommend__items">
			<div class="recommend__items__title">
				<div class="recommend__items__name">
					<span>{{$result['homeSectionName']}}</span>
				</div>
			</div>
			<div class="recommend__item">
				<?php $image = Session('image')?Session::get('image'):[]; ?>
				@foreach($result['recommendContentVOList'] as $movie)
				<a href="{{ route('movie.detail', ['category' => $movie['category'], 'id' => $movie['id']]) }}" class="card__film"> 
					<?php 
					$urlImage = 'img/'.$movie['category'].$movie['id'].'.jpg';
					if(!file_exists($urlImage)) {
						$urlImage = $movie['imageUrl'];
						$image[$movie['category'].$movie['id']] = $movie['imageUrl'];
					}
					?>
					<img class="image" src="{{asset($urlImage)}}" alt="image" />
					<p class="film__name">{{$movie['title']}}</p>
				</a>
				@endforeach
				<?php Session()->put('image', $image); ?>
			</div>
		</div>
	</div>
</div>
@endsection
