<header>
	<div class="box">
		<div class="logo"><a href="{{route('home')}}">TOPFILM</a></div>
		<div class="search">
			<!-- <div class="search-form"> -->
				<form action="{{route('key-search')}}" method="post">
					@csrf
					<input type="text" name="keyword" placeholder="Tên phim ..." class="search__input">
					<button class="search__btn">Tìm kiếm</button>
				</form>
			<!-- </div> -->
		</div>
	</div>
</header>