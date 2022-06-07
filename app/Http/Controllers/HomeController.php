<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MovieService;
use App\Models\Movie;
use Session;

class HomeController extends Controller
{
    public function getUpdateFilm()
    {
        $movie = Movie::whereNull('description')->first();

        $movieService = new MovieService();
        $url = 'https://ga-mobile-api.loklok.tv/cms/app/movieDrama/get?id=' . $movie->id . '&category=' . $movie->category;
        $movie_detail = $movieService->getDataTest($url);

        if ($movie_detail == null) {
            $movie->description = 'ok';
            $movie->save();
            return response()->json($movie->movie_id);
        }

        if ($movie->meta == '') {
            $str = $movie_detail['name'];
            $i = 0;
            $data = [];
            $output = '';
            while (strlen($str) > 0) {
                $index = strpos($str, ' ');
                if ($index == null) {
                    $data[$i] = $str;
                    $str = '';
                } else {
                    $data[$i] = substr($str, 0, $index);
                    $str = substr($str, $index + 1);
                    ++$i;
                }
            }
            $size = sizeof($data);
            if ($size > 2) {
                if ($size == 3) {
                    $pos = 2;
                } else if ($size >= 7) {
                    $pos = $size - 3;
                } else {
                    $pos = $size - 2;
                }
                for ($i = $pos; $i < $size; ++$i) {
                    for ($j = 0; $j <= $size - $i; ++$j) {
                        for ($k = $j; $k < $j + $i; ++$k) {
                            if ($k == $j + $i - 1) {
                                $output .= $data[$k] . ', ';
                            } else {
                                $output .= $data[$k] . ' ';
                            }
                        }
                    }
                }
            }

            $movie->meta = $output;
        }
        if (!str_contains($movie->meta, 'fullhd')) {
            $movie->meta = $movie->meta . $movie_detail['name'] . ' vietsub, ' . $movie_detail['name'] . ' fullhd, ' . $movie_detail['name'] . ' fullhd vietsub, ' . $movie_detail['name'];
        }
        if ($movie->description == '') {
            $movie->description = $movie_detail['introduction'];
        }
        if ($movie->name == '') {
            $movie->name = $movie_detail['name'];
        }
        if ($movie->year == '') {
            $movie->year = $movie_detail['year'];
        }
        if ($movie->rate == '') {
            $movie->rate = $movie_detail['score'];
        }
        if ($movie->image == '' || $movie->image == '1') {
            $movie->image = asset('img/' . $movie->category . $movie->id . '.jpg');
        }

        $count_episodes = count($movie_detail['episodeVo']) - 1;
        if (!str_contains($movie->sub, '-' . $count_episodes . '-')) {
            $sub = '';

            foreach ($movie_detail['episodeVo'] as $key_episodeVo => $episodeVo) {
                $checksub_vi = false;
                if ($episodeVo['subtitlingList'] != null) {
                    foreach ($episodeVo['subtitlingList'] as $subtitle) {
                        if ($subtitle['languageAbbr'] == 'vi') {
                            $checksub_vi = true;
                            $sub .= '-' . $key_episodeVo . '-https://srt-to-vtt.vercel.app/?url=' . $subtitle['subtitlingUrl'] . '+' . $key_episodeVo . '+';
                        }
                    }
                    if (!$checksub_vi) {
                        $sub .= '-' . $key_episodeVo . '-' . '+' . $key_episodeVo . '+';
                    }
                } else {
                    $sub .= '-' . $key_episodeVo . '-' . '+' . $key_episodeVo . '+';
                }
            }
            $movie->sub = $sub;
        }

        try {
            $movie->save();
        } catch (\Exception $e) {
            $movie->sub = '';
            $movie->save();
            return response()->json($movie->movie_id);
        }

        return response()->json($movie->movie_id);
    }

    public function updateMovieId()
    {
        $movie = Movie::where('movie_id', '>', 0)->orderBy('movie_id', 'desc')->first();
        $i = $movie->movie_id;
        $movies = Movie::all();
        foreach ($movies as $movie) {
            if ($movie->movie_id == null) {
                $movie->movie_id = $i;
                $movie->save();
                ++$i;
            }
        }
    }
    public function getHomePage()
    {
        return view('pages.home');
    }

