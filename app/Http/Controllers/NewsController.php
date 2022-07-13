<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function getNewsDetail($slug) {
        $news_detail = News::where('slug', $slug)->first(); 
        return view('pages.news', compact('news_detail'));
    }
}
