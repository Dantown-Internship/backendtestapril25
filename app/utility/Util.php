<?php

namespace App\utility;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class Util {
    public static function Auth(){
        $user = Auth::guard('sanctum')->user();

        return $user;
    }

    public static function storeImage($request){
        $imagePath = null;
        if ($request->hasFile('image')) {
            $fileImage = $request->file('image');

            $originalExtension = $fileImage->getClientOriginalExtension();
            $uniqueName = Str::uuid(). '.' . $originalExtension;
          return  $imagePath = $fileImage->storeAs('posts', $uniqueName, 'public');
        }
        return  $imagePath;
    }

}