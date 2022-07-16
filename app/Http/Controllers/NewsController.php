<?php

namespace App\Http\Controllers;

use App\Models\ImageFile;
use App\Models\News;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isNull;

class NewsController extends Controller
{
    public function getNewsDetail($slug)
    {
        $news_detail = News::where('slug', $slug)->first();
        $news_rand = News::inRandomOrder()->take(3)->get();

        foreach ($news_rand as $item) {
            if (is_null($item->image)) {
                $image = ImageFile::where('id_news', $item->id)->first();

                if (!is_null($image)) {
                    $item->image = $image->src;
                    $item->save();
                } else {
                    $item->image = asset('css\assets\images\auth\lockscreen-bg.jpg');
                    $item->save();
                }
            }
        }
        return view('pages.news', compact('news_detail', 'news_rand'));
    }
}
