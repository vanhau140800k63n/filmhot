@extends('admin.master')
@section('head')
<title> Website của bạn | Topfilm </title>
<link rel="stylesheet" href="{{ asset(mix('css/app.css')) }}">
@endsection
@section('content')
<embed src="{{ route('user.home', \Illuminate\Support\Facades\Auth::guard('user')->id()) }}" style="width:100%; height: 2000px;  overflow: hidden; pointer-events: none;">
@endsection