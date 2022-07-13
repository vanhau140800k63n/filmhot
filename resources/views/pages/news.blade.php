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
<title>{{ $news_detail->title }}</title>
<style>
    h1 {
        font-size: 25px !important;
        margin-top: 30px !important;
        text-transform: uppercase;
        font-weight: 500 !important;
    }
</style>
@endsection
@section('content')
<section class="news">
    <div class="box advanced">
        <h1> {{ $news_detail->title }} </h1>
        <div style="margin-top: 20px;">
            {!! $news_detail->content !!} 
        </div>
    </div>
</section>
@endsection