    public function getTest()
    {
        $movieService = new MovieService();
        $url = 'https://ga-mobile-api.loklok.tv/cms/app/movieDrama/get?id=' . '16394' . '&category=' .'0';
        $movie_detail = $movieService->getData($url);
        dd($movie_detail);
    }

    public function searchMovie($key)
    {

        $movieService = new MovieService();

        $movieSearchWithKey = $movieService->searchWithKeyWord($key);
        while ($movieSearchWithKey == null) {
            $movieSearchWithKey = $movieService->searchWithKeyWord($key);
        }

        return view('pages.search', compact('movieSearchWithKey', 'key'));
    }

    public function searchMovieAdvanced(Request $req)
    {
        $movieService = new MovieService();

        $movie_search_advanced = $movieService->searchAdvanced($req);
        while ($movie_search_advanced == null) {
            $movie_search_advanced = $movieService->searchAdvanced($req);
        }

        $output = '<div class="listfilm" style="width: 100%;">
		<div class="recommend__items">
			<div class="recommend__items__title">
				<div class="recommend__items__name">
					<span>Tìm kiếm cho từ khóa:</span>
				</div>
			</div>
			<div class="recommend__item">';
        $image = Session('image') ? Session::get('image') : [];
        $movie_list = Session('movie_list') ? Session::get('movie_list') : [];
        foreach ($movie_search_advanced['searchResults'] as $movie) {
            $urlImage = 'img/' . $movie['domainType'] . $movie['id'] . '.jpg';

            if (!file_exists($urlImage)) {
                $urlImage = $movie['coverVerticalUrl'];
                $image[$movie['domainType'] . $movie['id']] = $movie['coverVerticalUrl'];
            }

            $movie_check = Movie::where('id', $movie['id'])->where('category', $movie['domainType'])->first();
            if ($movie_check == null) {
                $movie_list[$movie['domainType'] . $movie['id']] = ['id' => $movie['id'], 'category' => $movie['domainType'], 'name' => $movie['name']];
            }
            $route = $movie_check == null ? route('movie.detail', ['category' => $movie['domainType'], 'id' => $movie['id'], 'name' => $movie['name']]) : route('detail_name', $movie_check->slug);
            $output .= '
					<a href="' . $route . '" class="card__film">
					<img class="image" src="' . asset($urlImage) . '" alt="image" />
					<p class="film__name">' . $movie['name'] . '</p>
				</a>';
        }

        Session()->put('image', $image);
        Session()->put('movie_list', $movie_list);

        $count = count($movie_search_advanced['searchResults']);

        if ($count == 0) {
            return false;
        }

        $output .= '</div>
        <div class="text-center">
			<div class="lds-facebook">
				<div></div>
				<div></div>
				<div></div>
			</div>
		</div>
        <div id="info" count="' . $count . '" sort="' . $movie_search_advanced['searchResults'][$count - 1]['sort'] . '"></div>
		       </div>
	        </div>';

        $data = [0 => $output, 1 => $count];

        return response()->json($data);
    }

