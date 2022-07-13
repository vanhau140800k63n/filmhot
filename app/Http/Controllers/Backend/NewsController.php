<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ImageFile;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Image;

class NewsController extends Controller
{
    public function list()
    {
        $news = News::orderBy('updated_at', 'desc')->paginate(15);

        return view('admin.pages.news.index', compact('news'));
    }

    public function createNews()
    {
        return view('admin.pages.news.create');
    }

    public function editNews($id)
    {
        $news_detail = News::find($id);
        return view('admin.pages.news.edit', compact('news_detail'));
    }

    public function store(Request $request)
    {

        $this->validate($request, News::rules(), News::rulesNoti());

        $alert = 'Tạo tin thành công';
        $news = new News;
        $news->title = $request->title;

        $str =  $news->title;
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

        if(isset($request->seo_keywords)) {
            $news->seo_keywords = $request->seo_keywords;
        }

        if(isset($request->seo_description)) {
            $news->seo_description = $request->seo_description;
        }

        $news->created_by = Auth::guard('user')->id();
        $news->status = 1;

        $news->save();

        $news->slug = $str . 'p' . $news->id;

        $description = $request->all()['content'];
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

                $img_src = 'img/image_news/' . $news->slug . '.jpg';

                if (file_exists($img_src)) {
                    $img_src = 'img/image_news/' . $news->slug . Str::random(2) . '.jpg';
                }


                $imgFile->save($img_src);

                $img_tag_replace = '<img src="' . asset($img_src) . '" alt="' . $news->title . '" ' . $image_style . '>';

                $image_file = new ImageFile();
                $image_file->src = $img_src;
                $image_file->id_news = $news->id;
                $image_file->save();
                // dd($img_src);
            } else {
                $img_src = str_replace('../', '', $img_src);
                $img_tag_replace = '<img src="' . asset($img_src) . '" alt="' . $news->title . '" ' . $image_style . '>';
            }

            $description = str_replace($img_tag, $img_tag_replace, $description);
            array_push($src_imgs, $img_src);
            $first_pos_img_tag = strpos($description, '<img', $first_pos_img_tag + 1);
        }
        // dd($src_imgs);
        $image_files = ImageFile::where('id_news', $news->id)->whereNotIn('src', $src_imgs)->get();
        foreach ($image_files as $item) {
            File::delete($item->src);
            $item->delete();
        }

        $file_upload = '';

        if (isset($request->myfile)) {
            $array_contents = explode("\n", file_get_contents($request->myfile));
            $index = 2;
            while ($index < sizeof($array_contents)) {
                $file_upload .= $array_contents[$index] . ' ';
                $index += 4;
            }

            $file_upload = str_replace('[âm nhạc]', '', $file_upload);
        }

        if (!empty($file_upload)) {
            $news->content = $description . '.<br>' . $file_upload;
        } else {
            $news->content = $description;
        }

        $news->save();

        return redirect()->route('admin.news.edit_news', $news->id)->with('alert', $alert);
    }

    public function update(Request $request, $id)
    {

        $this->validate($request, News::rules(), News::rulesNoti());

        $alert = 'Tạo tin thành công';
        $news = News::find($id);

        $news->title = $request->title;

        $str =  $news->title;
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

        $news->slug = $str . 'p' . $news->id;

        if(isset($request->seo_keywords)) {
            $news->seo_keywords = $request->seo_keywords;
        }

        if(isset($request->seo_description)) {
            $news->seo_description = $request->seo_description;
        }

        $news->created_by = Auth::guard('user')->id();
        $news->status = 1;

        $description = $request->all()['content'];
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

                $img_src = 'img/image_news/' . $news->slug . '.jpg';

                if (file_exists($img_src)) {
                    $img_src = 'img/image_news/' . $news->slug . Str::random(2) . '.jpg';
                }


                $imgFile->save($img_src);

                $img_tag_replace = '<img src="' . asset($img_src) . '" alt="' . $news->title . '" ' . $image_style . '>';

                $image_file = new ImageFile();
                $image_file->src = $img_src;
                $image_file->id_news = $news->id;
                $image_file->save();
                // dd($img_src);
            } else {
                $img_src = str_replace('../', '', $img_src);
                $img_tag_replace = '<img src="' . asset($img_src) . '" alt="' . $news->title . '" ' . $image_style . '>';
            }

            $description = str_replace($img_tag, $img_tag_replace, $description);
            array_push($src_imgs, $img_src);
            $first_pos_img_tag = strpos($description, '<img', $first_pos_img_tag + 1);
        }
        // dd($src_imgs);
        $image_files = ImageFile::where('id_news', $news->id)->whereNotIn('src', $src_imgs)->get();
        foreach ($image_files as $item) {
            File::delete($item->src);
            $item->delete();
        }

        $file_upload = '';

        if (isset($request->myfile)) {
            $array_contents = explode("\n", file_get_contents($request->myfile));
            $index = 2;
            while ($index < sizeof($array_contents)) {
                $file_upload .= $array_contents[$index] . ' ';
                $index += 4;
            }

            $file_upload = str_replace('[âm nhạc]', '', $file_upload);
        }

        if (!empty($file_upload)) {
            $news->content = $description . '.<br>' . $file_upload;
        } else {
            $news->content = $description;
        }

        $news->save();

        return redirect()->back()->with('alert', $alert);
    }

    public function destroy($id) {
        $news = News::find($id);

        $image_files = ImageFile::where('id_news', $news->id)->get();
        foreach ($image_files as $item) {
            File::delete($item->src);
            $item->delete();
        }

        $news->delete();

        $alert = 'Đã xóa thành công';
        return redirect()->back()->with('alert', $alert);
    }
}
