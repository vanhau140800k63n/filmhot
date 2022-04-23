<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MovieService;
use App\Models\Movie;
use App\Exceptions\PageException;
use Session;

class MovieController extends Controller
{
    // public MovieService 

    public function getMovieEdit($name)
    {
        $movie = Movie::where('slug', $name)->first();
        if ($movie == null) {
            throw new PageException();
        }
        return view('pages.edit', compact('movie'));
    }

    public function getMovieUpdate(Request $req, $name)
    {
        $movie = Movie::where('slug', $name)->first();
        if ($movie == null) {
            throw new PageException();
        }
        $movie->description = $req->all()['description'];
        $movie->save();
        return redirect()->route('detail_name', $movie->slug);
    }

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

        $episode_id = 0;

        return view('pages.movie', compact('episode_id', 'movie', 'name'));
    }

    public function getMovieByNameEposode($name, $episode_id)
    {
        --$episode_id;
        $movie = Movie::where('slug', $name)->first();

        if ($movie == null) {
            throw new PageException();
        }

        return view('pages.movie', compact('episode_id', 'movie', 'name'));
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

    public function getViewMovieAjax(Request $req)
    {
        $movie = Movie::where('slug', $req->name)->first();

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

        $output = '';

        $output .= '<div class="movie__container">
        <div class="movie__media" id="movie__media">
            <input id="media" id_media="' . $movie_detail['id'] . '" category="' . $movie_detail['category'] . '" id_episode="' . $req->episode_id . '" class="hidden">
            <video class="movie__screen video-js" id="video_media" preload="auto" data-setup="{}" controls autoplay>
                <source src="ThuyDung" type="application/x-mpegURL">';
        foreach ($movie_detail['episodeVo'][$req->episode_id]['subtitlingList'] as $subtitle) {
            if ($subtitle['languageAbbr'] == 'vi') {
                $output .= '<track id="subtitles" kind="subtitles" label="' . $subtitle['language'] . '" srclang="' . $subtitle['languageAbbr'] . '" src="https://srt-to-vtt.vercel.app/?url=' . $subtitle['subtitlingUrl'] . '">';
            }
        }
        $output .= '</video>
            <div class="movie__load">
                <div id="loading_movie"></div>
            </div>
        </div>
        <h1 class="movie__name" id="' . $movie_detail['name'] . '">';
        $output .= $movie_detail['episodeCount'] > 1 ? $movie_detail['name'] . ' - Tập ' . ($req->episode_id + 1) : $movie_detail['name'];
        $output .=  '
        </h1>
        <div class="movie__episodes">';
        if ($movie_detail['episodeCount'] > 1) {
            foreach ($movie_detail['episodeVo'] as $key => $episode) {
                $output .= '<a class="episode';
                $output .= intval($key) == intval($req->episode_id) ? ' active' : '';

                $output .= '" id="' . ($key + 1) . '" href="' . route('detail_name_episode', ['name' => $movie->slug, 'episode_id' => $key + 1]) . '">' . ($key + 1) . ' </a>';
            }
        }
        $output .= '</div>
        <div class="movie__info">
            <div class="movie__score"> <i class="fa-solid fa-star"></i> ' . $movie_detail['score'] . '</div>
            <div class="movie__year"> <i class="fa-solid fa-calendar"></i> ' . $movie_detail['year'] . '</div>
        </div>
        <div class="movie__tag">';
        foreach ($movie_detail['tagList'] as $item) {
            $output .= '<div class="tag__name" id_tag="' . $item['id'] . '">';
            if (trans()->has('search_advanced.detail.' . $item['name'])) {
                $output .=  __('search_advanced.detail.' . $item['name']);
            } else {
                $output .= $item['name'];
            }
            $output .= '</div>';
        }
        $output .= '</div>
        <div class="movie__intro">' . $movie_detail['introduction'] . ' <br>
            ' . $movie->description . '
        </div>
        <div class="comment_title"> Bình luận </div>
        <div style="background-color: #fff;">
            <div data-width="100%" class="fb-comments" data-href="{{ $url }}" data-width="" data-numposts="5"></div>
        </div>
        </div>';

        $output .= '<div class="movie__similar">';

        $image = Session('image') ? Session::get('image') : [];
        $movie_list = Session('movie_list') ? Session::get('movie_list') : [];
        foreach ($movie_detail['likeList'] as $movie) {
            $output .= '<a class="similar__container" href="';

            $movie_check = Movie::where('id', $movie['id'])->where('category', $movie['category'])->first();
            $output .= $movie_check == null ? route('movie.detail', ['category' => $movie['category'], 'id' => $movie['id'], 'name' => $movie['name']]) : route('detail_name', $movie_check->slug);

            $output .= '">';

            $urlImage = 'img/' . $movie['category'] . $movie['id'] . '.jpg';
            if (!file_exists($urlImage)) {
                $urlImage = $movie['coverVerticalUrl'];
                $image[$movie['category'] . $movie['id']] = $movie['coverVerticalUrl'];
            }
            $movie_check = Movie::where('id', $movie['id'])->where('category', $movie['category'])->first();
            if ($movie_check == null) {
                $movie_list[$movie['category'] . $movie['id']] = ['id' => $movie['id'], 'category' => $movie['category'], 'name' => $movie['name']];
            }

            $output .= '<img src="' . asset($urlImage) . '">
           <div class="similar__name">' . $movie['name'] . '</div>
       </a>';
        }
        Session()->put('image', $image);
        Session()->put('movie_list', $movie_list);
        $output .= '</div>';

        $data = [];

        array_push($data, $movie_detail, $output);
        return response()->json($data);
    }
}