    public function searchMovieAdvancedMore(Request $req)
    {
        $movieService = new MovieService();

        $movie_search_advanced = $movieService->searchAdvanced($req);
        while ($movie_search_advanced == null) {
            $movie_search_advanced = $movieService->searchAdvanced($req);
        }

        $output = '';
        $image = Session('image') ? Session::get('image') : [];
        $movie_list = Session('movie_list') ? Session::get('movie_list') : [];
        foreach ($movie_search_advanced['searchResults'] as $movie) {
            $urlImage = 'img/' . $movie['domainType'] . $movie['id'] . '.jpg';

            if (!file_exists($urlImage)) {
                $urlImage = $movie['coverVerticalUrl'];
                $image[$movie['domainType'] . $movie['id']] = $movie['coverVerticalUrl'];
            }
            $movie_check = Movie::where('id', $movie['id'])->where('category', $movie['domainType'])->first();
            if ($movie_check == null) {
                $movie_list[$movie['domainType'] . $movie['id']] = ['id' => $movie['id'], 'category' => $movie['domainType'], 'name' => $movie['name']];
            }
            $route = $movie_check == null ? route('movie.detail', ['category' => $movie['domainType'], 'id' => $movie['id'], 'name' => $movie['name']]) : route('detail_name', $movie_check->slug);
            $output .= '
					<a href="' . $route . '" class="card__film">
					<img class="image" src="' . asset($urlImage) . '" alt="image" />
					<p class="film__name">' . $movie['name'] . '</p>
				</a>';
        }

        Session()->put('image', $image);
        Session()->put('movie_list', $movie_list);

        $count = count($movie_search_advanced['searchResults']);

        if ($count == 0) {
            return false;
        }

        $info = '<div id="info" count="' . $count . '" sort="' . $movie_search_advanced['searchResults'][$count - 1]['sort'] . '"></div>';

        $data = [0 => $output, 1 => $info, 2 => $count];

        return response()->json($data);
    }

    public function searchMoreMovie($page, $id)
    {
        $movieService = new MovieService();

        $url_movie = 'https://ga-mobile-api.loklok.tv/cms/app/homePage/getHome?page=' . $page;
        $movie_home = $movieService->getData($url_movie);

        while ($movie_home == null) {
            $movie_home = $movieService->getData($url_movie);
        }

        $result = [];
        foreach ($movie_home['recommendItems'] as $keyRecommendItems => $recommendItems) {
            if ($keyRecommendItems == $id) {
                $result = $recommendItems;
            }
        }
        return view('pages.moremovie', compact('result'));
    }

    public function searchKey(Request $req)
    {
        $key = $req->input('keyword');
        return redirect()->route('search', $key);
    }

    public function getHomeAjax(Request $req)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ga-mobile-api.loklok.tv/cms/app/homePage/getHome?page=' . $req->page,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'lang: vi',
                'versioncode: 11',
                'clienttype: ios_jike_default',
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $convert = json_decode($response, true);

        $output = '';

        if (!empty($convert['data'])) {
            $image = Session('image') ? Session::get('image') : [];
            $movie_list = Session('movie_list') ? Session::get('movie_list') : [];

            foreach ($convert['data']['recommendItems'] as $keyRecommendItems => $recommendItems) {
                if ($recommendItems['homeSectionType'] == 'SINGLE_ALBUM') {
                    $output .= '<div class="recommend__items">
                    <div class="recommend__items__title">
                    <div class="recommend__items__name">
                    <span>' . $recommendItems['homeSectionName'] . '</span>
                    </div>
                    <a href="' . route('moremovie', ['page' => $req->page, 'id' => $keyRecommendItems]) . '" class="recommend__items__btn">  
                    <h1> Xem thêm </h1>
                    <svg xmlns="http://www.w3.org/2000/svg" class="arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                    </a>
                    </div>
                    <div class="recommend__item">';
                    foreach ($recommendItems['recommendContentVOList'] as $key => $movie) {
                        if ($key < 12) {
                            $urlImage = 'img/' . $movie['category'] . $movie['id'] . '.jpg';
                            if (!file_exists($urlImage)) {
                                $urlImage = $movie['imageUrl'];
                                $image[$movie['category'] . $movie['id']] = $movie['imageUrl'];
                            }
                            $movie_check = Movie::where('id', $movie['id'])->where('category', $movie['category'])->first();
                            if ($movie_check == null) {
                                $movie_list[$movie['category'] . $movie['id']] = ['id' => $movie['id'], 'category' => $movie['category'], 'name' => $movie['title']];
                            }
                            $route = $movie_check == null ? route('movie.detail', ['category' => $movie['category'], 'id' => $movie['id'], 'name' => $movie['title']]) : route('detail_name', $movie_check->slug);
                            $output .=     '<a href="' . $route . '" class="card__film"> 
                            <img class="image" src="' . asset($urlImage) . '" />
                            <p class="film__name">' . $movie['title'] . '</p>
                            </a>';
                        }
                    }
                    $output .= '</div>
                    </div>';
                }
            }
            $req->session()->put('image', $image);
            Session()->put('movie_list', $movie_list);
        }

