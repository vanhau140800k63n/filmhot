<?php

namespace App\Http\Controllers\User;

use App\Exceptions\PageException;
use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class MoviesController extends Controller
{
    public function getHome($id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            throw new PageException();
        }

        $movies = Movie::whereIn('id_movie', explode(',', $user->current_movies))->get();
        return view('user.home', compact('user', 'movies'));
    }

    public function getMovieByName($name, $id)
    {
        $pos = strpos($name, '.html');
        $name_check = substr($name, 0, $pos);
        $movie_detail = Movie::where('slug', 'like', $name_check . '%')->first();

        if ($movie_detail == null) {
            throw new PageException();
        }

        $name = $movie_detail->slug;

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

        // dd($movie_detail);

        $random_movies =  Movie::inRandomOrder()->take(30)->get();

        $productAll = Product::where('image', 'like', '%' . 'http' . '%')->inRandomOrder()->take(0)->orderBy('point', 'asc')->get();

        $user = User::find(intval($id));

        if (!is_null($user)) {
            $user->traffic += 1;

            $datas = explode(' ', $user->view_movies);
            $check_contain = 0;
            foreach ($datas as $key => $data) {
                if (str_contains($data, '.' . $movie_detail->id_movie . '-')) {
                    $first_pos = strpos($data, '-');
                    $last_pos = strpos($data, '+');

                    $traffic = intval(substr($data, $first_pos + 1, $last_pos - $first_pos - 1)) + 1;
                    $view = substr($data, $last_pos + 1, strlen($data) - $last_pos);

                    $datas[$key] = '.' . $movie_detail->id_movie . '-' . $traffic . '+' . $view;

                    $check_contain = 1;

                    break;
                }
            }
            if (!$check_contain) {
                array_push($datas, '.' . $movie_detail->id_movie . '-1+0');
            }
            $user->view_movies = implode(' ', $datas);
            $user->save();
        }

        return view('pages.movie', compact('episode_id', 'movie_detail', 'name', 'url', 'productAll', 'sub', 'sub_en', 'random_movies', 'user'));
    }

    public function updateView(Request $request)
    {
        $user = User::find(intval($request->id_user));
        $movie_detail = Movie::where('id_movie', intval($request->id_movie))->first();

        $user->view += 1;
        $datas = explode(' ', $user->view_movies);
        foreach ($datas as $key => $data) {
            if (str_contains($data, '.' . $movie_detail->id_movie . '-')) {
                $first_pos = strpos($data, '-');
                $last_pos = strpos($data, '+');

                $traffic = substr($data, $first_pos + 1, $last_pos - $first_pos - 1);
                $view = intval(substr($data, $last_pos + 1, strlen($data) - $last_pos)) + 1;

                $datas[$key] = '.' . $movie_detail->id_movie . '-' . $traffic . '+' . $view;
                break;
            }
        }
        $user->view_movies = implode(' ', $datas);
        $user->save();

        return response()->json(1);
    }
}
