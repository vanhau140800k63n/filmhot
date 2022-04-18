<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MovieService;
use App\Models\Movie;
use Session;

class HomeController extends Controller
{
    public function getHomePage()
    {
        // $movie_list = Session('movie_list') ? Session::get('movie_list') : [];
        // dd($movie_list);
        return view('pages.home');
    }

    public function getTest()
    {
        $movies = Movie::all();
        foreach ($movies as $item) {
            if (!str_contains($item->slug, '?')) {
                echo "&lt;url&gt;" . '<br>';
                echo "&lt;loc&gt;" . route('detail_name', $item->slug) . "&lt;/loc&gt;<br>";
                echo "&lt;lastmod&gt;2022-04-15T19:03:57+00:00&lt;/lastmod&gt;<br>";
                echo "&lt;priority&gt;1.00&lt;/priority&gt;<br>";
                echo "&lt;/url&gt;<br>";
            }
        }
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
            $route = $movie_check == null ? route('movie.detail', ['category' => $movie['domainType'], 'id' => $movie['id']]) : route('detail_name', $movie_check->slug);
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
            $route = $movie_check == null ? route('movie.detail', ['category' => $movie['domainType'], 'id' => $movie['id']]) : route('detail_name', $movie_check->slug);
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
                            $route = $movie_check == null ? route('movie.detail', ['category' => $movie['category'], 'id' => $movie['id']]) : route('detail_name', $movie_check->slug);
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
}
