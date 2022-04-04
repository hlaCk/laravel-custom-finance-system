<?php

namespace Ebess\AdvancedNovaMediaLibrary\Http\Controllers;

use Ebess\AdvancedNovaMediaLibrary\Http\Requests\MediaRequest;
use Ebess\AdvancedNovaMediaLibrary\Http\Resources\MediaResource;
use Exception;
use Illuminate\Support\Facades\Request;

class MediaController extends Controller
{
    public function showConfig(Request $request)
    {
        return response()->json([
                                    'per_page' => config('nova-media-library.per_page', 24),
                                    'thumbnails' => config('nova-media-library.thumbnails', []),
                                ]);
    }

    public function index(MediaRequest $request)
    {
        if( !config('nova-media-library.enable-existing-media') ) {
            throw new Exception('You need to enable the `existing media` feature via config.');
        }

        $mediaClass = config('media-library.media_model');
        $mediaClassIsSearchable = method_exists($mediaClass, 'search');

        $searchText = $request->input('search_text') ?: null;
        $perPage = $request->input('per_page') ?: config('nova-media-library.per_page', 24);

        $query = null;

        if( $searchText && $mediaClassIsSearchable ) {
            $query = $mediaClass::search($searchText);
        } else {
            $query = $mediaClass::query();

            if( $searchText ) {
                $query->where(function($query) use ($searchText) {
                    $query->where('name', 'LIKE', '%' . $searchText . '%');
                    $query->orWhere('file_name', 'LIKE', '%' . $searchText . '%');
                });
            }

            $query->latest();
        }

        $results = $query->paginate($perPage);

        return MediaResource::collection($results);
    }
}
