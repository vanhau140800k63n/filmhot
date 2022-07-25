@extends('layouts.master')
@section('meta')
<link href="{{ asset('css/video-js.css') }}" rel="stylesheet" />
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section('content')
<section class="movie">
    <div class="box advanced">
        <div class="bg-[#1e242c] w-full rounded-[5px] p-[30px] flex">
            <div class="w-4/12 flex flex-col justify-center items-center">
                <img src="{{ $movie_detail->image }}">
                <div class="">
                    <button class="bg-[#1884bd] px-[10px] py-[5px] mt-[20px] rounded-[5px]">Xem phim</button>
                </div>
            </div>
            <div class="w-8/12 relative px-[20px]">
                <video class="movie__screen video-js" id="video_preview" preload="auto" data-setup="{}" controls autoplay>
                    <source src="movie" type="application/x-mpegURL">
                </video>
                <div class="cover absolute w-full top-0 left-0 bg-[#111111] z-10 flex justify-center items-center">
                    <div class="lds-hourglass"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="{{ asset('js/video.min.js') }}"></script>
<script>
    $(document).ready(function() {
        video = videojs('video_preview');
        getVideo = setInterval(restart, 1000);

        $('.movie__screen').height($('.movie__screen').width() * 1080 / 1920);
        $('.cover').height($('.movie__screen').height() + 5);

        function restart() {
            if (video['cache_']['duration'] == 0 || !video['controls_'] || video['error_'] != null || isNaN(video['cache_']['duration']) || video['cache_']['duration'] == 'Infinity') {
                reload();
            } else {
                $('.cover').hide();
                video.duration(300);
                clearInterval(getVideo);
            }
        }

        function reload() {
            let _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('movie.preview')}}",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                type: "POST",
                dataType: 'json',
                data: {
                    id: "{{ $movie_detail->id }}",
                    category: "{{ $movie_detail->category }}",
                    _token: _token
                }
            }).done(function(data) {
                if (video['cache_']['duration'] == 0 || !video['controls_'] || video['error_'] != null || isNaN(video['cache_']['duration']) || video['cache_']['duration'] == 'Infinity') {
                    video.src(data[0]['mediaUrl']);
                }
                return true;
            }).fail(function(e) {
                return false;
            });
        }
    })
</script>
@endsection