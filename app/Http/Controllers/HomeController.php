<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Image;
use Session;

class HomeController extends Controller
{
    public function getHomePage(Request $req) {
        $image = Session('image')?Session::get('image'):null;

        // $image = array(['a' => 1, 'b' => 2], ['c' => 3]);

        // 
        // array_push($image, ['6' => 6]);
        // unset($image[1]);
        // array_pop($image);
        // $req->session()->flush();
        // dd(Session('image'));
    
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ga-mobile-api.loklok.tv/cms/app/homePage/getHome?page=0',
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
        $convert = json_decode($response,true);
        return view('pages.home', compact('convert'));

        dd($convert);
        // if(!empty($convert['data'])) {
        //     if($index + 1 <= sizeof($convert['data']['recommendItems'])) {
        //         $item = $convert['data']['recommendItems'][$index];
        //         if($item['homeSectionType'] == 'SINGLE_ALBUM') {
        //             foreach($item['recommendContentVOList'] as $recommendContentVOList) {
        //                 $img = $recommendContentVOList['imageUrl'];
        //                 $img = str_replace(' ', '%20', $img);
        //                 $img = file_get_contents($img);
        //                 $imgFile = Image::make($img);

        //                 $imgFile->resize(300, null, function ($constraint) {
        //                     $constraint->aspectRatio();
        //                 });
        //                 $imgFile->save('img/'.$recommendContentVOList['category'].$recommendContentVOList['id'].'.jpg');
        //             }
        //         }
        //     } else {
        //         ++$id;
        //         $index = 0;
        //         return view('pages.home', compact('convert', 'id', 'index'));
        //     }

        // }

        // ++$index;

        // return view('pages.home', compact('convert', 'id', 'index'));
        

        // dd($convert);

        // foreach() {

        // }
        
        // return view('pages.home', compact('convert'));
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
        return view('pages.search', compact('convert'));
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
                    $output .= '<div class="mt-8">
                    <div class="flex items-center mt-6 mb-2 justify-between">
                    <div class="flex items-center gap-2 text-[24px] font-semibold">
                    <div class="fade-loading"></div>
                    <span>'.$recommendItems['homeSectionName'].'</span>
                    </div>
                    <div class="">
                    <a href="">
                    <button class="flex items-center gap-1 text-[16px] font-medium bg-pink-400 hover:bg-pink-300 text-white rounded-full px-2 py-1">
                    <h1>Xem thêm</h1>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                    </button>
                    </a>
                    </div>
                    </div>
                    <div class="grid grid-cols-5 gap-4">';
                    foreach($recommendItems['recommendContentVOList'] as $key => $movie) {
                        $urlImage = 'img/'.$movie['category'].$movie['id'].'.jpg';
                        if(!file_exists($urlImage)) {
                            $urlImage = $movie['imageUrl'];
                            $image[$movie['category'].$movie['id']] = $movie['imageUrl'];
                        }
                        $output .=     '<a href="movies/category='.$movie['category'].'&id='.$movie['id'].'" class="bg-[#27282d] rounded-xl"> 
                        <img class="object-cover w-full h-[230px] rounded-t-xl"
                        src="'.$urlImage.'" />
                        <div class="max-h-[40px] mx-4  text-ellipsis overflow-hidden">
                        <h2 class="text-gray-100 py-1 text-[16px] whitespace-nowrap">'.$movie['title'].'</h2>
                        </div>
                        </a>';
                    }
                    $output .=     '</div>
                    </div>';

                }
            }
            $req->session()->put('image', $image);
        }
        
        $data = [$output, ++$req->page];

        return $data;
    }
}
