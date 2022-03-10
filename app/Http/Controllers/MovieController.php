<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function getMovie($category, $id) {
        $curl = curl_init();
        // {{ route('movie', ['category' => $movie['category'], 'id' => $movie['id']]) }}

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ga-mobile-api.loklok.tv/cms/app/movieDrama/get?id='.$id.'&category='.$category,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'lang: en',
                'versioncode: 11',
                'clienttype: ios_jike_default'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $movie =json_decode($response,true);
        $movie_detail = $movie['data'];
        $media = $this->getMovieEpisode($category, $id, $movie_detail['episodeVo'][0]['id']);  
        // dd($movie_detail);  
        return view('pages.movie', compact('movie_detail', 'media'));
    }

    function getMovieEpisode($category, $id, $episodeId) {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ga-mobile-api.loklok.tv/cms/app/media/previewInfo?category='.$category.'&contentId='.$id.'&episodeId='.$episodeId.'&definition=GROOT_LD',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'lang: en',
                'versioncode: 11',
                'clienttype: ios_jike_default'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $media = json_decode($response,true);
        return $media['data'];
    }
}
