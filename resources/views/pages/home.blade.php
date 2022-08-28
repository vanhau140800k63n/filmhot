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
<meta property="og:image" content="{{ asset('css/assets/images/banner/no-banner.jpg') }}">
<title>TOPFILM - Xem phim FullHD Vietsub mới nhất</title>
@endsection
@section('content')
<button class="btn btn-primary"></button>
<div class="box homepage advanced" id="2">
	<div class="loader_home">
		<div class="inner one"></div>
		<div class="inner two"></div>
		<div class="inner three"></div>
	</div>
</div>
<script>
	$('.overlay-bg').show();
	$('.overlay-content').css('display', 'flex');
	$.ajax({
		url: "{{ route('load_first_home_ajax') }}",
		type: "GET",
		dataType: 'json',
	}).done(function(data) {
		$('.homepage.advanced').html(data);

		let swiper__slider_img_width = $('.swiper__slider img').width();
		let swiper__slider_img_height = $('.swiper__slider img').height();

		let position = (swiper__slider_img_width - swiper__slider_img_height / 2.5) / 2;

		$('.swiper__slider img').css('object-position', '0px -' + position + 'px');

		$('.loader_home').remove();

		var swiper = new Swiper(".mySwiper", {
			cssMode: true,
			navigation: {
				nextEl: ".swiper-button-next",
				prevEl: ".swiper-button-prev",
			},
			pagination: {
				el: ".swiper-pagination",
			},
			mousewheel: true,
			keyboard: true,
		});

		$('.home__category').click(function() {
			array['category'] = $(this).attr('data');

			$('.box.advanced').html('');
			$('#preloader').show();

			let _token = $('input[name="_token"]').val();
			$.ajax({
				url: "{{ route('search_advanced') }}",
				type: "POST",
				dataType: 'json',
				data: {
					params: '',
					area: '',
					category: array['category'],
					year: '',
					_token: _token
				}
			}).done(function(data) {
				$('.box.advanced').removeClass('homepage').addClass('search_advanced_film');
				$('.box.search_advanced_film').html(data[0]);
				if (data[1] < 18) {
					$('.lds-facebook').remove();
				}
				$('#preloader').hide();
				return true;
			}).fail(function(e) {
				$('.box.advanced').removeClass('homepage').addClass('search_advanced_film');
				$('.box.search_advanced_film').html('<div style="padding-top: 30px; font-weight: 600; font-size: 20px">Không tìm thấy phim</div>');
				$('#preloader').hide();
				return false;
			});
		})
		return true;
	}).fail(function(e) {
		return false;
	});
</script>
@endsection