<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MovieService;
use Image;
use Session;

class HomeController extends Controller
{
    public function getHomePage(Request $req) {
        $movieService = new MovieService();
        $url_movie = 'https://ga-mobile-api.loklok.tv/cms/app/homePage/getHome?page=0';
        $movie_home = $movieService->getData($url_movie);

        $url_top = 'https://ga-mobile-api.loklok.tv/cms/app/search/v1/searchLeaderboard';
        $top_search = $movieService->getData($url_top);

        while ($movie_home == null) {
            $movie_home = $movieService->getData($url_movie);
        }
        while ($top_search == null) {
            $url_top = $movieService->getData($url_top);
        }

        // dd($movie_home);


        // getimagesize('img/09079.jpg');
        // $url = 'https://img.netpop.app/cover/20220310/1646891823998_905d77e4004c1aaab5633a29fff51ed7我们的蓝调 13596竖版.png';
        // $url = str_replace(' ', '%20', $url);
        // dd(getimagesize($url));

        // dd(route('home'));

        return view('pages.home', compact('movie_home', 'top_search'));
    }

    public function getTest() {
        require_once 'HTTP/Request2.php';
        $request = new HTTP_Request2();
        $request->setUrl('https://ga-mobile-api.loklok.tv/cms/app/homePage/getHome?page=0');
        $request->setMethod(HTTP_Request2::METHOD_GET);
        $request->setConfig(array(
            'follow_redirects' => TRUE
        ));
        $request->setHeader(array(
            'lang' => 'en',
            'versioncode' => '11',
            'clienttype' => 'ios_jike_default'
        ));
        try {
            $response = $request->send();
            if ($response->getStatus() == 200) {
                echo $response->getBody();
            }
            else {
                echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
                $response->getReasonPhrase();
            }
        }
        catch(HTTP_Request2_Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function searchMovie($key) {

        $movieService = new MovieService();
        $movieSearchWithKey = $movieService->searchWithKeyWord($key);
        
        while ($movieSearchWithKey == null) {
            $movieSearchWithKey = $movieService->searchWithKeyWord($key);
        }

        return view('pages.search', compact('movieSearchWithKey', 'key'));
    }

    public function searchMovieDetail($id) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ga-mobile-api.loklok.tv/cms/app/search/v1/search',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "size": 60,
                "params": "MOVIE,TVSPECIAL",
                "area": "",
                "category": "'.$id.'",
                "year": "",
                "subtitles": "",
                "order": "up"
            }',
            CURLOPT_HTTPHEADER => array(
                'lang: en',
                'versioncode: 11',
                'clienttype: ios_jike_default',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $convert=json_decode($response,true);
        $key = $id;
        return view('pages.search', compact('convert', 'key'));
    }

    public function searchMoreMovie($page, $id) {
        $movieService = new MovieService();

        $url_movie = 'https://ga-mobile-api.loklok.tv/cms/app/homePage/getHome?page='. $page;
        $movie_home = $movieService->getData($url_movie);
        $result = [];
        foreach($movie_home['recommendItems'] as $keyRecommendItems => $recommendItems) {
            if($keyRecommendItems == $id) {
                $result = $recommendItems;
            }
        }
        return view('pages.moremovie', compact('result'));
    }

    public function searchKey(Request $req) {
        $key = $req->input('keyword');
        return redirect()->route('search', $key);
    }

    public function getHomeAjax(Request $req) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ga-mobile-api.loklok.tv/cms/app/homePage/getHome?page='.$req->page,
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
        $convert=json_decode($response,true);

        $output = '';

        if(!empty($convert['data'])) {
            $image = Session('image')?Session::get('image'):[];

            foreach($convert['data']['recommendItems'] as $keyRecommendItems => $recommendItems) {
                if($recommendItems['homeSectionType'] == 'SINGLE_ALBUM') {
                    $output .= '<div class="recommend__items">
                    <div class="recommend__items__title">
                    <div class="recommend__items__name">
                    <span>'.$recommendItems['homeSectionName'].'</span>
                    </div>
                    <a href="'. route('moremovie', ['page' => $req->page, 'id' => $keyRecommendItems]).'" class="recommend__items__btn">  
                    <h1> Xem thêm </h1>
                    <svg xmlns="http://www.w3.org/2000/svg" class="arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                    </a>
                    </div>
                    <div class="recommend__item">';
                    foreach($recommendItems['recommendContentVOList'] as $key => $movie) {
                        if($key < 12) {
                            $urlImage = 'img/'.$movie['category'].$movie['id'].'.jpg';
                            if(!file_exists($urlImage)) {
                                $urlImage = $movie['imageUrl'];
                                $image[$movie['category'].$movie['id']] = $movie['imageUrl'];
                            }
                            $output .=     '<a href="'. route('movie.detail', ['category' => $movie['category'], 'id' => $movie['id']]) .'" class="card__film"> 
                            <img class="image" src="'.asset($urlImage).'" />
                            <p class="film__name">'.$movie['title'].'</p>
                            </a>';
                        }
                    }
                    $output .= '</div>
                    </div>';

                }
            }
            $req->session()->put('image', $image);
        }

        $output .= '<div class="text-center">
                <div class="lds-facebook"><div></div><div></div><div></div></div>
            </div>';
        
        $data = [$output, $req->page + 1];

        return $data;
    }
}
