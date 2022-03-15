<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Image;
use Session;

class StorageController extends Controller
{
    public function saveImage(Request $req) {
        $image = Session('image')?Session::get('image'):[];
         
        $index = 0;
        foreach($image as $key => $url) {
            if(!file_exists('img/'.$key.'.jpg')) {
                $url = str_replace(' ', '%20', $url);
                $url = file_get_contents($url);
                $imgFile = Image::make($url);
                $imgFile->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $imgFile->save('img/'.$key.'.jpg');
                unset($image[$key]);
                ++$index;
            } else {
                unset($image[$key]);
            }
            if($index == 10) {
                break;
            }
        }

        $req->session()->put('image', $image);

        return sizeof($image);
    }
}