<?php

use App\Http\Resources\Api\MenuItemCollectionResource;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/** /menu/header/en */
/** /menu/sidebar/en */
Route::get('/menu/{slug}/{lang?}', function($slug, $lang = null) {
    return nova_get_menu_by_slug($slug, $lang, fn($menu, $locale) => MenuItemCollectionResource::collection($menu->menuItems($locale)))
        ->additional([ "status" => "success", ]);
});

Route::get('/storage/{folder}/{file}', function($folder, $filename) {
    $path = config('app.url') . '/' . $folder . '/' . $filename;

    if( !File::exists($path) && in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), [ 'pdf' ]) ) {
        $path = public_path('images/pdf.png');
    }
    if( !File::exists($path) && in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), [ 'doc', 'docx' ]) ) {
        $path = public_path('images/doc.png');
    }
    if( !File::exists($path) && !in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), [ 'doc', 'docx', 'pdf' ]) ) {
        $path = public_path('images/no-image.png');
    }

    return \Image::make($path)->response();
});

// Info: to show what prizes attached to results that not exists in round
// Route::get('/test', function() {
//
//     $rounds = Round::with('round_results','round_results.prize','prizes')->get();
//     $diffs = [];
//
//     foreach( $rounds as $round ) {
//         $results = $round->round_results()->with('prize')->get();
//         foreach( $results as $result ) {
//             $results_prizes = $result->prize()->get();
//             $round_prizes = $round->prizes;
//             $diff = $results_prizes->diff($round_prizes);
//             // $diff = $diff->map->id->toArray();
//             $results_prizes = $results_prizes->map->id->toArray();
//             $round_prizes = $round_prizes->map->id->toArray();
//             $diffs[$result->id] = $diff->map->id->toArray();
//         }
//     }
//     dd(
//         $diffs,
//     );
// });

Route::namespace('\App\Http\Controllers\Nova')
     ->domain(config('nova.domain', null))
     ->middleware(config('nova.middleware', []))
    // ->as('nova.')
     ->prefix(config('nova.path'))
     ->group(function() {
         Route::get('/locale/{locale}', 'LocaleController@handle')->name('nova-locale');

         Route::group([ 'prefix' => 'round-result', 'as' => 'round-result.' ], function() {
             Route::post('prize', 'RoundResultController@getPrize')->name('get-prize');
         });

         Route::group([ 'prefix' => 'layout-page', 'as' => 'layout-page.' ], function() {
             Route::post('image', 'LayoutPageController@getImage')->name('image');
         });
     });

Route::get('/', function () {
    return redirect()->to(config('nova.path'));
    // return view('welcome');
});
