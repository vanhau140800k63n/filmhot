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

        // $urltest = 'https://ga-mobile-api.loklok.tv/cms/app/search/list';
        // $test = $movieService->getData($urltest);
        // dd($test);

        return view('pages.home', compact('movie_home', 'top_search'));
    }

    public function getTest() {

    }

    public function searchMovie($key) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ga-mobile-api.loklok.tv/cms/app/search/v1/searchWithKeyWord',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "searchKeyWord": "'.$key.'",
                "size": 50,
                "sort": "",
                "searchType": ""
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
        return view('pages.search', compact('convert', 'key'));
    }

    public function searchMovieCategory($id) {
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
                'clienttype: ios_jike_default'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $convert=json_decode($response,true);

        $output = '';

        if(!empty($convert['data'])) {
            $image = Session('image')?Session::get('image'):[];

            foreach($convert['data']['recommendItems'] as $key => $recommendItems) {
                if($recommendItems['homeSectionType'] == 'SINGLE_ALBUM') {
                    $output .= '<div class="mb-8">
                    <div class="flex items-center mt-6 mb-2 justify-between">
                    <div class="flex items-center gap-2 text-[24px] font-semibold">
                    <span>'.$recommendItems['homeSectionName'].'</span>
                    </div>
                    <div class="">
                    <a href="">
                    <button class="flex items-center gap-1 text-[16px] font-medium text-white rounded-[10px] px-2 py-1">
                    <h1>Xem thêm</h1>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                    </button>
                    </a>
                    </div>
                    </div>
                    <div class="grid grid-cols-6 gap-4">';
                    foreach($recommendItems['recommendContentVOList'] as $key => $movie) {
                        if($key < 12) {
                            $urlImage = 'img/'.$movie['category'].$movie['id'].'.jpg';
                            if(!file_exists($urlImage)) {
                                $urlImage = $movie['imageUrl'];
                                $image[$movie['category'].$movie['id']] = $movie['imageUrl'];
                            }
                            $output .=     '<a href="movies/category='.$movie['category'].'&id='.$movie['id'].'" class="bg-[#27282d] rounded-xl"> 
                            <img class="object-cover w-full rounded-t-xl" style="max-height:'. $req->width*14/10 .'px"
                            src="'.$urlImage.'" />
                            <div class="mx-4 text-center">
                            <h2 class="text-gray-100 py-1 text-[14px] film__name">'.$movie['title'].'</h2>
                            </div>
                            </a>';
                        }
                    }
                    $output .=     '</div>
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
