@extends('layouts.master')
@section('meta')
<meta name="description" content="{{ $news_detail->seo_description }}">
<meta name="keywords" content="{{ $news_detail->seo_keywords }}">
<meta name="robots" content="index, follow">
<meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta property="og:locale" content="vi_VN">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $news_detail->title }}">
<meta property="og:description" content="{{ $news_detail->seo_description }}">
<meta property="og:url" content="{{ route('news_detail', $news_detail->slug) }}">
<meta property="og:site_name" content="{{ $news_detail->title }}">
<meta property="og:image" content="{{ $news_detail->image }}">
<script src="https://cdn.tailwindcss.com"></script>
<title>{{ $news_detail->title }}</title>
<style>
    h1 {
        font-size: 25px !important;
        margin-top: 30px !important;
        text-transform: uppercase;
        font-weight: 500 !important;
    }

    .rand_news_title {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: break-word;
    }
</style>
@endsection
@section('content')
<section class="news">
    <div class="box advanced">
        <div class="flex flex-col lg:flex-row">
            <div class="w-full lg:w-9/12 p-[5px] lg:p-[20px]">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <img src="{{ asset('css/assets/images/faces/face.png') }}" class="w-[30px] h-[30px] rounded-[20px]">
                        <p class="ml-[10px] text-[14px] text-[#848484]"> Tác giả: Ẩn danh </p>
                    </div>
                    <p class="float-right text-[14px] text-[#848484]"> Đã đăng vào: {{ $news_detail->created_at }} </p>
                </div>
                <h1 class="text-[20px] font-medium uppercase mt-[30px] lg:text-[25px]"> {{ $news_detail->title }} </h1>
                <div style="margin-top: 20px;">
                    {!! $news_detail->content !!}
                </div>
            </div>
            <div class="w-full lg:w-3/12 p-[5px] lg:p-[20px]">
                <p class="text-[20px] font-medium text-[#b6d7de] mb-[20px]"> Tin tức khác </p>
                @foreach($news_rand as $item)
                <div class="mb-[30px]">
                    <a href="{{ route('news_detail', $item->slug) }}">
                        <img src="{{ $item->image }}">
                        <p class="text-center rand_news_title text-[#b6d7de] text-[14px] mt-[5px] mb-[15px]"> {{ $item->title }} </p>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection