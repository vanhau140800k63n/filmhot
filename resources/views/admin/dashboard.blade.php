@extends('admin.master')
@section('head')
<title> Trang chủ | Topfilm </title>
<style>
    .preview-list .preview-item {
        cursor: pointer;
    }

    .preview-list .preview-item:hover {
        background-color: #2e2f32;
        padding-left: 10px;
        padding-right: 10px;
        transition: 0.3s;
    }
</style>
@endsection
@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-9">
                            <div class="d-flex align-items-center align-self-start">
                                <h3 class="mb-0">{{ $user->traffic }}</h3>
                                <p class="text-success ms-2 mb-0 font-weight-medium">+0%</p>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="icon icon-box-success ">
                                <span class="mdi mdi-arrow-top-right icon-item"></span>
                            </div>
                        </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Lượt truy cập</h6>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-9">
                            <div class="d-flex align-items-center align-self-start">
                                <h3 class="mb-0"> {{ $user->view }}</h3>
                                <p class="text-success ms-2 mb-0 font-weight-medium">+0%</p>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="icon icon-box-success">
                                <span class="mdi mdi-arrow-top-right icon-item"></span>
                            </div>
                        </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Lượt xem</h6>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-9">
                            <div class="d-flex align-items-center align-self-start">
                                <h3 class="mb-0">0</h3>
                                <p class="text-danger ms-2 mb-0 font-weight-medium">-0%</p>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="icon icon-box-danger">
                                <span class="mdi mdi-arrow-bottom-left icon-item"></span>
                            </div>
                        </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Đóng góp</h6>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-9">
                            <div class="d-flex align-items-center align-self-start">
                                <h3 class="mb-0">0</h3>
                                <p class="text-success ms-2 mb-0 font-weight-medium">+0%</p>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="icon icon-box-success ">
                                <span class="mdi mdi-arrow-top-right icon-item"></span>
                            </div>
                        </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Phát triển</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Thống kê</h4>
                    <canvas id="transaction-history" class="transaction-chart"></canvas>
                    <div class="bg-gray-dark d-flex d-md-block d-xl-flex flex-row py-3 px-4 px-md-3 px-xl-4 rounded mt-3">
                        <div class="text-md-center text-xl-left">
                            <h6 class="mb-1">Thu nhập hôm nay</h6>
                            <p class="text-muted mb-0 fee_today"></p>
                        </div>
                        <div class="align-self-center flex-grow text-right text-md-center text-xl-right py-md-2 py-xl-0">
                            <h6 class="font-weight-bold mb-0">{{ $user->view * 1000 }} đ</h6>
                        </div>
                    </div>
                    <div class="bg-gray-dark d-flex d-md-block d-xl-flex flex-row py-3 px-4 px-md-3 px-xl-4 rounded mt-3">
                        <div class="text-md-center text-xl-left">
                            <h6 class="mb-1">Tổng thu</h6>
                            <p class="text-muted mb-0 fee_all"></p>
                        </div>
                        <div class="align-self-center flex-grow text-right text-md-center text-xl-right py-md-2 py-xl-0">
                            <h6 class="font-weight-bold mb-0">{{ $user->view * 1000 }} đ</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row justify-content-between">
                        <h4 class="card-title mb-1">Phim có lượt view cao</h4>
                        <p class="text-muted mb-1">Lượt view</p>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="preview-list">
                                <?php $check_view_movies = 0;
                                $index = 0; ?>
                                @foreach($get_view_movies as $data)
                                @if($data != '' && $index < 5)
                                <?php
                                $first_pos = strpos($data, '-');
                                $last_pos = strpos($data, '+');
            
                                $id_movie = intval(substr($data, 1 , $first_pos - 1));
                                $traffic = intval(substr($data, $first_pos + 1, $last_pos - $first_pos - 1));
                                $view = substr($data, $last_pos + 1, strlen($data) - $last_pos);
                                $movie = \App\Models\Movie::where('id_movie', $id_movie)->first();
                                $check_view_movies = 1;
                                ++$index;
                                ?>
                                <div class="preview-item border-bottom">
                                    <div class="preview-thumbnail">
                                        <div class="preview-icon bg-primary">
                                            <img src="{{ asset('img/'.$movie->category.$movie->id.'.jpg') }}">
                                        </div>
                                    </div>
                                    <div class="preview-item-content d-sm-flex flex-grow">
                                        <div class="flex-grow">
                                            <h6 class="preview-subject">{{ $movie->name }}</h6>
                                            <p class="text-muted mb-0">{{ $movie->year }}</p>
                                        </div>
                                        <div class="me-auto text-sm-right pt-2 pt-sm-0">
                                            <!-- <p class="text-muted">15 minutes ago</p> -->
                                            <p class="text-muted mb-0"> {{ $view }} </p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                                @if(!$check_view_movies)
                                <div class="preview-item border-bottom">
                                    <div class="preview-item-content d-sm-flex flex-grow">
                                        <div class="flex-grow">
                                            <h6 class="preview-subject">Không có dữ liệu</h6>
                                            <p class="text-muted mb-0"></p>
                                        </div>
                                        <div class="me-auto text-sm-right pt-2 pt-sm-0">
                                            <!-- <p class="text-muted">15 minutes ago</p> -->
                                            <p class="text-muted mb-0"></p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="row">
        <div class="col-sm-4 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h5>Revenue</h5>
                    <div class="row">
                        <div class="col-8 col-sm-12 col-xl-8 my-auto">
                            <div class="d-flex d-sm-block d-md-flex align-items-center">
                                <h2 class="mb-0">$32123</h2>
                                <p class="text-success ms-2 mb-0 font-weight-medium">+3.5%</p>
                            </div>
                            <h6 class="text-muted font-weight-normal">11.38% Since last month</h6>
                        </div>
                        <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                            <i class="icon-lg mdi mdi-codepen text-primary ms-auto"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h5>Sales</h5>
                    <div class="row">
                        <div class="col-8 col-sm-12 col-xl-8 my-auto">
                            <div class="d-flex d-sm-block d-md-flex align-items-center">
                                <h2 class="mb-0">$45850</h2>
                                <p class="text-success ms-2 mb-0 font-weight-medium">+8.3%</p>
                            </div>
                            <h6 class="text-muted font-weight-normal"> 9.61% Since last month</h6>
                        </div>
                        <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                            <i class="icon-lg mdi mdi-wallet-travel text-danger ms-auto"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h5>Purchase</h5>
                    <div class="row">
                        <div class="col-8 col-sm-12 col-xl-8 my-auto">
                            <div class="d-flex d-sm-block d-md-flex align-items-center">
                                <h2 class="mb-0">$2039</h2>
                                <p class="text-danger ms-2 mb-0 font-weight-medium">-2.1% </p>
                            </div>
                            <h6 class="text-muted font-weight-normal">2.27% Since last month</h6>
                        </div>
                        <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                            <i class="icon-lg mdi mdi-monitor text-success ms-auto"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                                    <th> Client Name </th>
                                    <th> Order No </th>
                                    <th> Product Cost </th>
                                    <th> Project </th>
                                    <th> Payment Mode </th>
                                    <th> Start Date </th>
                                    <th> Payment Status </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="form-check form-check-muted m-0">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input">
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <img src="{{asset('css/assets/images/faces/face1.jpg')}}" alt="image" />
                                        <span class="ps-2">Henry Klein</span>
                                    </td>
                                    <td> 02312 </td>
                                    <td> $14,500 </td>
                                    <td> Dashboard </td>
                                    <td> Credit card </td>
                                    <td> 04 Dec 2019 </td>
                                    <td>
                                        <div class="badge badge-outline-success">Approved</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check form-check-muted m-0">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input">
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <img src="{{asset('css/assets/images/faces/face2.jpg')}}" alt="image" />
                                        <span class="ps-2">Estella Bryan</span>
                                    </td>
                                    <td> 02312 </td>
                                    <td> $14,500 </td>
                                    <td> Website </td>
                                    <td> Cash on delivered </td>
                                    <td> 04 Dec 2019 </td>
                                    <td>
                                        <div class="badge badge-outline-warning">Pending</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check form-check-muted m-0">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input">
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <img src="{{asset('css/assets/images/faces/face5.jpg')}}" alt="image" />
                                        <span class="ps-2">Lucy Abbott</span>
                                    </td>
                                    <td> 02312 </td>
                                    <td> $14,500 </td>
                                    <td> App design </td>
                                    <td> Credit card </td>
                                    <td> 04 Dec 2019 </td>
                                    <td>
                                        <div class="badge badge-outline-danger">Rejected</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check form-check-muted m-0">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input">
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <img src="{{asset('css/assets/images/faces/face3.jpg')}}" alt="image" />
                                        <span class="ps-2">Peter Gill</span>
                                    </td>
                                    <td> 02312 </td>
                                    <td> $14,500 </td>
                                    <td> Development </td>
                                    <td> Online Payment </td>
                                    <td> 04 Dec 2019 </td>
                                    <td>
                                        <div class="badge badge-outline-success">Approved</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check form-check-muted m-0">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input">
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <img src="{{asset('css/assets/images/faces/face4.jpg')}}" alt="image" />
                                        <span class="ps-2">Sallie Reyes</span>
                                    </td>
                                    <td> 02312 </td>
                                    <td> $14,500 </td>
                                    <td> Website </td>
                                    <td> Credit card </td>
                                    <td> 04 Dec 2019 </td>
                                    <td>
                                        <div class="badge badge-outline-success">Approved</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <!-- <div class="row">
        <div class="col-md-6 col-xl-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row justify-content-between">
                        <h4 class="card-title">Messages</h4>
                        <p class="text-muted mb-1 small">View all</p>
                    </div>
                    <div class="preview-list">
                        <div class="preview-item border-bottom">
                            <div class="preview-thumbnail">
                                <img src="{{asset('css/assets/images/faces/face6.jpg')}}" alt="image" class="rounded-circle" />
                            </div>
                            <div class="preview-item-content d-flex flex-grow">
                                <div class="flex-grow">
                                    <div class="d-flex d-md-block d-xl-flex justify-content-between">
                                        <h6 class="preview-subject">Leonard</h6>
                                        <p class="text-muted text-small">5 minutes ago</p>
                                    </div>
                                    <p class="text-muted">Well, it seems to be working now.</p>
                                </div>
                            </div>
                        </div>
                        <div class="preview-item border-bottom">
                            <div class="preview-thumbnail">
                                <img src="{{asset('css/assets/images/faces/face8.jpg')}}" alt="image" class="rounded-circle" />
                            </div>
                            <div class="preview-item-content d-flex flex-grow">
                                <div class="flex-grow">
                                    <div class="d-flex d-md-block d-xl-flex justify-content-between">
                                        <h6 class="preview-subject">Luella Mills</h6>
                                        <p class="text-muted text-small">10 Minutes Ago</p>
                                    </div>
                                    <p class="text-muted">Well, it seems to be working now.</p>
                                </div>
                            </div>
                        </div>
                        <div class="preview-item border-bottom">
                            <div class="preview-thumbnail">
                                <img src="{{asset('css/assets/images/faces/face9.jpg')}}" alt="image" class="rounded-circle" />
                            </div>
                            <div class="preview-item-content d-flex flex-grow">
                                <div class="flex-grow">
                                    <div class="d-flex d-md-block d-xl-flex justify-content-between">
                                        <h6 class="preview-subject">Ethel Kelly</h6>
                                        <p class="text-muted text-small">2 Hours Ago</p>
                                    </div>
                                    <p class="text-muted">Please review the tickets</p>
                                </div>
                            </div>
                        </div>
                        <div class="preview-item border-bottom">
                            <div class="preview-thumbnail">
                                <img src="{{asset('css/assets/images/faces/face11.jpg')}}" alt="image" class="rounded-circle" />
                            </div>
                            <div class="preview-item-content d-flex flex-grow">
                                <div class="flex-grow">
                                    <div class="d-flex d-md-block d-xl-flex justify-content-between">
                                        <h6 class="preview-subject">Herman May</h6>
                                        <p class="text-muted text-small">4 Hours Ago</p>
                                    </div>
                                    <p class="text-muted">Thanks a lot. It was easy to fix it .</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Portfolio Slide</h4>
                    <div class="owl-carousel owl-theme full-width owl-carousel-dash portfolio-carousel" id="owl-carousel-basic">
                        <div class="item">
                            <img src="{{asset('css/assets/images/dashboard/Rectangle.jpg')}}" alt="">
                        </div>
                        <div class="item">
                            <img src="{{asset('css/assets/images/dashboard/Img_5.jpg')}}" alt="">
                        </div>
                        <div class="item">
                            <img src="{{asset('css/assets/images/dashboard/img_6.jpg')}}" alt="">
                        </div>
                    </div>
                    <div class="d-flex py-4">
                        <div class="preview-list w-100">
                            <div class="preview-item p-0">
                                <div class="preview-thumbnail">
                                    <img src="{{asset('css/assets/images/faces/face12.jpg')}}" class="rounded-circle" alt="">
                                </div>
                                <div class="preview-item-content d-flex flex-grow">
                                    <div class="flex-grow">
                                        <div class="d-flex d-md-block d-xl-flex justify-content-between">
                                            <h6 class="preview-subject">CeeCee Bass</h6>
                                            <p class="text-muted text-small">4 Hours Ago</p>
                                        </div>
                                        <p class="text-muted">Well, it seems to be working now.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted">Well, it seems to be working now. </p>
                    <div class="progress progress-md portfolio-progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 50%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-xl-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">To do list</h4>
                    <div class="add-items d-flex">
                        <input type="text" class="form-control todo-list-input" placeholder="enter task..">
                        <button class="add btn btn-primary todo-list-add-btn">Add</button>
                    </div>
                    <div class="list-wrapper">
                        <ul class="d-flex flex-column-reverse text-white todo-list todo-list-custom">
                            <li>
                                <div class="form-check form-check-primary">
                                    <label class="form-check-label">
                                        <input class="checkbox" type="checkbox"> Create invoice </label>
                                </div>
                                <i class="remove mdi mdi-close-box"></i>
                            </li>
                            <li>
                                <div class="form-check form-check-primary">
                                    <label class="form-check-label">
                                        <input class="checkbox" type="checkbox"> Meeting with Alita </label>
                                </div>
                                <i class="remove mdi mdi-close-box"></i>
                            </li>
                            <li class="completed">
                                <div class="form-check form-check-primary">
                                    <label class="form-check-label">
                                        <input class="checkbox" type="checkbox" checked> Prepare for presentation </label>
                                </div>
                                <i class="remove mdi mdi-close-box"></i>
                            </li>
                            <li>
                                <div class="form-check form-check-primary">
                                    <label class="form-check-label">
                                        <input class="checkbox" type="checkbox"> Plan weekend outing </label>
                                </div>
                                <i class="remove mdi mdi-close-box"></i>
                            </li>
                            <li>
                                <div class="form-check form-check-primary">
                                    <label class="form-check-label">
                                        <input class="checkbox" type="checkbox"> Pick up kids from school </label>
                                </div>
                                <i class="remove mdi mdi-close-box"></i>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Lượt xem các quốc gia</h4>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <i class="flag-icon flag-icon-vn"></i>
                                            </td>
                                            <td>Việt Nam</td>
                                            <td class="text-right"> 1500 </td>
                                            <td class="text-right font-weight-medium"> 100% </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div id="audience-map" class="vector-map"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let date = new Date();
    $('.fee_today').html(date.toDateString());
    $('.fee_all').html(date.toDateString());

    (function($) {
        'use strict';
        $.fn.andSelf = function() {
            return this.addBack.apply(this, arguments);
        }
        $(function() {
            if ($("#transaction-history").length) {
                var areaData = {
                    labels: ["Lượt truy cập", "Lượt view", "Lượt thoát"],
                    datasets: [{
                        data: [ {{ $user->traffic }}, {{ $user->view }}, {{ $user->traffic - $user->view }}],
                        backgroundColor: ["#57c7d4", "#00d25b", "#ffab00"]
                    }]
                };
                var areaOptions = {
                    responsive: true,
                    maintainAspectRatio: true,
                    segmentShowStroke: false,
                    cutoutPercentage: 70,
                    elements: {
                        arc: {
                            borderWidth: 0
                        }
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        enabled: true
                    }
                }
                var transactionhistoryChartPlugins = {
                    beforeDraw: function(chart) {
                        var width = chart.chart.width,
                            height = chart.chart.height,
                            ctx = chart.chart.ctx;

                        ctx.restore();
                        var fontSize = 1;
                        ctx.font = fontSize + "rem sans-serif";
                        ctx.textAlign = 'left';
                        ctx.textBaseline = "middle";
                        ctx.fillStyle = "#ffffff";

                        @if($user->view)
                        var text = "100%",
                            textX = Math.round((width - ctx.measureText(text).width) / 2),
                            textY = height / 2.0;
                        @else
                        var text = "Chưa có dữ liệu",
                            textX = Math.round((width - ctx.measureText(text).width) / 2),
                            textY = height / 2.0;
                        @endif

                        ctx.fillText(text, textX, textY);

                        ctx.restore();
                        var fontSize = 0.75;
                        ctx.font = fontSize + "rem sans-serif";
                        ctx.textAlign = 'left';
                        ctx.textBaseline = "middle";
                        ctx.fillStyle = "#6c7293";

                        var texts = "",
                            textsX = Math.round((width - ctx.measureText(text).width) / 1.93),
                            textsY = height / 1.7;

                        ctx.fillText(texts, textsX, textsY);
                        ctx.save();
                    }
                }
                var transactionhistoryChartCanvas = $("#transaction-history").get(0).getContext("2d");
                var transactionhistoryChart = new Chart(transactionhistoryChartCanvas, {
                    type: 'doughnut',
                    data: areaData,
                    options: areaOptions,
                    plugins: transactionhistoryChartPlugins
                });
            }
        });
    })(jQuery);
</script>
@endsection