<header>
	<div class="box">
		<div class="logo"><a href="{{route('home')}}">TOPFILM</a></div>
		<div class="search">
			<form action="{{route('key-search')}}" method="post">
				@csrf
				<input type="text" name="keyword" placeholder="Tên phim ..." class="search__input">
				<button class="search__btn">Tìm kiếm</button>
			</form>
		</div>
	</div>
</header>