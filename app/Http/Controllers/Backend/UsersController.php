<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index() {
        $users = User::orderBy('updated_at', 'desc')->get();
        return view('admin.pages.user_pages.index', compact('users'));
    }
}
