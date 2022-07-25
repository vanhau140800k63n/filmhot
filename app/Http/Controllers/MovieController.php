<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MovieService;
use App\Models\Movie;
use App\Models\Product;
use App\Exceptions\PageException;
use Session;

class MovieController extends Controller
{
    public function getMovieEdit($name)
    {
        $movie = Movie::where('slug', $name)->first();
        if ($movie == null) {
            throw new PageException();
        }
        // return view('pages.edit', compact('movie'));
    }

    public function getMovieUpdate(Request $req, $name)
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

        $sub = '';
        $sub_en = '';

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

            $checksub_en = false;
            if ($episodeVo['subtitlingList'] != null) {
                foreach ($episodeVo['subtitlingList'] as $subtitle) {
                    if ($subtitle['languageAbbr'] == 'en') {
                        $checksub_en = true;
                        $sub_en .= '-' . $key_episodeVo . '-https://srt-to-vtt.vercel.app/?url=' . $subtitle['subtitlingUrl'] . '+' . $key_episodeVo . '+';
                    }
                }
                if (!$checksub_en) {
                    $sub_en .= '-' . $key_episodeVo . '-' . '+' . $key_episodeVo . '+';
                }
            } else {
                $sub_en .= '-' . $key_episodeVo . '-' . '+' . $key_episodeVo . '+';
            }
        }


        $movie->sub = $sub;
        $movie->sub_en = $sub_en;

        if (isset($req->description)) {
            $movie->description = $req->description;
        }

        $movie->save();

        return redirect()->route('detail_name', $movie->slug);
    }

    public function postMovieUpdate(Request $req, $name)
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

        $sub = '';
        $sub_en = '';

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

            $checksub_en = false;
            if ($episodeVo['subtitlingList'] != null) {
                foreach ($episodeVo['subtitlingList'] as $subtitle) {
                    if ($subtitle['languageAbbr'] == 'en') {
                        $checksub_en = true;
                        $sub_en .= '-' . $key_episodeVo . '-https://srt-to-vtt.vercel.app/?url=' . $subtitle['subtitlingUrl'] . '+' . $key_episodeVo . '+';
                    }
                }
                if (!$checksub_en) {
                    $sub_en .= '-' . $key_episodeVo . '-' . '+' . $key_episodeVo . '+';
                }
            } else {
                $sub_en .= '-' . $key_episodeVo . '-' . '+' . $key_episodeVo . '+';
            }
        }


        $movie->sub = $sub;
        $movie->sub_en = $sub_en;

        if (isset($req->description)) {
            $movie->description = $req->description;
        }

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
        // $pos = strpos($name, '.html');
        // $name_check = substr($name, 0, $pos);
        $movie_detail = Movie::where('slug', $name)->first();

        if ($movie_detail == null) {
            throw new PageException();
        }

        // $name = $movie_detail->slug;

        $episode_id = 0;
        $url = route('detail_name', $name);

        $start_pos = strpos($movie_detail->sub, '-' . $episode_id . '-') + strlen($episode_id) + 2;
        $end_pos = strpos($movie_detail->sub, '+' . $episode_id . '+');

        $sub = '';
        if ($start_pos < $end_pos) {
            $sub = substr($movie_detail->sub, $start_pos, $end_pos - $start_pos);
        }

        $start_pos_en = strpos($movie_detail->sub_en, '-' . $episode_id . '-') + strlen($episode_id) + 2;
        $end_pos_en = strpos($movie_detail->sub_en, '+' . $episode_id . '+');

        $sub_en = '';
        if ($start_pos_en < $end_pos_en) {
            $sub_en = substr($movie_detail->sub_en, $start_pos_en, $end_pos_en - $start_pos_en);
        }

        $movie_detail->traffic += 1;
        $movie_detail->save();

        $random_movies =  Movie::inRandomOrder()->take(30)->get();

        foreach ($random_movies as $key => $movie) {
            if (!file_exists('img/' . $movie->category . $movie->id . '.jpg') || empty($movie->name)) {
                $random_movies->forget($key);
            }
        }

        $productAll = Product::where('image', 'like', '%' . 'http' . '%')->inRandomOrder()->take(0)->orderBy('point', 'asc')->get();

        return view('pages.movie', compact('episode_id', 'movie_detail', 'name', 'url', 'productAll', 'sub', 'sub_en', 'random_movies'));
    }

    public function getMovieByNameEposode($name, $episode_id)
    {
        --$episode_id;

        $movie_detail = Movie::where('slug', $name)->first();

        if ($movie_detail == null) {
            throw new PageException();
        }

        // $name = $movie_detail->slug;
        $url = route('detail_name', $name);

        $start_pos = strpos($movie_detail->sub, '-' . $episode_id . '-') + strlen($episode_id) + 2;
        $end_pos = strpos($movie_detail->sub, '+' . $episode_id . '+');

        $sub = '';
        if ($start_pos < $end_pos) {
            $sub = substr($movie_detail->sub, $start_pos, $end_pos - $start_pos);
        }

        $start_pos_en = strpos($movie_detail->sub_en, '-' . $episode_id . '-') + strlen($episode_id) + 2;
        $end_pos_en = strpos($movie_detail->sub_en, '+' . $episode_id . '+');

        $sub_en = '';
        if ($start_pos_en < $end_pos_en) {
            $sub_en = substr($movie_detail->sub_en, $start_pos_en, $end_pos_en - $start_pos_en);
        }

        $productAll = Product::where('image', 'like', '%' . 'http' . '%')->inRandomOrder()->take(0)->orderBy('point', 'asc')->get();
        $random_movies =  Movie::inRandomOrder()->take(30)->get();

        foreach ($random_movies as $key => $movie) {
            if (!file_exists('img/' . $movie->category . $movie->id . '.jpg') || empty($movie->name)) {
                $random_movies->forget($key);
            }
        }

        $movie_detail->traffic += 1;
        $movie_detail->save();

        return view('pages.movie', compact('episode_id', 'movie_detail', 'name', 'url', 'productAll', 'sub', 'sub_en', 'random_movies'));
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

        $checksub = true;

        $count_episodes = count($movie_detail['episodeVo']) - 1;
        if (!str_contains($movie->sub, '-' . $count_episodes . '-')) {
            $checksub = false;
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

        $meta = $movie->meta;
        $movie->save();

        if ($req->episode_id == 0) {
            $urlMovie = route('detail_name', $movie->slug);
        } else {
            $urlMovie = route('detail_name_episode', ['name' => $movie->slug, 'episode_id' => $req->episode_id + 1]);
        }

        $movie_episodes = '';

        if ($movie_detail['episodeCount'] > 1) {
            foreach ($movie_detail['episodeVo'] as $key => $episode) {
                $movie_episodes .= '<a class="episode';
                $movie_episodes .= intval($key) == intval($req->episode_id) ? ' active' : '';

                $movie_episodes .= '" id="' . ($key + 1) . '" href="' . route('detail_name_episode', ['name' => $movie->slug, 'episode_id' => $key + 1]) . '">' . ($key + 1) . ' </a>';
            }
        }

        $movie_tag = '';

        foreach ($movie_detail['tagList'] as $item) {
            $movie_tag .= '<div class="tag__name" id_tag="' . $item['id'] . '">';
            if (trans()->has('search_advanced.detail.' . $item['name'])) {
                $movie_tag .=  __('search_advanced.detail.' . $item['name']);
            } else {
                $movie_tag .= $item['name'];
            }
            $movie_tag .= '</div>';
        }

        $output = '';

        // $output .= '<div class="movie__container">
        // <div class="movie__media" id="movie__media">
        //     <input id="media" id_media="' . $movie_detail['id'] . '" category="' . $movie_detail['category'] . '" id_episode="' . $req->episode_id . '" class="hidden">
        //     <video class="movie__screen video-js" id="video_media" preload="auto" data-setup="{}" controls autoplay>
        //         <source src="ThuyDung" type="application/x-mpegURL">';
        // foreach ($movie_detail['episodeVo'][$req->episode_id]['subtitlingList'] as $subtitle) {
        //     if ($subtitle['languageAbbr'] == 'vi' || $subtitle['languageAbbr'] == 'en') {
        //         $output .= '<track id="subtitles" kind="subtitles" label="' . $subtitle['language'] . '" srclang="' . $subtitle['languageAbbr'] . '" src="https://srt-to-vtt.vercel.app/?url=' . $subtitle['subtitlingUrl'] . '">';
        //     }
        // }
        // $output .= '</video>
        //     <div class="movie__load">
        //         <div id="loading_movie"></div>
        //     </div>
        // </div>
        // <h1 class="movie__name" id="' . $movie_detail['name'] . '">';
        // $output .= $movie_detail['episodeCount'] > 1 ? $movie_detail['name'] . ' - Tập ' . ($req->episode_id + 1) : $movie_detail['name'];
        // $output .=  '
        // </h1>
        // <div class="movie__episodes">';
        // if ($movie_detail['episodeCount'] > 1) {
        //     foreach ($movie_detail['episodeVo'] as $key => $episode) {
        //         $output .= '<a class="episode';
        //         $output .= intval($key) == intval($req->episode_id) ? ' active' : '';

        //         $output .= '" id="' . ($key + 1) . '" href="' . route('detail_name_episode', ['name' => $movie->slug, 'episode_id' => $key + 1]) . '">' . ($key + 1) . ' </a>';
        //     }
        // }
        // $output .= '</div>
        // <div class="movie__info">
        //     <div class="movie__score"> <i class="fa-solid fa-star"></i> ' . $movie_detail['score'] . '</div>
        //     <div class="movie__year"> <i class="fa-solid fa-calendar"></i> ' . $movie_detail['year'] . '</div>
        // </div>
        // <div class="movie__tag">';
        // foreach ($movie_detail['tagList'] as $item) {
        //     $output .= '<div class="tag__name" id_tag="' . $item['id'] . '">';
        //     if (trans()->has('search_advanced.detail.' . $item['name'])) {
        //         $output .=  __('search_advanced.detail.' . $item['name']);
        //     } else {
        //         $output .= $item['name'];
        //     }
        //     $output .= '</div>';
        // }
        // $output .= '</div>
        // <div class="movie__intro">' . $movie_detail['introduction'] . ' <br>
        //     ' . $movie->description . '
        // </div>
        // <div class="comment_title"> Bình luận </div>
        // </div>';


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

        $data = [];

        $image = asset('img/' . $movie_detail['category'] . $movie_detail['id'] . '.jpg');

        $check_episode = $movie_detail['episodeCount'] > 1;

        array_push($data, $movie_detail, $output, $meta, $image, $movie_episodes, $movie_tag, $urlMovie, $check_episode, $checksub);
        return response()->json($data);
    }

    public function updateSlugMovie()
    {
        $movies = Movie::where('is_change_slug', 0)->get();

        $i = 0;
        foreach ($movies as $movie) {
            if ($i == 1500) return response()->json(1);
            ++$i;
            $pos = strpos($movie->slug, '.html');
            $movie->update(['is_change_slug' => 1, 'slug' => substr($movie->slug, 0, $pos) . '-' . $movie->category . $movie->id . substr($movie->slug, $pos)]);
        }

        return response()->json(true);
    }
}
