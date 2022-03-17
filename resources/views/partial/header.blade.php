<header>
	<div class="container">
		<div class="logo"><a href="{{route('home')}}">FILMHOT</a></div>
		<div class="search">
			<!-- <div class="search-form"> -->
				<form action="{{route('key-search')}}" method="post">
					@csrf
					<input type="text" name="keyword" placeholder="Tên phim ..." class="bg-white rounded-[10px] h-[50px] px-[15px] py-[5px] text-black">
					<button class="bg-[#ed5829] rounded-[10px] h-[50px] px-[10px] py-[5px] ml-[10px]">Tìm kiếm</button>
				</form>
			<!-- </div> -->
		</div>
	</div>
</header>