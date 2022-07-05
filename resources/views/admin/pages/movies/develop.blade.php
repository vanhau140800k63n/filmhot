@extends('admin.master')
@section('head')
<title> Phát triển phim | Topfilm </title>
<link rel="stylesheet" href="{{ asset(mix('css/app.css')) }}">
<link rel="stylesheet" href="{{ asset('css/assets/css/create_movie.css') }}">
@endsection
@section('content')
<section class="create_movie_section">
    <div class="create_container">
        @if(Session::has('alert'))
        <div class="alert alert-success" style="display: flex; justify-content: space-between;">
            <div>{{Session::get('alert')}} </div>
            <a type="button" href="{{ route('user.home', \Illuminate\Support\Facades\Auth::guard('user')->id()) }}" target="_blank" class="btn btn-success btn-fw">Xem website</a>
        </div>
        @endif
        <div class="row add_movie_content">
            <div class="col-md-12 grid-margin stretch-card" style="height: 450px; overflow: hidden;">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tìm kiếm phim</h4>
                        <div class="add-items d-flex">
                            <input type="text" class="form-control add_movie_search" placeholder="Nhập tên phim" style="color: #fff">
                            <button class="add btn btn-primary add_movie_push_btn" style="width:200px">Chỉnh sửa</button>
                        </div>
                        <div class="list-wrapper">
                            <ul class="d-flex flex-column-reverse text-white todo-list todo-list-custom add_movie_list">
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="recommend__item">
            @if($user->current_movies != null)
            @foreach(explode(',', $user->current_movies) as $id_movie)
            <?php $movie = \App\Models\Movie::where('id_movie', intval($id_movie))->first() ?>
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
            url: "{{ route('admin.movie.search_develop_movie') }}",
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
        // alert($("input[name='develop_movie']:checked").val());
        let _token = $('input[name="_token"]').val();
        $.ajax({
            url: "{{ route('admin.movie.get_url_edit') }}",
            type: "POST",
            dataType: 'json',
            data: {
                data: $('input[name="develop_movie"]:checked').val(),
                _token: _token
            }
        }).done(function(data) {
            // alert(data);
            window.location.href = data;
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