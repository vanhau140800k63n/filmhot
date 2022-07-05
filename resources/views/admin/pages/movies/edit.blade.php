@extends('admin.master')
@section('head')
<title> Phát triển phim | Topfilm </title>
<link rel="stylesheet" href="{{ asset(mix('css/app.css')) }}">
<link rel="stylesheet" href="{{ asset('css/assets/css/create_movie.css') }}">
<style>
    .tox .tox-statusbar {
        display: none !important;
    }

    .tox .tox-edit-area__iframe {
        background: #020d18 !important;
    }

    .tox-tinymce {
        border: 0 !important;
    }
</style>
@endsection
@section('content')
<section class="create_movie_section">
    <div class="create_container">
        @if(Session::has('alert'))
        <div class="alert alert-success" style="display: flex; justify-content: space-between;">
            <div>{{Session::get('alert')}} </div>
            <a type="button" href="{{ route('movie.detail', ['category' => $movie->category, 'id' => $movie->id, 'name' => $movie->name]) }}" target="_blank" class="btn btn-success btn-fw">Xem website</a>
        </div>
        @endif
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">ID_MOVIE: {{ $movie->id_movie }}, <span style="color: #ed29ec"> ID: {{ $movie->id }} </span>, <span style="color: #29ceed"> CATEGORY: {{ $movie->category }} </span></h4>
                    <form class="form-sample" action="{{route('admin.movie.update', $movie->id_movie)}}" enctype="multipart/form-data" method="post">
                        @csrf
                        <p class="card-description"> Personal info </p>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="name" value="{{ $movie->name }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Slug</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="slug" value="{{ $movie->slug }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" style="margin-bottom: 20px;">Description</label>
                                    <div class="col-sm-12">
                                        <textarea id="myeditorinstance" name="description">{{ $movie->description }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input id="my-file" type="file" name="my-file" style="display: none;" onchange="" />
                        <button type="submit" class="btn btn-primary me-2">Ghi nhận</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea#myeditorinstance', // Replace this CSS selector to match the placeholder element for TinyMCE
        plugins: 'code table lists image',
        toolbar: 'undo redo | formatselect| bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table | forecolor backcolor | image',
        images_file_types: 'jpg,svg,webp',
        // file_picker_types: 'file image media',
        // file_picker_callback: (callback, value, meta) => {
        //     if (meta.filetype == 'image') {
        //         var input = document.getElementById('my-file');
        //         input.click();
        //         input.onchange = function() {
        //             var file = input.files[0];
        //             var reader = new FileReader();
        //             reader.onload = function(e) {
        //                 callback(e.target.result, {
        //                     alt: file.name
        //                 });
        //             };
        //             reader.readAsDataURL(file);
        //         };
        //     }
        // }
    });
</script>
@endsection