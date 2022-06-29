@extends('admin.master')
@section('head')
<title> Tạo web phim | Topfilm </title>
<link rel="stylesheet" href="{{ asset(mix('css/app.css')) }}">
<link rel="stylesheet" href="{{ asset('css/assets/css/create_movie.css') }}">
@endsection
@section('content')
<section class="create_movie_section">
    <div class="create_container">
        <div class="create_movie_head">
            <div class="create_movie_title">Chào mừng bạn đến với Topfilm. Hãy tạo cho mình một web xem phim ngay tại đây nhé </div>
            <button class="btn btn-success btn-fw create_web">Tạo website</button>
        </div>
        <form action="{{ route('admin.movie.create_view') }}" enctype="multipart/form-data" method="post">
            @csrf
            <div class="add_banner">
                <div class="del_action" style="display: none">
                    <i class="mdi mdi-delete-forever"></i>
                </div>
                <input id="movie_banner1" accept="image/png, image/gif, image/jpeg" type="file" name="banner_img" class="img__input" style="display: none" />
                <label class="upload_box" for="movie_banner1">
                    <div class="edit_action" style="display: none">
                        <i class="mdi mdi-settings"></i>
                    </div>
                    <div class="upload_container">
                        <div class="upload__button create__product-div button" style="padding-top: 9px">
                            <div class="btn btn-primary btn-fw"><span class="menu-icon"><i class="mdi mdi-plus"></i></span> Thêm ảnh banner </div>
                        </div>
                    </div>
                </label>

            </div>
        </form>
        <div class="add_movie">
            <!-- <div class="add_movie_close" style="display: none;"><i class="mdi mdi-window-close"></i></div> -->
            <div class="upload_container">
                <div class="data__image-action">
                    <img class="icon__trash" src="{{ asset('assets/frontend/img/partner/create-product/icon__trash.svg') }}" alt="">
                </div>
                <div class="upload__button create__product-div button" style="padding-top: 9px">
                    <div class="btn btn-primary btn-fw add_movie_btn"><span class="menu-icon"><i class="mdi mdi-plus"></i></span> Thêm phim </div>
                </div>
            </div>
            <div class="row add_movie_content" style="display: none;">
                <div class="col-md-12 col-xl-6 grid-margin stretch-card" style="height: 450px; overflow: hidden;">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Tìm kiếm phim</h4>
                            <div class="add-items d-flex">
                                <input type="text" class="form-control add_movie_search" placeholder="Nhập tên phim" style="color: #fff">
                                <button class="add btn btn-primary add_movie_push_btn">Thêm</button>
                            </div>
                            <div class="list-wrapper">
                                <ul class="d-flex flex-column-reverse text-white todo-list todo-list-custom add_movie_list">
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-xl-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-row justify-content-between">
                                <h4 class="card-title">Phim đã chọn</h4>
                                <p class="text-muted mb-1 small">Lượt xem</p>
                            </div>
                            <div class="preview-list" style="height: 355px; overflow-y: scroll;">
                                @if($user->current_movies != null)
                                @foreach(explode(',', $user->current_movies) as $id_movie)
                                <?php $movie = \App\Models\Movie::find(intval($id_movie)) ?>
                                <div class="preview-item border-bottom">
                                    <div class="preview-thumbnail">
                                        <img src="{{ asset('img/' . $movie->category . $movie->id . '.jpg') }}" alt="image" class="rounded-circle" />
                                    </div>
                                    <div class="preview-item-content d-flex flex-grow">
                                        <div class="flex-grow">
                                            <div class="d-flex d-md-block d-xl-flex justify-content-between">
                                                <h6 class="preview-subject">{{ $movie->name }}</h6>
                                                <p class="text-muted text-small"> 0 </p>
                                            </div>
                                            <p class="text-muted">{{$movie->year}} </p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="recommend__item">
            @if($user->current_movies != null)
            @foreach(explode(',', $user->current_movies) as $id_movie)
            <?php $movie = \App\Models\Movie::find(intval($id_movie)) ?>
            <a href="{{route('detail_name', $movie->slug)}}" class="card__film" style="text-decoration: none">
                <?php
                if ($movie->image == '' || $movie->image == null) {
                    $url_image = asset('img/' . $movie->category . $movie->id . '.jpg');
                } else {
                    $url_image = $movie->image;
                }
                ?>
                <img class="image" src="{{$url_image}}" alt="image" />
                <p class="film__name" style="color: #fff;">{{$movie->name}}</p>
            </a>
            @endforeach
            @endif
        </div>
    </div>
</section>
<script>
    list_movie = [];
    check_del = 0;

    @if($user->current_movies != null)
    @foreach(explode(',', $user->current_movies) as $id_movie)
    list_movie.push({{ $id_movie }});
    @endforeach
    @endif

    @if($user->banner != null)
    $('.img__input').parent().children('.upload_box').children('.upload_container').css("display", "none");
    $('.img__input').parent().css('background-image', `url("{{asset($user->banner)}}")`);
    $('.img__input').parent().children('.upload_box').children('.edit_action').css("display", "block");
    $('.img__input').parent().children('.del_action').css("display", "block");
    @endif

    $('.add_movie_btn').click(function() {
        $menu = $(this).parent().parent().parent();
        // $(this).parent().parent().parent().children('.add_movie_close').show();
        $(this).parent().parent().hide();
        $menu.animate({
            width: '100%',
            height: '500px',
        }, {
            duration: 500
        })

        $menu.css('display', 'block');
        setTimeout(function() {
            $('.add_movie_content').show();
        }, 600)

    })

    $('.img__input').change(function() {
        $(this).parent().children('.upload_box').children('.upload_container').css("display", "none");
        $(this).parent().children('.upload_box').children('.edit_action').css("display", "block");
        $(this).parent().children('.del_action').css("display", "block");
        let reader = new FileReader();
        reader.onload = (e) => {
            $(this).parent().css('background-image',
                `url("` + e.target.result + `")`);
        }
        reader.readAsDataURL(this.files[0]);
    })

    $('.add_movie_search').keyup(function() {
        let _token = $('input[name="_token"]').val();
        $.ajax({
            url: "{{ route('admin.movie.search_add_movie') }}",
            type: "POST",
            dataType: 'json',
            data: {
                data: $(this).val(),
                _token: _token
            }
        }).done(function(data) {
            $('.add_movie_list').html(data);
            return true;
        }).fail(function(e) {

            return false;
        });
    })

    $('.add_movie_push_btn').click(function() {
        $("input[name='add_movie[]']:checked").each(function() {
            if (list_movie.indexOf($(this).val()) === -1) {
                list_movie.push($(this).val());
            }
        })
        let _token = $('input[name="_token"]').val();
        $.ajax({
            url: "{{ route('admin.movie.view_movie') }}",
            type: "POST",
            dataType: 'json',
            data: {
                data: list_movie,
                _token: _token
            }
        }).done(function(data) {
            $('.preview-list').html(data);
            return true;
        }).fail(function(e) {

            return false;
        });
    })

    $('.create_web').click(function() {
        if (list_movie.length > 0) {
            input_list_movie = $("<input hidden>").attr("name", "list_movie").val(list_movie);
            $('form').append(input_list_movie);
        }
        if (check_del == 1) {
            input_check_del = $("<input hidden>").attr("name", "check_del").val(1);
            $('form').append(input_check_del);
        }
        $('form').submit();
    })
    $('.del_action').click(function() {
        $('img__input').val('');
        $('.img__input').parent().children('.upload_box').children('.upload_container').css("display", "block");
        $('.img__input').parent().css('background-image', 'none');
        $('.img__input').parent().children('.upload_box').children('.edit_action').css("display", "none");
        $('.img__input').parent().children('.del_action').css("display", "none");
        check_del = 1;
    })
</script>
@endsection