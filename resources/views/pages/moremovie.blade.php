@extends('layouts.master')
@section('content')
<section class="home">
	<div class="container"></div>
</section>
<div class="container"> 
	<div class="w-full pb-[10px]">
		<div class="mt-6 rounded-md listfilm">
			<div class="mt-8">
				<div class="text-[30px] font-semibold mb-6"> {{$result['homeSectionName']}}</div>
				<div class="grid grid-cols-6 gap-4">
					<?php $image = Session('image')?Session::get('image'):[]; ?>
					@foreach($result['recommendContentVOList'] as $movie)
					<a href="movies/category={{$movie['category']}}&id={{$movie['id']}}" class="bg-[#27282d] rounded-xl card__film"> 
						<?php 
						    $urlImage = 'img/'.$movie['category'].$movie['id'].'.jpg';
						    if(!file_exists($urlImage)) {
						    	$urlImage = $movie['imageUrl'];
						    	$image[$movie['category'].$movie['id']] = $movie['imageUrl'];
						    }
						?>
						<img class="object-cover w-full rounded-t-xl image"
						src="{{$urlImage}}" alt="image" />
						<div class="mx-4  text-center">
							<h2 class="text-gray-100 py-1 text-[14px] film__name">{{$movie['title']}}</h2>
						</div>
					</a>
					@endforeach
					<?php Session()->put('image', $image); ?>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection