@extends('admin.master')
@section('head')
<title> Danh sách tin tức | Topfilm </title>
<style>
    th {
        text-align: center !important;
    }

    a {
        text-decoration: none !important;
    }
</style>
@endsection
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            @if(Session::has('alert'))
            <div class="alert alert-success" style="display: flex; justify-content: space-between;">
                <div>{{Session::get('alert')}} </div>
            </div>
            @endif
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Danh sách tin tức</h4>
                        <a class="btn btn-primary btn-fw" style="margin-bottom: 20px;" href="{{ route('admin.news.create_news') }}"> Thêm mới </a>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th> STT </th>
                                        <th style="width: 500px"> Tiêu đề </th>
                                        <th> Ngưởi tạo </th>
                                        <th> Thao tác </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0; ?>
                                    @foreach($news as $item)
                                    <tr>
                                        <td style="text-align: center"> {{ ++$i }} </td>
                                        <td> {{ $item->title }} </td>
                                        <?php
                                        $user = \App\Models\User::find($item->created_by);
                                        $created_by = is_null($user) ? 'Null' : $user->full_name;
                                        ?>
                                        <td> {{ $created_by }} </td>
                                        <td style="text-align: center; cursor:pointer">
                                            <a href="{{ route('admin.news.edit_news', $item->id) }}"> <i style="color: #4ad1d5" class="mdi mdi-shape-square-plus"></i> </a>
                                            <a href="{{ route('news_detail', ['slug' => $item->slug, 'id' => $item->id]) }}" target="_blank"> <i style="color: #4ad1d5; margin-left: 15px" class="mdi mdi-open-in-new"></i> </a>
                                            <a href="{{ route('admin.news.destroy', $item->id) }}"> <i style="color: #4ad1d5; margin-left: 15px" class="mdi mdi-delete"></i> </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection