<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function getNewsDetail($id) {
        $news_detail = News::find(intval($id)); 
        return view('pages.news', compact('news_detail'));
    }
}
