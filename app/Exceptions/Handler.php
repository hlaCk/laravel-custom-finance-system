<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            try {
                if( str_contains(\League\Flysystem\Util\MimeType::detectByFilename(basename($request->path())), 'image') && \File::exists(resource_path("images/no-image.png")) ) {
                    return \Image::make(resource_path("images/no-image.png"))->response();
                }
            } catch(\Exception $exception) {
            }

            if( $request->expectsJson() ) {
                return response()->json([
                                            'message' => __('auth.not_found'),
                                        ], 404);
            }
        });
    }
}
