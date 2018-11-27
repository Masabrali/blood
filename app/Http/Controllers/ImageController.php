<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Intervention\Image;
use App\File;

class ImageController extends Controller
{
    /**
    * Compress the uploaded images
    *
    * @param String $url
    * @return Bool
    */
    static function compress($image) {

        // // Compress the file
        // $_image = Image::make($image);
        //
        // // resize the image to a width of 300 and constraint aspect ratio (auto height)
        // $_image->resize(300, null, function ($constraint) {
        //     $constraint->aspectRatio();
        // });
        // // resize the image to a height of 200 and constraint aspect ratio (auto width)
        // $_image->resize(null, 200, function ($constraint) {
        //     $constraint->aspectRatio();
        // });
        // // prevent possible upsizing
        // $_image->resize(null, 400, function ($constraint) {
        //     $constraint->aspectRatio();
        //     $constraint->upsize();
        // });
        //
        // // Save the image
        // $_image->save($image);

        return $image;
    }
}
