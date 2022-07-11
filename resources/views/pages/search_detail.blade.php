<section class="home__products">
	<div class="box">
		<div class="home__products__all" id="all__products">
			@foreach($productAll as $key)
			<div class="home__product">
				<div class="home__product__card">
					<p class="ribbon__shop">Yêu Thích +</p>
					<div class="ribbon__sale">
						<p data__price="{{$key->price}}" data__price__sale="{{$key->sale}}">10% Giảm</p>
					</div>
					<img src="{{$key->image}}" alt="" class="home__product-img">
					<img class="home__product-label_bottom" src="https://devsne.vn/source/img/a/voucher.png">
					<div class="home__product-content">
						<h3 class="home__product-title">
							{{$key->name}}
						</h3>
						<div class="home__product-bottom">
							<div class="home__product_price">
								<div class="home__product_price-default"><span>₫</span>{{number_format($key->price)}}</div>
								<div class="home__product_price-sale"><span>₫</span>{{number_format($key->sale)}}</div>
							</div>
							<div class="home__product-rating">
								<div class="number-rated">{{$key->rated}}</div>
								<div class="star-rated">
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
								</div>
								<div class="sold-qty">| Đã bán {{$key->sold}}</div>
							</div>
						</div>
					</div>
				</div>
				<?php $type = substr($key->categorie, 0, 5) ?>
				<div class="home__product__search">
					<a href="">Sản phẩm tương tự</a>
				</div>
			</div>
			@endforeach
		</div>
	</div>
</section>