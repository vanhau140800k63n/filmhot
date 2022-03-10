@extends('layouts.master')
@section('content')
<div class="container"> 
	<div class="w-full border-l border-l-gray-200 pb-[10px]">
		<div class="mt-6 mx-[5%] rounded-md">
			@foreach($convert['data']['recommendItems'] as $key => $recommendItems)
			<div class="mt-8 ">
				<div class="flex items-center mt-6 mb-2 justify-between">
					<div class="flex items-center gap-2 text-[24px] font-semibold">
						<div class="fade-loading"></div>
						<span>{{$recommendItems['homeSectionName']}}</span>
					</div>
					<div class="">
						<a href="{{ url ('comming-soon') }}">
							<button class="flex items-center gap-1 text-[16px] font-medium bg-pink-400 hover:bg-pink-300 text-white rounded-full px-2 py-1">
								<h1>Xem thêm</h1>
								<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
								</svg>
							</button>
						</a>
					</div>
				</div>
				<div class="grid grid-cols-5 gap-4">
					@foreach($recommendItems['recommendContentVOList'] as $key => $movie)
					<a href="./category={{$movie['category']}}&id={{$movie['id']}}" class="bg-[#27282d] rounded-xl"> 
						<img class="object-cover w-full h-[230px] rounded-t-xl"
						src="{{$movie['imageUrl']}}" alt="image" />
						<div class="max-h-[40px] mx-4  text-ellipsis overflow-hidden">
							<h2 class="text-gray-100 py-1 text-[16px] whitespace-nowrap">{{$movie['title']}}</h2>
						</div>
					</a>
					@endforeach
				</div>
			</div>	
			@endforeach
		</div>
	</div>
</div>
@endsection