<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getLogin() {
        return view('admin.pages.user_pages.login');
    }

    public function getRegister() {
        return view('admin.pages.user_pages.register');
    }

    public function getDashboard() {
        return view('admin.dashboard');
    }
}
