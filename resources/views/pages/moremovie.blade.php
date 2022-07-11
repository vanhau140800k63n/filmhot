@extends('layouts.master')
@section('meta')
<meta name="description" content="{{$result['homeSectionName']}} với các phim có phụ đề vietsub và chất lượng hình ảnh fullhd, và các bộ phim mới được phát hành hàng ngày! - topfilm">
<meta name="keywords" content="topfilm, topphim, top film, top phim, phim vietsub, fullhd, full hd, phim moi nhat, phim hot, hen ho chon cong so, phim hay, top, film, hot phim, hot film, chieu rap, phim tam ly, devsne">
<meta name="robots" content="index, follow">
<meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta property="og:locale" content="vi_VN">
<meta property="og:type" content="website">
<meta property="og:title" content="{{$result['homeSectionName']}} - Xem phim FullHD Vietsub">
<meta property="og:description" content="{{$result['homeSectionName']}} với các phim có phụ đề vietsub và chất lượng hình ảnh fullhd, và các bộ phim mới được phát hành hàng ngày! - topfilm">
<meta property="og:url" content="https://topfilm.devsne.vn/">
<meta property="og:site_name" content="topfilm">
<meta property="og:image" content="">
<title>{{$result['homeSectionName']}} - Xem phim FullHD Vietsub</title>
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
				<?php $image = Session('image') ? Session::get('image') : [];
				$movie_list = Session('movie_list') ? Session::get('movie_list') : []; ?>
				@foreach($result['recommendContentVOList'] as $movie)
				<a href="<?php
							$movie_check = App\Models\Movie::where('id', $movie['id'])->where('category', $movie['category'])->first();
							echo $movie_check == null ? route('movie.detail', ['category' => $movie['category'], 'id' => $movie['id'], 'name' => $movie['title']]) : route('detail_name', $movie_check->slug);
							?>" class="card__film">
					<?php
					$urlImage = 'img/' . $movie['category'] . $movie['id'] . '.jpg';
					if (!file_exists($urlImage)) {
						$urlImage = $movie['imageUrl'];
						$image[$movie['category'] . $movie['id']] = $movie['imageUrl'];
					}
					$movie_check = App\Models\Movie::where('id', $movie['id'])->where('category', $movie['category'])->first();
					if ($movie_check == null) {
						$movie_list[$movie['category'] . $movie['id']] = ['id' => $movie['id'], 'category' => $movie['category'], 'name' => $movie['title']];
					}
					?>
					<img class="image" src="{{asset($urlImage)}}" alt="image" />
					<p class="film__name">{{$movie['title']}}</p>
				</a>
				@endforeach
				<?php Session()->put('image', $image);
				Session()->put('movie_list', $movie_list); ?>
			</div>
		</div>
	</div>
</div>
@endsection