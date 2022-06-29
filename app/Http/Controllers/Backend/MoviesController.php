<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MoviesController extends Controller
{
    public function create()
    {
        $random_movies = Movie::take(30)->get();

        $user = Auth::guard('user')->user();
        return view('admin.pages.movies.create', compact('random_movies', 'user'));
    }

    public function searchAddMovie(Request $request)
    {
        $movies = Movie::where('name', 'like', '%' . $request->data . '%')->take(4)->get();

        $output = '';
        foreach ($movies as $movie) {
            $output .= '<li>
                            <div class="form-check form-check-primary">
                                <label class="form-check-label" style="display:flex">
                                    <img style="width: 30px; height: 42px; margin-right: 20px" src=' . asset('img/' . $movie->category . $movie->id . '.jpg') . '>
                                    <input class="checkbox" type="checkbox" name="add_movie[]" value="' . $movie->id_movie . '">' . $movie->name . '
                                    <i class="input-helper"></i>
                                </label>
                            </div>
                            <i class="remove mdi mdi-close-box"></i>
                        </li>';
        }

        return response()->json($output);
    }

    public function viewMovie(Request $request)
    {
        $movies = Movie::whereIn('id_movie', $request->data)->get();

        $output = '';

        foreach ($movies as $movie) {
            $output .= '<div class="preview-item border-bottom">
                            <div class="preview-thumbnail">
                                <img src="' . asset('img/' . $movie->category . $movie->id . '.jpg') . '" alt="image" />
                            </div>
                            <div class="preview-item-content d-flex flex-grow">
                                <div class="flex-grow">
                                    <div class="d-flex d-md-block d-xl-flex justify-content-between">
                                        <h6 class="preview-subject">' . $movie->name . '</h6>
                                        <p class="text-muted text-small"> 0 </p>
                                    </div>
                                    <p class="text-muted">' . $movie->year . '</p>
                                </div>
                            </div>
                        </div>';
        }

        return response()->json($output);
    }

    public function createView(Request $request)
    {
        // dd($request->all());

        $user = Auth::guard('user')->user();

        if (isset($request->list_movie)) {
            $user->current_movies = $request->list_movie;
        }

        if ($request->hasFile('banner_img')) {
            if ($user->banner != null) {
                File::delete($user->banner);
            }
            $image  = $request->file('banner_img');
            $name = $user->id . 'banner.';
            $data_path = $name . pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);

            $image->move('css/assets/images/banner/', $data_path);
            $user->banner = 'css/assets/images/banner/' . $data_path;
        } elseif ($user->banner != null) {
            if (isset($request->check_del)) {
                File::delete($user->banner);
                $user->banner = null;
            }
        }

        $user->save();

        $alert = 'Đã cập nhật web site thành công';
        return redirect()->back()->with('alert', $alert);
    }

    public function develop() {
        return view('admin.pages.movies.develop');
    }

    public function website() {
        return view('admin.pages.movies.website');
    }
}
