<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MovieService;
use App\Models\Movie;
use App\Exceptions\PageException;

class MovieController extends Controller
{
    // public MovieService 

    public function getMovie($category, $id)
    {
        $movie = Movie::where('id', $id)->where('category', $category)->first();
        if($movie != null) {
            return redirect()->route('detail_name', $movie->slug);
        }
        $movieService = new MovieService();
        $url = 'https://ga-mobile-api.loklok.tv/cms/app/movieDrama/get?id=' . $id . '&category=' . $category;
        $movie_detail = $movieService->getData($url);

        while ($movie_detail == null) {
            $movie_detail = $movieService->getData($url);
        }

        $episode_id = null;
        $definitionList = [];
        if (!empty($movie_detail['episodeVo'])) {
            $definitionList = $movie_detail['episodeVo'][0]['definitionList'];
            $episode_id = 0;
        }

        return view('pages.movie', compact('movie_detail', 'episode_id', 'definitionList'));
    }

    public function getMovieByName($name)
    {
        $movie = Movie::where('slug', $name)->first();

        if ($movie == null) {
            throw new PageException();
        }

        $movieService = new MovieService();
        $url = 'https://ga-mobile-api.loklok.tv/cms/app/movieDrama/get?id=' . $movie->id . '&category=' . $movie->category;
        $movie_detail = $movieService->getData($url);

        while ($movie_detail == null) {
            $movie_detail = $movieService->getData($url);
        }

        $episode_id = null;
        $definitionList = [];
        if (!empty($movie_detail['episodeVo'])) {
            $definitionList = $movie_detail['episodeVo'][0]['definitionList'];
            $episode_id = 0;
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
                } else if($size >= 7) {
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
            $movie->save();
        }

        $media = [];
        if (!empty($movie_detail['episodeVo'])) {
            $definitionList = $movie_detail['episodeVo'][0]['definitionList'];
            $media = $this->getEpisode($movie->category, $movie->id, $movie_detail['episodeVo'][0]['id'], $definitionList[0]['code']);
           
        }

        // dd($media);

        return view('pages.movie', compact('movie_detail', 'episode_id', 'definitionList', 'movie', 'media'));
    }

    function getEpisode($category, $id, $episodeId, $definition)
    {

        $movieService = new MovieService();
        $url = 'https://ga-mobile-api.loklok.tv/cms/app/media/previewInfo?category=' . $category . '&contentId=' . $id . '&episodeId=' . $episodeId . '&definition=' . $definition;
        $media = $movieService->getData($url);

        while ($media == null) {
            $media = $movieService->getData($url);
        }

        return $media;
    }

    public function getEpisodeAjax(Request $req)
    {
        $movieService = new MovieService();
        $url = 'https://ga-mobile-api.loklok.tv/cms/app/movieDrama/get?id=' . $req->id . '&category=' . $req->category;
        $movie_detail = $movieService->getData($url);
        while ($movie_detail == null) {
            $movie_detail = $movieService->getData($url);
        }
        $media = [];
        if (!empty($movie_detail['episodeVo'])) {
            $definitionList = $movie_detail['episodeVo'][$req->episode_id]['definitionList'];
            if ($req->definition == null) {
                $media = $this->getEpisode($req->category, $req->id, $movie_detail['episodeVo'][$req->episode_id]['id'], $definitionList[0]['code']);
            } else {
                $media = $this->getEpisode($req->category, $req->id, $movie_detail['episodeVo'][$req->episode_id]['id'], $req->definition);
            }
        }

        $episode_title = "";
        // $film_url = 'category=' . $req->category . '&id=' . $req->id;
        $film_url = route('movie.detail', ['category' => $req->category, 'id' => $req->id]);
        $episode = $req->episode_id + 1;

        if ($movie_detail['episodeCount'] > 1) {
            $film_url = 'category=' . $req->category . '&id=' . $req->id . '&episode=' . $episode;
            $film_url = route('movie.episode', ['category' => $req->category, 'id' => $req->id, 'episode' => $episode]);
            $episode_title = " - Tập " . $episode;
        }

        array_push($media, $definitionList, $movie_detail['episodeVo'][$req->episode_id]['subtitlingList'], $film_url, $episode_title);
        return $media;
    }

    public function getMovieEpisode($category, $id, $episode)
    {
        $episode_id = --$episode;
        $movieService = new MovieService();

        $url = 'https://ga-mobile-api.loklok.tv/cms/app/movieDrama/get?id=' . $id . '&category=' . $category;
        $movie_detail = $movieService->getData($url);
        while ($movie_detail == null) {
            $movie_detail = $movieService->getData($url);
        }

        if (!empty($movie_detail['episodeVo'])) {
            $definitionList = $movie_detail['episodeVo'][$episode]['definitionList'];
        }
        return view('pages.movie', compact('movie_detail', 'episode_id', 'definitionList'));
    }
}
