<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MovieService;
use App\Models\Movie;
use App\Exceptions\PageException;

class MovieController extends Controller
{
    // public MovieService 

    public function getMovie($category, $id, $name)
    {
        $movie = Movie::where('id', $id)->where('category', $category)->first();

        $str =  $name;
        if ($movie == null) {
            $movie = new Movie();
            $movie->id = $id;
            $movie->category = $category;
            $movie->meta = '';

            $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
            $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
            $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
            $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
            $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
            $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
            $str = preg_replace("/(đ)/", 'd', $str);
            $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
            $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
            $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
            $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
            $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
            $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
            $str = preg_replace("/(Đ)/", 'D', $str);
            $str = preg_replace("/(\“|\”|\‘|\’|\,|\!|\&|\;|\@|\#|\%|\~|\`|\=|\_|\'|\]|\[|\}|\{|\)|\(|\+|\^|\/|\:)/", '-', $str);
            $str = preg_replace("/( )/", '-', $str);
            $str = preg_replace("/(---)/", '-', $str);
            $str = preg_replace("/(--)/", '-', $str);
            $str = strtolower($str);

            if (substr($str, strlen($str) - 1, 1) == '-') {
                $str = substr($str, 0, strlen($str) - 1);
            }

            $str .= '-full-hd-vietsub.html';

            $movie->slug = $str;
            $movie->save();
        }
        
        return redirect()->route('detail_name', $movie->slug);
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
            $movie->save();
        }

        $media = [];
        if (!empty($movie_detail['episodeVo'])) {
            $definitionList = $movie_detail['episodeVo'][0]['definitionList'];
            $media = $this->getEpisode($movie->category, $movie->id, $movie_detail['episodeVo'][0]['id'], $definitionList[0]['code']);
        }

        // dd($movie_detail);

        return view('pages.movie', compact('movie_detail', 'episode_id', 'definitionList', 'movie', 'media'));
    }

    public function getMovieByNameEposode($name, $episode_id)
    {
        --$episode_id;
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
            $movie->save();
        }

        $definitionList = [];
        $media = [];

        if (!empty($movie_detail['episodeVo'])) {
            $definitionList = $movie_detail['episodeVo'][$episode_id]['definitionList'];
            $media = $this->getEpisode($movie->category, $movie->id, $movie_detail['episodeVo'][$episode_id]['id'], $definitionList[0]['code']);
        }
         
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

        array_push($media);
        return $media;
    }
}
