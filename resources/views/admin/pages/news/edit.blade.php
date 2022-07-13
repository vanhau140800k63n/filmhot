@extends('admin.master')
@section('head')
<title> Chỉnh sửa tin tức | Topfilm </title>
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

    .edit_news_section {
        padding: 1.875rem 1.75rem;
        background: #000;
    }
</style>
@endsection
@section('content')
<section class="edit_news_section">
    <div class="create_container">
        @if(count($errors)>0)
        <div class="alert alert-danger">
            @foreach($errors->all() as $err)
            {{$err}} <br>
            @endforeach
        </div>
        @else
        @if(Session::has('alert'))
        <div class="alert alert-success" style="display: flex; justify-content: space-between;">
            <div>{{Session::get('alert')}} </div>
            <a type="button" href="{{ route('news_detail', $news_detail->slug) }}" target="_blank" class="btn btn-success btn-fw">Xem website</a>
        </div>
        @endif
        @endif
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <form class="form-sample" action="{{ route('admin.news.update', $news_detail->id) }}" enctype="multipart/form-data" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Title</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="title" value="{{ old('title', $news_detail->title) }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Slug</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="slug" value="{{ old('slug', $news_detail->slug) }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">SEO Keywords</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="seo_keywords" value="{{ old('seo_keywords', $news_detail->seo_keywords) }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">SEO Description</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="seo_description" value="{{ old('seo_description', $news_detail->seo_description) }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" style="margin-bottom: 20px;">Description</label>
                                    <div class="col-sm-12">
                                        <textarea id="myeditorinstance" name="content">{{ $news_detail->content }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input id="myfile" type="file" name="myfile" />
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
    });
</script>
@endsection