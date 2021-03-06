<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta')
    <link rel="shortcut icon" href="{{asset('img/logo1.png')}}" />
    <link rel="stylesheet" href="{{ asset(mix('css/app.css')) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

    <?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json;charset=UTF-8');
    ?>
</head>

<body>
    @include('partial.header')
    @yield('content')
    @include('partial.footer')

    <script src="{{asset('js/hls.min.js')}}" type="application/javascript"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script type="text/javascript">
        $('.image').css('max-height', $('.card__film').width() * 1.4);
        getImage();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function getImage() {
            $.ajax({
                url: "{{ route('storage-ajax') }}",
                type: "GET",
                dataType: 'json',
            }).done(function(data) {
                setTimeout(function() {
                    getImage();
                }, 2000);
                return true;
            }).fail(function(e) {
                return false;
            });
        }
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

        var scroll = true;
        $(window).scroll(function() {
            if ($('.box').hasClass('homepage')) {
                value = $('header').height() + $(".homepage").height() - $(window).scrollTop() - $(window).height() - 50;
                if (value < 0 && scroll) {
                    scroll = false;
                    let _token = $('input[name="_token"]').val();
                    $.ajax({
                        url: "{{route('home-ajax')}}",
                        type: "POST",
                        dataType: 'json',
                        data: {
                            page: $('.homepage').attr('id'),
                            width: $('.image').width(),
                            _token: _token
                        }
                    }).done(function(data) {
                        $('.lds-facebook').remove();
                        $('.listfilm').html($('.listfilm').html() + data[0]);
                        $('.homepage').attr('id', data[1]);
                        scroll = true;

                        return true;
                    }).fail(function(e) {
                        return false;
                    });
                }
            }
            if ($('.box').hasClass('search_advanced_film')) {
                value = $('header').height() + $(".search_advanced_film").height() - $(window).scrollTop() - $(window).height() - 50;
                if (value < 0 && scroll && $('#info').attr('count') == 18) {
                    scroll = false;
                    let _token = $('input[name="_token"]').val();
                    $.ajax({
                        url: "{{ route('search_advanced_more') }}",
                        type: "POST",
                        dataType: 'json',
                        data: {
                            params: array['as'] != '' ? $('#as_container' + array['as']).attr('params') : '',
                            area: array['area'],
                            category: array['category'],
                            year: array['year'],
                            sort: $('#info').attr('sort'),
                            _token: _token
                        }
                    }).done(function(data) {
                        $('.recommend__item').html($('.recommend__item').html() + data[0]);
                        $('#info').remove();
                        $('.recommend__items').html($('.recommend__items').html() + data[1]);
                        scroll = true;
                        if (data[2] < 18) {
                            $('.lds-facebook').remove();
                        }

                        return true;
                    }).fail(function(e) {
                        $('.lds-facebook').remove();
                        scroll = true;
                        $('#info').attr('count', 0);
                        return false;
                    });
                }
            }
        });
    </script>
</body>

</html>