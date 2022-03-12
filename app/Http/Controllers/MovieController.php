<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MovieService;

class MovieController extends Controller
{
    // public MovieService 

    public function getMovie($category, $id) {
        $movieService = new MovieService();
        $url = 'https://ga-mobile-api.loklok.tv/cms/app/movieDrama/get?id='.$id.'&category='.$category;
        $movie_detail = $movieService->getData($url);
        // dd($movie_detail);
        $episode_id = null;

        if(!empty($movie_detail['episodeVo'])) {
            $definitionList = $movie_detail['episodeVo'][0]['definitionList'];
            $episode_id = 0;
        }

        return view('pages.movie', compact('movie_detail', 'episode_id', 'definitionList'));
    }

    function getEpisode($category, $id, $episodeId, $definition) {

        $movieService = new MovieService();
        $url = 'https://ga-mobile-api.loklok.tv/cms/app/media/previewInfo?category='.$category.'&contentId='.$id.'&episodeId='.$episodeId.'&definition='.$definition;
        $media = $movieService->getData($url);
        
        return $media;
    }

    public function getEpisodeAjax(Request $req) {
        $movieService = new MovieService();
        $url = 'https://ga-mobile-api.loklok.tv/cms/app/movieDrama/get?id='.$req->id.'&category='.$req->category;
        $movie_detail = $movieService->getData($url);
        $media = [];
        if(!empty($movie_detail['episodeVo'])) {
            $definitionList = $movie_detail['episodeVo'][$req->episode_id]['definitionList'];
            $media = $this->getEpisode($req->category, $req->id, $movie_detail['episodeVo'][$req->episode_id]['id'], $definitionList[0]['code']); 
        }
        array_push($media, $definitionList);

        return $media;
    }

    public function getMovieEpisode($category, $id, $episode) {
        $episode_id = --$episode;
        $movieService = new MovieService();

        $url = 'https://ga-mobile-api.loklok.tv/cms/app/movieDrama/get?id='.$id.'&category='.$category;
        $movie_detail = $movieService->getData($url);

        if(!empty($movie_detail['episodeVo'])) {
            $definitionList = $movie_detail['episodeVo'][$episode]['definitionList'];
        } 
        return view('pages.movie', compact('movie_detail', 'episode_id', 'definitionList'));
    }
}
