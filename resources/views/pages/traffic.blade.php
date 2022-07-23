@extends('layouts.master')
@section('meta')
<title>Traffic - Topfilm</title>
@endsection
@section('content')
<div class="box advanced">
    <div> Tổng traffic: {{ $total_traffic }} </div>
	@foreach($movies as $movie)
    <div> {{$movie->name}} : {{$movie->traffic}} </div> 
    @endforeach
</div>
@endsection