<?php

namespace App\Http\Controllers;

use App\Models\Abstracts\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{

    public function renameFile(Request $request)
    {
        $update = Media::find($request->id)
                       ->update([ 'name' => $request->name ]);

        return $update ?
            response()->json([ 'message' => __('validation.file_renamed_successfully'), 'success' => true ]) :
            response()->json([ 'message' => __('validation.file_not_found'), 'success' => false ], 404);
    }

    public function deleteFile(Media $media)
    {
        return $media->delete();
    }
}
