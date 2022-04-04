@extends('layouts.master')
@section('meta')
<title>TOPFILM</title>
@endsection
@section('content')
<div class="box homepage" id="1"> 
	<div class="listfilm">
		@foreach($movie_home['recommendItems'] as $keyRecommendItems => $recommendItems)
		@if($recommendItems['homeSectionType'] == 'BANNER' && sizeof($recommendItems['recommendContentVOList']) > 1)
		<div class="listfilm__top">
			<div class="categorys">
				<a href="{{route('searchcategory', 1)}}" class="home__category">Phim hành động</a>
				<a href="{{route('searchcategory', 19)}}" class="home__category">Khoa học viễn tưởng</a>
				<a href="{{route('searchcategory', 3)}}" class="home__category">Hoạt hình</a>
				<a href="{{route('searchcategory', 13)}}" class="home__category">Kinh dị</a>
				<a href="{{route('searchcategory', 5)}}" class="home__category">Hài kịch</a>
				<a href="{{route('searchcategory', 64)}}" class="home__category">Thảm khốc</a>
				<a href="{{route('searchcategory', 24)}}" class="home__category">Chiến tranh</a>
			</div>
			<div class="swiper__slider">
				<div class="swiper mySwiper">
					<div class="swiper-wrapper">
						@foreach($recommendItems['recommendContentVOList'] as $key => $banner)
						<div class="swiper-slide rounded-xl">
							<img class="object-cover w-full"
							src="{{ $banner['imageUrl']}}"  alt="image" />
						</div>
						@endforeach
					</div>
					<div class="swiper-button-next"></div>
					<div class="swiper-button-prev"></div>
					<div class="swiper-pagination"></div>
				</div>
			</div>
		</div>
		@endif
		@if($recommendItems['homeSectionType'] == 'SINGLE_ALBUM')
		<div class="recommend__items">
			<div class="recommend__items__title">
				<div class="recommend__items__name">
					<span>{{$recommendItems['homeSectionName']}}</span>
				</div>

				<a href="page=0.{{$keyRecommendItems}}" class="recommend__items__btn">	
					<h1> Xem thêm </h1>
					<svg xmlns="http://www.w3.org/2000/svg" class="arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
					</svg>
				</a>
			</div>
			<div class="recommend__item">
				<?php $image = Session('image')?Session::get('image'):[]; ?>
				@foreach($recommendItems['recommendContentVOList'] as $key => $movie)
				@if($key < 6)
				<a href="movies/category={{$movie['category']}}&id={{$movie['id']}}" class="card__film"> 
					<?php 
					$urlImage = 'img/'.$movie['category'].$movie['id'].'.jpg';
					if(!file_exists($urlImage)) {
						$urlImage = $movie['imageUrl'];
						$image[$movie['category'].$movie['id']] = $movie['imageUrl'];
					}
					?>
					<img class="image" src="{{$urlImage}}" alt="image" />
					<p class="film__name">{{$movie['title']}}</p>
				</a>
				@endif
				@endforeach
				<?php Session()->put('image', $image); ?>
			</div>
		</div>
		@endif	
		@endforeach
		<div class="text-center">
			<div class="lds-facebook"><div></div><div></div><div></div></div>
		</div>
	</div>
	<div class="top_search">
		<div class="top_search__title">Top tìm kiếm</div>
		<?php $image = Session('image')?Session::get('image'):[]; ?>
		@foreach($top_search['list'] as $movie)
		<a href="movies/category={{$movie['domainType']}}&id={{$movie['id']}}" class="top_search__card">
			<?php 
			$urlImage = 'img/'.$movie['domainType'].$movie['id'].'top_search.jpg';
			if(!file_exists($urlImage)) {
				$urlImage = $movie['cover'];
				$image[$movie['domainType'].$movie['id'].'top_search'] = $movie['cover'];	
			} 
			?>
			<img src="{{$urlImage}}" class="top_search__card__img">
			<div class="top_search__card__name">{{$movie['title']}}</div>
		</a>
		@endforeach
		<?php Session()->put('image', $image); ?>
	</div>
</div>

<script src="{{asset('js/home.js')}}"></script>
@endsection