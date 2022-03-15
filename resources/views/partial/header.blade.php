<header>
	<div class="container">
		<div class="logo"><a href="{{route('home')}}">DUNGNTT</a></div>
		<div class="search">
			<div class="search-form">
				<form action="{{route('key-search')}}" method="post">
					@csrf
					<input type="text" name="keyword" placeholder="Tìm kiếm" class="bg-white rounded-[10px] h-[50px] px-[15px] py-[5px] text-black">
					<button>Tìm kiếm</button>
				</form>
			</div>
		</div>
	</div>
</header>