        $output .= '<div class="text-center">
                <div class="lds-facebook"><div></div><div></div><div></div></div>
            </div>';

        $data = [$output, $req->page + 1];

        return response()->json($data);
    }
    public function searchMovieCategory($id)
    {
        return view('');
    }

    public function getHeaderAjax(Request $req)
    {
        $movieService = new \App\Services\MovieService();
        $search_advanced_list = $movieService->searchAdvancedList();
        while ($search_advanced_list == null) {
            $search_advanced_list = $movieService->searchAdvancedList();
        }

        $output = '';

        foreach ($search_advanced_list as $as_key => $as_container) {
            $output .= '<div class="as_name" id_key="' . $as_key . '">' . $as_container['name'] . ' <i class="fa-solid fa-caret-down"></i></div>
	    		<div class="as_container" id="as_container' . $as_key . '" params="' . $as_container['params'] . '">';
            foreach ($as_container['screeningItems'] as $key_screening_items => $screening_items) {
                $output .= '<div class="as_items" index="as_' . $screening_items['id'] . '">';
                if ($key_screening_items < 3) {
                    $output .=  '<div class="as_items_name"> ' . __("search_advanced." . $screening_items['name']) . '</div>';
                    foreach ($screening_items['items'] as $key_as_items => $as_item) {
                        $output .= '<div class="as_item" value="' . $as_item['params'] . '" screening_type="' . $as_item['screeningType'] . '" check="' . $as_key . '.' . $as_item['screeningType'] . '#' . $as_item['params'] . '">';
                        if (trans()->has('search_advanced.detail.' . $as_item['name'])) {
                            $output .= __("search_advanced.detail." . $as_item['name']);
                        } else {
                            $output .= $as_item['name'];
                        }
                        $output .= '</div>';
                    }
                }
                $output .= '</div>';
            }
            $output .= '<div class="close_search_advanced">
				<button class="close_search_advanced_btn">Đóng</button>
			</div>
		</div>';
        }

        return response()->json($output);
    }

    public function getFirstHomeAjax()
    {
        $movieService = new \App\Services\MovieService();

        $url_movie = 'https://ga-mobile-api.loklok.tv/cms/app/homePage/getHome?page=0';
        $movie_home = $movieService->getData($url_movie);
        while ($movie_home == null) {
            $movie_home = $movieService->getData($url_movie);
        }

        $url_top = 'https://ga-mobile-api.loklok.tv/cms/app/search/v1/searchLeaderboard';
        $top_search = $movieService->getData($url_top);
        while ($top_search == null) {
            $top_search = $movieService->getData($url_top);
        }

        $output = '';

        $output .= '<div class="listfilm">';
        foreach ($movie_home['recommendItems'] as $keyRecommendItems => $recommendItems) {
            if ($recommendItems['homeSectionType'] == 'BANNER' && sizeof($recommendItems['recommendContentVOList']) > 1) {
                $output .= '<div class="listfilm__top">
                <div class="categorys">
                    <a data="1" class="home__category">Phim hành động</a>
                    <a data="19" class="home__category">Khoa học viễn tưởng</a>
                    <a data="3" class="home__category">Hoạt hình</a>
                    <a data="13" class="home__category">Kinh dị</a>
                    <a data="5" class="home__category">Hài kịch</a>
                    <a data="64" class="home__category">Thảm khốc</a>
                    <a data="24" class="home__category">Chiến tranh</a>
                </div>
                <div class="swiper__slider">
                    <div class="swiper mySwiper">
                        <div class="swiper-wrapper">';
                foreach ($recommendItems['recommendContentVOList'] as $key => $banner) {
                    $output .= '<div class="swiper-slide rounded-xl">
							<img class="banner_img" src="' . $banner['imageUrl'] . '" alt="image" />
						</div>';
                }
                $output .= '</div>
					<div class="swiper-button-next"></div>
					<div class="swiper-button-prev"></div>
					<div class="swiper-pagination"></div>
				</div>
			</div>
		</div>';
            }
            if ($recommendItems['homeSectionType'] == 'SINGLE_ALBUM') {
                $output .= '<div class="recommend__items">
			<div class="recommend__items__title">
				<div class="recommend__items__name">
					<span>' . $recommendItems['homeSectionName'] . '</span>
				</div>

				<a href="' . route('moremovie', ['page' => 0, 'id' => $keyRecommendItems]) . '" class="recommend__items__btn">
					Xem thêm
					<svg xmlns="http://www.w3.org/2000/svg" class="arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
					</svg>
				</a>
			</div>
			<div class="recommend__item">';
                $image = Session('image') ? Session::get('image') : [];
                $movie_list = Session('movie_list') ? Session::get('movie_list') : [];

                foreach ($recommendItems['recommendContentVOList'] as $key => $movie) {
                    if ($key < 6) {
                        $output .= '<a href="';
                        $movie_check = Movie::where('id', $movie['id'])->where('category', $movie['category'])->first();
                        $output .= $movie_check == null ? route('movie.detail', ['category' => $movie['category'], 'id' => $movie['id'], 'name' => $movie['title']]) : route('detail_name', $movie_check->slug);
                        $output .= '" class="card__film">';

                        $urlImage = 'img/' . $movie['category'] . $movie['id'] . '.jpg';
                        if (!file_exists($urlImage)) {
                            $urlImage = $movie['imageUrl'];
                            $image[$movie['category'] . $movie['id']] = $movie['imageUrl'];
                        }
                        $movie_check = Movie::where('id', $movie['id'])->where('category', $movie['category'])->first();
                        if ($movie_check == null) {
                            $movie_list[$movie['category'] . $movie['id']] = ['id' => $movie['id'], 'category' => $movie['category'], 'name' => $movie['title']];
                        }

                        $output .= '<img class="image" src="' . asset($urlImage) . '" alt="image" />
					<p class="film__name">' . $movie['title'] . '</p>
					</a>';
                    }
                }
                Session()->put('image', $image);
                Session()->put('movie_list', $movie_list);
                $output .= '</div>
                </div>';
            }
        }
        $output .= '
            <div class="text-center">
                <div class="lds-facebook">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>';

        $output .= '<div class="top_search">
		<div class="top_search__title">Top tìm kiếm</div>';
        $image = Session('image') ? Session::get('image') : [];
        $movie_list = Session('movie_list') ? Session::get('movie_list') : [];

        foreach ($top_search['list'] as $movie) {
            $output .= '<a href="';
            $movie_check = Movie::where('id', $movie['id'])->where('category', $movie['domainType'])->first();
            $output .= $movie_check == null ? route('movie.detail', ['category' => $movie['domainType'], 'id' => $movie['id'], 'name' => $movie['title']]) : route('detail_name', $movie_check->slug);
            $output .= '" class="top_search__card">';

            $urlImage = 'img/' . $movie['domainType'] . $movie['id'] . 'top_search.jpg';
            if (!file_exists($urlImage)) {
                $urlImage = $movie['cover'];
                $image[$movie['domainType'] . $movie['id'] . 'top_search'] = $movie['cover'];
            }
            $movie_check = Movie::where('id', $movie['id'])->where('category', $movie['domainType'])->first();
            if ($movie_check == null) {
                $movie_list[$movie['domainType'] . $movie['id']] = ['id' => $movie['id'], 'category' => $movie['domainType'], 'name' => $movie['title']];
            }
            $output .=
                '<img src="' . asset($urlImage) . '" class="top_search__card__img">
			<div class="top_search__card__name">' . $movie['title'] . '</div>
		</a>';
        }
        Session()->put('image', $image);
        Session()->put('movie_list', $movie_list);
        $output .= '</div>';

        return response()->json($output);
    }

    public function getTraffic() {
        $movies = Movie::where('traffic', '>', 0)->get();

        return view('pages.traffic', compact('movies'));
    }
}
