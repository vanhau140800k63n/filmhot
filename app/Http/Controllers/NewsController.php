<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function getNewsDetail($name) {
        return view('pages.news_test');
    }
}
