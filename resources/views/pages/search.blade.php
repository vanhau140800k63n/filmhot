@extends('layouts.master')
@section('content')
<section class="home">
	<div class="container"></div>
</section>
<div class="container"> 
	<div class="w-full pb-[10px]">
		<div class="mt-6 mx-[5%] rounded-md listfilm">
			<div class="mt-8">
				<div class="grid grid-cols-6 gap-4">
					<?php $image = Session('image')?Session::get('image'):[]; ?>
					@foreach($convert['data']['searchResults'] as $movie)
					<a href="movies/category={{$movie['domainType']}}&id={{$movie['id']}}" class="bg-[#27282d] rounded-xl card__film"> 
						<?php 
						    $urlImage = 'img/'.$movie['domainType'].$movie['id'].'.jpg';
						    if(!file_exists($urlImage)) {
						    	$urlImage = $movie['coverVerticalUrl'];
						    	$image[$movie['domainType'].$movie['id']] = $movie['coverVerticalUrl'];
						    }
						?>
						<img class="object-cover w-full rounded-t-xl"
						src="{{$urlImage}}" alt="image" />
						<div class="max-h-[40px] mx-4  text-ellipsis overflow-hidden">
							<h2 class="text-gray-100 py-1 text-[16px] whitespace-nowrap">{{$movie['name']}}</h2>
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