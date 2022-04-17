@extends('layouts.master')
@section('meta')
<title>Tìm kiếm: {{$key}}</title>
@endsection
@section('content')
<div class="box advanced">
	<div class="listfilm" style="width: 100%;">
		<div class="recommend__items">
			<div class="recommend__items__title">
				<div class="recommend__items__name" style="max-width: 100%">
					<span>Tìm kiếm cho từ khóa: {{$key}}</span>
				</div>
			</div>
			<div class="recommend__item">
				<?php $image = Session('image') ? Session::get('image') : [];
				$movie_list = Session('movie_list') ? Session::get('movie_list') : []; ?>
				@foreach($movieSearchWithKey['searchResults'] as $movie)
				<a href="<?php
							$movie_check = App\Models\Movie::where('id', $movie['id'])->where('category', $movie['domainType'])->first();
							echo $movie_check == null ? route('movie.detail', ['category' => $movie['domainType'], 'id' => $movie['id']]) : route('movie.detail_name', $movie_check->slug);
							?>" class="card__film">
					<?php
					$urlImage = 'img/' . $movie['domainType'] . $movie['id'] . '.jpg';
					if (!file_exists($urlImage)) {
						$urlImage = $movie['coverVerticalUrl'];
						$image[$movie['domainType'] . $movie['id']] = $movie['coverVerticalUrl'];
					}
					$movie_check = App\Models\Movie::where('id', $movie['id'])->where('category', $movie['domainType'])->first();
					if ($movie_check == null) {
						$movie_list[$movie['domainType'] . $movie['id']] = ['id' => $movie['id'], 'category' => $movie['domainType'], 'name' => $movie['name']];
					}
					?>
					<img class="image" src="{{asset($urlImage)}}" alt="image" />
					<p class="film__name">{{$movie['name']}}</p>
				</a>
				@endforeach
				<?php Session()->put('image', $image);
				Session()->put('movie_list', $movie_list); ?>

			</div>
		</div>
	</div>
</div>
@endsection