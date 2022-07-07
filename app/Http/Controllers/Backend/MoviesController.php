<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ImageFile;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Image;

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

    public function searchDevelopMovie(Request $request)
    {
        $movies = Movie::where('name', 'like', '%' . $request->data . '%')->take(4)->get();

        $output = '';
        foreach ($movies as $movie) {
            $output .= '<li>
                            <div class="form-check form-check-primary">
                                <label class="form-check-label" style="display:flex">
                                    <img style="width: 30px; height: 42px; margin-right: 20px" src=' . asset('img/' . $movie->category . $movie->id . '.jpg') . '>
                                    <input class="radio" type="radio" name="develop_movie" value="' . $movie->id_movie . '">' . $movie->name . '
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

    public function develop()
    {
        $user = Auth::guard('user')->user();
        return view('admin.pages.movies.develop', compact('user'));
    }

    public function website()
    {
        return view('admin.pages.movies.website');
    }

    public function getUrlEdit(Request $request)
    {
        $url = route('admin.movie.develop_by_id', $request->data);
        return response()->json($url);
    }

    public function developById($id)
    {
        $movie = Movie::where('id_movie', $id)->first();
        return view('admin.pages.movies.edit', compact('movie'));
    }

    public function update(Request $request, $id_movie)
    {
        // var_dump([1]);
        // $handle = fopen($request->all()['my-file'], 'r');
        // dd($array = explode("\n", file_get_contents($request->all()['my-file']))[2]);
        // dd($request->all());
        $alert = 'Cập nhật thành công';
        $movie = Movie::where('id_movie', $id_movie)->first();
        if (is_null($movie)) {
            $alert = 'Không tìm thấy phim';
            return redirect()->back()->with('alert', $alert);
        }
        $movie->name = $request->name;
        $movie->slug = $request->slug;

        $description = $request->all()['description'];
        $first_pos_img_tag = strpos($description, '<img', 0);

        $src_imgs = [];

        while ($first_pos_img_tag) {
            $last_pos_img_tag = strpos($description, '>', $first_pos_img_tag);
            // dd($last_pos_img_tag);
            $img_tag = substr($description, $first_pos_img_tag, $last_pos_img_tag - $first_pos_img_tag + 1);

            $image_style = '';

            $first_pos_img_style = strpos($img_tag, 'style="', 0);
            if ($first_pos_img_style) {
                $last_pos_img_style = strpos($img_tag, '" ', $first_pos_img_style + 7);
                $image_style = substr($img_tag, $first_pos_img_style, $last_pos_img_style - $first_pos_img_style + 1);
            }

            // dd($image_style);
            $first_pos_img_src = strpos($img_tag, 'src="', 0);
            $last_pos_img_src = strpos($img_tag, '" ', $first_pos_img_src + 5);
            $img_src = substr($img_tag, $first_pos_img_src + 5, $last_pos_img_src - $first_pos_img_src - 5);
            if (!str_contains($img_src, '../')) {
                // dd($img_src);
                $first_pos_img_width = strpos($img_tag, 'width="', 0);
                $last_pos_img_width = strpos($img_tag, '" ', $first_pos_img_width + 7);
                $img_width = intval(substr($img_tag, $first_pos_img_width + 7, $last_pos_img_width - $first_pos_img_width - 7));

                // dd($img_width);

                $url = file_get_contents(str_replace(' ', '%20', $img_src));

                $imgFile = Image::make($url);
                $imgFile->resize($img_width, null, function ($constraint) {
                    $constraint->aspectRatio();
                });

                $img_src = 'img/image_movie_detail/' . $movie->slug . '.jpg';

                if (file_exists($img_src)) {
                    $img_src = 'img/image_movie_detail/' . $movie->slug . Str::random(2) . '.jpg';
                }


                $imgFile->save($img_src);

                $img_tag_replace = '<img src="' . asset($img_src) . '" alt="' . $movie->name . '" '. $image_style .'>';

                $image_file = new ImageFile();
                $image_file->src = $img_src;
                $image_file->id_movie = $id_movie;
                $image_file->save();
                // dd($img_src);
            } else {
                $img_src = str_replace('../', '', $img_src);
                $img_tag_replace = '<img src="' . asset($img_src) . '" alt="' . $movie->name . '" '. $image_style .'>';
            }

            $description = str_replace($img_tag, $img_tag_replace, $description);
            array_push($src_imgs, $img_src);
            $first_pos_img_tag = strpos($description, '<img', $first_pos_img_tag + 1);
        }
        // dd($src_imgs);
        $image_files = ImageFile::where('id_movie', $id_movie)->whereNotIn('src', $src_imgs)->get();
        foreach ($image_files as $item) {
            File::delete($item->src);
            $item->delete();
        }

        $file_upload = '';

        if(isset($request->myfile)) {
            $array_contents = explode("\n", file_get_contents($request->myfile)); 
            $index = 2;
            while($index < sizeof($array_contents)) {
                $file_upload .= $array_contents[$index] . ' ';
                $index += 4;
            }

            $file_upload = str_replace('[âm nhạc]', '', $file_upload);
            $movie->file_upload = $file_upload;
        }

        $movie->description = $description . '.<br>' . $file_upload;

        $movie->save();

        return redirect()->back()->with('alert', $alert);
    }
}
