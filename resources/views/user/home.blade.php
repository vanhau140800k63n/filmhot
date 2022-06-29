@extends('layouts.master')
@section('meta')
<meta name="description" content="Topfilm là website phát các chương trình truyền hình, phim, hoạt hình từ khắp nơi trên thế giới, với phụ đề vietsub và chất lượng hình ảnh fullhd, và các bộ phim mới được phát hành hàng ngày! - topfilm">
<meta name="keywords" content="topfilm, topphim, top film, top phim, phim vietsub, fullhd, full hd, phim moi nhat, phim hot, hen ho chon cong so, phim hay, top, film, hot phim, hot film, chieu rap, phim tam ly, devsne">
<meta name="robots" content="index, follow">
<meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta property="og:locale" content="vi_VN">
<meta property="og:type" content="website">
<meta property="og:title" content="TOPFILM - Xem phim FullHD Vietsub mới nhất">
<meta property="og:description" content="Topfilm là website phát các chương trình truyền hình, phim, hoạt hình từ khắp nơi trên thế giới, với phụ đề vietsub và chất lượng hình ảnh fullhd, và các bộ phim mới được phát hành hàng ngày! - topfilm">
<meta property="og:url" content="{{route('home')}}">
<meta property="og:site_name" content="TOPFILM - Xem phim FullHD Vietsub mới nhất">
<meta property="og:image" content="">
<title>TOPFILM - Xem phim FullHD Vietsub mới nhất</title>
@endsection
@section('content')
<div class="box homepage advanced" id="2">
    <div class="listfilm">
        <div class="listfilm__top" style="margin: 30px 0;">
            <div class="categorys">
                <a data="1" class="home__category">Phim hành động</a>
                <a data="19" class="home__category">Khoa học viễn tưởng</a>
                <a data="3" class="home__category">Hoạt hình</a>
                <a data="13" class="home__category">Kinh dị</a>
                <a data="5" class="home__category">Hài kịch</a>
                <a data="64" class="home__category">Thảm khốc</a>
                <a data="24" class="home__category">Chiến tranh</a>
            </div>
            <div class="swiper__slider">
                @if($user->banner != null)
                <img src="{{ asset($user->banner) }}">
                @else
                <img src="{{ asset('css/assets/images/banner/no-banner.jpg') }}">
                @endif
            </div>
        </div>
        <div class="lisfilm_body">
            <div class="recommend__items__title">
                <div class="recommend__items__name" style="max-width: 100%; margin-top: 20px">
                    <span>Phim của {{ $user->full_name }}</span>
                </div>
            </div>
            <div class="recommend__item">
                @foreach($movies as $movie)
                <a href="{{route('user.detail_name', ['name' => $movie->slug, 'id' => $user->id])}}" class="card__film">
                    <?php
                    if ($movie->image == '' || $movie->image == null) {
                        $url_image = asset('img/' . $movie->category . $movie->id . '.jpg');
                    } else {
                        $url_image = $movie->image;
                    }
                    ?>
                    <img class="image" src="{{$url_image}}" alt="image" />
                    <p class="film__name">{{$movie->name}}</p>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    <div class="top_search">
		<div class="top_search__title">Top tìm kiếm</div>
        <?php
        $top_search = \App\Models\Movie::whereNotNull('name')->inRandomOrder()->take(20)->get();
        ?>
		@foreach($top_search as $movie)
        @if(file_exists('img/' . $movie->category . $movie->id . '.jpg'))
		<a href="{{ route('detail_name', $movie->slug) }}" class="top_search__card">
			<img src="{{ asset($movie->image) }}" class="top_search__card__img">
			<div class="top_search__card__name">{{ $movie->name }}</div>
		</a>
        @endif
		@endforeach
	</div>
</div>

@endsection