<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Image;
use Session;

class StorageController extends Controller
{
    public function saveImage(Request $req)
    {
        $image = Session('image') ? Session::get('image') : [];
        // return sizeof($image);
        $index = 0;
        foreach ($image as $key => $url) {
            if (!file_exists('img/' . $key . '.jpg')) {
                if ($url != "") {
                    $url = str_replace(' ', '%20', $url);
                    $size = getimagesize($url);
                    if ($size[0] < $size[1] || str_contains($key, 'top_search')) {
                        $url = file_get_contents($url);
                        $imgFile = Image::make($url);
                        $imgFile->resize(300, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $imgFile->save('img/' . $key . '.jpg');

                        ++$index;
                    }
                }
            }
            unset($image[$key]);

            if ($index == 10) {
                break;
            }
        }

        $req->session()->put('image', $image);

        return sizeof($image);
    }
}
