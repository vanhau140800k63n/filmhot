@extends('admin.master')
@section('head')
<title> Người phát triển | Topfilm </title>
@endsection
@section('content')
<div class="row ">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Order Status</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>
                                    <div class="form-check form-check-muted m-0">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input">
                                        </label>
                                    </div>
                                </th>
                                <th> Tên </th>
                                <th> ID </th>
                                <th> Lượt traffic </th>
                                <th> Lượt view </th>
                                <th> Thu nhập </th>
                                <th> Ngày tạo </th>
                                <th> Trạng thái </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="form-check form-check-muted m-0">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input">
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <!-- <img src="assets/images/faces/face1.jpg" alt="image" /> -->
                                    <span class="ps-2"> {{ $user->full_name }}</span>
                                </td>
                                <td> {{ $user->id }} </td>
                                <td> 0 </td>
                                <td> 0 </td>
                                <td> 0 </td>
                                <td> {{ $user->created_at }} </td>
                                <td>
                                    <div class="badge badge-outline-success">Hoạt động</div>
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
@endsection